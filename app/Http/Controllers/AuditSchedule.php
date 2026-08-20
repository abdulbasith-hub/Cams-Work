<?php

namespace App\Http\Controllers;

use App\Models\AssignCharge;
use App\Models\AuditManagementModel;
use App\Models\AuditMemberModel;
use App\Models\AuditModel;
use App\Models\AuditPlanModel;
use App\Models\Charge;
use App\Models\DeptModel;
use App\Models\DesignationModel;
use App\Models\DistrictModel;
use App\Models\InstAuditscheduleModel;
use App\Models\InstSchteamMemberModel;
use App\Models\MajorWorkAllocationtypeModel;
use App\Models\TransWorkAllocationModel;
use App\Models\UserChargeDetailsModel;
use App\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
// use App\Models\AccountParticularsModel;
use App\Models\SmsmailModel;
use App\Models\WorkAllocationModel;
use App\Services\PHPMailerService;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;
use DataTables;

class AuditSchedule extends Controller
{
    public function storeOrUpdate(Request $request, $userId = null)
    {
        // Log::info($request->all());

        // dd($request->all());
        // Validation for user input
        $chargedel = session('charge');
        $deptcode = $chargedel->deptcode;
        $data = $request->all();

        // // Add new fields to the data array
        // $data['created_at'] = now();  // Set the current timestamp for created_at
        // $data['updated_at'] = now();  // Set the current timestamp for updated_at

        if ($request->action == 'update') {
            $as_code = Crypt::decryptString($request->as_code);
            $request->merge(['as_code' => $as_code]);
        }

        $from_date = Carbon::createFromFormat('d/m/Y', $request->input('from_date'))->format('Y-m-d');
        $to_date = Carbon::createFromFormat('d/m/Y', $request->input('to_date'))->format('Y-m-d');
        $request->merge(['from_date' => $from_date]);
        $request->merge(['to_date' => $to_date]);

        // Manually inject the formatted date back into the request so that it gets validated properly
        // $request->merge(['from_date' => $from_date]);
        // $request->merge(['to_date' => $to_date]);

        $tm_uid = $request->input('tm_uid');

        $json_tm_uid = json_encode($tm_uid);

        $request->merge(['tm_uid' => $json_tm_uid]);

        $request->validate([
            'ap_code' => 'required',  // Ensures only digits, allows leading zeros
            'from_date' => 'required|date|date_format:Y-m-d|',  // Only alphabets (no numbers or symbols)
            'to_date' => 'required|date|date_format:Y-m-d|',  // Alphanumeric (letters and numbers)
            'rc_no' => 'required',
            'tm_uid' => 'required|json',  // date_format:Y-m-d //'after:today' // after:start_date'
            'th_uid' => 'required',  // Valid email format
        ], [
            'required' => 'The :attribute field is required.',
            'alpha' => 'The :attribute field must contain only letters.',
            'integer' => 'The :attribute field must be a valid number.',
            'regex' => 'The :attribute field must be a valid number.',
            'alpha_num' => 'The :attribute field must contain only letters and numbers.',
            'email' => 'The :attribute field must be a valid email address.',
            'date' => 'The :attribute field must be a valid date.',
            'max' => 'The :attribute field must not exceed :max characters.',
            'before_or_equal' => 'The :attribute field must be before or equal to today.',
            'after_or_equal' => 'The :attribute field must be on or after :date.',
            'dob.before' => 'The date of birth (DOB) must be before 18 years ago.',
            'dob.after_or_equal' => 'The date of birth (DOB) must be today or in the future.',
            'doj.after:dob' => 'The date of joining must be greater than Date of birth.',
            'dor.after:doj' => 'The date of reliveing must be greater than Date of birth.',
            'dor.after:dob' => 'The date of reliveing  must be greater than date of joining.',
        ]);

        $data = [
            'auditplanid' => $request->input('ap_code'),
            'fromdate' => $request->input('from_date'),
            'todate' => $request->input('to_date'),
            'rcno' => $request->input('rc_no'),
            'statusflag' => $request->input('finaliseflag'),
            'createdon' => now(),  // Current timestamp for created_at
            'updatedon' => now(),  // Current timestamp for updated_at
            'workallocationflag' => 'N'
        ];

        if ($request->action == 'update') {
            $audit_scheduleid = $request->input('as_code');
        } else
            $audit_scheduleid = null;

        // try {
        //     // Pass the current user ID (if available) for the update or create logic
        //     $user = UserModel::createIfNotExistsOrUpdate($data, $userId);

        //     if (!$user) {
        //         // If user already exists (based on conditions), return an error
        //         return response()->json(['error' => 'A user with the same email, phone, name, and address already exists.'], 400);
        //     }

        //     // Return success message
        //     return response()->json(['success' => 'User created/updated successfully', 'user' => $user]);
        // } catch (QueryException $e) {
        //     // Handle database exceptions (e.g., duplicate entry)
        //     return response()->json(['error' => 'Database error occurred: ' . $e->getMessage()], 500);
        // } catch (Exception $e) {
        //     // Handle other exceptions
        //     return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        // }
        try {
            $query = InstAuditscheduleModel::where('auditplanid', $request->input('ap_code'));

            // Add a condition for 'update' action
            if ($request->action === 'update') {
                $query->where('auditscheduleid', '!=', $audit_scheduleid);
            }

            $Exists_Instauditschedule = $query->first();

            if ($Exists_Instauditschedule) {
                return response()->json([
                    'error' => 'Already Audit Scheduled for this Institution.',
                ], 400);
            }

            // Call the model method for create or update
            $teamMemberIds11 = json_decode($request->input('tm_uid'), true);
            if (!is_array($teamMemberIds11)) {
                return response()->json(['error' => 'Invalid JSON format for team members.'], 400);
            }
            // $request->merge(['auditscheduleid' =>  $new_auditschedule_id]);
            // Insert each team member using the TeamMember model

            $teamMemberIds1 = $teamMemberIds11;
            // $userIds = array_merge([$request->input('th_uid')], $teamMemberIds1);
            $userIds = $teamMemberIds1;
            $conflictFound = false;

            // Check if any of the users already exist with the same from and to dates
            foreach ($userIds as $userId) {
                // Build the query to check for overlapping or in-between dates
                $query = InstSchteamMemberModel::where('userid', $userId)
                    ->where(function ($query) use ($request) {
                        $fromDate = $request->input('from_date');
                        $toDate = $request->input('to_date');

                        $query
                            ->whereBetween('auditfromdate', [$fromDate, $toDate])  // Check if auditfromdate overlaps
                            ->orWhereBetween('audittodate', [$fromDate, $toDate])  // Check if audittodate overlaps
                            ->orWhere(function ($query) use ($fromDate, $toDate) {
                                $query
                                    ->where('auditfromdate', '<=', $fromDate)  // Existing range fully encapsulates new range
                                    ->where('audittodate', '>=', $toDate);
                            });
                    });

                // Add a condition for 'update' action
                if ($request->action === 'update') {
                    $query->where('auditscheduleid', '!=', $audit_scheduleid);
                }

                $existing = $query->first();

                if ($existing) {
                    $conflictFound = true;
                    break;
                }
            }

            if ($conflictFound) {
                return response()->json([
                    'error' => 'audit is already scheduled for the specified users within the selected date range.',
                ], 400);
            }

            $beforeinsertflag = $request->input('beforeinsert');

            if ($beforeinsertflag == 'finalise_beforeinsert') {
                return response()->json([
                    'finalise_beforeinsert' => 'success'
                ]);
            }

            // Call the model method for create or update
            $audit_schedule = InstAuditscheduleModel::createIfNotExistsOrUpdate($data, $audit_scheduleid);

            if ($audit_schedule) {
                if ($request->action == 'update') {
                    $membersexist = InstSchteamMemberModel::fetchteamMembers($audit_scheduleid);
                    if ($membersexist->isNotEmpty()) {
                        // Extract the array of member IDs from the existing records
                        $existingMemberIds = $membersexist
                            ->filter(function ($member) {
                                return $member->auditteamhead === 'N' && $member->statusflag === 'Y';  // Apply both conditions
                            })
                            ->pluck('userid')
                            ->toArray();

                        // Assuming $newMemberIds is the array of new members you want to compare
                        $newMemberIds = is_string($tm_uid) ? json_decode($tm_uid, true) : $tm_uid;

                        // Find the difference between the existing members and new members
                        $membersToRemove = array_diff($existingMemberIds, $newMemberIds);
                        $membersToAdd = array_diff($newMemberIds, $existingMemberIds);

                        // Optionally, you can perform actions with $membersToRemove and $membersToAdd
                        // For example, delete members to remove
                        if (!empty($membersToRemove)) {
                            foreach ($membersToRemove as $memberId) {
                                InstSchteamMemberModel::whereIn('userid', $membersToRemove)
                                    ->where('auditscheduleid', $audit_scheduleid)
                                    ->where('userid', $memberId)
                                    ->where('auditteamhead', 'N')
                                    ->update(['statusflag' => 'N']);
                            }
                        }
                        if (!empty($membersToAdd)) {
                            foreach ($membersToAdd as $memberId) {
                                InstSchteamMemberModel::create([
                                    'auditscheduleid' => $audit_scheduleid,
                                    'userid' => $memberId,
                                    'auditteamhead' => 'N',
                                    'auditfromdate' => $request->input('from_date'),
                                    'audittodate' => $request->input('to_date'),
                                    'statusflag' => 'Y',
                                    'createdon' => now(),
                                    'updatedon' => now(),
                                    // other fields as necessary
                                ]);
                            }
                        }
                        if (empty($membersToAdd) && empty($membersToRemove)) {
                            $statusflag = 'Y';
                            $audit_schedule_member = InstSchteamMemberModel::update_teamstatus($statusflag, $audit_scheduleid);
                        }
                        // Add new members (if necessary)
                    }
                    // self::updateTeammembermapping($memarr, $audit_scheduleid);
                } else {
                    $max_audit_scheduleid = InstAuditscheduleModel::query()
                        ->where(function ($query) {
                            $query
                                ->where('statusflag', '=', 'Y')
                                ->orWhere('statusflag', '=', 'F');
                        })
                        ->max('auditscheduleid');

                    $teamMemberIds = json_decode($request->input('tm_uid'), true);
                    if (!is_array($teamMemberIds)) {
                        return response()->json(['error' => 'Invalid JSON format for team members.'], 400);
                    }
                    // $request->merge(['auditscheduleid' =>  $new_auditschedule_id]);
                    // Insert each team member using the TeamMember model
                    InstSchteamMemberModel::create([
                        'auditscheduleid' => $max_audit_scheduleid,
                        'userid' => $request->input('th_uid'),
                        'auditteamhead' => 'Y',
                        'auditfromdate' => $request->input('from_date'),
                        'audittodate' => $request->input('to_date'),
                        'statusflag' => 'Y',
                        'createdon' => now(),
                        'updatedon' => now(),
                    ]);
                    foreach ($teamMemberIds as $memberId) {
                        InstSchteamMemberModel::create([
                            'auditscheduleid' => $max_audit_scheduleid,
                            'userid' => $memberId,
                            'auditteamhead' => 'N',
                            'auditfromdate' => $request->input('from_date'),
                            'audittodate' => $request->input('to_date'),
                            'statusflag' => 'Y',
                            'createdon' => now(),
                            'updatedon' => now(),
                        ]);
                        // print_r($request->input('team_name'),);
                    }
                    $currentRcno = DB::table('audit.mst_dept')
                        ->where('deptcode', $deptcode)
                        ->value('rcno');  // `value()` will return the first column's value

                    if ($currentRcno !== null) {
                        // Increment the rcno
                        $incrementedRcno = $currentRcno + 1;

                        // Update the rcno value
                        DB::table('audit.mst_dept')
                            ->where('deptcode', $deptcode)
                            ->update(['rcno' => $incrementedRcno]);
                    } else {
                        DB::table('audit.mst_dept')
                            ->where('deptcode', $deptcode)
                            ->update(['rcno' => 1]);
                    }
                }

                $status = $request->input('finaliseflag');
                if ($status == 'Y') {
                    return response()->json(['success' => 'Audit Schedule Data Saved Successfully', 'audit_schedule' => $audit_schedule]);
                } else {
                    return response()->json(['success' => 'Audit Initimation Sent Successfully', 'audit_schedule' => $audit_schedule]);
                }

                // Format the new team code (if needed, e.g., with leading zeros)
            }
            // If no user is returned, it means a conflict occurred and an exception was thrown
            // return response()->json(['success' => 'User created/updated successfully', 'user' => $user]);
        } catch (\Exception $e) {
            // Catch the exception thrown by the model and return the error message
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Fetch user data for editing.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchUserData(Request $request)
    {
        // Retrieve deptuserid from the request

        $deptuserid = Crypt::decryptString($request->deptuserid);

        $request->merge(['deptuserid' => $deptuserid]);

        $request->validate([
            'deptuserid' => 'required|integer'
        ], [
            'required' => 'The :attribute field is required.',
            'integer' => 'The :attribute field must be a valid number.'
        ]);

        // Ensure deptuserid is provided
        if (!$deptuserid) {
            return response()->json(['success' => false, 'message' => 'User ID not provided'], 400);
        }

        // Fetch user data based on deptuserid
        $user = UserModel::where('deptuserid', $deptuserid)->first();  // Adjust query as needed

        if ($user) {
            return response()->json(['success' => true, 'data' => $user]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }

    public function fetchschedule_data(Request $request)
    {
        $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
        $inst = InstAuditscheduleModel::query()
            ->join('audit.auditplan as ap', 'inst_auditschedule.auditplanid', '=', 'ap.auditplanid')
            ->join('audit.mst_institution as mi', 'mi.instid', '=', 'ap.instid')
            ->join('audit.inst_schteammember as at', function ($join) {
                $join
                    ->on('at.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
                    ->where('at.auditteamhead', '=', 'Y');
            })
            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'at.userid')
            ->join('audit.deptuserdetails as du', 'at.userid', '=', 'du.deptuserid')
            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
            ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->leftJoin('audit.inst_schteammember as sub_atm', function ($join) {
                $join
                    ->on('sub_atm.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
                    ->where('sub_atm.auditteamhead', '=', 'N');
            })
            ->leftJoin('audit.deptuserdetails as sub_du', 'sub_atm.userid', '=', 'sub_du.deptuserid')
            ->select(
                'inst_auditschedule.auditscheduleid',
                'inst_auditschedule.fromdate',
                'inst_auditschedule.todate',
                'inst_auditschedule.rcno',
                'mi.instename',
                'mi.insttname',
                'mi.instid',
                'mi.mandays',
                'at.auditscheduleid',
                'at.userid as team_head_userid',
                'du.username as team_head_name',
                'cd.chargedescription',
                'teammembers.userid as team_member_userid',
                'teammembers.username as team_member_name',
                'cd.chargedescription',
                'de.desigelname',
                DB::raw('(
                SELECT COUNT(*)
                FROM audit.auditplanteammember as sub_atm
                WHERE sub_atm.auditplanteamid = ap.auditteamid

            ) as total_team_count')
            )
            ->leftJoin(
                DB::raw("(SELECT sub_atm.auditscheduleid, sub_atm.userid, sub_du.username
                            FROM audit.inst_schteammember as sub_atm
                            JOIN audit.deptuserdetails as sub_du
                                ON sub_atm.userid = sub_du.deptuserid
                            WHERE sub_atm.auditteamhead ='N'
                             AND (sub_atm.statusflag =  'Y' )) as teammembers"),
                'teammembers.auditscheduleid',
                '=',
                'inst_auditschedule.auditscheduleid'
            )
            ->where(function ($query) {
                $query->where('inst_auditschedule.statusflag', '=', 'Y');
                // ->orWhere('inst_auditschedule.statusflag', '=', 'N');
            })
            ->where('inst_auditschedule.auditscheduleid', '=', $auditscheduleid)
            ->get();

        foreach ($inst as $item) {
            $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
        }

        if ($inst) {
            return response()->json(['success' => true, 'data' => $inst]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }

    //     public function fetchAllScheduleData(Request $request)
    //     {
    //         $sessiondetails = session('charge');
    //         $deptcode = $sessiondetails->deptcode;
    //         $inst = InstAuditscheduleModel::query()
    //             ->join('audit.auditplan as ap', 'inst_auditschedule.auditplanid', '=', 'ap.auditplanid')
    //             ->join('audit.mst_institution as mi', 'mi.instid', '=', 'ap.instid')
    //             ->join('audit.inst_schteammember as at', function ($join) {
    //                 $join->on('at.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
    //                     ->where('at.statusflag', '=', 'Y');
    //             })
    //             // ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'at.userid')
    //             ->join('audit.deptuserdetails as du', 'at.userid', '=', 'du.deptuserid')
    //             // ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
    //             ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
    //             ->select(
    //                 'inst_auditschedule.auditscheduleid',
    //                 'inst_auditschedule.fromdate',
    //                 'inst_auditschedule.todate',
    //                 'inst_auditschedule.rcno',
    //                 'inst_auditschedule.statusflag',
    //                 'mi.instename',
    //                 'mi.insttname',
    //                 'mi.mandays',
    //                 'ap.auditplanid',
    //                 DB::raw("
    //                  MIN(
    //     CASE
    //         WHEN at.auditteamhead = 'Y' AND at.statusflag = 'Y' THEN du.username::text
    //         ELSE NULL
    //     END
    // ) AS teamhead
    //             "),
    //                 DB::raw("
    //                 STRING_AGG(
    //                     CASE
    //                         WHEN at.auditteamhead = 'N' AND at.statusflag = 'Y' THEN du.username::text
    //                         ELSE NULL
    //                     END, ', ' ORDER BY du.desigcode ASC, du.deptuserid ASC
    //                 ) AS teammembers
    //             "),
    //                 DB::raw('
    //                 COUNT(
    //                     CASE
    //                         WHEN at.statusflag = \'Y\' THEN 1
    //                         ELSE NULL
    //                     END
    //                 ) AS team_count
    //             ')
    //             )
    //             ->where(function ($query) {
    //                 $query->where('inst_auditschedule.statusflag', '=', 'Y')
    //                     ->orWhere('inst_auditschedule.statusflag', '=', 'F');
    //             })
    //             ->where('mi.deptcode', $deptcode)
    //             ->where('at.statusflag', '=', 'Y')
    //             ->groupBy(
    //                 'inst_auditschedule.auditscheduleid',
    //                 'inst_auditschedule.fromdate',
    //                 'inst_auditschedule.todate',
    //                 'inst_auditschedule.rcno',
    //                 'inst_auditschedule.statusflag',
    //                 'mi.instename',
    //                 'mi.insttname',
    //                 'mi.mandays',
    //                 'ap.auditplanid'
    //             )
    //             ->get();

    //         /* $inst = InstAuditscheduleModel::query()
    //                                     ->join('audit.auditplan as ap', 'inst_auditschedule.auditplanid', '=', 'ap.auditplanid')
    //                                     ->join('audit.mst_institution as mi', 'mi.instid', '=', 'ap.instid')
    //                                     ->join('audit.inst_schteammember as at', function ($join) {
    //                                         $join->on('at.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
    //                                             ->where('at.statusflag', '=', 'Y');
    //                                     })
    //                                     ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'at.userid')
    //                                     ->join('audit.deptuserdetails as du', 'at.userid', '=', 'du.deptuserid')
    //                                     ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
    //                                     ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
    //                                     ->select(
    //                                         'inst_auditschedule.auditscheduleid',
    //                                         'inst_auditschedule.fromdate',
    //                                         'inst_auditschedule.todate',
    //                                         'inst_auditschedule.rcno',
    //                                         'inst_auditschedule.statusflag',
    //                                         'mi.instename',
    //                                         'mi.insttname',
    //                                         'mi.mandays',
    //                                         'ap.auditplanid',
    //                                         DB::raw("
    //                                             STRING_AGG(
    //                                                 CASE
    //                                                     WHEN at.auditteamhead = 'Y' AND at.statusflag = 'Y' THEN du.username
    //                                                     ELSE NULL
    //                                                 END, ', '
    //                                             ) AS teamhead
    //                                         "),
    //                                         DB::raw("
    //                                             STRING_AGG(
    //                                                 CASE
    //                                                     WHEN at.auditteamhead = 'N' AND at.statusflag = 'Y' THEN du.username
    //                                                     ELSE NULL
    //                                                 END , ', '
    //                                             ) AS teammembers
    //                                         "),
    //                                         DB::raw('
    //                                             COUNT(
    //                                                 CASE
    //                                                     WHEN at.statusflag = \'Y\' THEN 1
    //                                                     ELSE NULL
    //                                                 END
    //                                             ) AS team_count
    //                                         ') // Total count of all team members (team heads + team members) with statusflag = 'Y'
    //                                     )
    //                                     ->where(function ($query) {
    //                                         $query->where('inst_auditschedule.statusflag', '=', 'Y')
    //                                             ->orWhere('inst_auditschedule.statusflag', '=', 'F');
    //                                     })
    //                                     ->where('at.statusflag', '=', 'Y')
    //                                     ->groupBy(
    //                                         'inst_auditschedule.auditscheduleid',
    //                                         'inst_auditschedule.fromdate',
    //                                         'inst_auditschedule.todate',
    //                                         'inst_auditschedule.rcno',
    //                                         'inst_auditschedule.statusflag',
    //                                         'mi.instename',
    //                                         'mi.insttname',
    //                                         'mi.mandays',
    //                                         'ap.auditplanid'
    //                                     )
    //                                     //->orderBy('du.deptuserid', 'desc') // Order by userid in descending order
    //                                     ->get();*/

    //         //print_r($inst);exit;

    //         foreach ($inst as $item) {
    //             $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
    //         }

    //         // Return data in JSON format
    //         return response()->json(['data' => $inst]); // Ensure the data is wrapped under "data"

    //     }

    public function audit_particulars($catcode = '', $deptcode = '')
    {
        $audit_particulars = MajorWorkAllocationtypeModel::callforrecords($catcode, $deptcode);
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
    //             return "No user found in session.";
    //         }
    //     }

    //     // echo $auditplanid;

    //     // echo $userid;
    //     // Fetch the data based on the provided auditplanid
    //     $inst = AuditModel::query()
    //         ->join('audit.mst_institution as ai', 'ai.instid', '=', 'auditplan.instid')
    //         ->join('audit.auditplanteam as at', 'at.auditplanteamid', '=', 'auditplan.auditteamid')
    //         ->join('audit.auditplanteammember as atm', 'atm.auditplanteamid', '=', 'auditplan.auditteamid')
    //         ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'atm.userid')
    //         ->join('audit.deptuserdetails as du', 'atm.userid', '=', 'du.deptuserid')
    //         ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
    //         ->join('audit.mst_designation as de','de.desigcode', '=', 'du.desigcode')
    //         ->select(
    //             'ai.instename',
    //             'ai.insttname',
    //             'ai.mandays',
    //             'ai.instid',
    //             'ai.catcode',
    //             'ai.deptcode',
    //             'de.desigelname',
    //             'de.desigtlname',
    //             'auditplan.auditteamid',
    //             'auditplan.auditplanid',
    //             'at.auditplanteamid',
    //             'atm.userid',
    //             'uc.userchargeid',
    //             'du.username',
    //             'cd.chargedescription',
    //             'auditplan.auditquartercode',
    //             DB::raw('(
    //                 SELECT COUNT(*)
    //                 FROM audit.auditplanteammember AS sub_atm
    //                 WHERE sub_atm.auditplanteamid = auditplan.auditteamid
    //             ) AS team_member_count')
    //         )
    //         ->where('auditplan.auditplanid', '=', $auditplanid) // Use the decrypted or plain auditplanid
    //         ->where('atm.userid', '=', $userid)
    //         ->where('auditplan.statusflag', '=', 'F')
    //         ->get();

    //         $catcode = $inst->first()->catcode;   // Access the 'catcode' attribute
    //         $deptcode = $inst->first()->deptcode;
    //         $Accountparticulars = self::audit_particulars($catcode,$deptcode);
    //         $quartercode    =   $inst->first()->auditquartercode;
    //         $schdel = DB::table('audit.inst_auditschedule')
    //         ->where('auditplanid', '=', $inst->first()->auditplanid)
    //         ->get();
    //         if(count($schdel)>0)
    //         {
    //             $rcno   =   $schdel->first()->rcno;
    //         }
    //         else
    //         {
    //             $deptdel = DB::table('audit.mst_dept')
    //             // ->where('auditplanid', '=', $inst->first()->auditplanid)
    //             ->where('deptcode', '=', $deptcode)
    //             ->get();

    //             if ($deptdel->isNotEmpty()) {
    //                 // Ensure there's a valid first item before accessing its properties
    //                 $firstItem = $deptdel->first();

    //                 if ($firstItem) {
    //                     // Now safely access properties on the first item
    //                     $rcnocount = $firstItem->rcno;
    //                     $deptsname = $firstItem->deptesname;
    //                     $deptfirstcharacter = substr($deptsname, 0, 1);  // Corrected the typo

    //                     // Increment the count, and ensure it's padded with leading zeros
    //                     $incrementcount = $rcnocount ? $rcnocount + 1 : 1;

    //                     // Pad the increment count with leading zeros to make it 4 digits
    //                     $incrementcount = str_pad($incrementcount, 4, '0', STR_PAD_LEFT);

    //                     // Concatenate the values
    //                     $rcno = $deptfirstcharacter . '25' . $quartercode . $incrementcount;
    //                 }
    //             }
    //         }

    //     // print_r($inst);
    //     // Redirect to the view and pass the data using compact
    //     return view('audit.auditdatefixing', compact('inst','Accountparticulars','rcno'));
    // }

    // public function audit_members(Request $request)
    // {
    //     $planid = $request->input('planid');

    //     $inst = AuditModel::query()
    //         ->join('audit.auditplanteam as at', 'at.auditplanteamid', '=', 'auditplan.auditteamid')
    //         ->join('audit.auditplanteammember as atm', 'atm.auditplanteamid', '=', 'auditplan.auditteamid')
    //         ->join('audit.deptuserdetails as du', function ($join) {
    //             $join->on('du.deptuserid', '=', 'atm.userid')
    //                 ->where('atm.teamhead', '=', 'N'); // Filter for team members
    //         })
    //         // ->join('audit.deptuserdetails as du', 'atm.userid', '=', 'du.deptuserid')
    //         // ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
    //         ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
    //         ->where('auditplan.statusflag', '=', 'F')
    //         ->where('auditplan.auditplanid', '=', $planid)
    //         /*->where('auditplan.auditteamid', function ($query) {
    //         $query->select('auditteamid')
    //             ->from('audit.auditplan')
    //             ->whereColumn('auditteamid', 'auditplan.auditteamid')
    //             ->where('statusflag', 'F')
    //             ->limit(1); // Ensure only one value is returned
    //     })*/
    //         ->select(
    //             'auditplan.auditteamid',
    //             // 'uc.userchargeid',
    //             'auditplan.auditplanid',
    //             // 'cd.chargedescription',
    //             'de.desigelname',
    //             'de.desigtlname',
    //             'du.username',
    //             'du.deptuserid',
    //             'atm.teamhead',
    //             'atm.userid'
    //         )
    //         ->orderBy('du.desigcode', 'asc') // Order by desigcode
    //         ->orderBy('du.deptuserid', 'asc') // Order by userid in descending order
    //         ->get();
    //     return response()->json($inst);
    // }

    // public function auditee_intimation(Request $request)
    // {
    //     $session = $request->session();
    //     if ($session->has('user')) {
    //         $user = $session->get('user');
    //         $userid = $user->userid ?? null;
    //     } else {
    //         return "No user found in session.";
    //     }

    //     $audit_plandetail = InstAuditscheduleModel::fetch_auditplandetails($userid);
    //     foreach ($audit_plandetail as $item) {
    //         $item->encrypted_auditplanid = Crypt::encryptString($item->auditplanid);
    //         $nodalname = $item->nodalname;
    //         $nodaldesig = $item->nodaldesignation;
    //         $item->nodalperson_details = $nodalname . '<br>' . $nodaldesig;

    //         $nodalemail = $item->nodalemail;
    //         $nodalmobile = $item->nodalmobile;
    //         $item->nodalperson_contact = $nodalmobile . '<br>' . $nodalemail;
    //     }

    //     return response()->json(['data' => $audit_plandetail]); // Ensure the data is wrapped under "data"

    //     // print_r($audit_plandetail);
    // }

    // function automateWorkAllocation(Request $request)
    // {
    //     echo $request->auditscheduleid;

    //     InstAuditscheduleModel::Automateworkallocation($request->auditscheduleid)

    //     check error and exception
    // }
    // public function automateWorkAllocation(Request $request)
    // {
    //     try {
    //         // Validate the input
    //         $request->validate([
    //             'auditscheduleid' => 'required|integer'
    //         ]);

    //         // Call the model function
    //         $result = InstAuditscheduleModel::Automateworkallocation($request->auditscheduleid);

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => $result->message ?? 'Work allocation Randomized completed',
    //             'data' => $result
    //         ]);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         Log::error('Validation Error: ' . $e->getMessage());
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ], 422);
    //     } catch (\Exception $e) {
    //         Log::error('Automation Error: ' . $e->getMessage());
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to automate work allocation. Please try again later.'
    //         ], 500);
    //     }
    // }

    public function update_exitmeet(Request $request)
    {
        $auditscheduleid = Crypt::decryptString($request->input('auditscheduleid'));
        if ($request->input('apiforexitmeeting') == 'Y') {
            $controller = new APIController();
            $apiresponse = $controller->exitmeetingdel($auditscheduleid);


DB::table('audit.apilog')->insert([
    'auditscheduleid' => $auditscheduleid,
    'apiname' => 'X',
    'apiurl' => $apiresponse['apiurl'],
    'encryptedpayload' => json_encode($apiresponse['encrypted_payload']),
    'response' => json_encode($apiresponse['response']),
    'createdon' => now()
]);
            if (
                !$apiresponse['status'] ||
                !isset($apiresponse['response']['status']) ||
                trim($apiresponse['response']['status']) !== 'SUCCESS'
            ) {
                return response()->json([
                    'status' => 'error',
                    'message' => $apiresponse['response']['message'] ?? 'Error',
'error'=> $apiresponse['response']['message'] ?? 'Error'
                ], 500);
            }
        }
        $exit_date = Carbon::createFromFormat('d/m/Y', $request->input('exit_date'))->format('Y-m-d');
        $request->merge(['exit_date' => $exit_date]);
        $session = session('user');
        $sessionuser = $session->userid;

        $data = [
            'exitmeetdate' => $request->input('exit_date'),
            'updatedby' => $sessionuser,
            'updatedon' => View::shared('get_nowtime'),
            'exitmeetdatewithtime' => View::shared('get_nowtime'),
        ];
        if ($request->input('spillover') === 'Y') {
            $data['diaryflag'] = 'Y';
        }

        // return $data;
        $audit_schedule = InstAuditscheduleModel::update_exitmeetdate($data, $auditscheduleid);
        if ($audit_schedule->entrymeetdate) {
            $audit_schedule->entrymeetdate = Carbon::createFromFormat('Y-m-d', $audit_schedule->entrymeetdate)->format('d/m/Y');
        }

        if ($audit_schedule->exitmeetdate) {
            $audit_schedule->exitmeetdate = Carbon::createFromFormat('Y-m-d', $audit_schedule->exitmeetdate)->format('d/m/Y');
        }

        if ($audit_schedule->fromdate) {
            $audit_schedule->fromdate = Carbon::createFromFormat('Y-m-d', $audit_schedule->fromdate)->format('d/m/Y');
        }

        if ($audit_schedule->auditscheduleid) {
            $audit_schedule->encrypted_auditscheduleid = Crypt::encryptString($audit_schedule->auditscheduleid);
        }

        $Lang = 'en';
        $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
        $sentsms = $auditModel->send_exitmeetingsms($audit_schedule->auditscheduleid, $Lang);

        return response()->json(['success' => 'Exit Meeting Date was updated successfully', 'data' => $audit_schedule]);
    }

    public function update_entrymeet(Request $request)
    {
        $auditscheduleid = Crypt::decryptString($request->input('auditscheduleid'));
        if ($request->input('apientrymeeting') == 'Y') {
            $controller = new APIController();
            $apiresponse = $controller->entrymeetingdata($auditscheduleid);

DB::table('audit.apilog')->insert([
    'auditscheduleid' => $auditscheduleid,
    'apiname' => 'E',
    'apiurl' => $apiresponse['apiurl'],
    'encryptedpayload' => json_encode($apiresponse['encrypted_payload']),
    'response' => json_encode($apiresponse['response']),
    'createdon' => now()
]);

            if (
                !$apiresponse['status'] ||
                !isset($apiresponse['response']['status']) ||
                trim($apiresponse['response']['status']) !== 'SUCCESS'
            ) {
                return response()->json([
                    'status' => 'error',
                    'message' => $apiresponse['response']['message'] ?? 'Error'
                ], 500);
            }
        }
        DB::beginTransaction();
        try {
            // print_r($request->all());
            // exit;
            $entry_date = Carbon::createFromFormat('d/m/Y', $request->input('entry_date'))->format('Y-m-d');
            $request->merge(['entry_date' => $entry_date]);
            $session = session('user');
            $sessionuser = $session->userid;
            $auditmode = $request->input('auditmode');
            DB::commit();
            if ($auditmode != 'P') {
                $result = DB::selectOne('
select calculatetodatewithmandaysteamsize
from audit.calculateTodateWithMandaysTeamsize(?, ?, ?, ?, ?)
limit 1
', [now()->toDateString(), 0, $auditscheduleid, 0, 'schedule']);

                $proposedExitDate = $result->calculatetodatewithmandaysteamsize;

                $data = [
                    'entrymeetdate' => $request->input('entry_date'),
                    'updatedby' => $sessionuser,
                    'updatedon' => View::shared('get_nowtime'),
                    'entrymeetdatewithtime' => View::shared('get_nowtime'),
                    'proposedexitmeetdate' => $proposedExitDate,
                ];
                // return $data;

                if (empty($auditscheduleid)) {
                    throw new \Exception('No Schedule details');
                }

                $scheduleDetails = AuditManagementModel::fetch_particularscheduleDetails($auditscheduleid);
                $entrymeetexist = $scheduleDetails[0]->entrymeetdate;
                $workallocationflag = $scheduleDetails[0]->workallocationflag;
                $spilloverflag = $scheduleDetails[0]->spilloverflag;

                if (!empty($entrymeetexist)) {
                    throw new \Exception('Entry Meeting Date is filled');
                }

                $epaccsIds = [
                    27431, 27519, 27375, 27435, 27356, 27413, 27366, 27539, 27423, 27483,
                    27360, 27462, 27447, 27508, 27588, 27401, 27327, 27374, 27480, 27331,
                    27453, 27419, 27402, 27478, 27384, 27464, 27489, 27603, 27587, 27492,
                    27496, 27370, 27633, 27600, 27566, 27563, 27473, 27484, 27337, 27450,
                    27619, 27474, 27485, 27382, 27598, 27455, 27418, 27495, 27403, 27408,
                    27428, 27334, 27378, 27444, 27329, 27344, 27397, 27422, 27369, 27669,
                    27363, 27458, 27352, 27345, 27580, 27551, 27468, 27648, 27497, 27584,
                    27506, 27432, 27525, 27510, 27651, 27537, 27429, 27387, 27475, 27573,
                    27562, 27434, 27348, 27522, 27343, 27405
                ];

                $is_epaccs = in_array($auditscheduleid, $epaccsIds);

                if ($workallocationflag == 'Y' && $spilloverflag != 'Y' && (!$is_epaccs)) {
                    throw new \Exception('Work Allocation was already allocated');
                }

                if ($spilloverflag != 'Y') {
                    if ($is_epaccs) {
                        $status = 'success';
                    } else {
                        $result = WorkAllocationModel::Automateworkallocation($auditscheduleid);
                        $jsonString = $result[0]->response;
                        $workAllcdata = json_decode($jsonString, true);

                        $status = $workAllcdata['status'] ?? null;
                        $message = $workAllcdata['message'] ?? null;
                    }
                } else {
                    $status = 'success';
                }
            } else {
                $data = [
                    'entrymeetdate' => $request->input('entry_date'),
                    'updatedby' => $sessionuser,
                    'updatedon' => View::shared('get_nowtime'),
                    'entrymeetdatewithtime' => View::shared('get_nowtime'),
                    'workallocationflag' => 'Y',
                ];
                $spilloverflag = 'N';
                $status = 'success';
            }

            // $message = 'sdasd';
            // $status = 'success';

            if ($status == 'error') {
                return response()->json([
                    'status' => 'error',
                    'message' => $message,
                ], 500);
            } else if ($status == 'success') {
                $audit_schedule = InstAuditscheduleModel::update_entrymeetdate($data, $auditscheduleid, $spilloverflag, $auditmode, $is_epaccs);

                if ($audit_schedule->entrymeetdate) {
                    $audit_schedule->entrymeetdate = Carbon::createFromFormat('Y-m-d', $audit_schedule->entrymeetdate)->format('d/m/Y');
                }

                if ($audit_schedule->exitmeetdate) {
                    $audit_schedule->exitmeetdate = Carbon::createFromFormat('Y-m-d', $audit_schedule->exitmeetdate)->format('d/m/Y');
                }

                if ($audit_schedule->fromdate) {
                    $audit_schedule->fromdate = Carbon::createFromFormat('Y-m-d', $audit_schedule->fromdate)->format('d/m/Y');
                }

                if ($audit_schedule->auditscheduleid) {
                    $audit_schedule->encrypted_auditscheduleid = Crypt::encryptString($audit_schedule->auditscheduleid);
                }

                $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
                // $sentsms = $auditModel->send_entrymeeting($audit_schedule->auditscheduleid);
            }

            return response()->json(['success' => 'Entry Meeting Date was updated successfully', 'data' => $audit_schedule]);
        } catch (Exception $e) {
            DB::rollBack();

            // Optionally log the error
            // Log::error($e);

            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetch_allocatedwork(Request $request)
    {
        $auditscheduleid = $request->scheduleid;
        $allocatedwor_det = TransWorkAllocationModel::fetch_allocatedwork($auditscheduleid);

        return response()->json(['success' => 'data was fetched successfully', 'data' => $allocatedwor_det]);
    }
}
