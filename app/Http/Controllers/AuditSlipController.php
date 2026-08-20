<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;



class AuditSlipController extends Controller
{
    public function showAuditSlips()
    {
        // Fetch data from the database where processcode = 'X'
        $auditSlips = DB::table('audit.trans_auditslip')
            ->where('processcode', 'X')
            ->get();

        // Pass data to the view
        return view('audit/transauditslip', ['auditSlips' => $auditSlips]);
    }

    public static function FetchAuditSlips($auditscheduleid)
    {
        // Fetch data from the database where processcode = 'X'

        $auditSlips =  DB::table('audit.trans_auditslip')
                    ->join('audit.mst_process as p', 'p.processcode', '=', 'trans_auditslip.processcode')
                    ->join('audit.mst_mainobjection as m', 'm.mainobjectionid', '=', 'trans_auditslip.mainobjectionid')
                    ->join('audit.mst_subobjection as s', 's.subobjectionid', '=', 'trans_auditslip.subobjectionid')
                    ->join('audit.deptuserdetails as dud', 'dud.deptuserid', '=', 'trans_auditslip.createdby')
                    ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'trans_auditslip.auditplanid')
                    ->join('audit.audtieeuserdetails as auditee', 'ap.instid', '=', 'auditee.instid')
                    ->select('dud.username as auditorname', 'auditee.username','mainslipnumber','m.objectionename', 's.subobjectionename', 'mainslipnumber', 'amtinvolved', 'slipdetails','p.processelname','p.processcode','liability', 'liabilityname','severity')
                    ->where('audit.trans_auditslip.processcode', 'X')
                    ->where('audit.trans_auditslip.auditscheduleid', $auditscheduleid)
                    ->orderBy('mainslipnumber', 'asc')
                    ->get();

        // Pass data to the view
        return $auditSlips;
    }

	public function getSlipDetails($slipId)
    {
$slipId = Crypt::decryptString($slipId);
       // Simulate fetching slip details from the database (replace this with actual logic)
        $slipDetails = DB::table('audit.trans_auditslip')
        ->join('audit.mst_process as p', 'p.processcode', '=', 'trans_auditslip.processcode')
        ->join('audit.mst_mainobjection as m', 'm.mainobjectionid', '=', 'trans_auditslip.mainobjectionid')
        ->leftjoin('audit.mst_subobjection as s', 's.subobjectionid', '=', 'trans_auditslip.subobjectionid')
        ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'trans_auditslip.auditplanid')
        ->join('audit.mst_auditquarter as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
        ->join('audit.audtieeuserdetails as auditee', 'ap.instid', '=', 'auditee.instid')
        ->join('audit.mst_institution as inst', 'ap.instid', '=', 'inst.instid')
        ->join('audit.inst_schteammember as schteam', 'schteam.schteammemberid', '=', 'trans_auditslip.schteammemberid')
        ->join('audit.deptuserdetails as dud', 'dud.deptuserid', '=', 'trans_auditslip.createdby')
        ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'dud.desigcode')

        ->select(
            'trans_auditslip.auditslipid',
            'trans_auditslip.auditscheduleid',
            'm.objectionename',
//            's.subobjectionename',
		DB::raw("CASE
                        WHEN trans_auditslip.subobjectionid IS NOT NULL THEN s.subobjectionename
                        ELSE 'N/A'
                     END AS subobjectionename"),
            'trans_auditslip.mainslipnumber',
            'trans_auditslip.slipdetails',
            'trans_auditslip.amtinvolved',
            'trans_auditslip.auditeeremarks',
            'trans_auditslip.auditorremarks',
            'trans_auditslip.memberrejoinderremarks',
            'trans_auditslip.finalremarks',
            'dud.username as auditorname',
            'trans_auditslip.createdby',
            'p.processelname',
            'inst.instename',
            'desig.desigelname as auditordesig',
            DB::raw("CASE
                        WHEN trans_auditslip.severity = 'M' THEN 'Medium'
                        WHEN trans_auditslip.severity = 'H' THEN 'High'
                        WHEN trans_auditslip.severity = 'L' THEN 'Low'
                        ELSE 'Unknown'
                    END AS severity"), // Transform severity value
            DB::raw("TO_CHAR(trans_auditslip.updatedon, 'DD-MM-YYYY  HH24:MI:SS') as updatedon"), // Format date for PostgreSQL
            'auditee.username',
            DB::raw("CASE WHEN trans_auditslip.rejoinderstatus = 'Y' THEN 'Yes' ELSE 'No' END AS rejoinderstatus") ,// Transform in query
            DB::raw("CASE WHEN trans_auditslip.liability = 'Y' THEN 'Yes' ELSE 'No' END AS liability"), // Transform in query
            'trans_auditslip.liabilityname',
            'trans_auditslip.liabilitydesig',
            'trans_auditslip.liabilitygpfno'
        )
        ->where('trans_auditslip.auditslipid', $slipId)
        ->first();

        if (!$slipDetails) {
        return response()->json(['status' => 'error', 'message' => 'Slip not found.'], 404);
        }

        // Get the current user
       /* $currentUser = auth()->user(); // Assuming you're using Laravel's authentication

        // Check if the current user is the same as the 'createdby' user and has the 'teamhead' flag set to 'Y'
        $isTeamMember = DB::table('audit.inst_schteammember')
        ->where('userid', $slipDetails->createdby) // Use the schteamid from the slip details
        ->where('auditteamhead', 'N') // Check for team head flag
        ->exists();*/


        // Get the auditscheduleid from slipDetails
        $auditScheduleId = $slipDetails->auditscheduleid;

        // Check if the current user is a team member with teamhead flag 'Y'
        $TeamHeadget = DB::table('audit.inst_schteammember')
            ->join('audit.deptuserdetails as dud', 'dud.deptuserid', '=', 'inst_schteammember.userid')
            ->select('dud.username as teamheadname')
            ->where('inst_schteammember.auditscheduleid', $auditScheduleId) // Use auditscheduleid for the team member check
            ->where('inst_schteammember.auditteamhead', 'Y') // Ensure it's a team head
            ->first();
        /*if ($isTeamMember) {
            // Logic if the user is the team head
            return response()->json([
                'status' => 'success',
                'data' => $slipDetails,
                'is_team_member' => true,
                'teamheadname' => $TeamHeadget->teamheadname
            ]);
        } else {*/
            return response()->json([
                'status' => 'success',
                'data' => $slipDetails,
                //'is_team_member' => false,
                'teamheadname' => $TeamHeadget->teamheadname
            ]);
       // }

    }

    public function getSlipHistoryDetails($slipId)
    {
	$slipId = Crypt::decryptString($slipId);
        $auditSlips = DB::table('audit.sliphistorytransactions as hist')
                        ->join('audit.trans_auditslip as trans_auditslip', 'trans_auditslip.auditslipid', '=', 'hist.auditslipid')
                        ->join('audit.mst_process as p', 'p.processcode', '=', 'hist.processcode')
                        ->join('audit.inst_schteammember as schteam', 'schteam.schteammemberid', '=', 'trans_auditslip.schteammemberid')
                        ->join('audit.deptuserdetails as dud_sch', 'dud_sch.deptuserid', '=', 'schteam.userid') // Team member details
                        ->leftJoin('audit.deptuserdetails as dud_forwardedby', 'dud_forwardedby.deptuserid', '=', 'hist.forwardedby') // Forwarded by (Auditor)
                        ->leftJoin('audit.deptuserdetails as dud_forwardedto', 'dud_forwardedto.deptuserid', '=', 'hist.forwardedto') // Forwarded to (Auditor)
                        ->leftJoin('audit.audtieeuserdetails as aud_forwardedby', 'aud_forwardedby.auditeeuserid', '=', 'hist.forwardedby') // Forwarded by (Auditee)
                        ->leftJoin('audit.audtieeuserdetails as aud_forwardedto', 'aud_forwardedto.auditeeuserid', '=', 'hist.forwardedto') // Forwarded to (Auditee)
                        ->select(
                            'hist.forwardedby',
                            'hist.forwardedto',
                            'hist.forwardedbyusertypecode',
                            'hist.forwardedtousertypecode',
                            'p.processelname',
                            'hist.remarks',
                            DB::raw("TO_CHAR(hist.forwardedon, 'DD-MM-YYYY  hh:MI AM') as forwardedon"), // Date formatting
                            DB::raw("
                                CASE
                                    WHEN hist.forwardedbyusertypecode = 'I'
                                    THEN CONCAT(aud_forwardedby.username, ' (Auditee)')
                                    ELSE CONCAT(dud_forwardedby.username, ' (Auditor)')
                                END AS forwardedby_username
                            "), // Append (Auditee) or (Auditor) for forwardedby
                            DB::raw("
                                CASE
                                    WHEN hist.forwardedtousertypecode = 'I'
                                    THEN CONCAT(aud_forwardedto.username, ' (Auditee)')
                                    ELSE CONCAT(dud_forwardedto.username, ' (Auditor)')
                                END AS forwardedto_username
                            ") // Append (Auditee) or (Auditor) for forwardedto
                        )
                        ->where('hist.auditslipid', $slipId)
                        ->orderBy('hist.transhistoryid', 'desc') // Order by transhistoryid descending
                        ->get();

        return response()->json(['status' => 'success', 'data' => $auditSlips]);
    }


}
