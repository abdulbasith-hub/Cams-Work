<?php

namespace App\Http\Controllers;

use App\Models\AuditManagementModel;
use App\Models\FieldAuditModel;
use App\Models\TransWorkAllocationModel;
use App\Services\FileUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;

class FieldAuditController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /*  public function init_fieldaudit()
      {
          $userData = session('user');
          $session_userid = $userData->userid;


          $results = DB::table('audit.inst_schteammember as scm')
              ->join('audit.inst_auditschedule as sc', 'sc.auditscheduleid', '=', 'scm.auditscheduleid')
              ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'sc.auditplanid')
              ->join('audit.mst_institution as mi', 'mi.instid', '=', 'ap.instid')
              ->where('auditeeresponse', 'A')
              ->where('scm.userid', $session_userid)
              ->where('sc.statusflag', 'F')
              ->where('scm.statusflag', 'Y')
              ->groupBy(
                  'sc.auditscheduleid',
                  'ap.auditplanid',
                  'ap.instid',
                  'mi.instename',
                  'sc.fromdate',
                  'sc.todate',
                  'sc.entrymeetdate',
                  'sc.exitmeetdate',
                  'mi.deptcode',
                  'mi.catcode',
                  'mi.subcatid',
                  'scm.auditteamhead',
                  'sc.workallocationflag'
              )
              ->select(
                  'sc.auditscheduleid',
                  'sc.fromdate',
                  'sc.todate',
                  'ap.auditplanid',
                  'ap.instid',
                  'mi.instename',
                  'sc.entrymeetdate',
                  'sc.exitmeetdate',
                  'mi.deptcode',
                  'mi.catcode',
                  'mi.subcatid',
                  'scm.auditteamhead',
                  'sc.workallocationflag'
              )
              ->get();

              $deptcode = '';
              $auditscheduleid='';
              $auditplanid='';
          foreach ($results as $all) {
              $all->encrypted_auditscheduleid = Crypt::encryptString($all->auditscheduleid);
              $all->formatted_fromdate = Carbon::createFromFormat('Y-m-d', $all->fromdate)->format('d/m/Y');
              $all->formatted_todate = Carbon::createFromFormat('Y-m-d', $all->todate)->format('d/m/Y');
              if($all->entrymeetdate)
              {
                  $all->entrymeetdate = Carbon::createFromFormat('Y-m-d', $all->entrymeetdate)->format('d/m/Y');
              }

              if($all->exitmeetdate)
              {
                  $all->exitmeetdate = Carbon::createFromFormat('Y-m-d', $all->exitmeetdate)->format('d/m/Y');
              }

              $all->slipexists=FieldAuditModel::Slipexists($all->auditscheduleid);

          $all->exceed_exitmeetdate=FieldAuditModel::ExceedexitMeet($all->auditscheduleid);

              $deptcode = $all->deptcode;
              $catcode = $all->catcode;
              // return $catcode;
              $subcatid = $all->subcatid;
              $auditscheduleid=$all->auditscheduleid;
              $auditplanid=$all->auditplanid;
          }

          if($deptcode)
          {
              $fetchcurrquarter = AuditManagementModel::getCurrentQuarter($deptcode);
              $str_Quarter = $fetchcurrquarter->quarterfrom;
              $str_Quarter = date('Y-m-01',strtotime($str_Quarter));

              $end_Quarter = $fetchcurrquarter->quarterto;
              $end_Quarter = date('Y-m-t',strtotime($end_Quarter));

              $Quarter =['fromquarter'=>$str_Quarter,'toquarter'=>$end_Quarter];

              $supercheckquestions = FieldAuditModel::FetchSuperCheckList($auditscheduleid, $deptcode, $catcode, $subcatid);

          }else
          {
              $Quarter =['fromquarter'=>'','toquarter'=>''];

              $supercheckquestions = [];


          }




          return view('fieldaudit.init_fieldaudit', compact('results','Quarter','supercheckquestions','auditscheduleid','auditplanid'));
      }*/

    public function init_fieldaudit()
    {
        $sessionchargedel = session('charge');
        $deptcode = $sessionchargedel->deptcode;

        $userData = session('user');
        $userid = $userData->userid;

        $results = DB::select('SELECT audit.getscheduledinstdel(?, ?) AS result', [$deptcode, $userid]);

        $results = json_decode($results[0]->result);

        $deptcode = '';
        $auditscheduleid = '';
        $auditplanid = '';
        $nodata = 'Y';

        $Quarter = '';
        $supercheckquestions = [];
        if ($results) {
            $nodata = 'N';
            foreach ($results as $all) {
                $all->encrypted_auditscheduleid = Crypt::encryptString($all->auditscheduleid);
                $all->formatted_fromdate = Carbon::createFromFormat('Y-m-d', $all->fromdate)->format('d/m/Y');
                $all->formatted_todate = Carbon::createFromFormat('Y-m-d', $all->todate)->format('d/m/Y');
                if ($all->entrymeetdate) {
                    $all->entrymeetdate = Carbon::createFromFormat('Y-m-d', $all->entrymeetdate)->format('d/m/Y');
                }

                if ($all->exitmeetdate) {
                    $all->exitmeetdate = Carbon::createFromFormat('Y-m-d', $all->exitmeetdate)->format('d/m/Y');
                }

                $all->slipexists = FieldAuditModel::Slipexists($all->auditscheduleid);
                $all->performanceslipexists = FieldAuditModel::performanceSlipexists($all->auditscheduleid);

                $deptcode = $all->deptcode;
                $catcode = $all->catcode;
                // return $catcode;
                $subcatid = $all->subcatid;
                $auditscheduleid = $all->auditscheduleid;
                $auditplanid = $all->auditplanid;

                $supercheckquestions[$all->auditscheduleid] = FieldAuditModel::FetchSuperCheckList($auditscheduleid, $deptcode, $catcode, $subcatid)->toArray();
            }

            // print_r($results);
            // exit;

            if ($deptcode) {
                $fetchcurrquarter = AuditManagementModel::getCurrentQuarter($deptcode, 'Q1');
                $str_Quarter = $fetchcurrquarter->quarterfrom;
                $str_Quarter = date('Y-m-01', strtotime($str_Quarter));

                $end_Quarter = $fetchcurrquarter->quarterto;
                $end_Quarter = date('Y-m-t', strtotime($end_Quarter));

                $Quarter = ['fromquarter' => $str_Quarter, 'toquarter' => $end_Quarter];

                //  $supercheckquestions = FieldAuditModel::FetchSuperCheckList($auditscheduleid, $deptcode, $catcode, $subcatid);
            } else {
                $Quarter = ['fromquarter' => '', 'toquarter' => ''];

                $supercheckquestions = [];
            }
        }
        return view('fieldaudit.init_fieldaudit', compact('results', 'Quarter', 'supercheckquestions', 'auditscheduleid', 'auditplanid', 'nodata'));
    }

    public function getcategoryBasedOnSerious(Request $request)
    {
        // Validate the input
        $request->validate([
            'serious' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        // Get the department code
        $serious = $request->input('serious');

        // Fetch regions from the model
        $catcode = FieldAuditModel::getcategoryBasedSerious($serious);

        // Return JSON response
        if ($catcode->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $catcode]);
        } else {
            return response()->json(['success' => false, 'message' => 'No catcode found'], 404);
        }
    }

    public function getsubcategoryBasedOnCategory(Request $request)
    {
        // Validate the input
        $request->validate([
            'category' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        // Get the department code
        $category = $request->input('category');

        // Fetch regions from the model
        $subcategory = FieldAuditModel::getsubcategoryBasedCatgory($category);

        // Return JSON response
        if ($subcategory->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $subcategory]);
        } else {
            return response()->json(['success' => false, 'message' => 'No subcategory found'], 404);
        }
    }

    public function auditslip_dropdown($encrypted_auditscheduleid, Request $request)
    {
        try {
            // Decrypt the encrypted audit schedule ID
            if ($encrypted_auditscheduleid) {
                $auditscheduleid = Crypt::decryptString($encrypted_auditscheduleid);

                $instid = $request->query('instid');
                $encrypted_instid = Crypt::encryptString($instid);
                $spilloverflag = $request->query('spilloverflag');
                $api = $request->query('api');
            }

            if ($auditscheduleid === null) {
                throw new \Exception('Audit schedule ID not found');
            }

            $chargeData = session('charge');
            $session_deptcode = $chargeData->deptcode;  // Accessing the department code from the session
            $session_usertypecode = $chargeData->usertypecode;
            $userData = session('user');
            $session_userid = $userData->userid;

            if ($session_userid === null) {
                throw new \Exception('User ID not found');
            }

            $scheduledel = FieldAuditModel::getscheduledel_basedonuser($session_userid, $auditscheduleid);
            $financialyearcode = $scheduledel[0]->financialyearcode;

            $teamheaddel = FieldAuditModel::getAuditScheduleHeaddel($auditscheduleid);

            $severitydel = FieldAuditModel::getSeverity();

            $schemename = FieldAuditModel::getSchemename();

            $cpsform = FieldAuditModel::checkCpsAllowed($instid);

            $serious = FieldAuditModel::getSerious();

            $getMainobjection = FieldAuditModel::getMainobjection($session_userid, $auditscheduleid, $financialyearcode, $api);

            $session_userid = Crypt::encryptString($session_userid);

            if ($getMainobjection->isEmpty()) {
                throw new \Exception('No Main Objection found for the user');
            }

            if ($scheduledel[0]->auditteamhead == 'N') {
                $sessionuserTeamheadOrNot = 'N';
            } else {
                $sessionuserTeamheadOrNot = 'Y';
                $getMainobjection = '';
            }

            if (($scheduledel->isEmpty()) || ($teamheaddel->isEmpty())) {
                return redirect()->route('site.error')->with('error', 'No audit schedule details found.');
            }

            $scheduleheadid = $teamheaddel[0]->userid;
            if ($api == 'Y') {
                $paccId = FieldAuditModel::getPacsId($instid);
            } else {
                $paccId = null;
            }
        //  echo $api;
            // $paccId = FieldAuditModel::getPacsId($instid);

            return view('fieldaudit.auditslip', compact('scheduledel', 'scheduleheadid', 'getMainobjection', 'sessionuserTeamheadOrNot', 'severitydel', 'schemename', 'serious', 'session_userid', 'encrypted_auditscheduleid', 'spilloverflag', 'encrypted_instid', 'cpsform', 'api','paccId'));
        } catch (\Exception $e) {
            echo $e->getMessage();
            // return redirect()->route('error')->with('error', 'An error occurred while processing the auditslip. Please try again later.');
        }
    }

    public function getobjectionForHead(Request $request)
    {
        try {
            $auditscheduleid = $request->input('auditscheduleid');
            $financialyearcode = $request->input('financialyearcode');

            $createdby = $request->input('createdby');
            $datafromapi = $request->input('datafromapi');
            $userData = session('user');
            $session_userid = $userData->userid;

            if (!($createdby))
                $request->merge(['createdby' => $session_userid]);

            $rules = [
                'auditscheduleid' => 'required|integer',
                'createdby' => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $alldetails = FieldAuditModel::getMainobjection($request->input('createdby'), $auditscheduleid, $financialyearcode, $datafromapi);

            if ($alldetails->isNotEmpty()) {
                return response()->json(['success' => true, 'data' => $alldetails], 200);
            } else {
                return response()->json(['success' => true, 'message' => 'nomainobjectionfound', 'error' => '400'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error in auditslip_dropdown: ' . $e->getMessage());
            return $e->getMessage();
            // return redirect()->route('error')->with('error', 'An error occurred while processing the auditslip. Please try again later.');
        }
    }

    public function view_fieldaudit()
    {
        $userData = session('user');
        $session_userid = $userData->userid;

        $results = DB::table('audit.inst_schteammember as scm')
            ->join('audit.inst_auditschedule as sc', 'sc.auditscheduleid', '=', 'scm.auditscheduleid')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'sc.auditplanid')
            ->join('audit.mst_institution as mi', 'mi.instid', '=', 'ap.instid')
            ->where('auditeeresponse', 'A')
            ->where('scm.userid', $session_userid)
            ->groupBy(
                'sc.auditscheduleid',
                'ap.auditplanid',
                'ap.instid',
                'mi.instename',
                'sc.fromdate',
                'sc.todate'
            )
            ->select(
                'sc.auditscheduleid',
                'sc.fromdate',
                'sc.todate',
                'ap.auditplanid',
                'ap.instid',
                'mi.instename',
                'sc.exitmeetdate'
            );

        $querySql = $historydel->toSql();
        $bindings = $historydel->getBindings();

        $finalQuery = vsprintf(
            str_replace('?', "'%s'", $querySql),
            array_map('addslashes', $bindings)
        );

        print_r($finalQuery);
        exit;
        // ->get();
        $resultsNew = [];
        foreach ($results as $all) {
            if ($all->exitmeetdate) {
                $currdate = strtotime(date('d-m-Y'));
                $exitmeetdate = strtotime($all->exitmeetdate);

                if ($currdate > $exitmeetdate) {
                    $all->encrypted_auditscheduleid = Crypt::encryptString($all->auditscheduleid);
                    $all->formatted_fromdate = Carbon::createFromFormat('Y-m-d', $all->fromdate)->format('d/m/Y');
                    $all->formatted_todate = Carbon::createFromFormat('Y-m-d', $all->todate)->format('d/m/Y');
                    $resultsNew[] = $all;
                }
            }
        }
        $results = json_encode($resultsNew);
        // return view('audit.listinstitute', compact('results'));
    }

    public function auditfield_dropdown($encrypted_auditscheduleid)
    {
        if ($encrypted_auditscheduleid) {
            $auditscheduleid = Crypt::decryptString($encrypted_auditscheduleid);
        }

        // Echo the ID to verify it's being passed correctly
        // Access session data
        $chargeData = session('charge');
        $session_deptcode = $chargeData->deptcode;  // Accessing the department code from the session
        $session_usertypecode = $chargeData->usertypecode;
        $userData = session('user');
        $session_userid = $userData->userid;

        $get_majorobjection = DB::table('audit.mst_mainobjection as ma')
            ->where('ma.deptcode', $session_deptcode)  // Query based on department code
            ->where('ma.statusflag', '=', 'Y')  // Filter for active or enabled records
            ->select('ma.objectionename', 'ma.objectiontname', 'ma.mainobjectionid')  // Select the necessary fields
            ->orderBy('ma.objectionename', 'asc')
            ->get();

        $inst_details = DB::table('audit.inst_schteammember as sm')
            ->join('audit.inst_auditschedule as is', 'is.auditscheduleid', '=', 'sm.auditscheduleid')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'is.auditplanid')
            ->join('audit.mst_institution as in', 'in.instid', '=', 'ap.instid')
            ->join('audit.mst_auditeeins_category as incat', 'incat.catcode', '=', 'in.catcode')
            ->join('audit.mst_typeofaudit as ta', 'ta.typeofauditcode', '=', 'ap.typeofauditcode')
            //  ->join('audit.mst_auditperiod as d', 'd.auditperiodid', '=', 'ap.auditperiodid')
            ->join('audit.yearcode_mapping as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
            ->join(
                'audit.mst_auditperiod as d',
                DB::raw('CAST(yrmap.yearselected AS INTEGER)'),
                '=',
                'd.auditperiodid'
            )
            ->where('yrmap.statusflag', 'Y')
            ->where('userid', $session_userid)
            ->where('is.auditscheduleid', $auditscheduleid)
            // Apply STRING_AGG to aggregate years
            ->select(
                'is.auditscheduleid',
                'sm.auditscheduleid',
                'sm.auditteamhead',
                'is.auditplanid',
                'is.fromdate',
                'is.todate',
                'ap.instid',
                'in.instename',
                'incat.catename',
                'in.mandays',
                'in.catcode',
                'in.deptcode',
                'in.subcatid',
                'sm.auditteamhead',
                'ta.typeofauditename',
                'sm.schteammemberid',
                DB::raw("STRING_AGG(DISTINCT d.fromyear || '-' || d.toyear, ', ') as yearname")
            )
            ->groupby('is.auditscheduleid', 'sm.auditscheduleid', 'sm.auditteamhead', 'is.auditplanid', 'is.fromdate', 'is.todate', 'ap.instid', 'in.instename', 'incat.catename', 'in.mandays', 'sm.auditteamhead', 'ta.typeofauditename', 'sm.schteammemberid', 'in.catcode', 'in.deptcode', 'in.subcatid')
            ->get();
        $teammemdel = DB::table('audit.inst_schteammember as sm');
        $teamheadid = 'N';
        if ($inst_details[0]->auditteamhead == 'N') {
            $teamheaddel = DB::table('audit.inst_schteammember as sm')
                ->where('auditscheduleid', $auditscheduleid)
                ->where('auditteamhead', 'Y')
                ->select('sm.userid')
                ->get();  // added 'get()' to fetch data
            $teamheadid = $teamheaddel[0]->userid;
        }
        $teammemdel = DB::table('audit.inst_schteammember as sm')
            ->join('audit.userchargedetails as uc', 'sm.userid', '=', 'uc.userid')
            ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
            ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('sm.statusflag', 'Y')
            ->select(
                'sm.schteammemberid',
                'sm.userid',
                'de.desigelname',
                'du.username',
                'sm.auditteamhead'
            )
            ->get();
        $majorworkdel = DB::table('audit.mst_majorworkallocationtype')
            ->where('statusflag', 'Y')
            ->select(
                'mst_majorworkallocationtype.majorworkallocationtypeename',
                'mst_majorworkallocationtype.majorworkallocationtypeid',
            )
            ->orderBy('mst_majorworkallocationtype.updatedby', 'asc')
            ->get();
        // Option 1: Returning a view with the data (pass the data to the view)

        // print_r($inst_details);

        $deptcode = $inst_details->first()->deptcode;
        $catcode = $inst_details->first()->catcode;
        // return $catcode;
        $subcatid = $inst_details->first()->subcatid;

        $supercheckquestions = FieldAuditModel::FetchSuperCheckList($auditscheduleid, $deptcode, $catcode, $subcatid);

        return view('fieldaudit.fieldaudit', compact('get_majorobjection', 'inst_details', 'teamheadid', 'teammemdel', 'majorworkdel', 'supercheckquestions'));

        // You can also add logic to handle the ID if needed
    }

    public function slipdetails_dropdown($viewvalue)
    {
        $chargeData = session('charge');
        $session_deptcode = $chargeData->deptcode;  // Accessing the department code from the session
        $session_usertypecode = $chargeData->usertypecode;
        $userData = session('user');
        $session_userid = $userData->userid;

        $get_majorobjection = DB::table('audit.mst_mainobjection as ma')
            ->where('ma.deptcode', $session_deptcode)  // Query based on department code
            ->where('ma.statusflag', '=', 'Y')  // Filter for active or enabled records
            ->select('ma.objectionename', 'ma.objectiontname', 'ma.mainobjectionid')  // Select the necessary fields
            ->orderBy('ma.objectionename', 'asc')
            ->get();

        // Precompute year aggregation
        $yearAgg = DB::table('audit.yearcode_mapping as yrmap')
            ->select('yrmap.auditplanid')
            ->selectRaw("STRING_AGG(DISTINCT d.fromyear || '-' || d.toyear, ', ') FILTER (WHERE d.financestatus = 'N') AS yearname")
            ->selectRaw("STRING_AGG(DISTINCT d.fromyear || '-' || d.toyear, ', ') FILTER (WHERE d.financestatus = 'Y') AS annadhanamyear")
            ->join('audit.mst_auditperiod as d', DB::raw('CAST(yrmap.yearselected AS INTEGER)'), '=', 'd.auditperiodid')
            ->where('yrmap.statusflag', 'Y')
            ->groupBy('yrmap.auditplanid');

        // Main query
        $inst_details = DB::table('audit.inst_auditschedule as sm')
            ->select([
                'sm.auditscheduleid',
                'sme.schteammemberid',
                'sme.userid',
                'sm.auditplanid',
                'ap.instid',
                'in.instename',
                'incat.catename',
                'in.mandays',
                'in.annadhanam_only',
                'in.deptcode',
                'tad.typeofauditename',
                'dud.username',
                'ap.spilloverflag',
                'sm.exitmeetdate',
                'ap.auditquartercode',
                'dept.currentquarter',
                DB::raw('ya.yearname'),
                DB::raw('ya.annadhanamyear'),
            ])
            ->join('audit.inst_schteammember as sme', 'sme.auditscheduleid', '=', 'sm.auditscheduleid')
            ->join('audit.deptuserdetails as dud', 'dud.deptuserid', '=', 'sme.userid')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'sm.auditplanid')
            ->join('audit.mst_institution as in', 'in.instid', '=', 'ap.instid')
            ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'in.deptcode')
            ->join('audit.mst_auditeeins_category as incat', 'incat.catcode', '=', 'in.catcode')
            ->join('audit.mst_typeofaudit as tad', 'tad.typeofauditcode', '=', 'ap.typeofauditcode')
            ->leftJoinSub($yearAgg, 'ya', function ($join) {
                $join->on('ya.auditplanid', '=', 'ap.auditplanid');
            })
            ->addSelect(DB::raw("(SELECT wd_date FROM (
SELECT wd_date
FROM generate_series(current_date - interval '30 days', current_date + interval '60 days', interval '1 day') AS g(wd_date)
WHERE EXTRACT(ISODOW FROM wd_date) NOT IN (6,7)
AND NOT EXISTS (SELECT 1 FROM audit.mst_holiday h WHERE h.holiday_date = g.wd_date AND h.statusflag='Y')
AND wd_date > sm.exitmeetdate
ORDER BY wd_date
LIMIT 6
) t ORDER BY wd_date DESC LIMIT 1) AS fifth_workday_after_exit"))
            // Join instead of WHERE EXISTS
            ->join('audit.trans_auditslip as ta', 'ta.auditscheduleid', '=', 'sm.auditscheduleid')
            ->join('audit.sliphistorytransactions as t', 't.auditslipid', '=', 'ta.auditslipid')
            ->where(function ($q) use ($session_userid) {
                $q
                    ->where(function ($q1) use ($session_userid) {
                        $q1
                            ->where('ta.forwardedto', $session_userid)
                            ->where('ta.forwardedtousertypecode', 'I');
                    })
                    ->orWhere(function ($q2) use ($session_userid) {
                        $q2
                            ->where('t.forwardedby', $session_userid)
                            ->where('t.forwardedbyusertypecode', 'I');
                    });
            })
            ->where('sme.auditteamhead', 'Y')
            ->where('sm.statusflag', 'F')
  	    ->whereNull('ap.datafromapi')
            // Current quarter or within 5 working days after exitmeetdate
            ->where(function ($query) {
                $query
                    ->whereRaw("ap.auditquartercode = dept.currentquarter AND ap.financialyearcode = '02'")
                    // ->whereRaw('ap.prioritycode = inst_priority')
                    ->orWhereRaw("
current_date <= (
\tSELECT MAX(wd_date)
\tFROM (
\tSELECT wd_date
\tFROM generate_series(sm.exitmeetdate + interval '1 day' , sm.exitmeetdate + interval '15 days' , interval '1 day' ) AS g(wd_date)
\tWHERE EXTRACT(ISODOW FROM wd_date) NOT IN (6,7)
\tAND NOT EXISTS (
\tSELECT 1 FROM audit.mst_holiday h
\tWHERE h.holiday_date=g.wd_date
\tAND h.statusflag='Y'
\t)
\tORDER BY wd_date
\tLIMIT 6
\t) AS workdays
\t) ");
            })
            ->distinct()  // remove duplicates from joins
            ->get();

        // $querySql = $inst_details->toSql();
        // $bindings = $inst_details->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );

        // print_r($finalQuery);
        // exit;
        $teammemdel = '';
        $majorworkdel = '';

        $get_majorobjection = DB::table('audit.mst_mainobjection as ma')
            ->where('ma.deptcode', $session_deptcode)  // Query based on department code
            ->where('ma.statusflag', '=', 'Y')  // Filter for active or enabled records
            ->select('ma.objectionename', 'ma.objectiontname', 'ma.mainobjectionid')  // Select the necessary fields
            ->get();

        if (count($inst_details)) {
            $teamheadid = $inst_details[0]->userid;
        } else
            $teamheadid = '';

        $severitydel = FieldAuditModel::getSeverity();

        // echo 'jo';

        // print_r($inst_details);
        // exit;

        return view($viewvalue, compact('get_majorobjection', 'inst_details', 'teamheadid', 'teammemdel', 'majorworkdel', 'severitydel'));
    }

    public function getauditslip(Request $request)
    {
        // Retrieve 'charge' from session
        $chargedel = session('charge');
        $userdel = session('user');
        $filter = $request->input('filter');
        $action = $request->input('action');
        $instid = $request->input('instid');
        $spilloverflag = $request->input('spilloverflag');

        $teamhead = $request->input('teamhead');
        // echo $teamhead;
        // exit;

        $usertypecode = $chargedel->usertypecode;

        $auditscheduleid = $request->input('auditscheduleid');

        if ($usertypecode == View::shared('auditorlogin')) {
            $userchargeid = $chargedel->userchargeid;
            $auditteamhead = $chargedel->auditteamhead;
        }
        $userid = $userdel->userid;
        // echo $userid;
        // exit;

        // Validate auditslipid if it's provided in the request
        if ($request->input('auditslipid')) {
            try {
                // Decrypt the auditslipid
                $auditslipid = Crypt::decryptString($request->auditslipid);
                $request->merge(['auditslipid' => $auditslipid]);

                // Validate decrypted auditslipid
                $request->validate([
                    'auditslipid' => 'required|integer',
                ], [
                    'required' => 'The :attribute field is required.',
                    'integer' => 'The :attribute field must be a valid number.',
                ]);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                return response()->json(['success' => false, 'message' => 'Invalid auditslipid.'], 400);
            }
        } else {
            $auditslipid = null;
        }

        // echo $auditslipid;
        // exit;
        // Since 'userchargeid' is from session, no need to validate it via request
        // But ensure userchargeid exists in session

        if ($usertypecode == View::shared('auditorlogin')) {
            if (!$userchargeid) {
                return response()->json(['success' => false, 'message' => 'User ID not provided'], 400);
            }
            $alldetails = FieldAuditModel::getslipdetails($userid, $auditslipid, $auditteamhead, $auditscheduleid, $filter, $action, $teamhead, $instid, $spilloverflag);

            if ($action == 'fetch' || $action == 'fetchwithdata') {
                if ($alldetails['auditDetails']->isNotEmpty()) {
                    foreach ($alldetails['auditDetails'] as $all) {
                        $all->encrypted_auditslipid = Crypt::encryptString($all->auditslipid);
                    }
                }
            }

            if ($alldetails['historydel']->isNotEmpty()) {
                foreach ($alldetails['historydel'] as $all) {
                    $all->encrypted_auditslipid = Crypt::encryptString($all->auditslipid);
                }
            }
        } else if ($usertypecode == View::shared('auditeelogin')) {
            if (!$userid) {
                return response()->json(['success' => false, 'message' => 'User ID not provided'], 400);
            }
            $alldetails = FieldAuditModel::fetchdata_auditee($userid, $auditslipid, $action, $filter, $instid, $spilloverflag, $auditscheduleid);

            // Check if 'auditDetails' is not empty
            if ($alldetails['auditDetails']->isNotEmpty()) {
                foreach ($alldetails['auditDetails'] as $all) {
                    $all->encrypted_auditslipid = Crypt::encryptString($all->auditslipid);
                }
            }

            if ($alldetails['historydel']->isNotEmpty()) {
                foreach ($alldetails['historydel'] as $all) {
                    $all->encrypted_auditslipid = Crypt::encryptString($all->auditslipid);
                }
            }
        }

        // Return response with the data
        if ($alldetails->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $alldetails]);
        } else {
            return response()->json(['success' => true, 'message' => 'No auditslips found'], 200);
        }
    }

    public function auditeereply(Request $request, $userId = null)
    {
        $action = $request->input('action');
        $rejoinderstatus = $request->input('rejoinderstatus');
        $rejoindercycle = $request->input('rejoindercycle');

        $auditscheduleid = $request->input('auditscheduleid');

        $auditslipid = Crypt::decryptString($request->auditslipid);

        $fileupload = $request->file('fileupload');
        $destinationPath = 'uploads/slipauditor';

        $userdel = session('user');
        $chargeData = session('charge');

        $session_userid = $userdel->userid ?? null;
        $sessionDeptcode = $chargeData->deptcode ?? null;
        if (!$session_userid) {
            return response()->json(['error' => 'User session is invalid.'], 400);
        }

        $deactive_fileuploadids = $request->input('deactive_fileid') ? explode(',', $request->input('deactive_fileid')) : [];
        $active_fileuploadids = $request->input('active_fileid') ? explode(',', $request->input('active_fileid')) : [];

        // Deactivate Files
        if (!empty($deactive_fileuploadids)) {
            $this->fileUploadService->deactive_uploadefile($auditslipid, $deactive_fileuploadids);
        }

        $fileUploadId = null;

        if (($request->hasFile('fileupload'))) {
            // print_r($fileupload);

            // public function insert_slipfileupload($auditslipid, array $fileUploadIds,$rejoinderstatus,$rejoindercycle,$processcode)

            // public function slipMultipleFileUpload(array $files, string $destinationPath, $auditslipid, $active_fileuploadid, $deactive_fileuploadid, $rejoinderstatus,$auditscheduleid)

            $uploadResult = $this->fileUploadService->slipMultipleFileUpload(
                $fileupload,
                $destinationPath,
                $auditslipid,
                $active_fileuploadids,
                $deactive_fileuploadids,
                $rejoinderstatus,
                $auditscheduleid
            );

            //  print_r($uploadResult );

            if (is_array($uploadResult) && isset($uploadResult['error'])) {
                return response()->json(['errors' => $uploadResult['error']], 400);
            } elseif ($uploadResult instanceof \Illuminate\Http\JsonResponse) {
                $fileUploadId = $uploadResult->getData(true)['uploaded_files'];
                // $fileUploadId  =   $fileUploadId[0];
            }
        }

        $request->validate([
            // 'auditee_upload' => 'required',  // Optional, max length of 255
            'auditeeremarks_append' => 'required',  // Optional, max length of 255
        ], [
            'required' => 'The :attribute field is required.',
            'alpha' => 'The :attribute field must contain only letters.',
            'integer' => 'The :attribute field must be a valid number.',
            'regex' => 'The :attribute field must be a valid number.',
            'alpha_num' => 'The :attribute field must contain only letters and numbers.',
            'max' => 'The :attribute field must not exceed :max characters.',
        ]);

        // Process content for remarks
        $content = json_encode([
            'content' => $request->input('auditeeremarks_append')
        ]);

        $userdel = session('user');
        $userid = $userdel->userid;

        // Prepare the data to insert or update
        $data = [
            'updatedon' => View::shared('get_nowtime'),
            'updatedby' => $userid
        ];

        $data['remarks'] = $content;
        $processcode = 'U';
        $data['processcode'] = $processcode;

        try {
            // Insert or update the audit slip record
            $auditslipdel = FieldAuditModel::createIfNotExistsOrUpdate($data, $auditslipid, '', $sessionDeptcode, '', '');

            $scheduleheaddel = FieldAuditModel::getAuditScheduleHeaddel($auditslipdel['auditscheduleid']);
            $scheduleheadid = $scheduleheaddel[0]->userid;

            $auditslipnumber = $auditslipdel['slipnumber'];
            $auditslipid = $auditslipdel['auditslipid'];
            $createdby = $auditslipdel['createdby'];

            $teamlead = 'N';

            if ($createdby == $scheduleheadid) {
                $teamlead = 'Y';
                $processcode_slipfileupload = 'R';
            } else {
                $processcode_slipfileupload = 'M';
            }

            // Proceed only if the audit slip was successfully created/updated
            if ($auditslipid) {
                // Create a relation for the file upload
                $data = [
                    'fileuploadid' => $fileUploadId,
                    'auditslipid' => $auditslipid,
                    'statusflag' => 'Y',
                    'updatedon' => View::shared('get_nowtime'),
                    'updatedby' => $userid
                ];

                if ($request->input('rejoinderstatus') == 'Y')
                    $data['rejoinderstatus'] = 'Y';

                // echo 'slipfileupload';

                // print_r($data);

                if ($fileUploadId) {
                    $this->fileUploadService->insert_slipfileupload($auditslipid, $fileUploadId, $rejoinderstatus, $rejoindercycle, $processcode);
                }

                // echo $processcode_slipfileupload;
                // echo $processcode;
                // exit;

                if ($request->input('finaliseflag') == 'Y') {
                    // echo 'statusflagY';

                    $chargeData = session('charge');
                    $session_usertypecode = $chargeData->usertypecode;  // Accessing the department code from the session

                    // $teamheadids = FieldAuditModel::fetchdata_teamheaduserid($auditslipid);
                    // $teamheadids = FieldAuditModel::fetchdata_slipcreatedby($auditslipid);

                    // $teamheadid  =   $teamheadids[0];

                    if ($createdby) {
                        // Handle the insertion of new transaction for the auditee
                        // $insertdata = [
                        //     'auditslipid' => $auditslipid,
                        //     'createdby' => $userid,
                        //     'createdon' => View::shared('get_nowtime'),
                        //     'forwardedto' => $createdby,
                        //     'forwardedtousertypecode' => 'A',
                        //     'updatedby' => $userid,
                        //     'updatedbyusertypecode' => $session_usertypecode,
                        //     'updatedon' => View::shared('get_nowtime'),
                        // ];

                        $updatedata = [
                            'forwardedto' => $createdby,
                            'forwardedtousertypecode' => 'A',
                            'updatedby' => $userid,
                            'updatedbyusertypecode' => 'I',
                            'updatedon' => View::shared('get_nowtime'),
                        ];

                        // print_r($insertdata);
                        // print_r($updatedata);

                        // // Insert transaction and update
                        // $transactionResult = FieldAuditModel::create_transactiondel($insertdata, $updatedata, $auditslipid);

                        // if ($transactionResult) {
                        // Insert history transaction if transaction was successful
                        $historyData = [
                            'auditslipid' => $auditslipid,
                            'forwardedby' => $userid,
                            'forwardedbyusertypecode' => $session_usertypecode,
                            'forwardedto' => $createdby,
                            'forwardedtousertypecode' => 'A',
                            'forwardedon' => View::shared('get_nowtime'),
                            'transstatus' => 'A',
                            'processcode' => $processcode_slipfileupload,
                            'remarks' => $content,
                        ];

                        if ($rejoinderstatus == 'Y')
                            $historyData['rejoinderstatus'] = 'Y';
                        if (($rejoindercycle > 0))
                            $historyData['rejoindercycle'] = $rejoindercycle;

                        // print_r($updatedata);
                        $historyTransaction = FieldAuditModel::insert_historytransactiondel($historyData);

                        if ($historyTransaction) {
                            // Update the auditslip table after inserting history transaction
                            $updateData = [
                                'processcode' => $processcode_slipfileupload,
                                'remarks' => $content,
                                'forwardedto' => $createdby,
                                'forwardedtousertypecode' => 'A',
                                'updatedby' => $userid,
                                'updatedbyusertypecode' => 'I',
                                'updatedon' => View::shared('get_nowtime'),
                            ];

                            //  print_r($updateData);
                            $updateSlip = FieldAuditModel::update_auditsliptable($updateData, $auditslipid);

                            if ($updateSlip) {
                                FieldAuditModel::updateslipfileupload($processcode_slipfileupload, $session_userid, $processcode, $auditslipid, $session_usertypecode, $rejoinderstatus, $rejoindercycle);
                                // DB::commit();
                                return response()->json(['success' => true, 'message' => 'Audit slip forwarded to Audit Team Successfully.', 'data' => array('slid' => Crypt::encryptString($auditslipid), 'auditslipnumber' => $auditslipnumber)]);
                            } else
                                throw new \Exception('Failed to insert history transaction.');
                        } else {
                            throw new \Exception('Failed to insert or update transaction.');
                        }
                        // }
                    }
                } else {
                    return response()->json(['success' => true, 'message' => 'Audit slip Data Saved successfully.', 'data' => array('slid' => Crypt::encryptString($auditslipid), 'auditslipnumber' => $auditslipnumber)]);
                }

                // if ($request->input('finaliseflag') === 'Y') {
                //     $session_usertypecode = $chargeData->usertypecode ?? null;
                //     $session_userchargeid = $chargeData->userchargeid ?? null;

                //     if ($createdby) {

                //         $historyData = [
                //             // 'auditslipid' => $auditslipid,
                //             // 'auditscheduleid' => $request->input('auditscheduleid'),
                //             // 'schteammemberid' => $request->input('schteammemberid'),
                //             // 'auditplanid' => $request->input('auditplanid'),
                //             // 'mainobjectionid' => $request->input('majorobjectioncode'),
                //             // 'subobjectionid' => $request->input('minorobjectioncode'),
                //             // 'tempslipnumber' => 1,
                //             // 'tempslipnumber' => $auditslipnumber,
                //             // 'severityid' => $request->input('severityid'),
                //             // 'liability' => $request->input('liability'),
                //             // 'slipdetails' => $request->input('slipdetails'),
                //             'remarks' => $content,
                //             'processcode'   =>  $processcode_slipfileupload,
                //             'forwardedby' => $session_userid,
                //             'forwardedbyusertypecode' => $session_usertypecode,
                //             'forwardedto' => $createdby,
                //             'transstatus' => 'A',
                //             'forwardedon' => View::shared('get_nowtime'),
                //         ];

                //         $updateTransauditData = [

                //             'updatedby' => $session_userid,
                //             'updatedbyusertypecode' => $session_usertypecode,
                //             'updatedon' => View::shared('get_nowtime'),
                //             'processcode' => $processcode_slipfileupload,
                //             'forwardedto' => $createdby,
                //             'forwardedtousertypecode' => 'A',
                //         ];

                //         $transactionResult = FieldAuditModel::insert_historytransactiondel($historyData, $auditslipid);

                //         if ($transactionResult) {

                //             $updateSlip = FieldAuditModel::update_auditsliptable($updateTransauditData, $auditslipid);
                //             if ($updateSlip) {

                //                 FieldAuditModel:: updateslipfileupload($processcode_slipfileupload,$session_userid,$processcode,$auditslipid,$session_usertypecode,$rejoinderstatus,$rejoindercycle);

                //                 // DB::commit();
                //                 return response()->json(['success' => true, 'message' => $message, 'data' => $auditslipnumber]);
                //             } else {
                //                 throw new \Exception("Failed to update the auditslip table.");
                //             }
                //         }
                //     }
                //     else
                //     {
                //         return response()->json(['success' => true, 'message' => 'No User Found', 'data' => $auditslipnumber]);
                //     }
                // }
                // else
                // {
                //     return response()->json(['success' => true, 'message' => 'Audit Slip saved successfully.', 'data' => $auditslipnumber]);
                // }
                // } else {
                //     throw new \Exception("Failed to create file upload relation.");
                // }
            } else {
                throw new \Exception('Failed to create or update the audit slip.');
            }
        } catch (\Exception $e) {
            // Rollback the transaction on failure
            // DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // public function audislip_insert(Request $request, $userId = null)
    // {
    //     // print_r($request->all());
    //     // exit;

    //     $action = $request->input('action');

    //     if (($request->input('liability') == 'Y')) {

    //         $notype   =   $request->input('notype');
    //         $name   =   $request->input('name');
    //         $gpfno   =   $request->input('gpfno');
    //         $amount   =   $request->input('amount');
    //         $designation   =   $request->input('designation');

    //         $liabilityid    =   $request->input('liabilityid');

    //         $liabilitydel  =   $request->input('liabilityid');

    //         $count_name = count($name);
    //         $deleted_liabilityid   =   $request->input('deleted_liabilityid');
    //         // $liabilityid   =   $request->input('liabilityid');

    //     }

    //     if (($request->input('scheme') == 'Y')) {

    //         $schemename   =   $request->input('schemename');
    //     }

    //     $teamhead   =   $request->input('teamhead');
    //     $auditscheduleid =   $request->input('auditscheduleid');
    //     $rejoinderstatus    =   $request->input('rejoinderstatus');

    //     $rejoindercycle     =    $request->input('rejoindercount');
    //     $slipcreatedby     =    $request->input('slipcreatedby');

    //     $actionfor     =    $request->input('actionfor');
    //     $rejoindersuggestion     =    $request->input('rejoindersuggestion');

    //     $auditslipid = ($action == 'update' && $request->auditslipid) ? Crypt::decryptString($request->auditslipid) : null;

    //     $fileupload = $request->file('fileupload');
    //     // $destinationPath = 'slipauditor';

    //     $destinationPath = '';

    //     $userdel = session('user');
    //     $chargeData = session('charge');

    //     $sessionDeptcode = $chargeData->deptcode ?? null;

    //     $session_userid = $userdel->userid ?? null;
    //     if (!$session_userid) {
    //         return response()->json(['error' => 'User session is invalid.'], 400);
    //     }

    //     $deactive_fileuploadids = $request->input('deactive_fileid') ? explode(',', $request->input('deactive_fileid')) : [];
    //     $active_fileuploadids = $request->input('active_fileid') ? explode(',', $request->input('active_fileid')) : [];

    //     // Deactivate Files
    //     if (!empty($deactive_fileuploadids)) {
    //         $this->fileUploadService->deactive_uploadefile($auditslipid, $deactive_fileuploadids);
    //     }

    //     $fileUploadId = null;

    //     if ((($action === 'insert') || ($action === 'update')) && ($request->hasFile('fileupload'))) {
    //         $uploadResult = $this->fileUploadService->slipMultipleFileUpload(
    //             $fileupload,
    //             $destinationPath,
    //             $auditslipid,
    //             $active_fileuploadids,
    //             $deactive_fileuploadids,
    //             '',
    //             $auditscheduleid
    //         );

    //         // print_r($uploadResult );

    //         if (is_array($uploadResult) && isset($uploadResult['error'])) {
    //             return response()->json(['errors' => $uploadResult['error']], 400);
    //         } elseif ($uploadResult instanceof \Illuminate\Http\JsonResponse) {
    //             $fileUploadId = $uploadResult->getData(true)['uploaded_files'];
    //             // print_r($fileUploadId);
    //         }
    //     }

    //     $request->validate([
    //         'majorobjectioncode' => ['required_if:actionfor,fresh', 'string', 'regex:/^\d+$/'],
    //         'minorobjectioncode' => ['required_if:actionfor,fresh', 'regex:/^\d+$/'],
    //         'amount_involved' => 'nullable|regex:/^\d{1,10}(\.\d{1,2})?$/',
    //         'severityid' => 'required|alpha|max:1',
    //         'liability' => 'required|alpha|max:1',
    //         'slipdetails' => 'required|string|max:500',

    //         'scheme' => 'required|alpha|max:1',
    //         'serious' => 'required|string|max:2',
    //         'category' => 'required|string|max:2',
    //         'subcategory' => 'required|string|max:2',
    //     ]);

    //     $content = json_encode(['content' => $request->input('remarks')]);

    //     $data = [
    //         'auditscheduleid' => $request->input('auditscheduleid'),
    //         'schteammemberid' => $request->input('schteammemberid'),
    //         'auditplanid' => $request->input('auditplanid'),
    //         'mainobjectionid' => $request->input('majorobjectioncode'),
    //         'subobjectionid' => $request->input('minorobjectioncode'),
    //         'tempslipnumber' => $request->input('currentslipnumber'),
    //         'severitycode' => $request->input('severityid'),
    //         'liability' => $request->input('liability'),

    //         'schemastatus' => $request->input('scheme'),
    //         'auditeeschemecode' => $request->input('schemename'),

    //         'irregularitiescode' => $request->input('serious'),
    //         'irregularitiescatcode' => $request->input('category'),
    //         'irregularitiessubcatcode' => $request->input('subcategory'),

    //         'slipdetails' => $request->input('slipdetails'),
    //         'remarks' => $content,
    //         'statusflag' => 'Y',
    //         // 'liabilityname' => $request->input('liability') == 'Y' ? $request->input('liabilityname') : '',
    //         // 'liabilitygpfno' => $request->input('liability') == 'Y' ? $request->input('liabilitygpfno') : '',
    //         // 'liabilitydesig' => $request->input('liability') == 'Y' ? $request->input('liabilitydesig') : '',

    //     ];

    //     if ($request->input('amount_involved')) {
    //         $data['amtinvolved'] = $request->input('amount_involved');
    //     } else    $data['amtinvolved']    =   null;

    //     if ($action === 'insert') {
    //         $processcode    =    'E';
    //         $data['processcode'] = 'E';
    //         $data['createdon'] = View::shared('get_nowtime');
    //         $data['createdby'] = $session_userid;
    //     } elseif ($action === 'update') {
    //         if (($slipcreatedby != $session_userid) &&  $teamhead == 'Y' && $actionfor == 'fresh') {
    //             $processcode    =    'T';
    //         } elseif (($slipcreatedby == $session_userid) && $actionfor == 'fresh') {
    //             $processcode    =    'E';
    //         } elseif (($actionfor == 'memeberrejoinder')) {
    //             $processcode    =    'R';
    //             if ($rejoindersuggestion ==  'Y')
    //                 $rejoinderstatus    =  'R';
    //             $data['rejoinderstatus'] =  $rejoinderstatus;
    //         } elseif (($actionfor == 'drop')) {
    //             $processcode    =    'A';
    //         } elseif (($actionfor == 'converttopara')) {
    //             $processcode    =    'X';
    //         } elseif (($actionfor == 'rejoinder')) {
    //             $processcode    =    'F';
    //             $data['rejoinderstatus'] =  'Y';
    //             $rejoinderstatus    =   'Y';
    //             if ($rejoindercycle ==  '') $rejoindercycle = 0;
    //             $rejoindercycle =   $rejoindercycle +   1;
    //             $data['rejoindercycle'] =  $rejoindercycle;
    //         }

    //         $data['updatedon'] = View::shared('get_nowtime');
    //         $data['updatedby'] = $session_userid;
    //     }

    //     // print_r($data);

    //     // exit;

    //     // DB::beginTransaction();

    //     try {
    //         $auditslipdel = FieldAuditModel::createIfNotExistsOrUpdate($data, $auditslipid, $auditscheduleid, $sessionDeptcode);
    //         $auditslipnumber = $auditslipdel['slipnumber'];
    //         $auditslipid = $auditslipdel['auditslipid'];

    //         if ($fileUploadId) {
    //             $this->fileUploadService->insert_slipfileupload($auditslipid, $fileUploadId, $rejoinderstatus, $rejoindercycle, $processcode);
    //         }

    //         // if(($request->input('liability') == 'Y'))
    //         // {
    //         //     FieldAuditModel::insertUpdateliability($auditslipid,$name,$gpfno,$designation,$amount,$liabilitydel[$i],)
    //         // }

    //         if (($request->input('liability') == 'Y')) {

    //             $activestatus   =   '';
    //             if ($processcode == 'E') {

    //                 if ($deleted_liabilityid) {
    //                     $deletedliabilitydel  = explode(",", $deleted_liabilityid);
    //                     FieldAuditModel::deleteLiability($deletedliabilitydel, $session_userid);
    //                 }
    //             } else {

    //                 $activestatus   =   $request->input('activestatus');
    //                 // return $activestatus;
    //             }

    //             FieldAuditModel::insertupdateLiability($liabilitydel, $notype, $name, $gpfno, $designation, $amount, $processcode, $auditslipid, $session_userid, $activestatus);
    //         }

    //         if ($request->input('finaliseflag') === 'Y') {
    //             $session_usertypecode = $chargeData->usertypecode ?? null;
    //             $session_userchargeid = $chargeData->userchargeid ?? null;

    //             $historyData = [
    //                 'auditslipid' => $auditslipid,
    //                 'auditscheduleid' => $request->input('auditscheduleid'),
    //                 'schteammemberid' => $request->input('schteammemberid'),
    //                 'auditplanid' => $request->input('auditplanid'),
    //                 'mainobjectionid' => $request->input('majorobjectioncode'),
    //                 'subobjectionid' => $request->input('minorobjectioncode'),
    //                 'tempslipnumber' => $auditslipnumber,
    //                 'severityid' => $request->input('severityid'),
    //                 'liability' => $request->input('liability'),
    //                 'slipdetails' => $request->input('slipdetails'),
    //                 'remarks' => $content,
    //                 'forwardedby' => $session_userid,
    //                 'forwardedbyusertypecode' => $session_usertypecode,
    //                 'transstatus' => 'A',
    //                 'forwardedon' => View::shared('get_nowtime'),
    // 	    'schemastatus' => $request->input('scheme'),
    //                 'irregularitiescode' => $request->input('serious'),
    //                 'irregularitiescatcode' => $request->input('category'),
    //                 'irregularitiessubcatcode' => $request->input('subcategory'),
    //             ];

    //             if ($rejoinderstatus ==  'Y')    $historyData['rejoinderstatus']    =   'Y';
    //             if (($rejoindercycle > 0))    $historyData['rejoindercycle']    =   $rejoindercycle;

    // 	if (($request->input('scheme') == 'Y')) {
    //                 $historyData['auditeeschemecode']    =   $request->input('schemename');
    //             }

    //             //print_r($historyData);

    //             $updateTransauditData = [
    //                 'updatedby' => $session_userid,
    //                 'updatedbyusertypecode' => $session_usertypecode,
    //                 'updatedon' => View::shared('get_nowtime'),
    //             ];

    //             if ($teamhead == 'N') {
    //                 $forwardto = $request->input('teamheadid');
    //                 $historyData['forwardedto'] =  $forwardto;
    //                 $historyData['forwardedtousertypecode'] =  'A';
    //                 if ($actionfor == 'fresh') {
    //                     $updateTransauditData['processcode'] =  'T';
    //                     $processcode_slipfileupload =   'T';
    //                     $historyData['processcode']   =  'T';
    //                 } else {
    //                     $updateTransauditData['processcode']  = $processcode;
    //                     $processcode_slipfileupload =  $processcode;
    //                     $historyData['processcode']   =  $processcode;
    //                 }

    //                 $updateTransauditData['forwardedto'] =  $forwardto;
    //                 $updateTransauditData['forwardedtousertypecode'] =  'A';
    //                 $message    =   'Audit slip Details Forward to Team Head successfully.';
    //             } else {
    //                 $instid = $request->input('instid');
    //                 $forwardto = FieldAuditModel::fetchdata_auditeeuserid($instid);
    //                 $updateTransauditData['remarks'] =   null;;

    //                 if (($actionfor == 'fresh') || ($actionfor == 'rejoinder')) {
    //                     $updateTransauditData['processcode'] =  'F';
    //                     $processcode_slipfileupload =   'F';
    //                     $updateTransauditData['forwardedto'] =  $forwardto[0];
    //                     $updateTransauditData['forwardedtousertypecode'] =  'I';
    //                     $historyData['forwardedtousertypecode'] =  'I';
    //                     $historyData['forwardedto'] =  $forwardto[0];
    //                     $historyData['processcode']   =  'F';

    //                     $message    =   'Audit Slip forwarded to Auditee successfully.';
    //                 } else {
    //                     $updateTransauditData['processcode']  = $processcode;
    //                     $processcode_slipfileupload =  $processcode;
    //                     $updateTransauditData['forwardedto'] =  null;
    //                     $historyData['processcode']   =  $processcode;
    //                     $updateTransauditData['forwardedtousertypecode'] =  null;
    //                     $message    =   'Audit Slip Completed successfully.';
    //                 }

    //                 // FieldAuditModel::insertupdateLiability($liabilitydel,$notype,$name,$gpfno,$designation,$amount,$processcode,$auditslipid,$session_userid);

    //             }

    //             // echo $processcode_slipfileupload;
    //             // echo $processcode;
    //             // print_r($historyData);
    //             // print_r($updateTransauditData);

    //             if ($forwardto) {

    //                 $transactionResult = FieldAuditModel::insert_historytransactiondel($historyData, $auditslipid);

    //                 if ($transactionResult) {

    //                     $updateSlip = FieldAuditModel::update_auditsliptable($updateTransauditData, $auditslipid);
    //                     if ($updateSlip) {

    //                         FieldAuditModel::updateslipfileupload($processcode_slipfileupload, $session_userid, $processcode, $auditslipid, $session_usertypecode, $rejoinderstatus, $rejoindercycle);

    //                         // DB::commit();
    //                         return response()->json(['success' => true, 'message' => $message, 'data' => array('slid' => Crypt::encryptString($auditslipid), 'auditslipnumber' => $auditslipnumber)]);
    //                     } else {
    //                         throw new \Exception("Failed to update the auditslip table.");
    //                     }
    //                 }
    //             } else {
    //                 return response()->json(['success' => true, 'message' => 'No User Found', 'data' => $auditslipid]);
    //             }
    //         } else {
    //             return response()->json(['success' => true, 'message' => 'Audit Slip saved successfully.', 'data' => array('slid' => Crypt::encryptString($auditslipid), 'auditslipnumber' => $auditslipnumber)]);
    //         }
    //     } catch (\Exception $e) {
    //         // Rollback the transaction on failure
    //         // DB::rollBack();
    //         return response()->json(['error' => $e->getMessage()], 400);
    //     }
    // }

    public function audislip_insert(Request $request, $userId = null)
    {
        $fileCount = count(array_filter($_FILES['fileupload']['name']));

        $fileuplaodstatus = 'N';

        $spilloverflag = $request->input('spilloverflag');
        $instid = $request->input('instid');

        // echo $spilloverflag;

        if ($fileCount > 0)
            $fileuplaodstatus = 'Y';

        $userdel = session('user');
        $chargeData = session('charge');

        $sessionDeptcode = $chargeData->deptcode ?? null;

        $session_userid = $userdel->userid ?? null;
        if (!$session_userid) {
            return response()->json(['error' => 'User session is invalid.'], 400);
        }

        $formsessionuserid = Crypt::decryptString($request->ens);

        if ($session_userid != $formsessionuserid)
            return response()->json(['success' => false, 'message' => 'Please refresh the page maintain one login at a time'], 402);

        $auditscheduleid = Crypt::decryptString($request->auditscheduleid);

        $request->merge(['auditscheduleid' => $auditscheduleid]);

        $action = $request->input('action');

        $auditslipid = ($action == 'update' && $request->auditslipid) ? Crypt::decryptString($request->auditslipid) : null;

        if ($action == 'update') {
            $scheduledel = FieldAuditModel::checkscheduleid($auditscheduleid, $auditslipid);

            if ($scheduledel == 'false')
                return response()->json(['success' => false, 'message' => 'Wrongly mapped with institution.pls Contact administator'], 402);
        }

        $draftcount = 1;

        if (($action == 'insert') && ($request->input('finaliseflag') != 'Y')) {
            $draftcount = FieldAuditModel::checkalreadydraftexists($auditscheduleid, $session_userid);
        }

        if ($draftcount > 1) {
            return response()->json(['success' => false, 'message' => 'Please complete the previous draft slip(s)'], 402);
        }

        // $subobjectionpresent = FieldAuditModel::checkingsubobjection_undermainobjection(
        //     $request->input('majorobjectioncode'),
        //     $request->input('minorobjectioncode')
        // );

        $subobjectionpresent = FieldAuditModel::checkingsubobjection_undermainobjection(
            $request->input('majorobjectioncode'),
            $request->input('minorobjectioncode'),
            $request->input('slipcreatedyear'),
            $request->input('catcode'),
            $request->input('subcatid'),
            $request->input('annadhanam_only')
        );

        if ($subobjectionpresent < 1) {
            return response()->json(['success' => false, 'message' => 'Please check Main objection and subobjection'], 402);
        }

        $liabilitydel = $request->input('liability');

        if ($liabilitydel === 'Y') {
            $request->validate([
                'name' => 'required|array',
                'name.*' => ['required', 'max:50', 'regex:/^[\p{Tamil}A-Za-z\s]+$/u'],
                'gpfno' => 'required|array',
                'gpfno.*' => ['required', 'max:20', 'regex:/^\d+$/'],
                'amount' => 'required|array',
                'amount.*' => ['required', 'numeric', 'max:999999999'],
                'designation' => 'required|array',
                'designation.*' => ['required', 'max:50', 'regex:/^[\p{Tamil}A-Za-z\s]+$/u'],
                'notype' => 'required|array',
                'notype.*' => ['required', 'max:20', 'regex:/^\d+$/'],  // Replace with your allowed types
                'liabilityid' => 'required|array',
                'liabilityid.*' => ['nullable', 'integer'],
            ],
                [
                    'name.required' => 'The name field is required.',
                    'name.*.required' => 'Liability name is required.',
                    'name.*.max' => 'Liability name must not exceed 50 characters.',
                    'name.*.regex' => 'Liability name must contain only letters and spaces.',
                    'gpfno.required' => 'The GPF number field is required.',
                    'gpfno.*.required' => 'Liability GPF number is required.',
                    'gpfno.*.max' => 'Liability GPF number must not exceed 20 digits.',
                    'gpfno.*.regex' => 'Liability GPF number must be numeric.',
                    'amount.required' => 'The amount field is required.',
                    'amount.*.required' => 'Liability amount is required.',
                    'amount.*.numeric' => 'Liability amount must be a valid number.',
                    'amount.*.max' => 'EacLiabilityh amount must not exceed 999999.',
                    'designation.required' => 'Liability designation field is required.',
                    'designation.*.required' => 'Liability designation is required.',
                    'designation.*.max' => 'Liability designation must not exceed 50 characters.',
                    'notype.required' => 'Liability Number type field is required.',
                    'notype.*.required' => 'Liability Number type is required.',
                    'notype.*.max' => 'Liability Number type must not exceed 20 characters.',
                    'notype.*.regex' => 'Liability Number type must be numeric.',
                    'liabilityid.required' => 'The liability ID field is required.',
                    'liabilityid.*.integer' => 'Each liability ID must be an integer.',
                ]);

            $notype = $request->input('notype');
            $name = $request->input('name');
            $gpfno = $request->input('gpfno');
            $amount = $request->input('amount');
            $designation = $request->input('designation');
            $liabilityid = $request->input('liabilityid');
            $liabilitydel = $request->input('liabilityid');
            $count_name = count($name);
            $deleted_liabilityid = $request->input('deleted_liabilityid');
        }

        if (($request->input('scheme') == 'Y')) {
            $schemename = $request->input('schemename');
        }

        $teamhead = $request->input('teamhead');
        $auditscheduleid = $request->input('auditscheduleid');
        $rejoinderstatus = $request->input('rejoinderstatus');
        $rejoindercycle = $request->input('rejoindercount');
        $slipcreatedby = $request->input('slipcreatedby');
        $actionfor = $request->input('actionfor');
        $rejoindersuggestion = $request->input('rejoindersuggestion');
        $auditquartercode = $request->input('auditquartercode');
        $financialyear = $request->input('financialyear');

        $fileupload = $request->file('fileupload');
        $destinationPath = '';

        $deactive_fileuploadids = $request->input('deactive_fileid') ? explode(',', $request->input('deactive_fileid')) : [];
        $active_fileuploadids = $request->input('active_fileid') ? explode(',', $request->input('active_fileid')) : [];

        // Deactivate Files
        if (!empty($deactive_fileuploadids)) {
            $this->fileUploadService->deactive_uploadefile($auditslipid, $deactive_fileuploadids);
        }

        $fileUploadId = null;

        if ((($action === 'insert') || ($action === 'update')) && ($request->hasFile('fileupload'))) {
            $uploadResult = $this->fileUploadService->slipMultipleFileUpload(
                $fileupload,
                $destinationPath,
                $auditslipid,
                $active_fileuploadids,
                $deactive_fileuploadids,
                '',
                $auditscheduleid
            );

            if (is_array($uploadResult) && isset($uploadResult['error'])) {
                return response()->json(['errors' => $uploadResult['error']], 400);
            } elseif ($uploadResult instanceof \Illuminate\Http\JsonResponse) {
                $fileUploadId = $uploadResult->getData(true)['uploaded_files'];
            }
        }

        $request->validate([
            'majorobjectioncode' => ['required_if:actionfor,fresh', 'string', 'regex:/^\d+$/'],
            'minorobjectioncode' => ['required_if:actionfor,fresh', 'regex:/^\d+$/'],
            'amount_involved' => 'nullable|regex:/^\d{1,10}(\.\d{1,2})?$/',
            'severityid' => 'required|alpha|max:1',
            'liability' => 'required|alpha|max:1',
            'slipdetails' => 'required|string|max:500|min:10',
            'scheme' => 'required|alpha|max:1',
            'schemename' => ['nullable', 'required_if:scheme,Y', 'string', 'regex:/^\d+$/'],
            'serious' => 'required|string|max:2',
            'category' => 'required|string|max:2',
            'subcategory' => 'required|string|max:2',
            'remarks' => 'required|string|min:20',
            'auditscheduleid' => 'required|string|regex:/^\d+$/',
            'financialyear' => ['required', 'integer', 'digits:4'],
            'auditquartercode' => ['required', 'in:Q1,Q2,Q3,Q4']
        ], [
            // majorobjectioncode
            'majorobjectioncode.required_if' => 'Major objection code is required when actionfor is fresh.',
            'majorobjectioncode.string' => 'Major objection code must be a valid string.',
            'majorobjectioncode.regex' => 'Major objection code must contain only digits.',
            // minorobjectioncode
            'minorobjectioncode.required_if' => 'Minor objection code is required when actionfor is fresh.',
            'minorobjectioncode.regex' => 'Minor objection code must contain only digits.',
            // amount_involved
            'amount_involved.regex' => 'Amount involved must be up to 10 digits with up to 2 decimal places.',
            // severityid
            'severityid.required' => 'Severity field is required.',
            'severityid.alpha' => 'Severity must contain only letters.',
            'severityid.max' => 'Severity must not exceed 1 character.',
            // liability
            'liability.required' => 'Liability field is required.',
            'liability.alpha' => 'Liability must contain only letters.',
            'liability.max' => 'Liability must not exceed 1 character.',
            // slipdetails
            'slipdetails.required' => 'Slip details field is required.',
            'slipdetails.string' => 'Slip details must be a valid string.',
            'slipdetails.max' => 'Slip details must not exceed 500 characters.',
            'slipdetails.min' => 'Slip details must be at least 10 characters long.',
            // scheme
            'scheme.required' => 'Scheme field is required.',
            'scheme.alpha' => 'Scheme must contain only letters.',
            'scheme.max' => 'Scheme must not exceed 1 character.',
            // schemename
            'schemename.required_if' => 'Scheme name is required when scheme is Y.',
            'schemename.string' => 'Scheme name must be a valid string.',
            'schemename.regex' => 'Scheme name must contain only digits.',
            // serious
            'serious.required' => 'Serious field is required.',
            'serious.string' => 'Serious must be a valid string.',
            'serious.max' => 'Serious must not exceed 2 characters.',
            // category
            'category.required' => 'Category field is required.',
            'category.string' => 'Category must be a valid string.',
            'category.max' => 'Category must not exceed 2 characters.',
            // subcategory
            'subcategory.required' => 'Subcategory field is required.',
            'subcategory.string' => 'Subcategory must be a valid string.',
            'subcategory.max' => 'Subcategory must not exceed 2 characters.',
            // remarks
            'remarks.required' => 'Remarks field is required.',
            'remarks.string' => 'Remarks must be a valid string.',
            'remarks.min' => 'Remarks must be at least 20 characters long.',
            // auditscheduleid
            'auditscheduleid.required' => 'Audit schedule ID is required.',
            'auditscheduleid.string' => 'Audit schedule ID must be a valid string.',
            'auditscheduleid.regex' => 'Audit schedule ID must contain only digits.',
            // financialyear
            'financialyear.required' => 'Financial year field is required.',
            'financialyear.integer' => 'Financial year must be a number.',
            'financialyear.digits' => 'Financial year must be exactly 4 digits.',
            // auditquartercode
            'auditquartercode.required' => 'Audit quarter field is required.',
            'auditquartercode.in' => 'Audit quarter must be one of Q1, Q2, Q3, or Q4.'
        ]);

        $content = json_encode(['content' => $request->input('remarks')]);

        $data = [
            'mainobjectionid' => $request->input('majorobjectioncode'),
            'subobjectionid' => $request->input('minorobjectioncode'),
            'tempslipnumber' => $request->input('currentslipnumber'),
            'severitycode' => $request->input('severityid'),
            'liability' => $request->input('liability'),
            'schemastatus' => $request->input('scheme'),
            'auditeeschemecode' => $request->input('schemename'),
            'irregularitiescode' => $request->input('serious'),
            'irregularitiescatcode' => $request->input('category'),
            'irregularitiessubcatcode' => $request->input('subcategory'),
            'slipdetails' => $request->input('slipdetails'),
            'remarks' => $content,
            'statusflag' => 'Y',
        ];
        if ($action == 'insert') {
            $data['auditscheduleid'] = $request->input('auditscheduleid');
            $data['auditplanid'] = $request->input('auditplanid');
            $data['schteammemberid'] = $request->input('schteammemberid');
            $data['financialyear'] = $financialyear;
            $data['quartercode'] = $auditquartercode;
        }

        if ($request->input('amount_involved')) {
            $data['amtinvolved'] = $request->input('amount_involved');
        } else
            $data['amtinvolved'] = null;

        if ($action === 'insert') {
            $processcode = 'E';
            $data['processcode'] = 'E';
            $data['createdon'] = View::shared('get_nowtime');
            $data['createdby'] = $session_userid;

            $data['updatedon'] = View::shared('get_nowtime');
            $data['updatedby'] = $session_userid;
        } elseif ($action === 'update') {
            if (($slipcreatedby != $session_userid) && $teamhead == 'Y' && $actionfor == 'fresh') {
                $processcode = 'T';
            } elseif (($slipcreatedby == $session_userid) && $actionfor == 'fresh') {
                $processcode = 'E';
            } elseif (($actionfor == 'memeberrejoinder')) {
                $processcode = 'R';
                if ($rejoindersuggestion == 'Y')
                    $rejoinderstatus = 'R';
                $data['rejoinderstatus'] = $rejoinderstatus;
            } elseif (($actionfor == 'drop')) {
                $processcode = 'A';
            } elseif (($actionfor == 'converttopara')) {
                $processcode = 'X';
            } elseif (($actionfor == 'rejoinder')) {
                $processcode = 'F';
                $data['rejoinderstatus'] = 'Y';
                $rejoinderstatus = 'Y';
                if ($rejoindercycle == '')
                    $rejoindercycle = 0;
                $rejoindercycle = $rejoindercycle + 1;
                $data['rejoindercycle'] = $rejoindercycle;
            }

            $data['updatedon'] = View::shared('get_nowtime');
            $data['updatedby'] = $session_userid;
        } else {
            return response()->json(['error' => true, 'message' => 'No Action Found', 'data' => '']);
        }

        try {
            DB::beginTransaction();
            if ($session_userid) {
                $auditslipdel = FieldAuditModel::createIfNotExistsOrUpdate($data, $auditslipid, $auditscheduleid, $sessionDeptcode, $spilloverflag, $instid);
                $auditslipnumber = $auditslipdel['slipnumber'];
                $auditslipid = $auditslipdel['auditslipid'];

                if (($fileuplaodstatus == 'Y')) {
                    if ($fileUploadId) {
                        $this->fileUploadService->insert_slipfileupload($auditslipid, $fileUploadId, $rejoinderstatus, $rejoindercycle, $processcode);
                    } else {
                        return response()->json(['error' => true, 'message' => 'Fileupload issue', 'data' => '']);
                    }
                }

                if (($request->input('liability') == 'Y')) {
                    $activestatus = '';

                    if ($processcode == 'E' && $deleted_liabilityid) {
                        $deletedliabilitydel = explode(',', $deleted_liabilityid);
                        FieldAuditModel::deleteLiability($deletedliabilitydel, $session_userid);
                    } else {
                        $activestatus = $request->input('activestatus');
                    }

                    FieldAuditModel::insertupdateLiability($liabilitydel, $notype, $name, $gpfno, $designation, $amount, $processcode, $auditslipid, $session_userid, $activestatus);
                }

                if ($request->input('finaliseflag') === 'Y') {
                    $session_usertypecode = $chargeData->usertypecode ?? null;
                    $session_userchargeid = $chargeData->userchargeid ?? null;

                    $historyData = [
                        'auditslipid' => $auditslipid,
                        'auditscheduleid' => $request->input('auditscheduleid'),
                        'schteammemberid' => $request->input('schteammemberid'),
                        'auditplanid' => $request->input('auditplanid'),
                        'mainobjectionid' => $request->input('majorobjectioncode'),
                        'subobjectionid' => $request->input('minorobjectioncode'),
                        'tempslipnumber' => $auditslipnumber,
                        'severityid' => $request->input('severityid'),
                        'liability' => $request->input('liability'),
                        'slipdetails' => $request->input('slipdetails'),
                        'remarks' => $content,
                        'forwardedby' => $session_userid,
                        'forwardedbyusertypecode' => $session_usertypecode,
                        'transstatus' => 'A',
                        'forwardedon' => View::shared('get_nowtime'),
                        'schemastatus' => $request->input('scheme'),
                        'irregularitiescode' => $request->input('serious'),
                        'irregularitiescatcode' => $request->input('category'),
                        'irregularitiessubcatcode' => $request->input('subcategory'),
                    ];

                    if ($rejoinderstatus == 'Y') {
                        $historyData['rejoinderstatus'] = 'Y';
                    }

                    if (($rejoindercycle > 0)) {
                        $historyData['rejoindercycle'] = $rejoindercycle;
                    }

                    if (($request->input('scheme') == 'Y')) {
                        $historyData['auditeeschemecode'] = $request->input('schemename');
                    }

                    $updateTransauditData = [
                        'updatedby' => $session_userid,
                        'updatedbyusertypecode' => $session_usertypecode,
                        'updatedon' => View::shared('get_nowtime'),
                    ];

                    if ($teamhead == 'N') {
                        $forwardto = $request->input('teamheadid');
                        $historyData['forwardedto'] = $forwardto;
                        $historyData['forwardedtousertypecode'] = 'A';
                        $historyData['processcode'] = ($actionfor == 'fresh') ? 'T' : $processcode;

                        $updateTransauditData['forwardedto'] = $forwardto;
                        $updateTransauditData['forwardedtousertypecode'] = 'A';
                        $updateTransauditData['processcode'] = $historyData['processcode'];

                        $processcode_slipfileupload = $historyData['processcode'];
                        $message = 'Audit slip Details Forward to Team Head successfully.';
                    } else {
                        $instid = $request->input('instid');
                        $forwardto = FieldAuditModel::fetchdata_auditeeuserid($instid);
                        $forwardtoUser = $forwardto[0] ?? null;

                        if (($actionfor == 'fresh') || ($actionfor == 'rejoinder')) {
                            $historyData['forwardedto'] = $forwardtoUser;
                            $historyData['forwardedtousertypecode'] = 'I';
                            $historyData['processcode'] = 'F';

                            $updateTransauditData['forwardedto'] = $forwardtoUser;
                            $updateTransauditData['forwardedtousertypecode'] = 'I';
                            $updateTransauditData['processcode'] = 'F';

                            $processcode_slipfileupload = 'F';
                            $message = 'Audit Slip forwarded to Auditee successfully.';
                        } else {
                            $historyData['processcode'] = $processcode;
                            $updateTransauditData['processcode'] = $processcode;
                            $processcode_slipfileupload = $processcode;
                            $message = 'Audit Slip Completed successfully.';
                        }
                    }

                    if (isset($forwardto)) {
                        $transactionResult = FieldAuditModel::insert_historytransactiondel($historyData, $auditslipid);

                        if ($transactionResult) {
                            $updateSlip = FieldAuditModel::update_auditsliptable($updateTransauditData, $auditslipid);

                            if ($updateSlip) {
                                FieldAuditModel::updateslipfileupload($processcode_slipfileupload, $session_userid, $processcode, $auditslipid, $session_usertypecode, $rejoinderstatus, $rejoindercycle);

                                DB::commit();  // ✅ COMMIT TRANSACTION
                                return response()->json([
                                    'success' => true,
                                    'message' => $message,
                                    'data' => ['slid' => Crypt::encryptString($auditslipid), 'auditslipnumber' => $auditslipnumber]
                                ]);
                            } else {
                                return response()->json(['success' => false, 'message' => 'Failed to update auditslip table.', 'data' => $auditslipid]);
                            }
                        } else {
                            return response()->json(['success' => false, 'message' => 'Failed to insert history auditslip table.', 'data' => $auditslipid]);
                        }
                    } else {
                        DB::commit();  // ✅ COMMIT TRANSACTION
                        return response()->json(['success' => false, 'message' => 'No User Found', 'data' => $auditslipid]);
                    }
                } else {
                    DB::commit();  // ✅ COMMIT TRANSACTION
                    return response()->json([
                        'success' => true,
                        'message' => 'Audit Slip saved successfully.',
                        'data' => ['slid' => Crypt::encryptString($auditslipid), 'auditslipnumber' => $auditslipnumber]
                    ]);
                }
            } else {
                return response()->json(['error' => true, 'message' => 'No Session user not Found', 'data' => '']);
            }
        } catch (\Exception $e) {
            DB::rollBack();  // ❌ ROLLBACK ON ERROR
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getsubobjection(Request $request)
    {
        // $mainobjectioncode = $request->input('mainobjectioncode');
        // $subobjectiondel = FieldAuditModel::getsubobjection($mainobjectioncode);
        // $mainobjectioncode = $request->input('mainobjectioncode');
        $mainobjectioncode = $request->input('mainobjectioncode');
        $datafromapi = $request->input('datafromapi');
        $catcode = $request->input('catcode');
        $subcatid = $request->input('subcatid');
        $financialyearcode = $request->input('financialyearcode');
        $annadhanam_only = $request->input('annadhanam_only');

        $subobjectiondel = FieldAuditModel::getsubobjection($mainobjectioncode, $catcode, $subcatid, $financialyearcode, $annadhanam_only, $datafromapi);  // Fetch user data based on deptuserid

        // Fetch user data based on deptuserid
        // $user = UserModel::where('deptuserid', $deptuserid)->first(); // Adjust query as needed

        if ($subobjectiondel) {
            return response()->json(['success' => true, 'data' => $subobjectiondel]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }

    // //////////////////////////////Work Allocation/////////////////////////////////////////////

    public function insert_workAllocation(Request $request)
    {
        $request->validate([
            'finaliseflag' => 'required',  // Ensures only digits, allows leading zeros
            'auditscheduleId' => 'required',  // Only alphabets (no numbers or symbols)
            'team_mem' => 'required',  // Alphanumeric (letters and numbers)
            'majorwa' => 'required',
        ]);

        if ($request->work_action == 'update') {
            $workallocationid = Crypt::decryptString($request->workallocationid);
            $request->merge(["workallocationid\t" => $workallocationid]);
        }
        $data = [
            'auditscheduleid' => $request->input('auditscheduleId'),
            'schteammemberid' => $request->input('team_mem'),
            'majorwa' => $request->input('majorwa'),
            'statusflag' => $request->input('finaliseflag'),
            'subtypecode' => $request->input('minorwa'),
        ];
        $minortypeID = $request->input('minorwa');
        if ($request->work_action == 'update') {
            $existingwork = TransWorkAllocationModel::fetchexistingwork($data);
            // foreach ($minortypeID as $subtypecode) {
            $auditscheduleid = trim($request->input('auditscheduleId'));

            if ($existingwork) {
                $existingWorkIds = $existingwork
                    ->filter(function ($work) use ($auditscheduleid) {  // Pass $auditscheduleid into the callback
                        return $work->auditscheduleid == $auditscheduleid && $work->statusflag === 'Y';
                    })
                    ->pluck('subtypecode')
                    ->toArray();

                $minortypeID = $request->input('minorwa');
                // print_r($existingWorkIds);
                // print_r($minortypeID);
                $existingWorkIdsEqualToMinortypecode = empty(array_diff($minortypeID, $existingWorkIds)) && empty(array_diff($existingWorkIds, $minortypeID));
                $newIdsExist = array_diff($minortypeID, $existingWorkIds);
                $idsToRemove = array_diff($existingWorkIds, $minortypeID);
                // print_r($newIdsExist);
                if (!empty($newIdsExist)) {
                    foreach ($minortypeID as $subtypecodeAdd) {
                        // Check if the subtypecode exists in minortype
                        if (in_array($subtypecodeAdd, $newIdsExist)) {
                            // echo 'if';
                            // print_r($subtypecodeAdd);
                            TransWorkAllocationModel::create([
                                'auditscheduleid' => $request->input('auditscheduleId'),
                                'schteammemberid' => $request->input('team_mem'),
                                'statusflag' => $request->input('finaliseflag'),
                                'subtypecode' => $subtypecodeAdd,
                                'createdon' => View::shared('get_nowtime'),
                                'updatedon' => View::shared('get_nowtime'),
                            ]);
                        } else {
                            // echo 'else';
                            // print_r($subtypecodeAdd);
                            // Add the new subtypecode that is not in minortype
                            TransWorkAllocationModel::updatework($data, $subtypecodeAdd, $request->input('auditscheduleId'));
                        }
                    }
                }
                if (!empty($idsToRemove)) {
                    foreach ($minortypeID as $subtypecodeAdd) {
                        // Check if the current minor type is in the removal list
                        if (in_array($subtypecodeAdd, $idsToRemove)) {
                            // If it's in the removal list, update it as removed
                            // TransWorkAllocationModel::where('subtypecode', $subtypecodeAdd)
                            //     ->where('auditscheduleid', $request->input('auditscheduleId'))
                            //     ->where('schteammemberid', $request->input('team_mem'))
                            //     ->update([
                            //         'statusflag' => 'N', // Mark as removed
                            //         'updatedon' => View::shared('get_nowtime'), // Update timestamp
                            //     ]);
                        } else {
                            // If not in the removal list, keep it active
                            TransWorkAllocationModel::where('subtypecode', $subtypecodeAdd)
                                ->where('auditscheduleid', $request->input('auditscheduleId'))
                                ->where('schteammemberid', $request->input('team_mem'))
                                ->update([
                                    'statusflag' => $request->input('finaliseflag'),  // Keep it active or finalized
                                    'updatedon' => View::shared('get_nowtime'),  // Update timestamp
                                ]);
                        }
                    }

                    // Loop through idsToRemove to find any IDs not in minortypeID
                    foreach ($idsToRemove as $subtypecodeToRemove) {
                        if (!in_array($subtypecodeToRemove, $minortypeID)) {
                            // Update the records for IDs that are not in the minor type
                            TransWorkAllocationModel::where('subtypecode', $subtypecodeToRemove)
                                ->where('auditscheduleid', $request->input('auditscheduleId'))
                                ->where('schteammemberid', $request->input('team_mem'))
                                ->update([
                                    'statusflag' => 'N',  // Mark as removed
                                    'updatedon' => View::shared('get_nowtime'),  // Update timestamp
                                ]);
                        }
                    }
                }

                // if (!empty($idsToRemove)) {
                //     foreach ($idsToRemove as $subtypecodeAdd) {
                //         TransWorkAllocationModel::whereIn('subtypecode', $idsToRemove)
                //             ->where('auditscheduleid', $auditscheduleid)
                //             ->where('schteammemberid', $request->input('team_mem'))
                //             // ->where('subtypecode', $subtypecode)
                //             ->update(['statusflag' => 'N']);

                //         // other fields as necessary

                //     }
                // }
                if (empty($newIdsExist) && empty($idsToRemove)) {
                    // print_r($minortypeID);
                    foreach ($minortypeID as $subtypecode) {
                        TransWorkAllocationModel::updatework($data, $subtypecode, $request->input('auditscheduleId'));
                    }
                    // foreach ($idsToRemove as $subtypecodeAdd) {
                    //     TransWorkAllocationModel::updatework($data, $subtypecode, $request->input('auditscheduleId'));
                    // }
                }
            }
            // }
            // $updatework = TransWorkAllocationModel::updatework($data, $subtypecode, $request->input('auditscheduleId'));
            return response()->json(['success' => true, 'message' => 'Work Allocation Data Saved Successfully']);

            // return $workallocationid;
        } else {
            foreach ($minortypeID as $subtypecode) {
                $checkforsubtype = TransWorkAllocationModel::checkforsubtype($data, $subtypecode);
                if ($checkforsubtype) {
                    return $checkforsubtype;
                }
            }
            foreach ($minortypeID as $subtypecode) {
                TransWorkAllocationModel::create([
                    'auditscheduleid' => $request->input('auditscheduleId'),
                    'schteammemberid' => $request->input('team_mem'),
                    'statusflag' => $request->input('finaliseflag'),
                    'subtypecode' => $subtypecode,
                    'createdon' => View::shared('get_nowtime'),
                    'updatedon' => View::shared('get_nowtime'),
                ]);
            }

            // print_r($request->input('team_name'),);
        }
        return response()->json(['success' => true, 'message' => 'Work Allocation Data Saved Successfully']);
    }

    public function fetchAllWorkData(Request $request)
    {
        $TeamHead = $request['teamhead'];
        $userid = $request['userid'];
        $auditscheduleid = $request->auditscheduleid;

        $randomizesWA = AuditManagementModel::checkrandomizedWA($auditscheduleid);
        $randomizesWA_status = $randomizesWA[0]->workallocationflag;

        $workallDetail = TransWorkAllocationModel::fetchworkdetail($auditscheduleid, $TeamHead, $userid);
        foreach ($workallDetail as $item) {
            $item->encrypted_schteammemberid = Crypt::encryptString($item->schteammemberid);
            $item->encrypted_workallocationid = Crypt::encryptString($item->workallocationid);
        }

        return response()->json(['data' => $workallDetail, 'workallc_status' => $randomizesWA_status]);
    }

    public function fetch_singleworkdet(Request $request)
    {
        $schteammemberid = Crypt::decryptString($request->schteammemberid);
        $auditscheduleid = $request->auditscheduleid;
        $major_id = $request->major_id;
        $workallDetail = TransWorkAllocationModel::fetchSingleworkdetail($schteammemberid, $auditscheduleid, $major_id);
        foreach ($workallDetail as $item) {
            $item->encrypted_workallocationid = Crypt::encryptString($item->workallocationid);
        }

        if ($workallDetail) {
            return response()->json(['success' => true, 'data' => $workallDetail]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }

    public function fetchminorworkdel(Request $request)
    {
        $majorworkid = $request->majorworkid;
        $minorworkdel = DB::table('audit.mst_subworkallocationtype')
            ->where('statusflag', 'Y')
            ->where('majorworkallocationtypeid', $majorworkid)
            ->select(
                'mst_subworkallocationtype.subworkallocationtypeename',
                'mst_subworkallocationtype.subworkallocationtypeid',
            )
            ->orderBy('mst_subworkallocationtype.orderid', 'asc')
            ->get();
        return response()->json($minorworkdel);
    }

    // //////////////////////////////Work Allocation/////////////////////////////////////////////

    // ////////////////////////////// Pending Parra /////////////////////////////////////////////

    public function audittrans_dropdown($encrypted_instid, $finanicalyear)
    {
        if ($encrypted_auditscheduleid) {
            $auditscheduleid = Crypt::decryptString($encrypted_auditscheduleid);
        }
        // Echo the ID to verify it's being passed correctly
        // Access session data
        $chargeData = session('charge');
        $session_deptcode = $chargeData->deptcode;  // Accessing the department code from the session
        $session_usertypecode = $chargeData->usertypecode;
        $userData = session('user');
        $session_userid = $userData->userid;

        // exit;
        $inst_details = DB::table('audit.inst_schteammember as sm')
            ->join('audit.inst_auditschedule as is', 'is.auditscheduleid', '=', 'sm.auditscheduleid')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'is.auditplanid')
            ->join('audit.mst_institution as in', 'in.instid', '=', 'ap.instid')
            ->join('audit.mst_auditeeins_category as incat', 'incat.catcode', '=', 'in.catcode')
            ->join('audit.mst_typeofaudit as ta', 'ta.typeofauditcode', '=', 'ap.typeofauditcode')
            ->join('audit.mst_dept as dept', 'in.deptcode', '=', 'dept.deptcode')
            //  ->join('audit.mst_auditperiod as d', 'd.auditperiodid', '=', 'ap.auditperiodid')
            ->join('audit.yearcode_mapping as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
            ->join(
                'audit.mst_auditperiod as d',
                DB::raw('CAST(yrmap.yearselected AS INTEGER)'),
                '=',
                'd.auditperiodid'
            )
            ->where('userid', $session_userid)
            ->where('is.auditscheduleid', $auditscheduleid)
            // Apply STRING_AGG to aggregate years
            ->select(
                'is.auditscheduleid',
                'sm.auditscheduleid',
                'sm.auditteamhead',
                'is.auditplanid',
                'is.fromdate',
                'is.todate',
                'ap.instid',
                'dept.deptelname',
                'in.instename',
                'incat.catename',
                'in.mandays',
                'sm.auditteamhead',
                'ta.typeofauditename',
                'sm.schteammemberid',
                DB::raw("STRING_AGG(DISTINCT d.fromyear || '-' || d.toyear, ', ') as yearname")
            )
            ->groupby('is.auditscheduleid', 'sm.auditscheduleid', 'sm.auditteamhead', 'is.auditplanid', 'is.fromdate', 'is.todate', 'ap.instid', 'dept.deptelname', 'in.instename', 'incat.catename', 'in.mandays', 'sm.auditteamhead', 'ta.typeofauditename', 'sm.schteammemberid')
            ->get();

        return view('audit.transauditslip', compact('inst_details'));

        // You can also add logic to handle the ID if needed
    }

    public function getpendingparadetails(Request $request)
    {
        $auditscheduleid = Crypt::decryptString($request->auditscheduleid);

        $quartercode = $request->quartercode;
        $slipsts = $request->slipsts;
        $filterapply = $request->filterapply;

        // Sanitize and validate input
        // $request->validate([
        //     'auditscheduleid' => 'required|integer'
        // ]);

        // $auditscheduleid = $request->auditscheduleid;

        // Fetch details
        $alldetails = FieldAuditModel::getpendingparadetails($auditscheduleid, $quartercode, $slipsts, $filterapply);
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

    public function pendingparra()
    {
        $sessionuserdel = session('user');
        $sessionuserid = $sessionuserdel->userid;

        $results = DB::table('audit.inst_schteammember as scm')
            ->join('audit.inst_auditschedule as sc', 'sc.auditscheduleid', '=', 'scm.auditscheduleid')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'sc.auditplanid')
            ->join('audit.mst_institution as mi', 'mi.instid', '=', 'ap.instid')
            ->where('auditeeresponse', 'A')
            ->where('scm.userid', '=', $sessionuserid)
            ->groupBy(
                'sc.auditscheduleid',
                'ap.auditplanid',
                'ap.instid',
                'mi.instename',
                'sc.fromdate',
                'sc.todate'
            )
            ->select(
                'sc.auditscheduleid',
                'sc.fromdate',
                'sc.todate',
                'ap.auditplanid',
                'ap.instid',
                'mi.instename'
            )
            ->get();
        foreach ($results as $all) {
            $all->encrypted_auditscheduleid = Crypt::encryptString($all->auditscheduleid);
            $all->formatted_fromdate = Carbon::createFromFormat('Y-m-d', $all->fromdate)->format('d-m-Y');
            $all->formatted_todate = Carbon::createFromFormat('Y-m-d', $all->todate)->format('d-m-Y');
        }

        return view('fieldaudit.pendingpara', compact('results'));
    }

    // ////////////////////////////// Pending Parra /////////////////////////////////////////////

    public static function Supercheck_QuesAns(Request $request)
    {
        $Supercheck_QuesAns = [
            'auditscheduleid' => $request->auditscheduleid,
            'auditplanid' => $request->auditplanid,
            'quesno' => $request->quesno,
            'questiontype' => $request->questiontype,  // Insert the generated yearcodemapping_id here
            'answer_remarks' => $request->answer_remarks
        ];

        $supercheckquesinsert = FieldAuditModel::Supercheck_QuesAns($Supercheck_QuesAns);

        return response()->json(['success' => 'Superchecklist added successfully']);
    }

    public function confirmationdiary(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'diaryflag' => 'required|string|in:Y,N',
                'auditscheduleid' => 'required'
            ]);

            $auditscheduleid = $request->auditscheduleid;
            $instid = $request->instid;
            $exitmeetdate = $request->exitmeetdate;
            $nextQuarterDate = $request->nextQuarterDate;

            $session = session('user');
            $userid = $session->userid;
            $now = View::shared('get_nowtime');
            // $auditmode = $request->auditmode;

            // ✅ Check if any users haven't completed diary
            $pendingUsers = FieldAuditModel::getPendingUsers($auditscheduleid);

            if (!empty($pendingUsers)) {
                DB::rollBack();  // Roll back if users haven't completed diary
                return response()->json([
                    'success' => false,
                    'message' => 'not_all_statusflags_are_Y',
                    'pending_userids' => $pendingUsers['user_en'],
                    'user_list_en' => implode(', ', $pendingUsers['user_en']),
                    'user_list_ta' => implode(', ', $pendingUsers['user_ta'])
                ], 400);
            }

            // ✅ Insert exit meeting entry
            FieldAuditModel::insertExitMeeting($auditscheduleid, $instid, $nextQuarterDate, $userid, $now);
            $Cpsnotfinalized = FieldAuditModel::Cpsnotfinalized($instid, $auditscheduleid);

            if ($Cpsnotfinalized) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'cps_not_finalized',
                ], 400);
            }

            // ✅ Update diary flag
            FieldAuditModel::updateDiaryFlag($auditscheduleid, $request->diaryflag, $nextQuarterDate, $userid, $now);

            DB::commit();  // ✅ Commit all changes if everything succeeds

            return response()->json([
                'success' => true,
                'message' => 'diaryconfirm'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();  // Roll back on validation errors
            return response()->json([
                'success' => false,
                'message' => 'validation_error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();  // Roll back on general exception
            return response()->json([
                'success' => false,
                'message' => 'submission_failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pendingusersfornotspillover(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'diaryflag' => 'required|string|in:Y,N',
                'auditscheduleid' => 'required'
            ]);

            $auditscheduleid = $request->auditscheduleid;
            $instid = $request->instid;
            $session = session('user');
            $userid = $session->userid;
            $now = View::shared('get_nowtime');
            $auditmode = $request->auditmode;
            if ($auditmode != 'P') {
                $pendingUsers = FieldAuditModel::getPendingUsers($auditscheduleid);
            } else {
                $pendingUsers = '';
            }
            // $pendingUsers = FieldAuditModel::getPendingUsers($auditscheduleid);
            // $notreplyusers = FieldAuditModel::notresponseusererror($auditscheduleid);

            // if (!$notreplyusers->isEmpty()) {
            // DB::rollBack();
            //     return response()->json([
            //     'success' => false,
            //    'message' => 'not_replytoauditee',
            //     'not_reply_users' => $notreplyusers
            // ], 400);
            // }

            if (!empty($pendingUsers)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'not_all_statusflags_are_Y',
                    'pending_userids' => $pendingUsers['user_en'],
                    'user_list_en' => implode(', ', $pendingUsers['user_en']),
                    'user_list_ta' => implode(', ', $pendingUsers['user_ta'])
                ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'diaryconfirm'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false
            ], 500);
        }
    }

    private function validateAuditSlip($employee, $fieldName, &$errors)
    {
        if (!empty($employee[$fieldName])) {
            $slipNo = ltrim($employee[$fieldName], '#');

            $exists = DB::table('audit.trans_auditslip')
                ->where('mainslipnumber', $slipNo)
                ->exists();

            if (!$exists) {
                $fieldLabels = [
                    'remittanceexcess_remarks' => 'Remittance in Excess',
                    'credited_remarks' => 'Whether amount is credited in appropriate Head of Accounts (Yes/No)',
                ];

                $label = $fieldLabels[$fieldName] ?? ucfirst(str_replace('_', ' ', $fieldName));

                $errors[] = 'Employee <strong>' . $employee['name'] . '</strong>: '
                    . $label . ' Slip "' . $employee[$fieldName] . '" does not exist.';
            }
        }
    }

    private function validateShortfallTotalMatch(array $employees, array &$errors): void
    {
        foreach ($employees as $index => $employee) {
            $shortfall = (float) ($employee['shortfall'] ?? 0);

            if ($shortfall <= 0) {
                continue;
            }

            $shortfalls = $employee['shortfalls'] ?? [];
            $shortfallTotal = null;

            if (is_array($shortfalls)) {
                foreach ($shortfalls as $sf) {
                    if (isset($sf['shortfall_total']) && $sf['shortfall_total'] !== '' && is_numeric($sf['shortfall_total'])) {
                        $shortfallTotal = (float) $sf['shortfall_total'];
                        break;
                    }
                }
            }

            if ($shortfallTotal === null) {
                $shortfallTotal = collect($shortfalls)->sum(function ($sf) {
                    return (isset($sf['shortfall_amount']) && is_numeric($sf['shortfall_amount']))
                        ? (float) $sf['shortfall_amount']
                        : 0;
                });
            }

            if (abs($shortfall - (float) $shortfallTotal) > 0.009) {
                $employeeName = !empty($employee['name']) ? $employee['name'] : ('#' . ($index + 1));
                $errors[] = 'Employee <strong>' . $employeeName . '</strong>: Shortfall in Remittance and Total Shortfall must be same.';
            }
        }
    }

    public function details($instid, $scheduleid)
    {
        try {
            // 🔓 Decrypt values
            // $instiddec = Crypt::decryptString($instid);
            // $scheduleid = Crypt::decryptString($scheduleid);

            $financialyear = FieldAuditModel::getfinancialyear();
        } catch (DecryptException $e) {
            // If someone tampers URL
            return redirect()->back()->with('error', 'Invalid or tampered URL.');
        }

        return view('fieldaudit.cpsdetails', compact('instid', 'scheduleid', 'financialyear'));
    }

    public function cpsinsert(Request $request)
    {
        $payload = $request->all();
        $auditScheduleId = Crypt::decryptString($request->auditscheduleid);
        $instId = Crypt::decryptString($request->instid);

        $payload['auditscheduleid'] = (int) $auditScheduleId;
        $payload['instid'] = (int) $instId;

        $rules = [
            'auditscheduleid' => 'required|integer',
            'instid' => 'required|integer',
            'employees' => 'required|array|min:1',
            // 'employees.*.department_name' => 'required|string|max:50',
            'employees.*.treasury_cps' => 'nullable|string|max:100',
            'employees.*.ddocode_cps' => [
                    'nullable',
                    'string',
                    'regex:/^(?!00000000)\d{8}$/'
                ],
            'employees.*.cpsNo' => [
                'required',
                'string',
                'min:6',
                'max:10',
            ],
            'employees.*.name' => ['required', 'max:50', 'regex:/^[\p{Tamil}A-Za-z\s]+$/u'],
            'employees.*.designation' => ['required', 'max:100', 'regex:/^[\p{Tamil}A-Za-z\s]+$/u'],
            'employees.*.payscale' => 'required|',
            'employees.*.q1' => 'required|string|in:Y,N',
            'employees.*.details1' => 'required_if:employees.*.q1,N|string|nullable',
            'employees.*.q2' => 'required|string|in:Y,N',
            'employees.*.details2' => 'required_if:employees.*.q2,N|string|nullable',
            'employees.*.score' => 'required|string|in:Y,N',
            'employees.*.transcpsid' => 'nullable|integer',
            // flattened remittance fields
            'employees.*.employeefinancialyear' => 'required|string|max:3',
            'employees.*.employerfinancialyear' => 'required|string|max:3',
            'employees.*.employee' => 'required|numeric',
            'employees.*.employer' => 'required|numeric',
            'employees.*.total' => 'required|numeric',
            'employees.*.actual' => 'required|numeric',
            'employees.*.credited' => 'required|string|in:Y,N',
            'employees.*.credited_remarks' => 'nullable|string',
            'employees.*.shortfall' => 'required|numeric',
            'employees.*.excess' => 'required|numeric',
            'employees.*.remittanceexcess_remarks' => 'nullable|string|max:13',
            'employees.*.shortfalls' => 'sometimes|array',
            'employees.*.shortfalls.*.shortfall_amount' => 'required_if:employees.*.shortfall,>,0|numeric',
            'employees.*.shortfalls.*.shortfall_headofaccount' => [
                'required_if:employees.*.shortfall,>,0',
                'string', 'size:16',
                'regex:/^\d{9}[A-Z]{2}\d{5}$/'
            ],
            'employees.*.shortfalls.*.shortfall_treasury' => 'required_if:employees.*.shortfall,>,0|string|max:100',
            'employees.*.shortfalls.*.shortfall_voucher' => 'required_if:employees.*.shortfall,>,0|string|min:14|max:14',
        ];
        $messages = [
            'auditscheduleid.required' => 'Audit Schedule ID is required.',
            'auditscheduleid.integer' => 'Audit Schedule ID must be a valid number.',
            'instid.required' => 'Institution ID is required.',
            'instid.integer' => 'Institution ID must be a valid number.',
            'employees.required' => 'At least one employee record is required.',
            'employees.array' => 'Employees data must be in valid format.',
            'employees.min' => 'At least one employee must be added.',
            // 'department_name.required' => 'Treasury name is required.',
            // 'department_name.max'      => 'Treasury name must not exceed 50 characters.',
            // 'department_name.regex'    => 'Treasury name must contain only letters and spaces.',
            // 'ddocode_cps.required' => 'DDO Code is required.',
            // 'ddocode_cps.integer'  => 'DDO Code must be a valid number.',
            // 'treasury_cps.required' => 'Treasury name is required.',
            // 'treasury_cps.max'      => 'Treasury name must not exceed 100 characters.',
            // 'treasury_cps.regex'    => 'Treasury name must contain only letters and spaces.',
            // Existing messages...
            'employees.*.shortfalls.*.shortfall_voucher.required_if' => 'Voucher number is required for shortfall.',
            'employees.*.shortfalls.*.shortfall_voucher.min' => 'Voucher number must be at least 14 characters.',
            'employees.*.shortfalls.*.shortfall_voucher.max' => 'Voucher number must not exceed 14 characters.',
            'employees.*.shortfalls.*.shortfall_headofaccount.required_if' => 'Head of account is required for shortfall.',
            'employees.*.shortfalls.*.shortfall_headofaccount.size' => 'Head of account must be exactly 16 characters.',
            'employees.*.shortfalls.*.shortfall_headofaccount.regex' => 'Head of account must be in the format like: 000600101AA21499',
            'employees.*.cpsNo.required' => 'CPS Number is required.',
            'employees.*.name.required' => 'Employee name is required.',
            'employees.*.name.max' => 'Employee name must not exceed 50 characters.',
            'employees.*.name.regex' => 'Employee name must contain only letters and spaces.',
            'employees.*.designation.required' => 'Designation is required.',
            'employees.*.designation.max' => 'Designation must not exceed 100 characters.',
            'employees.*.designation.regex' => 'Designation must contain only letters and spaces.',
            'employees.*.payscale.required' => 'Pay scale is required.',
            'employees.*.payscale.integer' => 'Pay scale must be a valid number.',
            'employees.*.q1.required' => 'Employee Contribution response is required.',
            'employees.*.q2.required' => 'Employer Contribution response is required.',
            'employees.*.score.required' => 'Missing Credits status is required.',
            'employees.*.score.in' => 'Missing Credits must be either Y or N.',
            'employees.*.employee.required' => 'Employee contribution is required.',
            'employees.*.employee.numeric' => 'Employee contribution must be a valid number.',
            'employees.*.employer.required' => 'Employer contribution is required.',
            'employees.*.employer.numeric' => 'Employer contribution must be a valid number.',
            'employees.*.total.required' => 'Total CPS amount is required.',
            'employees.*.total.numeric' => 'Total CPS amount must be a valid number.',
            'employees.*.actual.required' => 'Actual remittance is required.',
            'employees.*.actual.numeric' => 'Actual remittance must be a valid number.',
            'employees.*.credited.required' => 'Credited in appropriate head is required.',
            'employees.*.credited.in' => 'Credited in appropriate head must be either Y or N.',
            'employees.*.shortfall.required' => 'Shortfall amount is required.',
            'employees.*.shortfall.numeric' => 'Shortfall amount must be a valid number.',
            'employees.*.excess.required' => 'Excess amount is required.',
            'employees.*.excess.numeric' => 'Excess amount must be a valid number.',
        ];

        $validator = Validator::make($payload, $rules, $messages);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 'validation_error',
        //         'errors' => $validator->errors()
        //     ], 422);
        // }
        if ($validator->fails()) {
            $errors = [];

            foreach ($validator->errors()->toArray() as $key => $messages) {
                // DDO Code validation
                if (preg_match('/employees\.(\d+)\.ddocode_cps/', $key, $match)) {
                    $employeeNumber = $match[1] + 1;
                    if (
                            isset($payload['employees'][$match[1]]['ddocode_cps']) &&
                            $payload['employees'][$match[1]]['ddocode_cps'] === '00000000'
                        ) {
                            $errors[] = "Employee {$employeeNumber}: DDO Code cannot be 00000000.";
                        } else {
                            $errors[] = "Employee {$employeeNumber}: DDO Code must be exactly 8 digits.";
                        }

                }
                // CPS Number validation
                elseif (preg_match('/employees\.(\d+)\.cpsNo/', $key, $match)) {
                    $employeeNumber = $match[1] + 1;
                    $errors[] = "Employee {$employeeNumber}: CPS Number must be between 6 and 10 digits.";
                } else {
                    $errors[] = $messages[0];
                }
            }

            return response()->json([
                'status' => 'validation_error',
                'errors' => $errors
            ], 422);
        }

        $errors = [];

        foreach ($payload['employees'] as $employee) {
            $this->validateAuditSlip($employee, 'remittanceexcess_remarks', $errors);
            $this->validateAuditSlip($employee, 'credited_remarks', $errors);
        }

        $this->validateShortfallTotalMatch($payload['employees'], $errors);

        if (!empty($errors)) {
            return response()->json([
                'status' => 'validation_error',
                'errors' => $errors
            ], 422);
        }

        $auditScheduleId = $payload['auditscheduleid'];
        $instId = $payload['instid'];

        $cpsGroups = collect($payload['employees'])
            ->groupBy('cpsNo')
            ->filter(fn($group) => $group->count() > 1);

        if ($cpsGroups->isNotEmpty()) {
            $messages = [];

            foreach ($cpsGroups as $cpsNo => $employees) {
                $boldCpsNo = '<strong>' . $cpsNo . '</strong>';
                $names = '<strong>' . collect($employees)->pluck('name')->implode(', ') . '</strong>';

                $messages[] =
                    'CPS No ' . $boldCpsNo
                    . ' already exists for employees ' . $names
                    . '. Please change the CPS number.';
            }

            return response()->json([
                'status' => 'validation_error',
                'errors' => [
                    'employees' => $messages
                ]
            ], 422);
        }

        $incomingIds = collect($payload['employees'])
            ->pluck('transcpsid')
            ->filter()
            ->toArray();

        $incomingShortfallIds = collect($payload['employees'])
            ->pluck('shortfalls.*.shortfallid')
            ->filter()
            ->toArray();

        //  dd($shortfallIds);

        $cps = session('user');
        $userid = $cps->userid;

        // dd($incomingShortfallIds);

        DB::beginTransaction();

        try {
            $existingIds = DB::table('audit.trans_cps')
                ->where('auditscheduleid', $auditScheduleId)
                ->where('instid', $instId)
                ->where('statusflag', 'Y')
                ->pluck('transcpsid')
                ->toArray();

            $removedIds = array_diff($existingIds, $incomingIds);

            if (!empty($removedIds)) {
                DB::table('audit.trans_cps')
                    ->whereIn('transcpsid', $removedIds)
                    ->update([
                        'statusflag' => 'N',
                        'updatedon' => View::shared('get_nowtime'),
                        'updatedby' => $userid,
                    ]);

                // Also deactivate shortfalls linked to removed CPS rows
                DB::table('audit.trans_cps_shortfalls')
                    ->whereIn('transcpsid', $removedIds)
                    ->where('auditscheduleid', $auditScheduleId)
                    ->where('instid', $instId)
                    ->where('statusflag', 'Y')
                    ->update([
                        'statusflag' => 'N',
                        'updatedon' => View::shared('get_nowtime'),
                        'updatedby' => $userid,
                    ]);
            }
            foreach ($payload['employees'] as $employee) {
                $data = [
                    'auditscheduleid' => $auditScheduleId,
                    'instid' => $instId,
                    // 'department_name' => $employee['department_name'],
                    'treasury_cps' => $employee['treasury_cps'],
                    'ddocode_cps' => $employee['ddocode_cps'],
                    'cpsno' => $employee['cpsNo'],
                    // 'cps_year'        => $employee['cps_year'],
                    'cps_month_employee' => json_encode($employee['cps_month_employee']),
                    'cps_month_employer' => json_encode($employee['cps_month_employer']),
                    'emp_name' => $employee['name'],
                    'designation' => $employee['designation'],
                    'payscale' => $employee['payscale'],
                    'employee_cont' => $employee['q1'],
                    'employee_remarks' => $employee['details1'] ?? null,
                    'employer_cont' => $employee['q2'],
                    'employer_remarks' => $employee['details2'] ?? null,
                    'missing_credits' => $employee['score'],
                    // flattened remittance fields
                    'employeefinancialyear' => $employee['employeefinancialyear'],
                    'employerfinancialyear' => $employee['employerfinancialyear'],
                    'total_employee_cont' => $employee['employee'],
                    'total_employer_cont' => $employee['employer'],
                    'total_cps' => $employee['total'],
                    'actual_remittance' => $employee['actual'],
                    'creditedin_appropriatehead' => $employee['credited'],
                    'approproatehead_remarks' => $employee['credited_remarks'] ?? null,
                    'shortfall_remittance' => $employee['shortfall'],
                    'remittanceexcess_cps' => $employee['excess'],
                    'remittanceexcess_remarks' => $employee['remittanceexcess_remarks'],
                    'statusflag' => 'Y',
                    'updatedon' => View::shared('get_nowtime'),
                    'updatedby' => $userid,
                    'createdon' => View::shared('get_nowtime'),
                    'createdby' => $userid,
                ];

                if (!empty($employee['transcpsid'])) {
                    // Update existing
                    DB::table('audit.trans_cps')
                        ->where('transcpsid', $employee['transcpsid'])
                        ->update($data);
                    $transcpsId = $employee['transcpsid'];
                } else {
                    // Insert new
                    // DB::table('audit.trans_cps')->insert($data);
                    $transcpsId = DB::table('audit.trans_cps')->insertGetId($data, 'transcpsid');
                }

                $existingShortfallIds = DB::table('audit.trans_cps_shortfalls')
                    ->where('transcpsid', $transcpsId)
                    ->where('auditscheduleid', $auditScheduleId)
                    ->where('instid', $instId)
                    ->where('statusflag', 'Y')
                    ->pluck('shortfallid')
                    ->toArray();

                // dd($existingShortfallIds);

                $incomingShortfallIds = [];

                if (!empty($employee['shortfalls']) && is_array($employee['shortfalls'])) {
                    foreach ($employee['shortfalls'] as $sf) {
                        $data = [
                            'transcpsid' => $transcpsId,
                            'auditscheduleid' => $auditScheduleId,
                            'instid' => $instId,
                            'shortfall_month' => $sf['shortfall_month'] ?? null,
                            'shortfall_amount' => $sf['shortfall_amount'] ?? 0,
                            'shortfall_headofaccount' => $sf['shortfall_headofaccount'] ?? null,
                            'shortfall_voucher' => $sf['shortfall_voucher'] ?? null,
                            'shortfall_treasury' => $sf['shortfall_treasury'] ?? null,
                            'shortfall_total' => $sf['shortfall_total'] ?? null,
                            'updatedon' => View::shared('get_nowtime'),
                            'updatedby' => $userid,
                            'statusflag' => 'Y',
                        ];

                        // 🔹 UPDATE
                        if (!empty($sf['shortfallid'])) {
                            DB::table('audit.trans_cps_shortfalls')
                                ->where('shortfallid', $sf['shortfallid'])
                                ->update($data);

                            $incomingShortfallIds[] = (int) $sf['shortfallid'];
                        }
                        // 🔹 INSERT
                        else {
                            $data['createdon'] = View::shared('get_nowtime');
                            $data['createdby'] = $userid;

                            $newId = DB::table('audit.trans_cps_shortfalls')
                                ->insertGetId($data, 'shortfallid');

                            $incomingShortfallIds[] = (int) $newId;
                        }
                    }

                    // 3️⃣ Mark removed shortfalls as inactive
                    $existingShortfallIds = array_map('intval', $existingShortfallIds);
                    $incomingShortfallIds = array_filter(
                        array_map('intval', $incomingShortfallIds),
                        fn($id) => $id > 0
                    );

                    $removedShortfallIds = array_values(array_diff($existingShortfallIds, $incomingShortfallIds));

                    if (!empty($removedShortfallIds)) {
                        DB::table('audit.trans_cps_shortfalls')
                            ->whereIn('shortfallid', $removedShortfallIds)
                            ->update([
                                'statusflag' => 'N',
                                'updatedon' => View::shared('get_nowtime'),
                                'updatedby' => $userid,
                            ]);
                    }
                }
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'CPS details saved successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save CPS details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cpsFetch(Request $request)
    {
        $auditScheduleId = Crypt::decryptString($request->auditscheduleid);
        $instId = Crypt::decryptString($request->instid);

        $cpsData = DB::table('audit.trans_cps as cps')
            ->join('audit.inst_auditschedule as sch', 'sch.auditscheduleid', '=', 'cps.auditscheduleid')
            ->select('cps.*', 'sch.cps_completed', 'sch.exitmeetdate')
            ->where('cps.auditscheduleid', $auditScheduleId)
            ->where('cps.instid', $instId)
            ->where('cps.statusflag', 'Y')
            ->orderBy('cps.transcpsid')
            ->get();

        $transCpsIds = $cpsData->pluck('transcpsid')->toArray();

        $shortfalls = DB::table('audit.trans_cps_shortfalls')
            ->whereIn('transcpsid', $transCpsIds)
            ->where('statusflag', 'Y')
            ->orderBy('shortfallid')
            ->get()
            ->groupBy('transcpsid');

        //  dd($shortfalls);

        $result = $cpsData->map(function ($cps) use ($shortfalls) {
            $cps->shortfalls = $shortfalls[$cps->transcpsid] ?? [];
            return $cps;
        });

        return response()->json($result);
    }

    public function finalizeCps(Request $request)
    {
        $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
        $instId = Crypt::decryptString($request->instid);

        if (!$auditscheduleid) {
            return response()->json(['success' => false, 'message' => 'Audit schedule not found']);
        }

        DB::table('audit.inst_auditschedule')
            ->where('auditscheduleid', $auditscheduleid)
            ->update(['cps_completed' => 'Y']);

        return response()->json(['success' => true]);
    }

    public function check_entrymeetprerequisite(Request $request)
    {
        try {
            // Validate the input

            $auditscheduleid = Crypt::decryptString($request->scheduleId);

            // Call the model function
            $result = FieldAuditModel::check_entrymeetprerequisite($auditscheduleid);

            $jsonString = $result[0]->response;

            // $data = json_decode($jsonString, true);

            $data = json_decode($result[0]->response, true);

            $leavedetails = 1;

            if ($data['leavedetails']['auditscheduleid'] == null && $data['leavedetails']['username'] == null)
                $leavedetails = 0;

            if ($data['inanotherschedulerequest'] == 'Y') {
                return response()->json([
                    'status' => 'error',
                    'alreadyhasrequest' => 'Y',
                    'message' => 'Already Schedule request sent For another institution',
                    'data' => $leavedetails
                ]);
            }

            if ($leavedetails == 0) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'worlallc_completed',
                    'alreadyhasrequest' => 'N',
                    'data' => $result
                ]);
            } else if ($leavedetails > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'In this schedule users are in leave',
                    'alreadyhasrequest' => 'N',
                    'data' => $leavedetails
                ]);
            }

            // Step 3: Extract status and message
            $status = $data['status'] ?? null;
            $message = $data['message'] ?? null;
            // return $status;
            if ($status == 'error') {
                return response()->json([
                    'status' => 'error',
                    'message' => $message
                ]);
            } else {
                return response()->json([
                    'status' => $status,
                    'message' => 'worlallc_completed',
                    'data' => $result
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log::error('Validation Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            // Log::error('Automation Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to automate work allocation. Please try again later.'
            ], 500);
        }
    }

public function getspilloverslipdetails(Request $request)
{
try {
// ✅ Validate request
if (!$request->has('lastauditplanid')) {
return response()->json([
'status' => false,
'message' => 'Missing lastauditplanid'
], 400);
}

// ✅ Decrypt safely
try {
$lastauditplanid = Crypt::decryptString($request->lastauditplanid);
//$lastauditplanid = $request->lastauditplanid;
} catch (\Exception $e) {
return response()->json([
'status' => false,
'message' => 'Invalid or tampered ID'
], 400);
}



// ✅ Fetch data
$result = FieldAuditModel::getspilloverslipdetails($lastauditplanid);

// ✅ Check empty result
if ($result->isEmpty()) {
return response()->json([
'status' => true,
'data' => [],
'message' => 'No records found'
]);
}

// ✅ Encrypt IDs safely
foreach ($result as $row) {
if (!empty($row->auditslipid)) {
$row->auditslipid = Crypt::encryptString($row->auditslipid);
}
}

// ✅ Success response
return response()->json([
'status' => true,
'data' => $result
]);
} catch (\Exception $e) {
// ✅ Log error (very important for production)
Log::error('Spillover Slip Details Error', [
'message' => $e->getMessage(),
'line' => $e->getLine(),
'file' => $e->getFile()
]);

// ❌ Generic error to user (don’t expose internal details)
return response()->json([
'status' => false,
'message' => 'Something went wrong. Please try again later.'
], 500);
}
}

public function getspilloverdel_compact($auditplanid, Request $request)
{
$lastauditplanid = $auditplanid;

//$lastauditplanid = Crypt::decryptString($auditplanid);
//echo $lastauditplanid;


return view('fieldaudit.spilloverdetails', compact('lastauditplanid'));
}
}
