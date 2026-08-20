<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

use App\Models\AuditTeamModel;
use App\Models\AuditModel;
use App\Models\DesignationModel;
use App\Models\DistrictModel;
use App\Models\UserChargeDetailsModel;
use App\Models\AuditMemberModel;
use App\Models\DashboardModel;
use App\Models\Charge;
use App\Models\AssignCharge;
use App\Models\UserChargeDetailModel;
use Illuminate\Http\Request;
use App\Models\ReportModel;


use DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function dashboard_detail()
    {
        $charge = session('charge');
        $chargeid = $charge->chargeid;
        if ($chargeid == '1') {
            // $auditscheduleid = $request->auditscheduleid;
            $teamdetail = AuditTeamModel::fetch_teamdetail();
            $plandetail = AuditModel::fetch_plandetail();

            return response()->json([

                'teamdetail' => $teamdetail,
                'plandetail' => $plandetail,
            ]);
        }
    }

    public function Get_dept(Request $request)
    {
        // Session data
        $charge = session('charge');
        $usertypecode = $charge->usertypecode ?? null;
        $user = session('user');
        $userId = $user->userid ?? null;

        $profileUpdate = null;

        if ($usertypecode && $userId) {
            if ($usertypecode === 'A') {
                $userRecord = DB::table('audit.deptuserdetails')->where('deptuserid', $userId)->first();
            } elseif ($usertypecode === 'I') {
                $userRecord = DB::table('audit.audtieeuserdetails')->where('auditeeuserid', $userId)->first();
            }

            if (isset($userRecord) && $userRecord->profile_update === 'Y') {
                $profileUpdate = 'Y';
            }
        }

        // Get department-related data
        $sessionChargeDetails = session('charge');
        $sessionRoleType = $sessionChargeDetails->roletypecode ?? null;
        $deptCode = $sessionChargeDetails->deptcode ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;
        $userTypeCode = $sessionChargeDetails->usertypecode ?? null;
        $roleTypeCode = $sessionChargeDetails->roletypecode ?? null;

        // Default auditScheduleId to 0 if not provided
        $auditScheduleId = $request->input('auditscheduleid', 0);

        $userChargeId = $user->userid ?? null;

        // Fetch department, district, and other details
        $dept = DashboardModel::fetchDeptDetails($deptCode);
        $year = DashboardModel::fetchYearDetails($deptCode);
        $dist = DashboardModel::fetchDistDetails($sessionRoleType, $deptCode, $regionCode, $distCode);
        $institutionDetails = DashboardModel::fetchInstitutionDetails('');
        $headinstitutionDetails = DashboardModel::fetchInstitutionDetails('Y');
        $countDetails = DashboardModel::fetchCountDetails(
            $deptCode,
            $regionCode,
            $distCode,
            $userChargeId,
            $userTypeCode,
            $roleTypeCode,
            $auditScheduleId
        );

        $deptCode = $sessionChargeDetails->deptcode ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;

        $auditQuarters = DB::table('audit.auditplan as a')
            ->leftjoin('audit.mst_dept as d', 'd.nextquarter', '=', 'a.auditquartercode')
            ->select('a.auditquartercode')
            ->distinct()
            // ->orderBy('a.auditquartercode')
            ->pluck('a.auditquartercode');

        $countdetailsofsch = DashboardModel::GetcountDetails($deptCode, $regionCode, $distCode, $auditQuarters, $quarter = null);

        $countdetailsofsch = $countdetailsofsch[0];

        // Return the view with all necessary data
        return view('dashboard.dashboard', compact(
            'dept',
            'dist',
            'institutionDetails',
            'countDetails',
            'year',
            'headinstitutionDetails',
            'countdetailsofsch',
            'auditQuarters'
        ));
    }

//   public function NewDashboard(Request $request)
//     {
//         // Session data
//         $charge = session('charge');
//         $usertypecode = $charge->usertypecode ?? null;
//         $user = session('user');
//         $userId = $user->userid ?? null;

//         $profileUpdate = null;

//         if ($usertypecode && $userId) {
//             if ($usertypecode === 'A') {
//                 $userRecord = DB::table('audit.deptuserdetails')->where('deptuserid', $userId)->first();
//             } elseif ($usertypecode === 'I') {
//                 $userRecord = DB::table('audit.audtieeuserdetails')->where('auditeeuserid', $userId)->first();
//             }

//             if (isset($userRecord) && $userRecord->profile_update === 'Y') {
//                 $profileUpdate = 'Y';
//             }
//         }

//         // Get department-related data
//         $sessionChargeDetails = session('charge');
//         $sessionRoleType = $sessionChargeDetails->roletypecode ?? null;
//         $deptCode = $sessionChargeDetails->deptcode ?? null;
//         $regionCode = $sessionChargeDetails->regioncode ?? null;
//         $distCode = $sessionChargeDetails->distcode ?? null;
//         $userTypeCode = $sessionChargeDetails->usertypecode ?? null;
//         $roleTypeCode = $sessionChargeDetails->roletypecode ?? null;

//         // Default auditScheduleId to 0 if not provided
//         $auditScheduleId = $request->input('auditscheduleid', 0);

//         $userChargeId = $user->userid ?? null;

//         // Fetch department, district, and other details
//         $dept = DashboardModel::fetchDeptDetails($deptCode);
//         $departments = DashboardModel::fetchDeptDetails($deptCode);
//         $financialyear = ReportModel::getDFinancialyear();
//         $dist = DashboardModel::fetchDistDetails($sessionRoleType, $deptCode, $regionCode, $distCode);
//         $institutionDetails = DashboardModel::fetchInstitutionDetails('');
//         $headinstitutionDetails = DashboardModel::fetchInstitutionDetails('Y');
//         $countDetails = DashboardModel::fetchCountDetails(
//             $deptCode,
//             $regionCode,
//             $distCode,
//             $userChargeId,
//             $userTypeCode,
//             $roleTypeCode,
//             $auditScheduleId
//         );

//         $deptCode = $sessionChargeDetails->deptcode ?? null;
//         $regionCode = $sessionChargeDetails->regioncode ?? null;
//         $distCode = $sessionChargeDetails->distcode ?? null;

//         $auditQuarters = DB::table('audit.auditplan as a')
//             ->leftjoin('audit.mst_dept as d', 'd.nextquarter', '=', 'a.auditquartercode')
//             ->select('a.auditquartercode')
//             ->distinct()
// 	     ->orderBy('a.auditquartercode','desc') 
//             ->pluck('a.auditquartercode');

//         $countdetailsofsch = DashboardModel::GetcountDetails($deptCode, $regionCode, $distCode, $auditQuarters, $quarter = null);

//         $countdetailsofsch = $countdetailsofsch[0];

//         // Return the view with all necessary data
//         return view('dashboard.newdashboard', compact(
//             'profileUpdate',
//             'dept',
//             'dist',
//             'institutionDetails',
//             'countDetails',
//             'financialyear',
//             'headinstitutionDetails',
//             'countdetailsofsch',
//             'auditQuarters',
//             'departments'
//         ));
//     }

  public function NewDashboard(Request $request)
    {
        // Session data
        $charge = session('charge');
        $usertypecode = $charge->usertypecode ?? null;
        $user = session('user');
        $userId = $user->userid ?? null;

        $profileUpdate = null;

        if ($usertypecode && $userId) {
            if ($usertypecode === 'A') {
                $userRecord = DB::table('audit.deptuserdetails')->where('deptuserid', $userId)->first();
            } elseif ($usertypecode === 'I') {
                $userRecord = DB::table('audit.audtieeuserdetails')->where('auditeeuserid', $userId)->first();
            }

            if (isset($userRecord) && $userRecord->profile_update === 'Y') {
                $profileUpdate = 'Y';
            }
        }

        // Get department-related data
        $sessionChargeDetails = session('charge');
        $sessionRoleType = $sessionChargeDetails->roletypecode ?? null;
        $deptCode = $sessionChargeDetails->deptcode ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;
        $userTypeCode = $sessionChargeDetails->usertypecode ?? null;
        $roleTypeCode = $sessionChargeDetails->roletypecode ?? null;

        // Default auditScheduleId to 0 if not provided
        $auditScheduleId = $request->input('auditscheduleid', 0);

        $userChargeId = $user->userid ?? null;

        $dept = DashboardModel::fetchDeptDetails($deptCode);
        $departments = DashboardModel::fetchDeptDetails($deptCode);
        $financialyear = ReportModel::getDFinancialyear();

        $dist = DashboardModel::fetchDistDetails($sessionRoleType, $deptCode, $regionCode, $distCode);
        $institutionDetails = DashboardModel::fetchInstitutionDetails('');
        $headinstitutionDetails = DashboardModel::fetchInstitutionDetails('Y');
        $countDetails = DashboardModel::fetchCountDetails(
            $deptCode,
            $regionCode,
            $distCode,
            $userChargeId,
            $userTypeCode,
            $roleTypeCode,
            $auditScheduleId
        );

        $deptCode = $sessionChargeDetails->deptcode ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;

        $selectedFinancialYear = $request->input('financialyearcode');

        if (empty($selectedFinancialYear) && session()->has('selected_financial_year')) {
            $selectedFinancialYear = session('selected_financial_year');
        }

        if (empty($selectedFinancialYear) && !empty($financialyear) && count($financialyear) > 0) {
            $selectedFinancialYear = $financialyear[0]->financialyearcode;
        }

        $query = DB::table('audit.auditplan as plan')
            ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'plan.planmappingid')
            ->select('apm.planmappingid', 'apm.planname', 'apm.prioritycode','apm.group_key', 'apm.deptcode')
            ->where('apm.financialyearcode', $selectedFinancialYear);

        if ($deptCode && $deptCode !== 'all') {
            $query->where('apm.deptcode', $deptCode);
        }

        $auditQuarters = $query
            ->whereIn('apm.statusflag', ['F', 'P', 'Y'])
            ->distinct()
            ->orderBy('apm.planmappingid', 'desc')
            ->get();

        $countdetailsofsch = DashboardModel::GetcountDetails($deptCode, $regionCode, $distCode, $quarter = null, $selectedFinancialYear);
        $countdetailsofsch = $countdetailsofsch[0];

        return view('dashboard.newdashboard', compact(
            'profileUpdate',
            'dept',
            'dist',
            'institutionDetails',
            'countDetails',
            'financialyear',
            'headinstitutionDetails',
            'countdetailsofsch',
            'auditQuarters',
            'departments',
            'selectedFinancialYear'
        ));
    }


    public function CallingData(Request $request)
    {
        // Retrieve session details
        $sessionChargeDetails = session('charge');
        $sessionRoleType = $sessionChargeDetails->roletypecode ?? null;
        $deptCode = $sessionChargeDetails->deptcode ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;
        $userTypeCode = $sessionChargeDetails->usertypecode ?? null;
        $roleTypeCode = $sessionChargeDetails->roletypecode ?? null;

        // Retrieve the auditscheduleid and activeTab from the request
        $auditscheduleid = $request->input('auditscheduleid');
        $activeTab = $request->input('activeTab'); // 0 for the third tab, 1 for the first tab, etc.

        if ($activeTab == 3) {
            $userChargeId = 0;
        } else {

            $user = session('user');
            $userChargeId = $user->userid ?? null;
        }

        //     echo "User Charge ID: " . $userChargeId;
        //    exit;
        $countDetails = DashboardModel::fetchCountDetails(
            $deptCode,
            $regionCode,
            $distCode,
            $userChargeId,
            $userTypeCode,
            $roleTypeCode,
            $auditscheduleid
        );

        // Return the count details as a JSON response
        return response()->json($countDetails);
    }

    public function sentDetails()
    {
        $user = session('user');
        $sessionChargeDetails = session('charge');
        $userId = $user->userid ?? null;
        $userTypeCode = $sessionChargeDetails->usertypecode ?? null;

        // echo $userTypeCode;
        $sentDetails = DashboardModel::fetchSentDetails($userTypeCode, $userId);
        // print_r($sentDetails);

        return view('dashboard.sentdetails', compact('sentDetails')); // Replace with your view name
    }

    public function descriptionData(Request $request)
    {
        $sessionChargeDetails = session('charge');
        $sessionRoleType = $sessionChargeDetails->roletypecode ?? null;
        $deptCode = $sessionChargeDetails->deptcode ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;
        $userTypeCode = $sessionChargeDetails->usertypecode ?? null;
        $roleTypeCode = $sessionChargeDetails->roletypecode ?? null;

        //   echo $sessionChargeDetails->scheduleid ?? null;

        $auditScheduleId = $request->input('auditscheduleid', 0);
        $activeTab = $request->input('activeTab'); // 0 for the third tab, 1 for the first tab, etc.

        if ($activeTab == 3) {
            $userId = 0;
        } else {

            $user = session('user');
            $userId = $user->userid ?? null;
        }
        $description = $request->input('description', 'allslip'); // Default to 'allslip'

        // Fetch data from model
        $dashboardDetails = DashboardModel::fetchDashboardDescription(
            $deptCode,
            $regionCode,
            $distCode,
            $userId,
            $userTypeCode,
            $roleTypeCode,
            $auditScheduleId,
            $description
        );

        // print_r($dashboardDetails);
        // Pass data to Blade view
        //  return view('dashboard.dashboard', compact('dashboardDetails'));
        return response()->json($dashboardDetails);
    }

//       public function DeptWiseAjax(Request $request)
//     {
//         $sourceForm = $request->input('source_form');
//         $sessionChargeDetails = session('charge');
//         $deptCode = $sessionChargeDetails->deptcode ?? null;
//         $regionCode = $sessionChargeDetails->regioncode ?? null;
//         $distCode = $sessionChargeDetails->distcode ?? null;
//         $quarterVal = $request->input('quarter');
//         $slipQuarterVal = $request->input('quarterslip');

//         if ($sourceForm === 'plantabform' ||
//         $sourceForm === 'auditreport' ||
//         $sourceForm === 'templateaudit' ||
//         $sourceForm === 'inspectionaudit' ||
//         $sourceForm === 'legacyreport' ||
//         $sourceForm === 'parareport' ||
//         $sourceForm === 'paracount') {

//             $quarter = $quarterVal;
//         } else {
//             $quarter = $slipQuarterVal;
//         }

//         $getDeptName = DashboardModel::GetallDept($deptCode);
//         $deptwisedata = [];

//         $totals = [
//             'audit_completed' => 0,
//             'report_finalized' => 0,
//             'report_issued' => 0,
//             'pending_finalize' => 0,
//             'pending_issue' => 0,
//         ];

//         if ($deptCode) {
//             if ($sourceForm == 'auditreport') {
//                 $countdetails = DashboardModel::GetReportCounts($deptCode, $regionCode, $distCode, $quarter);

//                 $deptData = [
//                     'deptname' => $getDeptName[0]->deptelname ?? '',
//                     'audit_completed' => $countdetails[0]->audit_completed_institution ?? 0,
//                     'report_finalized' => $countdetails[0]->report_finalized ?? 0,
//                     'report_issued' => $countdetails[0]->report_issued ?? 0,
//                     'pending_finalize' => $countdetails[0]->pending_to_finalize ?? 0,
//                     'pending_issue' => $countdetails[0]->pending_to_issue ?? 0,
//                     'deptCode' => $deptCode,
//                     'regionCode' => $regionCode,
//                     'distCode' => $distCode,
//                     'regioncount' => null,
//                     'distcount' => null,
//                     'alloc_inscount' => null,
//                     'totalslips' => null,
//                     'pendingslipcount' => null,
//                     'convertedslipcount' => null,
//                     'droppedslipcount' => null,
//                 ];

//                 $totals['audit_completed'] += $deptData['audit_completed'];
//                 $totals['report_finalized'] += $deptData['report_finalized'];
//                 $totals['report_issued'] += $deptData['report_issued'];
//                 $totals['pending_finalize'] += $deptData['pending_finalize'];
//                 $totals['pending_issue'] += $deptData['pending_issue'];

//                 $deptwisedata[] = $deptData;
//             } elseif ($sourceForm === 'templateaudit') {

//                 $countdetails = DashboardModel::GetTemplateAuditCounts($deptCode, $regionCode, $distCode, $quarter)[0];

//                 $deptwisedata[] = [
//                     'deptname' => $getDeptName[0]->deptelname,
//                     'deptsname' => $getDeptName[0]->deptesname,
//                     'regioncount' => $countdetails['regioncount'],
//                     'distcount' => $countdetails['distcount'],
//                     'alloc_inscount' => $countdetails['alloc_inscount'],
//                     'deptCode' => $deptCode,
//                     'regionCode' => $regionCode,
//                     'distCode' => $distCode,
//                     'total_count' => $countdetails['total_count'],
//                     'pending_count' => $countdetails['pending_count'],
//                     'ongoing_count' => $countdetails['ongoing_count'],
//                     'completed_count' => $countdetails['completed_count'],
//                 ];

//             } elseif ($sourceForm === 'inspectionaudit') {

//                 $countdetails = DashboardModel::GetInspectionCounts($deptCode, $regionCode, $distCode, $quarter)[0];

//                 $deptwisedata[] = [
//                     'deptname' => $getDeptName[0]->deptelname,
//                     'deptsname' => $getDeptName[0]->deptesname,
//                     'regioncount' => $countdetails['regioncount'],
//                     'distcount' => $countdetails['distcount'],
//                     'alloc_inscount' => $countdetails['alloc_inscount'],
//                     'deptCode' => $deptCode,
//                     'regionCode' => $regionCode,
//                     'distCode' => $distCode,
//                     'total_inspection_count' => $countdetails['total_inspection_count'] ?? 0,
//                     'pending_inspection_count' => $countdetails['pending_inspection_count'] ?? 0,
//                     'ongoing_inspection_count' => $countdetails['ongoing_inspection_count'] ?? 0,
//                     'completed_inspection_count' => $countdetails['completed_inspection_count'] ?? 0,
//                     'single_completed_inspection_count' => $countdetails['single_completed_inspection_count'] ?? 0,
//                     'multi_completed_inspection_count' => $countdetails['multi_completed_inspection_count'] ?? 0,
//                 ];
//             } elseif ($sourceForm === 'legacyreport') {

//                 $countdetails = DashboardModel::GetLegacyReportCounts($deptCode, $regionCode, $distCode, $quarter)[0];

//                 $deptwisedata[] = [
//                     'deptname' => $getDeptName[0]->deptelname,
//                     'deptsname' => $getDeptName[0]->deptesname,
//                     'regioncount' => $countdetails['regioncount'],
//                     'distcount' => $countdetails['distcount'],
//                     'alloc_inscount' => $countdetails['alloc_inscount'],
//                     'deptCode' => $deptCode,
//                     'regionCode' => $regionCode,
//                     'distCode' => $distCode,
//                     'total_legacy_count' => $countdetails['totalcount'] ?? 0,
//                     'finalize_legacy_count' => $countdetails['legacycount'] ?? 0,
//                     'pending_legacy_count' => $countdetails['entrycount'] ?? 0,
//                 ];
//             } elseif ($sourceForm === 'parareport') {

//                 $countdetails = DashboardModel::GetParaReportCounts($deptCode, $regionCode, $distCode)[0];
		
//                 $deptwisedata[] = [
//                     'deptname' => $getDeptName[0]->deptelname,
//                     'deptsname' => $getDeptName[0]->deptesname,
//                     'regioncount' => $countdetails['regioncount'],
//                     'distcount' => $countdetails['distcount'],
//                     'alloc_inscount' => null,
//                     'deptCode' => $deptCode,
//                     'regionCode' => $regionCode,
//                     'distCode' => $distCode,
//                     'totalparacount' => $countdetails['totalparacount'] ?? 0,
//                     'processedparacount' => $countdetails['processedparacount'] ?? 0,
//                     'pendingparacount' => $countdetails['pendingparacount'] ?? 0,
//                 ];
//             } 
//  elseif ( $sourceForm === 'paracount') {

//                 $countdetails = DashboardModel::GetParaCountDetails($deptCode, $regionCode, $distCode)[0];
//                 // dd( $countdetails);

//                 $deptwisedata[] = [
//                     'deptname' => $getDeptName[0]->deptelname,
//                     'deptsname' => $getDeptName[0]->deptesname,
//                     'regioncount' => $countdetails['regioncount'],
//                     'distcount' => $countdetails['distcount'],
//                     'alloc_inscount' => null,
//                     'deptCode' => $deptCode,
//                     'regionCode' => $regionCode,
//                     'distCode' => $distCode,
//                     'totalparacount' => $countdetails['totalparacount'] ?? 0,
//                     'processedparacount' => $countdetails['processedparacount'] ?? 0,
//                     'pendingparacount' => $countdetails['pendingparacount'] ?? 0,
//                 ];
//             }
// 		else {

//                 // dd($deptCode, $regionCode, $distCode, $quarter)[0];

//                 $countdetails = DashboardModel::GetcountDetails($deptCode, $regionCode, $distCode, $quarter)[0];

//                 if ($sourceForm == 'sliptabform') {
//                     $countdetails['alloc_inscount'] = $countdetails['commencedinscount'];
//                 }

//                 $deptwisedata[] = [
//                     'deptname' => $getDeptName[0]->deptelname,
//                     'deptsname' => $getDeptName[0]->deptesname,
//                     'regioncount' => $countdetails['regioncount'],
//                     'distcount' => $countdetails['distcount'],
//                     'alloc_inscount' => $countdetails['alloc_inscount'],
//                     'deptCode' => $deptCode,
//                     'regionCode' => $regionCode,
//                     'distCode' => $distCode,
//                     'totalslips' => $countdetails['totalslipcount'],
//                     'pendingslipcount' => $countdetails['pendingslipcount'],
//                     'convertedslipcount' => $countdetails['convertedslipcount'],
//                     'droppedslipcount' => $countdetails['droppedslipcount'],
//                     'audit_completed' => null,
//                     'report_finalized' => null,
//                     'report_issued' => null,
//                     'pending_finalize' => null,
//                     'pending_issue' => null,
//                 ];
//             }
//         } else {
//             $getDeptName = DashboardModel::GetallDept();

//             foreach ($getDeptName as $deptval) {
//                 $deptCode = $deptval->deptcode;
//                 $regionCode = null;
//                 $distCode = null;

//                 if ($sourceForm == 'auditreport') {
//                     $countdetails = DashboardModel::GetReportCounts($deptCode, $regionCode, $distCode, $quarter);

//                     $deptData = [
//                         'deptname' => $deptval->deptelname,
//                         'deptsname' => $deptval->deptesname,
//                         'audit_completed' => $countdetails[0]->audit_completed_institution ?? 0,
//                         'report_finalized' => $countdetails[0]->report_finalized ?? 0,
//                         'report_issued' => $countdetails[0]->report_issued ?? 0,
//                         'pending_finalize' => $countdetails[0]->pending_to_finalize ?? 0,
//                         'pending_issue' => $countdetails[0]->pending_to_issue ?? 0,
//                         'deptCode' => $deptCode,
//                         'regionCode' => $regionCode,
//                         'distCode' => $distCode,
//                         'regioncount' => null,
//                         'distcount' => null,
//                         'alloc_inscount' => null,
//                     ];

//                     // Add to totals
//                     $totals['audit_completed'] += $deptData['audit_completed'];
//                     $totals['report_finalized'] += $deptData['report_finalized'];
//                     $totals['report_issued'] += $deptData['report_issued'];
//                     $totals['pending_finalize'] += $deptData['pending_finalize'];
//                     $totals['pending_issue'] += $deptData['pending_issue'];

//                     $deptwisedata[] = $deptData;
//                 } elseif ($sourceForm === 'templateaudit') {

//                     $getdetails = DashboardModel::GetTemplateAuditCounts($deptCode, $regionCode, $distCode, $quarter)[0];

//                     $deptwisedata[] = [
//                         'deptname' => $deptval->deptelname,
//                         'deptsname' => $deptval->deptesname,
//                         'regioncount' => $getdetails['regioncount'],
//                         'distcount' => $getdetails['distcount'],
//                         'alloc_inscount' => $getdetails['alloc_inscount'],
//                         'deptCode' => $deptCode,
//                         'regionCode' => $regionCode,
//                         'distCode' => $distCode,
//                         'total_count' => $getdetails['total_count'],
//                         'pending_count' => $getdetails['pending_count'],
//                         'ongoing_count' => $getdetails['ongoing_count'],
//                         'completed_count' => $getdetails['completed_count'],

//                     ];

//                 } elseif ($sourceForm === 'inspectionaudit') {

//                     $getdetails = DashboardModel::GetInspectionCounts($deptCode, $regionCode, $distCode, $quarter)[0];

//                     $deptwisedata[] = [
//                         'deptname' => $deptval->deptelname,
//                         'deptsname' => $deptval->deptesname,
//                         'regioncount' => $getdetails['regioncount'],
//                         'distcount' => $getdetails['distcount'],
//                         'alloc_inscount' => $getdetails['alloc_inscount'],
//                         'deptCode' => $deptCode,
//                         'regionCode' => $regionCode,
//                         'distCode' => $distCode,
//                         'total_inspection_count' => $getdetails['total_inspection_count'] ?? 0,
//                         'pending_inspection_count' => $getdetails['pending_inspection_count'] ?? 0,
//                         'ongoing_inspection_count' => $getdetails['ongoing_inspection_count'] ?? 0,
//                         'completed_inspection_count' => $getdetails['completed_inspection_count'] ?? 0,
//                         'single_completed_inspection_count' => $getdetails['single_completed_inspection_count'] ?? 0,
//                         'multi_completed_inspection_count' => $getdetails['multi_completed_inspection_count'] ?? 0,
//                     ];
//                 } elseif ($sourceForm === 'legacyreport') {

//                     $getdetails = DashboardModel::GetLegacyReportCounts($deptCode, $regionCode, $distCode, $quarter)[0];

//                     $deptwisedata[] = [
//                         'deptname' => $deptval->deptelname,
//                         'deptsname' => $deptval->deptesname,
//                         'regioncount' => $getdetails['regioncount'],
//                         'distcount' => $getdetails['distcount'],
//                         'alloc_inscount' => $getdetails['alloc_inscount'],
//                         'deptCode' => $deptCode,
//                         'regionCode' => $regionCode,
//                         'distCode' => $distCode,
//                         'total_legacy_count' => $getdetails['totalcount'] ?? 0,
//                         'finalize_legacy_count' => $getdetails['legacycount'] ?? 0,
//                         'pending_legacy_count' => $getdetails['entrycount'] ?? 0,
//                     ];
//                 } elseif ($sourceForm === 'parareport') {

//                     $getdetails = DashboardModel::GetParaReportCounts($deptCode, $regionCode, $distCode)[0];

//                     $deptwisedata[] = [
//                         'deptname' => $deptval->deptelname,
//                         'deptsname' => $deptval->deptesname,
//                         'regioncount' => $getdetails['regioncount'],
//                         'distcount' => $getdetails['distcount'],
//                         'alloc_inscount' => null,
//                         'deptCode' => $deptCode,
//                         'regionCode' => $regionCode,
//                         'distCode' => $distCode,
//                         'totalparacount' => $getdetails['totalparacount'] ?? 0,
//                         'processedparacount' => $getdetails['processedparacount'] ?? 0,
//                         'pendingparacount' => $getdetails['pendingparacount'] ?? 0,
//                     ];
//                 }elseif ($sourceForm === 'paracount') {

//                     $getdetails = DashboardModel::GetParaCountDetails($deptCode, $regionCode, $distCode)[0];

//                     $deptwisedata[] = [
//                         'deptname' => $deptval->deptelname,
//                         'deptsname' => $deptval->deptesname,
//                         'regioncount' => $getdetails['regioncount'],
//                         'distcount' => $getdetails['distcount'],
//                         'alloc_inscount' => null,
//                         'deptCode' => $deptCode,
//                         'regionCode' => $regionCode,
//                         'distCode' => $distCode,
//                         'totalparacount' => $getdetails['totalparacount'] ?? 0,
//                         'processedparacount' => $getdetails['processedparacount'] ?? 0,
//                         'pendingparacount' => $getdetails['pendingparacount'] ?? 0,
//                     ];
//                 } else {
//                     // Handle other forms for all departments
//                     // dd($deptCode, $regionCode, $distCode, $quarter)[0];
//                     $getdetails = DashboardModel::GetcountDetails($deptCode, $regionCode, $distCode, $quarter)[0];

//                     if ($sourceForm == 'sliptabform') {
//                         $getdetails['alloc_inscount'] = $getdetails['commencedinscount'];
//                     }

//                     $deptwisedata[] = [
//                         'deptname' => $deptval->deptelname,
//                         'deptsname' => $deptval->deptesname,
//                         'regioncount' => $getdetails['regioncount'],
//                         'distcount' => $getdetails['distcount'],
//                         'alloc_inscount' => $getdetails['alloc_inscount'],
//                         'deptCode' => $deptCode,
//                         'regionCode' => $regionCode,
//                         'distCode' => $distCode,
//                         'totalslips' => $getdetails['totalslipcount'],
//                         'pendingslipcount' => $getdetails['pendingslipcount'],
//                         'convertedslipcount' => $getdetails['convertedslipcount'],
//                         'droppedslipcount' => $getdetails['droppedslipcount'],
//                     ];
//                 }
//             }
//         }

//         if ($sourceForm == 'auditreport') {
//             $deptwisedata[] = [
//                 'deptname' => 'TOTAL',
//                 'audit_completed' => $totals['audit_completed'],
//                 'report_finalized' => $totals['report_finalized'],
//                 'report_issued' => $totals['report_issued'],
//                 'pending_finalize' => $totals['pending_finalize'],
//                 'pending_issue' => $totals['pending_issue'],
//                 'deptCode' => null,
//                 'regionCode' => null,
//                 'distCode' => null,
//                 'regioncount' => null,
//                 'distcount' => null,
//                 'alloc_inscount' => null,
//                 'totalslips' => null,
//                 'pendingslipcount' => null,
//                 'convertedslipcount' => null,
//                 'droppedslipcount' => null,
//             ];
//         }

//         return response()->json(['data' => $deptwisedata]);
//     }


public function DeptWiseAjax(Request $request)
    {
        $sourceForm = $request->input('source_form');
        $sessionChargeDetails = session('charge');
        $deptCode = $sessionChargeDetails->deptcode ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;
        $quarterVal = $request->input('quarter');
        $slipQuarterVal = $request->input('quarterslip');
        $financialyearcode = $request->input('financialyear');

        if ($sourceForm === 'plantabform' ||
        $sourceForm === 'auditreport' ||
        $sourceForm === 'templateaudit' ||
        $sourceForm === 'inspectionaudit' ||
        $sourceForm === 'legacyreport' ||
        $sourceForm === 'parareport' ||
        $sourceForm === 'paracount' || $sourceForm === 'retirementpara') {

            $quarter = $quarterVal;
        } else {
            $quarter = $slipQuarterVal;
        }

        $getDeptName = DashboardModel::GetallDept($deptCode);
        $deptwisedata = [];

        $totals = [
            'audit_completed' => 0,
            'report_finalized' => 0,
            'report_issued' => 0,
            'pending_finalize' => 0,
            'pending_issue' => 0,
        ];

        if ($deptCode) {
            if ($sourceForm == 'auditreport') {
                $countdetails = DashboardModel::GetReportCounts($deptCode, $regionCode, $distCode, $quarter, $financialyearcode);

                $deptData = [
                    'deptname' => $getDeptName[0]->deptelname ?? '',
                    'audit_completed' => $countdetails[0]->audit_completed_institution ?? 0,
                    'report_finalized' => $countdetails[0]->report_finalized ?? 0,
                    'report_issued' => $countdetails[0]->report_issued ?? 0,
                    'pending_finalize' => $countdetails[0]->pending_to_finalize ?? 0,
                    'pending_issue' => $countdetails[0]->pending_to_issue ?? 0,
                    'deptCode' => $deptCode,
                    'regionCode' => $regionCode,
                    'distCode' => $distCode,
                    'regioncount' => null,
                    'distcount' => null,
                    'alloc_inscount' => null,
                    'totalslips' => null,
                    'pendingslipcount' => null,
                    'convertedslipcount' => null,
                    'droppedslipcount' => null,
                ];

                $totals['audit_completed'] += $deptData['audit_completed'];
                $totals['report_finalized'] += $deptData['report_finalized'];
                $totals['report_issued'] += $deptData['report_issued'];
                $totals['pending_finalize'] += $deptData['pending_finalize'];
                $totals['pending_issue'] += $deptData['pending_issue'];

                $deptwisedata[] = $deptData;
            } elseif ($sourceForm === 'templateaudit') {

                $countdetails = DashboardModel::GetTemplateAuditCounts($deptCode, $regionCode, $distCode, $quarter, $financialyearcode)[0];

                $deptwisedata[] = [
                    'deptname' => $getDeptName[0]->deptelname,
                    'deptsname' => $getDeptName[0]->deptesname,
                    'regioncount' => $countdetails['regioncount'],
                    'distcount' => $countdetails['distcount'],
                    'alloc_inscount' => $countdetails['alloc_inscount'],
                    'deptCode' => $deptCode,
                    'regionCode' => $regionCode,
                    'distCode' => $distCode,
                    'total_count' => $countdetails['total_count'],
                    'pending_count' => $countdetails['pending_count'],
                    'ongoing_count' => $countdetails['ongoing_count'],
                    'completed_count' => $countdetails['completed_count'],
                ];

            } elseif ($sourceForm === 'inspectionaudit') {

                $countdetails = DashboardModel::GetInspectionCounts($deptCode, $regionCode, $distCode, $quarter, $financialyearcode)[0];

                $deptwisedata[] = [
                    'deptname' => $getDeptName[0]->deptelname,
                    'deptsname' => $getDeptName[0]->deptesname,
                    'regioncount' => $countdetails['regioncount'],
                    'distcount' => $countdetails['distcount'],
                    'alloc_inscount' => $countdetails['alloc_inscount'],
                    'deptCode' => $deptCode,
                    'regionCode' => $regionCode,
                    'distCode' => $distCode,
                    'total_inspection_count' => $countdetails['total_inspection_count'] ?? 0,
                    'pending_inspection_count' => $countdetails['pending_inspection_count'] ?? 0,
                    'ongoing_inspection_count' => $countdetails['ongoing_inspection_count'] ?? 0,
                    'completed_inspection_count' => $countdetails['completed_inspection_count'] ?? 0,
                    'single_completed_inspection_count' => $countdetails['single_completed_inspection_count'] ?? 0,
                    'multi_completed_inspection_count' => $countdetails['multi_completed_inspection_count'] ?? 0,
                ];
            } elseif ($sourceForm === 'legacyreport') {

                $countdetails = DashboardModel::GetLegacyReportCounts($deptCode, $regionCode, $distCode, $quarter)[0];

                $deptwisedata[] = [
                    'deptname' => $getDeptName[0]->deptelname,
                    'deptsname' => $getDeptName[0]->deptesname,
                    'regioncount' => $countdetails['regioncount'],
                    'distcount' => $countdetails['distcount'],
                    'alloc_inscount' => $countdetails['alloc_inscount'],
                    'deptCode' => $deptCode,
                    'regionCode' => $regionCode,
                    'distCode' => $distCode,
                    'total_legacy_count' => $countdetails['totalcount'] ?? 0,
                    'finalize_legacy_count' => $countdetails['legacycount'] ?? 0,
                    'pending_legacy_count' => $countdetails['entrycount'] ?? 0,
                ];
            } elseif ($sourceForm === 'parareport') {

                $countdetails = DashboardModel::GetParaReportCounts($deptCode, $regionCode, $distCode)[0];

                $deptwisedata[] = [
                    'deptname' => $getDeptName[0]->deptelname,
                    'deptsname' => $getDeptName[0]->deptesname,
                    'regioncount' => $countdetails['regioncount'],
                    'distcount' => $countdetails['distcount'],
                    'alloc_inscount' => null,
                    'deptCode' => $deptCode,
                    'regionCode' => $regionCode,
                    'distCode' => $distCode,
                    'totalparacount' => $countdetails['totalparacount'] ?? 0,
                    'processedparacount' => $countdetails['processedparacount'] ?? 0,
                    'pendingparacount' => $countdetails['pendingparacount'] ?? 0,
                ];
            }  elseif ( $sourceForm === 'paracount') {

                $countdetails = DashboardModel::GetParaCountDetails($deptCode, $regionCode, $distCode)[0];
                // dd( $countdetails);

                $deptwisedata[] = [
                    'deptname' => $getDeptName[0]->deptelname,
                    'deptsname' => $getDeptName[0]->deptesname,
                    'regioncount' => $countdetails['regioncount'],
                    'distcount' => $countdetails['distcount'],
                    'alloc_inscount' => null,
                    'deptCode' => $deptCode,
                    'regionCode' => $regionCode,
                    'distCode' => $distCode,
                    'totalparacount' => $countdetails['totalparacount'] ?? 0,
                    'processedparacount' => $countdetails['processedparacount'] ?? 0,
                    'pendingparacount' => $countdetails['pendingparacount'] ?? 0,
                ];
            } else if ($sourceForm === 'retirementpara') {

                $roleActionCode = view()->shared('rtd_committee_roleaction');

                $countdetails = DashboardModel::GetRetirementParaDetails($deptCode, $regionCode, $distCode, $roleActionCode)[0];
                // dd( $countdetails);

                $deptwisedata[] = [
                    'deptname' => $getDeptName[0]->deptelname,
                    'deptsname' => $getDeptName[0]->deptesname,
                    'regioncount' => $countdetails['regioncount'],
                    'distcount' => $countdetails['distcount'],
                    'alloc_inscount' => null,
                    'deptCode' => $deptCode,
                    'regionCode' => $regionCode,
                    'distCode' => $distCode,
                    'total_retirement_paracount' => $countdetails['total_retirement_paracount'] ?? 0,
                    'pending_retirement_paracount' => $countdetails['pending_retirement_paracount'] ?? 0,
                    'processed_retirement_paracount' => $countdetails['processed_retirement_paracount'] ?? 0,
                ];
            } else {

                // dd($deptCode, $regionCode, $distCode, $quarter)[0];

                $countdetails = DashboardModel::GetcountDetails($deptCode, $regionCode, $distCode, $quarter, $financialyearcode)[0];

                if ($sourceForm == 'sliptabform') {
                    $countdetails['alloc_inscount'] = $countdetails['commencedinscount'];
                }

                $deptwisedata[] = [
                    'deptname' => $getDeptName[0]->deptelname,
                    'deptsname' => $getDeptName[0]->deptesname,
                    'regioncount' => $countdetails['regioncount'],
                    'distcount' => $countdetails['distcount'],
                    'alloc_inscount' => $countdetails['alloc_inscount'],
                    'deptCode' => $deptCode,
                    'regionCode' => $regionCode,
                    'distCode' => $distCode,
                    'totalslips' => $countdetails['totalslipcount'],
                    'pendingslipcount' => $countdetails['pendingslipcount'],
                    'convertedslipcount' => $countdetails['convertedslipcount'],
                    'droppedslipcount' => $countdetails['droppedslipcount'],
                    'audit_completed' => null,
                    'report_finalized' => null,
                    'report_issued' => null,
                    'pending_finalize' => null,
                    'pending_issue' => null,
                ];
            }
        } else {
            $getDeptName = DashboardModel::GetallDept();

            foreach ($getDeptName as $deptval) {
                $deptCode = $deptval->deptcode;
                $regionCode = null;
                $distCode = null;

                if ($sourceForm == 'auditreport') {
                    $countdetails = DashboardModel::GetReportCounts($deptCode, $regionCode, $distCode, $quarter, $financialyearcode);

                    $deptData = [
                        'deptname' => $deptval->deptelname,
                        'deptsname' => $deptval->deptesname,
                        'audit_completed' => $countdetails[0]->audit_completed_institution ?? 0,
                        'report_finalized' => $countdetails[0]->report_finalized ?? 0,
                        'report_issued' => $countdetails[0]->report_issued ?? 0,
                        'pending_finalize' => $countdetails[0]->pending_to_finalize ?? 0,
                        'pending_issue' => $countdetails[0]->pending_to_issue ?? 0,
                        'deptCode' => $deptCode,
                        'regionCode' => $regionCode,
                        'distCode' => $distCode,
                        'regioncount' => null,
                        'distcount' => null,
                        'alloc_inscount' => null,
                    ];

                    // Add to totals
                    $totals['audit_completed'] += $deptData['audit_completed'];
                    $totals['report_finalized'] += $deptData['report_finalized'];
                    $totals['report_issued'] += $deptData['report_issued'];
                    $totals['pending_finalize'] += $deptData['pending_finalize'];
                    $totals['pending_issue'] += $deptData['pending_issue'];

                    $deptwisedata[] = $deptData;
                } elseif ($sourceForm === 'templateaudit') {

                    $getdetails = DashboardModel::GetTemplateAuditCounts($deptCode, $regionCode, $distCode, $quarter, $financialyearcode)[0];

                    $deptwisedata[] = [
                        'deptname' => $deptval->deptelname,
                        'deptsname' => $deptval->deptesname,
                        'regioncount' => $getdetails['regioncount'],
                        'distcount' => $getdetails['distcount'],
                        'alloc_inscount' => $getdetails['alloc_inscount'],
                        'deptCode' => $deptCode,
                        'regionCode' => $regionCode,
                        'distCode' => $distCode,
                        'total_count' => $getdetails['total_count'],
                        'pending_count' => $getdetails['pending_count'],
                        'ongoing_count' => $getdetails['ongoing_count'],
                        'completed_count' => $getdetails['completed_count'],

                    ];

                } elseif ($sourceForm === 'inspectionaudit') {

                    $getdetails = DashboardModel::GetInspectionCounts($deptCode, $regionCode, $distCode, $quarter, $financialyearcode)[0];

                    $deptwisedata[] = [
                        'deptname' => $deptval->deptelname,
                        'deptsname' => $deptval->deptesname,
                        'regioncount' => $getdetails['regioncount'],
                        'distcount' => $getdetails['distcount'],
                        'alloc_inscount' => $getdetails['alloc_inscount'],
                        'deptCode' => $deptCode,
                        'regionCode' => $regionCode,
                        'distCode' => $distCode,
                        'total_inspection_count' => $getdetails['total_inspection_count'] ?? 0,
                        'pending_inspection_count' => $getdetails['pending_inspection_count'] ?? 0,
                        'ongoing_inspection_count' => $getdetails['ongoing_inspection_count'] ?? 0,
                        'completed_inspection_count' => $getdetails['completed_inspection_count'] ?? 0,
                        'single_completed_inspection_count' => $getdetails['single_completed_inspection_count'] ?? 0,
                        'multi_completed_inspection_count' => $getdetails['multi_completed_inspection_count'] ?? 0,
                    ];
                } elseif ($sourceForm === 'legacyreport') {

                    $getdetails = DashboardModel::GetLegacyReportCounts($deptCode, $regionCode, $distCode, $quarter)[0];

                    $deptwisedata[] = [
                        'deptname' => $deptval->deptelname,
                        'deptsname' => $deptval->deptesname,
                        'regioncount' => $getdetails['regioncount'],
                        'distcount' => $getdetails['distcount'],
                        'alloc_inscount' => $getdetails['alloc_inscount'],
                        'deptCode' => $deptCode,
                        'regionCode' => $regionCode,
                        'distCode' => $distCode,
                        'total_legacy_count' => $getdetails['totalcount'] ?? 0,
                        'finalize_legacy_count' => $getdetails['legacycount'] ?? 0,
                        'pending_legacy_count' => $getdetails['entrycount'] ?? 0,
                    ];
                } elseif ($sourceForm === 'parareport') {

                    $getdetails = DashboardModel::GetParaReportCounts($deptCode, $regionCode, $distCode)[0];

                    $deptwisedata[] = [
                        'deptname' => $deptval->deptelname,
                        'deptsname' => $deptval->deptesname,
                        'regioncount' => $getdetails['regioncount'],
                        'distcount' => $getdetails['distcount'],
                        'alloc_inscount' => null,
                        'deptCode' => $deptCode,
                        'regionCode' => $regionCode,
                        'distCode' => $distCode,
                        'totalparacount' => $getdetails['totalparacount'] ?? 0,
                        'processedparacount' => $getdetails['processedparacount'] ?? 0,
                        'pendingparacount' => $getdetails['pendingparacount'] ?? 0,
                    ];
                }elseif ($sourceForm === 'paracount') {

                    $getdetails = DashboardModel::GetParaCountDetails($deptCode, $regionCode, $distCode)[0];

                    $deptwisedata[] = [
                        'deptname' => $deptval->deptelname,
                        'deptsname' => $deptval->deptesname,
                        'regioncount' => $getdetails['regioncount'],
                        'distcount' => $getdetails['distcount'],
                        'alloc_inscount' => null,
                        'deptCode' => $deptCode,
                        'regionCode' => $regionCode,
                        'distCode' => $distCode,
                        'totalparacount' => $getdetails['totalparacount'] ?? 0,
                        'processedparacount' => $getdetails['processedparacount'] ?? 0,
                        'pendingparacount' => $getdetails['pendingparacount'] ?? 0,
                    ];
                } elseif ($sourceForm === 'retirementpara') {

                    $roleActionCode = view()->shared('rtd_committee_roleaction');

                    $getdetails = DashboardModel::GetRetirementParaDetails($deptCode, $regionCode, $distCode, $roleActionCode)[0];

                    $deptwisedata[] = [
                        'deptname' => $deptval->deptelname,
                        'deptsname' => $deptval->deptesname,
                        'regioncount' => $getdetails['regioncount'],
                        'distcount' => $getdetails['distcount'],
                        'alloc_inscount' => null,
                        'deptCode' => $deptCode,
                        'regionCode' => $regionCode,
                        'distCode' => $distCode,
                        'total_retirement_paracount' => $getdetails['total_retirement_paracount'] ?? 0,
                        'pending_retirement_paracount' => $getdetails['pending_retirement_paracount'] ?? 0,
                        'processed_retirement_paracount' => $getdetails['processed_retirement_paracount'] ?? 0,
                    ];
                } else {
                    // dd($deptCode, $regionCode, $distCode, $quarter)[0];
                    $getdetails = DashboardModel::GetcountDetails($deptCode, $regionCode, $distCode, $quarter, $financialyearcode)[0];

                    if ($sourceForm == 'sliptabform') {
                        $getdetails['alloc_inscount'] = $getdetails['commencedinscount'];
                    }

                    $deptwisedata[] = [
                        'deptname' => $deptval->deptelname,
                        'deptsname' => $deptval->deptesname,
                        'regioncount' => $getdetails['regioncount'],
                        'distcount' => $getdetails['distcount'],
                        'alloc_inscount' => $getdetails['alloc_inscount'],
                        'deptCode' => $deptCode,
                        'regionCode' => $regionCode,
                        'distCode' => $distCode,
                        'totalslips' => $getdetails['totalslipcount'],
                        'pendingslipcount' => $getdetails['pendingslipcount'],
                        'convertedslipcount' => $getdetails['convertedslipcount'],
                        'droppedslipcount' => $getdetails['droppedslipcount'],
                    ];
                }
            }
        }

        if ($sourceForm == 'auditreport') {
            $deptwisedata[] = [
                'deptname' => 'TOTAL',
                'audit_completed' => $totals['audit_completed'],
                'report_finalized' => $totals['report_finalized'],
                'report_issued' => $totals['report_issued'],
                'pending_finalize' => $totals['pending_finalize'],
                'pending_issue' => $totals['pending_issue'],
                'deptCode' => null,
                'regionCode' => null,
                'distCode' => null,
                'regioncount' => null,
                'distcount' => null,
                'alloc_inscount' => null,
                'totalslips' => null,
                'pendingslipcount' => null,
                'convertedslipcount' => null,
                'droppedslipcount' => null,
            ];
        }

        return response()->json(['data' => $deptwisedata]);
    }



    //     public function InstitutedetailsGet(Request $request)
    // {
    //     $sessionchargedel = session('charge');

    //     $deptCode = $request->deptCode;
    //     $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
    //     $distCode = $request->distCode;
    //     $quarter = $request->input('quarter');
    //     $sourceForm = $request->sourceform;

    //     $isTemplateAudit = ($sourceForm === 'templateaudit');
    //     $isInspectionAudit = ($sourceForm === 'inspectionaudit');
    //     $isLegacyreport = ($sourceForm === 'legacyreport');
    //     $isParareport = ($sourceForm === 'parareport');
    //     $isParacount = ($sourceForm === 'paracount');

    //     $cacheKey = ($isTemplateAudit ? 'template_audit_institute_details' : 'institute_details')
    //                 .":{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$sourceForm}";

    //     $institutes = Cache::remember($cacheKey, now()->addHours(1), function () use ($deptCode, $regionCode, $distCode, $quarter, $isTemplateAudit, $isInspectionAudit, $isLegacyreport, $isParareport, $isParacount   ) {
    //         if ($isTemplateAudit) {
    //             return DashboardModel::TemplateAuditInstitutedetailsGet($deptCode, $regionCode, $distCode, $quarter);
    //         } elseif ($isInspectionAudit) {
    //             return DashboardModel::InspectionAuditInstitutedetailsGet($deptCode, $regionCode, $distCode, $quarter);
    //         } elseif ($isLegacyreport) {
    //             return DashboardModel::LegacyInstitutionwiseData($deptCode, $regionCode, $distCode);
    //         } elseif ($isParareport) {
    //             return DashboardModel::ParaInstitutionwiseData($deptCode, $regionCode, $distCode);
    //         } elseif ($isParacount) {
    //             return DashboardModel::ParaCountwiseData($deptCode, $regionCode, $distCode);
    //         } else {
    //             return DashboardModel::InstitutedetailsGet($deptCode, $regionCode, $distCode, $quarter);
    //         }
    //     });

    //     $institutes = collect($institutes)->map(function ($item) {
    //         if (isset($item['auditscheduleid'])) {
    //             $item['encrypted_auditscheduleid'] = Crypt::encryptString($item['auditscheduleid']);
    //         }
    //         if (isset($item['auditinspectionid'])) {
    //             $item['auditinspectionid'] = Crypt::encryptString($item['auditinspectionid']);
    //         }

    //         return $item;
    //     })->values();

    //     return response()->json(['data' => $institutes]);
    // }

        public function InstitutedetailsGet(Request $request)
    {
        $sessionchargedel = session('charge');

        $deptCode = $request->deptCode;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        $distCode = $request->distCode;
        $quarter = $request->input('quarter');
        $financialyearcode = $request->input('financialyearcode');
        $sourceForm = $request->sourceform;

        $isTemplateAudit = ($sourceForm === 'templateaudit');
        $isInspectionAudit = ($sourceForm === 'inspectionaudit');
        $isLegacyreport = ($sourceForm === 'legacyreport');
        $isParareport = ($sourceForm === 'parareport');
        $isParacount = ($sourceForm === 'paracount');
        $isRetirementPara = ($sourceForm === 'retirementpara');

        $cacheKey = ($isTemplateAudit ? 'template_audit_institute_details' : 'institute_details')
                    .":{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$financialyearcode}:{$sourceForm}";

        $institutes = Cache::remember($cacheKey, now()->addHours(1), function () use ($deptCode, $regionCode, $distCode, $quarter, $financialyearcode, $isTemplateAudit, $isInspectionAudit, $isLegacyreport, $isParareport, $isParacount, $isRetirementPara   ) {
            if ($isTemplateAudit) {
                return DashboardModel::TemplateAuditInstitutedetailsGet($deptCode, $regionCode, $distCode, $quarter, $financialyearcode);
            } elseif ($isInspectionAudit) {
                return DashboardModel::InspectionAuditInstitutedetailsGet($deptCode, $regionCode, $distCode, $quarter, $financialyearcode);
            } elseif ($isLegacyreport) {
                return DashboardModel::LegacyInstitutionwiseData($deptCode, $regionCode, $distCode);
            } elseif ($isParareport) {
                return DashboardModel::ParaInstitutionwiseData($deptCode, $regionCode, $distCode);
            } elseif ($isParacount) {
                return DashboardModel::ParaCountwiseData($deptCode, $regionCode, $distCode);
            } elseif ($isRetirementPara) {
                return DashboardModel::RetirementParadetailsGet($deptCode, $regionCode, $distCode);
            } else {
                return DashboardModel::InstitutedetailsGet($deptCode, $regionCode, $distCode, $quarter , $financialyearcode);
            }
        });

        $institutes = collect($institutes)->map(function ($item) {
            if (isset($item['auditscheduleid'])) {
                $item['encrypted_auditscheduleid'] = Crypt::encryptString($item['auditscheduleid']);
            }
            if (isset($item['auditinspectionid'])) {
                $item['auditinspectionid'] = Crypt::encryptString($item['auditinspectionid']);
            }

            return $item;
        })->values();

        return response()->json(['data' => $institutes]);
    }


// public function CommencedInstitutedetailsGet(Request $request)
//     {
//         $sessionchargedel = session('charge');

//         $deptCode = $request->deptCode;
//         $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
//         $distCode = $request->distCode;
//         $whichslip = $request->whichslip;
//         $quarter = $request->input('quarter');

//         // Create a unique cache key based on all parameters
//         $cacheKey = "commenced_institutes:{$deptCode}:{$regionCode}:{$distCode}:{$whichslip}:{$quarter}";

//         // Try to get data from cache first
//         $institutes = Cache::remember($cacheKey, 3600, function () use ($deptCode, $regionCode, $distCode, $whichslip, $quarter) {
//             // This closure will only be executed if data is not in cache
//             return DashboardModel::CommencedInstitutedetailsGet($deptCode, $regionCode, $distCode, $whichslip, $quarter);
//         });

//         // Encrypt the auditscheduleid for each item
//         foreach ($institutes as $item) {
//             $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
//         }

//         return response()->json(['data' => $institutes]); // format required by DataTables
//     }


public function CommencedInstitutedetailsGet(Request $request)
    {
        $sessionchargedel = session('charge');

        $deptCode = $request->deptCode;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        $distCode = $request->distCode;
        $whichslip = $request->whichslip;
        $quarter = $request->input('quarter');
        $financialyearcode = $request->input('financialyearcode');

        // Create a unique cache key based on all parameters
        $cacheKey = "commenced_institutes:{$deptCode}:{$regionCode}:{$distCode}:{$whichslip}:{$quarter}:{$financialyearcode}";

        // Try to get data from cache first
        $institutes = Cache::remember($cacheKey, 3600, function () use ($deptCode, $regionCode, $distCode, $whichslip, $quarter, $financialyearcode) {
            // This closure will only be executed if data is not in cache
            return DashboardModel::CommencedInstitutedetailsGet($deptCode, $regionCode, $distCode, $whichslip, $quarter, $financialyearcode);
        });

        // Encrypt the auditscheduleid for each item
        foreach ($institutes as $item) {
            $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
        }

        return response()->json(['data' => $institutes]); // format required by DataTables
    }


    public function getauditslipdetails(Request $request)
    {
        $auditscheduleid = $request->auditscheduleid;

        $alldetails = DashboardModel::getslipcount($auditscheduleid);

        return $alldetails;

    }

//   public function RegionwiseDetails(Request $request)
//     {
//         $sessionchargedel = session('charge');

//         $deptCode = $request->deptCode;
//         $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
//         $distCode = $request->distCode ?? null;
//         $sourceForm = $request->sourceform;
//         $quarter = $request->input('quarter');

//         $isTemplateAudit = ($sourceForm === 'templateaudit');
//         $isInspection = ($sourceForm === 'inspectionaudit');
//         $isLegacyreport = ($sourceForm === 'legacyreport');
//         $isParareport = ($sourceForm === 'parareport');
//         $isParacount = ($sourceForm === 'paracount');

//         if ($isTemplateAudit) {
//             $countMethod = 'GetTemplateAuditCounts';
//         } elseif ($isLegacyreport) {
//             $countMethod = 'GetLegacyReportCounts';
//         } elseif ($isParareport) {
//             $countMethod = 'GetParaReportCounts';
//         } elseif ($isInspection) {
//             $countMethod = 'GetInspectionCounts';
//         } elseif ($isParacount) {
//             $countMethod = 'GetParaCountDetails';
//         } else {
//             $countMethod = 'GetcountDetails';
//         }

//         $cacheKey = ($isTemplateAudit ? 'region_template_audit_details' : ($isLegacyreport
//                     ? 'region_legacy_report_details' : ($isInspection ?  'region_inspection_audit_details' : 'regionwise_details'))).":{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$sourceForm}";

//         // Cache for 1 hour
//         $cacheTime = now()->addHours(1);

//         $RegionwiseDetails = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter, $isTemplateAudit) {
//             if ($isTemplateAudit) {
//                 return DashboardModel::RegionTemplatewiseDetails($deptCode, $regionCode, $distCode, $quarter);
//             } else {
//                 return DashboardModel::RegionwiseDetails($deptCode, $regionCode, $distCode, $quarter);
//             }
//         });

//         foreach ($RegionwiseDetails as $item) {
//             if ($isTemplateAudit) {
//                 $prefix = 'region_template_audit_counts';
//             } elseif ($isLegacyreport) {
//                 $prefix = 'region_legacy_report_counts';
//             } elseif ($isParareport) {
//                 $prefix = 'region_para_report_counts';
//             } elseif ($isInspection) {
//                 $prefix = 'region_inspection_audit_counts';
//             } else {
//                 $prefix = 'region_counts';
//             }

//             $countCacheKey = "{$prefix}:{$deptCode}:{$item->regioncode}:{$quarter}";

//             $countdetails = Cache::remember($countCacheKey, $cacheTime, function () use ($deptCode, $item, $quarter, $distCode, $countMethod) {
//                 return DashboardModel::{$countMethod}(
//                     $deptCode,
//                     $item->regioncode,
//                     $distCode,
//                     $quarter
//                 );
//             });

//             $this->assignCountDetails($item, $countdetails, $sourceForm, null);
//         }

//         return response()->json(['data' => $RegionwiseDetails]);
//     }

 public function RegionwiseDetails(Request $request)
    {
        $sessionchargedel = session('charge');

        $deptCode = $request->deptCode;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        $distCode = $request->distCode ?? null;
        $sourceForm = $request->sourceform;
        $quarter = $request->input('quarter');
        $financialyearcode = $request->input('financialyearcode');

        $isTemplateAudit = ($sourceForm === 'templateaudit');
        $isInspection = ($sourceForm === 'inspectionaudit');
        $isLegacyreport = ($sourceForm === 'legacyreport');
        $isParareport = ($sourceForm === 'parareport');
        $isParacount = ($sourceForm === 'paracount');
        $isRetirementPara = ($sourceForm === 'retirementpara');

        if ($isTemplateAudit) {
            $countMethod = 'GetTemplateAuditCounts';
        } elseif ($isLegacyreport) {
            $countMethod = 'GetLegacyReportCounts';
        } elseif ($isParareport) {
            $countMethod = 'GetParaReportCounts';
        } elseif ($isInspection) {
            $countMethod = 'GetInspectionCounts';
        } elseif ($isParacount) {
            $countMethod = 'GetParaCountDetails';
        } elseif ($isRetirementPara) {
            $countMethod = 'GetRetirementParaDetails';
            $roleActionCode = view()->shared('rtd_committee_roleaction');
        } else {
            $countMethod = 'GetcountDetails';
        }

        $cacheKey = ($isTemplateAudit ? 'region_template_audit_details' :
                    ($isLegacyreport ? 'region_legacy_report_details' :
                    ($isInspection ? 'region_inspection_audit_details' :
                    ($isRetirementPara ? 'region_retirement_para_details' :
                    'regionwise_details')))).":{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$financialyearcode}:{$sourceForm}";

        // Cache for 1 hour
        $cacheTime = now()->addHours(1);

        $RegionwiseDetails = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter, $financialyearcode, $isTemplateAudit) {
            if ($isTemplateAudit) {
                return DashboardModel::RegionTemplatewiseDetails($deptCode, $regionCode, $distCode, $quarter, $financialyearcode);
            } else {
                return DashboardModel::RegionwiseDetails($deptCode, $regionCode, $distCode, $quarter, $financialyearcode);
            }
        });

        foreach ($RegionwiseDetails as $item) {
            if ($isTemplateAudit) {
                $prefix = 'region_template_audit_counts';
            } elseif ($isLegacyreport) {
                $prefix = 'region_legacy_report_counts';
            } elseif ($isParareport) {
                $prefix = 'region_para_report_counts';
            } elseif ($isInspection) {
                $prefix = 'region_inspection_audit_counts';
            } elseif ($isParacount) {
                $prefix = 'region_para_count_details';
            } elseif ($isRetirementPara) {
                $prefix = 'region_retirement_para_counts';
            } else {
                $prefix = 'region_counts';
            }

            $countCacheKey = "{$prefix}:{$deptCode}:{$item->regioncode}:{$quarter}:{$financialyearcode}:{$sourceForm}";

            if ($isRetirementPara) {
                $countdetails = Cache::remember($countCacheKey, $cacheTime, function () use ($deptCode, $item, $quarter, $distCode, $countMethod, $roleActionCode , $financialyearcode) {
                    return DashboardModel::{$countMethod}(
                        $deptCode,
                        $item->regioncode,
                        $distCode,
                        $quarter,
                        $roleActionCode,
                        $financialyearcode
                    );
                });
            } else {
                $countdetails = Cache::remember($countCacheKey, $cacheTime, function () use ($deptCode, $item, $quarter, $distCode, $countMethod, $financialyearcode) {
                    return DashboardModel::{$countMethod}(
                        $deptCode,
                        $item->regioncode,
                        $distCode,
                        $quarter,
                        $financialyearcode
                    );
                });
            }

            $this->assignCountDetails($item, $countdetails, $sourceForm, null);
        }

        return response()->json(['data' => $RegionwiseDetails]);
    }

//   public function DistrictwiseDetails(Request $request)
//     {
//         $sessionchargedel = session('charge');

//         $deptCode = $request->deptCode;
//         $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
//         $distCode = $request->distCode;
//         $sourceForm = $request->sourceform;
//         $quarter = $request->input('quarter');

//         $isTemplateAudit = ($sourceForm === 'templateaudit');
//         $isInspectionAudit = ($sourceForm === 'inspectionaudit');
//         $isLegacyreport = ($sourceForm === 'legacyreport');
//         $isParareport = ($sourceForm === 'parareport');
//         $isParacount = ($sourceForm === 'paracount');

//         if ($isTemplateAudit) {
//             $countMethod = 'GetTemplateAuditCounts';
//         } elseif ($isLegacyreport) {
//             $countMethod = 'GetLegacyReportCounts';
//         } elseif ($isParareport) {
//             $countMethod = 'GetParaReportCounts';
//         } elseif ($isInspectionAudit) {
//             $countMethod = 'GetInspectionCounts';
//         } elseif ($isParacount) {
//             $countMethod = 'GetParaCountDetails';
//         } else {
//             $countMethod = 'GetcountDetails';
//         }

//         if ($isTemplateAudit) {
//             $cacheKeyPrefix = 'districtwise_template_audit_details';
//         } elseif ($isLegacyreport) {
//             $cacheKeyPrefix = 'districtwise_legacy_report_details';
//         } elseif ($isParareport) {
//             $cacheKeyPrefix = 'districtwise_para_report_details';
//         } elseif ($isInspectionAudit) {
//             $cacheKeyPrefix = 'districtwise_inspection_audit_details';
//         } elseif ($isParacount) {
//             $cacheKeyPrefix = 'districtwise_para_count_details';
//         } else {
//             $cacheKeyPrefix = 'districtwise_details';
//         }

//         $cacheKey = "{$cacheKeyPrefix}:{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$sourceForm}";

//         $cacheTime = now()->addHours(1);

//         $DistrictwiseDetails = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter) {
//             return DashboardModel::DistrictwiseDetails($deptCode, $regionCode, $distCode, $quarter);
//         });

//         foreach ($DistrictwiseDetails as $item) {
//             $countCacheKey = ($isLegacyreport
//                     ? 'district_legacy_report_counts'
//                     : ($isTemplateAudit ? 'district_template_audit_counts' : 'district_counts')).":{$deptCode}:{$item->regioncode}:{$item->distcode}:{$quarter}";

//             $countdetails = Cache::remember($countCacheKey, $cacheTime, function () use ($deptCode, $item, $quarter, $countMethod) {
//                 return DashboardModel::{$countMethod}(
//                     $deptCode,
//                     $item->regioncode,
//                     $item->distcode,
//                     $quarter
//                 );
//             });

//             $this->assignCountDetails($item, $countdetails, $sourceForm, $item->distcode);
//         }

//         return response()->json(['data' => $DistrictwiseDetails]);
//     }

public function DistrictwiseDetails(Request $request)
    {
        $sessionchargedel = session('charge');

        $deptCode = $request->deptCode;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        $distCode = $request->distCode;
        $sourceForm = $request->sourceform;
        $quarter = $request->input('quarter');
        $financialyearcode = $request->input('financialyearcode');

        $isTemplateAudit = ($sourceForm === 'templateaudit');
        $isInspectionAudit = ($sourceForm === 'inspectionaudit');
        $isLegacyreport = ($sourceForm === 'legacyreport');
        $isParareport = ($sourceForm === 'parareport');
        $isParacount = ($sourceForm === 'paracount');
        $isRetirementPara = ($sourceForm === 'retirementpara');

        if ($isTemplateAudit) {
            $countMethod = 'GetTemplateAuditCounts';
        } elseif ($isLegacyreport) {
            $countMethod = 'GetLegacyReportCounts';
        } elseif ($isParareport) {
            $countMethod = 'GetParaReportCounts';
        } elseif ($isInspectionAudit) {
            $countMethod = 'GetInspectionCounts';
        } elseif ($isParacount) {
            $countMethod = 'GetParaCountDetails';
        } elseif ($isRetirementPara) {
            $countMethod = 'GetRetirementParaDetails';
            $roleActionCode = view()->shared('rtd_committee_roleaction');
        } else {
            $countMethod = 'GetcountDetails';
        }

        if ($isTemplateAudit) {
            $cacheKeyPrefix = 'districtwise_template_audit_details';
        } elseif ($isLegacyreport) {
            $cacheKeyPrefix = 'districtwise_legacy_report_details';
        } elseif ($isParareport) {
            $cacheKeyPrefix = 'districtwise_para_report_details';
        } elseif ($isInspectionAudit) {
            $cacheKeyPrefix = 'districtwise_inspection_audit_details';
        } elseif ($isParacount) {
            $cacheKeyPrefix = 'districtwise_para_count_details';
        } elseif ($isRetirementPara) {
            $cacheKeyPrefix = 'districtwise_retirement_para_details';
        } else {
            $cacheKeyPrefix = 'districtwise_details';
        }

        $cacheKey = "{$cacheKeyPrefix}:{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$financialyearcode}:{$sourceForm}";

        $cacheTime = now()->addHours(1);

        $DistrictwiseDetails = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter, $financialyearcode) {
            return DashboardModel::DistrictwiseDetails($deptCode, $regionCode, $distCode, $quarter, $financialyearcode);
        });

        foreach ($DistrictwiseDetails as $item) {
            $countCacheKey = ($isLegacyreport
                    ? 'district_legacy_report_counts'
                    : ($isTemplateAudit ? 'district_template_audit_counts' :
                    ($isRetirementPara ? 'district_retirement_para_counts' :
                    'district_counts'))).":{$deptCode}:{$item->regioncode}:{$item->distcode}:{$quarter}:{$financialyearcode}:{$sourceForm}";

            // Pass $roleActionCode to the closure using 'use' if it's retirement para
            if ($isRetirementPara) {
                $countdetails = Cache::remember($countCacheKey, $cacheTime, function () use ($deptCode, $item, $quarter, $countMethod, $roleActionCode, $financialyearcode) {
                    return DashboardModel::{$countMethod}(
                        $deptCode,
                        $item->regioncode,
                        $item->distcode,
                        $quarter,
                        $roleActionCode,
                        $financialyearcode
                    );
                });
            } else {
                $countdetails = Cache::remember($countCacheKey, $cacheTime, function () use ($deptCode, $item, $quarter, $countMethod, $financialyearcode) {
                    return DashboardModel::{$countMethod}(
                        $deptCode,
                        $item->regioncode,
                        $item->distcode,
                        $quarter,
                        $financialyearcode
                    );
                });
            }

            $this->assignCountDetails($item, $countdetails, $sourceForm, $item->distcode);
        }

        return response()->json(['data' => $DistrictwiseDetails]);
    }


   private function assignCountDetails($item, $countdetails, $sourceForm, $distCode)
    {
        $item->distcode = $distCode;
        $item->distcount = $countdetails[0]['distcount'] ?? 0;
        $item->alloc_inscount = ($sourceForm == 'sliptabform')
            ? ($countdetails[0]['commencedinscount'] ?? 0)
            : ($countdetails[0]['alloc_inscount'] ?? 0);
        $item->totalslips = $countdetails[0]['totalslipcount'] ?? 0;
        $item->pendingslipcount = $countdetails[0]['pendingslipcount'] ?? 0;
        $item->convertedslipcount = $countdetails[0]['convertedslipcount'] ?? 0;
        $item->droppedslipcount = $countdetails[0]['droppedslipcount'] ?? 0;
        $item->audit_completed = $countdetails[0]['audit_completed'] ?? 0;
        $item->report_finalized = $countdetails[0]['report_finalized'] ?? 0;
        $item->report_issued = $countdetails[0]['report_issued'] ?? 0;
        $item->pending_finalize = $countdetails[0]['pending_finalize'] ?? 0;
        $item->pending_issue = $countdetails[0]['pending_issue'] ?? 0;

        $item->total_count = $countdetails[0]['total_count'] ?? 0;
        $item->pending_count = $countdetails[0]['pending_count'] ?? 0;
        $item->ongoing_count = $countdetails[0]['ongoing_count'] ?? 0;
        $item->completed_count = $countdetails[0]['completed_count'] ?? 0;

        $item->total_inspection_count = $countdetails[0]['total_inspection_count'] ?? 0;
        $item->pending_inspection_count = $countdetails[0]['pending_inspection_count'] ?? 0;
        $item->ongoing_inspection_count = $countdetails[0]['ongoing_inspection_count'] ?? 0;
        $item->completed_inspection_count = $countdetails[0]['completed_inspection_count'] ?? 0;
        $item->single_completed_inspection_count = $countdetails[0]['single_completed_inspection_count'] ?? 0;
        $item->multi_completed_inspection_count = $countdetails[0]['multi_completed_inspection_count'] ?? 0;
        // legacy
        $item->total_legacy_count = $countdetails[0]['totalcount'] ?? 0;
        $item->finalize_legacy_count = $countdetails[0]['legacycount'] ?? 0;
        $item->pending_legacy_count = $countdetails[0]['entrycount'] ?? 0;
        // para
        $item->totalparacount = $countdetails[0]['totalparacount'] ?? 0;
        $item->processedparacount = $countdetails[0]['processedparacount'] ?? 0;
        $item->pendingparacount = $countdetails[0]['pendingparacount'] ?? 0;

    }



//   public function RegionwiseAuditReportDetails(Request $request)
//     {
//         // Validate request parameters
//         $validated = $request->validate([
//             'deptCode' => 'nullable|string',
//             'regionCode' => 'nullable|string',
//             'distCode' => 'nullable|string',
//             'quarter' => 'required|string',
//             'viewType' => 'nullable|string',
//         ]);

//         $sessionchargedel = session('charge');

//         $deptCode = $validated['deptCode'] ?? null;
//         $regionCode = $validated['regionCode'] ?? $sessionchargedel->regioncode ?? null;
//         $distcode = $validated['distCode'] ?? $sessionchargedel->distcode ?? null;
//         $quarter = $validated['quarter'];
//         $viewType = $validated['viewType'] ?? null;

//         // Generate cache key
//         $cacheKey = "audit_report_region:{$deptCode}:{$regionCode}::{$distcode}:{$quarter}";
//         if ($viewType) {
//             $cacheKey .= ":{$viewType}";
//         }

//         $cacheTime = now()->addHours(1);

//         // Get data from cache or database using model
//         $reportData = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distcode, $quarter) {
//             return DashboardModel::getAuditReportRegionwise($quarter, $deptCode, $regionCode ,$distcode);
//         });

//         if ($viewType) {
//             $reportData = array_filter($reportData, function ($item) use ($viewType) {
//                 switch ($viewType) {
//                     case 'audit_completed':
//                         return $item->audit_completed > 0;
//                     case 'report_finalized':
//                         return $item->report_finalized > 0;
//                     case 'report_issued':
//                         return $item->report_issued > 0;
//                     case 'pending_finalize':
//                         return $item->pending_finalize > 0;
//                     case 'pending_issue':
//                         return $item->pending_issue > 0;
//                     default:
//                         return true;
//                 }
//             });
//         }

//         return response()->json([
//             'success' => true,
//             'data' => $reportData,
//             'type' => 'region_wise',
//         ]);
//     }

 public function RegionwiseAuditReportDetails(Request $request)
    {
        // Validate request parameters
        $validated = $request->validate([
            'deptCode' => 'nullable|string',
            'regionCode' => 'nullable|string',
            'distCode' => 'nullable|string',
            'quarter' => 'required|string',
            'financialyearcode' => 'nullable|string',
            'viewType' => 'nullable|string',
        ]);

        $sessionchargedel = session('charge');

        $deptCode = $validated['deptCode'] ?? null;
        $regionCode = $validated['regionCode'] ?? $sessionchargedel->regioncode ?? null;
        $distcode = $validated['distCode'] ?? $sessionchargedel->distcode ?? null;
        $quarter = $validated['quarter'];
        $financialyearcode = $validated['financialyearcode'] ?? null;
        $viewType = $validated['viewType'] ?? null;

        // Generate cache key
        $cacheKey = "audit_report_region:{$deptCode}:{$regionCode}::{$distcode}:{$quarter}:{$financialyearcode}";
        if ($viewType) {
            $cacheKey .= ":{$viewType}";
        }

        $cacheTime = now()->addHours(1);

        // Get data from cache or database using model
        $reportData = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distcode, $quarter, $financialyearcode) {
            return DashboardModel::getAuditReportRegionwise($quarter, $deptCode, $regionCode ,$distcode, $financialyearcode);
        });

        if ($viewType) {
            $reportData = array_filter($reportData, function ($item) use ($viewType) {
                switch ($viewType) {
                    case 'audit_completed':
                        return $item->audit_completed > 0;
                    case 'report_finalized':
                        return $item->report_finalized > 0;
                    case 'report_issued':
                        return $item->report_issued > 0;
                    case 'pending_finalize':
                        return $item->pending_finalize > 0;
                    case 'pending_issue':
                        return $item->pending_issue > 0;
                    default:
                        return true;
                }
            });
        }

        return response()->json([
            'success' => true,
            'data' => $reportData,
            'type' => 'region_wise',
        ]);
    }



    // public function DistrictwiseAuditReportDetails(Request $request)
    // {
    //     // Validate request parameters
    //     $validated = $request->validate([
    //         'deptCode' => 'nullable|string',
    //         'regionCode' => 'nullable|string',
    //         'distCode' => 'nullable|string',
    //         'quarter' => 'required|string',
    //         'viewType' => 'nullable|string',
    //     ]);

    //     $sessionchargedel = session('charge');

    //     $deptCode = $validated['deptCode'] ?? null;
    //     $regionCode = $validated['regionCode'] ?? $sessionchargedel->regioncode ?? null;
    //     $distCode = $validated['distCode'] ?? null;
    //     $quarter = $validated['quarter'];
    //     $viewType = $validated['viewType'] ?? null;

    //     // Generate cache key
    //     $cacheKey = "audit_report_district:{$deptCode}:{$regionCode}:{$distCode}:{$quarter}";
    //     if ($viewType) {
    //         $cacheKey .= ":{$viewType}";
    //     }
    //     $cacheTime = now()->addHours(1);

    //     // Get data from cache or database using model
    //     $results = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter, $viewType) {
    //         return DashboardModel::getAuditReportDistrictwise($quarter, $deptCode, $regionCode, $distCode, $viewType);
    //     });

    //     return response()->json([
    //         'success' => true,
    //         'data' => $results,
    //         'type' => 'district_wise',
    //     ]);
    // }

     public function DistrictwiseAuditReportDetails(Request $request)
    {
        // Validate request parameters
        $validated = $request->validate([
            'deptCode' => 'nullable|string',
            'regionCode' => 'nullable|string',
            'distCode' => 'nullable|string',
            'quarter' => 'required|string',
            'viewType' => 'nullable|string',
            'financialyearcode' => 'nullable|string',
        ]);

        $sessionchargedel = session('charge');

        $deptCode = $validated['deptCode'] ?? null;
        $regionCode = $validated['regionCode'] ?? $sessionchargedel->regioncode ?? null;
        $distCode = $validated['distCode'] ?? null;
        $quarter = $validated['quarter'];
        $viewType = $validated['viewType'] ?? null;
        $financialyearcode = $validated['financialyearcode'] ?? null;

        // Generate cache key
        $cacheKey = "audit_report_district:{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$financialyearcode}";
        if ($viewType) {
            $cacheKey .= ":{$viewType}";
        }
        $cacheTime = now()->addHours(1);

        // Get data from cache or database using model
        $results = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter, $viewType, $financialyearcode) {
            return DashboardModel::getAuditReportDistrictwise($quarter, $deptCode, $regionCode, $distCode, $viewType, $financialyearcode);
        });

        return response()->json([
            'success' => true,
            'data' => $results,
            'type' => 'district_wise',
        ]);
    }


    //  public function InstitutionwiseAuditReportDetails(Request $request)
    // {
    //     // Validate request parameters
    //     $validated = $request->validate([
    //         'deptCode' => 'nullable|string',
    //         'regionCode' => 'nullable|string',
    //         'distCode' => 'nullable|string',
    //         'quarter' => 'required|string',
    //         'viewType' => 'nullable|string',
    //     ]);

    //     $sessionchargedel = session('charge');

    //     $deptCode = $validated['deptCode'] ?? null;
    //     $regionCode = $validated['regionCode'] ?? $sessionchargedel->regioncode ?? null;
    //     $distCode = $validated['distCode'] ?? null;
    //     $quarter = $validated['quarter'];
    //     $viewType = $validated['viewType'] ?? null;

    //     // Generate cache key
    //     $cacheKey = "audit_report_institution:{$deptCode}:{$regionCode}:{$distCode}:{$quarter}";
    //     if ($viewType) {
    //         $cacheKey .= ":{$viewType}";
    //     }
    //     $cacheTime = now()->addHours(1);

    //     $results = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter, $viewType) {
    //         $data = DashboardModel::getAuditReportInstitutionDetails($quarter, $deptCode, $regionCode, $distCode);
    //         if ($viewType) {
    //             $data = array_filter($data, function ($item) use ($viewType) {
    //                 switch ($viewType) {
    //                     case 'audit_completed':
    //                         return $item->audit_completed === 'Completed';
    //                     case 'report_finalized':
    //                         return $item->report_finalized === 'Finalized';
    //                     case 'report_issued':
    //                         return $item->report_issued === 'Issued';
    //                     case 'pending_finalize':
    //                         return $item->pending_finalize === 'Pending Finalization';
    //                     case 'pending_issue':
    //                         return $item->pending_issue === 'Pending Issue';
    //                     default:
    //                         return true;
    //                 }
    //             });
    //         }

    //         return array_values($data);
    //     });

    //     return response()->json([
    //         'success' => true,
    //         'data' => $results,
    //         'type' => 'institution_wise',
    //     ]);
    // }

     public function InstitutionwiseAuditReportDetails(Request $request)
    {
        // Validate request parameters
        $validated = $request->validate([
            'deptCode' => 'nullable|string',
            'regionCode' => 'nullable|string',
            'distCode' => 'nullable|string',
            'quarter' => 'required|string',
            'viewType' => 'nullable|string',
            'financialyearcode' => 'nullable|string',
        ]);

        $sessionchargedel = session('charge');

        $deptCode = $validated['deptCode'] ?? null;
        $regionCode = $validated['regionCode'] ?? $sessionchargedel->regioncode ?? null;
        $distCode = $validated['distCode'] ?? null;
        $quarter = $validated['quarter'];
        $financialyearcode = $validated['financialyearcode'] ?? null;
        $viewType = $validated['viewType'] ?? null;

        // Generate cache key
        $cacheKey = "audit_report_institution:{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$financialyearcode}";
        if ($viewType) {
            $cacheKey .= ":{$viewType}";
        }
        $cacheTime = now()->addHours(1);

        $results = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter, $viewType, $financialyearcode) {
            $data = DashboardModel::getAuditReportInstitutionDetails($quarter, $deptCode, $regionCode, $distCode, $financialyearcode);
            if ($viewType) {
                $data = array_filter($data, function ($item) use ($viewType) {
                    switch ($viewType) {
                        case 'audit_completed':
                            return $item->audit_completed === 'Completed';
                        case 'report_finalized':
                            return $item->report_finalized === 'Finalized';
                        case 'report_issued':
                            return $item->report_issued === 'Issued';
                        case 'pending_finalize':
                            return $item->pending_finalize === 'Pending Finalization';
                        case 'pending_issue':
                            return $item->pending_issue === 'Pending Issue';
                        default:
                            return true;
                    }
                });
            }

            return array_values($data);
        });

        return response()->json([
            'success' => true,
            'data' => $results,
            'type' => 'institution_wise',
        ]);
    }


    // Helper method to clear cache when data changes (optional but recommended)
   public function clearDashboardCache($deptCode = null, $regionCode = null, $distCode = null, $quarter = null)
    {
        $cachePrefixes = [
            'regionwise_details',
            'region_template_audit_details',
            'region_inspection_audit_details',
            'districtwise_details',
            'districtwise_template_audit_details',
            'district_inspection_audit_details',
            'region_counts',
            'region_template_audit_counts',
            'region_inspection_audit_counts',
            'district_counts',
            'district_template_audit_counts',
            'district_inspection_audit_counts',
            'audit_report_region',
            'audit_report_district',
            'audit_report_institution',
            'commenced_institutes',
            'auditee_institute_details',
            'auditee_commenced_institutes',
            'auditee_districtwise_details',
            'auditee_regionwise_details',
            'auditee_district_counts',
            'auditee_region_counts',
            'auditee_audit_report_region',
            'auditee_report_district',
            'auditee_report_institution',
            'institute_details',
            'template_audit_institute_details',
            'inspection_audit_institute_details',
            'districtwise_legacy_report_details',
            'district_legacy_report_counts',
            'region_legacy_report_details',
            'region_legacy_report_counts',
            'districtwise_para_report_details',
            'region_para_report_counts'

        ];
        foreach ($cachePrefixes as $prefix) {
            $pattern = "{$prefix}:{$deptCode}:{$regionCode}:{$distCode}:{$quarter}*";
            Cache::flush($pattern);
        }
    }


    public function auditee_dashboardcount(Request $request)
    {
        $charge = session('charge');
        $usertypecode = $charge->usertypecode ?? null;
        $user = session('user');
        $userId = $user->userid ?? null;

        // Profile update flag
        $profileUpdate = null;

        if ($usertypecode && $userId) {
            if ($usertypecode === 'A') {
                $userRecord = DB::table('audit.deptuserdetails')->where('deptuserid', $userId)->first();
            } elseif ($usertypecode === 'I') {
                $userRecord = DB::table('audit.audtieeuserdetails')->where('auditeeuserid', $userId)->first();
            }

            if (isset($userRecord) && $userRecord->profile_update === 'Y') {
                $profileUpdate = 'Y';
            }
        }

        // Get session data
        $sessionChargeDetails = session('charge');
        $sessionRoleType = $sessionChargeDetails->roletypecode ?? null;
        $deptCode = $sessionChargeDetails->deptcode ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;
        $userTypeCode = $sessionChargeDetails->usertypecode ?? null; // Corrected variable name
        $roleTypeCode = $sessionChargeDetails->roletypecode ?? null; // Corrected variable name

        // Default auditScheduleId to 0 if not provided
        $auditScheduleId = $request->input('auditscheduleid', 0); // Use the value from the request or default to 0
        //  echo $auditScheduleId;
        $user = session('user');
        $userChargeId = $user->userid ?? null;

        // echo($headinstitutionDetails);

        // Fetch count details using the PostgreSQL function
        $countDetails = DashboardModel::fetchCountDetails(
            $deptCode,
            $regionCode,
            $distCode,
            $userChargeId,
            $userTypeCode,
            $roleTypeCode,
            $auditScheduleId
        );

        $intimationcount = DashboardModel::getinitimationcount($userChargeId, $deptCode);

        // print_r ($countDetails[0]);
        // return view('dashboard.auditeedashboard', compact('countDetails','profileUpdate','intimationcount'));

        return view('dashboard.old_auditeedashboard', compact('countDetails', 'profileUpdate', 'intimationcount'));
        // return view('dashboard.dashboard', compact('dept', 'dist', 'institutionDetails', 'countDetails','year','headinstitutionDetails'));

    }


public function dynamic_modal($viewName)
    {
        $charge = session('charge');
        $usertypecode = $charge->usertypecode ?? null;

        $user = session('user');
        $userId = $user->userid ?? null;

        $profileUpdate = null;
        $auditeeMobile = null;
        $auditeeUserVerifiedFlag = null;
        $auditeeMobVerify = null;
        $otpMobile = null;
        $otpMobVerify = null;
        $isOtpUserHome = false;
        $otpLoginDayCount = null;

        $isOtpUserHome = false;
        if ($usertypecode && $userId) {
            if ($usertypecode === 'A') {
                $userRecord = DB::table('audit.deptuserdetails')->where('deptuserid', $userId)->first();
                if ($userRecord && isset($userRecord->mobilenumber)) {
                    $otpMobile = $userRecord->mobilenumber;
                }
                if ($userRecord && isset($userRecord->mob_verify)) {
                    $otpMobVerify = $userRecord->mob_verify;
                }
                // dd($otpMobVerify);
                $otpRoleActionCodes = \View::shared('otp_roleactioncodes') ?? [];
                $isOtpUserHome = DB::table('audit.deptuserdetails as du')
                    ->join('audit.userchargedetails as u', 'u.userid', '=', 'du.deptuserid')
                    ->join('audit.chargedetails as c', 'c.chargeid', '=', 'u.chargeid')
                    ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
                    ->join('audit.mst_roleaction as ra', 'ra.roleactioncode', '=', 'rm.roleactioncode')
                    ->where('du.statusflag', 'Y')
                    ->where('u.statusflag', 'Y')
                    ->where('u.chargeflag', 'P')
                    ->where('rm.statusflag', 'Y')
                    ->where('ra.statusflag', 'Y')
                    ->whereIn('ra.roleactioncode', $otpRoleActionCodes)
                    ->where('du.deptuserid', $userId)
                    ->exists();
                $lastLogin = DB::table('audit.userlogindetails')
                    ->where('userid', $userId)
                    // ->where('usertypecode', 'A')
                    ->orderByDesc('loginid')
                    ->first();
                if ($lastLogin && isset($lastLogin->userverifed_flag)) {
                    $auditeeUserVerifiedFlag = $lastLogin->userverifed_flag;
                }
                $otpLoginDayCount = DB::table('audit.userlogindetails')
                    ->where('userid', $userId)
                    ->where('userverifed_flag',  'Y')
                    ->selectRaw('COUNT(DISTINCT DATE(logintime)) as cnt')
                    ->value('cnt');


            } elseif ($usertypecode === 'I') {
                $userRecord = DB::table('audit.audtieeuserdetails')->where('auditeeuserid', $userId)->first();
                if ($userRecord && isset($userRecord->mobilenumber)) {
                    $auditeeMobile = $userRecord->mobilenumber;
                    $otpMobile = $userRecord->mobilenumber;
                }
                if ($userRecord && isset($userRecord->mob_verify)) {
                    $auditeeMobVerify = $userRecord->mob_verify;
                    $otpMobVerify = $userRecord->mob_verify;
                }
                $lastLogin = DB::table('audit.userlogindetails')
                    ->where('userid', $userId)
                    ->where('usertypecode', 'I')
                    ->orderByDesc('loginid')
                    ->first();
                if ($lastLogin && isset($lastLogin->userverifed_flag)) {
                    $auditeeUserVerifiedFlag = $lastLogin->userverifed_flag;
                }
                $isOtpUserHome = true;
                $otpLoginDayCount = DB::table('audit.userlogindetails')
                    ->where('userid', $userId)
                    ->where('usertypecode', 'I')
                    ->where('userverifed_flag',  'Y')
                    ->selectRaw('COUNT(DISTINCT DATE(logintime)) as cnt')
                    ->value('cnt');


            }elseif ($usertypecode === 'H') {
                 $userRecord = DB::table('audit.auditee_dept')->where('auditeedeptid', $userId)->first();
             }

            if (isset($userRecord) && $userRecord->profile_update === 'Y') {
                $profileUpdate = 'Y';
            }
        }

        return view($viewName, compact(
            'profileUpdate',
            'auditeeMobile',
            'auditeeUserVerifiedFlag',
            'auditeeMobVerify',
            'otpMobile',
            'otpMobVerify',
            'isOtpUserHome',
            'otpLoginDayCount'
        ));
    }



 public function getQuartersByFinancialYear(Request $request)
    {
        $financialYearCode = $request->input('financialyearcode');
        $deptCode = $request->input('deptcode');

        if (empty($financialYearCode)) {
            return response()->json([
                'success' => false,
                'message' => 'Financial year code is required'
            ]);
        }

        $query = DB::table('audit.auditplan as plan')
            ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'plan.planmappingid')
            ->where('apm.financialyearcode', $financialYearCode)
            ->whereIn('apm.statusflag', ['F', 'P', 'Y']);

        if ($deptCode && $deptCode !== 'all') {
            $quarters = $query
                ->where('apm.deptcode', $deptCode)
                ->select(
                    'apm.group_key',
                    'apm.planname',
                    'apm.prioritycode'
                )
                ->groupBy('apm.group_key', 'apm.planname', 'apm.prioritycode')
                ->orderByRaw('MIN(apm.planmappingid) DESC')
                ->get();
        }

        else {
            $quarters = $query
                ->select(
                    'apm.group_key',
                    DB::raw('MIN(apm.planname) as planname'),
                    'apm.prioritycode'
                )
                ->groupBy('apm.group_key', 'apm.prioritycode')
                ->orderByRaw('MIN(apm.planmappingid) DESC')
                ->get();
        }

        return response()->json([
            'success' => true,
            'quarters' => $quarters,
            'financial_year_code' => $financialYearCode,
            'dept_code' => $deptCode
        ]);
    }


}
