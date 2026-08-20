<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

use App\Models\AuditTeamModel;
use App\Models\DeptModel;
use App\Models\DesignationModel;
use App\Models\DistrictModel;
use App\Models\UserChargeDetailsModel;
use App\Models\AuditMemberModel;
use App\Models\Charge;
use App\Models\AssignCharge;
use App\Models\UserChargeDetailModel;
use Illuminate\Http\Request;
use App\Models\RegionModel;
use App\Models\DeptMapModel;

use Illuminate\Support\Facades\DB;

class AuditTeam extends Controller
{


    public function createAuditTeam(Request $request, $userId = null)
    {
        // Validate incoming request data
        $request->validate([
            //'distcode'   => ['required'],
            //'deptcode'     => ['required', 'string', 'regex:/^\d+$/'],
            'teamHeadId'   => 'required|integer',
            'teamMemberIds' => 'required|json',
            'team_name'    => 'required|string|max:50',
        ], [
            'required' => 'The :attribute field is required.',
            'json'     => 'The :attribute field must be a valid JSON.',
            'max'      => 'The :attribute field must not exceed :max characters.',
            'regex'    => 'The :attribute field must be a valid number.',
            'integer'  => 'The :attribute field must be a valid number.',
        ]);

        // Handle update action
        if ($request->action == 'update') {
            try {
                $auditteamid = Crypt::decryptString($request->auditteamid);
                $request->merge(['auditplanteamid' => $auditteamid]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid audit team ID provided for decryption.'], 400);
            }
        } else {
            $auditteamid = ''; // Set to empty string for create action
        }

        $sessioncharge = session('charge');
        $deptcode = $sessioncharge->deptcode;
        $distcode = $sessioncharge->distcode;

        $deptcode = $deptcode ? $deptcode : $request->input('deptcode');
        $distcode = $distcode ? $distcode : $request->input('distcode');


        try {
            // Prepare data for the Audit Team
            $data = [
                'distcode'   => $distcode,
                'auditordiststatus'   => 'D',
                'deptcode'   => $deptcode,
                'teamname'   => $request->input('team_name'),
                'createdon'  => now(),
                'updatedon'  => now(),
            ];

            // print_r($data);

            // Set status flag based on 'finaliseflag' value
            if ($request->input('finaliseflag') == 'N') {
                $data['statusflag'] = 'Y';
            }
            if ($request->input('finaliseflag') == 'Y') {
                $data['statusflag'] = 'F';
            }

            // Attempt to create or update the audit team
            $auditTeam = AuditTeamModel::createIfNotExistsOrUpdate($data, $auditteamid);

            if ($auditTeam) {
                // If updating, get the team member IDs from the request
                $teamMemberIds = json_decode($request->input('teamMemberIds'), true);

                // Validate the team member IDs array
                if (!is_array($teamMemberIds)) {
                    return response()->json(['error' => 'Invalid JSON format for team members.'], 400);
                }

                // // Validate each team member ID to be an integer and exist
                // foreach ($teamMemberIds as $memberId) {
                //     if (!is_int($memberId) || !UserModel::find($memberId)) { // Assuming you have a User model
                //         return response()->json(['error' => 'Invalid member ID: ' . $memberId], 400);
                //     }
                // }

                // Prepare data for the Audit Member update or creation
                $data_member = [
                    'teamid'        => $auditTeam,
                    'teamheadid'    => $request->input('teamHeadId'),
                    'teammembersid' => $teamMemberIds,
                ];

                // Start a database transaction to ensure atomicity
                DB::beginTransaction();

                try {
                    // Call the method to update or create team members
                    AuditMemberModel::updateOrCreate($data_member);
                    //exit;

                    // Commit the transaction if everything is successful
                    DB::commit();

                    return response()->json(['success' => true, 'message' => 'Audit Team Data Saved Sucessfully.']);


                    // return response()->json(['success' => true, 'message' => 'Team created/updated successfully']);
                } catch (\Illuminate\Database\QueryException $e) {
                    // Catch database-specific exceptions (e.g., SQL errors)
                    //    / DB::rollback();
                    return response()->json(['error' => 'Database query error during team members update: ' . $e->getMessage()], 500);
                } catch (\Exception $e) {
                    // General exception catch
                    DB::rollback();
                    return response()->json(['error' => 'Unexpected error during team members update: ' . $e->getMessage()], 500);
                }
            } else

                return response()->json(['success' => false, 'message' => 'Team not already exists or could not be created.'], 400);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle any database issues related to the audit team creation or update
            return response()->json(['error' => 'Database query error during audit team creation/update: ' . $e->getMessage()], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['error' => 'Validation failed: ' . $e->getMessage()], 422);
        } catch (\Exception $e) {
            // Handle other unexpected errors
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }


    public function fetchAllData()
    {
        $sessioncharge = session('charge');
        $deptcode = $sessioncharge->deptcode;
        $regioncode = $sessioncharge->regioncode;
        $distcode = $sessioncharge->distcode;

        $teamData = AuditTeamModel::query()
            ->join('audit.mst_dept as de', 'de.deptcode', '=', 'auditplanteam.deptcode')
            ->join('audit.auditplanteammember as aptm', 'auditplanteam.auditplanteamid', '=', 'aptm.auditplanteamid')
            // ->leftjoin('audit.userchargedetails as uc', 'uc.userid', '=', 'aptm.userid')
            // ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'aptm.userid')
            // ->leftjoin('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
            // ->join('audit.mst_designation as d', 'd.desigcode', '=', 'du.desigcode')
            // ->leftjoin('audit.mst_district as di', 'di.distcode', '=', 'cd.distcode')
            // ->leftjoin('audit.mst_region as reg', 'reg.regioncode', '=', 'cd.regioncode')
            ->select(
                // Team head details (using MAX with CASE)
                // DB::raw("MAX(CASE WHEN aptm.teamhead = 'Y' THEN du.username || ' - ' || d.desigelname || ' - ' || di.distename END) AS teamhead_details"),

                // Team name and department name
                'auditplanteam.teamname',
                'de.deptelname',
                'auditplanteam.auditplanteamid',
                'auditplanteam.statusflag',
                // 'di.distename',
                // 'reg.regionename',

                // Members' details (excluding the team head)
                // DB::raw("string_agg(CASE WHEN aptm.teamhead = 'N' THEN du.username || ' - ' || d.desigelname || ' - ' || di.distename END, ', ') AS members")
            )
            // ->where('aptm.statusflag', '=', 'Y') // Active team member
            ->whereIn('auditplanteam.statusflag', ['Y', 'F']); // Active or flagged teams
            // ->groupBy('auditplanteam.auditplanteamid', 'auditplanteam.teamname', 'de.deptelname', 'auditplanteam.statusflag', 'di.distename', 'reg.regionename');

        // Conditional WHERE clauses for deptcode, regioncode, distcode based on session or input
        if (!empty($deptcode)) {
            $teamData->where('auditplanteam.deptcode', '=', $deptcode);
        }

        // if (!empty($regioncode)) {
        //     $teamData->where('cd.regioncode', '=', $regioncode);
        // }

        // if (!empty($distcode)) {
        //     $teamData->where('auditplanteam.distcode', '=', $distcode);
        // }

        //dd($teamData->toSql());
        // Execute the query
        $teamData = $teamData->get();

        // print_r($teamData);
        //dd($teamData); 


        foreach ($teamData as $user) {
            $user->encrypted_auditteamid = Crypt::encryptString($user->auditplanteamid);
        }

        //  print_r($teamData);
        return response()->json(['data' => $teamData]);
    }


    public function fetchTeamData(Request $request)
    {
        // Decrypt the auditplanteamid from the request
        $auditplanteamid = Crypt::decryptString($request->auditteamid);

        // Merge decrypted auditplanteamid back into the request
        $request->merge(['auditplanteamid' => $auditplanteamid]);

        // Validate the auditplanteamid input
        $request->validate([
            'auditplanteamid' => 'required|integer'
        ], [
            'required' => 'The :attribute field is required.',
            'integer' => 'The :attribute field must be a valid number.'
        ]);

        // Ensure auditplanteamid is provided
        if (!$auditplanteamid) {
            return response()->json(['success' => false, 'message' => 'Team Code not provided'], 400);
        }



        $teamData = AuditTeamModel::query()
            ->join('audit.mst_dept as de', 'de.deptcode', '=', 'auditplanteam.deptcode')
            ->join('audit.auditplanteammember as aptm', 'auditplanteam.auditplanteamid', '=', 'aptm.auditplanteamid')
            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'aptm.userid')
            ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'aptm.userid')
            ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
            ->join('audit.mst_designation as d', 'd.desigcode', '=', 'du.desigcode')
            ->join('audit.mst_district as di', 'di.distcode', '=', 'cd.distcode')
            ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'cd.regioncode')

            ->select(
                // Team head details (using MAX with CASE)
                DB::raw("MAX(CASE WHEN aptm.teamhead = 'Y' THEN du.username || ' - ' ||  d.desigelname  || ' - ' ||di.distename|| ' - ' || aptm.userid END) AS teamhead_details"),
                'auditplanteam.deptcode',
                'auditplanteam.auditordiststatus',
                'auditplanteam.distcode',
                // Team name and department name
                'auditplanteam.teamname',
                'de.deptelname',
                'auditplanteam.auditplanteamid',
                'reg.regioncode',

                // Members' details (excluding the team head)
                DB::raw("string_agg(CASE WHEN aptm.teamhead = 'N' THEN du.username || ' - ' || d.desigelname || ' - ' || di.distename || ' - ' || aptm.userid END, ', ') AS members")
            )
            ->where('auditplanteam.auditplanteamid', '=', $auditplanteamid)
            // Ensure the user is active (statusflag = 'Y')
            ->where('aptm.statusflag', '=', 'Y')
            ->where('auditplanteam.statusflag', '=', 'Y')
            ->groupBy('auditplanteam.auditplanteamid', 'auditplanteam.teamname', 'de.deptelname', 'auditplanteam.deptcode', 'auditplanteam.auditordiststatus', 'auditplanteam.distcode', 'reg.regioncode')
            ->get();





        // Check if data is retrieved
        if ($teamData) {
            return response()->json(['success' => true, 'data' => $teamData]);
        } else {
            return response()->json(['success' => false, 'message' => 'Team Detail not found'], 404);
        }
    }


    public function getAuditors(Request $request)
    {
        $distcode = $request->distcode;
        $deptcode = $request->deptcode;
        // print_r( $distcode);
        // $teamcode = $request->teamcode;

        if ($request->auditteamid)
            $auditteamid = Crypt::decryptString($request->auditteamid);
        else    $auditteamid   =   '';

        if ($auditteamid) {

            // Step 1: First, retrieve the teamhead_userid and teammember_userid
            $excludedUserIds = AuditTeamModel::query()
                ->join('audit.auditplanteammember as t', 't.auditplanteamid', '=', 'auditplanteam.auditplanteamid')  // Join with the teammember table
                ->where('auditplanteam.auditplanteamid', '=', $auditteamid)  // Filter by the specified teamcode
                ->where('t.statusflag', '=', 'Y') // Filter by teamcode
                ->select('t.userid')  // Select the user IDs
                ->get()
                ->pluck('userid')  // Get all teamhead_userid values
                ->merge(
                    AuditTeamModel::query()
                        ->join('audit.auditplanteammember as t', 't.auditplanteamid', '=', 'auditplanteam.auditplanteamid')  // Join again with the teammember table
                        ->where('auditplanteam.auditplanteamid', '=', $auditteamid)  // Filter by the specified teamcode
                        ->where('t.statusflag', '=', 'Y') // Filter by teamcode
                        ->select('t.userid')  // Select the user IDs
                        ->get()
                        ->pluck('userid')  // Get all teammember_userid values
                );
            // Step 2: Then, use the excluded user IDs in your original query to filter out them
            $auditors = UserChargeDetailsModel::query()
                ->join('audit.deptuserdetails as du', 'userchargedetails.userid', '=', 'du.deptuserid')
                ->join('audit.chargedetails as cd', 'userchargedetails.chargeid', '=', 'cd.chargeid')
                ->join('audit.mst_district as md', 'md.distcode', '=', 'cd.distcode')
                ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
                ->select(
                    'du.deptuserid',
                    'du.username',
                    'cd.chargedescription',
                    'md.distename',
                    'md.disttname',
                    'userchargedetails.userid',
                    'de.desigelname',
                    'de.desigtlname'

                )
                ->where('userchargedetails.statusflag', '=', 'Y')
                ->when($distcode !== 'A', function ($query) use ($distcode) {
                    return $query->where('md.distcode', '=', $distcode);
                })
                ->whereNotIn('userchargedetails.userid', $excludedUserIds)  // Exclude the userids
                ->get();
        } else {
            $auditors = UserChargeDetailsModel::query()
                ->join('audit.deptuserdetails as du', 'userchargedetails.userid', '=', 'du.deptuserid')
                ->join('audit.chargedetails as cd', 'userchargedetails.chargeid', '=', 'cd.chargeid')
                ->join('audit.mst_district as md', 'md.distcode', '=', 'cd.distcode')
                ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
                ->select(
                    'du.deptuserid',
                    'du.username',
                    'cd.chargedescription',
                    'md.distename',
                    'md.disttname',
                    'de.desigelname',
                    'de.desigtlname'

                )
                ->where('userchargedetails.statusflag', '=', 'Y')
                ->where('du.deptcode', '=',  $deptcode)

                ->when($distcode !== 'A', function ($query) use ($distcode) {
                    // Only apply the distcode filter if it is not 'A'
                    return $query->where('md.distcode', '=', $distcode);
                })
                ->get();
        }



        return response()->json(['success' => true, 'auditor' => $auditors]);
    }

     public function FilterByDept(Request $request)
    {
        if ($request->regioncode) {

            $district = DB::table('audit.mst_institution as inst')
                ->join('audit.mst_district as dist', 'inst.distcode', '=', 'dist.distcode')
                ->where('inst.regioncode', $request->regioncode)
                ->where('inst.deptcode', $request->deptcode)
                ->select('dist.distcode', 'dist.distename', 'dist.disttname')
                ->distinct()
                ->get();

            $districtArray = $district->map(function ($district) {
                return [
                    'distcode' => $district->distcode,
                    'distename' => $district->distename,
                    'disttname' => $district->disttname,
                ];
            })->toArray();

            return response()->json($districtArray);
        } else {

            $region = DB::table('audit.mst_institution as inst')

                ->join('audit.mst_region as re', 're.regioncode', '=', 'inst.regioncode')
                // ->where('inst.regioncode', $request->regioncode)
                ->where('inst.deptcode', $request->deptcode)
                ->where('re.statusflag', '=', 'Y')
                ->where('inst.statusflag', '=', 'Y')
                ->select('re.regioncode', 're.regionename', 're.regiontname')
                ->distinct()
                ->get();

            $headDesig =  DB::table('audit.mst_dept as dept')
                ->join('audit.mst_designation as desig', 'desig.deptcode', '=', 'dept.deptcode')
                ->where('desig.belowaddesig', 'Y')
                ->where('dept.deptcode', $request->deptcode)
                ->select('desig.desigcode')
                ->orderBy('desig.orderid', 'ASC')
                ->limit($request->headlevel)
                ->get();


            // $region = RegionModel::where('statusflag', '=', 'Y')
            //     ->where('deptcode', $request->deptcode)
            //     ->get();
            // $desigArray=   $headDesig->map(function ($headDesig) {
            //                 return [
            //                     'desigcode' => $headDesig->desigcode,


            //                 ];
            //             })->toArray();

            $regionArray = $region->map(function ($region) {
                return [
                    'regioncode' => $region->regioncode,
                    'regionename' => $region->regionename,
                    'regiontname' => $region->regiontname,

                ];
            })->toArray();

            return response()->json(['regiondet' => $regionArray, 'headlevelarray' => $headDesig]);
        }
    }





    public function creatuser_dropdownvalues()
    {
        $chargeData = session('charge');
        $regioncode = $chargeData->regioncode;
        $distcode   = $chargeData->distcode;
        $deptcode   = $chargeData->deptcode;

        $dept = DeptModel::where('statusflag', '=', 'Y')
            // ->where('deptcode', $deptcode)
            ->orderBy('orderid', 'asc')
            ->get();

        $designation = DesignationModel::where('statusflag', '=', 'Y')
            ->orderBy('desigelname', 'asc')
            ->get();

        $region = DB::table('audit.mst_region')
            // ->where('regioncode', $regioncode)
            ->orderBy('regioncode', 'asc')
            ->get();

        $district = DistrictModel::orderBy('distename', 'asc')->get();

        $auditors = UserChargeDetailsModel::query() // Start the query builder
            ->join('audit.deptuserdetails as du', 'userchargedetails.userid', '=', 'du.deptuserid') // INNER JOIN with alias 'dept'
            ->join('audit.chargedetails as cd', 'userchargedetails.chargeid', '=', 'cd.chargeid') // INNER JOIN with alias 'desig'
            ->join('audit.mst_designation as desig', 'du.desigcode', '=', 'desig.desigcode') // INNER JOIN with alias 'dept'
            ->join('audit.mst_district as md', 'md.distcode', '=', 'cd.distcode')
            ->select(
                'userchargedetails.userchargeid',
                'du.deptuserid',
                'du.username',
                'cd.chargedescription',
                'md.distename',
                'md.distcode'
            )
            ->where('userchargedetails.statusflag', '=', 'Y') // Filter records where statusflag is 'Y'
            ->orderBy('du.username', 'desc') // Order results by createdon in descending order
            ->get();

        return view('audit.auditteam', compact('dept', 'designation', 'district', 'auditors', 'region'));
        // return view('audit.auditteam', compact('dept'));

    }
}
