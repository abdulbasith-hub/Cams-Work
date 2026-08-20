<?php

namespace App\Http\Controllers;

use App\Models\AuditModel;
use App\Models\AuditTeamModel;
use App\Models\Charge;
use App\Models\DashboardModel;
use App\Models\ReportModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
// use DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuditeeDashboardController extends Controller
{

    protected $connection = 'dashboard';

    protected static $dbConnection = 'dashboard';

    private static function db()
    {
        try {
            $conn = DB::connection(self::$dbConnection);
            $conn->getPdo();
            return $conn;

        } catch (\Throwable $e) {
            throw new NotFoundHttpException('Dashboard database not reachable.');
        }
    }


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

        return view('dashboard.dashboard', compact(
            'profileUpdate',
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

    public function AuditeeDashboard(Request $request)
    {
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

        $sessionChargeDetails = session('charge');
        $sessionRoleType = $sessionChargeDetails->roletypecode ?? null;
        $deptCode = $sessionChargeDetails->deptcode ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;
        $userTypeCode = $sessionChargeDetails->usertypecode ?? null;
        $roleTypeCode = $sessionChargeDetails->roletypecode ?? null;
        $auditeeDeptCode = $sessionChargeDetails->auditeeDeptCode ?? null;
        $catcode = $sessionChargeDetails->catcode ?? null;
        $subcatcode = $sessionChargeDetails->subcatcode ?? null;

        $auditScheduleId = $request->input('auditscheduleid', 0);
        $userChargeId = $user->userid ?? null;

        $departments = DashboardModel::fetchDeptDetails($deptCode);
        $financialyear = ReportModel::getDFinancialyear();

        $selectedFinancialYear = $request->input('financialyear');

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

        $countdetailsofsch = DashboardModel::GetAuditeeCountDetails(
            $deptCode,
            $regionCode,
            $distCode,
            $quarter = null,
            $auditeeDeptCode,
            $catcode,
            $subcatcode,
            $selectedFinancialYear

        );

        return view('dashboard.auditeedashboard', compact(
            'profileUpdate',
            'countdetailsofsch',
            'auditQuarters',
            'departments',
            'usertypecode',
            'financialyear',
            'selectedFinancialYear',
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

    public function AuditeeDeptWiseAjax(Request $request)
    {
        $sourceForm = $request->input('source_form');
        $sessionChargeDetails = session('charge');
        $deptCode = $request->input('deptcode') ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;
        $auditeeDeptCode = $request->input('auditeeDeptCode') ?? null;
        $quarterVal = $request->input('quarter');
        $slipQuarterVal = $request->input('quarterslip');
        $catcode = $request->input('catcode') ?? null;
        $subcatcode = $request->input('subcatcode') ?? null;
        $financialyearcode = $request->input('financialyear');

        $finalCatcode = $this->prepareCategoryParameter($catcode, 'category');
        // dd($finalCatcode);
        $finalSubcatcode = $this->prepareCategoryParameter($subcatcode, 'subcategory');

        if ($sourceForm === 'plantabform' || $sourceForm === 'auditreport' ||  $sourceForm === 'templateaudit' || $sourceForm === 'parareport' || $sourceForm === 'inspectionaudit') {
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
                $countdetails = DashboardModel::GetAuditeeReportCounts($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode);

                $deptData = [
                    'deptname' => $getDeptName[0]->deptelname ?? '',
                    'deptsname' => $getDeptName[0]->deptesname,
                    'audit_completed' => $countdetails[0]->audit_completed_institution ?? 0,
                    'report_finalized' => $countdetails[0]->report_finalized ?? 0,
                    'report_issued' => $countdetails[0]->report_issued ?? 0,
                    'pending_finalize' => $countdetails[0]->pending_to_finalize ?? 0,
                    'pending_issue' => $countdetails[0]->pending_to_issue ?? 0,
                    'deptCode' => $deptCode,
                    'regionCode' => $regionCode,
                    'distCode' => $distCode,
                    'auditeedeptCode' => $auditeeDeptCode,
                    'regioncount' => null,
                    'distcount' => null,
                    'auditeedeptcount' => null,
                    'alloc_inscount' => null,
                    'totalslips' => null,
                    'pendingslipcount' => null,
                    'convertedslipcount' => null,
                    'droppedslipcount' => null,
                ];

                // Add to totals
                $totals['audit_completed'] += $deptData['audit_completed'];
                $totals['report_finalized'] += $deptData['report_finalized'];
                $totals['report_issued'] += $deptData['report_issued'];
                $totals['pending_finalize'] += $deptData['pending_finalize'];
                $totals['pending_issue'] += $deptData['pending_issue'];

                $deptwisedata[] = $deptData;

            } elseif ($sourceForm === 'templateaudit') {

                $countdetails = DashboardModel::GetAuditeeTemplateAuditCounts($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode)[0];

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

                $countdetails = DashboardModel::GetAuditeeInspectionCounts($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode)[0];

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
                ];
            } elseif ($sourceForm === 'parareport') {

                $countdetails = DashboardModel::GetAuditeeParaReportCounts($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode , $financialyearcode);

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
            } else {
                $countdetailsResult = DashboardModel::GetAuditeeCountDetails($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode);

                $countdetails = $countdetailsResult[0] ?? [];

                if ($sourceForm == 'sliptabform') {
                    $countdetails['alloc_inscount'] = $countdetails['commencedinscount'] ?? 0;
                }

                $deptwisedata[] = [
                    'deptname' => $getDeptName[0]->deptelname ?? '',
                    'regioncount' => $countdetails['regioncount'] ?? 0,
                    'distcount' => $countdetails['distcount'] ?? 0,
                    'auditeedeptcount' => $countdetails['auditeedeptcount'] ?? 0,
                    'alloc_inscount' => $countdetails['alloc_inscount'] ?? 0,
                    'deptCode' => $deptCode,
                    'regionCode' => $regionCode,
                    'distCode' => $distCode,
                    'totalslips' => $countdetails['totalslipcount'] ?? 0,
                    'pendingslipcount' => $countdetails['pendingslipcount'] ?? 0,
                    'convertedslipcount' => $countdetails['convertedslipcount'] ?? 0,
                    'droppedslipcount' => $countdetails['droppedslipcount'] ?? 0,
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
                $auditeeDeptCode = null;

                if ($sourceForm == 'auditreport') {
                    $countdetails = DashboardModel::GetAuditeeReportCounts($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode);

                    $deptData = [
                        'deptname' => $deptval->deptelname,
                        'audit_completed' => $countdetails[0]->audit_completed_institution ?? 0,
                        'report_finalized' => $countdetails[0]->report_finalized ?? 0,
                        'report_issued' => $countdetails[0]->report_issued ?? 0,
                        'pending_finalize' => $countdetails[0]->pending_to_finalize ?? 0,
                        'pending_issue' => $countdetails[0]->pending_to_issue ?? 0,
                        'deptCode' => $deptCode,
                        'regionCode' => $regionCode,
                        'distCode' => $distCode,
                        'auditeedeptCode' => $auditeeDeptCode,
                        'regioncount' => null,
                        'distcount' => null,
                        'auditeedeptcount' => null,
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
                    $getdetails = DashboardModel::GetAuditeeTemplateAuditCounts($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode)[0];

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

                    $getdetails = DashboardModel::GetAuditeeInspectionCounts($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode)[0];

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
                    ];
                } elseif ($sourceForm === 'parareport') {

                    $getdetails = DashboardModel::GetAuditeeParaReportCounts($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode);

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
                } else {
                    $getdetails = DashboardModel::GetAuditeeCountDetails($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode , $financialyearcode);

                    if ($sourceForm == 'sliptabform') {
                        $getdetails['alloc_inscount'] = $getdetails['commencedinscount'] ?? 0;
                    }

                    $deptwisedata[] = [
                        'deptname' => $deptval->deptelname,
                        'deptsname' => $deptval->deptesname,
                        'regioncount' => $getdetails['regioncount'] ?? 0,
                        'distcount' => $getdetails['distcount'] ?? 0,
                        'auditeedeptcount' => $getdetails['auditeedeptcount'] ?? 0,
                        'alloc_inscount' => $getdetails['alloc_inscount'] ?? 0,
                        'deptCode' => $deptCode,
                        'regionCode' => $regionCode,
                        'distCode' => $distCode,
                        'totalslips' => $getdetails['totalslipcount'] ?? 0,
                        'pendingslipcount' => $getdetails['pendingslipcount'] ?? 0,
                        'convertedslipcount' => $getdetails['convertedslipcount'] ?? 0,
                        'droppedslipcount' => $getdetails['droppedslipcount'] ?? 0,
                        'audit_completed' => null,
                        'report_finalized' => null,
                        'report_issued' => null,
                        'pending_finalize' => null,
                        'pending_issue' => null,
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
                'auditeedeptcount' => null,
                'alloc_inscount' => null,
                'totalslips' => null,
                'pendingslipcount' => null,
                'convertedslipcount' => null,
                'droppedslipcount' => null,
            ];
        }

        return response()->json(['data' => $deptwisedata]);
    }

    private function prepareCategoryParameter($categoryValue, $type = 'category')
    {
        if (empty($categoryValue) || $categoryValue === 'null') {
            return null;
        }

        if ($categoryValue === 'ALL') {
            return null;
        }

        if ($categoryValue === 'ALL_SESSION') {
            $sessionChargeDetails = session('charge');

            if ($sessionChargeDetails) {
                if ($type === 'category') {
                    if (isset($sessionChargeDetails->catcode)) {
                        $catArray = json_decode($sessionChargeDetails->catcode, true);
                        if (isset($catArray['1']) && is_array($catArray['1']) && ! in_array('A', $catArray['1'])) {
                            return implode(',', $catArray['1']);
                        }
                    }
                } else {
                    if (isset($sessionChargeDetails->auditeeins_subcategoryid)) {
                        $subcatArray = json_decode($sessionChargeDetails->auditeeins_subcategoryid, true);
                        if (isset($subcatArray['1']) && is_array($subcatArray['1']) && ! in_array('A', $subcatArray['1'])) {
                            return implode(',', $subcatArray['1']);
                        }
                    }
                }
            }

            return null;
        }

        if (strpos($categoryValue, ',') !== false) {
            return $categoryValue;
        }

        return $categoryValue;
    }

    public function AuditeeInstitutedetailsGet(Request $request)
    {
        $sessionchargedel = session('charge');

        $deptCode = $request->deptCode;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        $distCode = $request->distCode;
        $quarter = $request->input('quarter');
        $auditeeDeptCode = $request->auditeeDeptCode;
        $catcode = $request->catcode;
        $subcatcode = $request->subcatcode;
        $sourceForm = $request->sourceform;
        $financialyearcode = $request->input('financialyear');

        $finalCatcode = $this->prepareCategoryParameter($catcode, 'category');
        $finalSubcatcode = $this->prepareCategoryParameter($subcatcode, 'subcategory');
        $isTemplateAudit = ($sourceForm === 'templateaudit');
        $isInspectionAudit = ($sourceForm === 'inspectionaudit');
        $isParareport = ($sourceForm === 'parareport');

        $cacheKey = ($isTemplateAudit ? 'template_audit_auditee_institute_details' : 'auditee_institute_details').":{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$sourceForm}:{$auditeeDeptCode}:{$finalCatcode}:{$finalSubcatcode}:{$financialyearcode}";

        $institutes = Cache::remember($cacheKey, now()->addHours(1), function () use ($deptCode, $regionCode, $distCode, $quarter, $isTemplateAudit, $isInspectionAudit, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $isParareport, $financialyearcode) {
            if ($isTemplateAudit) {
                return DashboardModel::AuditeeTemplateInstitutedetailsGet($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode , $financialyearcode);
            } elseif ($isInspectionAudit) {
                return DashboardModel::AuditeeInspectionInstitutedetailsGet($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode , $financialyearcode);
            } elseif ($isParareport) {
                return DashboardModel::AuditeeParaInstitutionwiseData($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode , $financialyearcode);
            } else {
                return DashboardModel::AuditeeInstitutedetailsGet($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode , $financialyearcode);
            }
        });

        if (! $isTemplateAudit && ! $isInspectionAudit) {

            $institutes = collect($institutes)->map(function ($item) {
                $item['encrypted_auditscheduleid'] = Crypt::encryptString($item['auditscheduleid']);

                return $item;
            })->values();
        }

        return response()->json(['data' => $institutes]);
    }

    public function CommencedInstitutedetailsGet(Request $request)
    {
        $sessionchargedel = session('charge');

        $deptCode = $request->deptCode;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        $distCode = $request->distCode;
        $whichslip = $request->whichslip;
        $quarter = $request->input('quarter');
        $financialyearcode = $request->input('financialyear');

        // Create a unique cache key based on all parameters
        $cacheKey = "commenced_institutes:{$deptCode}:{$regionCode}:{$distCode}:{$whichslip}:{$quarter}";

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

    public function AuditeeCommencedInstitutedetailsGet(Request $request)
    {
        $sessionchargedel = session('charge');

        $deptCode = $request->deptCode;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        $distCode = $request->distCode;
        $whichslip = $request->whichslip;
        $auditeeDeptCode = $request->auditeeDeptCode;
        $catcode = $request->catcode;
        $subcatcode = $request->subcatcode;
        $quarter = $request->input('quarter');
        $financialyearcode = $request->input('financialyear');

        $finalCatcode = $this->prepareCategoryParameter($catcode, 'category');
        $finalSubcatcode = $this->prepareCategoryParameter($subcatcode, 'subcategory');

        $cacheKey = "auditee_commenced_institutes:{$deptCode}:{$regionCode}:{$distCode}:{$whichslip}:{$quarter}:{$auditeeDeptCode}:{$finalCatcode}:{$finalSubcatcode}:{$financialyearcode}";

        $institutes = Cache::remember($cacheKey, 3600, function () use ($deptCode, $regionCode, $distCode, $whichslip, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode) {
            return DashboardModel::AuditeeCommencedInstitutedetailsGet($deptCode, $regionCode, $distCode, $whichslip, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode);
        });

        foreach ($institutes as $item) {
            $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
        }

        return response()->json(['data' => $institutes]);
    }

    public function getauditslipdetails(Request $request)
    {
        $auditscheduleid = $request->auditscheduleid;

        $alldetails = DashboardModel::getslipcount($auditscheduleid);

        return $alldetails;

    }

    public function AuditeeRegionwiseDetails(Request $request)
    {
        $sessionchargedel = session('charge');

        $deptCode = $request->deptCode;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        $distCode = $request->distCode ?? null;
        $sourceForm = $request->sourceform;
        $auditeeDeptCode = $request->auditeeDeptCode;
        $catcode = $request->catcode;
        $subcatcode = $request->subcatcode;
        $quarter = $request->input('quarter');
        $financialyearcode = $request->input('financialyear');

        $finalCatcode = $this->prepareCategoryParameter($catcode, 'category');
        $finalSubcatcode = $this->prepareCategoryParameter($subcatcode, 'subcategory');

        $isTemplateAudit = ($sourceForm === 'templateaudit');
        $isInspection = ($sourceForm === 'inspectionaudit');
        $isParareport = ($sourceForm === 'parareport');
        if ($isParareport) {
            $countMethod = 'GetAuditeeParaReportCounts';
        } elseif ($isTemplateAudit) {
            $countMethod = 'GetAuditeeTemplateAuditCounts';
        } elseif ($isInspection) {
            $countMethod = 'GetAuditeeInspectionCounts';
        } else {
            $countMethod = 'GetAuditeeCountDetails';
        }

        $cacheKey = ($isTemplateAudit ? 'region_auditee_template_audit_details' : ($isInspection ? 'region_auditee_inspection_audit_details' : 'regionwise_auditee_details')).":{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$sourceForm}:{$auditeeDeptCode}:{$finalCatcode}:{$finalSubcatcode}";

        $cacheTime = now()->addHours(1);

        $RegionwiseDetails = Cache::remember($cacheKey, $cacheTime, function () use ($isTemplateAudit, $deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode) {
            if ($isTemplateAudit) {
                return DashboardModel::AuditeeTemplateRegionwiseDetails($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode);
            } else {
                return DashboardModel::AuditeeRegionwiseDetails($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode , $financialyearcode);
            }
        });

        foreach ($RegionwiseDetails as $item) {
            $countCacheKey = ($isTemplateAudit ? 'region_auditee_template_audit_counts' : ($isInspection ? 'region_auditee_inspection_audit_counts' : 'region_auditee_counts')).":{$deptCode}:{$item->regioncode}:{$quarter}:{$auditeeDeptCode}:{$finalCatcode}:{$finalSubcatcode}";

            $countdetails = Cache::remember($countCacheKey, $cacheTime, function () use ($deptCode, $item, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $countMethod ,$distCode , $financialyearcode) {
                return DashboardModel::{$countMethod}(
                    $deptCode,
                    $item->regioncode,
                    $distCode,
                    $quarter,
                    $auditeeDeptCode,
                    $finalCatcode,
                    $finalSubcatcode,
                    $financialyearcode
                );
            });

            $this->assignCountDetails($item, $countdetails, $sourceForm, $distCode);
        }
        return response()->json(['data' => $RegionwiseDetails]);
    }

    public function DistrictwiseDetails(Request $request)
    {

        $sessionchargedel = session('charge');

        $deptCode = $request->deptCode;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        $distCode = $request->distCode;
        $sourceForm = $request->sourceform;
        $quarter = $request->input('quarter');

        $financialyearcode = $request->input('financialyear');



        $cacheKey = "districtwise_details:{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$sourceForm}";
        $cacheTime = now()->addHours(1);

        $DistrictwiseDetails = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter, $financialyearcode) {
            return DashboardModel::DistrictwiseDetails($deptCode, $regionCode, $distCode, $quarter, $financialyearcode);
        });

        foreach ($DistrictwiseDetails as $item) {
            $countCacheKey = "district_counts:{$deptCode}:{$item->regioncode}:{$item->distcode}:{$quarter}";

            $countdetails = Cache::remember($countCacheKey, $cacheTime, function () use ($deptCode, $item, $quarter, $financialyearcode) {
                return DashboardModel::GetcountDetails(
                    $deptCode,
                    $item->regioncode,
                    $item->distcode,
                    $quarter,
                    $financialyearcode
                );
            });

            $this->assignCountDetails($item, $countdetails, $sourceForm, $item->distcode);
        }

        return response()->json(['data' => $DistrictwiseDetails]);
    }

    public function AuditeeDistrictwiseDetails(Request $request)
    {
        $sessionchargedel = session('charge');

        $deptCode = $request->deptCode;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        $distCode = $request->distCode;
        $sourceForm = $request->sourceform;
        $quarter = $request->input('quarter');
        $auditeeDeptCode = $request->auditeeDeptCode;
        $catcode = $request->catcode;
        $subcatcode = $request->subcatcode;

        $financialyearcode = $request->input('financialyear');
        $finalCatcode = $this->prepareCategoryParameter($catcode, 'category');
        $finalSubcatcode = $this->prepareCategoryParameter($subcatcode, 'subcategory');

        $isTemplateAudit = ($sourceForm === 'templateaudit');
        $isInspection = ($sourceForm === 'inspectionaudit');
        $isParareport = ($sourceForm === 'parareport');
        if ($isParareport) {
            $countMethod = 'GetAuditeeParaReportCounts';
        } elseif ($isTemplateAudit) {
            $countMethod = 'GetAuditeeTemplateAuditCounts';
        } elseif ($isInspection) {
            $countMethod = 'GetAuditeeInspectionCounts';
        } else {
            $countMethod = 'GetAuditeeCountDetails';
        }
        $cacheKey = ($isTemplateAudit ? 'districtwise_auditee_template_audit_details' : ($isInspection ? 'districtwise_auditee_inspection_audit_details' : 'districtwise_auditee_details')).":{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$sourceForm}:{$auditeeDeptCode}:{$finalCatcode}:{$finalSubcatcode}:{$financialyearcode}";

        $cacheTime = now()->addHours(1);

        $DistrictwiseDetails = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $isTemplateAudit, $financialyearcode) {
            if ($isTemplateAudit) {
                return DashboardModel::AuditeeTemplateDistrictwiseDetails($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode);

            } else {
                return DashboardModel::AuditeeDistrictwiseDetails($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode);
            }
        });

        foreach ($DistrictwiseDetails as $item) {
            $countCacheKey = ($isTemplateAudit ? 'district_auditee_template_audit_counts' : 'district_auditee_counts').":{$deptCode}:{$item->regioncode}:{$item->distcode}:{$quarter}:{$auditeeDeptCode}:{$finalCatcode}:{$finalSubcatcode}:{$financialyearcode}";

            $countdetails = Cache::remember($countCacheKey, $cacheTime, function () use ($deptCode, $item, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $countMethod, $financialyearcode) {
                return DashboardModel::{$countMethod}(
                    $deptCode,
                    $item->regioncode,
                    $item->distcode,
                    $quarter,
                    $auditeeDeptCode,
                    $finalCatcode,
                    $finalSubcatcode,
                    $financialyearcode
                );
            });
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

        $item->total_count = $countdetails[0]['total_count'] ?? 0;
        $item->pending_count = $countdetails[0]['pending_count'] ?? 0;
        $item->ongoing_count = $countdetails[0]['ongoing_count'] ?? 0;
        $item->completed_count = $countdetails[0]['completed_count'] ?? 0;

        $item->total_inspection_count = $countdetails[0]['total_inspection_count'] ?? 0;
        $item->pending_inspection_count = $countdetails[0]['pending_inspection_count'] ?? 0;
        $item->ongoing_inspection_count = $countdetails[0]['ongoing_inspection_count'] ?? 0;
        $item->completed_inspection_count = $countdetails[0]['completed_inspection_count'] ?? 0;

                // para
        $item->totalparacount = $countdetails[0]['totalparacount'] ?? 0;
        $item->processedparacount = $countdetails[0]['processedparacount'] ?? 0;
        $item->pendingparacount = $countdetails[0]['pendingparacount'] ?? 0;
    }

    public function RegionwiseAuditReportDetails(Request $request)
    {
        // Validate request parameters
        $validated = $request->validate([
            'deptCode' => 'nullable|string',
            'regionCode' => 'nullable|string',
            'distCode' => 'nullable|string',
            'quarter' => 'required|string',
            'viewType' => 'nullable|string',
        ]);

        $sessionchargedel = session('charge');

        $deptCode = $validated['deptCode'] ?? null;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        $distCode = $request->distCode ?? $sessionchargedel->distcode ?? null;
        $quarter = $validated['quarter'];
        $viewType = $validated['viewType'] ?? null;
        $financialyearcode = $request->input('financialyear');
        // Generate cache key
        $cacheKey = "audit_report_region:{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$financialyearcode}";
        if ($viewType) {
            $cacheKey .= ":{$viewType}";
        }

        $cacheTime = now()->addHours(1);

        // Get data from cache or database using model
        $reportData = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter , $financialyearcode) {
            return DashboardModel::getAuditReportRegionwise($quarter, $deptCode, $regionCode, $distCode, $financialyearcode);
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

    public function AuditeeRegionwiseAuditReportDetails(Request $request)
    {
        // Validate request parameters
        $validated = $request->validate([
            'deptCode' => 'nullable|string',
            'regionCode' => 'nullable|string',
            'distCode' => 'nullable|string',
            'quarter' => 'required|string',
            'viewType' => 'nullable|string',
            'auditeeDeptCode' => 'nullable|string',
            'catcode' => 'nullable|string',
            'subcatcode' => 'nullable|string',
        ]);

        $sessionchargedel = session('charge');

        $deptCode = $validated['deptCode'] ?? null;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        $distCode = $request->distCode ?? $sessionchargedel->distcode ?? null;
        $quarter = $validated['quarter'];
        $viewType = $validated['viewType'] ?? null;
        $auditeeDeptCode = $validated['auditeeDeptCode'] ?? null;
        $catcode = $validated['catcode'] ?? null;
        $subcatcode = $validated['subcatcode'] ?? null;

        $financialyearcode = $request->input('financialyear');
        $finalCatcode = $this->prepareCategoryParameter($catcode, 'category');
        $finalSubcatcode = $this->prepareCategoryParameter($subcatcode, 'subcategory');

        // Generate cache key
        $cacheKey = "auditee_audit_report_region:{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$auditeeDeptCode}:{$finalCatcode}:{$finalSubcatcode}:{$financialyearcode}";
        if ($viewType) {
            $cacheKey .= ":{$viewType}";
        }

        $cacheTime = now()->addHours(1);

        $reportData = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode) {
            return DashboardModel::getAuditeeReportRegionwise($quarter, $deptCode, $regionCode, $distCode, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode);
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

    public function DistrictwiseAuditReportDetails(Request $request)
    {
        // Validate request parameters
        $validated = $request->validate([
            'deptCode' => 'nullable|string',
            'regionCode' => 'nullable|string',
            'distCode' => 'nullable|string',
            'quarter' => 'required|string',
            'viewType' => 'nullable|string',
        ]);

        $sessionchargedel = session('charge');

        $deptCode = $validated['deptCode'] ?? null;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        $distCode = $validated['distCode'] ?? null;
        $quarter = $validated['quarter'];
        $viewType = $validated['viewType'] ?? null;

        // Generate cache key
        $cacheKey = "audit_report_district:{$deptCode}:{$regionCode}:{$distCode}:{$quarter}";
        if ($viewType) {
            $cacheKey .= ":{$viewType}";
        }
        $cacheTime = now()->addHours(1);

        // Get data from cache or database using model
        $results = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter, $viewType) {
            return DashboardModel::getAuditReportDistrictwise($quarter, $deptCode, $regionCode, $distCode, $viewType);
        });

        return response()->json([
            'success' => true,
            'data' => $results,
            'type' => 'district_wise',
        ]);
    }

    public function AuditeeDistrictwiseAuditReportDetails(Request $request)
    {
        // Validate request parameters
        $validated = $request->validate([
            'deptCode' => 'nullable|string',
            'regionCode' => 'nullable|string',
            'distCode' => 'nullable|string',
            'quarter' => 'required|string',
            'viewType' => 'nullable|string',
            'auditeeDeptCode' => 'nullable|string',
            'catcode' => 'nullable|string',
            'subcatcode' => 'nullable|string',
        ]);
        $sessionchargedel = session('charge');


        $deptCode = $validated['deptCode'] ?? null;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        // $regionCode = $validated['regionCode'] ?? null;
        $distCode = $validated['distCode'] ?? null;
        $quarter = $validated['quarter'];
        $viewType = $validated['viewType'] ?? null;
        $auditeeDeptCode = $validated['auditeeDeptCode'] ?? null;
        $catcode = $validated['catcode'] ?? null;
        $subcatcode = $validated['subcatcode'] ?? null;
        $financialyearcode = $validated['financialyearcode'] ?? null;

        $finalCatcode = $this->prepareCategoryParameter($catcode, 'category');
        $finalSubcatcode = $this->prepareCategoryParameter($subcatcode, 'subcategory');

        // Generate cache key
        $cacheKey = "auditee_report_district:{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$auditeeDeptCode}:{$finalCatcode}:{$finalSubcatcode}:{$financialyearcode}";
        if ($viewType) {
            $cacheKey .= ":{$viewType}";
        }
        $cacheTime = now()->addHours(1);

        $results = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter, $viewType, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode) {
            return DashboardModel::getAuditeeReportDistrictwise($quarter, $deptCode, $regionCode, $distCode, $viewType, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode);
        });

        return response()->json([
            'success' => true,
            'data' => $results,
            'type' => 'district_wise',
        ]);
    }

    public function InstitutionwiseAuditReportDetails(Request $request)
    {
        // Validate request parameters
        $validated = $request->validate([
            'deptCode' => 'nullable|string',
            'regionCode' => 'nullable|string',
            'distCode' => 'nullable|string',
            'quarter' => 'required|string',
            'viewType' => 'nullable|string',
        ]);

        $sessionchargedel = session('charge');

        $deptCode = $validated['deptCode'] ?? null;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        $distCode = $validated['distCode'] ?? null;
        $quarter = $validated['quarter'];
        $viewType = $validated['viewType'] ?? null;
        $financialyearcode = $request->input('financialyear');

        // Generate cache key
        $cacheKey = "audit_report_institution:{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$financialyearcode}";
        if ($viewType) {
            $cacheKey .= ":{$viewType}";
        }
        $cacheTime = now()->addHours(1);

        $results = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter, $viewType, $financialyearcode) {
            $data = DashboardModel::getAuditReportInstitutionDetails($quarter, $deptCode, $regionCode, $distCode, $financialyearcode);
            if ($viewType) {
                $data = array_filter($data, function ($item) use ($viewType, $financialyearcode) {
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

    public function AuditeeInstitutionwiseAuditReportDetails(Request $request)
    {
        // Validate request parameters
        $validated = $request->validate([
            'deptCode' => 'nullable|string',
            'regionCode' => 'nullable|string',
            'distCode' => 'nullable|string',
            'quarter' => 'required|string',
            'viewType' => 'nullable|string',
            'auditeeDeptCode' => 'nullable|string',
            'catcode' => 'nullable|string',
            'subcatcode' => 'nullable|string',
        ]);

        $sessionchargedel = session('charge');

        $deptCode = $validated['deptCode'] ?? null;
        $regionCode = $request->regionCode ?? $sessionchargedel->regioncode ?? null;
        $distCode = $validated['distCode'] ?? null;
        $quarter = $validated['quarter'];
        $viewType = $validated['viewType'] ?? null;
        $auditeeDeptCode = $validated['auditeeDeptCode'] ?? null;
        $catcode = $validated['catcode'] ?? null;
        $subcatcode = $validated['subcatcode'] ?? null;

        $financialyearcode = $request->input('financialyear');
        $finalCatcode = $this->prepareCategoryParameter($catcode, 'category');
        $finalSubcatcode = $this->prepareCategoryParameter($subcatcode, 'subcategory');

        $cacheKey = "auditee_report_institution:{$deptCode}:{$regionCode}:{$distCode}:{$quarter}:{$auditeeDeptCode}:{$finalCatcode}:{$finalSubcatcode}:{$financialyearcode}";
        if ($viewType) {
            $cacheKey .= ":{$viewType}";
        }
        $cacheTime = now()->addHours(1);

        $results = Cache::remember($cacheKey, $cacheTime, function () use ($deptCode, $regionCode, $distCode, $quarter, $viewType, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode) {
            $data = DashboardModel::getAuditeeReportInstitutionDetails($quarter, $deptCode, $regionCode, $distCode, $auditeeDeptCode, $finalCatcode, $finalSubcatcode, $financialyearcode);
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
}
