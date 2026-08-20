<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use App\Helpers\CryptoHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\TransactionFlowModel;
use Illuminate\Support\Facades\View;
use App\Models\BaseModel;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Validator;
     use App\Services\PHPMailerService;
        use App\Services\SmsService;
        use App\Models\SmsmailModel;

use App\Models\UserManagementModel;

use Illuminate\Http\Request;

use DataTables;


class TransactionflowController extends Controller
{
    protected static $roletype = BaseModel::ROLETYPE;
    protected static $roletypemapping_table = BaseModel::ROLETYPEMAPPING_TABLE;
    protected static $department_table = BaseModel::DEPARTMENT_TABLE;
    protected static $region_table = BaseModel::REGION_TABLE;
    protected static $transtype_table = BaseModel::TRANSACTIONTYPE_TABLE;
    protected static $district_table = BaseModel::DIST_Table;
    protected static $designation_table = BaseModel::DESIGNATION_TABLE;
    protected static $userdet_table = BaseModel::USERDETAIL_TABLE;
    protected static $othertrans_table = BaseModel::OTHERTRANS_TABLE;
    protected static $leavetype_table = BaseModel::LEAVETYPE_TABLE;
    protected static $transactionflow_table = BaseModel::TRANSACTIONFLOW_TABLE;
    protected static $futureplanheadtransfer_table = BaseModel::FUTUREPLANHEADTRANSFER;
    protected static $logothertransplandel_table = BaseModel::LOGOTHERTRANSPLANDEL;
    protected static $logothertransscheduledel_table = BaseModel::LOGOTHERTRANS_SCHEDULEDELTABLE;
    protected static $auditplanteammember_table = BaseModel::AUDITPLANTEAMMEM_TABLE;
    protected static $fn_headchangeforplan = BaseModel::fn_headchangeforplan;
    protected static $fn_calculateTodateWithMandaysTeamsize = BaseModel::fn_calculateTodateWithMandaysTeamsize;

    protected $fileUploadService;


    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }



    public function usertrans_dropdown()
    {
        $dept = TransactionFlowModel::getdeptbasedonsession();
        $todept = TransactionFlowModel::getTodept();
        $trans_type = DB::table(self::$transtype_table)
            ->whereNotIn('transactiontypecode', ['01'])
            ->where('statusflag', 'Y')
            ->get();

        $userdata = session('user');
        $sessionuserid = $userdata->userid;
        $ensessionuserid = Crypt::encryptString($sessionuserid);

        return view('transactionflow.othertransaction', compact('dept', 'trans_type', 'ensessionuserid', 'todept'));
    }

    public function getroletypecode_basedondept_othertrans(Request $request)
    {
        $deptcode   =   $_REQUEST['deptcode'];
        $page       =   $_REQUEST['page'];

        $userData = session('charge');
        $session_roletypecode = $userData->roletypecode ?? '';

        // echo $session_roletypecode;
        // exit;

        $request->validate([
            'deptcode'  =>  ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex'     =>  'The :attribute field must be a valid number.',
        ]);

        // Fetch user data based on deptuserid
        $roletypedel = UserManagementModel::roletypebasedon_sessionroletype($deptcode, $session_roletypecode, $page); // Adjust query as needed


        if ($roletypedel) {
            return response()->json(['success' => true, 'data' => $roletypedel]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }



    public function getdeptbaseddesig(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode'      => ['required', 'string', 'regex:/^\d+$/'],
            // 'valuefor'      => ['required', 'string', 'in:desig,userdetail'], // Include "region"
        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
            // 'in'       => 'The :attribute field must be one of: desig, userdetail',
        ]);



        // Extract validated data
        $deptcode = $validatedData['deptcode'];
        $instid = $request->input('instid');
        $for    =    $request->input('for');



        // echo $instid;

        // exit;

        // Additional validation for 'region'
        if (!$deptcode) {
            return response()->json(['success' => false, 'message' => 'Department code is required for region.'], 422);
        }
        try {

            $getdata =  TransactionFlowModel::getdeptbased_desig(
                $deptcode,
                $instid,
                $for
            );

            if ($getdata) {
                return response()->json(['success' => true, 'data' => $getdata]);
            }

            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function fetchRegDistInstbasedondept(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode'      => ['required', 'string', 'regex:/^\d+$/'],
            'roletypecode'  => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode'    => ['nullable', 'string', 'regex:/^\d+$/'],
            'distcode'      => ['nullable', 'string', 'regex:/^\d+$/'],
            'valuefor'      => ['required', 'string', 'in:region,district,institution'], // Include "region"
        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
            'in'       => 'The :attribute field must be one of: region, district, institution.',
        ]);

        // Extract validated data
        $deptcode = $validatedData['deptcode'];
        $regioncode = $validatedData['regioncode'] ?? null;
        $distcode = $validatedData['distcode'] ?? null;
        $roletypecode = $validatedData['roletypecode'];
        $valuefor = $validatedData['valuefor'];

        // Additional validation for 'region'
        if ($valuefor === 'region' && !$deptcode) {
            return response()->json(['success' => false, 'message' => 'Department code is required for region.'], 422);
        }

        // Additional validation for 'district'
        if ($valuefor === 'district' && !$regioncode) {
            return response()->json(['success' => false, 'message' => 'Region code is required for district.'], 422);
        }

        // Additional validation for 'institution'
        if ($valuefor === 'institution' && in_array($roletypecode, [View::shared('Re_roletypecode'), View::shared('Dist_roletypecode')])) {
            if (!$regioncode) {
                return response()->json(['success' => false, 'message' => 'Region code is required for institution.'], 422);
            }
            if ($roletypecode === View::shared('Dist_roletypecode') && !$distcode) {
                return response()->json(['success' => false, 'message' => 'District code is required for this role type.'], 422);
            }
        }
        try {
            $getdata =  TransactionFlowModel::getdata_regdistinst($deptcode, $regioncode, $distcode, $valuefor, $roletypecode);

            if ($getdata) {
                return response()->json(['success' => true, 'data' => $getdata]);
            }

            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function instdataforothers(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode'      => ['required', 'string', 'regex:/^\d+$/'],
            'roletypecode'  => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode'    => ['nullable', 'string', 'regex:/^\d+$/'],
            'distcode'      => ['nullable', 'string', 'regex:/^\d+$/'],
            'fromdistcode'  => ['nullable', 'string', 'regex:/^\d+$/'],
            'valuefor'      => ['required', 'string', 'in:region,district,institution'], // Include "region"
        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
            'in'       => 'The :attribute field must be one of: region, district, institution.',
        ]);

        // Extract validated data
        $deptcode = $validatedData['deptcode'];
        $regioncode = $validatedData['regioncode'] ?? null;
        $distcode = $validatedData['distcode'] ?? null;
        $fromdistcode = $validatedData['fromdistcode'] ?? null;
        $roletypecode = $validatedData['roletypecode'];
        $valuefor = $validatedData['valuefor'];



        // Additional validation for 'region'
        if ($valuefor === 'region' && !$deptcode) {
            return response()->json(['success' => false, 'message' => 'Department code is required for region.'], 422);
        }

        // Additional validation for 'district'
        if ($valuefor === 'district' && !$regioncode) {
            return response()->json(['success' => false, 'message' => 'Region code is required for district.'], 422);
        }

        // Additional validation for 'institution'
        if ($valuefor === 'institution' && in_array($roletypecode, [View::shared('Re_roletypecode'), View::shared('Dist_roletypecode')])) {
            if (!$regioncode) {
                return response()->json(['success' => false, 'message' => 'Region code is required for institution.'], 422);
            }
            if ($roletypecode === View::shared('Dist_roletypecode') && !$distcode) {
                return response()->json(['success' => false, 'message' => 'District code is required for this role type.'], 422);
            }
        }
        try {

            $getdata =  TransactionFlowModel::getdataforToInst($deptcode, $regioncode, $distcode, $valuefor, $fromdistcode, $roletypecode);

            if ($getdata) {
                return response()->json(['success' => true, 'data' => $getdata]);
            }

            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function filterforusertrans(Request $request)
    {

        if ($request->desigcode && $request->deptcode) {
            $paramCheck = [
                'deptcode' => $request->deptcode,
                'desigcode' => $request->desigcode,
                'distcode' => $request->distcode ?? null,
                'transtype' => $request->transtype ?? null

            ];
            $userdet = TransactionFlowModel::desigbaseduser(self::$userdet_table, $paramCheck);

            return response()->json(['users' => $userdet]);
        }
        if ($request->regioncode && $request->deptcode) {
            $districtdata = TransactionFlowModel::regionbaseddist(self::$district_table, $request->regioncode, $request->deptcode);

            return response()->json(['districtdata' => $districtdata]);
        }
        if ($request->deptcode) {

            $regionDet = TransactionFlowModel::deptbasedregion(self::$roletypemapping_table, $request->deptcode);
            $designationDet = TransactionFlowModel::deptbaseddesignation(self::$designation_table, $request->deptcode);

            return response()->json(['designation' => $designationDet, 'region' => $regionDet]);
        }
    }








    // public function othertransction_insertupdate(Request $request)
    // {

    //     try {
    //         $data = $request->all();
    //         // return $request;

    //         $userSessionData = session('user');
    //         $userid = $userSessionData->userid;
    //         // $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
    //         $order_date = Carbon::createFromFormat('d/m/Y', $request->input('order_date'))->format('Y-m-d');
    //         // return $request->deptuserid;
    //         $request->merge(['order_date' => $order_date]);
    //         $action = $request->input('action');
    //         // $scheduleid  = TransactionFlowModel::getScheduleid($request->deptuserid);
    //         // $auditscheduleid = $scheduleid[0]->auditscheduleid ?? null;
    //         $request->validate([
    //             'deptuserid'            => 'required|integer',
    //             'roletypecode'           => 'required|string|regex:/^\d+$/',
    //             'deptcode'              => 'required|string|regex:/^\d+$/',
    //             'regioncode'              => 'nullable|string|regex:/^\d+$/',
    //             'distcode'              => 'nullable|string|regex:/^\d+$/',
    //             'frominstmapcode'        => 'required|string|regex:/^\d+$/',
    //             'desigcode'              => 'required|string|regex:/^\d+$/',
    //             'transtypecode'         =>  'required|string|regex:/^\d+$/',         // Only alphabets (no numbers or symbols)
    //             'order_date'             =>  'required|date|date_format:Y-m-d|',             // Alphanumeric (letters and numbers)
    //             'orderno'               =>  'required|integer',



    //         ]);
    //         $data = [
    //             'userid'        => $request->deptuserid,
    //             'frominstmappingcode' => $request->frominstmapcode,

    //             'transactiontypecode' => $request->transtypecode,
    //             'orderdate'     => $request->order_date,
    //             'orderno'       => $request->orderno,
    //             'fromdesigcode' => $request->desigcode,
    //             'statusflag'    => 'Y',
    //             'processcode'   => View::shared('Insert'),
    //             'inoutstatus'   =>  View::shared('Outflag'),
    //             'updatedby'     =>  $sessionuserid,
    //             'updatedon'     =>  View::shared('get_nowtime')
    //         ];



    //         if (($request->transtypecode == View::shared('diversionTransactiontypecode')) ||
    //             ($request->transtypecode == View::shared('transfercode')) ||
    //             ($request->transtypecode == View::shared('transferwithpromocode'))
    //         ) {
    //             $data['toinstmappingcode'] = $request->audit_inst;
    //             if (($request->transtypecode == View::shared('transferwithpromocode')) ||
    //                 ($request->transtypecode == View::shared('transfercode'))
    //             ) {
    //                 $data['todesigcode'] = $request->to_desig;
    //             }

    //             // $data['instmappingid'] = 13;
    //         }

    //         // print_r($data);
    //         // exit;

    //         $uploadid = $request->input('uploadid');

    //         if ((($action === 'insert') || ($action === 'update')) && ($request->hasFile('file'))) {

    //             $destinationPath = 'uploads/othertransaction';
    //             $destinationarray = [
    //                 $request->deptcode,
    //                 $request->regioncode,
    //                 $request->distcode,
    //                 $request->frominstmapcode,
    //                 View::shared('othertransactionfilepath'),

    //             ];
    //             if ($uploadid) {
    //                 $uploadResult = $this->fileUploadService->uploadFile($request->file('file'), $destinationPath, $uploadid,  $destinationarray);
    //             } else {
    //                 $uploadResult = $this->fileUploadService->uploadFile($request->file('file'), $destinationPath, '',  $destinationarray);
    //             }

    //             $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
    //             $data['uploadid'] = $fileuploadId;

    //             $data['createdby'] = $sessionuserid;
    //             $data['createdon'] = View::shared('get_nowtime');


    //         }
    //         if ($request->action == 'update') {
    //             $othertransid = $request->filled('othertransid') ? Crypt::decryptString($request->othertransid) : null;

    //         } else
    //             $othertransid =   null;
    //         $othertrandet = TransactionFlowModel::insertorUpdateOthertrans(self::$othertrans_table, $data, $userid, $othertransid);
    //         return response()->json(['success' => 'Apllication was created/updated successfully', 'othertrans' => $othertrandet]);
    //     } catch (ValidationException $e) {
    //         return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
    //     }
    // }

    public function othertransction_insertupdate(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $userSessionData = session('user');
            $userid = $userSessionData->userid ?? null;
            $charge = session('charge');
            $userchargeid = $charge->userchargeid ?? null;

            // Validate session and user
            if (!$userid) {
                throw new \Exception('User session is invalid or user not logged in.');
            }

            if (!$userchargeid) {
                throw new \Exception('User charge session is missing. Please login again.');
            }

            $enuserid = Crypt::decryptString($request->enuserid);
            // echo $enuserid;
            // echo $userid;
            // exit;

            if ($enuserid != $userid) {

                throw new \Exception('Session mismatch detected. Unauthorized access.');
            }


            $action = $request->input('action');
            $othertransid = ($action === 'update' && $request->filled('othertransid'))
                ? Crypt::decryptString($request->othertransid)
                : null;

            if ($othertransid && $action === 'update' && $enuserid != $userid) {
                throw new \Exception('Update action detected with session mismatch. Aborting.');
            }

            // Validate order_date
            $request->validate([
                'order_date' => 'required|date_format:d/m/Y'
            ], [
                'order_date.required' => 'The Order Date field is required.',
                'order_date.date_format' => 'Order Date must be in the format DD/MM/YYYY.',
            ]);

            // Format date
            $order_date = Carbon::createFromFormat('d/m/Y', $request->input('order_date'))->format('Y-m-d');
            $request->merge(['order_date' => $order_date]);

            // Main validation
            $request->validate([
                'deptuserid' => 'required|integer',
                'roletypecode' => 'required|string|regex:/^\d+$/',
                'deptcode' => 'required|string|regex:/^\d+$/',
                'regioncode' => 'required|string|regex:/^\d+$/',
                'distcode' => 'required|string|regex:/^\d+$/',
                'frominstmapcode' => 'required|string|regex:/^\d+$/',
                'desigcode' => 'required|string|regex:/^\d+$/',
                'transtypecode' => 'required|string|regex:/^\d+$/',
                'order_date' => 'required|date|date_format:Y-m-d',
                'orderno' => 'required|integer',
                'action' => 'required'
            ], [
                'required' => 'The :attribute field is mandatory.',
                'regex' => 'The :attribute field must contain only numbers.',
                'date_format' => 'Invalid date format for :attribute. Expected Y-m-d.'
            ]);

            // Prepare data
            $data = [
                'userid' => $request->deptuserid,
                'frominstmappingcode' => $request->frominstmapcode,
                'transactiontypecode' => $request->transtypecode,
                'orderdate' => $request->order_date,
                'orderno' => $request->orderno,
                'fromdesigcode' => $request->desigcode,
                'statusflag' => 'Y',
                'processcode' => View::shared('Insert'),
                'inoutstatus' => View::shared('Outflag'),
                'updatedbyuserchargeid' => $userchargeid,
                'updatedon' => View::shared('get_nowtime'),
            ];

            // Conditional validations
            if (in_array($request->transtypecode, [
                View::shared('diversionTransactiontypecode'),
                View::shared('transfercode'),
                View::shared('transferwithpromocode')
            ])) {
                $request->validate([
                    'audit_inst' => 'required|string|regex:/^\d+$/'
                ], [
                    'toinstmappingcode.required' => 'The To Institution Mapping Code field is required.',
                    'toinstmappingcode.regex' => 'The To Institution Mapping Code must be numeric.'
                ]);

                $data['toinstmappingcode'] = $request->audit_inst;

                if (in_array($request->transtypecode, [
                    // View::shared('transfercode'),
                    View::shared('transferwithpromocode')
                ])) {
                    $request->validate([
                        'todesigcode' => 'required|string|regex:/^\d+$/'
                    ], [
                        'todesigcode.required' => 'The To Designation Code field is required for this transaction.',
                        'todesigcode.regex' => 'The To Designation Code must be numeric.'
                    ]);

                    $data['todesigcode'] = $request->to_desig;
                }
            } else {
                $data['toinstmappingcode'] =    null;
            }

            // Handle file upload
            $uploadid = $request->input('uploadid');
            if (($action === 'insert' || $action === 'update') && $request->hasFile('file')) {
                $destinationPath = 'uploads/othertransaction';
                $destinationarray = [
                    $request->deptcode,
                    $request->regioncode,
                    $request->distcode,
                    $request->frominstmapcode,
                    View::shared('othertransactionfilepath'),
                ];

                $uploadResult = $this->fileUploadService->uploadFile(
                    $request->file('file'),
                    $destinationPath,
                    $uploadid ?? '',
                    $destinationarray
                );

                $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
                if (!$fileuploadId) {
                    throw new \Exception('File upload failed. Please try again.');
                }

                $data['uploadid'] = $fileuploadId;
                $data['createdbyuserchargeid'] = $userchargeid;
                $data['createdon'] = View::shared('get_nowtime');
            }

            // Insert or update data
            $othertrandet = TransactionFlowModel::insertorUpdateOthertrans($data, $othertransid, 'maintable');

            if (!$othertrandet || !isset($othertrandet['status'])) {
                throw new \Exception('Unexpected response from transaction model.');
            }

            DB::commit();

            if ($othertrandet['status'] === 'inserted') {
                return response()->json([
                    'success' => 'Application created successfully.',
                    'othertransid' => $othertrandet['othertransid']
                ], 201);
            } elseif ($othertrandet['status'] === 'updated') {
                return response()->json([
                    'success' => 'Application updated successfully.',
                    'othertrans' => $othertrandet['data']
                ], 200);
            } else {
                throw new \Exception($othertrandet['message'] ?? 'Unknown error occurred.');
            }
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Validation failed: ' . $e->getMessage()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }





    public function fetchOtherTranDel(Request $request)
    {
        try {

            $othertransid = $request->filled('othertransid') ? Crypt::decryptString($request->othertransid) : null;
            $othertransDet = TransactionFlowModel::fetchothertransdel($othertransid);

            foreach ($othertransDet as $all) {
                $all->encrypted_othertransid = Crypt::encryptString($all->othertransid);
            }

            if ($othertransid) {
                if ($othertransDet->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mapping Details not found',
                        'data' => null
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'message' => '',
                    'data' => $othertransDet
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $othertransDet->isEmpty() ? null : $othertransDet
            ], 200);
        } catch (QueryException $e) {
            \Log::error('Database Query Error: ' . $e->getMessage());  // Log the error for debugging

            return response()->json([
                'success' => false,
                'message' => 'There was an issue with the database query. Please try again.'
            ], 400);  // Return a custom error message without the generic server error
        } catch (Exception $e) {
            \Log::error('General Error: ' . $e->getMessage());  // Log the error for debugging

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 400);  // Return a more user-friendly error leavetype_dropdownvalues
        }
    }


     public function forward_application(Request $request)
    {
        DB::beginTransaction();  // Begin a new transaction

	        try {
	            if ($request->transactiontypecode == View::shared('Leavetransactiontypecode') && $request->action == 'insert') {
		                    $sessionCharge = session('charge');
		                $rhLeaveLimitCheck = $this->checkRestrictedHolidayYearlyLimit(
		                    $request->input('leave_type'),
		                    $request->input('userid'),
		                    $request->input('from_date'),
		                    $request->input('to_date'),
		                    $this->getDeptRestrictedHolidayLimit($sessionCharge->deptcode)
		                );

		                if (!$rhLeaveLimitCheck['allowed']) {
		                    DB::rollBack();
		                    return response()->json([
		                        'status' => 'error',
		                        'message' => $rhLeaveLimitCheck['message'],
		                    ], 400);
		                }

		                if ($this->isAutoMandayExtensionEligible($request, $sessionCharge->deptcode)) {
		                    $scheduleDetails = TransactionFlowModel::getShortLeaveScheduleDetails(
	                        $request->input('userid'),
	                        $sessionCharge->deptcode,
	                        $request->input('from_date'),
	                        $request->input('to_date')
	                    );
		                    $leaveExecutionCheck = $this->checkLeaveExecutionLimit($scheduleDetails, $request->input('userid'), null, $request->input('from_date'), $request->input('to_date'));

	                    if (!$leaveExecutionCheck['allowed']) {
	                        $request->merge(['auto_manday_extension' => 'N']);
	                    }
	                }

	                $data = [
	                    'userid' => $request->input('userid'),
                    'fromdate' => $request->input('from_date'),
                    'todate' => $request->input('to_date'),
                    'leavetypecode' => $request->input('leave_type'),
                    'reason' => $request->input('reason'),
                    'statusflag' => 'Y',
                    'updatedon' => View::shared('get_nowtime'),
                    'updatedby' => $request->input('userid'),
                    'updatedbyuserchargeid' => $request->input('userchargeid'),
                    'processcode' => View::shared('Insert'),
                    'transactiontypecode' => View::shared('Leavetransactiontypecode'),
                    'createdon' => View::shared('get_nowtime'),
                    'createdbyuserchargeid' => $request->input('userchargeid'),
                    'createdby' => $request->input('userid'),
                    'longleave' => $request->input('longleave'),
                    'leavedayscount' => $request->input('leavedays'),
                ];
                $result = TransactionFlowModel::createleave_insertupdate($data, '', 'audit.ind_leavedetail', $request->input('userid'), 'form');

                // Handle result
                if (in_array($result['status'], ['inserted', 'updated'])) {
                } elseif ($result['status'] === 'failed') {
                    return response()->json([
                        // 'success' => false,
                        'status' => 'error',
                        'message' => $result['message']
                    ], 400);
                } else {
                    return response()->json([
                        // 'success' => false,
                        'status' => 'error',
                        'message' => $result['message'] ?? 'Unexpected error occurred.'
                    ], 500);
                }

                if (in_array($result['status'], ['inserted', 'updated'])) {
                    $leavedel = $result['data'];
                    $id = $leavedel->leaveid;
                    $action = 'first';
                    $trans_action = 'first';
                }
            } else {
                $action = $request->action;
                $id = $request->id;
                $id = Crypt::decryptString($request->id);
                $trans_action = $request->action;
            }

            // Get session data
            $userSessionData = session('user');
            $userSessionChargeData = session('charge');

            // Validate session data
            if (!$userSessionData || !isset($userSessionData->userid)) {
                return redirect()->back()->withErrors(['Session expired or data missing.']);
            }

	            $sessionchargeid = $userSessionChargeData->userchargeid;
	            $userid = $userSessionData->userid;

	            $autoLeaveApproved = false;

	            if (
	                $request->transactiontypecode == View::shared('Leavetransactiontypecode') &&
	                $this->isAutoMandayExtensionEligible($request, $userSessionChargeData->deptcode) &&
	                isset($id)
			    ) {
	                $autoMandayRoleTypeCode = $this->resolveAutoMandayRoleTypeCode($userSessionChargeData, $userid);
	
			                $autoMandaysResult = $this->applyAutoMandaysExtensionForLeave(
		                    $id,
		                    $userid,
		                    $userSessionChargeData->deptcode,
		                    $request->input('from_date'),
		                    $request->input('to_date'),
		                    $autoMandayRoleTypeCode,
		                    $sessionchargeid
		                );

	                if ($autoMandaysResult['status'] !== 'success') {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
	                        'message' => $autoMandaysResult['message'] ?? 'Failed to auto approve mandays extension.',
		                    ], 500);
		                }

		                $autoLeaveApproved = true;
		                DB::commit();
		                return response()->json([
		                    'status' => 'success',
		                    'message' => 'Leave Approved Successfully',
		                ]);
		            }

	            if ($request->transactiontypecode == View::shared('LeaveIntransactiontypecode') && $request->action == 'first') {
                $detailuserid = Crypt::decryptString($request->userid);

                $data = [
                    'userid' => $detailuserid,
                    'leaveid' => $id,
                    'statusflag' => 'Y',
                    'updatedon' => View::shared('get_nowtime'),
                    'updatedby' => $detailuserid,
                    'updatedbyuserchargeid' => $sessionchargeid,
                    'processcode' => View::shared('Insert'),
                    'transactiontypecode' => View::shared('LeaveIntransactiontypecode'),
                    'createdon' => View::shared('get_nowtime'),
                    'createdbyuserchargeid' => $sessionchargeid,
                    'createdby' => $detailuserid,
                ];

                $result = TransactionFlowModel::leavein_insertupdate($data, '', 'form');

                if (in_array($result['status'], ['inserted', 'updated'])) {
                } elseif ($result['status'] === 'failed') {
                    return response()->json([
                        // 'success' => false,
                        'status' => 'error',
                        'message' => $result['message']
                    ], 400);
                } else {
                    return response()->json([
                        // 'success' => false,
                        'status' => 'error',
                        'message' => $result['message'] ?? 'Unexpected error occurred.'
                    ], 500);
                }

                if (in_array($result['status'], ['inserted', 'updated'])) {
                    $leavedel = $result['data'];
                    $id = $leavedel->leaveinid;
                    $action = 'first';
                    $trans_action = 'first';
                }
            }

            $transtypecode = $request->transactiontypecode;

            if ($transtypecode == View::shared('Leavetransactiontypecode')) {
                $detailuserid = $userid;
            } else if ($request->transactiontypecode == View::shared('LeaveIntransactiontypecode')) {
                $detailuserid = $detailuserid;
            } else {
                $detailuserid = $request->userid;
            }

            // Retrieve request data

            $transtypecode = $request->transactiontypecode;

            // Forward the application to the next level based on transaction type code
            $forwarddel = TransactionFlowModel::forwardtonextlevel($transtypecode, $detailuserid, $action);

            if (count($forwarddel) == 1) {
                $forwardtouserchargeid = $forwarddel[0]->userchargeid;

	                // Determine process code based on action (Approve or Forward)
	                $process = $trans_action == 'Approve' ? View::shared('Approve') : View::shared('Forward');
	                    if (
	                        $transtypecode == View::shared('Leavetransactiontypecode') &&
	                        $this->isAutoMandayExtensionEligible($request, $userSessionChargeData->deptcode)
	                    ) {
	                        $process = View::shared('Approve_Processcode');
	                    }

                // Prepare the transaction detail data
                $transactiondel_data = [
                    'forwardedtouserchargeid' => $forwardtouserchargeid,
                    'updatedbyuserchargeid' => $sessionchargeid,
                    'updatedon' => View::shared('get_nowtime'),
                ];

                if ($action == 'first') {
                    $transactiondel_data['userid'] = $detailuserid;
                    $transactiondel_data['transactiontypecode'] = $transtypecode;
                    $transactiondel_data['createdbyuserchargeid'] = $sessionchargeid;
                    $transactiondel_data['createdon'] = View::shared('get_nowtime');
                    $transactiondel_data['statusflag'] = 'Y';

                    // Set where condition based on transaction type
                    if ($transtypecode == View::shared('Leavetransactiontypecode')) {
                        $transactiondel_data['leaveid'] = $id;
                        $where = ['leaveid' => $id];
                    } else if ($transtypecode == View::shared('LeaveIntransactiontypecode')) {
                        $transactiondel_data['leaveinid'] = $id;
                        $where = ['leaveinid' => $id];
                    } else {
                        $transactiondel_data['othertransid'] = $id;
                        $where = ['othertransid' => $id];
                    }
                }

                // Prepare main table update data
                $maintableUpdate = [
                    'processcode' => $process,
                    'updatedon' => View::shared('get_nowtime'),
                ];

                if ($transtypecode != View::shared('Leavetransactiontypecode')) {
                    $maintableUpdate['updatedbyuserchargeid'] = $sessionchargeid;
                } else if ($transtypecode == View::shared('LeaveIntransactiontypecode')) {
                    $maintableUpdate['updatedbyuserchargeid'] = $sessionchargeid;
                } else {
                    $maintableUpdate['updatedby'] = $userid;
                }  // Prepare history transaction data

                $historytransaction_data = [
                    'userid' => $detailuserid,
                    'transactiontypecode' => $transtypecode,
                    'processcode' => $process,
                    'forwardedtouserchargeid' => $forwardtouserchargeid,
                    'forwardedbyuserchargeid' => $sessionchargeid,
                    'forwardedon' => View::shared('get_nowtime'),
                    'statusflag' => 'Y',
                    'transstatus' => 'A',
                ];

                if ($transtypecode == View::shared('Leavetransactiontypecode')) {
                    $historytransaction_data['leaveid'] = $id;
                } else if ($transtypecode == View::shared('LeaveIntransactiontypecode')) {
                    $historytransaction_data['leaveinid'] = $id;
                } else {
                    $historytransaction_data['othertransid'] = $id;
                }

                // print_r($historytransaction_data);
                // print_r($where);

                // Insert/update history transaction record
                $historytransid = TransactionFlowModel::insert_historyTransDetail($historytransaction_data, $where);

                // Check if the history transaction was inserted
                if ($historytransid && $historytransid['status'] == 'inserted') {
                    // Insert or update the transaction detail
                    $transdetailid = TransactionFlowModel::insertupdate_transdet($transactiondel_data, $where);

                    if ($transdetailid && (($transdetailid['status'] == 'updated') || ($transdetailid['status'] == 'inserted'))) {
                        // Update the main transaction table
                        if ($transtypecode == View::shared('Leavetransactiontypecode')) {
                            $leavetableUpdateStatus = TransactionFlowModel::createleave_insertupdate($maintableUpdate, $id, 'audit.ind_leavedetail', $detailuserid, 'transaction');
                        } else if ($transtypecode == View::shared('LeaveIntransactiontypecode')) {
                            $leavetableUpdateStatus = TransactionFlowModel::leavein_insertupdate($maintableUpdate, $id, 'transaction');
                        } else {
                            $othertransUpdateStatus = TransactionFlowModel::insertorUpdateOthertrans($maintableUpdate, $id, 'processtable');
                        }

                        // Check if the main table update was successful
                        if (
                            isset($leavetableUpdateStatus) && $leavetableUpdateStatus['status'] == 'updated' ||
                            isset($othertransUpdateStatus) && $othertransUpdateStatus['status'] == 'updated'
                        ) {
	                            DB::commit();  // Commit the transaction if all operations are successful
	                            return response()->json([
	                                'status' => 'success',
	                                'message' => $autoLeaveApproved ? 'Leave Approved Successfully' : 'Application forwarded successfully',
	                            ]);
                        } else {
                            DB::rollBack();  // Rollback the transaction if the update failed
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Failed to update main transaction table.',
                            ]);
                        }
                    } else {
                        DB::rollBack();  // Rollback the transaction if the transaction detail update failed
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Failed to insert/update transaction detail.',
                        ]);
                    }
                } else {
                    DB::rollBack();  // Rollback the transaction if history transaction insertion failed
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to insert history transaction.',
                    ]);
                }
            } elseif (count($forwarddel) == 0) {
                DB::rollBack();  // Rollback the transaction if no forwarding user found
                return response()->json([
                    'status' => 'error',
                    'message' => 'No forwarding user found for the application.',
                ]);
            } else {
                DB::rollBack();  // Rollback the transaction in case of unexpected behavior
                return response()->json([
                    'status' => 'error',
                    'message' => 'Multiple users found for forwarding, check your request.',
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();  // Rollback the transaction on a database-related error
            return response()->json([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();  // Rollback the transaction on any unexpected error
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ]);
        }
    }


    public function reject_application(Request $request)
    {
        DB::beginTransaction(); // Begin a new transaction

        try {

            $leaveid = Crypt::decryptString($request->leaveid);

            $userSessionChargeData  =   session('charge');
            $userSessionData  =   session('user');

            $sessionchargeid = $userSessionChargeData->userchargeid;
            $userid = $userSessionData->userid;

            $transtypecode = $request->transactiontypecode;
            $detailuserid = $userid;

            $transactiondel_data = [
                'forwardedtouserchargeid' => null,
                'updatedbyuserchargeid' => $sessionchargeid,
                'updatedon' => View::shared('get_nowtime'),
            ];

            // Prepare main table update data
            $maintableUpdate = [
                'processcode' => View::shared('Reject'),
                'updatedby' => $userid,
                'updatedon' => View::shared('get_nowtime'),
            ];

            // Prepare history transaction data
            $historytransaction_data = [
                'userid' => $detailuserid,
                'transactiontypecode' => View::shared('Leavetransactiontypecode'),
                'processcode' => View::shared('Reject'),
                'forwardedbyuserchargeid' => $sessionchargeid,
                'forwardedon' => View::shared('get_nowtime'),
                'statusflag' => 'Y',
                'transstatus' => 'A',
                'leaveid' => $leaveid
            ];

            $where = ['leaveid' => $leaveid];


            // print_r( $where);




            // Insert/update history transaction record
            $historytransid = TransactionFlowModel::insert_historyTransDetail($historytransaction_data, $where);

            // Check if the history transaction was inserted
            if ($historytransid && $historytransid['status'] == 'inserted') {



                // Insert or update the transaction detail
                $transdetailid = TransactionFlowModel::insertupdate_transdet($transactiondel_data, $where);
                //   print_r($transdetailid);
                //   exit;

                if ($transdetailid && (($transdetailid['status'] == 'updated') || ($transdetailid['status'] == 'inserted'))) {
                    // Update the main transaction table
                    $leavetableUpdateStatus = TransactionFlowModel::createleave_insertupdate($maintableUpdate, $leaveid, 'audit.ind_leavedetail', $detailuserid, 'transaction');


                    //   $leavetableUpdateStatus['status']
                    // Check if the main table update was successful
                    if (isset($leavetableUpdateStatus) && $leavetableUpdateStatus['status'] == 'updated') {
                        DB::commit(); // Commit the transaction if all operations are successful
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Application Rejected successfully.',
                        ]);
                    } else {
                        DB::rollBack(); // Rollback the transaction if the update failed
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Failed to update main transaction table.',
                        ]);
                    }
                } else {
                    DB::rollBack(); // Rollback the transaction if the transaction detail update failed
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to insert/update transaction detail.',
                    ]);
                }
            } else {
                DB::rollBack(); // Rollback the transaction if history transaction insertion failed
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to insert history transaction.',
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack(); // Rollback the transaction on a database-related error
            return response()->json([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on any unexpected error
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ]);
        }
    }



    public function fetchall_transflowdata()
    {
        $userSessionData = session('user');
        $userSessionChargeData = session('charge');
        $userSessionChargeData = session('charge');
        $userchargeid = $userSessionChargeData->userchargeid;
        $userid = $userSessionData->userid;

        $forwarded_det = TransactionFlowModel::fetchTransactionFlowData($userchargeid, $userid);

        foreach ($forwarded_det as $item) {
            if ($item->leaveid)
                $item->leaveid = Crypt::encryptString($item->leaveid);
            $item->othertransid = Crypt::encryptString($item->othertransid);
            $item->mandaysextensionid = Crypt::encryptString($item->mandaysextensionid);
            $item->leaveinid = Crypt::encryptString($item->leaveinid);
        }

        $userSessionChargeData = session('charge');
        $userrolemappingid = $userSessionChargeData->rolemappingid;
        $roleactioncode = DB::table('audit.rolemapping as rm')
            ->join('audit.mst_roleaction as mr', 'mr.roleactioncode', '=', 'rm.roleactioncode')
            ->where('rolemappingid', $userrolemappingid)
            ->get(['mr.roleactioncode']);
        // return view('transactionmaster.transaction', compact('roleactioncode'));
        return response()->json(['success' => true, 'data' => $forwarded_det, 'role' => $roleactioncode]);
        // return $forwarded_det;
    }



    public function datatrans_dropdown(Request $request)
    {


        $data = ($request->id); // Get 'id' from URL
        $id =   Crypt::decryptString($request->id);
        $inoutstatus =  $request->inoutstatus;
        $userid =  $request->userid;
        $transtypecode = $request->transtype;

        $roleActionCodes = DB::table('audit.userchargedetails as uc')
            ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
            ->join('audit.rolemapping as ro', 'ro.rolemappingid', '=', 'ch.rolemappingid')
            ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'uc.userid')
            ->where('uc.userid', $userid)
 	    ->where('ro.roleactioncode', '04')
            ->where('uc.statusflag', 'Y')
            ->groupBy('roleactioncode')
            ->select('roleactioncode')
            ->get();



        if (count($roleActionCodes) > 1) {
            echo 'user has 2 roles';
            exit;
        } elseif (count($roleActionCodes) == 1) {
            $roleactioncode =   $roleActionCodes[0]->roleactioncode;
            if (($inoutstatus == 'O') || ($transtypecode == View::shared('Leavetransactiontypecode'))) {
                $pendingdel =  TransactionFlowModel::getting_pendingdel($id, $transtypecode, $userid, $roleactioncode);
            } else    $pendingdel = '';

            $auditscheduleids = collect(); // empty collection by default

            if (!empty($pendingdel['schedulependings']) && $pendingdel['schedulependings']->isNotEmpty()) {
                $auditscheduleids = $pendingdel['schedulependings']->pluck('auditscheduleid');
                $schedule = [];
                foreach ($auditscheduleids as $a) {
                    // echo  $a;
                    $othermembers = $this->getothermembers($a);
                    // print_r($othermembers);

                    $schedule[$a]   =   $othermembers;
                }

                // print_r($schedule);
                // exit;

                $othermembers   =   $schedule;
            } else {
                // No data found or empty collection
                $auditscheduleids = collect(); // or handle empty case as you want
                $othermembers = collect();
            }








            // if($roleactioncode == view::shared('AuditorRoleactioncode'))
            // {

            // }
            // else if($roleactioncode == view::shared('AdminplanviewRoleactioncode'))
            // {

            // }
            // else if($roleactioncode == view::shared('AdminentryRoleactioncode'))
            // {

            // }
            // else{

            // }
        } else {
        }






        // $pendingdel =  TransactionFlowModel::getting_pendingdel($othertransid, $transtypecode,$userid);

        $data =  TransactionFlowModel::fetch_usedrdata_transfer($id, $transtypecode, $inoutstatus, $roleactioncode);
        $otherteamhead =  TransactionFlowModel::fetch_otherteamhead($userid);









        // print_r($data['othertransdet']);
        // foreach ($data as $all) {
        //     $all->a = Crypt::encryptString($all->auditscheduleid);
        // }
        // return $data;
        $othertrans = $data['othertransdet'];
        // $dept       = $data['dept'];
        // $region     = $data['region'];
        // $district   = $data['district'];
        // $user       = $data['user'];
        $touserdata  = $data['touser'];
        // $transtype  = $data['transtype'];

        $othertransid = $id;

        // print_r($othertrans);
        // print_r($touserdata);
        // print_r($othertransid);

        // print_r($inoutstatus);
        // print_r($pendingdel);

        // exit;



        return view('transactionflow.datatransfer', compact('othertrans', 'touserdata',  'othertransid', 'inoutstatus', 'pendingdel', 'othermembers', 'otherteamhead'));
    }



    public function getothermembers($auditscheduleids)
    {
        $scheduleid = $auditscheduleids;

        // Call your model method to get the data
        $getdata = TransactionFlowModel::getothermembers($scheduleid);
        return $getdata;
    }

///leave apply start
 public function leavetype_dropdownvalues(Request $request)
    {
	        $leavetype_det = DB::table(self::$leavetype_table)
	            ->where('statusflag', 'Y')
	            ->orderByRaw('ord_id ASC ')
	            ->orderBy('leavetypeid')
	            ->get();
        $userSessionChargeData = session('charge');
        $userrolemappingid = $userSessionChargeData->rolemappingid;
        // $roleactioncode =   TransactionFlowModel::getroleactioncode($userrolemappingid);

        $session_user = session('user');
        $encryptedUserId = Crypt::encrypt($session_user->userid);

        $holidays = TransactionFlowModel::getholidays();
        $deptdel = TransactionFlowModel::getdeptbasedonsession();
        $maxshortleave = $deptdel[0]->maxshortleavedays;

        return view('transactionflow.leaveform', compact('leavetype_det', 'encryptedUserId', 'holidays', 'maxshortleave'));
    }

    public function checkShortLeaveScheduleDetails(Request $request)
    {
        try {
	            $request->validate([
	                'from_date' => 'required|date_format:d/m/Y',
	                'to_date' => 'required|date_format:d/m/Y',
	                'leave_type' => 'required',
	            ]);

            $sessionUser = session('user');
            $sessionCharge = session('charge');

            if (!$sessionUser || !$sessionCharge) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please login again.',
                ], 401);
            }

	            $fromDate = Carbon::createFromFormat('d/m/Y', $request->input('from_date'))->format('Y-m-d');
	            $toDate = Carbon::createFromFormat('d/m/Y', $request->input('to_date'))->format('Y-m-d');

	            if (Carbon::parse($fromDate)->gt(Carbon::parse($toDate))) {
	                return response()->json([
	                    'success' => false,
	                    'message' => 'To date must be same as or after from date.',
	                ], 422);
	            }

	            $excludeLeaveId = $request->filled('leave_id')
	                ? Crypt::decryptString($request->input('leave_id'))
	                : null;
	            $leaveDays = $this->calculateWorkingLeaveDays($fromDate, $toDate);
	            $isAutoApprovedLeaveType = $this->isAutoApprovedLeaveType($request->input('leave_type'));

		            $dept = DB::table(self::$department_table)
	                ->where('statusflag', 'Y')
	                ->where('deptcode', $sessionCharge->deptcode)
	                ->select('deptcode', 'maxshortleavedays', 'maxrhleaveperyear')
	                ->first();

	            $maxShortLeaveDays = (int) ($dept->maxshortleavedays ?? 0);
		            $rhLeaveLimitCheck = $this->checkRestrictedHolidayYearlyLimit(
		                $request->input('leave_type'),
		                $sessionUser->userid,
		                $fromDate,
		                $toDate,
		                (int) ($dept->maxrhleaveperyear ?? 0),
		                $excludeLeaveId
		            );

		            if (!$rhLeaveLimitCheck['allowed']) {
		                return response()->json([
		                    'success' => true,
		                    'leave_limit_blocked' => true,
		                    'message' => $rhLeaveLimitCheck['message'],
		                ]);
		            }

		            $isShortLeave = $isAutoApprovedLeaveType && $leaveDays > 0 && $leaveDays <= $maxShortLeaveDays;
            $scheduleDetails = TransactionFlowModel::getShortLeaveScheduleDetails(
                $sessionUser->userid,
                $sessionCharge->deptcode,
                $fromDate,
                $toDate
            );

            if ($isShortLeave) {
                $this->appendAutoMandaysPreview($scheduleDetails, $leaveDays);
            }

            if ($isShortLeave) {
	                $leaveExecutionCheck = $this->checkLeaveExecutionLimit($scheduleDetails, $sessionUser->userid, $excludeLeaveId, $fromDate, $toDate);

                if (!$leaveExecutionCheck['allowed']) {
                    return response()->json([
                        'success' => true,
                        'is_short_leave' => $isShortLeave,
                        'leave_days' => $leaveDays,
                        'max_short_leave_days' => $maxShortLeaveDays,
                        'has_schedule' => $scheduleDetails->isNotEmpty(),
                        'leave_execution_blocked' => true,
	                        'message' => 'Auto approval leave limit is already used for this schedule. This leave will be forwarded in normal flow. Do you want to continue?',
                        'data' => $leaveExecutionCheck['blocked_schedules'],
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'is_short_leave' => $isShortLeave,
                'leave_days' => $leaveDays,
                'max_short_leave_days' => $maxShortLeaveDays,
                'has_schedule' => $scheduleDetails->isNotEmpty(),
                'leave_execution_blocked' => false,
                'data' => $scheduleDetails,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to check schedule details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

	    private function calculateWorkingLeaveDays($fromDate, $toDate)
	    {
        $holidayDates = collect(TransactionFlowModel::getholidays())
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->flip();

	        $from = Carbon::parse($fromDate);
	        $to = Carbon::parse($toDate);

	        if ($from->gt($to)) {
	            return 0;
	        }

        $workingDays = 0;
        $current = $from->copy();

        while ($current->lte($to)) {
            $formattedDate = $current->format('Y-m-d');

            if (!$current->isWeekend() && !$holidayDates->has($formattedDate)) {
                $workingDays++;
            }

            $current->addDay();
        }

	        return $workingDays;
	    }

		    private function isAutoApprovedLeaveType($leaveTypeCode)
		    {
		        if (!$leaveTypeCode) {
		            return false;
	        }

		        return DB::table(self::$leavetype_table)
		            ->where('statusflag', 'Y')
		            ->where('leavetypecode', $leaveTypeCode)
		            ->where('autoapprovedflag', 'Y')
		            ->exists();
		    }

		    private function isRestrictedHolidayLeaveType($leaveTypeCode)
		    {
		        if (!$leaveTypeCode) {
		            return false;
		        }

		        $leaveType = DB::table(self::$leavetype_table)
		            ->where('statusflag', 'Y')
		            ->where('leavetypecode', $leaveTypeCode)
		            ->select('leavetypecode', 'leavetypeelname')
		            ->first();

		        if (!$leaveType) {
		            return false;
		        }

		        $leaveTypeCodeText = strtoupper(trim((string) $leaveType->leavetypecode));
		        $leaveTypeNameText = strtoupper(trim((string) $leaveType->leavetypeelname));

		        return $leaveTypeCodeText === 'RH'
		            || $leaveTypeNameText === 'RH'
		            || str_contains($leaveTypeNameText, 'RESTRICTED');
		    }

		    private function getRestrictedHolidayLeaveTypeCodes()
		    {
		        return DB::table(self::$leavetype_table)
		            ->where('statusflag', 'Y')
		            ->where(function ($query) {
		                $query->whereRaw('UPPER(TRIM(leavetypecode)) = ?', ['RH'])
		                    ->orWhereRaw('UPPER(TRIM(leavetypeelname)) = ?', ['RH'])
		                    ->orWhereRaw('UPPER(leavetypeelname) LIKE ?', ['%RESTRICTED%']);
		            })
		            ->pluck('leavetypecode')
		            ->filter()
		            ->values()
		            ->all();
		    }

		    private function getDeptRestrictedHolidayLimit($deptcode)
		    {
		        $dept = DB::table(self::$department_table)
		            ->where('statusflag', 'Y')
		            ->where('deptcode', $deptcode)
		            ->select('maxrhleaveperyear')
		            ->first();

		        return (int) ($dept->maxrhleaveperyear ?? 0);
		    }

		    private function checkRestrictedHolidayYearlyLimit($leaveTypeCode, $userid, $fromDate, $toDate, $maxRhLeavePerYear, $excludeLeaveId = null)
		    {
		        if (!$this->isRestrictedHolidayLeaveType($leaveTypeCode)) {
		            return ['allowed' => true];
		        }

		        $maxRhLeavePerYear = (int) $maxRhLeavePerYear;

		        if ($maxRhLeavePerYear <= 0) {
		            return ['allowed' => true];
		        }

		        $from = Carbon::parse($fromDate);
		        $to = Carbon::parse($toDate);

		        if ($from->gt($to)) {
		            return [
		                'allowed' => false,
		                'message' => 'To date must be same as or after from date.',
		            ];
		        }

		        $rhLeaveTypeCodes = $this->getRestrictedHolidayLeaveTypeCodes();

		        if (empty($rhLeaveTypeCodes)) {
		            $rhLeaveTypeCodes = [$leaveTypeCode];
		        }

		        for ($year = (int) $from->format('Y'); $year <= (int) $to->format('Y'); $year++) {
		            $yearStart = Carbon::create($year, 1, 1)->startOfDay();
		            $yearEnd = Carbon::create($year, 12, 31)->startOfDay();

		            $currentFrom = $from->copy()->greaterThan($yearStart) ? $from->copy() : $yearStart->copy();
		            $currentTo = $to->copy()->lessThan($yearEnd) ? $to->copy() : $yearEnd->copy();

		            if ($currentFrom->gt($currentTo)) {
		                continue;
		            }

		            $currentLeaveDays = $this->calculateCalendarLeaveDays($currentFrom->format('Y-m-d'), $currentTo->format('Y-m-d'));
		            $usedLeaveDays = $this->getExistingRestrictedHolidayLeaveDays(
		                $userid,
		                $rhLeaveTypeCodes,
		                $yearStart->format('Y-m-d'),
		                $yearEnd->format('Y-m-d'),
		                $excludeLeaveId
		            );

		            if (($usedLeaveDays + $currentLeaveDays) > $maxRhLeavePerYear) {
		                return [
		                    'allowed' => false,
		                    'message' => "You do not have enough restricted leave for this calendar year. Restricted leave allowed only {$maxRhLeavePerYear} days.",
		                ];
		            }
		        }

		        return ['allowed' => true];
		    }

		    private function getExistingRestrictedHolidayLeaveDays($userid, array $rhLeaveTypeCodes, $yearStart, $yearEnd, $excludeLeaveId = null)
		    {
		        $query = DB::table('audit.ind_leavedetail')
		            ->where('userid', $userid)
		            ->where('statusflag', 'Y')
		            ->where('processcode', '<>', 'I')
		            ->whereIn('leavetypecode', $rhLeaveTypeCodes)
		            ->whereDate('fromdate', '<=', $yearEnd)
		            ->whereDate('todate', '>=', $yearStart)
		            ->select('leaveid', 'fromdate', 'todate');

		        if ($excludeLeaveId) {
		            $query->where('leaveid', '<>', $excludeLeaveId);
		        }

		        return $query->get()->sum(function ($leave) use ($yearStart, $yearEnd) {
		            $fromDate = Carbon::parse($leave->fromdate)->greaterThan(Carbon::parse($yearStart))
		                ? Carbon::parse($leave->fromdate)->format('Y-m-d')
		                : Carbon::parse($yearStart)->format('Y-m-d');
		            $toDate = Carbon::parse($leave->todate)->lessThan(Carbon::parse($yearEnd))
		                ? Carbon::parse($leave->todate)->format('Y-m-d')
		                : Carbon::parse($yearEnd)->format('Y-m-d');

		            return $this->calculateCalendarLeaveDays($fromDate, $toDate);
		        });
		    }

		    private function calculateCalendarLeaveDays($fromDate, $toDate)
		    {
		        $from = Carbon::parse($fromDate);
		        $to = Carbon::parse($toDate);

		        if ($from->gt($to)) {
		            return 0;
		        }

		        return $from->diffInDays($to) + 1;
		    }

		    private function isAutoMandayExtensionEligible(Request $request, $deptcode)
		    {
	        if ($request->input('auto_manday_extension') != 'Y') {
	            return false;
	        }

	        if (!$this->isAutoApprovedLeaveType($request->input('leave_type'))) {
	            return false;
	        }

	        $fromDate = $this->normalizeLeaveDate($request->input('from_date'));
	        $toDate = $this->normalizeLeaveDate($request->input('to_date'));

	        if (!$fromDate || !$toDate || Carbon::parse($fromDate)->gt(Carbon::parse($toDate))) {
	            return false;
	        }

	        $leaveDays = $this->calculateWorkingLeaveDays($fromDate, $toDate);
	        $dept = DB::table(self::$department_table)
	            ->where('statusflag', 'Y')
	            ->where('deptcode', $deptcode)
	            ->select('maxshortleavedays')
	            ->first();

	        $maxShortLeaveDays = (int) ($dept->maxshortleavedays ?? 0);

	        return $leaveDays > 0 && $maxShortLeaveDays > 0 && $leaveDays <= $maxShortLeaveDays;
	    }

	    private function normalizeLeaveDate($date)
	    {
	        if (!$date) {
	            return null;
	        }

	        try {
	            if (strpos($date, '/') !== false) {
	                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
	            }

	            return Carbon::parse($date)->format('Y-m-d');
	        } catch (\Exception $e) {
	            return null;
	        }
	    }
	    private function getAutoMandaysExtensionSummary($auditscheduleid)
	    {
	        $summary = DB::table(BaseModel::MANDAYSEXTENSION)
	            ->where('auditscheduleid', $auditscheduleid)
	            ->where('transactiontypecode', View::shared('MandaysExtenstion'))
	            ->where('statusflag', 'Y')
	            ->where('processcode', View::shared('Approve_Processcode'))
	            ->where('createdby', 3763)
	            ->where('remarks', View::shared('autoremakrs'))
	            ->selectRaw('COALESCE(SUM(extramandays), 0) as existing_leave_days, MIN(oldpurposedexitmeetdate) as base_exit_date')
	            ->first();

	        return [
	            'existing_leave_days' => (int) ($summary->existing_leave_days ?? 0),
	            'base_exit_date' => $summary->base_exit_date ?? null,
	        ];
	    }
	  private function appendAutoMandaysPreview($scheduleDetails, $leaveDays)
	    {
	        foreach ($scheduleDetails as $schedule) {
	            $oldMandays = (int) ($schedule->mandays ?? 0);
	            $oldProposedExitDate = $schedule->proposedexitmeetdate ?? $schedule->todate;
	            $teamSize = max((int) ($schedule->teamsize ?? 1), 1);
	            $mandaysExtension = (int) $leaveDays * $teamSize;
	            $dateExtensionDays = (int) $leaveDays;
	
	            $schedule->extramandays = $mandaysExtension;
	            $schedule->oldmandays = $oldMandays;
	            $schedule->newmandays = $oldMandays + $mandaysExtension;
	            $schedule->oldproposedexitmeetdate = $oldProposedExitDate;
	            $schedule->newproposedexitmeetdate = $oldProposedExitDate
	                ? $this->calculateProposedExitMeetDate($oldProposedExitDate, $dateExtensionDays)
	                : null;
	        }
	    }

	    private function calculateProposedExitMeetDate($fromDate, $extensionDays)
	    {
	        if (!$fromDate) {
	            return null;
	        }

	        if ($extensionDays <= 0) {
	            return Carbon::parse($fromDate)->format('Y-m-d');
	        }

	        $result = DB::selectOne('
	            SELECT audit.calculatetodatewithmandaysteamsize(?, ?, ?, ?, ?) AS finalexitmeetdate
	        ', [
	            Carbon::parse($fromDate)->format('Y-m-d'),
	            $extensionDays + 1,
	            0,
	            0,
	            'workingdays',
	        ]);

	        $calculatedDate = $result->finalexitmeetdate ?? null;

	        if ($calculatedDate && Carbon::parse($calculatedDate)->gt(Carbon::parse($fromDate))) {
	            return $calculatedDate;
	        }

	        return $this->addWorkingDaysFromDate($fromDate, $extensionDays);
	    }

	    private function checkLeaveExecutionLimit($scheduleDetails, $userid, $excludeLeaveId = null, $currentFromDate = null, $currentToDate = null)
	    {
	        $blockedSchedules = collect();

	        foreach ($scheduleDetails as $schedule) {
		            $allowedLeaveDays = (int) ($schedule->leaveextention ?? 0);

	            if ($allowedLeaveDays <= 0) {
	                continue;
	            }

            $scheduleStartDate = $schedule->entrymeetdate ?? $schedule->fromdate;
            $scheduleEndDate = $schedule->proposedexitmeetdate ?? $schedule->todate;

            if (!$scheduleStartDate || !$scheduleEndDate) {
                continue;
            }

	            $existingLeaveQuery = DB::table('audit.ind_leavedetail')
	                ->where('userid', $userid)
	                ->where('statusflag', 'Y')
	                ->where('processcode', '<>', 'I')
	                ->whereDate('fromdate', '<=', Carbon::parse($scheduleEndDate)->format('Y-m-d'))
	                ->whereDate('todate', '>=', Carbon::parse($scheduleStartDate)->format('Y-m-d'))
	                ->select('leaveid', 'fromdate', 'todate');

	            if ($excludeLeaveId) {
	                $existingLeaveQuery->where('leaveid', '<>', $excludeLeaveId);
	            }

	            $existingLeaveDays = $existingLeaveQuery->get()->sum(function ($leave) use ($scheduleStartDate, $scheduleEndDate) {
	                return $this->calculateOverlappingWorkingLeaveDays(
	                    $leave->fromdate,
	                    $leave->todate,
	                    $scheduleStartDate,
	                    $scheduleEndDate
	                );
	            });

	            $currentLeaveDays = 0;

	            if ($currentFromDate && $currentToDate) {
	                $currentLeaveDays = $this->calculateOverlappingWorkingLeaveDays(
	                    $currentFromDate,
	                    $currentToDate,
	                    $scheduleStartDate,
	                    $scheduleEndDate
	                );
	            }

		            $schedule->leaveextention_used = $existingLeaveDays;
		            $schedule->leaveextention_available = max($allowedLeaveDays - $existingLeaveDays, 0);

	            if (($existingLeaveDays + $currentLeaveDays) > $allowedLeaveDays) {
	                $blockedSchedules->push($schedule);
	            }
	        }

        return [
            'allowed' => $blockedSchedules->isEmpty(),
            'blocked_schedules' => $blockedSchedules,
        ];
    }

    private function addWorkingDaysFromDate($fromDate, $daysToAdd)
    {
        $holidayDates = collect(TransactionFlowModel::getholidays())
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->flip();

        $current = Carbon::parse($fromDate)->copy();
        $remainingDays = (int) $daysToAdd;

        while ($remainingDays > 0) {
            $current->addDay();

            if (!$current->isWeekend() && !$holidayDates->has($current->format('Y-m-d'))) {
                $remainingDays--;
            }
        }

        return $current->format('Y-m-d');
    }

		    private function applyAutoMandaysExtensionForLeave($leaveId, $userid, $deptcode, $fromDate, $toDate, $sessionRoleTypeCode, $sessionUserChargeId)
		    {
		        return TransactionFlowModel::autoApproveMandaysForLeave([
		            'leaveid' => $leaveId,
	            'userid' => $userid,
	            'deptcode' => $deptcode,
	            'fromdate' => $fromDate,
	            'todate' => $toDate,
	            'createdbyroletypecode' => $sessionRoleTypeCode,
	            'transactiontypecode' => View::shared('MandaysExtenstion'),
		            'approveprocesscode' => View::shared('Approve_Processcode'),
		            'remarks' => View::shared('autoremakrs'),
		            'systemuserid' => 3763,
		            'sessionuserchargeid' => $sessionUserChargeId,
		        ]);
		    }

	    private function resolveAutoMandayRoleTypeCode($sessionChargeData, $userid = null)
	    {
	        $roleTypeCode = trim((string) ($sessionChargeData->roletypecode ?? ''));

	        if ($roleTypeCode !== '') {
	            return $roleTypeCode;
	        }

	        $chargeId = $sessionChargeData->chargeid ?? null;
	        $userChargeId = $sessionChargeData->userchargeid ?? null;

	        if ($userChargeId || $chargeId || $userid) {
	            $userChargeQuery = DB::table('audit.userchargedetails as uc')
	                ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
	                ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'ch.rolemappingid')
	                ->join('audit.roletypemapping as rtm', 'rtm.roletypemappingcode', '=', 'rm.roletypemappingcode')
	                ->where('uc.statusflag', 'Y')
	                ->where('ch.statusflag', 'Y');

	            if ($userChargeId) {
	                $userChargeQuery->where('uc.userchargeid', $userChargeId);
	            } elseif ($chargeId) {
	                $userChargeQuery->where('uc.chargeid', $chargeId);
	            } elseif ($userid) {
	                $userChargeQuery->where('uc.userid', $userid);
	            }

	            $roleTypeCode = trim((string) $userChargeQuery->value('rtm.roletypecode'));

	            if ($roleTypeCode !== '') {
	                return $roleTypeCode;
	            }
	        }

	        if (!$chargeId) {
	            return null;
	        }

	        $roleTypeCode = trim((string) DB::table('audit.chargedetails as ch')
	            ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'ch.rolemappingid')
	            ->join('audit.roletypemapping as rtm', 'rtm.roletypemappingcode', '=', 'rm.roletypemappingcode')
	            ->where('ch.chargeid', $chargeId)
	            ->value('rtm.roletypecode'));

	        return $roleTypeCode !== '' ? $roleTypeCode : null;
	    }

	    private function calculateOverlappingWorkingLeaveDays($leaveFromDate, $leaveToDate, $rangeFromDate, $rangeToDate)
	    {
	        $leaveFrom = Carbon::parse($leaveFromDate);
	        $leaveTo = Carbon::parse($leaveToDate);
	        $rangeFrom = Carbon::parse($rangeFromDate);
	        $rangeTo = Carbon::parse($rangeToDate);

	        $fromDate = $leaveFrom->greaterThan($rangeFrom) ? $leaveFrom : $rangeFrom;
	        $toDate = $leaveTo->lessThan($rangeTo) ? $leaveTo : $rangeTo;

	        if ($fromDate->gt($toDate)) {
	            return 0;
	        }

	        return $this->calculateWorkingLeaveDays($fromDate->format('Y-m-d'), $toDate->format('Y-m-d'));
	    }



	   public function storeOrUpdateLeave(Request $request)
    {
        try {
            $userSessionData = session('user');
            $userid = $userSessionData->userid;

            $chargedel = session('charge');
            $userchargeid = $chargedel->userchargeid;

	            $from_date = Carbon::createFromFormat('d/m/Y', $request->input('from_date'))->format('Y-m-d');
	            $to_date = Carbon::createFromFormat('d/m/Y', $request->input('to_date'))->format('Y-m-d');

	            if (Carbon::parse($from_date)->gt(Carbon::parse($to_date))) {
	                return response()->json([
	                    'status' => 'error',
	                    'message' => 'To date must be same as or after from date.',
	                ], 422);
	            }

	            $request->merge(['from_date' => $from_date, 'to_date' => $to_date]);

            $request->validate([
                'from_date' => 'required|date|date_format:Y-m-d',
                'to_date' => 'required|date|date_format:Y-m-d',
                'leave_type' => 'required',
                'reason' => 'required',
            ], [
                'required' => 'The :attribute field is required.',
                'from_date.date' => 'The from date must be a valid date.',
                'to_date.date' => 'The to date must be a valid date.',
                'from_date.date_format' => 'The from date must be in the format Y-m-d.',
                'to_date.date_format' => 'The to date must be in the format Y-m-d.',
            ]);

            $request->merge(['from_date' => $from_date, 'to_date' => $to_date, 'userid' => $userid, 'userchargeid' => $userchargeid]);

            if ($request->finaliseflag == 'F') {
                $request->merge([
                    'transactiontypecode' => View::shared('Leavetransactiontypecode'),
                ]);
                return $this->forward_application($request);
            }

            $data = [
                'userid' => $userid,
                'fromdate' => $request->input('from_date'),
                'todate' => $request->input('to_date'),
                'leavetypecode' => $request->input('leave_type'),
                'reason' => $request->input('reason'),
                'statusflag' => $request->input('finaliseflag'),
                'updatedon' => View::shared('get_nowtime'),
                'updatedby' => $userid,
                'updatedbyuserchargeid' => $userchargeid,
	                'processcode' => View::shared('Insert'),
	                'transactiontypecode' => View::shared('Leavetransactiontypecode'),
                'longleave' => $request->input('longleave'),
                'leavedayscount' => $request->input('leavedays'),
	            ];

            if ($request->action == 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdbyuserchargeid'] = $userchargeid;
                $data['createdby'] = $userid;
            }

	            $leave_id = $request->action === 'update'
	                ? Crypt::decryptString($request->input('leave_id'))
	                : null;

		            $rhLeaveLimitCheck = $this->checkRestrictedHolidayYearlyLimit(
		                $request->input('leave_type'),
		                $userid,
		                $from_date,
		                $to_date,
		                $this->getDeptRestrictedHolidayLimit($chargedel->deptcode),
		                $leave_id
		            );

		            if (!$rhLeaveLimitCheck['allowed']) {
		                return response()->json([
		                    'status' => 'error',
		                    'message' => $rhLeaveLimitCheck['message'],
		                ], 400);
		            }

		            if ($this->isAutoMandayExtensionEligible($request, $chargedel->deptcode)) {
	                $scheduleDetails = TransactionFlowModel::getShortLeaveScheduleDetails(
	                    $userid,
	                    $chargedel->deptcode,
	                    $from_date,
	                    $to_date
	                );
	                $leaveExecutionCheck = $this->checkLeaveExecutionLimit($scheduleDetails, $userid, $leave_id, $from_date, $to_date);

	                if (!$leaveExecutionCheck['allowed']) {
	                    $request->merge(['auto_manday_extension' => 'N']);
	                }
	            }

	            // Call the model function
	            $result = TransactionFlowModel::createleave_insertupdate($data, $leave_id, 'audit.ind_leavedetail', $userid, 'form');

            // Handle result
            if (in_array($result['status'], ['inserted', 'updated'])) {
                return response()->json([
                    // 'success' => true,
                    'status' => 'success',
                    'message' => 'Leave application was ' . $result['status'] . ' successfully.',
                    'data' => $result['data']
                ]);
            } elseif ($result['status'] === 'failed') {
                return response()->json([
                    // 'success' => false,
                    'status' => 'error',
                    'message' => $result['message']
                ], 400);
            } else {
                return response()->json([
                    // 'success' => false,
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Unexpected error occurred.'
                ], 500);
            }
        } catch (\Exception $e) {
            // Log the error if needed (optional)
            // Log::error('Leave Store/Update Error: ' . $e->getMessage());

            return response()->json([
                // 'success' => false,
                'status' => 'error',
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function fetchall_leavedata()
    {
        $userSessionData = session('user');
        if (!$userSessionData || !isset($userSessionData->userid)) {
            return redirect()->back()->withErrors(['Session expired or data missing.']);
        }
        $userid = $userSessionData->userid;
        $leavedetail = TransactionFlowModel::fetchalldata($userid);


        foreach ($leavedetail as $item) {
            $item->encrypted_leaveid = Crypt::encryptString($item->leaveid);
            $item->transactiontypecode = View::shared('Leavetransactiontypecode');
            unset($item->leaveid);
        }


        if ($leavedetail) {
            return response()->json(['success' => true, 'data' => $leavedetail]);
        } else {
            return response()->json(['success' => false, 'message' => 'Leave Details was not found'], 404);
        }
    }



	    public function fetchsingle_data(Request $request)
	    {
        $leaveid = Crypt::decryptString($request->leaveid);

        try {
            // Call the model method for create or update
            $single_leavedetail = TransactionFlowModel::fetchsingle_data($leaveid, 'audit.ind_leavedetail');

            foreach ($single_leavedetail as $item) {
                $item->encrypted_leaveid = Crypt::encryptString($item->leaveid);
            }


            if ($single_leavedetail) {
                return response()->json(['success' => true, 'data' => $single_leavedetail]);
            } else {
                return response()->json(['success' => false, 'message' => 'Leave Detail was not found'], 404);
            }
        } catch (\Exception $e) {
            // Catch the exception thrown by the model and return the error message
            return response()->json(['error' => $e->getMessage()], 400);
	        }
	    }
///leave apply end


	    public function getinstitutiondel(Request $request)
    {
        $auditscheduleid =  $request->auditscheduleid;
        $data =  TransactionFlowModel::getinstitutiondel($auditscheduleid);
        // print_r($data);
        // exit;
        return response()->json(['success' => true, 'data' => $data]);
    }




     public function insert_datatransfer(Request $request)
    {
        try {
            $sessionchargedel = session('charge');
            $sessionuserdel = session('user');

            if (!$sessionchargedel || !$sessionuserdel) {
                throw new \Exception('Session expired. Please log in again.');
            }

            $userchargeid = $sessionchargedel->userchargeid;
            $userid = $sessionuserdel->userid;

            if ($request['transactiontypecode'] == View::shared('LeaveIntransactiontypecode')) {
                $data = TransactionFlowModel::approveleavein($request, $userid, $userchargeid);
            } else {
                $data = TransactionFlowModel::insert_datatransfer($request, $userid, $userchargeid);
            }

            if ($data['status'] === 'success') {
                return response()->json(['success' => true, 'message' => 'Transfer completed successfully']);
            } else {
                return response()->json(['success' => false, 'error' => $data['message']], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }




    public function getworkalloactionbasedonSchedulemember(Request $request)
    {
        $auditscheduleid =  $request->auditscheduleid;
        $schememberid =  $request->schememberid;
        $data =  TransactionFlowModel::getworkalloactionbasedonSchedulemember($auditscheduleid, $schememberid);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public  function getslipdetailsbasedon_schedulemember(Request $request)
    {

        $auditscheduleid =  $request->auditscheduleid;
        $schememberid =  $request->schememberid;
        $data =  TransactionFlowModel::getslipdetailsbasedon_schedulemember($auditscheduleid, $schememberid);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function transactionapproveddetails(Request $request)
    {

        $session_user = session('user');
        $session_charge = session('charge');
        $deptcode = $session_charge->deptcode;
        $regioncode = $session_charge->regioncode;
        $distcode = $session_charge->distcode;


        $getapproveddetails = TransactionFlowModel::getapproveddetails($deptcode, $regioncode, $distcode);
        // print_r($getapproveddetails);
        // exit;

        return view('transactionflow.approveddetails', compact('getapproveddetails'));
    }


    public function viewdatatransferdel(Request $request)
    {
        $othertransid = $request->othertransid;
        $transactiontypecode = $request->transactiontypecode;

        $data = TransactionFlowModel::getdatatransferdel($othertransid, $transactiontypecode);

        return response()->json([
            'result1' => $data['query1'],
            'result2' => $data['query2'],
            'result3' => $data['query3']
        ]);
    }

public function schedulerequestdata_dropdown(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        $transtypecode = $request->transtype;

        $schedulrequestdel = TransactionFlowModel::getschedulereqdel($id, $transtypecode);

        $holidays = TransactionFlowModel::getholidays();

        // if ($schedulrequestdel) {

        //     $auditscheduleid =  $schedulrequestdel[0]->auditscheduleid;
        //     $othermembers = $this->getothermembers($auditscheduleid);

        //     $headuserid =  $schedulrequestdel[0]->headuserid;
        //     $otherteamhead =  TransactionFlowModel::fetch_otherteamhead($headuserid);
        // }

        return view('transactionflow.schedulerequest_datatransfer', compact('schedulrequestdel', 'holidays'));
    }

 public function schedulerequestpagecompact($viewname)
    {
        $sessionuserdel = session('user');
        $sessionchargedel = session('charge');

        $sessionroletypecode = $sessionchargedel->roletypecode;

        $sessionuserid = $sessionuserdel->userid;
        $dept = TransactionFlowModel::getdeptbasedonsession();

        $userstatus = TransactionFlowModel::getsessionrequestdel($sessionuserid);
        if ($sessionroletypecode == View::shared('DGA_roletypecode'))
            $userstatus = 'S';
        $holidays = TransactionFlowModel::getholidays();

        if ($userstatus == 'H' || $userstatus == 'S') {
            $request_option = [
                '09' => 'Mandays Extension'
            ];
        } else
            $request_option = [];

        return view($viewname, compact('dept', 'userstatus', 'request_option', 'holidays'));
    }

     public function getscheduledel(Request $request)
    {
        // try {
        $userstatus = $request->userstatus;
        $reasoncode = $request->reasoncode;
        $chargedel = session('charge');
        $sessionroletypecode = $chargedel->roletypecode;

        if ($request->auditscheduleid)
            $auditscheduleid = $request->has('auditscheduleid') ? Crypt::decryptString($request->auditscheduleid) : null;
        else
            $auditscheduleid = $request->auditscheduleid;

        $sessionuserdel = session('user');
        $sessionuserid = $sessionuserdel->userid ?? null;

        if (!$sessionuserid) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        // if (View::shared('DGA_roletypecode') == $sessionroletypecode) {
        //     print_r($request->all());
        // }

        $scheduledel = TransactionFlowModel::getscheduledel($reasoncode, $sessionuserid, $auditscheduleid, $sessionroletypecode);
        // return $scheduledel;
        // exit;

        if ($scheduledel) {
            foreach ($scheduledel as $all) {
                $all->auditscheduleid = Crypt::encryptString($all->auditscheduleid);
            }
            return response()->json(['success' => true, 'data' => $scheduledel]);
        } else {
            return response()->json(['success' => false, 'message' => 'No schedule found.']);
        }
        // } catch (\Exception $e) {
        //     // Optional: Log the error for debugging

        //     return response()->json([
        //         'success' => false,
        //         'message' => 'An error occurred while processing schedule deletion.',
        //         'error' => $e->getMessage()
        //     ], 400);
        // }
    }
///////////Mandays state ////////////////////

public function fetch_deptbaseddata(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode' => ['nullable', 'string', 'regex:/^\d+$/'],
            'distcode' => ['nullable', 'string', 'regex:/^\d+$/'],
            'valuefor' => ['required', 'string', 'in:region,district,inst'],  // Include "region"
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
            'in' => 'The :attribute field must be one of: region, district, inst',
        ]);

        // Extract validated data
        $deptcode = $validatedData['deptcode'];
        $regioncode = $validatedData['regioncode'] ?? null;
        $distcode = $validatedData['distcode'] ?? null;
        $valuefor = $validatedData['valuefor'];

        if (($valuefor === 'region' && !$deptcode)) {
            return response()->json(['success' => false, 'message' => 'Department code is required for Region.'], 422);
        }

        // Additional validation for 'district'
        if (($valuefor === 'district' && !$regioncode)) {
            return response()->json(['success' => false, 'message' => 'Region code is required for district.'], 422);
        }
        if ($valuefor === 'district' && !$deptcode) {
            return response()->json(['success' => false, 'message' => 'Department code is required for district.'], 422);
        }
        if ($valuefor === 'inst' && !($deptcode || $regioncode || $distcode)) {
            return response()->json(['success' => false, 'message' => 'Essential field is required for Institution.'], 422);
        }

        // Additional validation for 'institution'

        try {
            $getdata = TransactionFlowModel::fetch_deptbaseddata(
                $deptcode,
                $regioncode,
                $distcode,
                $valuefor
            );
            // return $getdata;
            if ($getdata) {
                if ($valuefor == 'inst') {
                    foreach ($getdata['data'] as $item) {
                        $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);

                        // unset($item->auditscheduleid);
                    }
                }
            }

            return response()->json(['success' => true, 'data' => $getdata['data']]);
            // }

            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

      public function schedulerequest_insertupdate(Request $request, $userId = null)
    {
        // print_r($_POST);
        // exit;
        try {
            // Session Data
            $user = session('user');
            $charge = session('charge');

            $sessionroletypecode = $charge->roletypecode;

            if (!$user || !$charge || !isset($charge->userchargeid)) {
                return response()->json([
                    'success' => false,
                    'message' => 'User or charge session not found.'
                ], 400);
            }

            $sessionUserId = $user->userid;
            $sessionUserChargeId = $charge->userchargeid;
            $sessionRoleMappingId = $charge->rolemappingid;
            $sessionDeptCode = $charge->deptcode;
            $sessionDistCode = $charge->distcode;

            if ((View::shared('DGA_roletypecode') == $sessionroletypecode)) {
                $requestedexitmeetdate = Carbon::createFromFormat('d/m/Y', $request->input('requestedexitmeetdate'))->format('Y-m-d');
                $request->merge(['requestedexitmeetdate' => $requestedexitmeetdate]);
            }

            // echo $request['reason'];
            // echo View::shared('ScheduleRequest');

            if ($request['reasoncode'] == View::shared('ScheduleRequest')) {
                $requestedexitmeetdate = Carbon::createFromFormat('d/m/Y', $request->input('exitmeetingdate'))->format('Y-m-d');
                $request->merge(['exitmeetingdate' => $requestedexitmeetdate]);
                $leaveids = [];

                foreach ($_POST['leaveid'] as $leaveid) {
                    // echo $leaveid;
                    $leaveid = Crypt::decryptString($leaveid);
                    $leaveid_dec[] = $leaveid;
                    // Decrypt or treat as JSON string
                    $leaveids[] = json_decode($leaveid, true);  // true returns associative array
                }

                foreach ($_POST['userid'] as $userid) {
                    $userid = Crypt::decryptString($userid);
                    $userid_dec[] = $userid;
                    // Decrypt or treat as JSON string
                    $userids[] = json_decode($userid, true);  // true returns associative array
                }

                // Final JSON to insert into DB
                $leaveidjson = json_encode(['leaveid' => $leaveids], JSON_UNESCAPED_SLASHES);
                $useridjson = json_encode(['userid' => $userids], JSON_UNESCAPED_SLASHES);
            }

            // Decrypt auditscheduleid
            $request->merge([
                'auditscheduleid' => Crypt::decryptString($request->auditscheduleid)
            ]);

            // Validation
            $rules = [
                'reasoncode' => ['required', 'string', 'regex:/^\d+$/'],
                'auditscheduleid' => 'required|integer',
                'remarks' => ['required', 'regex:/^[\p{Tamil}A-Za-z0-9\s]+$/u', 'max:200'],
                'exitmeetingdate' => 'required|date|date_format:Y-m-d',
                'action' => 'required|in:insert,update',
                'oldmandays' => 'required|integer',
                'teamsize' => 'required|integer',
            ];

            // Conditionally add rules
            if (View::shared('DGA_roletypecode') == $sessionroletypecode) {
                $rules['extendmandays'] = 'required|integer';
                $rules['requestedexitmeetdate'] = 'required|date|date_format:Y-m-d';
            }

            // Create the validator with the full ruleset
            $validator = Validator::make($request->all(), $rules);

            // Handle failure
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // $mandaysextensionid = null;

            $mandaysextensionid = $request->action === 'update'
                ? Crypt::decryptString($request->input('mandaysextensionid'))
                : null;

            DB::beginTransaction();

            // Prepare data
            $transactionTypeCode = $request->input('reasoncode');
            $now = View::shared('get_nowtime');

            if ($request['reasoncode'] == View::shared('MandaysExtenstion')) {
                $insertData = [
                    'transactiontypecode' => $transactionTypeCode,
                    'auditscheduleid' => $request->input('auditscheduleid'),
                    'remarks' => $request->input('remarks'),
                    'statusflag' => 'Y',
                    'updatedby' => $sessionUserId,
                    'updatedon' => $now,
                    'oldmandays' => $request->input('oldmandays'),
                    'oldpurposedexitmeetdate' => $request->input('exitmeetingdate'),
                    'teamsize' => $request->input('teamsize'),
                    'createdbyroletypecode' => $sessionroletypecode
                    // 'requestedexitmeetdate'   => $request->input('requestedexitmeetdate')
                ];
            } else if ($request['reasoncode'] == View::shared('ScheduleRequest')) {
                $insertData = [
                    'transactiontypecode' => $transactionTypeCode,
                    'auditscheduleid' => $request->input('auditscheduleid'),
                    'entrymeetdate' => $request->input('entrymeetdate'),
                    'remarks' => $request->input('remarks'),
                    'statusflag' => 'Y',
                    'updatedby' => $sessionUserId,
                    'updatedon' => $now,
                    'mandays' => $request->input('oldmandays'),
                    'exitmeetdate' => $request->input('exitmeetingdate'),
                    'teamsize' => $request->input('teamsize'),
                    'createdbyroletypecode' => $sessionroletypecode,
                    'leaveid' => $leaveidjson
                    // 'requestedexitmeetdate'   => $request->input('requestedexitmeetdate')
                ];
            }

            if ($request->action == 'insert') {
                $insertData['createdby'] = $sessionUserId;
                $insertData['createdon'] = $now;
            }

            if (View::shared('DGA_roletypecode') == $sessionroletypecode) {
                $insertData['extramandays'] = $request->input('extendmandays');
                $insertData['newpurposedexitmeetdate'] = $request->input('requestedexitmeetdate');
                $newmandays = $request->input('oldmandays') + $request->input('extendmandays');
                $insertData['newmandays'] = $newmandays;

                $username = CryptoHelper::decryptPassword($request->input('username'));
                $email = CryptoHelper::decryptPassword($request->input('email'));

                if ($request->input('finaliseflag') == View::shared('Forward')) {
                    $insertData['processcode'] = View::shared('Approve_Processcode');
                } else {
                    $insertData['processcode'] = View::shared('Entry_processcode');
                }

                $insertResult = TransactionFlowModel::mandaysextension_insert($insertData, $mandaysextensionid, $sessionroletypecode);

                if ($insertResult['status'] !== 'success') {
                    DB::rollBack();
                    return response()->json([
                        'error' => 'true',
                        'message' => $insertResult['error']
                    ]);
                }

                // If "Forward", update related tables
                if ($request->input('finaliseflag') == View::shared('Forward')) {
                    $auditscheduleid = $request->input('auditscheduleid');

                    $updatescheduledel = [
                        'proposedexitmeetdate' => $request->input('requestedexitmeetdate'),
                    ];

                    $updateauditplandel = [
                        'mandays' => $newmandays,
                        'sessionuserid' => $sessionUserId,
                    ];

                    // 🔁 Update inst_auditschedule
                    $scheduleUpdate = TransactionFlowModel::update_scheduledel($updatescheduledel, $auditscheduleid);
                    if (!$scheduleUpdate) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Failed to update schedule details'
                        ]);
                    }

                    // 🔁 Update auditplan
                    $planUpdate = TransactionFlowModel::update_plandel($updateauditplandel, $auditscheduleid);
                    if (!$planUpdate) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Failed to update audit plan details'
                        ]);
                    }
                }

                DB::commit();

                if ($request->input('finaliseflag') == View::shared('Forward')) {
                    $text = 'Mandays extension has been approved successfully';
                    $data['username'] = $username;
                    $data['email'] = $email;
                    $data['schedulename'] = $request->input('schedulename');
                    $data['for'] = 'approval';

                    $smsmailmodel = new SmsmailModel(new SmsService(), new PHPMailerService());
                    $sentsms = $smsmailmodel->sendmandaysextention($data);
                } else
                    $text = 'Mandays extension has been insert/updated successfully';

                return response()->json([
                    'success' => 'true',
                    'message' => $text
                ]);
            } else {
                // Get forwarding user
                $forwardResult = TransactionFlowModel::getforwardtouserid(
                    $transactionTypeCode,
                    $sessionDeptCode,
                    $sessionDistCode,
                    $sessionRoleMappingId
                );

                $data['username'] = $forwardResult['data']->username;
                $data['email'] = $forwardResult['data']->email;
                $data['schedulename'] = $request->input('schedulename');
                $data['for'] = 'request';

                if ($forwardResult['status'] !== 'success') {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => $forwardResult['error']
                    ]);
                }

                $forwardedUser = $forwardResult['data'];
                // $insertData['forwardedtouserid'] = $forwardedUser->userid;
                // $insertData['forwardedtouserchargeid'] = $forwardedUser->userchargeid;
                $insertData['processcode'] = View::shared('Forward');

                if ($request['reasoncode'] == View::shared('MandaysExtenstion')) {
                    // Insert into mandays extension
                    $insertResult = TransactionFlowModel::mandaysextension_insert($insertData, '', $sessionroletypecode);
                } else if ($request['reasoncode'] == View::shared('ScheduleRequest')) {
                    // Insert into mandays extension

                    $insertResult = TransactionFlowModel::schedulerequest_insert($insertData, '', $sessionroletypecode, $leaveid_dec, $userid_dec);
                }

                if ($insertResult['status'] !== 'success') {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => $insertResult['error']
                    ]);
                }

                // Insert into history transaction
                $historyData = [
                    'transactiontypecode' => $transactionTypeCode,
                    'processcode' => $insertData['processcode'],
                    'forwardedtouserchargeid' => $forwardedUser->userchargeid,
                    'forwardedbyuserchargeid' => $sessionUserChargeId,
                    'forwardedon' => $now,
                    'statusflag' => 'Y',
                    'transstatus' => 'A',
                    // 'mandaysextensionid' => $mandaysextensionId
                ];

                $transDetailData = [
                    'transactiontypecode' => $transactionTypeCode,
                    'forwardedtouserchargeid' => $forwardedUser->userchargeid,
                    'updatedbyuserchargeid' => $sessionUserChargeId,
                    'updatedon' => $now,
                    'statusflag' => 'Y',
                    'createdbyuserchargeid' => $forwardedUser->userchargeid,
                    'createdon' => $now,
                    // 'mandaysextensionid' => $mandaysextensionId
                ];

                if ($request['reasoncode'] == View::shared('MandaysExtenstion')) {
                    $mandaysextensionId = $insertResult['data'];
                    $historyData['mandaysextensionid'] = $mandaysextensionId;
                    $transDetailData['mandaysextensionid'] = $mandaysextensionId;
                    $where = ['mandaysextensionid' => $mandaysextensionId];
                } else {
                    $schedulrequestid = $insertResult['data'];
                    $historyData['schedulerequestid'] = $schedulrequestid;
                    $transDetailData['schedulerequestid'] = $schedulrequestid;
                    $where = ['schedulerequestid' => $schedulrequestid];
                }

                $historyResult = TransactionFlowModel::insert_historyTransDetail($historyData, $where);

                // print_r($historyResult);

                if ($historyResult['status'] !== 'inserted') {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to insert history transaction.'
                    ]);
                }

                // Insert/update transaction detail

                $transDetailResult = TransactionFlowModel::insertupdate_transdet($transDetailData, $where);

                // print_r($transDetailResult);

                if (!in_array($transDetailResult['status'], ['inserted', 'updated'])) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to insert/update transaction detail.'
                    ]);
                }

                DB::commit();

                $smsmailmodel = new SmsmailModel(new SmsService(), new PHPMailerService());
                $sentsms = $smsmailmodel->sendmandaysextention($data);

                return response()->json([
                    'success' => 'true',
                    'message' => 'Request submitted successfully'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Unexpected server error.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



	    public function fetchdata_schedulerequest(Request $request)
    {
        try {
            $sessionuserdel = session('user');
            $sessionuserid = $sessionuserdel->userid;

            $mandaysextensionid = $request->filled('mandaysextenstionid') ? Crypt::decryptString($request->mandaysextenstionid) : null;

            // Fetch data using the model
            $userdel = TransactionFlowModel::fetchschedulesrequest($sessionuserid, $mandaysextensionid);

            foreach ($userdel as $item) {
                $item->mandaysextensionid = Crypt::encryptString($item->mandaysextensionid);
                $item->enc_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
            }

            // If userid is not provided (fetch mode)
            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $userdel->isEmpty() ? null : $userdel
            ], 200);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

	public function schedulerequest_approve(Request $request)
    {
        try {
            $sessionchargedel = session('charge');
            $sessionuserdel = session('user');

            if (!$sessionchargedel || !$sessionuserdel) {
                throw new \Exception('Session expired. Please log in again.');
            }

            $userchargeid = $sessionchargedel->userchargeid;
            $userid = $sessionuserdel->userid;

            $data = TransactionFlowModel::schedulerequest_approve($request, $userid, $userchargeid);

            if ($data['status'] === 'success') {
                return response()->json(['success' => true, 'message' => 'Extension of Manday(s) approved successfully']);
            } else {
                return response()->json(['success' => false, 'error' => $data['message']], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

 ////////leave in start ////////////
   public function leaveindetails(Request $request)
    {
        $user = session('user');
        $userid = $user->userid;
        // $getdata = TransactionFlowModel::leaveindetails($userid);
        // return $getdata;
        // return view($, compact('getdata'));

        try {
            // Check if userid is provided

            // Fetch data using the model
            $getdata = TransactionFlowModel::leaveindetails($userid);

            // Encrypt user IDs in results
            // $userdel->transform(function ($all) {
            //     $all->encrypted_userid = Crypt::encryptString($all->deptuserid);
            //     return $all;
            // });

            foreach ($getdata as $all) {
                $all->leaveid = Crypt::encryptString($all->leaveid);
                $all->userid = Crypt::encryptString($all->userid);
            }

            // If userid is not provided (fetch mode)
            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $getdata->isEmpty() ? null : $getdata
            ], 200);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching user data'
            ], 500);
        }
    }

    public function forwardin(Request $request)
    {
        $transtypecode == View::shared('LeaveIntransactiontypecode');
        DB::beginTransaction();  // Begin a new transaction

        try {
            $id = Crypt::decryptString($request->id);
            $userid = Crypt::decryptString($request->userid);

            // Get session data
            $userSessionData = session('user');
            $userSessionChargeData = session('charge');

            // Validate session data
            if (!$userSessionData || !isset($userSessionData->userid)) {
                return redirect()->back()->withErrors(['Session expired or data missing.']);
            }

            $sessionchargeid = $userSessionChargeData->userchargeid;
            $userid = $userSessionData->userid;

            // Forward the application to the next level based on transaction type code
            $forwarddel = TransactionFlowModel::forwardtonextlevel($transtypecode, $detailuserid, $action);

            if (count($forwarddel) == 1) {
                $forwardtouserchargeid = $forwarddel[0]->userchargeid;

                // Determine process code based on action (Approve or Forward)
                $process = $trans_action == 'Approve' ? View::shared('Approve') : View::shared('Forward');

                // Prepare the transaction detail data
                $transactiondel_data = [
                    'forwardedtouserchargeid' => $forwardtouserchargeid,
                    'updatedbyuserchargeid' => $sessionchargeid,
                    'updatedon' => View::shared('get_nowtime'),
                ];

                if ($action == 'first') {
                    $transactiondel_data['userid'] = $detailuserid;
                    $transactiondel_data['transactiontypecode'] = $transtypecode;
                    $transactiondel_data['createdbyuserchargeid'] = $sessionchargeid;
                    $transactiondel_data['createdon'] = View::shared('get_nowtime');
                    $transactiondel_data['statusflag'] = 'Y';

                    // Set where condition based on transaction type
                    if ($transtypecode == View::shared('Leavetransactiontypecode')) {
                        $transactiondel_data['leaveid'] = $id;
                        $where = ['leaveid' => $id];
                    } else {
                        $transactiondel_data['othertransid'] = $id;
                        $where = ['othertransid' => $id];
                    }
                }

                // Prepare main table update data
                $maintableUpdate = [
                    'processcode' => $process,
                    'updatedon' => View::shared('get_nowtime'),
                ];

                if ($transtypecode != View::shared('Leavetransactiontypecode')) {
                    $maintableUpdate['updatedbyuserchargeid'] = $sessionchargeid;
                } else {
                    $maintableUpdate['updatedby'] = $userid;
                }  // Prepare history transaction data

                $historytransaction_data = [
                    'userid' => $detailuserid,
                    'transactiontypecode' => $transtypecode,
                    'processcode' => $process,
                    'forwardedtouserchargeid' => $forwardtouserchargeid,
                    'forwardedbyuserchargeid' => $sessionchargeid,
                    'forwardedon' => View::shared('get_nowtime'),
                    'statusflag' => 'Y',
                    'transstatus' => 'A',
                ];

                if ($transtypecode == View::shared('Leavetransactiontypecode')) {
                    $historytransaction_data['leaveid'] = $id;
                } else {
                    $historytransaction_data['othertransid'] = $id;
                }

                // print_r($historytransaction_data);
                // print_r($where);

                // Insert/update history transaction record
                $historytransid = TransactionFlowModel::insert_historyTransDetail($historytransaction_data, $where);

                // Check if the history transaction was inserted
                if ($historytransid && $historytransid['status'] == 'inserted') {
                    // Insert or update the transaction detail
                    $transdetailid = TransactionFlowModel::insertupdate_transdet($transactiondel_data, $where);

                    if ($transdetailid && (($transdetailid['status'] == 'updated') || ($transdetailid['status'] == 'inserted'))) {
                        // Update the main transaction table
                        if ($transtypecode == View::shared('LeaveIntransactiontypecode')) {
                            $leavetableUpdateStatus = TransactionFlowModel::leavein_insertupdate($maintableUpdate, $id, 'transaction');
                        }

                        // Check if the main table update was successful
                        if (
                            isset($leavetableUpdateStatus) && $leavetableUpdateStatus['status'] == 'updated' ||
                            isset($othertransUpdateStatus) && $othertransUpdateStatus['status'] == 'updated'
                        ) {
                            DB::commit();  // Commit the transaction if all operations are successful
                            return response()->json([
                                'status' => 'success',
                                'message' => 'Application forwarded successfully',
                            ]);
                        } else {
                            DB::rollBack();  // Rollback the transaction if the update failed
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Failed to update main transaction table.',
                            ]);
                        }
                    } else {
                        DB::rollBack();  // Rollback the transaction if the transaction detail update failed
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Failed to insert/update transaction detail.',
                        ]);
                    }
                } else {
                    DB::rollBack();  // Rollback the transaction if history transaction insertion failed
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to insert history transaction.',
                    ]);
                }
            } elseif (count($forwarddel) == 0) {
                DB::rollBack();  // Rollback the transaction if no forwarding user found
                return response()->json([
                    'status' => 'error',
                    'message' => 'No forwarding user found for the application.',
                ]);
            } else {
                DB::rollBack();  // Rollback the transaction in case of unexpected behavior
                return response()->json([
                    'status' => 'error',
                    'message' => 'Multiple users found for forwarding, check your request.',
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();  // Rollback the transaction on a database-related error
            return response()->json([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();  // Rollback the transaction on any unexpected error
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ]);
        }
    }

    public function leaveindata(Request $request)
    {
        $id = Crypt::decryptString($request->id);

        $leaveindel = TransactionFlowModel::leaveindata($id);

        $leaveinschdel = TransactionFlowModel::leaveinschdel($id);

        // if ($schedulrequestdel) {

        //     $auditscheduleid =  $schedulrequestdel[0]->auditscheduleid;
        //     $othermembers = $this->getothermembers($auditscheduleid);

        //     $headuserid =  $schedulrequestdel[0]->headuserid;
        //     $otherteamhead =  TransactionFlowModel::fetch_otherteamhead($headuserid);
        // }

        return view('transactionflow.leaveindatatransfer', compact('leaveindel', 'leaveinschdel'));
    }

/////leave in end //////////////

////// team head change plan ////

public function futureplanheadchange_compact($view)
    {
        $userdel = session('charge');

        $distcode = $userdel->distcode;
        $deptcode = $userdel->deptcode;
        $regioncode = $userdel->regioncode;

        $userleavedetails = TransactionFlowModel::getauditornamesbasedondist($deptcode, $distcode);
        $reverselistusers = TransactionFlowModel::reverselistusers($deptcode, $regioncode);

        foreach ($userleavedetails as $item) {
            $item->deptuserid = Crypt::encryptString($item->deptuserid);
        }

        foreach ($reverselistusers as $item) {
            $item->deptuserid = Crypt::encryptString($item->deptuserid);
        }
        return view($view, compact('userleavedetails', 'reverselistusers'));
    }

    public function getplandetails(Request $request)
    {
        // try {
        $userid = $request->userid;
        $auditplanid = $request->auditplanid;

        if ($request->userid) {
            $userid = $request->has('userid') ? Crypt::decryptString($request->userid) : null;
        }

        if ($request->auditplanid) {
            $auditplanid = $request->has('auditplanid') ? Crypt::decryptString($request->auditplanid) : null;
            // echo $auditplanid;
            // exit;
        }

        $sessionuserdel = session('user');
        $sessionuserid = $sessionuserdel->userid ?? null;

        if (!$sessionuserid) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }
        $scheduledel = TransactionFlowModel::getplandetails($userid, $auditplanid);
        // print_r($scheduledel);
        // exit;

        if ($scheduledel) {
            foreach ($scheduledel as $all) {
                $all->auditplanid = Crypt::encryptString($all->auditplanid);
            }

            return response()->json(['success' => true, 'data' => $scheduledel]);
        } else {
            return response()->json(['success' => false, 'message' => 'No schedule found.']);
        }
    }

    public function fetch_assignusers(Request $request)
    {
        // try {
        $userid = $request->userid;
        $datatransfertypecode = $request->datatransfertypecode;
        $auditplanid = $request->auditplanid;

        if ($request->userid) {
            $userid = $request->has('userid') ? Crypt::decryptString($request->userid) : null;
        }

        if ($request->auditplanid) {
            $auditplanid = $request->has('auditplanid') ? Crypt::decryptString($request->auditplanid) : null;
        }

        $sessionuserdel = session('user');
        $sessionuserid = $sessionuserdel->userid ?? null;

        if (!$sessionuserid) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        if ($datatransfertypecode == 'AH') {
            $scheduledel = TransactionFlowModel::fetch_otherteamhead($userid);
        } else {
            $scheduledel = TransactionFlowModel::planmemberdetails($auditplanid);
        }

        if ($scheduledel) {
            foreach ($scheduledel as $all) {
                $all->userid = Crypt::encryptString($all->userid);
            }

            return response()->json(['success' => true, 'data' => $scheduledel]);
        } else {
            return response()->json(['success' => false, 'message' => 'No schedule found.']);
        }
    }

    public function futureplanheadtransfer_finalise(Request $request)
    {
        try {
            $userSessionData = session('user');
            $userid = $userSessionData->userid;

            $auditplanid = Crypt::decryptString($request->auditplanid);
            $datatransfercode = $request->datatransfercode;

            if ($datatransfercode == 'CD') {
                $touserid = Crypt::decryptString($request->reserveuserid);
            } else {
                $touserid = Crypt::decryptString($request->touserid);
            }
            $fromuserid = Crypt::decryptString($request->userid);

            $request->merge(['touserid' => $touserid]);
            $request->merge(['fromuserid' => $fromuserid]);
            $request->merge(['auditplanid' => $auditplanid]);

            $request->validate([
                'remarks' => 'required|string|max:500|min:10',
                'touserid' => 'required|integer',
                'fromuserid' => 'required|integer',
                'auditplanid' => 'required|integer',
                'datatransfercode' => 'required|string|max:2',
            ]);

            // Call the model function (make sure it returns decoded JSON array)
            $result = TransactionFlowModel::futureplanheadtransfer_insertupdate(
                $auditplanid, $fromuserid, $touserid, $datatransfercode, $userid, $request->remarks
            );

            // If result is a JSON string, decode it
            if (is_string($result)) {
                $result = json_decode($result, true);
            }

            if (isset($result['status']) && $result['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Plan was transferred successfully.',
                    'data' => $result['data'] ?? null
                ]);
            } elseif (isset($result['status']) && $result['status'] === 'failure') {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Transfer failed.'
                ], 400);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Unexpected error occurred.'
                ], 500);
            }
        } catch (\Exception $e) {
            // Optional: Log error here
            // Log::error('Plan Transfer Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function futureplanheadtransferdel(Request $request)
    {
        $userdel = session('charge');

        $distcode = $userdel->distcode;
        $deptcode = $userdel->deptcode;
        $regioncode = $userdel->regioncode;

        try {
            $futureplanheadtransferdel = TransactionFlowModel::futureplanheadtransferdel($deptcode, $distcode);

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $futureplanheadtransferdel->isEmpty() ? null : $futureplanheadtransferdel
            ], 200);
        } catch (QueryException $e) {
            \Log::error('Database Query Error: ' . $e->getMessage());  // Log the error for debugging

            return response()->json([
                'success' => false,
                'message' => 'There was an issue with the database query. Please try again.'
            ], 400);  // Return a custom error message without the generic server error
        } catch (Exception $e) {
            \Log::error('General Error: ' . $e->getMessage());  // Log the error for debugging

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 400);  // Return a more user-friendly error message
        }
    }

public function get_fromtodeptdet(Request $request)
    {
        try {

            $request->validate([
                'from_deptcode'              =>  ['required', 'string',  'in:01,02,03,04,05'],
                'transactiontype_code'  =>  ['required', 'string', 'regex:/^\d+$/'],
            ], [
                'required'  => 'The :attribute field is required.',
                'regex'     =>  'The :attribute field must be a valid number.',
                'in' => 'Invalid department code selected.'
            ]);

            $fromdeptcode = $request->from_deptcode;
            $transtype    = $request->transactiontype_code;

            $fromtodeptdet = TransactionFlowModel::get_fromtodeptdet($fromdeptcode, $transtype);

            if ($fromtodeptdet) {
                return response()->json(['success' => true, 'data' => $fromtodeptdet]);
            } else {
                return response()->json(['success' => false, 'data' => $fromtodeptdet]);
            }
        } catch (\Exception $e) {


            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }
/////team head plan change end ///////////
 public function get_api_changeuserdel()
    {
        try {

            $response = Transactionflowmodel::get_changeusererp();

            $data = $response->getData()->data ?? [];

            foreach ($data as $row) {

                if (!empty($row->leaveid)) {
                    $row->leaveid = Crypt::encryptString($row->leaveid);
                }

                if (!empty($row->othertransid)) {
                    $row->othertransid = Crypt::encryptString($row->othertransid);
                }
            }

            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Controller error',
                'error'   => $e->getMessage(),
                'line'    => $e->getLine()
            ], 500);
        }
    }

}
