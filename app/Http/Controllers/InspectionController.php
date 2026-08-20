<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\MastersModel;
use Illuminate\Http\Request;
use App\Models\InspectionModel;
use App\Models\FieldAuditModel;

use App\Http\Requests\MasterdesignationRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\BaseModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class InspectionController extends Controller
{

    public  function inspectview_dropdown()
    {

        try {
            $dept     = InspectionModel::getDept();
            $region   = InspectionModel::getRegion();
            $district   = InspectionModel::getDistrict();
	   $isteammember = InspectionModel::teammember_check();
            //      throw new \Exception("Audit schedule ID not found");
            return view('inspection.inspectionview', compact('dept', 'region', 'district', 'isteammember'));

        } catch (\Exception $e) {
            return view('inspection.inspectionview', [
                'dept' => $dept ?? null,
                'region' => $region ?? null,
                'district' => $district ?? null,
                'errorMessage' => $e->getMessage(),
                'pageName' => 'inspectview',
            ]);
        }
    }

    public  function fetch_instDetails(Request $request)
    {
        try {
            $deptcode = $request->deptcode ?? null;
            $regioncode = $request->regioncode ?? null;
            $distcode = $request->distcode ?? null;

            $data = [
                'deptcode' => $deptcode,
                'regioncode' => $regioncode,
                'distcode' => $distcode,
            ];
            $session = session('charge');
            $teamHead =  $session->auditteamhead;
            $instdet  =    InspectionModel::getInstdetails($data,  $teamHead);
            if ($instdet->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Institution Details not found',
                    'data' => null
                ], 404);
            }
            foreach ($instdet as $item) {
                $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);


            }
            foreach ($instdet as $item) {
                $item->encrypted_auditinspectionid = Crypt::encryptString($item->inspectionid);


                unset($item->inspectionid);
            }
            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $instdet
            ], 200);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid  ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching user datails'
            ], 500);
        }
    }

    function fetch_deptbaseddata(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode'      => ['nullable', 'string', 'regex:/^\d+$/'],
            'regioncode'    => ['nullable', 'string', 'regex:/^\d+$/'],
            'distcode'      => ['nullable', 'string', 'regex:/^\d+$/'],
            'valuefor'      => ['required', 'string', 'in:region,district'], // Include "region"
            'formname'      => ['required', 'string', 'in:checkschedulestatus,district'],

        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
            'in'       => 'The :attribute field must be one of: region, district, institution.',
        ]);

        // Extract validated data
        $deptcode = $validatedData['deptcode'];
        $regioncode = $validatedData['regioncode'] ?? null;
        $distcode = $validatedData['distcode'] ?? null;
        $valuefor = $validatedData['valuefor'];
        $formname = $validatedData['formname'];

        // if (($valuefor === 'region' && !$deptcode)) {
        //     return response()->json(['success' => false, 'message' => 'Department code is required for Region.'], 422);
        // }

        // Additional validation for 'district'
        // if (($valuefor === 'district' && !$regioncode)) {
        //     return response()->json(['success' => false, 'message' => 'Region code is required for district.'], 422);
        // }
        if ($valuefor === 'district' && !$deptcode) {
            return response()->json(['success' => false, 'message' => 'Department code is required for district.'], 422);
        }

        // Additional validation for 'institution'

        try {

            $getdata =  InspectionModel::fetch_deptbaseddata(
                $deptcode,
                $regioncode,
                $distcode,
                $valuefor,
                $formname
            );
            // return $getdata;
            if ($getdata) {
		foreach ($getdata['details'] as $item) {
                    $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);


                }
                foreach ($getdata['details'] as $item) {
                    $item->encrypted_auditinspectionid = Crypt::encryptString($item->inspectionid);


                    unset($item->inspectionid);
                }
                return response()->json(['success' => true, 'data' => $getdata['data'], 'details' => $getdata['details']]);
            }

            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function inspectionquery_dropdown($form, $encrypted_auditscheduleid, $encrypted_auditinspectionid)
    {
        try {

            list(, $encrypted_auditscheduleid) = explode('=', $encrypted_auditscheduleid);

            list(, $encrypted_auditinspectionid) = explode('=', $encrypted_auditinspectionid);

            list(, $formstatus) = explode('=', $form);

            // Decrypt the encrypted audit schedule ID
            if ($encrypted_auditscheduleid) {
                $auditscheduleid = Crypt::decryptString($encrypted_auditscheduleid);
            }
            if ($encrypted_auditinspectionid) {
                $auditinspectionid = Crypt::decryptString($encrypted_auditinspectionid);
            }
            //  return $auditscheduleid;


            if ($auditscheduleid === null) {
                throw new \Exception("Audit schedule ID not found");
            }
            $sessioncharge     = session('charge');
            $sessionuser    = session('user');
            if (empty($sessionuser)) {
                throw new \Exception("No session details");
            } else {
                $sessionuserid = $sessionuser->userid;
                $formsessionuserid = Crypt::encryptString($sessionuserid);
            }
            $sessiondesigcode  = $sessioncharge->desigcode;
            $isteamhead      = InspectionModel::checkisteamhead($auditscheduleid);
            if ($isteamhead) {
                $teamheadFlag = 'Y';
            } else {
                $teamheadFlag = '';
            }

            $scheduledel       = InspectionModel::getscheduledetails($auditscheduleid);

            // print_r($scheduledel);
            // exit;
            if ($scheduledel->isEmpty()) {
                throw new \Exception("Schedule Details not Found");
            }

            $data = InspectionModel::getchecklistdetails($auditscheduleid, $sessiondesigcode, $teamheadFlag, $auditinspectionid);
            // print_r($scheduledel);
            // exit;
            if ($data->isEmpty()) {
                throw new \Exception("Check List Details not Found");
            }
           foreach ($scheduledel as $item) {
                $item->encrypted_planid = Crypt::encryptString($item->auditplanid);
                $item->encrypted_instid = Crypt::encryptString($item->instid);
                $item->encrypted_teamhead_userid = Crypt::encryptString($item->teamhead_userid);
                $item->encrypted_team_members_userids = Crypt::encryptString($item->team_members_userids);
                $item->encrypted_schteammemberid = Crypt::encryptString($item->schteammemberid);
                unset($item->auditplanid);
            }

            return view('inspection.inspectionquery', compact('formsessionuserid', 'scheduledel', 'encrypted_auditscheduleid', 'data', 'encrypted_auditinspectionid', 'formstatus'));
        } catch (\Exception $e) {
            Log::error("Error in Inspection: " . $e->getMessage());
            echo $e->getMessage();
            return redirect()->back()->with([
                'errorMessage' => $e->getMessage(),
                'pageName' => 'inspectionquery'
            ]);
            //return redirect()->route('error')->with('error', 'An error occurred while processing the auditslip. Please try again later.');
        }
    }

    public function getpendingparadetails(Request $request)
    {
        // return $request;
        $validatedData = $request->validate([
            'auditscheduleid'  => ['required', 'string'],
            'auditinspectionid'  => ['nullable', 'string'],
            'slipsts'          => ['nullable', 'string',],
            'filterapply'      => ['nullable', 'string'],

        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
        ]);

        if ($request->auditscheduleid) {
            $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
        }
        if ($request->auditinspectionid) {
            $auditinspectionid = Crypt::decryptString($request->auditinspectionid);
        }
        if ($auditscheduleid === null) {
            throw new \Exception("Audit schedule ID not found");
        }
        $auditscheduleid = $auditscheduleid;
        $slipsts = $validatedData['slipsts'] ?? null;
        $filterapply = $validatedData['filterapply'] ?? null;

        $session = session('charge');


        $isteamhead      = InspectionModel::checkisteamhead($auditscheduleid);
        if ($isteamhead) {
            $teamHead = 'Y';
        } else {
            $teamHead = '';
        }


        try {

            $getdata =  InspectionModel::getpendingparadetails(
                $auditscheduleid,
                $slipsts,
                $filterapply,
            );


            $inspectdetails = InspectionModel::getInspectiondetbyId($auditscheduleid, $auditinspectionid, $teamHead);

            if (!empty($inspectdetails['data'])) {
                foreach ($inspectdetails['data'] as $item) {
                    $item->encrypted_auditinspectionid = Crypt::encryptString($item->auditinspectionid);
                    unset($item->auditinspectionid);
                }
            }


            if ($getdata) {
                return response()->json(['success' => true, 'data' => $getdata, 'inspectdata' => $inspectdetails['data'] ?? [], 'historydata' => $inspectdetails['historydata'] ?? []]);
            }

            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getSlipDetailsHistory(Request $request)
    {
        $slipId = $request->slipid;
        // $slipId = Crypt::decryptString($slipId);
        return InspectionModel::getSlipDetailsHistory($slipId);
    }


    public function getsliphistorydetails(Request $request)
    {
        $validatedData = $request->validate([
            'slipid'      => ['required', 'string'],


        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
        ]);
        $slipid = $request->slipid;
        // if ($request->slipid) {
        //     $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
        // }
        if ($request->slipid === null) {
            throw new \Exception("Slip ID not found");
        }
        try {
            $getdata =  InspectionModel::getsliphistorydetails($slipid);
            if ($getdata) {
                return response()->json(['success' => true, 'data' => $getdata]);
            }

            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    // public static function getchecklistdetails(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'auditscheduleid'      => ['required', 'string'],

    //     ], [
    //         'required' => 'The :attribute field is required.',
    //         'regex'    => 'The :attribute field must be a valid number.',
    //     ]);

    //     $auditscheduleid = $validatedData['auditscheduleid'];
    //     if (!$auditscheduleid) {
    //         return response()->json(['success' => false, 'message' => 'Audit Schedulid was not found'], 422);
    //     }
    //     if ($auditscheduleid) {
    //         $auditscheduleid = Crypt::decryptString($auditscheduleid);
    //     }
    //     try {
    //         $getdata = InspectionModel::getchecklistdetails($auditscheduleid);
    //         if ($getdata) {
    //             return response()->json(['success' => true, 'data' => $getdata]);
    //         }

    //         return response()->json(['success' => false, 'message' => 'Data not found'], 404);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    //     }
    // }

    public  function inspectchecklist_insert(Request $request)
    {
        $action = $request->input('action');

        $session = session('user');
        $sessioncharge = session('charge');
        $sessionuserid = $session->userid;
 	$session_userid = $session->userid ?? null;

 	if (!$session_userid) {
                return response()->json(['error' => 'User session is invalid.'], 400);
            }

            $formsessionuserid = Crypt::decryptString($request->formuserid);
           
            if($session_userid != $formsessionuserid )
            return response()->json(['message' => 'Please refresh the page maintain one login at a time', 'error' => 402], 402);

        $teamHead      = $request->auditteamhead ?? NULL;

        $allInputs = $request->all();

        $validatedData = $request->validate([
            'auditscheduleid'      => ['required', 'string', 'regex:/^[A-Za-z0-9\/+=]+$/'],
            'auditplanid'          => ['required', 'string', 'regex:/^[A-Za-z0-9\/+=]+$/'],
            'finaliseflag'         => ['required', 'string', 'regex:/^[A-Za-z0-9\/+=]{1}$/'],
            // 'checkpoints'          => ['exclude_if:teamheadFlag,Y', 'required', 'array', 'min:1',],
            'teamheadFlag'         => ['nullable', 'string',  'min:1', 'max:1', 'regex:/^[A-Za-z0-9\/+=]$/'],
            'forwaredby'          => ['nullable', 'string',  'regex:/^[A-Za-z0-9\/+=]+$/'],
            'actionfor'          => ['nullable', 'string',   'in:rejoinder,fresh,complete'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
            'checkpoints.required' => 'Please select at least one checkpoint.',
            'checkpoints.array'    => 'Invalid checkpoints format.',
            'checkpoints.min'      => 'Please select at least one checkpoint.',
        ]);

        //return $request->forwardedby;

        try {
            //$inspectcheckpoints = json_encode($request->input('checkpoints'));
            $auditscheduleid    = Crypt::decryptString($request->auditscheduleid);
            $auditplanid        = Crypt::decryptString($request->auditplanid);
            $finaliseflag       = $request->finaliseflag;
            $teamheadFlag       = $request->teamheadFlag;
            $actionfor          = $request->actionfor;
            $rejoindercycle     = $request->rejoindercycle;


            $slipcontent    = json_encode(['content' => $request->input('slipremarks')]);

            $data = [
                'auditscheduleid'      => $auditscheduleid,
                'auditplanid'          => $auditplanid,
                'auditteamhead'         => $teamHead,

                'slipremarks'          => $slipcontent,
                //  'inspectcheckpoints'   => $inspectcheckpoints,
                'statusflag'           => $finaliseflag,

            ];
            $actionData = [];
            $remarksData = [];
            $headRemarksData = [];

            if ($teamheadFlag == 'Y') {

                foreach ($allInputs  as $key => $value) {


                    if (Str::startsWith($key, 'teamhead_remarks_')) {
                        $id = str_replace('teamhead_remarks_', '', $key);
                        $headRemarksData[$id] = $value;
                    }
                }

                $jsonHeadRemarks = json_encode($headRemarksData);
                $data['inscheckpointheadremarks'] = $jsonHeadRemarks;
            } else {
                $content       = json_encode(['content' => $request->input('remarks')]);
                $data['remarks'] = $content;
            }

            foreach ($allInputs  as $key => $value) {

                if (Str::startsWith($key, 'action_')) {
                    $id = str_replace('action_', '', $key);
                    $actionData[$id] = $value;
                }
                if (Str::startsWith($key, 'remarks_')) {
                    $id = str_replace('remarks_', '', $key);
                    $remarksData[$id] = $value;
                }
            }

            // Convert to JSON

            $jsonActions = json_encode($actionData);

            $jsonRemarks = json_encode($remarksData);

            $data['inspectcheckpoints'] = $jsonActions;

            $data['inscheckpointremarks'] = $jsonRemarks;
            //  $data['inscheckpointremarks'] = $jsonRemarks;
            //       }
            if ($action == 'insert' && $request->input('teamheadFlag') != 'Y') {
                $data['createdby'] = $sessionuserid;
                $data['createdon'] = View::shared('get_nowtime');
                // $getmaxtranno = InspectionModel::getmaxtranactionno();
                // $data['transactionno'] = $getmaxtranno;
                $data['updatedby'] = $sessionuserid;
                $data['updatedon'] = View::shared('get_nowtime');
            } else if ($action == 'insert' && $request->input('teamheadFlag') == 'Y') {
                $data['updatedby'] = $sessionuserid;
                $data['updatedon'] = View::shared('get_nowtime');
            }

            $processcode = '';
            $activeinspection = '';
            if ($finaliseflag == 'F') {

                $processcode = Str::is('Y', $teamheadFlag) ? 'H' : 'T';

                if ($teamheadFlag != 'Y') {
                    $forwardtofetch = InspectionModel::getforwardtoDetails($auditscheduleid, $teamHead);


                    if (empty($forwardtofetch)) {

                        if ($teamHead == 'Y') {
                            throw new \Exception("Unable to Forward.Please check the User to be forwarded");
                        } else {
                            throw new \Exception("Team head not found to forward the details");
                        }
                    }

                    $forwardto = $forwardtofetch->deptuserid;

                    if ($actionfor == 'rejoinder') {
                        $data['rejoinderstatus'] =  'Y';
                        $data['activeinspection'] = 'A';
                        // $rejoinderstatus    =   'Y';
                        if ($rejoindercycle ==  '') $rejoindercycle = 0;
                        $rejoindercycle =   $rejoindercycle +   1;
                        $data['rejoindercycle'] =  $rejoindercycle;
                    } else if ($actionfor == 'complete') {
                        $processcode    =    'C';
                        $data['statusflag'] = 'C';
                        $data['activeinspection'] = 'I';
                        $forwardto = null;
                        $data['initiatedbydesigcode'] = $sessioncharge->desigcode;
                    } else {
                        $data['activeinspection'] = 'A';
                        $processcode                  =    'T';
                        $data['initiatedbydesigcode'] = $sessioncharge->desigcode;
                    }
                } else {
                    $forwardto = $request->forwardedby;
                    $processcode    =    'H';
                    $data['activeinspection'] = 'A';
                }

                $forwardedby = $sessionuserid;
                //   return;
                $data['forwardedto'] = $forwardto;
                $data['forwardedby'] =  $forwardedby;
            } else {
                $data['activeinspection'] = 'A';
                if ($teamheadFlag != 'Y') {
                    $data['initiatedbydesigcode'] = $sessioncharge->desigcode;
                    $processcode = 'E';
                } else {
                    $processcode = 'E';
                }
            }

            if ($request->action == 'update') {

                $data['updatedby'] = $sessionuserid;
                $data['updatedon'] = View::shared('get_nowtime');
            }
            $data['processcode'] = $processcode;

            // $auditinspectionid =
            //     ($request->input('action') === 'update' || $request->input('teamheadFlag') === 'Y')
            //     ? Crypt::decryptString($request->input('auditinspectionid'))
            //     : null;


            $auditinspectionid = !empty($request->input('auditinspectionid'))
                ? Crypt::decryptString($request->input('auditinspectionid'))
                : '';

            //  return $data;
            $newAuditinspectiondetail = InspectionModel::inspectchecklist_insert($data, $auditinspectionid);
            //   return $newAuditinspectiondetail;
            $newAuditinspectionid =   $newAuditinspectiondetail->auditinspectionid;
            $transactionno =   $newAuditinspectiondetail->transactionno;
            if ($teamHead != 'Y') {
                $remarksfromnewdata = $newAuditinspectiondetail->remarks;
            } else {
                $remarksfromnewdata = json_encode('');
            }
            //  $remarksfromnewdata = $newAuditinspectiondetail->remarks;
            //            return $data;

            if ($finaliseflag == 'F') {
                $historydata = [
                    'auditinspectionid'    => $newAuditinspectionid,
                    'auditscheduleid'      => $auditscheduleid,
                    'auditplanid'          => $auditplanid,
                    'auditteamhead'         => $teamHead,
                    'remarks'              => $remarksfromnewdata,
                    'slipremarks'          => $slipcontent,
                    'inspectcheckpoints'   => $jsonActions,
                    'statusflag'           => $finaliseflag,
                    'forwardedto'          => $forwardto,
                    'forwardedby'          => $forwardedby,
                    'transstatus'          => 'A',
                    'forwardedon'          => View::shared('get_nowtime'),
                    'inscheckpointremarks' => $jsonRemarks ?? null,
                    'inscheckpointheadremarks' => $jsonHeadRemarks ?? null,
                    'processcode'          => $processcode,
                    'updatedby'            => $sessionuserid,
                    'updatedon'           => View::shared('get_nowtime'),
                    'transactionno'       =>  $transactionno

                ];



                $historyinsert = InspectionModel::insert_inspecthistorydetails($historydata, $newAuditinspectionid);
                if ($historyinsert) {
                    switch ($processcode) {
                        case 'T':
                            $successmessage = 'Audit Inspection details have been sent to Team Head successfully';
                            break;
                        case 'H':
                            $successmessage = 'Audit Inspection details have been forwarded successfully';
                            break;
                        case 'C':
                            $successmessage = 'Audit Inspection has been completed successfully';
                            break;
                        default:
                            $successmessage = 'Data has been saved as a draft';
                            break;
                    }
                    return response()->json([
                        'success' => true,
                        'message' => $successmessage,
                        'data' => [
                            'auditinspectionid' => Crypt::encryptString($newAuditinspectionid)
                        ]
                    ]);
                } else {
                    return response()->json([
                        'error' => 409,
                        'message' => 'Failed to Forward',

                    ], 409);
                }
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Data has been saved as a draft successfully',
                    'data' => [
                        'auditinspectionid' => Crypt::encryptString($newAuditinspectionid)
                    ]
                ]);
            }

            // return $newAuditinspectionid;


            // return $data;
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public static function getinspectionDetails(Request $request)
    {
        $validatedData = $request->validate([
            'auditscheduleid'      => ['required', 'string', 'regex:/^[A-Za-z0-9\/+=]+$/'],

        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
        ]);

        $auditscheduleid = $validatedData['auditscheduleid'];
        if (!$auditscheduleid) {
            return response()->json(['success' => false, 'message' => 'Audit Schedule details not found'], 422);
        }
        if ($auditscheduleid) {
            $auditscheduleid = Crypt::decryptString($auditscheduleid);
        }

        $session = session('user');
        $sessioncharge = session('charge');
        $sessionuserid = $session->userid;

        $isteamhead      = InspectionModel::checkisteamhead($auditscheduleid);
        if ($isteamhead) {
            $teamHead = 'Y';
        } else {
            $teamHead = '';
        }


        try {
            $getdata = InspectionModel::getinspectionDetails($auditscheduleid, $teamHead, $sessionuserid);

            if ($getdata) {
                foreach ($getdata['data'] as $item) {
                    $item->encrypted_auditinspectionid = Crypt::encryptString($item->auditinspectionid);
                    unset($item->auditinspectionid);
                }
                return response()->json(['success' => true, 'data' => $getdata['data'], 'historydata' => $getdata['historydata']]);
            }

            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function insertslipremarks(Request $request)
    {
        // return $request;
        $session = session('user');
        $sessioncharge = session('charge');
        $sessionuserid = $session->userid;



       // $formsessionid = Crypt::decryptString($request->formuserid);

       $session_userid = $session->userid ?? null;
            if (!$session_userid) {
                return response()->json(['error' => 'User session is invalid.'], 400);
            }

            $formsessionuserid = Crypt::decryptString($request->formuserid);
           
            if($session_userid != $formsessionuserid )
            return response()->json(['message' => 'Please refresh the page maintain one login at a time', 'error' => 402], 402);


        $validatedData = $request->validate([
            'slip_auditscheduleid'      => ['required', 'string', 'regex:/^[A-Za-z0-9\/+=]+$/'],

        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
        ]);


        try {
            $auditscheduleid    = Crypt::decryptString($request->slip_auditscheduleid);
            $auditplanid        = Crypt::decryptString($request->slip_auditplanid);
            $isteamhead      = InspectionModel::checkisteamhead($auditscheduleid);
            if ($isteamhead) {
                $teamHead = 'Y';
            } else {
                $teamHead = '';
            }
            $content = json_encode(['content' => $request->input('slipremarks')]);

            $data = [
                'auditscheduleid'      => $auditscheduleid,
                'auditplanid'          => $auditplanid,
                'slipremarks'          => $content,
                'activeinspection'     => 'A',
                // 'initiatedbydesigcode' => $sessioncharge->desigcode,

                //  'inspectcheckpoints'   => $inspectcheckpoints,
                //'statusflag'           => 'Y',

            ];
            if ($teamHead != 'Y') {
                $data['initiatedbydesigcode'] = $sessioncharge->desigcode;
            }
            $auditinspectionid = !empty($request->input('slip_auditinspectionid'))
                ? Crypt::decryptString($request->input('slip_auditinspectionid'))
                : null;
            if (empty($request->input('slip_auditinspectionid'))) {
                $data['processcode'] = 'O';
            }


            if ($auditinspectionid) {
                $data['updatedby'] = $sessionuserid;
                $data['updatedon'] = View::shared('get_nowtime');
            } else {
                $data['initiatedbydesigcode'] = $sessioncharge->desigcode;
                $data['createdby'] = $sessionuserid;
                $data['createdon'] = View::shared('get_nowtime');
                $getmaxtranno = InspectionModel::getmaxtranactionno();
                $data['transactionno'] = $getmaxtranno;
            }

            $newAuditinspectiondetail = InspectionModel::slipremarks_insert($data, $auditinspectionid, $auditscheduleid);
            //  return $newAuditinspectiondetail;
            $newAuditinspectionid =   $newAuditinspectiondetail->auditinspectionid;
            $transactionno =   $newAuditinspectiondetail->transactionno;

            if ($newAuditinspectiondetail) {
                return response()->json([
                    'success' => true,
                    'message' => 'Remarks on slip has been saved successfully',
                    'data' => [
                        'auditinspectionid' => Crypt::encryptString($newAuditinspectionid)
                    ]
                ]);
            } else {
                return response()->json([
                    'error' => 409,
                    'message' => 'Failed to Forward',

                ], 409);
            }
            // return $newAuditinspectionid;
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }
}
