<?php

namespace App\Http\Controllers;

use App\Models\RegionModel;
use App\Models\DistrictModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\WorkAllocationModel;


class WorkAllocationController extends Controller
{
    public function showForm(Request $request)
    {
        // Fetch regions and districts
        $regions = RegionModel::where('statusflag', 'Y')->orderBy('regionename', 'asc')->get();
        $districts = DistrictModel::where('statusflag', 'Y')->orderBy('distename', 'asc')->get();

        // Retrieve session or request values
        $regioncode = $request->input('regioncode') ?? session('regioncode');
        $distcode = $request->input('distcode') ?? session('distcode');

        return view('yourviewname', compact('regions', 'districts', 'regioncode', 'distcode'));
    }
        public function fetchinstdet()
    {
        $session = session('charge');
        $usersession = session('user');
        $session_dept = $session->deptcode;
        $data = [
            'distcode'  => $session->distcode,
            'deptcode'  => $session->deptcode,
            'userid'    => $usersession->userid
        ];

        $instdet = WorkAllocationModel::fetchinstdet($data);
        foreach ($instdet as $all) {
            $all->encrypted_instid = Crypt::encryptString($all->instid);
            $all->encrypt_auditscheduleid = Crypt::encryptString($all->auditscheduleid);
            // dd($majorworkallocationtypeid);

            unset($all->instid);
            unset($all->auditscheduleid);
        }
        return view('fieldaudit.workallocationlist', compact('instdet'));
    }

    public function fetchworkdata(Request $request)
    {
        try {
            $usersession = session('user');

            if (empty($request->instid) && empty($request->scheduleid)) {
                return response()->json([
                    'status' => 'success',
                    'data' => 0
                ]);
            }
            $instid = Crypt::decryptString($request->instid);
            $scheduleid = Crypt::decryptString($request->scheduleid);




            $data = [
                'scheduleid' => $scheduleid,
                'instid'     => $instid,
                'userid'     => $usersession->userid
            ];
            // return $data;
            $workdata = WorkAllocationModel::fetchworkdata($data);
            if (isset($workdata['error'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => $workdata['error']
                ], 400); // or other appropriate status
            }

            return response()->json([
                'status' => 'success',
                'data' => $workdata
            ]);


            // return $workdata;
            //return $instid;
        } catch (\Exception $e) {
            // Log::error('Automation Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error Occured'
            ], 500);
        }
    }


public function fetchworkdataforinspection(Request $request)
    {
        try {
            if (empty($request->instid) || empty($request->scheduleid)) {
                return response()->json([
                    'status' => 'success',
                    'data' => 0
                ]);
            }

            $instid = Crypt::decryptString($request->instid);
            $scheduleid = Crypt::decryptString($request->scheduleid);
            $teamHeadUserId = Crypt::decryptString($request->teamhead_userid);
            $teamMembersUserIds = Crypt::decryptString($request->team_members_userids);

            $membersArray = array_filter(array_map('trim', explode(',', $teamMembersUserIds)));
            $allUserIds = array_merge([$teamHeadUserId], $membersArray);

            $finalOutput = [];

            foreach ($allUserIds as $userid) {
                $data = [
                    'scheduleid' => $scheduleid,
                    'instid'     => $instid,
                    'userid'     => $userid
                ];

                $workdata = WorkAllocationModel::fetchworkdata($data);


                $finalOutput[] = [
                    'userid' => $userid,
                    'isteamhead' => ($userid == $teamHeadUserId) ? 'Y' : 'N',
                    'work'   => $workdata
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => $finalOutput
            ]);

        } catch (\Exception $e) {
            \Log::error('Fetch work data error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error Occured'
            ], 500);
        }
    }

    public function automateWorkAllocation(Request $request)
    {
        try {
            // Validate the input
          
           $auditscheduleid= Crypt::decryptString($request->auditscheduleid);
            // Call the model function
            $result = WorkAllocationModel::Automateworkallocation($auditscheduleid);

            $jsonString = $result[0]->response;

            // Step 2: Decode the JSON string into an array
            $data = json_decode($jsonString, true);

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
    public function fetch_allocatedwork(Request $request)
    {
        try {
            $rules = [
                'scheduleid' => 'required|string|',
            ];


            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $auditscheduleid = Crypt::decryptString($request->scheduleid);
            // return $auditscheduleid;
            // $auditscheduleid = $request->scheduleid;
            $allocatedwor_det = WorkAllocationModel::fetch_allocatedwork($auditscheduleid);

            return response()->json(['success' => 'data was fetched successfully', 'data' => $allocatedwor_det]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }
    }
