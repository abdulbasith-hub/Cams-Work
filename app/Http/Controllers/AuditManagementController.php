<?php

namespace App\Http\Controllers;

use App\Models\AuditeeModel;
use App\Models\AuditManagementModel;
use App\Models\AuditModel;
use App\Models\AuditPeriodModel;
use App\Models\AuditQuarterModel;
use App\Models\AuditSubcategoryModel;
use App\Models\AuditTeamModel;
use App\Models\BaseModel;
use App\Models\CommonModel;
use App\Models\DeptMapModel;
use App\Models\DeptModel;
use App\Models\DistrictModel;
use App\Models\InstituteCategoryModel;
use App\Models\MajorWorkAllocationtypeModel;
use App\Models\RegionModel;
use App\Models\SmsmailModel;
use App\Models\TypeofAuditModel;
use App\Models\YearcodeMapping;
use App\Services\PHPMailerService;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;
use DataTables;
use Exception;

class AuditManagementController extends Controller
{
    protected static $deptartment_table = BaseModel::DEPARTMENT_TABLE;
    protected static $institution_table = BaseModel::INSTITUTION_TABLE;
    protected static $auditplan_table = BaseModel::AUDITPLAN_TABLE;
    protected static $temprankusers_table = BaseModel::TEMPRANKUSERS_TABLE;
    protected static $designation_table = BaseModel::DESIGNATION_TABLE;
    protected static $userdetail_table = BaseModel::USERDETAIL_TABLE;
    protected static $auditplanteam_table = BaseModel::AUDITPLANTEAM_TABLE;
    protected static $auditplanteammem_table = BaseModel::AUDITPLANTEAMMEM_TABLE;
    protected static $typeofaudit_table = BaseModel::TYPEOFAUDIT_TABLE;
    protected static $mstauditeeinscategory_table = BaseModel::MSTAUDITEEINSCATEGORY_TABLE;
    protected static $instauditschedule_table = BaseModel::INSTSCHEDULE_TABLE;
    protected static $instauditschedulemem_table = BaseModel::INSTSCHEDULEMEM_TABLE;
    protected static $dist_table = BaseModel::DIST_Table;
    protected static $auditquarter_table = BaseModel::AUDITQUARTER_TABLE;
    protected static $subcategory_table = BaseModel::SUBCATEGORY_TABLE;

    public function create_userdet()
    {
        $session = session('charge');

        $distcode = $session->distcode;
        $deptcode = $session->deptcode;
        $dist_det = DB::table(self::$dist_table)
            ->where('distcode', $distcode)
            ->first();
        $dept_det = DB::table(self::$deptartment_table)
            ->where('deptcode', $deptcode)
            ->first();
        $quarter_det = AuditManagementModel::getquarterDet($deptcode);
        // ->where('deptcode', $deptcode)
        // ->orderBy('auditquartercode', 'asc')
        // ->get();

        return view('audit.auditplanning', compact('dist_det', 'dept_det', 'quarter_det'));
    }

    public function fetchall_automatedata(Request $request)
    {
        $session = session('charge');

        $distcode = $session->distcode;
        $deptcode = $session->deptcode;
        // $regioncode = $session->regioncode;
        // $checkparam = $request->checkparam;

        // $request->validate([
        //     'checkparam'     => 'required|string',

        // ]);
        //  return $checkparam;
        $data = [
            'distcode' => $distcode,
            'deptcode' => $deptcode,
            // 'regioncode'    => $regioncode,
            // 'checkparam'    => $checkparam,
        ];
        // return $data;
        // return  $distcode;
        try {
            $audit_plan_status = AuditManagementModel::getUser_planStatus($data);

            // $audit_plan_status = AuditManagementModel::getAuditPlanStatus($checkdata);
            $plan_status = $audit_plan_status[0]->autoplanstatus;
            $user_status = $audit_plan_status[0]->userverified;

            if ($user_status == 'N' || $user_status == '' || empty($user_status)) {
                $details = AuditManagementModel::getAuditorUser($data);
            } else if ($user_status == 'Y' && $plan_status == 'Y') {
                $details = AuditManagementModel::getAuditors($deptcode, $distcode);
                //  return  $details;
            } else if ($user_status == 'Y' && $plan_status == 'F') {
                $details = AuditManagementModel::getAuditorsfromplan($deptcode, $distcode);
                //  return  $details;
            } else if ($user_status == 'Y' && ($plan_status == 'N' || $plan_status == '')) {
                $details = AuditManagementModel::getAuditors($deptcode, $distcode);
            }
            return response()->json(['success' => 'Users are fetched Successfully', 'audit_plan_status' => $audit_plan_status, 'planned_auditors' => $details]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching data'], 500);
        }
    }

    public function checkfordetails(Request $request)
    {
        try {
            $rules = [
                'distcode' => 'required|string|regex:/^\d+$/',
                'deptcode' => 'required|string|regex:/^\d+$/',
                'quarter_code' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }
            $distcode = $request->input('distcode');
            $deptcode = $request->input('deptcode');
            $auditquartercode = $request->input('quarter_code');
            // $auditquartercode = 'Q4';
            $data = [
                'distcode' => $distcode,
                'deptcode' => $deptcode,
                'auditquartercode' => $auditquartercode,
            ];

            $check = AuditManagementModel::checkfordetails($data);
            $status = $check[0]->readyforautomateplan;

            list($statusCode, $statusMessage) = explode(': ', $status, 2);

            // Trim values to remove extra spaces
            $statusCode = trim($statusCode);
            $statusMessage = trim($statusMessage);

            if ($statusCode == 'Error') {
                return response()->json(['error' => $statusMessage], 500);
            } elseif ($statusCode == 'Success') {
                $audit_plan_status = AuditManagementModel::getUser_planStatus($data);

                return response()->json(['success' => $statusMessage, 'audit_plan_status' => $audit_plan_status]);
            } else {
                return response()->json(['error' => 'An error occurred while fetching data'], 500);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
        // $deptocode
    }

    public function finalize_data(Request $request)
    {
        try {
            $rules = [
                'distcode' => 'required|string|regex:/^\d+$/',
                'deptcode' => 'required|string|regex:/^\d+$/',
                // 'quarter_code'  => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }
            $distcode = $request->input('distcode');
            $deptcode = $request->input('deptcode');
            $auditquartercode = $request->input('quarter_code');

            $session = session('charge');

            // $distcode = $session->distcode;
            // $deptcode = $session->deptcode;
            // $regioncode = $session->regioncode;

            // $finaliseFlag == 'P';

            // $audit_plan = AuditManagementModel::getAuditPlanStatus($deptcode, $distcode, $regioncode);
            // $finaliseFlag = $audit_plan[0]->finaliseflag;

            // if ($finaliseFlag == 'F') {
            //     return response()->json(['error' => 'Audit Planning was already finalised'], 500);
            // }
            $auditors = AuditManagementModel::finalize_plan($deptcode, $distcode, $auditquartercode);

            $distributeplan_response_json = $auditors[0]->distributeauditteamplan;
            $distributeplan_response = json_decode($distributeplan_response_json, true);
            $distributeplan_status = $distributeplan_response['status'];
            //  return $distributeplan_status;
            if ($distributeplan_status == 'error') {
                return response()->json(['error' => $distributeplan_response['message']], 500);
            } else {
                $auditors_detail = AuditManagementModel::getAuditors($deptcode, $distcode);
                $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
                $sentsms = $auditModel->sendscheduleInstitutes($distcode, $deptcode, $auditquartercode);

                return response()->json(['success' => 'success_finalise', 'auditors' => $auditors_detail]);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function automate_plan(Request $request)
    {
        try {
            $rules = [
                'distcode' => 'required|string|regex:/^\d+$/',
                'deptcode' => 'required|string|regex:/^\d+$/',
                'quarter_code' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $distcode = $request->input('distcode');
            $deptcode = $request->input('deptcode');
            $auditquartercode = $request->input('quarter_code');

            $auditors = AuditManagementModel::automate_plan($deptcode, $distcode, $auditquartercode);

            $autoplan_status = $auditors[0]->status;
            // return $autoplan_status;
            if ($autoplan_status == 'Error') {
                return response()->json(['error' => $auditors[0]->msg], 500);
            } else {
                $auditors_detail = AuditManagementModel::getAuditors($deptcode, $distcode);
                return response()->json(['success' => 'success_automate', 'auditors' => $auditors_detail]);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function storeOrUpdateAudit(Request $request, $userId = null)
    {
        \Log::info($request->all());

        // Check if the statusflag is 'delete'
        $isDelete = $request->has('statusflag') && $request->statusflag === 'Y';
        $yeararr = [];

        // Conditional validation: Only validate required fields if not deleting
        if ($isDelete == 1) {
            $request->validate([
                'statusflag' => 'required|string',
                'auditplanid' => 'nullable|int',
            ]);
        } else {
            $request->validate([
                'instcatcode' => 'required|string|max:2',
                'instcode' => 'required|string|max:4',
                'auditteamcode' => 'required|string|max:2',
                'yearcode' => 'required|string|max:2',
                'auditcode' => 'required|string|max:2',
                'periodcode' => 'required|string|max:2',
                'statusflag' => 'nullable|string|max:2',
            ]);

            $isFinalize = $request->has('finalize') && $request->finalize === 'F';

            if ($isFinalize) {
                $FinalizedStsFlag = $request->finalize;
            } else {
                $FinalizedStsFlag = 'Y';
            }
            /* YearMultiple Array */
            $yeararr = $request->input('yearselected');

            $auditplanData = [
                'instid' => $request->instcode,
                'auditteamid' => $request->auditteamcode,
                'typeofauditcode' => $request->auditcode,
                'auditperiodid' => $request->yearcode,  // Insert the generated yearcodemapping_id here
                'auditquartercode' => $request->periodcode,
                'statusflag' => $FinalizedStsFlag
            ];

            if ((isset($_POST['auditplanid']) && $_POST['auditplanid'] != '') || $isFinalize == 'F')
                $userId = $request->input('auditplanid');
            else
                $userId = null;
        }

        try {
            if ($request->has('statusflag') && $request->statusflag === 'Y') {
                $auditplanid = Crypt::decryptString($request->auditencryptedplanid);
                $request->merge(['auditplanid' => $auditplanid]);
                $auditId = $request->auditplanid;
                $Audit = AuditModel::find($auditId);
                $auditplanData['statusflag'] = $request->statusflag;

                if ($Audit) {
                    // If the user exists, delete the record
                    $Audit = AuditModel::createIfNotExistsOrUpdate($auditplanData, $auditId, $yeararr, 'Delete');
                    return response()->json(['success' => 'Details deleted successfully.']);
                } else {
                    // If no such record exists to delete
                    return response()->json(['error' => 'Details not found.'], 404);
                }
            } else {
                // Pass the current user ID (if available) for the update or create logic
                $user = AuditModel::createIfNotExistsOrUpdate($auditplanData, $userId, $yeararr);
                if (!$user) {
                    // If user already exists (based on conditions), return an error
                    return response()->json(['error' => 'Details already exists'], 400);
                }

                // Return success message
                return response()->json(['success' => 'Audit Plan Data Saved successfully', 'user' => $user]);
            }
        } catch (QueryException $e) {
            // Handle database exceptions (e.g., duplicate entry)
            return response()->json(['error' => 'Database error occurred: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Fetch user data for editing.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchUserDataAudit(Request $request)
    {
        // Retrieve deptuserid from the request
        $auditplanid = Crypt::decryptString($request->auditplanid);
        $request->merge(['auditplanid' => $auditplanid]);

        $request->validate([
            'auditplanid' => 'required|integer'
        ], [
            'required' => 'The :attribute field is required.',
            'integer' => 'The :attribute field must be a valid number.'
        ]);

        // Ensure deptuserid is provided
        if (!$auditplanid) {
            return response()->json(['success' => false, 'message' => 'Audit ID not provided'], 400);
        }

        // Fetch user data based on deptuserid
        $auditplanNew = AuditModel::where('auditplanid', $auditplanid)->first();  // Adjust query as needed

        // Get Institute Name
        /*$GetInstitute = DeptMapModel::where('statusflag', '=', 'Y')
                                     ->where('instid', $auditplan->instid)
                                     ->first();*/

        $auditplan = AuditModel::query()
            ->join('audit.mst_institution as inst', 'auditplan.instid', '=', 'inst.instid')
            ->where('inst.statusflag', '=', 'Y')
            ->where('inst.instid', '=', $auditplanNew->instid)
            ->where('auditplan.auditplanid', '=', $auditplanid)
            ->first();
        $auditplan->typeofauditcode = $auditplanNew->typeofauditcode;
        $Yearmapping = YearcodeMapping::fetchYearmapById($auditplanid);
        foreach ($Yearmapping as $yeararr => $yearval) {
            $yearsGet[$yeararr] = $yearval->yearselected;
        }
        $auditplan['yearcode'] = $yearsGet;
        // $auditplan['yearcode']='2024 -2025';
        if ($auditplan) {
            return response()->json(['success' => true, 'data' => $auditplan]);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        }
    }

    public function auditfetchAllData()
    {
        // Fetch all users
        $audits = AuditModel::fetchAllusers();

        $Slno = 1;
        foreach ($audits as $audit) {
            $Arr['RegionName'] = $audit->regionename;
            $Arr['DistName'] = $audit->distename;
            $ImplodeReg_Dist = implode('<br>', $Arr);

            $audit->Reg_Dist = $ImplodeReg_Dist;
            $audit->deptname = $audit->deptesname;
            $audit->instcatname = $audit->catename;
            $audit->instname = $audit->instename;
            $audit->auditteamname = $audit->teamname;
            $audit->typeofaudit = $audit->typeofauditename;

            $audit->encrypted_auditid = Crypt::encryptString($audit->auditplanid);

            // $Yearmapping = YearcodeMapping::fetchYearmapById($audit->auditplanid);

            // $audits = AuditPeriodModel::where('statusflag', '=', 'Y')->get();
            // if ($AuditPeriods->isNotEmpty()) {
            //     // Determine the minimum and maximum years
            //     $auditfromperiod = $AuditPeriods->min('fromyear'); // Earliest fromyear
            //     $audittoperiod = $AuditPeriods->max('toyear');     // Latest toyear
            // }
            //     $YearMasterArr = [];
            //     $index = 1;

            //     // Generate year ranges
            //     for ($year = $audittoperiod; $year >= $auditfromperiod; $year--) {
            //         $nextYear = $year + 1;
            //         $YearMasterArr[$index] = "$year-$nextYear";
            //         $index++;
            //     }
            //  }

            // foreach($Yearmapping as $yeararr => $yearval)
            // {
            //    $yearsGet[$yeararr]= $YearMasterArr[$yearval->yearselected];
            // }
            // $implodeArrYrs=implode('<br>',$yearsGet);
            // $audit->auditperiod = $implodeArrYrs;

            // $audit->auditquarter = $audit->auditquarter;

            // $audit->Slno = $Slno;
            // $Slno++;
        }
        // Return data in JSON format
        return response()->json(['data' => $audits]);  // Ensure the data is wrapped under "data"
    }

    public function FilterByDept(Request $request)
    {
        $DeptMapping = DeptMapModel::where('statusflag', '=', 'Y')
            ->where('deptcode', $request->deptcode);

        if ($request->regioncode) {
            $DeptMapping->where('regioncode', $request->regioncode);
        }

        if ($request->distcode) {
            $DeptMapping->where('distcode', $request->distcode);
        }

        if ($request->instcatcode) {
            $DeptMapping->where('catcode', $request->instcatcode);
        }

        if ($request->instsubcatcode) {
            $DeptMapping->where('subcatid', $request->instsubcatcode);
        }

        $DeptMapping = $DeptMapping->get();

        $regioncode = [];
        $districtCode = [];
        $InstcatCode = [];
        // print_r($DeptMapping);
        foreach ($DeptMapping as $Deptkey => $DeptVal) {
            $regioncode[] = $DeptVal->regioncode;
            $districtCode[] = $DeptVal->distcode;
            $InstcatCode[] = $DeptVal->catcode;
        }
        if (sizeof($regioncode) > 0) {
            $regioncode = array_unique($regioncode);
        }

        if ($request->regioncode) {
            $districtCode = array_unique($districtCode);
        }

        if ($request->distcode) {
            $InstcatCode = array_unique($InstcatCode);

            $auditteammodalget = AuditTeamModel::where('statusflag', '=', 'F')
                ->where('deptcode', $request->deptcode)
                ->whereIn('distcode', [$request->distcode, 'A'])
                ->get();
        }

        $RegionFinal = '';
        $DistrictFinal = '';
        $InstCategoryFinal = '';
        $InstNameFinal = '';
        $TypeofAuditFinal = '';
        $AuditQuarterFinal = '';
        $AuditPeriodFinal = '';

        if ($DeptMapping) {
            if ($request->instsubcatcode) {
                $InstNameFinal = self::ArrayCombineFunction($DeptMapping, 'instid', 'instename');
                return $InstNameFinal;
            }

            if ($request->instcatcode) {
                $DeptMappingfetch = DeptMapModel::where('statusflag', '=', 'Y')
                    ->where('deptcode', $request->deptcode)
                    ->where('catcode', $request->instcatcode)
                    ->first();

                if ($DeptMappingfetch->subcatid != '') {
                    $AuditSubcategoryModel = AuditSubcategoryModel::where('statusflag', '=', 'Y')
                        ->where('catcode', $request->instcatcode)
                        ->where('auditeeins_subcategoryid', $DeptMappingfetch->subcatid)
                        ->get();
                    $DynField = 'subcategory';
                    $InstSubCategoryFinal = self::ArrayCombineFunction($AuditSubcategoryModel, 'auditeeins_subcategoryid', 'subcatename');
                    $response = $DynField . '~~' . $InstSubCategoryFinal;
                    return $response;
                } else {
                    $InstNameFinal = self::ArrayCombineFunction($DeptMapping, 'instid', 'instename');

                    $DynField = 'institutename';
                    $response = $DynField . '~~' . $InstNameFinal;
                    return $response;
                }
            }

            if ($request->distcode) {
                $InstCategory = InstituteCategoryModel::where('statusflag', '=', 'Y')
                    ->whereIn('catcode', $InstcatCode)
                    ->get();

                $InstCategoryFinal = self::ArrayCombineFunction($InstCategory, 'catcode', 'catename');
                $AuditTeam = self::ArrayCombineFunction($auditteammodalget, 'auditplanteamid', 'teamname');

                return $InstCategoryFinal . '~' . $AuditTeam;
            }

            if ($request->regioncode) {
                $district = DistrictModel::where('statusflag', '=', 'Y')
                    ->whereIn('distcode', $districtCode)
                    ->get();
                $DistrictFinal = self::ArrayCombineFunction($district, 'distcode', 'distename');
                return $DistrictFinal;
            }

            $region = RegionModel::where('statusflag', '=', 'Y')
                ->whereIn('regioncode', $regioncode)
                ->get();
            $RegionFinal = self::ArrayCombineFunction($region, 'regioncode', 'regionename');

            $AuditQuarter = AuditQuarterModel::where('statusflag', '=', 'Y')
                ->where('deptcode', $request->deptcode)
                ->get();

            $AuditPeriod = AuditPeriodModel::where('statusflag', '=', 'Y')
                ->first();

            $TypeofAudit = TypeofAuditModel::where('statusflag', '=', 'Y')
                ->where('deptcode', $request->deptcode)
                ->get();

            $auditteammodalget = AuditTeamModel::where('statusflag', '=', 'F')
                ->where('deptcode', $request->deptcode)
                ->get();

            $auditfromperiod = $AuditPeriod->fromyear;
            $audittoperiod = $AuditPeriod->toyear;

            $AuditPeriodFinal = $auditfromperiod . ' - ' . $audittoperiod;
            $AuditQuarterFinal = self::ArrayCombineFunction($AuditQuarter, 'auditquartercode', 'auditquarter');
            $TypeofAuditFinal = self::ArrayCombineFunction($TypeofAudit, 'typeofauditcode', 'typeofauditename');

            return $RegionFinal . '~' . $AuditPeriodFinal . '~' . $AuditQuarterFinal . '~' . $TypeofAuditFinal;
        }
    }

    public function ArrayCombineFunction($ARR, $aaa, $bbb)
    {
        $Final = [];
        $Id = [];
        $Name = [];

        if ($ARR) {
            foreach ($ARR as $ArrVal) {
                $Id[] = $ArrVal->$aaa;
                $Name[] = $ArrVal->$bbb;
            }

            if (sizeof($Id) > 0 && sizeof($Name) > 0) {
                $Final = array_combine($Id, $Name);
            }
        }

        return json_encode($Final);
    }

    public function creatuser_dropdownvalues($view)
    {
        $dept = DeptModel::where('statusflag', '=', 'Y')
            ->orderBy('orderid', 'asc')
            ->get();

        $district = DistrictModel::where('statusflag', '=', 'Y')
            ->get();

        $region = RegionModel::where('statusflag', '=', 'Y')
            ->get();

        return view($view, compact('dept', 'district', 'region'));  // Using 'district' to pass it to the view
    }

    public function audit_plandetails(Request $request)
    {
        try {
              $planmappingid = $request->planmappingid;
            // $quartercode = $request->quartercode;
            $session = $request->session();
            if ($session->has('user')) {
                $user = $session->get('user');
                $userid = $user->userid ?? null;
            } else {
                return 'No user found in session.';
            }
            $audit_plandetail = AuditManagementModel::fetch_auditplandetails($userid, $planmappingid);
            foreach ($audit_plandetail as $item) {
                $item->encrypted_auditplanid = Crypt::encryptString($item->auditplanid);
                $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
                $item->encrypted_instid = Crypt::encryptString($item->instid);
            }

            return response()->json(['data' => $audit_plandetail]);  // Ensure the data is wrapped under "data"
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    // public function creatauditschedule_dropdownvalues(Request $request)
    // {
    //     // $auditplanid = $request->query('auditplanid'); // Default to '1' if no value is provided.
    //     if ($request->auditplanid) {
    //         $auditplanid = Crypt::decryptString($request->auditplanid);
    //         $userid = $request->userid;
    //     } else {
    //         // print_r($auditplanid);
    //         $session = $request->session();
    //         if ($session->has('user')) {
    //             $user = $session->get('user');
    //             $userid = $user->userid ?? null;
    //         } else {
    //             return 'No user found in session.';
    //         }
    //     }

    //     // echo $auditplanid;

    //     // echo $userid;
    //     // Fetch the data based on the provided auditplanid
    //     $inst = AuditManagementModel::auditplandet($auditplanid, $userid);

    //     $auditmode = $inst->first()->auditmode;

    //     if ($auditmode == view::shared('performance_audit')) {
    //         $prfaudit_det = AuditManagementModel::get_prfauditDetails($auditplanid);
    //     } else {
    //         $prfaudit_det = collect();
    //     }
    //     $catcode = $inst->first()->catcode;
    //     $deptcode = $inst->first()->deptcode;
    //     $subcatid = $inst->first()->subcatid;
    //     $planquartercode = $inst->first()->auditquartercode;  // fetch from plandet

    //     $fetchcurrquarter = AuditManagementModel::getCurrentQuarter($deptcode, $planquartercode);
    //     //  $str_Quarter = "Q2";
    //     $str_Quarter = $fetchcurrquarter->quarterfrom;
    //     $str_Quarter = date('Y-m-01', strtotime($str_Quarter));

    //     $end_Quarter = $fetchcurrquarter->quarterto;
    //     $end_Quarter = date('Y-m-t', strtotime($end_Quarter));

    //     $Quarter = ['fromquarter' => $str_Quarter, 'toquarter' => $end_Quarter];

    //     $Accountparticulars = self::audit_particulars($catcode, $deptcode, $subcatid);
    //     $quartercode = $inst->first()->auditquartercode;
    //     $schdel = DB::table('audit.inst_auditschedule')
    //         ->where('auditplanid', '=', $inst->first()->auditplanid)
    //         ->get();

    //     if (count($schdel) > 0) {
    //         $rcno = $schdel->first()->rcno;
    //     } else {
    //         $deptdel = DB::table('audit.mst_dept')
    //             // ->where('auditplanid', '=', $inst->first()->auditplanid)
    //             ->where('deptcode', '=', $deptcode)
    //             ->get();

    //         if ($deptdel->isNotEmpty()) {
    //             // Ensure there's a valid first item before accessing its properties
    //             $firstItem = $deptdel->first();

    //             if ($firstItem) {
    //                 $yearSuffix = date('y');
    //                 // Now safely access properties on the first item
    //                 $rcnocount = $firstItem->rcno;
    //                 $deptsname = $firstItem->deptesname;
    //                 $deptfirstcharacter = substr($deptsname, 0, 1);  // Corrected the typo

    //                 // Increment the count, and ensure it's padded with leading zeros
    //                 $incrementcount = $rcnocount ? $rcnocount + 1 : 1;

    //                 // Pad the increment count with leading zeros to make it 4 digits
    //                 $incrementcount = str_pad($incrementcount, 4, '0', STR_PAD_LEFT);

    //                 // Concatenate the values
    //                 $rcno = $deptfirstcharacter . $yearSuffix . $quartercode . $incrementcount;
    //             }
    //         }
    //     }

    //     $auditperiod = AuditPeriodModel::select('auditperiodid', DB::raw("CONCAT(fromyear, ' - ', toyear) AS audit_period"))
    //         ->where('deptcode', $deptcode)
    //         ->where('statusflag', 'Y')
    //         ->where('financestatus', 'N')
    //         ->whereIn('lagacyyear', ['N', 'B'])
    //         ->orderBy('fromyear', 'desc')
    //         ->get();

    //     $annadhanamperiod = AuditPeriodModel::select('auditperiodid', DB::raw("CONCAT(fromyear, ' - ', toyear) AS audit_period"))
    //         ->where('deptcode', $deptcode)
    //         ->where('statusflag', 'Y')
    //         ->where('financestatus', 'Y')
    //         ->whereIn('lagacyyear', ['N', 'B'])
    //         ->orderBy('fromyear', 'desc')
    //         ->get();

    //     $DraftStatus['auditschid'] = '';
    //     $DraftStatus['exists'] = 'N';

    //     $hasexists = DB::table('audit.inst_auditschedule')
    //         ->where('auditplanid', $auditplanid)
    //         ->where('statusflag', 'Y')
    //         ->exists();

    //     if ($hasexists) {
    //         $schedules = DB::table('audit.inst_auditschedule')
    //             ->select('auditscheduleid')
    //             ->where('auditplanid', $auditplanid)
    //             ->where('statusflag', 'Y')
    //             ->first();

    //         $DraftStatus['auditschid'] = $schedules->auditscheduleid;
    //         $DraftStatus['exists'] = 'Y';
    //     }

    //     // Redirect to the view and pass the data using compact
    //     return view('audit.auditdatefixing', compact('inst', 'Accountparticulars', 'rcno', 'auditperiod', 'annadhanamperiod', 'Quarter', 'DraftStatus', 'prfaudit_det'));
    // }

 public function creatauditschedule_dropdownvalues(Request $request)
    {

        if ($request->auditplanid) {
            $auditplanid = Crypt::decryptString($request->auditplanid);
            $userid = $request->userid;
        } else {
            $session = $request->session();
            if ($session->has('user')) {
                $user = $session->get('user');
                $userid = $user->userid ?? null;
            } else {
                return "No user found in session.";
            }
        }

        $inst =   AuditManagementModel::auditplandet($auditplanid, $userid);

        $auditmode          = $inst->first()->auditmode;
        $catcode            = $inst->first()->catcode;
        $deptcode           = $inst->first()->deptcode;
        $subcatid           = $inst->first()->subcatid;
        $planquartercode    = $inst->first()->auditquartercode;
        $planmappingid      = $inst->first()->planmappingid;
        $isScheduled        = $inst->first()->isscheduled;
        $quartercode        = $inst->first()->auditquartercode;
        $rcno               = $inst->first()->rcno ?? null;
        $auditscheduleid    = $inst->first()->auditscheduleid ?? null;


        if ($auditmode == view::shared('performance_audit')) {
            $prfaudit_det = AuditManagementModel::get_prfauditDetails($auditplanid);
        } else {
            $prfaudit_det = collect();
        }


        $str_Quarter =  $inst->first()->fromdate;
        $str_Quarter = date('Y-m-01', strtotime($str_Quarter));

        $end_Quarter =  $inst->first()->todate;
        $end_Quarter = date('Y-m-t', strtotime($end_Quarter));

        $Quarter = ['fromquarter' => $str_Quarter, 'toquarter' => $end_Quarter];

        $Accountparticulars = self::audit_particulars($catcode, $deptcode, $subcatid);


        if ($isScheduled == 'N') {

            $yearSuffix = date('y');
            // Now safely access properties on the first item
            $rcnocount = $inst->first()->dept_rcno;
            $deptsname = $inst->first()->deptesname;
            $deptfirstcharacter = substr($deptsname, 0, 1);  // Corrected the typo

            // Increment the count, and ensure it's padded with leading zeros
            $incrementcount = $rcnocount ? $rcnocount + 1 : 1;

            // Pad the increment count with leading zeros to make it 4 digits
            $incrementcount = str_pad($incrementcount, 4, '0', STR_PAD_LEFT);

            // Concatenate the values
            $rcno = $deptfirstcharacter . $yearSuffix . $quartercode . $incrementcount;
        }

        $auditperiod = AuditPeriodModel::select("auditperiodid", DB::raw("CONCAT(fromyear, ' - ', toyear) AS audit_period"))
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->where('financestatus', 'N')
            ->whereIn('lagacyyear', ['N', 'B'])
            ->orderBy('fromyear', 'desc')
            ->get();

        $annadhanamperiod = AuditPeriodModel::select("auditperiodid", DB::raw("CONCAT(fromyear, ' - ', toyear) AS audit_period"))
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->where('financestatus', 'Y')
            ->whereIn('lagacyyear', ['N', 'B'])
            ->orderBy('fromyear', 'desc')
            ->get();

        $DraftStatus['auditschid'] = '';
        $DraftStatus['exists'] = 'N';

        $hasexists = $isScheduled == 'Y';

        if ($hasexists) {

            $DraftStatus['auditschid'] = $auditscheduleid;
            $DraftStatus['exists'] = 'Y';
        }

        return view('audit.auditdatefixing', compact('inst', 'Accountparticulars', 'rcno', 'auditperiod', 'annadhanamperiod', 'Quarter', 'DraftStatus', 'prfaudit_det'));
    }

    public function audit_particulars($catcode = '', $deptcode = '', $subcatid = '')
    {
        $audit_particulars = MajorWorkAllocationtypeModel::callforrecords($catcode, $deptcode, $subcatid);
        // print_r($audit_particulars);exit;
        // $account_particulars = AccountParticularsModel::where('statusflag', '=', 'Y')
        // ->orderBy('accountparticularsename', 'asc')
        // ->get();
        $account_particulars = DB::table('audit.mst_accountparticulars')
            ->where('statusflag', '=', 'Y')
            ->orderBy('accountparticularsename', 'asc')
            ->get();

        if ($audit_particulars) {
            return response()->json([
                'data' => $audit_particulars,
                'account_particulars' => $account_particulars
            ]);
        }
    }

    public function audit_members(Request $request)
    {
        $planid = $request->input('planid');

        $inst = AuditManagementModel::audit_members($planid);
        if (!$inst) {
            return response()->json([
                'error' => true,
                'message' => 'failedtofetch'
            ]);
        }
        return response()->json($inst);
    }

    public function fetchAllScheduleData(Request $request)
    {
        $sessiondetails = session('charge');
        $deptcode = $sessiondetails->deptcode;
        $inst = AuditManagementModel::fetchAllScheduleData($deptcode);

        foreach ($inst as $item) {
            $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
        }

        // Return data in JSON format
        return response()->json(['data' => $inst]);  // Ensure the data is wrapped under "data"
    }

    public function fetchschedule_data(Request $request)
    {
        // $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
        $auditscheduleid = $request->auditscheduleid;
        $inst = AuditManagementModel::fetchsingle_scheduledata($auditscheduleid);

        foreach ($inst as $item) {
            $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
        }

        if ($inst) {
            return response()->json(['success' => true, 'data' => $inst]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }

    public function storeOrUpdateAuditSchedule(Request $request, $userId = null)
    {
         $planmappingid  =  $request->input('planmappingid');
        $chargedel = session('charge');
        $deptcode = $chargedel->deptcode;
        $data = $request->all();

        if ($request->action == 'update') {
            $as_code = Crypt::decryptString($request->as_code);
            $request->merge(['as_code' => $as_code]);
        }

        $from_date = Carbon::createFromFormat('d/m/Y', $request->input('from_date'))->format('Y-m-d');
        $to_date = Carbon::createFromFormat('d/m/Y', $request->input('to_date'))->format('Y-m-d');
        $request->merge(['from_date' => $from_date]);
        $request->merge(['to_date' => $to_date]);

        $tm_uid = $request->input('tm_uid');
        $json_tm_uid = json_encode($tm_uid);
        $request->merge(['tm_uid' => $json_tm_uid]);

        $request->validate([
            'ap_code' => 'required',
            'from_date' => 'required|date|date_format:Y-m-d|',
            'to_date' => 'required|date|date_format:Y-m-d|',
            'rc_no' => 'required',
            'tm_uid' => 'required|json',
            'th_uid' => 'required',
            'yearselected' => 'required',
            'auditmode' => 'nullable|string|max:2'
        ], [
            'required' => 'The :attribute field is required.',
            'alpha' => 'The :attribute field must contain only letters.',
            'integer' => 'The :attribute field must be a valid number.',
            'regex' => 'The :attribute field must be a valid number.',
            'email' => 'The :attribute field must be a valid email address.',
            'date' => 'The :attribute field must be a valid date.',
            'max' => 'The :attribute field must not exceed :max characters.',
        ]);

        $annadhanam_yearselected = $request->input('annadhanam_yearselected');
        $parallelinst = $request->isparalelinst;
        $auditmode = $request->auditmode;
        if ($annadhanam_yearselected) {
            $annadhanam_yearselected = $annadhanam_yearselected;
        } else {
            $annadhanam_yearselected = [];
        }

        $data = [
            'auditplanid' => $request->input('ap_code'),
            'fromdate' => $request->input('from_date'),
            'todate' => $request->input('to_date'),
            'rcno' => $request->input('rc_no'),
            'statusflag' => $request->input('finaliseflag'),
            'diaryflag' => 'N',
            'workallocationflag' => 'N',
            'yearselected' => $request->input('yearselected'),
            'annadhanam_yearselected' => $annadhanam_yearselected
        ];
        $sessiondet = session('user');
        $sessionuserid = $sessiondet->userid;

        if ($request->action == 'update') {
            $audit_scheduleid = $request->input('as_code');
        } else
            $audit_scheduleid = null;

        try {
            DB::beginTransaction();

            $query = DB::table(self::$instauditschedule_table)
                ->where('auditplanid', $request->input('ap_code'))  // Filter by auditplanid
                ->whereNotIn('statusflag', ['C', 'R', 'S', 'N']);  // Exclude rows where status is either 'C' or 'R'

            // Add a condition for 'update' action
            if ($request->action === 'update') {
                $query->where('auditscheduleid', '!=', $audit_scheduleid);
            }

            $Exists_Instauditschedule = $query->first();

            if ($Exists_Instauditschedule) {
                DB::rollBack();
                return response()->json([
                    'error' => true,
                    'message' => 'already_audit_scheduled'
                ], 400);
            }

            // Call the model method for create or update
            $teamMemberIds11 = json_decode($request->input('tm_uid'), true);
            // if (!is_array($teamMemberIds11)) {
            //   DB::rollBack();
            //   return response()->json(['error' => true, 'message' => 'invalid_teamuser'], 400);
            //  }
            // $request->merge(['auditscheduleid' =>  $new_auditschedule_id]);
            // Insert each team member using the TeamMember model

            $teamMemberIds1 = $teamMemberIds11;
            // $userIds = array_merge([$request->input('th_uid')], $teamMemberIds1);
            if (is_array($teamMemberIds11)) {
                $userIds = array_merge([$request->input('th_uid')], $teamMemberIds1);
            } else {
                $userIds = (array) $request->input('th_uid');
            }
            // $userIds = $teamMemberIds1;
            $conflictFound = false;

            // MapinstUsercountGet
            if ($auditmode != 'P') {
                $InsufficientUser = AuditManagementModel::UserMatchCheck($request->input('ap_code'));

                if ($InsufficientUser == 'insufficient_head_count') {
                    DB::rollBack();
                    return response()->json([
                        'success' => true,
                        'message' => 'insufficient_head_count'
                    ], 400);
                } else if ($InsufficientUser == 'insufficient_user_count') {
                    DB::rollBack();
                    return response()->json([
                        'success' => true,
                        'message' => 'insufficient_user_count'
                    ], 400);
                }
            }
            if (is_array($teamMemberIds11) && $parallelinst == 'Y') {
                $checkuserIDS = $teamMemberIds1;
            } else {
                $checkuserIDS = $userIds;
            }
            // Check if any of the users already exist with the same from and to dates
            if ($auditmode != 'P') {
                foreach ($checkuserIDS as $userId) {
                    // Build the query to check for overlapping or in-between dates
                    // $query = AuditManagementModel::DatecheckAuditschedule($userId, $request->input('from_date'), $request->input('to_date'), $parallelinst);
  $query = AuditManagementModel::DatecheckAuditschedule($userId, $request->input('from_date'), $request->input('to_date'), $parallelinst, $planmappingid);
                    if ($request->action === 'update') {
                        $query->where('ism.auditscheduleid', '!=', $audit_scheduleid);
                    }
                    $query->whereNotIn('ism.statusflag', ['C', 'R', 'S']);  // Exclude rows where status is 'C

                    $existing = $query->first();

                    if ($existing) {
                        $conflictFound = true;
                        break;
                    }
                }

                if ($conflictFound) {
                    DB::rollBack();
                    return response()->json([
                        'success' => true,
                        'message' => 'already_audit_scheduled_with_daterange'
                    ], 400);
                }
            }
            $beforeinsertflag = $request->input('beforeinsert');

            if ($beforeinsertflag == 'finalise_beforeinsert') {
                return response()->json([
                    'finalise_beforeinsert' => 'success'
                ]);
            }

            // Call the model method for create or update
            $audit_schedule = AuditManagementModel::createIfNotExistsOrUpdateAuditSchedule($data, $audit_scheduleid, $sessionuserid);
            // print_r($audit_schedule);exit;
            if (!$audit_schedule) {
                DB::rollBack();
                return response()->json(['error' => true, 'message' => 'schedule_save_failed'], 400);
            }

            if ($audit_schedule) {
                if ($request->action == 'update') {
                    $statusflag = 'Y';
                    $audit_schedule_member = AuditManagementModel::update_teamstatus('Y', $audit_scheduleid, $request->input('from_date'), $request->input('to_date'));

                    /* }
                        }
                    }*/

                    $audit_scheduleid = $audit_scheduleid;
                } else {
                    $teamMemberIds = json_decode($request->input('tm_uid'), true);

                    // insert auditscheduled teamhead details;
                    AuditManagementModel::insertAuditScheduleMem(
                        $audit_schedule,
                        $request->input('th_uid'),
                        $request->input('from_date'),
                        $request->input('to_date'),
                        'Y',
                        $sessionuserid,
                    );
                    // insert auditscheduled teammember details;
                    // foreach ($teamMemberIds as $memberId) {
                    //  AuditManagementModel::insertAuditScheduleMem($audit_schedule,  $memberId,  $request->input('from_date'),  $request->input('to_date'),  'N', $sessionuserid,);
                    //  }

                    if (is_array($teamMemberIds11) && !empty($teamMemberIds11)) {
                        foreach ($teamMemberIds as $memberId) {
                            AuditManagementModel::insertAuditScheduleMem(
                                $audit_schedule,
                                $memberId,
                                $request->input('from_date'),
                                $request->input('to_date'),
                                'N',
                                $sessionuserid,
                            );
                        }
                    }

                    $currentRcno = DB::table('audit.mst_dept')
                        ->where('deptcode', $deptcode)
                        ->value('rcno');  // `value()` will return the first column's value

                    if ($currentRcno !== null) {
                        // Increment the rcno
                        $incrementedRcno = $currentRcno + 1;

                        // Update the rcno value
                        AuditManagementModel::updateRcno($deptcode, $incrementedRcno);
                    } else {
                        AuditManagementModel::updateRcno($deptcode, '1');
                    }
                    $audit_scheduleid = $audit_schedule;
                }

                $status = $request->input('finaliseflag');

                $auditplanid = $request->input('ap_code');
                $inst = AuditManagementModel::auditplandet($auditplanid, $sessionuserid);

                $catcode = $inst->first()->catcode;
                // Access the 'catcode' attribute
                $deptcode = $inst->first()->deptcode;
                $subcatid = $inst->first()->subcatid;
                if ($auditmode != 'P') {
                    $Accountparticulars = self::audit_particulars($catcode, $deptcode, $subcatid);
                    $Accountparticulars = $Accountparticulars->original;
                    $Accountparticulars = $Accountparticulars['data'];

                    $Accountparticulars = $Accountparticulars
                        ->pluck('callforrecordsid')
                        ->toArray();
                }

                if ($status == 'F') {
                    if ($auditmode != 'P') {
                        $json_checked_CFR = json_encode($Accountparticulars);

                        if (empty($json_checked_CFR)) {
                            DB::rollBack();
                            return response()->json([
                                'success' => true,
                                'message' => 'failed_cfr'
                            ], 400);
                        }
                        $CFRSelected = AuditManagementModel::CFRStoreData($audit_scheduleid, $json_checked_CFR);
                        if (!$CFRSelected) {
                            DB::rollBack();
                            return response()->json([
                                'success' => true,
                                'message' => 'failed_cfr'
                            ], 400);
                        }
                    }
                    $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
                    $sentsms = $auditModel->sendIntimation($audit_scheduleid);
                    //    $sentsms = AuditManagementModel::sent_intimation($audit_scheduleid);

                    //  print_r($sentsms);
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'audit_scheduled_finalize'
                    ], 201);
                } else {
                    /* $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
                    $sentsms = $auditModel->sendIntimation($audit_scheduleid);
                    print_r($sentsms);exit;*/

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'schdeuleid' => $audit_schedule,
                        'message' => 'audit_scheduled_success'
                    ], 201);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function auditee_intimation(Request $request)
    {
        $session = $request->session();
        // $quartercode = $request->quartercode;
        $planmappingid = $request->planmappingid;
        if ($session->has('user')) {
            $user = $session->get('user');
            $userid = $user->userid ?? null;
        } else {
            return 'No user found in session.';
        }
 $audit_plandetail = AuditManagementModel::fetch_auditscheduledetails($userid, $planmappingid);
        // $audit_plandetail = AuditManagementModel::fetch_auditscheduledetails($userid, $quartercode);
        foreach ($audit_plandetail as $item) {
            // return $audit_plandetail;

            $item->encrypted_auditplanid = Crypt::encryptString($item->auditplanid);
            $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);

            // return $audit_plandetail;
            $nodalname = $item->nodalname;
            $nodaldesig = $item->nodaldesignation;
            $item->nodalperson_details = $nodalname . '<br>' . $nodaldesig;

            $nodalemail = $item->nodalemail;
            $nodalmobile = $item->nodalmobile;
            $item->nodalperson_contact = $nodalmobile . '<br>' . $nodalemail;
            unset($item->auditscheduleid);
        }

        return response()->json(['data' => $audit_plandetail]);  // Ensure the data is wrapped under "data"

        // print_r($audit_plandetail);
    }

    public function auditee_acceptdetails(Request $request)
    {
        $request->validate([
            'auditscheduleid' => 'required',
        ]);
        $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
        $account_particularsaccept = AuditManagementModel::fetch_Accountaccepteddetails($auditscheduleid);
        $cfr_saccept = AuditManagementModel::fetch_cfraccepteddetails($auditscheduleid);
        $auditeeuserdetails = AuditeeModel::fetch_auditeeofficeusers($auditscheduleid);

        return response()->json([
            'data' => $account_particularsaccept,
            'cfr' => $cfr_saccept,
            'auditeeuserdetails' => $auditeeuserdetails
        ]);
        // return response()->json(['data' => $account_particularsaccept]); // Ensure the data is wrapped under "data"
    }

    /* public static function CancelAuditschedule(Request $request)
    {
        $cancelschedule = AuditManagementModel::CancelSchedule($request->scheduleid,$request->cancel_remarks);

        return $cancelschedule;

    }*/

    public static function CancelorRescheduleAudit(Request $request)
    {
        $session = session('user');
        $sessionuser = $session->userid;
        $data = [
            'auditscheduleid' => $request->scheduleid,
            'remarks' => $request->Remarks,
            'statusflag' => $request->statusflag,
            'updatedby' => $sessionuser,
            'updatedon' => View::shared('get_nowtime'),
        ];
        $CancelorRescheduleAudit = AuditManagementModel::CancelorReSchedule($data);
        if ($CancelorRescheduleAudit) {
            if ($request->statusflag == 'R') {
                $response = 'Audit Rescheduled Successfully!';
            } else if ($request->statusflag == 'S') {
                $response = 'Audit Suspended Successfully!';
            } else {
                $response = 'Audit Cancelled Successfully!';
            }
        }
        return $response;
    }

    public function fetchinstitution(Request $request)
    {
        $deptcode = $request->input('deptcode');
        $regioncode = $request->input('regioncode');
        $distcode = $request->input('distcode');

        $institution = AuditManagementModel::fetchInstitutionData($deptcode, $regioncode, $distcode);

        // print_r($institution);
        return response()->json([
            'success' => true,
            'auditor' => $institution,
        ]);
    }

    public function fetchteam(Request $request)
    {
        $deptcode = $request->input('deptcode');
        $regioncode = $request->input('regioncode');
        $distcode = $request->input('distcode');
        $auditteamid = $request->input('auditteamid');
        $instid = $request->input('instid');
        // echo $auditteamid;

        $teams = AuditManagementModel::fetchTeamData($deptcode, $regioncode, $distcode, $auditteamid, $instid);

        // print_r($teams);
        return response()->json([
            'success' => true,
            'auditor' => $teams['scheduledauditors'],
            'membercount' => $teams['membercount'],
        ]);
    }

    public function getAuditors_updateplanuser(Request $request)
    {
        $distcode = $request->distcode;
        $deptcode = $request->deptcode;
        $regioncode = $request->regioncode;
        $auditteamid = $request->auditteamid;
        // print_r( $distcode);
        // $teamcode = $request->teamcode;

        if ($request->auditteamid)
            $auditteamid = Crypt::decryptString($request->auditteamid);
        else
            $auditteamid = '';

        $auditors = AuditManagementModel::getauditors_updateplanuser($deptcode, $regioncode, $distcode, $auditteamid);

        return response()->json(['success' => true, 'auditor' => $auditors]);
    }

    public function auditteam_insertupdate(Request $request)
    {
        // print_r($request->all());
        // exit;
        try {
            $session = session('user');
            $userId = $session->userid;

            $action = $request->input('action');
            $finaliseflag = $request->input('finaliseflag');
            $auditteamid = $action === 'update' ? Crypt::decryptString($request->input('auditteamid')) : null;

            $request->validate([
                'oldteamhead' => 'nullable|string',
                'oldteammembers' => 'nullable|string',
                'newteamhead' => 'nullable|string',
                'newteammembers' => 'nullable|string',
                'remarks' => 'nullable|string',
            ]);

            $newteammembers = $request->input('newteammembers');
            $newteammembers = $newteammembers ? json_decode($newteammembers, true) : [];

            $data = [
                'auditplanid' => $request->input('instid'),
                'newteamhead' => $request->input('newteamhead'),
                'newteammembers' => $request->input('newteammembers') !== null ? json_encode(json_decode($request->input('newteammembers'))) : null,
                'remarks' => $request->input('remarks'),
                'updatedby' => $userId,
                'updatedon' => View::shared('get_nowtime'),
            ];

            if ($action === 'insert') {
                $data['createdby'] = $userId;
                $data['createdon'] = View::shared('get_nowtime');
                $data['oldteamhead'] = $request->input('oldteamhead');
                $data['oldteammembers'] = $request->input('oldteammembers') !== null ? json_encode(json_decode($request->input('oldteammembers'))) : null;
            }

            $data['statusflag'] = $finaliseflag === 'Y' ? 'F' : 'S';
            $uploadid = $request->input('uploadid');

            //  print_r(($request->hasFile('file')));
            // //File Upload////

            if ((($action === 'insert') || ($action === 'update')) && ($request->hasFile('file'))) {
                $destinationPath = 'uploads/alterplan';
                $sessioncharge = session('charge');
                $designationpathArray = [
                    $request->input('deptcode'),
                    $request->input('regioncode'),
                    $request->input('distcode'),
                    $request->input('instid'),
                    View::shared('alterplan')
                ];
                // return   $designationpathArray;
                if ($uploadid) {
                    $uploadResult = $this->fileUploadService->uploadFile($request->file('file'), $destinationPath, $uploadid, $designationpathArray);
                } else {
                    // echo 'ads';
                    $uploadResult = $this->fileUploadService->uploadFile($request->file('file'), $destinationPath, '', $designationpathArray);
                }

                $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
                // return $fileuploadId;
                $data['fileuploadid'] = $fileuploadId;
            } else {
                $data['fileuploadid'] = NULL;
            }

            // exit;
            $result = AuditManagementModel::updateauditplanuser($data, $auditteamid);

            if ($result['status']) {
                return response()->json([
                    'success' => true,
                    'type' => $result['type'],
                    'message' => $result['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['message']
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function fetchUpdateplanTeam(Request $request)
    {
        // echo $request->auditteamid;
        // echo 'hi';
        // exit;
        // Decrypt the audit team ID from the request, if exists
        $auditteamid = $request->has('auditteamid') ? Crypt::decryptString($request->auditteamid) : null;

        // echo $auditteamid;
        // exit;

        // Instantiate the model and call the method
        $auditManagementModel = new AuditManagementModel();
        $updatedteam = $auditManagementModel->fetchUpdateuserplanData($auditteamid);

        // print_r($updatedteam );

        // exit;

        // Make sure $updatedteam is a collection or array before looping
        if ($updatedteam && $updatedteam->isNotEmpty()) {
            foreach ($updatedteam as $all) {
                // Check if the property exists before using it
                if (isset($all->auditteamsdraftid)) {
                    $all->encrypted_auditteamsdraftid = Crypt::encryptString($all->auditteamsdraftid);
                    // Remove the original auditplanid
                    unset($all->auditteamsdraftid);
                }
            }
        }

        // print_r($updatedteam);
        // exit;

        // Return the response
        return response()->json([
            'success' => $updatedteam->isNotEmpty(),
            'message' => $updatedteam->isEmpty() ? 'User not found' : '',
            'data' => $updatedteam->isEmpty() ? null : $updatedteam
        ], $updatedteam->isEmpty() ? 404 : 200);
    }

    public function fetchupdatedata(Request $request)
    {
        // $auditteamid = $request->auditteamid;

        $auditteamid = $request->has('auditteamid') ? Crypt::decryptString($request->auditteamid) : null;

        if (!$auditteamid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid audit team ID provided.'
            ], 400);
        }

        $auditManagementModel = new AuditManagementModel();
        $updatedteam = $auditManagementModel->fetchUpdateuserplanData($auditteamid);

        if ($updatedteam->isNotEmpty()) {
            foreach ($updatedteam as $all) {
                if (isset($all->auditteamsdraftid)) {
                    $all->encrypted_auditteamsdraftid = Crypt::encryptString($all->auditteamsdraftid);
                    unset($all->auditteamsdraftid);
                }
            }
        }

        return response()->json([
            'success' => $updatedteam->isNotEmpty(),
            'message' => $updatedteam->isEmpty() ? 'User not found' : '',
            'data' => $updatedteam->isEmpty() ? null : $updatedteam
        ], $updatedteam->isEmpty() ? 404 : 200);
    }

    public static function changerequestdeptfetch()
    {
        $dept = AuditManagementModel::commondeptfetch();

        return view('audit.changerequest', compact('dept'));
    }

  public function getquarterBasedOninst(Request $request)
{

    $request->validate([
        'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
    ], [
        'required' => 'The :attribute field is required.',
        'regex'    => 'The :attribute field must be a valid number.',
    ]);

    $deptcode = $request->input('deptcode');
    $instmappingcode = $request->input('instmappingcode');
    $auditquartercode = $request->input('auditquartercode');
 //dd($auditquartercode);
    //$quarter = AuditManagementModel::commonquarterfetch($deptcode,$instmappingcode);

    $financialyear = AuditManagementModel::getFinancialYears($deptcode,$instmappingcode);
    $auditperiod_yrselected = AuditManagementModel::Auditperiodfetch($instmappingcode,$auditquartercode);
    $annathanamyear = AuditManagementModel::Auditannathanamfetch($deptcode, $instmappingcode,$auditquartercode);
    $annathanamyearselected = $annathanamyear->pluck('yearselected')->filter();
    $annadhanam_only = $financialyear->first()->annadhanam_only;

   // dd($annathanamyearselected);

    //  return $auditperiod_yrselected ;
        $yearselected = $auditperiod_yrselected->pluck('yearselected');
        $yearselectedArray = $yearselected->toArray();

        $auditplanid = $auditperiod_yrselected->pluck('auditplanid')->unique()->values()->toArray();


    $auditperiod = AuditManagementModel::Auditperiodcompactfetch($deptcode);



    return response()->json([
        'success' => true,
        'auditperiod' => $auditperiod,
        'financialyear' => $financialyear,
        //'quarter' => $quarter,
        'yearselected'=>$yearselectedArray,
        'auditperiod_yrselected' => $auditperiod_yrselected,
        'auditplanid'=>$auditplanid,
        'annathanamyear' => $annathanamyear,
        'annathanamyearselected' => $annathanamyearselected,
        'annadhanam_only' => $annadhanam_only


    ]);
}


    public function changerequest_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'regioncode' => 'required|string|regex:/^\d+$/',
                'distcode' => 'required|string|regex:/^\d+$/',
                'instmappingcode' => 'required|string|regex:/^\d+$/',
                'yearselected' => 'required|string|regex:/^\d{4}\s*-\s*\d{4}$/',
                'auditplanid' => 'required|string',
                'annathnamyear' => 'required|string|regex:/^\d{4}\s*-\s*\d{4}$/',
            ];

            $auditplan = session('user');
            if (!$auditplan || !isset($auditplan->userid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $auditplan->userid;
            $auditplanid = $request->input('auditplanid');
            // echo 'auditplanid';print_r($auditplanid);exit;

            $data = [
                'yearselected' => $request->yearselected ?? null,
                'updatefield' => $request->updatefield,  // this is key
                'updatedby' => $userchargeid,
                'financestatus' => $request->annathnamyear ?? null,
            ];

            // if ($request->input('action') === 'update') {
            $data['createdon'] = View::shared('get_nowtime');
            $data['createdby'] = $userchargeid;
            $data['updatedon'] = View::shared('get_nowtime');
            $data['updatedby'] = $userchargeid;
            // }

            $result = AuditManagementModel::changerequests_insertupdate($data, $auditplanid, 'audit.yearcode_mapping');
            // return   $result;
            return response()->json(['success' => true, 'message' => $result]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function getregionbasedondeptchangerequest(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $deptcode = $request->input('deptcode');

        $regions = AuditManagementModel::getRegionsByDept($deptcode);
        $auditperiod = AuditManagementModel::Auditperiodcompactfetch($deptcode);

        return response()->json([
            'success' => true,
            'data' => $regions,
            'auditperiod' => $auditperiod,  // Include audit periods in the response
        ]);
    }

    public function getdistrictbasedonregionchangerequest(Request $request)
    {
        // Validate the input
        $request->validate(
            [
                'region' => ['required', 'string', 'regex:/^\d+$/'],
                'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            ],
            [
                'region.required' => 'The region field is required.',
                'region.regex' => 'The region field must be a valid number.',
                'deptcode.required' => 'The deptcode field is required.',
                'deptcode.regex' => 'The deptcode field must be a valid number.',
            ]
        );

        // Get the department code
        $regioncode = $request->input('region');
        $deptcode = $request->input('deptcode');

        // Fetch regions from the model
        $district = AuditManagementModel::getdistrictByregion($regioncode, $deptcode);

        // Return JSON response
        if ($district->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $district]);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

    public function getinstitutionbasedondistchangerequest(Request $request)
    {
        // Validate the input
        $request->validate([
            'region' => ['required', 'string', 'regex:/^\d+$/'],
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'district' => ['required', 'string', 'regex:/^\d+$/'],
            'updatefield' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'region.required' => 'The :attribute field is required.',
            'region.regex' => 'The :attribute field must be a valid number.',
            'deptcode.required' => 'The deptcode field is required.',
            'deptcode.regex' => 'The deptcode field must be a valid number.',
            'district.required' => 'The district field is required.',
            'district.regex' => 'The district field must be a valid number.',
            'updatefield.required' => 'The updatefield field is required.',
            'updatefield.regex' => 'The updatefield field must be a valid number.',
        ]);

        // Get the department code
        $regioncode = $request->input('region');
        $deptcode = $request->input('deptcode');
        $district = $request->input('district');
        $updatefield = $request->input('updatefield');

        // Fetch regions from the model
        $institution = AuditManagementModel::getinstitutionBydistrictchange($district, $regioncode, $deptcode, $updatefield);

        // Return JSON response
        if ($institution->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $institution]);
        } else {
            return response()->json(['success' => false, 'message' => 'No Institutions found'], 200);
        }
    }

    public function changerequest_fetchData(Request $request)
    {
        $auditplanid = $request->has('auditplanid') ? Crypt::decryptString($request->auditplanid) : null;
        $auditplan = AuditManagementModel::changerequestfetchData($auditplanid, 'audit.auditplan');

        if (is_iterable($auditplan)) {
            foreach ($auditplan as $all) {
                $all->encrypted_auditplanid = Crypt::encryptString($all->auditplanid);
                unset($all->auditplanid);
            }
        }

        return response()->json([
            'success' => true,
            'message' => empty($auditplan) ? 'No Details found' : '',
            'data' => $auditplan ?? []
        ], 200);
    }

    /* Manual Plan - start */

    public function fetchUpdatepManualplan(Request $request)
    {
        // echo $request->auditteamid;
        // echo 'hi';
        // exit;
        // Decrypt the audit team ID from the request, if exists
        $auditteamid = $request->has('auditteamid') ? Crypt::decryptString($request->auditteamid) : null;

        // echo $auditteamid;
        // exit;
        $updatedteam = AuditManagementModel::fetchUpdatepManualplan($auditteamid);

        // print_r($updatedteam);

        // exit;

        if ($updatedteam) {
            foreach ($updatedteam as $all) {
                if (isset($all->auditplanteamid)) {
                    $all->encrypted_auditplanteamid = Crypt::encryptString($all->auditplanteamid);
                    $all->encrypted_auditplanid = Crypt::encryptString($all->auditplanid);

                    //  unset($all->auditplanteamid, $all->auditplanid);
                }
            }
        }
        // Make sure $updatedteam is a collection or array before looping

        // print_r($updatedteam);
        // exit;

        // Return the response
        return response()->json([
            'success' => $updatedteam->isNotEmpty(),
            'message' => $updatedteam->isEmpty() ? 'User not found' : '',
            'data' => $updatedteam->isEmpty() ? null : $updatedteam
        ], $updatedteam->isEmpty() ? 404 : 200);
    }

    public function getAuditors_manualplan(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
            'distcode' => ['required', 'string', 'regex:/^\d+$/'],
            'auditteamid' => ['nullable', 'string'],
            'user_det' => ['nullable', 'string']
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid field.',
        ]);
        $distcode = $request->distcode;
        $deptcode = $request->deptcode;
        $regioncode = $request->regioncode;
        $auditteamid = $request->auditteamid;

        $isreservedauditors = $request->user_det;
        // print_r( $distcode);
        // $teamcode = $request->teamcode;

        try {
            if ($request->auditteamid)
                $auditteamid = Crypt::decryptString($request->auditteamid);
            else
                $auditteamid = '';

            $auditors = AuditManagementModel::getAuditors_manualplan($deptcode, $regioncode, $distcode, $auditteamid, $isreservedauditors);

            return response()->json(['success' => true, 'auditor' => $auditors]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public function getManualSchemeTeamMembers(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'instid' => ['required', 'integer'],
            'head_userid' => ['required', 'integer'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid field.',
        ]);

        try {
            $result = AuditManagementModel::getManualSchemeTeamMembers(
                $request->input('deptcode'),
                $request->input('instid'),
                $request->input('head_userid')
            );

            return response()->json([
                'success' => true,
                'scheme_active' => $result['scheme_active'],
                'members' => $result['members'],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public function fetchExcessinstitution(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
            'distcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid field.',
        ]);

        try {
            $deptcode = $request->input('deptcode');
            $regioncode = $request->input('regioncode');
            $distcode = $request->input('distcode');
            $instid = $request->input('selectedinstid') ?? null;

            $institution = AuditManagementModel::fetchExcessinstitution($deptcode, $regioncode, $distcode, $instid);
            // return  $institution;
            return response()->json([
                'success' => true,
                'auditor' => $institution,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }

        // print_r($institution);
    }

    public function manualplan_insertupdate(Request $request)
    {
        // print_r($request->all());
        // exit;

        try {
            $session = session('user');
            $userId = $session->userid;

            //  return $auditteamid;
            $validatedData = $request->validate([
                'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
                'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
                'distcode' => ['required', 'string', 'regex:/^\d+$/'],
                'newteamhead' => [
                    'required',
                    'string',
                ],
                'newteammembers' => [
                    'required',
                    'string',
                ],
                'teamsize' => [
                    'required',
                    'string',
                ],
                'formparam' => [
                    'required',
                    'string',
                ],
            ], [
                'required' => 'The :attribute field is required.',
                'regex' => 'The :attribute field must be a valid field.',
            ]);
            $headlevel = json_decode($request->headlevel, true);
            $teamMemberDesigCodes = json_decode($request->teamMemberdesigcode, true);
            $teamHeaddesigcode = trim($request->teamHeaddesigcode, '"');
            $newTeamMembers = json_decode($request->newteammembers, true);

            $firstHeadDesigCode = $headlevel[0]['desigcode'];

            if (in_array($firstHeadDesigCode, $teamMemberDesigCodes)) {
                throw new Exception('Cannot be a memeber');
            }
            if (count($newTeamMembers) !== ($request->teamsize - 1)) {
                throw new Exception('Team size mismatched');
            }

            $validHeadCodes = array_column($headlevel, 'desigcode');
            if (!in_array($teamHeaddesigcode, $validHeadCodes)) {
                throw new Exception('Team head designation is not valid');
            }

            $action = $request->input('action');
            $finaliseflag = $request->input('finaliseflag');
            $auditteamid = $action === 'update' ? Crypt::decryptString($request->input('auditteamid')) : null;
            $auditplanid = $action === 'update' ? Crypt::decryptString($request->input('auditplanid')) : 0;

            $newteammembers = $request->input('newteammembers');
            $newteammembers = $newteammembers ? json_decode($newteammembers, true) : [];
            // $newTeamMembers = $request->input('newteammembers'); // This is an array: ["1897"]

            // Convert to PostgreSQL array format string:
            $newTeamMembersArray = '{' . implode(',', $newteammembers) . '}';
            // $newTeamMembersArray = 'ARRAY[' . implode(',', $newteammembers) . ']';

            //   return  $newTeamMembersArray;
            $data =
                [
                    'instid' => $request->input('instid'),
                    'deptcode' => $request->input('deptcode'),
                    'regioncode' => $request->input('regioncode'),
                    'distcode' => $request->input('distcode'),
                    'newteamhead' => $request->input('newteamhead'),
                    // 'newteammembers'    => $request->input('newteamhead'),
                    'newteammembers' => $newTeamMembersArray,
	                    'remarks' => $request->input('remarks'),
	                    'updatedby' => $userId,
	                    'updatedon' => View::shared('get_nowtime'),
	                    'formparam' => $request->input('formparam'),
	                    'is_spillover' => $request->input('is_spillover'),
	                    'remainingmandays' => $request->input('remainingmandays'),
	                ];

            if ($action === 'insert') {
                $data['createdby'] = $userId;
                $data['createdon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userId;
                $data['updatedon'] = View::shared('get_nowtime');
            } else {
                $data['updatedby'] = $userId;
                $data['updatedon'] = View::shared('get_nowtime');
            }

            $data['statusflag'] = $finaliseflag === 'Y' ? 'F' : 'S';

            //    print_r($data);
            //  exit;

            $result = AuditManagementModel::updatemanualplan($data, $auditteamid, $auditplanid);
            // print_r($result);
            // exit;
            // if ($result['status'] == false) {
            //     return response()->json(['message' =>  $result['message'], 'error' =>  $result['message']], 401);
            // }

            $resultdet = $result[0]->manualplan;

            // $status = $resultdet['status'];
            $resultdata = json_decode($resultdet, true);  // returns associative array
            $currentQuarter = DB::table(self::$deptartment_table)
                ->where('deptcode', $request->input('deptcode'))
                ->select('currentquarter', 'currentquarterfromdate')
                ->first();
            if ($currentQuarter) {
                $resultdata['currentquartercode'] = $currentQuarter->currentquarter;
                $resultdata['currentquarterfromdate'] = $currentQuarter->currentquarterfromdate;
                $resultdet = json_encode($resultdata);
            }
            $status = $resultdata['status'];
            // print_r($resultdet);
            // exit;

            if ($status === 'false') {
                // return response()->json(['message' =>  $resultdata['error'], 'error' =>  $resultdata['error']], 401);
                return response()->json([
                    'success' => true,
                    'data' => $resultdet,
                    'message' => $status
                ]);
            } else if ($status === 'inserted') {
                return response()->json([
                    'success' => true,
                    'data' => $resultdet,
                    'message' => $status
                ]);
            } else if ($status === 'success') {
                return response()->json([
                    'success' => true,
                    'data' => $resultdet,
                    'message' => $status
                ]);
            } else if ($status === 'updated') {
                return response()->json([
                    'success' => true,
                    'data' => $resultdet,
                    'message' => $status
                ]);
            } else if ($status === 'finalised') {
                return response()->json([
                    'success' => true,
                    'data' => $resultdet,
                    'message' => 'finalised successfully'
                ]);
            } else {
                return response()->json(['message' => 'Error Occurs', 'error' => $resultdata], 401);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    function fetchmanualupdatedata(Request $request)
    {
        $auditteamid = $request->has('auditteamid') ? Crypt::decryptString($request->auditteamid) : null;
        try {
            if (!$auditteamid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid audit team ID provided.'
                ], 400);
            }

            $auditManagementModel = new AuditManagementModel();
            $updatedteam = $auditManagementModel->fetchUpdatepManualplan($auditteamid);

            if ($updatedteam) {
                foreach ($updatedteam as $all) {
                    if (isset($all->auditplanteamid)) {
                        $all->encrypted_auditplanteamid = Crypt::encryptString($all->auditplanteamid);
                        $all->encrypted_auditplanid = Crypt::encryptString($all->auditplanid);
                        //    unset($all->auditplanteamid, $all->auditplanid);
                    }
                }
            }
            return $updatedteam;
            return response()->json([
                'success' => $updatedteam->isNotEmpty(),
                'message' => $updatedteam->isEmpty() ? 'User not found' : '',
                'data' => $updatedteam->isEmpty() ? null : $updatedteam
            ], $updatedteam->isEmpty() ? 404 : 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    // -------------------------------------------Quarter Transaction Start----------------------------

    // public static function fetch_notscheduledinst()
    // {
    //     $spillous = AuditManagementModel::fetchSpilloverWithCount();
    //     // print_R($spillous);
    //     // exit;
    //     $checkinstdetails = AuditManagementModel::fetchspilloverQuarterDetails($spillous);
    //     $spilloverresult = AuditManagementModel::spillover_fetchData($spillous, 'audit.temp_inst_q1_pending');
    //     $spilloverresultData = $spilloverresult['data'];
    //     $spillovertempCount = $spilloverresult['count'];
    //     // print_r($spilloverresultData);
    //     // exit;
    //     $Pendinginstitutions = AuditManagementModel::getnotscheduleinstData();
    //     $pendingmastercount = AuditManagementModel::getnotscheduleinstCount();
    //     // print_r($Pendinginstitutions);
    //     // exit;
    //     $penidnResult = AuditManagementModel::penidninst_fetchData($Pendinginstitutions, 'audit.temp_inst_q1_pending');
    //     $penidnData = $penidnResult['data'];
    //     $penidnCountfromtemp = $penidnResult['count'];
    //     $pendinginstcheck = AuditManagementModel::pendinginstcheck();
    //     $quarterInfo = $pendinginstcheck['quarterinfo'] ?? null;
    //     //  print_r($quarterInfo);

    //     $templateinstitutionlist = AuditManagementModel::getTemplateInstitutionList();
    //     // print_r($templateinstitutionlist);
    //     // exit;
    //     $templateInstCount = $templateinstitutionlist['count'] ?? 0;
    //     $templateInstData = $templateinstitutionlist['data'] ?? collect();
    //     $templateresults = AuditManagementModel::penidndtemplate_fetchData($templateInstData, 'audit.temp_inst_q1_pending');
    //     $templatedatatfromtemp = $templateresults['data'];
    //     $templateCountfromtemp = $templateresults['count'];
    //     //  print_r($templateCountfromtemp);
    //     // exit;
    //     return view('audit.instchange', [
    //         'spillous' => $spillous,
    //         'checkinstdetails' => $checkinstdetails,
    //         'spilloverresultData' => $spilloverresultData,
    //         'spillovertempCount' => $spillovertempCount,
    //         'Pendinginstitutions' => $Pendinginstitutions,
    //         'penidnData' => $penidnData,
    //         'penidnCountfromtemp' => $penidnCountfromtemp,
    //         'pendingmastercount' => $pendingmastercount,
    //         'pendinginstcheck' => $pendinginstcheck,
    //         'quarterInfo' => $quarterInfo,
    //         'templateInstCount' => $templateInstCount,
    //         'templateInstData' => $templateInstData,
    //         'templateresults' => $templateresults,
    //         'templateCountfromtemp' => $templateCountfromtemp,
    //         'templatedatatfromtemp' => $templatedatatfromtemp,
    //     ]);
    // }

    // public function penidninstUpdation(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $session = session('user');
    //         $updatemap = session('charge');

    //         if(!$session || empty($session->userid)) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Session expired. Please login again.'
    //             ], 401);
    //         }

    //         $deptcode = $updatemap->deptcode ?? null;
    //         $distcode = $updatemap->distcode ?? null;
    //         $userid = $session->userid;
    //         $now = View::shared('get_nowtime');

    //         $action = $request->input('action');
    //         $status = $request->input('status');

    //         $pendingRows = $request->input('pending.rows', []);
    //         $templateRows = $request->input('template.rows', []);

    //         $spilloverRaw = $request->input('spillover', []);
    //         $spillous = $request->input('spillous', []);
    //         // dd($spillous);
    //         $spillover = isset($spilloverRaw['rows'])
    //             ? $spilloverRaw['rows']
    //             : $spilloverRaw;

    //         $newQuarter = AuditManagementModel::getQuarterValue($deptcode, 'currentquarter');
    //         $currentQuarter = AuditManagementModel::getQuarterValue($deptcode, 'previousquarter');

    //         // print_r($currentQuarter);exit;
    //         $spilloverIndexedInsert = AuditManagementModel::indexSpillousByInstid(
    //             $action === 'finalize' ? $spillover : $spillous
    //         );

    //         $spilloverIndexedFinalize = AuditManagementModel::indexSpillousByInstid($spillover);

    //         if($action === 'spilldatainsert') {
    //             AuditManagementModel::insertSpilloverInstitutions(
    //                 $spilloverIndexedInsert,
    //                 $newQuarter,
    //                 $userid,
    //                 $now
    //             );

    //             DB::commit();

    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'spilinsert'
    //             ]);
    //         }

    //         if(in_array($action, ['pendingdatainsert', 'templatedatainsert'])) {

    //             foreach($spillous as $row) {

    //                 if(empty($row['instid'])) {
    //                     continue;
    //                 }

    //                 // Priority Logic
    //                 $fromPriority = null;
    //                 $toPriority = null;

    //                 if(isset($row['quarterchange']) && $row['quarterchange'] === 'Q4') {
    //                     // $fromPriority = '01';
    //                     $toPriority = '02';
    //                 }

    //                 $data = [
    //                     'instid' => $row['instid'],
    //                     'quartercode' => $row['currentquarter'],
    //                     'pendingflag' => 'D',
    //                     'newquartercode' => $row['quarterchange'],
    //                     'yearofaudit' => '2026',
    //                     'createdon' => $now,
    //                     'createdby' => $userid,
    //                     'updatedon' => $now,
    //                     'updatedby' => $userid,
    //                     'spilloverflag' => 'N',
    //                     'toprioritycode' => $toPriority,
    //                     'fromprioritycode' =>'01',
    //                     'auditmode' => $action === 'pendingdatainsert' ? 'P' : 'T',
    //                 ];

    //                 $result = AuditManagementModel::UpdateNotscheduledData(
    //                     $data,
    //                     null,
    //                     'audit.temp_inst_q1_pending'
    //                 );

    //                 if(!$result['status']) {
    //                     throw new Exception($result['message']);
    //                 }
    //             }

    //             DB::commit();

    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'pendinginsert'
    //             ]);
    //         }

    //         if($action === 'finalize') {
    //             AuditManagementModel::updateInstitutionExistingQuarter();

    //             AuditManagementModel::updateSpilloverInstitutions(
    //                 $spilloverIndexedFinalize,
    //                 $newQuarter,
    //                 $userid,
    //                 $now
    //             );

    //             $allRows = collect($pendingRows)
    //                 ->map(fn($r) => $r + ['source' => 'pending'])
    //                 ->merge(
    //                     collect($templateRows)->map(fn($r) => $r + ['source' => 'template'])
    //                 )
    //                 ->values();

    //             foreach($allRows as $row) {
    //                 if(empty($row['instid'])) {
    //                     continue;
    //                 }

    //                 $data = [
    //                     'instid' => $row['instid'],
    //                     'quartercode' => $row['currentquarter'],
    //                     'pendingflag' => 'F',
    //                     'newquartercode' => $row['quarterchange'],
    //                     'yearofaudit' => '2026',
    //                     'createdon' => $now,
    //                     'createdby' => $userid,
    //                     'updatedon' => $now,
    //                     'updatedby' => $userid,
    //                     'spilloverflag' => 'N',
    //                     'auditmode' => $row['source'] === 'pending' ? 'P' : 'T',
    //                 ];

    //                 $result = AuditManagementModel::UpdateNotscheduledData(
    //                     $data,
    //                     null,
    //                     'audit.temp_inst_q1_pending'
    //                 );

    //                 if(!$result['status']) {
    //                     throw new Exception($result['message']);
    //                 }
    //             //  dd($row['quarterchange']);
    //                 AuditManagementModel::updateInstitutionQuarter(
    //                     [$row['instid']],
    //                     $row['quarterchange'],
    //                     $currentQuarter,
    //                     $userid,
    //                     $now,
    //                     $row['source']
    //                 );

    //                 AuditManagementModel::deactivateAuditDataByInstid(
    //                     $row['instid'],
    //                     $userid,
    //                     $now
    //                 );
    //             }
    //             AuditManagementModel::pendingupdation($deptcode, $distcode, $userid, $now);

    //             if($status === 'N') {
    //                 AuditManagementModel::pendingupdation(
    //                     $deptcode,
    //                     $distcode,
    //                     $userid,
    //                     $now
    //                 );

    //                 $finalMessage = 'done';
    //             } else {
    //                 $finalMessage = 'done';
    //             }

    //             DB::commit();

    //             return response()->json([
    //                 'success' => true,
    //                 'message' => $finalMessage
    //             ]);
    //         }
    //         if($status === 'N') {
    //             AuditManagementModel::pendingupdation(
    //                 $deptcode,
    //                 $distcode,
    //                 $userid,
    //                 $now
    //             );
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'done'
    //         ]);
    //     } catch (Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 422);
    //     }
    // }

    // public function spillover_fetchData(Request $request)
    // {
    //     try {
    //         $tempid = null;
    //         if($request->has('tempid')) {
    //             $tempid = Crypt::decryptString($request->tempid);
    //         }

    //         $result = AuditManagementModel::spillover_fetchData($tempid, 'audit.temp_inst_q1_pending');

    //         $data = $result['data'];
    //         $count = $result['count'];

    //         foreach($data as $all) {
    //             $all->encrypted_tempid = Crypt::encryptString($all->tempid);
    //             unset($all->tempid);
    //         }

    //         return response()->json([
    //             'success' => $count > 0,
    //             'message' => $count === 0 ? 'No data found' : '',
    //             'count' => $count,
    //             'data' => $count === 0 ? null : $data
    //         ], $count === 0 ? 404 : 200);
    //     } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Invalid tempid',
    //             'error' => $e->getMessage()
    //         ], 400);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Server Error',
    //             'error' => $e->getMessage(),
    //             'line' => $e->getLine(),
    //             'file' => $e->getFile()
    //         ], 500);
    //     }
    // }

    // public function SendOTP_QT(Request $request)
    // {
    //     $sessiondet = session('user');
    //     $username = $sessiondet->username;
    //     $userid = $sessiondet->userid;
    //     $email = $sessiondet->email;

    //      $otp = rand(100000, 999999);
    //     // $otp = '123456';  // For testing purposes
    //     $data = [
    //         'userid' => $userid,
    //         'email' => $email,
    //         'otp' => $otp,
    //     ];

    //     $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
    //     $sentsms = $auditModel->sendotp_forQT($data, $username);
    //     // return $sentsms;
    //     if($sentsms === 'Message has been sent') {
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'OTP has been sent successfully.'
    //         ]);
    //     } else {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to send OTP. Please try again later.'
    //         ], 500);
    //     }
    // }

    // public function VerifyOTP_QT(Request $request)
    // {
    //     $userOtp = $request->otp;

    //     $sessiondet = session('user');
    //     $userid = $sessiondet->userid;
    //     $email = $sessiondet->email;

    //     $data = [
    //         'userid' => $userid,
    //         'email' => $email,
    //         'otp' => $userOtp,
    //     ];

    //     $storedOtp = SmsmailModel::verifyOTP_QT($data);

    //     if($storedOtp) {
    //         return response()->json(['status' => 'success']);
    //     } else {
    //         return response()->json(['status' => 'error', 'message' => 'Incorrect OTP']);
    //     }
    // }

    // -------------------------------------------Quarter Transaction End----------------------------

    // -------------------------------------------Quarter Transaction Start----------------------------

    private static function getInstChangePlanContext($deptcode = null): array
    {
        $deptcode = $deptcode ?: (session('charge')->deptcode ?? null);
        return AuditManagementModel::getInstChangePlanContext($deptcode);
    }

    private static function getInstChangeQuarterByType(string $quarterType, $deptcode = null): ?string
    {
        $deptcode = $deptcode ?: (session('charge')->deptcode ?? null);
        return AuditManagementModel::getInstChangeQuarterCodeByType($quarterType, $deptcode);
    }

    private function getInstChangeFilterCriteria(Request $request): array
    {
        $request->validate([
            'deptcode' => ['nullable', 'string', 'regex:/^\d+$/'],
            'regioncode' => ['nullable', 'string', 'regex:/^\d+(,\d+)*$/'],
            'distcode' => ['nullable', 'string', 'regex:/^\d+$/'],
        ], [
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $hasRequestFilter = $request->has('filtermode')
            || $request->filled('deptcode')
            || $request->filled('regioncode')
            || $request->filled('distcode');
        $sessionCharge = session('charge');

        if ($hasRequestFilter) {
            return [
                'deptcode' => $request->input('deptcode') ?: null,
                'regioncode' => $request->input('regioncode') ?: null,
                'distcode' => $request->input('distcode') ?: null,
            ];
        }

        return [
            'deptcode' => $sessionCharge->deptcode ?? null,
            'regioncode' => $sessionCharge->regioncode ?? null,
            'distcode' => $sessionCharge->distcode ?? null,
        ];
    }

    private function normalizeInstChangeCodeValue($value): ?string
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        $value = trim((string) ($value ?? ''));
        $value = trim($value, " \t\n\r\0\x0B{}[]()");
        $value = str_replace(['"', "'"], '', $value);
        $value = preg_replace('/\s+/', '', $value);

        return $value !== '' ? $value : null;
    }

    private function instChangeCodeList($value): array
    {
        $normalizedValue = $this->normalizeInstChangeCodeValue($value);

        if (is_array($value)) {
            $values = [];
            foreach ($value as $item) {
                $item = $this->normalizeInstChangeCodeValue($item);
                if ($item !== null) {
                    $values = array_merge($values, preg_split('/\s*,\s*/', $item, -1, PREG_SPLIT_NO_EMPTY));
                }
            }
        } else {
            $values = preg_split('/\s*,\s*/', (string) ($normalizedValue ?? ''), -1, PREG_SPLIT_NO_EMPTY);
        }

        return array_values(array_unique(array_filter(array_map(static function ($item) {
            return trim((string) $item);
        }, $values), static function ($item) {
            return $item !== '';
        })));
    }

    private function singleInstChangeCode($value): ?string
    {
        $codes = $this->instChangeCodeList($value);

        return count($codes) === 1 ? $codes[0] : null;
    }

    private function filterInstChangeOptions($data, string $key, array $allowedCodes)
    {
        $rows = collect($data);
        if (empty($allowedCodes)) {
            return $rows->values();
        }

        return $rows->filter(static function ($row) use ($key, $allowedCodes) {
            return in_array((string) data_get($row, $key), $allowedCodes, true);
        })->values();
    }

    private function instChangePendingStatusForFilters(array $filters): ?string
    {
        return AuditManagementModel::pendinginststatus(
            $filters['deptcode'] ?? null,
            $filters['distcode'] ?? null
        );
    }

    public function instchangeFieldLoader(Request $request)
    {
        $request->merge([
            'deptcode' => $this->normalizeInstChangeCodeValue($request->input('deptcode')),
            'regioncode' => $this->normalizeInstChangeCodeValue($request->input('regioncode')),
        ]);

        $request->validate([
            'field' => ['required', 'string', 'in:department,region,district'],
            'deptcode' => ['nullable', 'string', 'regex:/^\d+$/'],
            'regioncode' => ['nullable', 'string', 'regex:/^\d+(,\d+)*$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
            'in' => 'Invalid field requested.',
        ]);

        $sessionCharge = session('charge');
        $field = $request->input('field');
        $sessionDeptCodes = $this->instChangeCodeList($sessionCharge->deptcode ?? null);
        $sessionRegionCodes = $this->instChangeCodeList($sessionCharge->regioncode ?? null);
        $sessionDistCodes = $this->instChangeCodeList($sessionCharge->distcode ?? null);
        $requestDeptCodes = $this->instChangeCodeList($request->input('deptcode'));
        $requestRegionCodes = $this->instChangeCodeList($request->input('regioncode'));
        $deptcode = count($requestDeptCodes) === 1
            ? $requestDeptCodes[0]
            : $this->singleInstChangeCode($sessionCharge->deptcode ?? null);
        $regioncode = count($requestRegionCodes) === 1
            ? $requestRegionCodes[0]
            : $this->singleInstChangeCode($sessionCharge->regioncode ?? null);

        if ($field === 'department') {
            $data = AuditManagementModel::instchangeDepartmentOptions();
            $data = $this->filterInstChangeOptions($data, 'deptcode', $sessionDeptCodes);
        } elseif ($field === 'region') {
            $data = $deptcode ? AuditManagementModel::instchangeRegionOptions($deptcode) : collect();
            $data = $this->filterInstChangeOptions($data, 'regioncode', $sessionRegionCodes);
        } else {
            $data = ($deptcode && $regioncode)
                ? AuditManagementModel::instchangeDistrictOptions($deptcode, $regioncode)
                : collect();
            $data = $this->filterInstChangeOptions($data, 'distcode', $sessionDistCodes);
        }

        $selectedDeptCode = $this->singleInstChangeCode($sessionCharge->deptcode ?? null);
        $selectedRegionCode = $this->singleInstChangeCode($sessionCharge->regioncode ?? null);
        $selectedDistCode = $this->singleInstChangeCode($sessionCharge->distcode ?? null);

        if ($field === 'department' && empty($selectedDeptCode) && $data->count() === 1) {
            $selectedDeptCode = (string) data_get($data->first(), 'deptcode');
        }

        if ($field === 'region' && empty($selectedRegionCode) && $data->count() === 1) {
            $selectedRegionCode = (string) data_get($data->first(), 'regioncode');
        }

        if ($field === 'district' && empty($selectedDistCode) && $data->count() === 1) {
            $selectedDistCode = (string) data_get($data->first(), 'distcode');
        }

        return response()->json([
            'success' => true,
            'field' => $field,
            'data' => $data,
            'selected' => [
                'deptcode' => $selectedDeptCode,
                'regioncode' => $selectedRegionCode,
                'distcode' => $selectedDistCode,
            ],
            'locked' => [
                'deptcode' => count($sessionDeptCodes) === 1,
                'regioncode' => count($sessionRegionCodes) === 1,
                'distcode' => count($sessionDistCodes) === 1,
            ],
        ]);
    }

    public function verificationLanding()
    {
        $defaultDataVerificationFilters = $this->defaultDataVerificationFilters();
        $dataVerificationFilters = [
            'deptcode' => $defaultDataVerificationFilters['deptcode'],
            'regioncode' => $defaultDataVerificationFilters['regioncode'],
            'distcode' => $defaultDataVerificationFilters['distcode'],
            'quartercode' => $defaultDataVerificationFilters['quartercode'],
            'planmappingid' => $defaultDataVerificationFilters['planmappingid'],
            'prioritycode' => $defaultDataVerificationFilters['prioritycode'] ?: 'null',
        ];

        return view('audit.verification', compact('dataVerificationFilters'));
    }

    public function dataVerification(Request $request, $deptcode, $regioncode, $distcode, $quartercode, $planmappingid, $prioritycode = null)
    {
        $request->merge([
            'deptcode' => $deptcode,
            'regioncode' => $regioncode,
            'distcode' => $distcode,
            'quartercode' => $this->normalizeDataVerificationQuarterCode($quartercode),
            'planmappingid' => $planmappingid,
            'prioritycode' => $this->normalizeDataVerificationPriorityCode($prioritycode),
        ]);
        //          $request->merge([
        //     'deptcode' => '02',
        //     'regioncode' => '27',
        //     'distcode' => '005',
        //     'quartercode' => 'Q1',
        //     'planmappingid' => '35',
        //     'prioritycode' => '',
        // ]);
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
            'distcode' => ['required', 'string', 'regex:/^\d+$/'],
            'quartercode' => ['required', 'string', 'max:20'],
            'planmappingid' => ['required', 'string', 'regex:/^\d+$/'],
            'prioritycode' => ['nullable', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $dataVerificationFilters = [
            'deptcode' => $request->deptcode,
            'regioncode' => $request->regioncode,
            'distcode' => $request->distcode,
            'planmappingid' => $request->planmappingid,
            'planname' => $request->quartercode,
            'quartercode' => $request->quartercode,
            'prioritycode' => $request->prioritycode,
        ];
        $dataVerificationOptions = AuditManagementModel::dataVerificationSelectedOptions(
            $dataVerificationFilters['deptcode'],
            $dataVerificationFilters['regioncode'],
            $dataVerificationFilters['distcode']
        );
        $dataVerificationFrozen = true;

        return view('audit.data_verification', compact(
            'dataVerificationFilters',
            'dataVerificationOptions',
            'dataVerificationFrozen'
        ));
    }

    public function dataVerificationDetails(Request $request)
    {
        $request->merge([
            'quartercode' => $this->normalizeDataVerificationQuarterCode($request->input('quartercode')),
            'prioritycode' => $this->normalizeDataVerificationPriorityCode($request->input('prioritycode')),
        ]);

        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
            'distcode' => ['required', 'string', 'regex:/^\d+$/'],
            'planmappingid' => ['nullable', 'string', 'regex:/^\d+$/'],
            'planname' => ['nullable', 'string', 'max:20'],
            'quartercode' => ['nullable', 'string', 'max:20'],
            'prioritycode' => ['nullable', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $isFinalized = AuditManagementModel::dataVerificationIsFinalized(
            $request->deptcode,
            $request->regioncode,
            $request->distcode,
            $request->planmappingid,
            $request->quartercode,
            $request->prioritycode
        );

        return response()->json([
            'success' => true,
            'users' => $isFinalized
                ? AuditManagementModel::dataVerificationLogUserDetails(
                    $request->deptcode,
                    $request->regioncode,
                    $request->distcode,
                    $request->planmappingid,
                    $request->quartercode
                )
                : AuditManagementModel::dataVerificationUserDetails(
                    $request->deptcode,
                    $request->distcode
                ),
            'institutions' => $isFinalized
                ? AuditManagementModel::dataVerificationLogInstitutionDetails(
                    $request->deptcode,
                    $request->regioncode,
                    $request->distcode,
                    $request->planmappingid,
                    $request->quartercode,
                    $request->prioritycode
                )
                : AuditManagementModel::dataVerificationInstitutionDetails(
                    $request->deptcode,
                    $request->regioncode,
                    $request->distcode,
                    $request->quartercode,
                    $request->prioritycode
                ),
            'is_finalized' => $isFinalized,
            'verification_meta' => $isFinalized
                ? AuditManagementModel::dataVerificationMeta(
                    $request->deptcode,
                    $request->regioncode,
                    $request->distcode,
                    $request->planmappingid,
                    $request->quartercode,
                    $request->prioritycode
                )
                : null,
            'draft_status' => AuditManagementModel::dataVerificationDraftStatus(
                $request->deptcode,
                $request->regioncode,
                $request->distcode,
                $request->planmappingid,
                $request->quartercode,
                $request->prioritycode
            ),
        ]);
    }

    public function dataVerificationSaveDraft(Request $request)
    {
        $request->merge([
            'quartercode' => $this->normalizeDataVerificationQuarterCode($request->input('quartercode')),
            'prioritycode' => $this->normalizeDataVerificationPriorityCode($request->input('prioritycode')),
        ]);

        $request->validate([
            'save_type' => ['required', 'string', 'in:users,institutions'],
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
            'distcode' => ['required', 'string', 'regex:/^\d+$/'],
            'planmappingid' => ['required', 'string', 'regex:/^\d+$/'],
            'quartercode' => ['required', 'string', 'max:20'],
            'prioritycode' => ['nullable', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $savedCount = $request->save_type === 'users'
            ? AuditManagementModel::saveDataVerificationUserDrafts(
                $request->deptcode,
                $request->regioncode,
                $request->distcode,
                $request->planmappingid,
                $request->quartercode
            )
            : AuditManagementModel::saveDataVerificationInstitutionDrafts(
                $request->deptcode,
                $request->regioncode,
                $request->distcode,
                $request->planmappingid,
                $request->quartercode,
                $request->prioritycode
            );

        if ($savedCount <= 0) {
            return response()->json([
                'success' => false,
                'message' => $request->save_type === 'users'
                    ? 'No user details available to save in draft.'
                    : 'No institution details available to save in draft. Please check department, region, district, quarter and priority filters.',
                'count' => $savedCount,
            ], 422);
        }

        $draftStatus = AuditManagementModel::dataVerificationDraftStatus(
            $request->deptcode,
            $request->regioncode,
            $request->distcode,
            $request->planmappingid,
            $request->quartercode,
            $request->prioritycode
        );

        return response()->json([
            'success' => true,
            'message' => $request->save_type === 'users'
                ? 'User details saved in a draft.'
                : 'Institution details saved in a draft.',
            'count' => $savedCount,
            'draft_status' => $draftStatus,
        ]);
    }

    public function dataVerificationPendingTransactions(Request $request)
    {
        $request->merge([
            'quartercode' => $this->normalizeDataVerificationQuarterCode($request->input('quartercode')),
            'prioritycode' => $this->normalizeDataVerificationPriorityCode($request->input('prioritycode')),
        ]);

        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
            'distcode' => ['required', 'string', 'regex:/^\d+$/'],
            'planmappingid' => ['required', 'string', 'regex:/^\d+$/'],
            'quartercode' => ['required', 'string', 'max:20'],
            'prioritycode' => ['nullable', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $rows = AuditManagementModel::dataVerificationPendingTransactions(
            $request->deptcode,
            $request->distcode
        );
        $draftStatus = AuditManagementModel::dataVerificationDraftStatus(
            $request->deptcode,
            $request->regioncode,
            $request->distcode,
            $request->planmappingid,
            $request->quartercode,
            $request->prioritycode
        );
        $tempStatus = AuditManagementModel::dataVerificationTempStatus(
            $request->deptcode,
            $request->regioncode,
            $request->distcode,
            $request->planmappingid,
            $request->quartercode,
            $request->prioritycode
        );

        if (empty($tempStatus['ready_to_finalize'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please update the verification before finalize.',
                'draft_status' => $draftStatus,
                'temp_status' => $tempStatus,
            ], 422);
        }

        $teamAssignmentStatus = AuditManagementModel::dataVerificationTeamAssignmentStatus(
            $request->deptcode,
            $request->distcode,
            $request->planmappingid
        );

        if (empty($teamAssignmentStatus['ready_to_finalize'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete both team assignment details before finalize.',
                'draft_status' => $draftStatus,
                'temp_status' => $tempStatus,
                'team_assignment_status' => $teamAssignmentStatus,
            ], 422);
        }

        // $assignmentMismatchStatus = AuditManagementModel::dataVerificationAssignmentMismatchStatus(
        //     $request->deptcode,
        //     $request->regioncode,
        //     $request->distcode,
        //     $request->planmappingid,
        //     $request->quartercode,
        //     $request->prioritycode
        // );

        // if (empty($assignmentMismatchStatus['matched'])) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Data finalized successfully.',
        //         'draft_status' => $draftStatus,
        //         'temp_status' => $tempStatus,
        //         'team_assignment_status' => $teamAssignmentStatus,
        //         'assignment_mismatch_status' => $assignmentMismatchStatus,
        //     ], 422);
        // }

        return response()->json([
            'success' => true,
            'transactions' => $rows,
            'count' => $rows->count(),
            'draft_status' => $draftStatus,
            'temp_status' => $tempStatus,
            'team_assignment_status' => $teamAssignmentStatus,
        ]);
    }

    public function dataVerificationFinalize(Request $request)
    {
        $request->merge([
            'quartercode' => $this->normalizeDataVerificationQuarterCode($request->input('quartercode')),
            'prioritycode' => $this->normalizeDataVerificationPriorityCode($request->input('prioritycode')),
        ]);

        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
            'distcode' => ['required', 'string', 'regex:/^\d+$/'],
            'planmappingid' => ['required', 'string', 'regex:/^\d+$/'],
            'quartercode' => ['required', 'string', 'max:20'],
            'prioritycode' => ['nullable', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $sessionUser = session('user');
        $verifiedby = $sessionUser->userid ?? null;

        if (empty($verifiedby)) {
            return response()->json([
                'success' => false,
                'message' => 'Session user not found.',
            ], 401);
            }

        $draftStatus = AuditManagementModel::dataVerificationDraftStatus(
            $request->deptcode,
            $request->regioncode,
            $request->distcode,
            $request->planmappingid,
            $request->quartercode,
            $request->prioritycode
        );

        if (empty($draftStatus['ready_to_finalize'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please save draft for both User Details and Institution Details before finalize.',
                'draft_status' => $draftStatus,
            ], 422);
        }

        $tempStatus = AuditManagementModel::dataVerificationTempStatus(
            $request->deptcode,
            $request->regioncode,
            $request->distcode,
            $request->planmappingid,
            $request->quartercode,
            $request->prioritycode
        );

        if (empty($tempStatus['ready_to_finalize'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please update the verification before finalize.',
                'draft_status' => $draftStatus,
                'temp_status' => $tempStatus,
            ], 422);
        }

        $teamAssignmentStatus = AuditManagementModel::dataVerificationTeamAssignmentStatus(
            $request->deptcode,
            $request->distcode,
            $request->planmappingid
        );

        if (empty($teamAssignmentStatus['ready_to_finalize'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete both team assignment details before finalize.',
                'draft_status' => $draftStatus,
                'temp_status' => $tempStatus,
                'team_assignment_status' => $teamAssignmentStatus,
            ], 422);
        }

        // $assignmentMismatchStatus = AuditManagementModel::dataVerificationAssignmentMismatchStatus(
        //     $request->deptcode,
        //     $request->regioncode,
        //     $request->distcode,
        //     $request->planmappingid,
        //     $request->quartercode,
        //     $request->prioritycode
        // );

        // if (empty($assignmentMismatchStatus['matched'])) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Data finalized successfully.',
        //         'draft_status' => $draftStatus,
        //         'temp_status' => $tempStatus,
        //         'team_assignment_status' => $teamAssignmentStatus,
        //         'assignment_mismatch_status' => $assignmentMismatchStatus,
        //     ], 422);
        // }

        $finalizedCounts = AuditManagementModel::finalizeDataVerificationDrafts(
            $request->deptcode,
            $request->regioncode,
            $request->distcode,
            $request->planmappingid,
            $request->quartercode,
            $request->prioritycode,
            $verifiedby
        );

        return response()->json([
            'success' => true,
            'message' => 'Data finalized successfully.',
            'finalized_counts' => $finalizedCounts,
            'temp_status' => $tempStatus,
        ]);
    }

    private function defaultDataVerificationFilters(): array
    {
        return [
            'deptcode' => '02',
            'regioncode' => '26',
            'distcode' => '005',
            'planmappingid' => '35',
            'planname' => 'Q2',
            'quartercode' => 'Q2',
            'prioritycode' =>   '',
        ];
    }

    private function encryptDataVerificationFilters(array $filters): array
    {
        return array_map(function ($value) {
            return Crypt::encryptString((string) $value);
        }, $filters);
    }

    private function decryptDataVerificationFilters(array $filters): array
    {
        $decrypted = [];

        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            try {
                $decrypted[$key] = Crypt::decryptString($value);
            } catch (Exception $e) {
                continue;
            }
        }

        return $decrypted;
    }

    private function normalizeDataVerificationPriorityCode($prioritycode): ?string
    {
        $prioritycode = trim((string) ($prioritycode ?? ''));

        if ($prioritycode === '' || in_array(strtolower($prioritycode), ['null', 'undefined', "''"], true)) {
            return null;
        }

        return $prioritycode;
    }

    private function normalizeDataVerificationQuarterCode($quartercode): ?string
    {
        $quartercode = strtoupper(trim((string) ($quartercode ?? '')));

        return in_array($quartercode, ['Q1', 'Q2', 'Q3', 'Q4'], true) ? $quartercode : null;
    }

    public static function fetch_notscheduledinst()
    {
        $spillous = AuditManagementModel::fetchSpilloverWithCount();
        // dd($spillous);
        $spillovermasterCount = (int) ($spillous['overallInstCount'] ?? 0);
        //
        $pendingmasterRaw = AuditManagementModel::getnotscheduleinstCount();
        $pendingmastercount = is_numeric($pendingmasterRaw) ? (int) $pendingmasterRaw : 0;
        $pendingInstitutions = AuditManagementModel::getnotscheduleinstData();
        $pendingTempResult = AuditManagementModel::penidninst_fetchData($pendingInstitutions, 'audit.temp_inst_q1_pending');
        $pendingTempCount = (int) ($pendingTempResult['count'] ?? 0);

        $pendinginstcheck = AuditManagementModel::pendinginstcheck();
        $planContext = self::getInstChangePlanContext();
        $quarterInfo = (object) $planContext;
        $financialYearMeta = [
            'currentfincode' => $planContext['currentfincode'] ?? null,
            'currentFinancialYear' => $planContext['currentFinancialYear'] ?? null,
            'currentFinancialYearLabel' => $planContext['currentFinancialYearLabel'] ?? '',
            'tofincode' => $planContext['tofincode'] ?? null,
            'toFinancialYear' => $planContext['toFinancialYear'] ?? null,
            'toFinancialYearLabel' => $planContext['toFinancialYearLabel'] ?? '',
            'moveToOptions' => $planContext['moveToOptions'] ?? [],
        ];

        $templateinstitutionlist = AuditManagementModel::getTemplateInstitutionList();
        $templateInstCount = (int) ($templateinstitutionlist['count'] ?? 0);
        $templateTempResult = AuditManagementModel::penidndtemplate_fetchData($templateinstitutionlist['data'] ?? collect(), 'audit.temp_inst_q1_pending');

        $templateTempCount = (int) ($templateTempResult['count'] ?? 0);
        // dd($templateTempCount);
        return view('audit.instchange', [
            'spillovermasterCount' => $spillovermasterCount,
            'pendingmastercount' => $pendingmastercount,
            'pendingTempCount' => $pendingTempCount,
            'pendinginstcheck' => $pendinginstcheck,
            'quarterInfo' => $quarterInfo,
            'templateInstCount' => $templateInstCount,
            'templateTempCount' => $templateTempCount,
            'instChangeFinancialMeta' => $financialYearMeta,
        ]);
    }

    public function instchangeTabData(Request $request)
    {
        try {
            $tab = $request->input('tab');
            if (!in_array($tab, ['spillover', 'pending', 'template'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid tab'
                ], 422);
            }

            $filters = $this->getInstChangeFilterCriteria($request);

            if ($tab === 'spillover') {
                return $this->instchangeSpilloverTab($filters);
            }

            if ($tab === 'pending') {
                return $this->instchangePendingTab($filters);
            }

            return $this->instchangeTemplateTab($filters);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // private function instchangeSpilloverTab()
    // {
    //     $spillous = AuditManagementModel::fetchSpilloverWithCount();
    //     // dd($spillous);
    //     $checkinstdetails = AuditManagementModel::fetchspilloverQuarterDetails($spillous);
    //     $quarterCollection = collect($checkinstdetails['selected_quarters'] ?? [])->keyBy('instid');
    //     $quarterStartsFrom = collect($checkinstdetails['quarter_starts_from'] ?? [])->keyBy('instid');
    //     $spilloverData = collect($spillous['spilloverData'] ?? []);

    //     $rows = $spilloverData->map(function ($row) use ($quarterCollection, $quarterStartsFrom) {
    //         $instid = $row['instid'] ?? null;
    //         $quarterInfo = $quarterStartsFrom[$instid] ?? [];
    //         $quarterDateInfo = $quarterCollection[$instid] ?? null;

    //         return [
    //             'instid' => $instid,
    //             'instename' => $row['instename'] ?? '-',
    //             'team_member_count' => $row['team_member_count'] ?? null,
    //             'mandays' => $row['mandays'] ?? null,
    //             'auditquartercode' => $quarterInfo['auditquartercode'] ?? '-',
    //             'team_head_en' => $row['team_head_en'] ?? '-',
    //             'team_members_en' => $row['team_members_en'] ?? '-',
    //             'fromdate' => $row['fromdate'] ?? null,
    //             'todate' => $row['todate'] ?? null,
    //             'createdon' => $row['createdon'] ?? null,
    //             'entrymeetdate' => $row['entrymeetdate'] ?? null,
    //             'proposedexitmeetdate' => $quarterDateInfo->proposedexitmeetdate ?? null,
    //             'exitmeetdate' => $quarterDateInfo->exitmeetdate ?? null,
    //             'completed_mandays' => $row['completed_mandays'] ?? null,
    //             'remaining_mandays' => $row['remaining_mandays'] ?? null,
    //             'new_remainingmandays' => $row['new_remainingmandays'] ?? null,
    //             'auditquarter' => $row['auditquarter'] ?? null,
    //             'updatedmandays' => $row['updatedmandays'] ?? null,
    //             'remaining_working_days' => $row['remaining_working_days'] ?? null,
    //             'saved_action' => '',
    //             'saved_remarks' => '',
    //         ];
    //     })->values();

    //     return response()->json([
    //         'success' => true,
    //         'tab' => 'spillover',
    //         'masterCount' => (int) ($spillous['overallInstCount'] ?? 0),
    //         'rows' => $rows,
    //     ]);
    // }

    private function instchangeSpilloverTab(array $filters)
    {
        $deptcode = $filters['deptcode'] ?? null;
        $regioncode = $filters['regioncode'] ?? null;
        $distcode = $filters['distcode'] ?? null;
        if (!$deptcode || !$regioncode || !$distcode) {
            return response()->json([
                'success' => true,
                'tab' => 'spillover',
                'masterCount' => 0,
                'pendingStatus' => null,
                'rows' => collect(),
            ]);
        }

        $spillous = AuditManagementModel::fetchSpilloverWithCount($deptcode, $distcode);
        $checkinstdetails = AuditManagementModel::fetchspilloverQuarterDetails($spillous, $deptcode);
        $quarterStartsFrom = collect($checkinstdetails['quarter_starts_from'] ?? [])->keyBy('instid');
        $exitMeetDates = AuditManagementModel::fetchexitmeetdate($spillous, $deptcode)->keyBy('instid');
        $spilloverData = collect($spillous['spilloverData'] ?? []);

        $rows = $spilloverData->map(function ($row) use ($quarterStartsFrom, $exitMeetDates) {
            $instid = $row['instid'] ?? null;
            $quarterInfo = $quarterStartsFrom[$instid] ?? [];
            $exitMeetInfo = $exitMeetDates[$instid] ?? null;

            return [
                'instid' => $instid,
                'instename' => $row['instename'] ?? '-',
                'team_member_count' => $row['team_member_count'] ?? null,
                'mandays' => $row['mandays'] ?? null,
                'auditquartercode' => $quarterInfo['auditquartercode'] ?? '-',
                'team_head_en' => $row['team_head_en'] ?? '-',
                'team_members_en' => $row['team_members_en'] ?? '-',
                'fromdate' => $row['fromdate'] ?? null,
                'todate' => $row['todate'] ?? null,
                'createdon' => $row['createdon'] ?? null,
                'entrymeetdate' => $row['entrymeetdate'] ?? null,
                'proposedexitmeetdate' => $exitMeetInfo->proposedexitmeetdate ?? null,
                'exitmeetdate' => $exitMeetInfo->exitmeetdate ?? null,
                'completed_mandays' => $row['completed_mandays'] ?? null,
                'remaining_mandays' => $row['remaining_mandays'] ?? null,
                'new_remainingmandays' => $row['new_remainingmandays'] ?? null,
                'auditquarter' => $row['auditquarter'] ?? null,
                'updatedmandays' => $row['updatedmandays'] ?? null,
                'remaining_working_days' => $row['remaining_working_days'] ?? null,
                'saved_action' => '',
                'saved_remarks' => '',
            ];
        })->values();

        return response()->json([
            'success' => true,
            'tab' => 'spillover',
            'masterCount' => (int) ($spillous['overallInstCount'] ?? 0),
            'pendingStatus' => $this->instChangePendingStatusForFilters($filters),
            'rows' => $rows,
        ]);
    }

    private function instchangePendingTab(array $filters)
    {
        $deptcode = $filters['deptcode'] ?? null;
        $regioncode = $filters['regioncode'] ?? null;
        $distcode = $filters['distcode'] ?? null;
        if (!$deptcode || !$regioncode || !$distcode) {
            return response()->json([
                'success' => true,
                'tab' => 'pending',
                'masterCount' => 0,
                'tempCount' => 0,
                'pendingStatus' => null,
                'rows' => collect(),
            ]);
        }

        $pendingInstitutions = AuditManagementModel::getnotscheduleinstData($deptcode, $regioncode, $distcode);
        $pendingResult = AuditManagementModel::penidninst_fetchData($pendingInstitutions, 'audit.temp_inst_q1_pending', $deptcode, $regioncode, $distcode);
        $pendingSaved = collect($pendingResult['data'] ?? [])->keyBy('instid');
        $planContext = self::getInstChangePlanContext($deptcode);
        $currentQuarter = self::getInstChangeQuarterByType('CURRENT', $deptcode) ?? 'Q4';
        $pendingmasterRaw = AuditManagementModel::getnotscheduleinstCount($deptcode, $regioncode, $distcode);
        $masterCount = is_numeric($pendingmasterRaw) ? (int) $pendingmasterRaw : 0;

        $rows = collect($pendingInstitutions)->map(function ($inst) use ($pendingSaved) {
            $saved = $pendingSaved[$inst->instid] ?? null;

            return [
                'instid' => $inst->instid,
                'instename' => $inst->instename ?? '-',
                'selected_quarter' => isset($saved->toplanmappingid) ? (string) $saved->toplanmappingid : ($saved->newquartercode ?? null),
                'is_saved' => (bool) $saved,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'tab' => 'pending',
            'masterCount' => $masterCount,
            'tempCount' => (int) ($pendingResult['count'] ?? 0),
            'pendingStatus' => $this->instChangePendingStatusForFilters($filters),
            'currentquarter' => $currentQuarter,
            'phaseLabel' => $planContext['phaseLabel'] ?? '',
            'currentfincode' => $planContext['currentfincode'] ?? null,
            'currentFinancialYear' => $planContext['currentFinancialYear'] ?? null,
            'currentFinancialYearLabel' => $planContext['currentFinancialYearLabel'] ?? '',
            'tofincode' => $planContext['tofincode'] ?? null,
            'toFinancialYear' => $planContext['toFinancialYear'] ?? null,
            'toFinancialYearLabel' => $planContext['toFinancialYearLabel'] ?? '',
            'moveToOptions' => $planContext['moveToOptions'] ?? [],
            'rows' => $rows,
        ]);
    }

    private function instchangeTemplateTab(array $filters)
    {
        $deptcode = $filters['deptcode'] ?? null;
        $regioncode = $filters['regioncode'] ?? null;
        $distcode = $filters['distcode'] ?? null;
        if (!$deptcode || !$regioncode || !$distcode) {
            return response()->json([
                'success' => true,
                'tab' => 'template',
                'masterCount' => 0,
                'tempCount' => 0,
                'pendingStatus' => null,
                'rows' => collect(),
            ]);
        }

        $templateinstitutionlist = AuditManagementModel::getTemplateInstitutionList($deptcode, $regioncode, $distcode);
        $templateInstData = $templateinstitutionlist['data'] ?? collect();
        $templateresults = AuditManagementModel::penidndtemplate_fetchData($templateInstData, 'audit.temp_inst_q1_pending', $deptcode, $regioncode, $distcode);
        $templateSaved = collect($templateresults['data'] ?? [])->keyBy('instid');
        $templateCount = (int) ($templateinstitutionlist['count'] ?? 0);
        $planContext = self::getInstChangePlanContext($deptcode);

        $rows = collect($templateInstData)->map(function ($inst) use ($templateSaved) {
            $saved = $templateSaved[$inst->instid] ?? null;

            return [
                'instid' => $inst->instid,
                'instename' => $inst->instename ?? '-',
                'selected_quarter' => isset($saved->toplanmappingid) ? (string) $saved->toplanmappingid : ($saved->newquartercode ?? null),
                'is_saved' => (bool) $saved,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'tab' => 'template',
            'masterCount' => $templateCount,
            'tempCount' => (int) ($templateresults['count'] ?? 0),
            'pendingStatus' => $this->instChangePendingStatusForFilters($filters),
            'phaseLabel' => $planContext['phaseLabel'] ?? '',
            'currentfincode' => $planContext['currentfincode'] ?? null,
            'currentFinancialYear' => $planContext['currentFinancialYear'] ?? null,
            'currentFinancialYearLabel' => $planContext['currentFinancialYearLabel'] ?? '',
            'tofincode' => $planContext['tofincode'] ?? null,
            'toFinancialYear' => $planContext['toFinancialYear'] ?? null,
            'toFinancialYearLabel' => $planContext['toFinancialYearLabel'] ?? '',
            'moveToOptions' => $planContext['moveToOptions'] ?? [],
            'rows' => $rows,
        ]);
    }

    public function penidninstUpdation(Request $request)
    {
        DB::beginTransaction();

        try {
            $session = session('user');
            $updatemap = session('charge');

            if (!$session || empty($session->userid)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please login again.'
                ], 401);
            }

            $deptcode = $request->input('deptcode') ?: ($updatemap->deptcode ?? null);
            $regioncode = $request->input('regioncode') ?: ($updatemap->regioncode ?? null);
            $distcode = $request->input('distcode') ?: ($updatemap->distcode ?? null);
            $userid = $session->userid;
            $now = View::shared('get_nowtime');

            $action = $request->input('action');
            $status = $request->input('status');

            $pendingRows = $request->input('pending.rows', []);
            $templateRows = $request->input('template.rows', []);

            $spilloverRaw = $request->input('spillover', []);
            $spillous = $request->input('spillous', []);
            // dd($spillous);
            $spillover = isset($spilloverRaw['rows'])
                ? $spilloverRaw['rows']
                : $spilloverRaw;

            $planContext = self::getInstChangePlanContext($deptcode);
            $quarterMap = $planContext['byQuarterCode'] ?? [];
            $moveToOptionMap = $planContext['byOptionValue'] ?? [];
            $currentQuarter = self::getInstChangeQuarterByType('CURRENT', $deptcode);
            $nextQuarter = self::getInstChangeQuarterByType('NEXT', $deptcode);
            $currentPlan = $planContext['current'] ?? null;
            $nextPlan = $planContext['next'] ?? null;
            // dd($nextPlan);
            // print_r($currentQuarter);exit;
            // Spillover is display-only; skip temp table indexing/updates

            // if($action === 'spilldatainsert') {
            //     AuditManagementModel::insertSpilloverInstitutions(
            //         $spilloverIndexedInsert,
            //         $newQuarter,
            //         $userid,
            //         $now
            //     );

            //     DB::commit();

            //     return response()->json([
            //         'success' => true,
            //         'message' => 'spilinsert'
            //     ]);
            // }

            if (in_array($action, ['pendingdatainsert', 'templatedatainsert'])) {
                foreach ($spillous as $row) {
                    if (empty($row['instid'])) {
                        continue;
                    }

                    $selectedQuarter = $row['quarterchange'] ?? null;
                    $currentQuarterForRow = $row['currentquarter'] ?? $currentQuarter;
                    $fromPlan = $currentPlan;
                    $toPlan = $moveToOptionMap[$selectedQuarter] ?? ($quarterMap[$selectedQuarter] ?? $fromPlan);
                    $effectiveQuarterCode = $toPlan['auditquartercode'] ?? $selectedQuarter;
                    $effectiveToPriority = (($toPlan['auditquartercode'] ?? null) === $effectiveQuarterCode)
                        ? ($toPlan['prioritycode'] ?? null)
                        : ($fromPlan['prioritycode'] ?? null);

                    $data = [
                        'instid' => $row['instid'],
                        'quartercode' => $currentQuarter,
                        'pendingflag' => 'D',
                        'newquartercode' => $effectiveQuarterCode,
                        'yearofaudit' => '2026',
                        'createdon' => $now,
                        'createdby' => $userid,
                        'updatedon' => $now,
                        'updatedby' => $userid,
                        'spilloverflag' => 'N',
                        'toprioritycode' => $effectiveToPriority,
                        'fromprioritycode' => $fromPlan['prioritycode'] ?? null,
                        'fromfinyearcode' => $fromPlan['financialyearcode'] ?? null,
                        'tofinyearcode' => $toPlan['financialyearcode'] ?? ($nextPlan['financialyearcode'] ?? null),
                        'fromplanmappingid' => $fromPlan['planmappingid'] ?? null,
                        'toplanmappingid' => $toPlan['planmappingid'] ?? null,
                        'auditmode' => $action === 'pendingdatainsert' ? null : 'T',
                    ];

                    $result = AuditManagementModel::UpdateNotscheduledData(
                        $data,
                        null,
                        'audit.temp_inst_q1_pending'
                    );

                    if (!$result['status']) {
                        throw new Exception($result['message']);
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'pendinginsert'
                ]);
            }

            if ($action === 'finalize') {
                AuditManagementModel::updateInstitutionExistingQuarter();

                $allRows = collect($pendingRows)
                    ->map(fn($r) => $r + ['source' => 'pending'])
                    ->merge(
                        collect($templateRows)->map(fn($r) => $r + ['source' => 'template'])
                    )
                    ->values();
                foreach ($allRows as $row) {
                    if (empty($row['instid'])) {
                        continue;
                    }

                    $selectedQuarter = $row['quarterchange'] ?? null;
                    $currentQuarterForRow = $row['currentquarter'] ?? $currentQuarter;
                    $fromPlan = $currentPlan;
                    $toPlan = $moveToOptionMap[$selectedQuarter] ?? ($quarterMap[$selectedQuarter] ?? $fromPlan);
                    $effectiveQuarterCode = $toPlan['auditquartercode'] ?? $selectedQuarter;
                    $effectiveToPriority = (($toPlan['auditquartercode'] ?? null) === $effectiveQuarterCode)
                        ? ($toPlan['prioritycode'] ?? null)
                        : ($fromPlan['prioritycode'] ?? null);
                    // dd($effectiveToPriority);
                    $data = [
                        'instid' => $row['instid'],
                        'quartercode' => $currentQuarter,
                        'pendingflag' => 'F',
                        'newquartercode' => $effectiveQuarterCode,
                        'yearofaudit' => '2026',
                        'createdon' => $now,
                        'createdby' => $userid,
                        'updatedon' => $now,
                        'updatedby' => $userid,
                        'spilloverflag' => 'N',
                        'toprioritycode' => $toPlan['prioritycode'] ?? ($nextPlan['prioritycode'] ?? null),
                        'fromprioritycode' => $fromPlan['prioritycode'] ?? null,
                        'fromfinyearcode' => $fromPlan['financialyearcode'] ?? null,
                        'tofinyearcode' => $toPlan['financialyearcode'] ?? null,
                        'fromplanmappingid' => $fromPlan['planmappingid'] ?? null,
                        'toplanmappingid' => $toPlan['planmappingid'] ?? null,
                        'auditmode' => $action === 'pendingdatainsert' ? null : 'T',
                    ];

                    $result = AuditManagementModel::UpdateNotscheduledData(
                        $data,
                        null,
                        'audit.temp_inst_q1_pending'
                    );

                    if (!$result['status']) {
                        throw new Exception($result['message']);
                    }
                    //  dd($row['quarterchange']);
                    AuditManagementModel::updateInstitutionQuarter(
                        [$row['instid']],
                        $effectiveQuarterCode,
                        $currentQuarter,
                        $fromPlan['planmappingid'] ?? null,
                        $toPlan['planmappingid'] ?? null,
                        $userid,
                        $now,
                        $row['source']
                    );

                    DB::table('audit.mst_institution')
                        ->where('instid', $row['instid'])
                        ->update([
                            'inst_priority_kms' => $effectiveToPriority,
                            'updatedon' => $now,
                            'updatedby' => $userid,
                        ]);

                    AuditManagementModel::deactivateAuditDataByInstid(
                        $row['instid'],
                        $userid,
                        $now
                    );
                }
                if (!$deptcode || !$distcode) {
                    throw new Exception('Please select a district.');
                }

                AuditManagementModel::pendingupdation($deptcode, $distcode, $userid, $now);

                if ($status === 'N') {
                    AuditManagementModel::pendingupdation(
                        $deptcode,
                        $distcode,
                        $userid,
                        $now
                    );

                    $finalMessage = 'done';
                } else {
                    $finalMessage = 'done';
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => $finalMessage
                ]);
            }
            if ($action === 'finalize_empty' || $status === 'N') {
                if (!$deptcode || !$distcode) {
                    throw new Exception('Please select a district.');
                }

                AuditManagementModel::pendingupdation(
                    $deptcode,
                    $distcode,
                    $userid,
                    $now
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'done'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function SendOTP_QT(Request $request)
    {
        $sessiondet = session('user');
        $username = $sessiondet->username;
        $userid = $sessiondet->userid;
        $email = $sessiondet->email;

        // $otp = rand(100000, 999999);
        $otp = '123456';  // For testing purposes
        $data = [
            'userid' => $userid,
            'email' => $email,
            'otp' => $otp,
        ];

        $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
        $sentsms = $auditModel->sendotp_forQT($data, $username);
        // return $sentsms;
        if ($sentsms === 'Message has been sent') {
            return response()->json([
                'status' => 'success',
                'message' => 'OTP has been sent successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send OTP. Please try again later.'
            ], 500);
        }
    }

    public function VerifyOTP_QT(Request $request)
    {
        $userOtp = $request->otp;

        $sessiondet = session('user');
        $userid = $sessiondet->userid;
        $email = $sessiondet->email;

        $data = [
            'userid' => $userid,
            'email' => $email,
            'otp' => $userOtp,
        ];

        $storedOtp = SmsmailModel::verifyOTP_QT($data);

        if ($storedOtp) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Incorrect OTP']);
        }
    }

    public function SendOTP_DataVerification(Request $request)
    {
        $sessiondet = session('user');
        $username = $sessiondet->username;
        $userid = $sessiondet->userid;
        $email = $sessiondet->email;

        // $otp = rand(100000, 999999);
        $otp = '123456';  // For testing purposes
        $data = [
            'userid' => $userid,
            'email' => $email,
            'otp' => $otp,
        ];

        $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
        $sentsms = $auditModel->sendotp_forDataVerification($data, $username);

        if ($sentsms === 'Message has been sent') {
            return response()->json([
                'status' => 'success',
                'message' => 'OTP has been sent successfully.'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send OTP. Please try again later.'
        ], 500);
    }

    public function VerifyOTP_DataVerification(Request $request)
    {
        $userOtp = $request->otp;

        $sessiondet = session('user');
        $userid = $sessiondet->userid;
        $email = $sessiondet->email;

        $data = [
            'userid' => $userid,
            'email' => $email,
            'otp' => $userOtp,
        ];

        $storedOtp = SmsmailModel::verifyOTP_DataVerification($data);

        if ($storedOtp) {
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Incorrect OTP']);
    }

    // -------------------------------------------Quarter Transaction End----------------------------

    // ----------------------------------------------------------------------------SpillOver Revoke--------------------------------------

    public static function fetchdeptartment(request $request)
    {
        $dept = AuditManagementModel::deptfetch();

        return view('audit.spilloverrevoke', compact('dept'));
    }

    public function fetchRegion(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $deptcode = $request->input('deptcode');

        $regions = AuditManagementModel::regionfetch($deptcode);
        // print_r($regions);

        return response()->json([
            'success' => true,
            'data' => $regions
        ]);
    }

    public function fetch_districts(Request $request)
    {
        $request->validate([
            'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $regioncode = $request->input('regioncode');

        $dist = AuditManagementModel::fetch_districts($regioncode);
        // print_r($regions);

        return response()->json([
            'success' => true,
            'data' => $dist
        ]);
    }

    public function fethInstituions_spilloverrevoke(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'regex:/^\d+$/'],
            'regioncode' => ['required', 'regex:/^\d+$/'],
            'distcode' => ['required', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
        ]);
        $deptcode = $request->input('deptcode');
        $regioncode = $request->input('regioncode');
        $distcode = $request->input('distcode');

        $inst = AuditManagementModel::fetch_institutions($deptcode, $regioncode, $distcode);
        // dd($inst);

        return response()->json([
            'success' => true,
            'data' => $inst
        ]);
    }

    public function spilloverRevokeCheck(Request $request)
    {
        $request->validate([
            'instid' => ['required', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
        ]);

        $instid = $request->input('instid');

        $details = AuditManagementModel::getSpilloverRevokeDetails($instid);

        if (!$details) {
            return response()->json(['success' => false, 'message' => 'Institution details not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $details
        ]);
    }

    public function spilloverRevokeUpdate(Request $request)
    {
        try {
            $request->validate([
                'instid' => ['required', 'regex:/^\d+$/'],
                'revoke' => ['required', 'in:Y,N'],
                'otpverified' => ['nullable', 'in:Y,N'],
                'remarks' => ['required', 'string', 'min:10', 'max:150'],
            ], [
                'required' => 'The :attribute field is required.',
            ]);

            $instid = $request->input('instid');
            $revokeFlag = $request->input('revoke');
            $otpVerified = $request->input('otpverified', 'N');
            $remarks = preg_replace('/\s+/', ' ', trim($request->input('remarks')));

            $details = AuditManagementModel::getSpilloverRevokeDetails($instid);
            if (!$details) {
                return response()->json(['success' => false, 'message' => 'Institution details not found'], 404);
            }

            if (($details->spillovercompleted ?? 'N') === 'Y' && $otpVerified !== 'Y') {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP verification is required for completed spillover institution.'
                ], 422);
            }

            $sessionUser = session('user');
            $userId = $sessionUser->userid ?? null;
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Session details not found'], 401);
            }

            $updated = AuditManagementModel::updateSpilloverRevokeFlagByInstid($instid, $revokeFlag, $userId, $remarks);
            $successMessage = (($updated['spillovercompleted'] ?? 'N') === 'Y')
                ? 'Institution updated as Spillover Completed successfully.'
                : 'Institution updated as Carry Forward to Next Quarter successfully.';

            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'data' => $updated
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function SendOTP_revokespill(Request $request)
    {
        $request->validate([
            'instid' => ['required', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
        ]);

        $sessiondet = session('user');
        $username = $sessiondet->username;
        $userid = $sessiondet->userid;
        // $email = $sessiondet->email;
        $email = View::shared('spillrevoke_Mail');
        $otp = rand(100000, 999999);
        // $otp = '123456';  // For testing purposes
        $data = [
            'userid' => $userid,
            'email' => $email,
            'otp' => $otp,
        ];

        $instid = $request->input('instid');
        $details = AuditManagementModel::getSpilloverRevokeDetails($instid);
        if (empty($details) || empty($details->auditscheduleid)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Institution schedule not found'
            ], 404);
        }

        $audit_scheduleid = $details->auditscheduleid;

        $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
        $isNtoY = (($details->spillovercompleted ?? 'N') === 'N');
        $instDetails = [
            'instname' => $details->instename ?? '-',
            'show_full_details' => $isNtoY ? 'N' : 'Y',
            'teamsize' => $isNtoY ? '' : (int) ($details->teamsize ?? 0),
            'working_days' => $isNtoY ? '' : (int) ($details->working_days ?? 0),
            'remaining_mandays' => $isNtoY ? '' : (int) ($details->recalculated_remaining_mandays ?? ($details->remainingmandays ?? 0)),
            'total_working_mandays' => $isNtoY ? '' : (((int) ($details->teamsize ?? 0)) * ((int) ($details->working_days ?? 0))),
        ];

        $sentsms = $auditModel->sendotp_forspillrevoke($data, $username, $instDetails);

        // $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
        // SmsmailModel::saveOTP($data);
        // $sentsms = $auditModel->sendIntimation($audit_scheduleid);

        $isSuccess = false;
        if ($sentsms === 'Message has been sent') {
            $isSuccess = true;
        } elseif (is_array($sentsms) && (($sentsms['status'] ?? null) == 100)) {
            $isSuccess = true;
        }

        if ($isSuccess) {
            return response()->json([
                'status' => 'success',
                'message' => 'OTP has been sent successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send OTP. Please try again later.'
            ], 500);
        }
    }

    public function VerifyOTP_revokespill(Request $request)
    {
        $userOtp = $request->otp;

        $sessiondet = session('user');
        $userid = $sessiondet->userid;
        // $email = $sessiondet->email;
        $email = View::shared('spillrevoke_Mail');
        $data = [
            'userid' => $userid,
            'email' => $email,
            'otp' => $userOtp,
        ];

        $storedOtp = SmsmailModel::VerifyOTP_revokespill($data);

        if ($storedOtp) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Incorrect OTP']);
        }
    }

    // ----------------------------------------------------------------------------SpillOver Revoke--------------------------------------

    /* Manual Plan - End */

    // ---------------------------------SMS Function----------------------------------//
    public function SendOTP_allocatePlan(Request $request)
    {
        $sessiondet = session('user');
        $username = $sessiondet->username;
        $userid = $sessiondet->userid;
        $email = $sessiondet->email;

        // $email = 'nijisa18@gmail.com';

        // $email =$sessiondet->email;

        $otp = rand(100000, 999999);  // 6-digit OTP

        $data = [
            'userid' => $userid,
            'email' => $email,
            'otp' => $otp,
        ];

        $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
        $sentsms = $auditModel->sendotp_allocateplan($data, $username);
        // return $sentsms;
        if ($sentsms === 'Message has been sent') {
            // Session::put('customer_otp', $otp);

            return response()->json([
                'status' => 'success',
                'message' => 'OTP has been sent successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send OTP. Please try again later.'
            ], 500);  // 500 Internal Server Error
        }
    }

    public function VerifyOTP_allocatePlan(Request $request)
    {
        $userOtp = $request->otp;

        $sessiondet = session('user');
        $userid = $sessiondet->userid;
        $email = $sessiondet->email;

        $data = [
            'userid' => $userid,
            'email' => $email,
            'otp' => $userOtp,
        ];

        $storedOtp = SmsmailModel::verifyOTP($data);

        if ($storedOtp) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Incorrect OTP']);
        }
    }

    //  public function checkexitmeetstatus(Request $request)
    //     {
    //         $validatedData = $request->validate([
    //             'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
    //             'distcode' => ['required', 'string', 'regex:/^\d+$/'],
    //         ], [
    //             'required' => 'The :attribute field is required.',
    //             'regex'    => 'The :attribute field must contain only numbers.',
    //         ]);

    //         try {
    //             $deptcode = $request->deptcode;
    //             $distcode = $request->distcode;

    //             $exitMeetStatus = AuditManagementModel::checkexitmeetstatus($deptcode, $distcode);

    //             if ($exitMeetStatus) {
    //                 return response()->json([
    //                     'status' => 'error',
    //                     'message' => 'Some institutions have not entered exit meet date',
    //                     'error' => 500
    //                 ], 500);
    //             }

    //             $templateStatus = AuditManagementModel::checkTemplateAudit($deptcode, $distcode);

    //             if ($templateStatus) {
    //                 return response()->json([
    //                     'status' => 'error',
    //                     'message' => 'Some institutions have not finalized template audit',
    //                     'error' => 500
    //                 ], 500);
    //             }

    //             return response()->json([
    //                 'status' => 'success',
    //                 'message' => 'All institutions have completed exit meetings'
    //             ]);

    //         } catch (ValidationException $e) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => $e->getMessage(),
    //                 'error' => 401
    //             ], 401);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => $e->getMessage(),
    //                 'error' => 409
    //             ], 409);
    //         }
    //     }

    public function checkexitmeetstatus(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'distcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must contain only numbers.',
        ]);

        try {
            $deptcode = $request->deptcode;
            $distcode = $request->distcode;

            $plan_config_details = CommonModel::getplandetails($deptcode);

            $exitMeetStatus = AuditManagementModel::checkexitmeetstatus($deptcode, $distcode, $plan_config_details);

            if ($exitMeetStatus) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Some institutions have not entered exit meet date',
                    'error' => 500
                ], 500);
            }

            $templateStatus = AuditManagementModel::checkTemplateAudit($deptcode, $distcode);

if ($templateStatus) {
    return response()->json([
        'status' => 'error',
        'message' => 'Some institutions have not finalized template audit',
        'error' => 500
    ], 500);
}

            // $performanceStatus = AuditManagementModel::checkperformanceAudit($deptcode, $distcode);

            // if ($performanceStatus) {
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'Some institutions have not finalized performance audit',
            //         'error' => 500
            //     ], 500);
            // }

            return response()->json([
                'status' => 'success',
                'message' => 'All institutions have completed exit meetings'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'error' => 401
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'error' => 409
            ], 409);
        }
    }

    /* Check List Audit Plan ******************************************************************************* */

    //    public function checkisteamassigned(Request $request)
    //     {
    //         $validatedData = $request->validate([
    //             'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
    //             'distcode' => ['required', 'string', 'regex:/^\d+$/'],
    //         ], [
    //             'required' => 'The :attribute field is required.',
    //             'regex' => 'The :attribute field must be a valid field.',
    //         ]);
    //         try {
    //             $deptcode = $request->deptcode;
    //             $distcode = $request->distcode;

    //             $quartercode = AuditManagementModel::getauditquarter($deptcode);

    //             $isPlanfinalized = AuditManagementModel::checkisPlanfinalized($deptcode, $distcode);
    //             $spilloverinststatus = $isPlanfinalized[0]->spilloverinststatus;
    //             $planstatus = $isPlanfinalized[0]->autoplanstatus;
    //             $pendinginststatus = $isPlanfinalized[0]->pendinginststatus;

    //             $isteamassigned = AuditManagementModel::checkisteamassigned($deptcode, $distcode);

    //             if ($planstatus == 'F') {
    //                 $finalisedplanData = AuditManagementModel::getAuditorsfromplan($deptcode, $distcode, $quartercode);
    //                 $finalisedtemplateData = AuditManagementModel::getTemplateplan($deptcode, $distcode, $quartercode);

    //                 // return $finalisedplanData;

    //                 return response()->json(['success' => true, 'templatedata' => $finalisedtemplateData, 'planned_auditors' => $finalisedplanData, 'teamassignedstatus' => $isteamassigned, 'planstatus' => $planstatus, 'executingquartercode' => $quartercode]);
    //             } else if ($planstatus == 'N' || $planstatus == 'Y' || $planstatus == '') {
    //                 // if ($isteamassigned) {
    //                     $getalldetails = AuditManagementModel::getalldetails($deptcode, $distcode, $quartercode);
    //                     // return  $getalldetails;
    //                     //   return $getalldetails;
    //                     $totalinstcount = $getalldetails[0];

    //                     // If you need it as array:
    //                     $data = (array) $totalinstcount;

    //                     // Step 1: Extract the JSON string
    //                     $jsonString = $data['checklistdel'];

    //                     // Step 2: Decode the JSON string into an array
    //                     $checklist = json_decode($jsonString, true);

    //                     // Step 3: Access the decoded data (it's an array with 1 object)
    //                     $details = $checklist[0];

    //                     // return $details;

    //                     // Now you can access values
    //                     $institutionCount = $details['institutioncount_normal'];
    //                     $templateinstcount = $details['institutioncount_template'];
    //                     $auditorCount = $details['auditorcount'];
    //                     $designationDetails = $details['designationdel'];
    //                     $teamCombinations = $details['teamcombination'];
    //                     $teamallocation = $details['teamallocation'];
    //                     $idelauditorslist = $details['idelauditorslist'];
    //                     $idelinstitutionlist = $details['idelinstitutionlist_normal']['json_agg'];
    //                     $templateidelinstitutionlist = $details['idelinstitutionlist_template']['json_agg'];

    //                     $totalworkingdays = $details['totalworkingdays'];
    //                     $sumofinstmandays = $details['sumofinstmandays'];
    //                     $neededmandays = $details['neededmandays'];
    //                     $allocatedmandays = $details['allocatedmandays_normal'];
    //                     $templateallocatedmandays = $details['allocatedmandays_template'];

    //                     $quarterfromdate = $details['quarterfromdate'];
    //                     $quartertodate = $details['quartertodate'];

    //                     $teamdet = AuditManagementModel::getchecklistteamdet($deptcode, $distcode, $quartercode);
    //                     $AuditorInst = AuditManagementModel::getAuditorsInstdet($deptcode, $distcode, $quartercode);
    //                     $getallocdet_temp = $details['templateuser']['json_agg'];
    //                     $template_instdet = AuditManagementModel::getallocdet_temp($deptcode, $distcode, $quartercode);
    //                      $performanceinstcount = $details['precount_normal'];
    //                     $performance_idlelist = $details['preidelauditorslist'];
    //                     $performanceInstdet = $AuditorInst['performanceinst_det'];
    //                     //    return  $template_instdet;
    //                     //  $mandaysDetais =AuditManagementModel::getmandaysDetais($deptcode, $distcode, $quartercode);
    //                     // //return $teamdet;

    //                     return response()->json(
    //                         [
    //                             'success' => true,
    //                             'templateidelinstitutionlist' => $templateidelinstitutionlist,
    //                             'template_instdet' => $template_instdet,
    //                             // 'checklistdetails' => $details,
    //                             'templateallocatedmandays' => $templateallocatedmandays,
    //                             'templateinstcount' => $templateinstcount,
    //                             'planstatus' => $planstatus,
    //                             'totalworkingdays' => $totalworkingdays,
    //                             'sumofinstmandays' => $sumofinstmandays,
    //                             'neededmandays' => $neededmandays,
    //                             'allocatedmandays' => $allocatedmandays,
    //                             'quarterfromdate' => $quarterfromdate,
    //                             'quartertodate' => $quartertodate,
    //                             // 'mandaysdet'=>$mandaysDetais,
    //                             'teamassignedstatus' => $isteamassigned,
    //                             'teamdet' => $teamCombinations,
    //                             'totalteamdetails' => $teamallocation,
    //                             'idelusers' => $idelauditorslist,
    //                             'idleinst' => $idelinstitutionlist,
    //                             'totalinstcount' => $institutionCount,
    //                             'totalauditorscount' => $auditorCount,
    //                             'designationDetails' => $designationDetails,
    //                             'distname' => $teamdet['distname'],
    //                             'deptname' => $teamdet['deptname'],
    //                             'users' => $AuditorInst['users'],
    //                             'inst_det' => $AuditorInst['inst_det'],
    //                             'allocdet_temp' => $getallocdet_temp,
    //                             'pendinginststatus' => $pendinginststatus,
    //                         'spilloverinstatus' => $spilloverinststatus,
    //                          'performanceInstdet'  => $performanceInstdet,
    //                         'performance_idlelist' => $performance_idlelist,
    //                         'performanceinstcount' => $performanceinstcount,
    //                         ]
    //                     );
    //                 // } else {
    //                   //  $teamdet = null;
    //                   //  return response()->json(['success' => true, 'teamassignedstatus' => $isteamassigned, 'pendinginststatus' => $pendinginststatus, 'spilloverinstatus' => $spilloverinststatus]);
    //                // }
    //             }
    //         } catch (ValidationException $e) {
    //             return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
    //         } catch (\Exception $e) {
    //             return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
    //         }
    //     }

     public function checkisteamassigned(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'distcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid field.',
        ]);
        try {
            $deptcode = $request->deptcode;
            $distcode = $request->distcode;

            $session = session('user');
            $loginid = $session->loginid;

            $planConfig   = CommonModel::getplandetails($deptcode)[0];

            $quartercode  = $planConfig->auditquartercode;
            $prioritycode = $planConfig->prioritycode;

            $isPlanfinalized    =  AuditManagementModel::checkisPlanfinalized($deptcode, $distcode)[0];


            $planstatus         =  $isPlanfinalized->autoplanstatus;
            $pendinginststatus  =  $isPlanfinalized->pendinginststatus;

            $isteamassigned = AuditManagementModel::checkisteamassigned($deptcode, $distcode);


            if ($planstatus === 'F') {

                $plannedAuditors = AuditManagementModel::getAuditorsfromplan(
                    $deptcode,
                    $distcode,
                    $quartercode,
                    $prioritycode
                );

                $templateData = AuditManagementModel::getTemplateplan(
                    $deptcode,
                    $distcode,
                    $quartercode,
                    $prioritycode
                );

                return response()->json([
                    'success'              => true,
                    'templatedata'         => $templateData,
                    'planned_auditors'     => $plannedAuditors,
                    'teamassignedstatus'   => $isteamassigned,
                    'planstatus'           => $planstatus,
                    'executingquartercode' => $quartercode,
                ]);
            }

            //If plan isin cheklist  this part executes

            $getalldetails = AuditManagementModel::getalldetails(
                $deptcode,
                $distcode,
                $loginid
            );

            $checklist = json_decode(
                $getalldetails[0]->checklistdel,
                true
            );

            // return $checklist;

            if (($checklist['status'] ?? '') === 'Error') {
                return response()->json([
                    'message' => $checklist['message'],
                    'code'    => $checklist['code'],
                    'error'   => $checklist['status']
                ], 403);
            }


            $details = $checklist;
            //return  $planConfig;
            $AuditorInst      = AuditManagementModel::getAuditorsInstdet($deptcode, $distcode, $quartercode, $planConfig);
            // return $AuditorInst;
            $template_instdet = AuditManagementModel::getallocdet_temp($deptcode, $distcode, $quartercode, $planConfig);

            $planItems = collect([
                'planwithdistance' => 'Distance',
                'templateaudit' => 'Template Audit',
                'performanceaudit' => 'Performance Audit',
                'withhubspoke' => 'Hub & Spoke'
            ])->filter(
                fn($label, $field) => ($details[$field] ?? 'N') === 'Y'
            )->values()->toArray();

            return response()->json([
                'success' => true,

                'plannedinst_count' => $details['institutionallocatedcount_normal'] ?? 0,
                'ideleinst_count'   => $details['institutionidelcount_normal'] ?? 0,


                'verifiedplandetails' => $details['verifiedplandetails'] ?? null,
                'planquarter' => $details['planquarter'] ?? null,
                'planmappingid' => $details['planmappingid'] ?? null,
                'prioritycode' => $details['prioritycode'] ?? null,

                'planstatus' => $planstatus,
                'teamassignedstatus' => $isteamassigned,
                'pendinginststatus'  => $pendinginststatus,

                'planname' => ($details['planname'] ?? '') .
                    ' - (' . ($details['financialyear'] ?? '') . ')',

                'plan_items' => $planItems,

                'lastworkingdate' => $details['lastworkingdate'] ?? null,
                'lastplanruntime' => $details['lastplanruntime'] ?? null,
                'preference_list' => $details['perferenceorder'] ?? null,

                'totalinstcount' => $details['institutioncount_normal'] ?? 0,
                'templateinstcount' => $details['institutioncount_template'] ?? 0,
                'totalauditorscount' => $details['auditorcount'] ?? 0,

                'totalworkingdays' => $details['totalworkingdays'] ?? 0,
                'sumofinstmandays' => $details['sumofinstmandays'] ?? 0,
                'neededmandays' => $details['neededmandays'] ?? 0,

                'allocatedmandays' => $details['allocatedmandays_normal'] ?? 0,
                'allocatedmandays_performance' => $details['_allocatedmandays_performance'] ?? 0,
                'templateallocatedmandays' => $details['allocatedmandays_template'] ?? 0,

                'quarterfromdate' => $details['quarterfromdate'] ?? null,
                'quartertodate' => $details['quartertodate'] ?? null,

                'teamdet' => $details['teamcombination'] ?? [],
                'totalteamdetails' => $details['teamallocation'] ?? [],
                'designationDetails' => $details['designationdel'] ?? [],

                'idelusers' => $details['idelauditorslist'] ?? [],
                'idleinst' => $details['idelinstitutionlist_normal'] ?? [],
                'templateidelinstitutionlist' => $details['idelinstitutionlist_template'] ?? [],

                'performanceinstcount' => $details['precount_normal'] ?? 0,
                'performance_idlelist' => $details['preidelauditorslist'] ?? [],
                'performanceInstdet'   => $AuditorInst['performanceinst_det'] ?? [],



                'users' => $AuditorInst['users'],
                'inst_det'         => $AuditorInst['inst_det'],
                'plannedinst_det'  => $AuditorInst['plannedinst_det'],
                'idleinst_det'     => $AuditorInst['idleinst_det'],

                'allocdet_temp' => $details['templateuser'] ?? null,
                'template_instdet' => $template_instdet,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    //     public function assignteams(Request $request)
    //     {

    //         $validatedData = $request->validate([
    //             'deptcode'              => ['required', 'string', 'regex:/^\d+$/'],
    //             'distcode'              => ['required', 'string', 'regex:/^\d+$/'],

    //         ], [
    //             'required' => 'The :attribute field is required.',
    //             'regex'    => 'The :attribute field must be a valid field.',

    //         ]);
    //         try {
    //             $deptcode = $request->deptcode;
    //             $distcode = $request->distcode;

    //             $getauditquarter = AuditManagementModel::getauditquarter($deptcode);
    //             // return $getauditquarter;
    //             //$distcode = 'd';
    //             $quartercode = $getauditquarter;

    //             $assignteams = AuditManagementModel::assignteams($deptcode, $distcode, $quartercode);

    //           $isteamassigned = AuditManagementModel::checkisteamassigned($deptcode, $distcode);
    //           $template_instdet = AuditManagementModel::getallocdet_temp($deptcode, $distcode, $quartercode);
    // return response()->json(['success' => true, 'data' => $assignteams[0], 'teamassignedstatus' => $isteamassigned,'template_instdet' => $template_instdet]);

    //         } catch (ValidationException $e) {
    //             return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
    //         } catch (\Exception $e) {
    //             return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
    //         }
    //     }

   public function assignteams(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'distcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid field.',
        ]);
        try {
            $session    =   session('user');
            $loginid    =   $session->loginid;
            $deptcode   =   $request->deptcode;
            $distcode   =   $request->distcode;

            $getauditquarter = CommonModel::getplandetails($deptcode)[0];

            $quartercode = $getauditquarter->auditquartercode;

            $assignteams = AuditManagementModel::assignteams($deptcode, $distcode, $quartercode, $loginid)[0];

            $teams_assign_det = $assignteams->fn_auditplan_final;

            $assigned_data = json_decode($teams_assign_det, true);

            if ($assigned_data['status'] == 'Error') {
                return response()->json([
                    'message' => $assigned_data['message'],
                    'code' => $assigned_data['code'],
                    'error' => $assigned_data['status']
                ], 403);
            }

            $isteamassigned = AuditManagementModel::checkisteamassigned($deptcode, $distcode);

            $template_instdet = AuditManagementModel::getallocdet_temp($deptcode, $distcode, $quartercode, $getauditquarter);

            return response()->json(['success' => true, 'data' => $assigned_data, 'teamassignedstatus' => $isteamassigned, 'template_instdet' => $template_instdet]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }


    // public static function finaliseplan(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'deptcode'              => ['required', 'string', 'regex:/^\d+$/'],
    //         'distcode'              => ['required', 'string', 'regex:/^\d+$/'],

    //     ], [
    //         'required' => 'The :attribute field is required.',
    //         'regex'    => 'The :attribute field must be a valid field.',

    //     ]);

    //     try {
    //         $deptcode = $request->deptcode;
    //         $distcode = $request->distcode;

    //         $quartercode = AuditManagementModel::getauditquarter($deptcode);

    //         // return 'finalise'

    //         $finailiseddata = AuditManagementModel::finaliseplan($deptcode, $distcode);

    //         $finalisedet = $finailiseddata[0]->distributeauditteamplan;
    //         $finalisedet = json_decode($finalisedet, true); // now it's an array
    //         $finalisestatus = $finalisedet['status'];       // ✅ this will now work

    //         if ($finalisestatus == 'error') {
    //             return response()->json(['message' =>  $finalisedet['message'], 'error' => 500], 500);
    //         } else {
    //             $finalisedplanData = AuditManagementModel::getAuditorsfromplan($deptcode, $distcode, $quartercode);
    //             return response()->json(['success' => true, 'planned_auditors' => $finalisedplanData, 'data' => $finalisestatus, 'executingquartercode' => $quartercode]);
    //         }
    //     } catch (ValidationException $e) {
    //         return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
    //     }
    // }

    public static function finaliseplan(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'distcode' => ['required', 'string', 'regex:/^\d+$/'],
            'callfor' => ['required', 'string', 'in:C,F'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid field.',
        ]);

        try {
            $deptcode = $request->deptcode;
            $distcode = $request->distcode;
            $callfor = $request->callfor;

            $plan_det = CommonModel::getplandetails($deptcode);
            $quartercode = $plan_det[0]->auditquartercode;
            $prioritycode = $plan_det[0]->prioritycode;
            // return 'finalise'
            $finailiseddata = AuditManagementModel::finaliseplan($deptcode, $distcode, $callfor);

            $finalisedet = $finailiseddata[0]->distributeauditteamplan;
            $finalisedet = json_decode($finalisedet, true);

            $finalisestatus = $finalisedet['status'];
            // return $finalisestatus;
            if ($finalisestatus == 'error') {
                return response()->json(['message' => $finalisedet['message'], 'error' => $finalisedet['code'] ?? null], 500);
            } else if ($callfor == 'C') {
                $data =
                    [
                        'usercount' => $finalisedet['Usercount'],
                        'performanceaudit_count' => $finalisedet['PerformanceAuditInstitution'],
                        'spilloverinst_count' => $finalisedet['SpilloverInstitution'],
                        'templateinst_count' => $finalisedet['TemplateAuditInstitution'],
                        'normalaudit_count' => $finalisedet['NormalAuditInstitution'],
                    ];
                return response()->json(
                    [
                        'success' => true,
                        'message' => $finalisedet['message'],
                        'data' => $data,
                    ], 200
                );
            } else {
                $finalisedplanData = AuditManagementModel::getAuditorsfromplan($deptcode, $distcode, $quartercode, $prioritycode);
                return response()->json(['success' => true, 'planned_auditors' => $finalisedplanData, 'data' => $finalisestatus, 'executingquartercode' => $quartercode]);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public function markChecklistPlanDetailsVerified(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
            'distcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        try {
            $result = AuditManagementModel::markChecklistPlanDetailsVerified(
                $request->deptcode,
                $request->regioncode,
                $request->distcode
            );

            return response()->json([
                'success' => true,
                'message' => 'Checklist plan details verified.',
                'updated_count' => $result['updated'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public function chk_dist(Request $request)
    {
        try {
            $request->validate([
                'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
                'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
            ], [
                'required' => 'The :attribute field is required.',
                'regex'    => 'The :attribute field must be a valid number.',
            ]);

            $sessioncharge  = session('charge');
            $userchargeid   = $sessioncharge->userchargeid;
            $deptcode       = $request->deptcode;

            $param          = 'district';
            $regioncode     = $request->regioncode;
            $dist_details   = AuditManagementModel::dist_details($deptcode, $userchargeid, $param, $regioncode);
                // dd($dist_details);
            if ($dist_details->isEmpty()) {
                return response()->json([
                    'message' => 'No districts found for the selected region.'
                ], 404);
            }

            return response()->json($dist_details);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }


    // public function chk_dist(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
    //             'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
    //         ], [
    //             'required' => 'The :attribute field is required.',
    //             'regex'    => 'The :attribute field must be a valid number.',
    //         ]);

    //         $sessioncharge  = session('charge');
    //         $userchargeid   = $sessioncharge->userchargeid;
    //         $deptcode       = $request->deptcode;

    //         $param          = 'district';
    //         $regioncode     = $request->regioncode;
    //         $dist_details   = AuditManagementModel::dist_details($deptcode, $userchargeid, $param, $regioncode);

    //         if ($dist_details->isEmpty()) {
    //             return response()->json([
    //                 'message' => 'No districts found for the selected region.'
    //             ], 404);
    //         }

    //         return response()->json($dist_details);
    //     } catch (ValidationException $e) {
    //         return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
    //     }
    // }


     public function checklist_dropdown()
    {
        try {
            $sessioncharge  = session('charge');
            $userchargeid   = $sessioncharge->userchargeid;
// dd($userchargeid);
            $deptcode       = $sessioncharge->deptcode;
            $reg_arr        = $sessioncharge->regioncode;
            // dd($reg_arr);
            // $reg_arr       = explode(',', trim($reg_arr, '{}'));
            $param          = 'region';

            //return  $reg_arr;

            $quatdetails    = CommonModel::getplandetails($deptcode);
            $dist_details   = AuditManagementModel::dist_details($deptcode, $userchargeid, $param, '');

            $planname = $quatdetails[0]->planname;
            $financialyear = $quatdetails[0]->financialyear;

            $currentplanname = $planname . ' - ( ' . $financialyear . ' )';

            return view('audit.checklistplan', compact('currentplanname', 'dist_details'));
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public function initschedule_dropdown()
    {
        try {
            $sessioncharge = session('charge');
            $sessionuser = session('user');

            if (empty($sessionuser)) {
                throw new \Exception('No session details');
            } else {
                $sessionuserid = $sessionuser->userid;
                $formsessionuserid = Crypt::encryptString($sessionuserid);
            }
            $deptcode = $sessioncharge->deptcode;
           // $quarter_det = AuditManagementModel::getquarterdetails($deptcode);
$quarter_det =  CommonModel::getplandetails($deptcode);

            //      $quarter_det = 'dfsd';

            return view('audit.initauditschedule', compact('formsessionuserid', 'quarter_det'));
        } catch (\Exception $e) {
            return view('audit.initauditschedule', [
                'quarter_det' => $quarter_det ?? null,
                'errorMessage' => $e->getMessage(),
                'pageName' => 'initauditschedule',
            ]);
        }
    }

    public function viewintimation_dropdown(Request $request)
    {
        try {
            $sessioncharge = session('charge');
            $sessionuser = session('user');

            if (empty($sessionuser)) {
                throw new \Exception('No session details');
            } else {
                $sessionuserid = $sessionuser->userid;
                $formsessionuserid = Crypt::encryptString($sessionuserid);
            }
            $deptcode = $sessioncharge->deptcode;
            // $quarter_det = AuditManagementModel::getquarterdetails($deptcode);
$quarter_det =  CommonModel::getplandetails($deptcode);

            //      $quarter_det = 'dfsd';

            return view('audit.viewintimationdetails', compact('formsessionuserid', 'quarter_det'));
        } catch (\Exception $e) {
            return view('audit.initauditschedule', [
                'quarter_det' => $quarter_det ?? null,
                'errorMessage' => $e->getMessage(),
                'pageName' => 'initauditschedule',
            ]);
        }
    }

    public function spilloverschedule_values(Request $request)
    {
        try {
            $instid = Crypt::decryptString($request->id);
            $planid = Crypt::decryptString($request->planid);
            if (empty($instid)) {
                throw new \Exception('Institution Details not found');
            }
            if (empty($planid)) {
                throw new \Exception('Plan details  not found');
            }

            $getplandetails = AuditManagementModel::getspilloverplandetails($instid, $planid);
            // print_r($getplandetails);
            // exit;
            foreach ($getplandetails as $all) {
                $all->encrypted_instid = Crypt::encryptString($all->instid);
                unset($all->instid);
            }
            return view('audit.spilloverschedule', compact('getplandetails'));

            // print_r($getplandetails);
            // exit;
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public function chargetakingover(Request $request)
    {
        try {
            $instid = Crypt::decryptString($request->instid);

            if (empty($instid)) {
                throw new \Exception('Institution Details not found');
            }

            $session = session('user');
            $userid = $session->userid;

            $result = AuditManagementModel::chargetakingover($instid, $userid);

            $jsonString = $result[0]->response;
            $data = json_decode($jsonString, true);

            $status = $data['status'] ?? null;
            $message = $data['message'] ?? null;
            // $status = 'success';
            // $message = 'success';

            if ($status == 'error') {
                return response()->json(['message' => $message, 'error' => 500], 500);
            } else if ($status == 'success') {
                return response()->json(['success' => true, 'message' => $message]);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Some error occured',
                ], 200);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public static function DeptandRoletypeFetchforallocation()
    {
        $dept = AuditManagementModel::ForInstituionlDeptfetch();

        return view('audit.institutionallocation', compact('dept'));
    }

    public function ForallocationRegionBasedOnDept(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $deptcode = $request->input('deptcode');

        $regions = AuditManagementModel::getRegionsByDept($deptcode);

        return response()->json([
            'success' => true,
            'regions' => $regions,  // Regions data
        ]);
    }

    public function getdistrictbasedonregionallocation(Request $request)
    {
        // Validate the input
        $request->validate(
            [
                'region' => ['required', 'string', 'regex:/^\d+$/'],
                'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            ],
            [
                'region.required' => 'The region field is required.',
                'region.regex' => 'The region field must be a valid number.',
                'deptcode.required' => 'The deptcode field is required.',
                'deptcode.regex' => 'The deptcode field must be a valid number.',
            ]
        );

        // Get the department code
        $regioncode = $request->input('region');
        $deptcode = $request->input('deptcode');

        // Fetch regions from the model
        $district = AuditManagementModel::getdistrictByregion($regioncode, $deptcode);

        // Return JSON response
        if ($district->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $district]);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

    public function getinstitutionbasedondistallocation(Request $request)
    {
        // Validate the input
        $request->validate([
            'region' => ['required', 'string', 'regex:/^\d+$/'],
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'district' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'region.required' => 'The :attribute field is required.',
            'region.regex' => 'The :attribute field must be a valid number.',
            'deptcode.required' => 'The deptcode field is required.',
            'deptcode.regex' => 'The deptcode field must be a valid number.',
            'district.required' => 'The district field is required.',
            'district.regex' => 'The district field must be a valid number.',
        ]);

        $regioncode = $request->input('region');
        $deptcode = $request->input('deptcode');
        $district = $request->input('district');

        $institution = AuditManagementModel::getinstitutionBydistrictalocation($district, $regioncode, $deptcode);

        if ($institution->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $institution]);
        } else {
            return response()->json(['success' => false, 'message' => 'No Institutions found'], 200);
        }
    }

    public function getteamBasedOninst(Request $request)
    {
        // Validate the input
        $request->validate([
            'instmappingcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'instmappingcode.required' => 'The :attribute field is required.',
            'instmappingcode.regex' => 'The :attribute field must be a valid number.',
        ]);

        $instmappingcode = $request->input('instmappingcode');

        $team = AuditManagementModel::getteamBasedOninst($instmappingcode);

        if ($team->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $team]);
        } else {
            return response()->json(['success' => false, 'message' => 'No Institutions found'], 200);
        }
    }

    public function institutionallocationform_insertupdate(Request $request)
    {
        // print_r($_REQUEST);
        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'regioncode' => 'required|string|regex:/^\d+$/',
                'distcode' => 'required|string|regex:/^\d+$/',
                'instmappingcode' => 'required|string|regex:/^\d+$/',
                'auditplanid' => 'required|integer',
                'listresponse' => 'nullable|string',
                'currentquarter' => 'required|string',
            ];

            // $validator = Validator::make($request->all(), $rules);

            // // If validation fails, throw an exception with a single message
            // if ($validator->fails()) {
            //     throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            // }

            $allocation = session('user');
            if (!$allocation || !isset($allocation->userid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userid = $allocation->userid;

            $data = [
                'listresponse' => $request->listresponse ?? null,
                'instmappingcode' => $request->instmappingcode ?? null,
                'auditplanid' => $request->auditplanid ?? null,
                'currentquarter' => $request->currentquarter ?? null,
                'statusflag' => 'C',
                'createdon' => View::shared('get_nowtime'),
                'createdby' => $userid,
                'updatedon' => View::shared('get_nowtime'),
                'updatedby' => $userid,
            ];

            $result = AuditManagementModel::institutionallocation_insertupdate($data);
            return response()->json(['success' => true, 'message' => 'newinstitutionalloc_succes', 'flag' => 'Y']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function institutionallocation_fetchData(Request $request)
    {
        $instituionalloc = AuditManagementModel::institutionallocation_fetchData('audit.auditplan');

        return response()->json([
            'success' => !$instituionalloc->isEmpty(),
            'message' => $instituionalloc->isEmpty() ? 'User not found' : '',
            'data' => $instituionalloc->isEmpty() ? null : $instituionalloc
        ], $instituionalloc->isEmpty() ? 404 : 200);
    }

    public function get_userbasedtemp(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
                'distcode' => ['required', 'string', 'regex:/^\d+$/'],
                'userid' => ['required', 'integer'],
            ], [
                'required' => 'The :attribute field is required.',
                'regex' => 'The :attribute field must be a valid field.',
            ]);
            $deptcode = $request->deptcode;
            $distcode = $request->distcode;
            $userid = $request->userid;

            $guserbasedtemp = AuditManagementModel::get_userbasedtemp($deptcode, $distcode, $userid);

            if ($guserbasedtemp) {
                return response()->json(['success' => true, 'message' => 'Data was fetched successfully', 'data' => $guserbasedtemp]);
            } else {
                return response()->json(['success' => false, 'message' => 'Error occured while fetching data']);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public function getquarterbasedonyear(Request $request){

    $request->validate([
        'instmappingcode' => ['required', 'string', 'regex:/^\d+$/'],
    ], [
        'required' => 'The :attribute field is required.',
        'regex'    => 'The :attribute field must be a valid number.',
    ]);

    $instmappingcode = $request->input('instmappingcode');
    $financialyear = $request->input('financialyear');

    $quarter = AuditManagementModel::commonquarterfetch($instmappingcode,$financialyear);


    return response()->json([
        'success' => true,
        'quarter' => $quarter,
    ]);
}
public function getcompactdel_readyforaudit()
    {
        $session = session('charge');

        $deptcode = $session->deptcode;
        $plan_config_details = CommonModel::getplandetails($deptcode);
        $planname = $plan_config_details[0]->planname;
        $dept_det = DB::table(self::$deptartment_table)
            ->where('deptcode', $deptcode)
            ->first();
        $regions = AuditManagementModel::getreadyforauditregions($deptcode);

        return view('audit.finalisereadyforaudit', compact(
            'planname',
            'dept_det',
            'regions',
        ));
    }



 public function getReadyForAuditRegions()
    {
        try {
            $session = session('charge');
            $deptcode = $session->deptcode ?? null;

            $regions = AuditManagementModel::getreadyforauditregions($deptcode);

            return response()->json([
                'status' => true,
                'data' => $regions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching regions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getReadyForAuditDistricts(Request $request)
    {
        try {
            $session = session('charge');
            $deptcode = $session->deptcode ?? null;
            $regioncode = $request->query('regioncode') ?: ($session->regioncode ?? null);

            if (empty($regioncode)) {
                return response()->json([
                    'status' => true,
                    'data' => []
                ]);
            }

            $districts = AuditManagementModel::getreadyforauditdistricts($deptcode, $regioncode);

            return response()->json([
                'status' => true,
                'data' => $districts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching districts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getInstitutions(Request $request)
    {
        try {
            $session = session('charge');

            $deptcode = $request->query('deptcode') ?: ($session->deptcode ?? null);
            $regioncode = $request->query('regioncode') ?: ($session->regioncode ?? null);
            $distcode = $request->query('distcode') ?: ($session->distcode ?? null);

            if (empty($distcode)) {
                return response()->json([
                    'status' => true,
                    'finalised' => false,
                    'requiresDistrict' => true,
                    'data' => []
                ]);
            }

            // Check if plan already finalised
            $finalise = AuditManagementModel::checkreadyforauditfinalised($deptcode, $distcode);

            if ($finalise) {
                return response()->json([
                    'status' => true,
                    'finalised' => true,
                    'data' => []
                ]);
            }

            // Get institutions
            $institutions = AuditManagementModel::getreadyforauditinstitutions($deptcode, $regioncode, $distcode);

            return response()->json([
                'status' => true,
                'finalised' => false,
                'requiresDistrict' => false,
                'data' => $institutions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching institutions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function automateschedule(Request $request)
    {
        try {
            $planid = Crypt::decryptString($request->auditplanid);
            $instid = Crypt::decryptString($request->instid);
            $userid = $request->userid;

            if (!is_numeric($planid) || !is_numeric($instid)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid input values'
                ], 400);
            }


            $response = AuditManagementModel::automateschedule($planid, $instid, $userid);
            // $response['status'] = 'success';
            if ($response['status'] === 'success') {

                $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
                // $sentsms = $auditModel->sendIntimation_mail(24728);

                 $sentsms = $auditModel->sendIntimation_mail($response['auditscheduleid']);
                return response()->json($response, 200);
            } else {
                return response()->json($response, 400);
            }
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function save(Request $request)
    {
        try {
            $session = session('charge');

            $deptcode = $request->deptcode ?: ($session->deptcode ?? null);
            $regioncode = $request->regioncode ?: ($session->regioncode ?? null);
            $distcode = $request->distcode ?: ($session->distcode ?? null);

            $instid = $request->instid ?? [];

            if (empty($distcode)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please select a district.'
                ], 422);
            }

            $result = AuditManagementModel::updatereadyforaudit($instid, $deptcode, $distcode, $regioncode);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error while saving',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
