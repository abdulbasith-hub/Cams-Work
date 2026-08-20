<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class ReportModel extends Model
{
    use HasFactory;

    protected $connection = 'pgsql'; // PostgreSQL connection
    protected $table = BaseModel::REPORTCONTENTS_table;
protected static $auditplanmappingtable = BaseModel::AUDITPLANMAPPING_TABLE;
    protected static $auditplanteammemberTable = BaseModel::TEAMMEMBER_Table;
    protected static $Instschedule_Table            =  BaseModel::INSTSCHEDULE_TABLE;
    protected static $AuditPlan_Table               =  BaseModel::AUDITPLAN_TABLE;
    protected static $InstscheduleMem_Table         =  BaseModel::INSTSCHEDULEMEM_TABLE;
    protected static $AuditPlanTeam_Table           =  BaseModel::AUDITPLANTEAM_TABLE;
    protected static $Institution_Table             =  BaseModel::INSTITUTION_TABLE;
    protected static $AuditeeUserDetail_Table       =  BaseModel::AUDITEEUSERDETAIL_TABLE;
    protected static $TypeofAudit_Table             =  BaseModel::TYPEOFAUDIT_TABLE;
    protected static $Dept_Table                    =  BaseModel::DEPT_TABLE;
    protected static $MstAuditeeInsCategory_Table   =  BaseModel::MSTAUDITEEINSCATEGORY_TABLE;
    protected static $AuditQuarter_Table            =  BaseModel::AUDITQUARTER_TABLE;
    protected static $UserChargeDetails_Table       =  BaseModel::USERCHARGEDETAIL_TABLE;
    protected static $UserDetails_Table             =  BaseModel::USERDETAIL_TABLE;
    protected static $Designation_Table             =  BaseModel::DESIGNATION_TABLE;
    protected static $MapYearcode_Table             =  BaseModel::MAPYEARCODE_TABLE;
    protected static $AuditPeriod_Table             =  BaseModel::AUDITPERIOD_TABLE;
    protected static $FileUpload_Table              =  BaseModel::FILEUPLOAD_TABLE;
    protected static $AccountParticulars_Table      =  BaseModel::ACCOUNTPARTICULARS_TABLE;
    protected static $TransaccountDetails_Table     =  BaseModel::TRANSACCOUNTDETAILS_TABLE;
    protected static $CallforRecordsAuditee_Table   =  BaseModel::CALLFORRECORDS_AUDITEE_TABLE;
    protected static $TransCallforRecords_Table     =  BaseModel::TRANSCALLFORRECORDS_TABLE;
    protected static $MapCallforRecords_Table       =  BaseModel::MAPCALLFORRECORDS_TABLE;
    protected static $ChargeDetails_Table           =  BaseModel::CHARGEDETAIL_TABLE;
    protected static $District_Table                =  BaseModel::DIST_Table;

    protected static $ProcessFlag_Table             =  BaseModel::PROCESSFLAG_TABLE;
    protected static $MajorObj_Table                =  BaseModel::MAINOBJ_TABLE;
    protected static $SubObj_Table                  =  BaseModel::SUBOBJ_TABLE;
    protected static $TransAuditSlip_Table          =  BaseModel::TRANSAUDITSLIP_TABLE;

    protected static $TransWorkAllocation_Table     =  BaseModel::TRANSWORKALLOCATION_TABLE;
    protected static $MapWorkAllocation_Table       =  BaseModel::MAPWORKALLOCATION_TABLE;
    protected static $MajWorkAllocation_Table       =  BaseModel::MAJWORKALLOCATION_TABLE;
    protected static $SlipHistroyDetails_Table      =  BaseModel::SLIPHISTORYDETAILS_TABLE;

    // protected static $userchargedetail_table = BaseModel::USERCHARGEDETAIL_TABLE;
    protected static $deptTable = BaseModel::DEPT_TABLE;
    protected static $distTable = BaseModel::DIST_Table;
    protected static $institutionTable = BaseModel::INSTITUTION_TABLE;
    protected static $sliphistorytable = BaseModel::SLIPHISTORYTRANSACTION_TABLE;
    protected static $auditplan_table = BaseModel::AUDITPLAN_TABLE;
    protected static $instauditschedule_table    = BaseModel::INSTSCHEDULE_TABLE;
    protected static $auditeinstmap              = BaseModel::AUDITOR_INSTMAPPING_TABLE;
    protected static $regionTable                = BaseModel::REGION_TABLE;
    protected static $instauditschedulemem_table = BaseModel::INSTSCHEDULEMEM_TABLE;
    protected static $designation = BaseModel::DESIGNATION_Table;  
    protected static $roleactionTable = BaseModel::ROLEACTION_TABLE;
    protected static $rolemapping_table = BaseModel::ROLEMAPPING_TABLE;//---idle & Checkscedulestatus
      protected static $slipFileUpload_Table = BaseModel::SLIP_FILEUPLOAD_TABLE;

    protected static $FileUploadDetail_Table = BaseModel::FILEUPLOAD_TABLE;

    protected static $Inspection_History_Table = BaseModel::INSPECTIONHISTORY_TABLE;

    protected static $Trans_Inspection_Table = BaseModel::TRANSAUDITINSPECTION_TABLE;


    protected static $transinspection_table = BaseModel::TRANSAUDITINSPECTION_TABLE;
 protected static $transparahistory = BaseModel::HISTORYTRANSPARA_TABLE;
    protected static $parafileupload = BaseModel::PARAFILEUPLOAD_TABLE;
    protected static $transfollowup = BaseModel::TRANSFOLLOWUP_TABLE;
    protected static $transpara = BaseModel::TRANSPARA_TABLE;
    protected static $severitytable = BaseModel::SEVERITY_TABLE;

    //protected $reportcontents_table = 'audit.report_contents'; // Table name

    // Primary Key
    protected $primaryKey = 'reportid';
    protected $keyType = 'int';
    public $incrementing = true; // Set to `false` if `reportid` is not auto-incrementing

    // Custom timestamps
    const CREATED_AT = 'createdon';
    const UPDATED_AT = null; // Set to null if you don’t have an `updated_at` column

    // Fillable Fields
    protected $fillable = [
        'report_type',
        'report_name',
        'report_contents',
        'statusflag'
    ];

    // Cast JSON field to array when retrieved
    protected $casts = [
        'report_contents' => 'array'
    ];

    public static function fetchpendingparas($sessionuserid)
    {
        return DB::table(self::$InstscheduleMem_Table . ' as scm')
            ->join(self::$Instschedule_Table . ' as sc', 'sc.auditscheduleid', '=', 'scm.auditscheduleid')
            ->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', 'sc.auditplanid')
            ->join(self::$Institution_Table . ' as mi', 'mi.instid', '=', 'ap.instid')
            ->where('sc.auditeeresponse', 'A')
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
    }


    // public static function getpendingparadetails($auditscheduleid, $quartercode, $slipsts, $filterapply, $quarter)
    // {
    //     // Fetch the main query data
    //     $table = self::$TransAuditSlip_Table;
    //     $schteammem = self::$InstscheduleMem_Table;
    //     $userdetails = self::$UserDetails_Table;

    //     $query =  DB::table($table)
    //         ->join(self::$ProcessFlag_Table . ' as p', 'p.processcode', '=', $table . '.processcode')
    //         ->join(self::$MajorObj_Table . ' as m', 'm.mainobjectionid', '=', $table . '.mainobjectionid')
    //         ->leftJoin(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', $table . '.subobjectionid')
    //         ->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', $table . '.auditplanid')
    //         ->join(self::$AuditQuarter_Table . ' as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
    //         ->join(self::$InstscheduleMem_Table . ' as schteam', 'schteam.schteammemberid', '=', $table . '.schteammemberid')
    //         ->leftJoin(self::$UserDetails_Table . ' as dud', 'dud.deptuserid', '=', $table . '.createdby')
    //         ->where($table . '.auditscheduleid', $auditscheduleid)
	//     ->where('ap.auditquartercode', $quarter)
    //         ->select(
    //             'm.objectionename',
    //             'mainslipnumber',
    //             'amtinvolved',
    //             'slipdetails',
    //             'p.processelname',
    //             'p.processcode',
    //             'dud.username as auditorname',
    //             'liability',
    //             'auditslipid',
	// 	'ap.spilloverflag',
    //             'maq.auditquartercode',
    //             DB::raw("CASE WHEN $table.subobjectionid IS NOT NULL THEN s.subobjectionename
    //                                                     ELSE 'N/A'
    //                                                 END AS subobjectionename"), // Conditionally show subobjectionename or 'N/a'
    //             DB::raw("TO_CHAR($table.createdon, 'DD-MM-YYYY hh:MI AM') as createddate"),
    //             DB::raw("CASE WHEN rejoinderstatus = 'Y' THEN 'Yes' ELSE 'No' END AS rejoinderstatus"),
    //            DB::raw("(SELECT dud.username FROM $schteammem AS schteam
    //                                             JOIN $userdetails AS dud ON dud.deptuserid = schteam.userid
    //                                             WHERE schteam.auditscheduleid = $table.auditscheduleid
    //                                             AND schteam.auditteamhead = 'Y'    AND schteam.statusflag = 'Y' LIMIT 1) AS teamheadname")
    //         )
    //         ->orderBy($table . '.auditslipid', 'asc')
    //         ->distinct();

    //     // Apply filters conditionally
    //     if ($filterapply == true) {
    //         if ($slipsts != 'all') {
    //             if ($slipsts == 'P') {
    //                 $query->whereNotIn($table . '.processcode', ['A', 'X']);
    //             } else {
    //                 $query->where($table . '.processcode', $slipsts);
    //             }
    //         }

    //         if ($quartercode != 'all') {
    //             $query->where('maq.auditquartercode', $quartercode);
    //         }
    //     }

    //     // Execute the query to fetch data
    //     $query = $query->get();

    //     // Start building the count query
    //     $countQuery = DB::table($table)->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', $table . '.auditplanid')
    //         ->join(self::$AuditQuarter_Table . ' as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
    //         ->where($table . '.auditscheduleid', $auditscheduleid);

    //     // Apply filter if filterapply is true
    //     if ($filterapply == true) {
    //         if ($quartercode != 'all') {
    //             // Filter by quartercode if it's not 'all'
    //             $countQuery->where('maq.auditquartercode', $quartercode);
    //         }
    //     }

    //     // Add the selectRaw part for counting, with DISTINCT and GROUP BY to avoid duplicates
    //     $countQuery->selectRaw("
    //         COUNT(DISTINCT CASE WHEN $table.processcode IS NOT NULL THEN $table.auditslipid END) as totalslips,
    //         COUNT(DISTINCT CASE WHEN $table.processcode = 'A' THEN $table.auditslipid END) as droppedslips,
    //         COUNT(DISTINCT CASE WHEN $table.processcode = 'X' THEN $table.auditslipid END) as convertedslips,
    //         COUNT(DISTINCT CASE WHEN $table.processcode NOT IN ('A', 'X') THEN $table.auditslipid END) as pendingslips
    //     ")
    //         ->groupBy($table . '.auditscheduleid'); // Group by 'auditscheduleid' to avoid duplicates

    //     // Execute the query and get the first result
    //     $countQuery = $countQuery->first();
    //     $data = json_decode(json_encode($countQuery), true); // Convert stdClass to array

    //     // $data = json_decode($countQuery, true);


    //     // Access the values, ensuring they aren't negative
    //     $totalslips = isset($data['totalslips']) && $data['totalslips'] >= 0 ? $data['totalslips'] : 0;
    //     $droppedslips = isset($data['droppedslips']) && $data['droppedslips'] >= 0 ? $data['droppedslips'] : 0;
    //     $convertedslips = isset($data['convertedslips']) && $data['convertedslips'] >= 0 ? $data['convertedslips'] : 0;
    //     $pendingSlips = isset($data['pendingslips']) && $data['pendingslips'] >= 0 ? $data['pendingslips'] : 0;

    //     // Now, the values are guaranteed to be non-negative



    //     // Prepare the response array
    //     $response = [
    //         'totalslips' => $totalslips,
    //         'droppedslips' => $droppedslips,
    //         'convertedslips' => $convertedslips,
    //         'pendingSlips' => $pendingSlips,
    //         'data' => $query->toArray()
    //     ];

    //     // Return the response
    //     return response()->json($response);
    // }

    public static function getpendingparadetails($auditscheduleid, $quartercode, $slipsts, $filterapply, $quarter = null, $financialyearcode)
    {
        // Fetch the main query data
        $table = self::$TransAuditSlip_Table;
        $schteammem = self::$InstscheduleMem_Table;
        $userdetails = self::$UserDetails_Table;

        $query =  DB::table($table)
            ->join(self::$ProcessFlag_Table . ' as p', 'p.processcode', '=', $table . '.processcode')
            ->join(self::$MajorObj_Table . ' as m', 'm.mainobjectionid', '=', $table . '.mainobjectionid')
            ->leftJoin(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', $table . '.subobjectionid')
            ->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', $table . '.auditplanid')
            ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'ap.planmappingid')
            ->join(self::$AuditQuarter_Table . ' as maq', 'maq.auditquartercode', '=', 'apm.auditquartercode')
            ->join(self::$InstscheduleMem_Table . ' as schteam', 'schteam.schteammemberid', '=', $table . '.schteammemberid')
            ->leftJoin(self::$UserDetails_Table . ' as dud', 'dud.deptuserid', '=', $table . '.createdby')
            ->where($table . '.auditscheduleid', $auditscheduleid)
            ->when(!empty($quartercode), function ($query) use ($quartercode) {
                return $query->where('apm.group_key', $quartercode);
            })

            ->when($financialyearcode, function ($query) use ($financialyearcode) {
                return $query->where('apm.financialyearcode', $financialyearcode);
            })
            ->select(
                'm.objectionename',
                'mainslipnumber',
                'amtinvolved',
                'slipdetails',
                'p.processelname',
                'p.processcode',
                'dud.username as auditorname',
                'liability',
                'auditslipid',
		'ap.spilloverflag',
                'apm.planname as auditquartercode',
                DB::raw("CASE WHEN $table.subobjectionid IS NOT NULL THEN s.subobjectionename
                                                        ELSE 'N/A'
                                                    END AS subobjectionename"), // Conditionally show subobjectionename or 'N/a'
                DB::raw("TO_CHAR($table.createdon, 'DD-MM-YYYY hh:MI AM') as createddate"),
                DB::raw("CASE WHEN rejoinderstatus = 'Y' THEN 'Yes' ELSE 'No' END AS rejoinderstatus"),
               DB::raw("(SELECT dud.username FROM $schteammem AS schteam
                                                JOIN $userdetails AS dud ON dud.deptuserid = schteam.userid
                                                WHERE schteam.auditscheduleid = $table.auditscheduleid
                                                AND schteam.auditteamhead = 'Y'    AND schteam.statusflag = 'Y' LIMIT 1) AS teamheadname")
            )
            ->orderBy($table . '.auditslipid', 'asc')
            ->distinct();

        // Apply filters conditionally
        if ($filterapply == true) {
            if ($slipsts != 'all') {
                if ($slipsts == 'P') {
                    $query->whereNotIn($table . '.processcode', ['A', 'X']);
                } else {
                    $query->where($table . '.processcode', $slipsts);
                }
            }

            if ( !empty($quartercode)) {
                $query->where('apm.group_key', $quartercode);
            }
        }

        // Execute the query to fetch data
        $query = $query->get();

        // Start building the count query
        $countQuery = DB::table($table)->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', $table . '.auditplanid')
            ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'ap.planmappingid')
            ->join(self::$AuditQuarter_Table . ' as maq', 'maq.auditquartercode', '=', 'apm.auditquartercode')
            ->where($table . '.auditscheduleid', $auditscheduleid);

        // Apply filter if filterapply is true
        if ($filterapply == true) {
            if ( !empty($quartercode)) {
                // Filter by quartercode if it's not 'all'
                $countQuery->where('apm.group_key', $quartercode);
            }
        }

        // Add the selectRaw part for counting, with DISTINCT and GROUP BY to avoid duplicates
        $countQuery->selectRaw("
            COUNT(DISTINCT CASE WHEN $table.processcode IS NOT NULL THEN $table.auditslipid END) as totalslips,
            COUNT(DISTINCT CASE WHEN $table.processcode = 'A' THEN $table.auditslipid END) as droppedslips,
            COUNT(DISTINCT CASE WHEN $table.processcode = 'X' THEN $table.auditslipid END) as convertedslips,
            COUNT(DISTINCT CASE WHEN $table.processcode NOT IN ('A', 'X') THEN $table.auditslipid END) as pendingslips
        ")
            ->groupBy($table . '.auditscheduleid'); // Group by 'auditscheduleid' to avoid duplicates

        // Execute the query and get the first result
        $countQuery = $countQuery->first();
        $data = json_decode(json_encode($countQuery), true); // Convert stdClass to array

        // $data = json_decode($countQuery, true);


        // Access the values, ensuring they aren't negative
        $totalslips = isset($data['totalslips']) && $data['totalslips'] >= 0 ? $data['totalslips'] : 0;
        $droppedslips = isset($data['droppedslips']) && $data['droppedslips'] >= 0 ? $data['droppedslips'] : 0;
        $convertedslips = isset($data['convertedslips']) && $data['convertedslips'] >= 0 ? $data['convertedslips'] : 0;
        $pendingSlips = isset($data['pendingslips']) && $data['pendingslips'] >= 0 ? $data['pendingslips'] : 0;

        // Now, the values are guaranteed to be non-negative



        // Prepare the response array
        $response = [
            'totalslips' => $totalslips,
            'droppedslips' => $droppedslips,
            'convertedslips' => $convertedslips,
            'pendingSlips' => $pendingSlips,
            'data' => $query->toArray()
        ];

        // Return the response
        return response()->json($response);
    }


    public static function getSlipDetails($slipId)
    {
        $table = self::$TransAuditSlip_Table;
        // Simulate fetching slip details from the database (replace this with actual logic)
        $slipDetails = DB::table($table)
            ->join(self::$ProcessFlag_Table . ' as p', 'p.processcode', '=', $table . '.processcode')
            ->join(self::$MajorObj_Table . ' as m', 'm.mainobjectionid', '=', $table . '.mainobjectionid')
            ->leftjoin(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', $table . '.subobjectionid')
            ->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', $table . '.auditplanid')
            ->join(self::$AuditQuarter_Table . ' as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
            ->join(self::$AuditeeUserDetail_Table . ' as auditee', 'ap.instid', '=', 'auditee.instid')
            ->join(self::$Institution_Table . '  as inst', 'ap.instid', '=', 'inst.instid')
            ->join(self::$InstscheduleMem_Table . ' as schteam', 'schteam.schteammemberid', '=', $table . '.schteammemberid')
            ->join(self::$UserDetails_Table . ' as dud', 'dud.deptuserid', '=', $table . '.createdby')
            ->join(self::$Designation_Table . ' as desig', 'desig.desigcode', '=', 'dud.desigcode')

            ->select(
                $table . '.auditscheduleid',
                'm.objectionename',
                DB::raw("CASE
                        WHEN $table.subobjectionid IS NOT NULL THEN s.subobjectionename
                        ELSE 'N/A'
                     END AS subobjectionename"),
                $table . '.mainslipnumber',
                $table . '.slipdetails',
                $table . '.amtinvolved',
                'dud.username as auditorname',
                'p.processelname',
                DB::raw("CASE WHEN $table.liability = 'Y' THEN 'Yes' ELSE 'No' END AS liability"), // Transform in query
                DB::raw("CASE WHEN $table.severitycode = 'M' THEN 'Medium'
                              WHEN $table.severitycode = 'H' THEN 'High'
                              WHEN $table.severitycode = 'L' THEN 'Low'
                              ELSE 'Unknown'
                        END AS severity"),
            )
            ->where($table . '.auditslipid', $slipId)
            ->first();

        if (!$slipDetails) {
            return response()->json(['status' => 'error', 'message' => 'Slip not found.'], 404);
        }

        $LiabilityDetails = DB::table('audit.liability as liability')
            ->where('liability.auditslipid', $slipId)
            ->get();


        // Get the current user
        /* $currentUser = auth()->user(); // Assuming you're using Laravel's authentication

        // Check if the current user is the same as the 'createdby' user and has the 'teamhead' flag set to 'Y'
        $isTeamMember = DB::table('audit.inst_schteammember')
        ->where('userid', $slipDetails->createdby) // Use the schteamid from the slip details
        ->where('auditteamhead', 'N') // Check for team head flag
        ->exists();*/


        // Get the auditscheduleid from slipDetails
        $auditScheduleId = $slipDetails->auditscheduleid;


        $InstscheduleMem_Table = self::$InstscheduleMem_Table;

        // Check if the current user is a team member with teamhead flag 'Y'
        $TeamHeadget = DB::table($InstscheduleMem_Table)
            ->join(self::$UserDetails_Table . ' as dud', 'dud.deptuserid', '=', $InstscheduleMem_Table . '.userid')
            ->select('dud.username as teamheadname')
            ->where($InstscheduleMem_Table . '.auditscheduleid', $auditScheduleId) // Use auditscheduleid for the team member check
            ->where($InstscheduleMem_Table . '.auditteamhead', 'Y') // Ensure it's a team head
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
            'teamheadname' => $TeamHeadget->teamheadname,
            'liability' => $LiabilityDetails
        ]);
        // }

    }

  public static function getSlipDetailsHistory($slipId)
    {
        $table = self::$SlipHistroyDetails_Table;

        // Get slip details with all joins
        $slipDetails = DB::table($table)
            ->leftJoin(self::$ProcessFlag_Table.' as p', 'p.processcode', '=', $table.'.processcode')
            ->leftJoin(self::$MajorObj_Table.' as m', 'm.mainobjectionid', '=', $table.'.mainobjectionid')
            ->leftJoin(self::$SubObj_Table.' as s', function ($join) use ($table) {
                $join->on('s.subobjectionid', '=', $table.'.subobjectionid')
                    ->whereNotNull($table.'.subobjectionid');
            })
            ->leftJoin(self::$AuditPlan_Table.' as ap', 'ap.auditplanid', '=', $table.'.auditplanid')
            ->leftJoin(self::$AuditeeUserDetail_Table.' as auditee', 'ap.instid', '=', 'auditee.instid')
            ->leftJoin(self::$Institution_Table.' as inst', 'ap.instid', '=', 'inst.instid')
            ->leftJoin(self::$InstscheduleMem_Table.' as schteam', 'schteam.schteammemberid', '=', $table.'.schteammemberid')
            ->leftJoin(self::$UserDetails_Table.' as dud_forwardedby', 'dud_forwardedby.deptuserid', '=', $table.'.forwardedby')
            ->leftJoin(self::$AuditeeUserDetail_Table.' as aud_forwardedby', 'aud_forwardedby.auditeeuserid', '=', $table.'.forwardedby')
            ->select(
                $table.'.auditscheduleid',
                $table.'.transhistoryid',
                $table.'.auditslipid',
                'm.objectionename',
                DB::raw("COALESCE(s.subobjectionename, 'N/A') AS subobjectionename"),
                $table.'.mainslipnumber',
                $table.'.slipdetails',
                $table.'.amtinvolved',
                $table.'.remarks',
                $table.'.forwardedbyusertypecode',
                $table.'.processcode',
                $table.'.rejoinderstatus',
                $table.'.rejoindercycle',
                $table.'.forwardedby',
                'p.processelname',
                DB::raw("CASE WHEN $table.liability = 'Y' THEN 'Yes' ELSE 'No' END AS liability"),
                DB::raw("CASE
                    WHEN $table.severityid = 'M' THEN 'Medium'
                    WHEN $table.severityid = 'H' THEN 'High'
                    WHEN $table.severityid = 'L' THEN 'Low'
                    ELSE 'Unknown'
                END AS severity"),
                DB::raw("TO_CHAR($table.forwardedon, 'DD-MM-YYYY HH12:MI AM') as forwardedon"),
                DB::raw("
                CASE
                    WHEN $table.forwardedbyusertypecode = 'I'
                    THEN CONCAT(aud_forwardedby.username, ' (Auditee)')
                    ELSE CONCAT(dud_forwardedby.username, ' (Auditor)')
                END AS forwardedby_username
            ")
            )
            ->where($table.'.auditslipid', $slipId)
            ->orderBy($table.'.transhistoryid', 'asc')
            ->get();

        // Get files for each history transaction with proper filtering
        $historyFiles = [];
        foreach ($slipDetails as $history) {
            $filesQuery = DB::table(self::$slipFileUpload_Table.' as slip')
                ->join(self::$FileUploadDetail_Table.' as file_upload', 'file_upload.fileuploadid', '=', 'slip.fileuploadid')
                ->leftJoin(self::$UserDetails_Table.' as auditor_user', function($join) {
                    $join->on('auditor_user.deptuserid', '=', 'file_upload.uploadedby')
                        ->where('file_upload.usertypecode', '=', 'A');
                })
                ->leftJoin(self::$AuditeeUserDetail_Table.' as auditee_user', function($join) {
                    $join->on('auditee_user.auditeeuserid', '=', 'file_upload.uploadedby')
                        ->where('file_upload.usertypecode', '=', 'I');
                })
                ->select(
                    'slip.auditslipid',
                    'file_upload.fileuploadid',
                    'file_upload.filename',
                    'file_upload.filepath',
                    'file_upload.filesize',
                    'file_upload.uploadedby',
                    'file_upload.usertypecode',
                    'slip.processcode',
                    'slip.rejoinderstatus',
                    'slip.rejoindercycle',
                    DB::raw("TO_CHAR(file_upload.uploadedon, 'DD-MM-YYYY HH12:MI AM') as uploaded_date"),
                    DB::raw("
                        CASE
                            WHEN file_upload.usertypecode = 'A' THEN auditor_user.username
                            WHEN file_upload.usertypecode = 'I' THEN auditee_user.username
                            ELSE 'Unknown'
                        END AS uploaded_by_username
                    "),
                    DB::raw("
                        CASE
                            WHEN file_upload.usertypecode = 'A' THEN 'Auditor'
                            WHEN file_upload.usertypecode = 'I' THEN 'Auditee'
                            ELSE 'Unknown'
                        END AS uploaded_by_role
                    ")
                )
                ->where('slip.auditslipid', $slipId);

            // Apply the same complex filtering logic from getslipdetails
            $filesQuery->where(function($query) use ($history) {
                // For processcode 'F' - Final
                if ($history->processcode === 'F') {
                    $query->where(function($q) use ($history) {
                        $q->where('slip.processcode', 'F')
                        ->orWhere('slip.processcode', 'T');
                    });

                    // Handle rejoinder logic for processcode 'F'
                    if ($history->rejoinderstatus === 'Y') {
                        $query->where(function($q) use ($history) {
                            $q->where(function($q2) use ($history) {
                                $q2->where('slip.rejoinderstatus', $history->rejoinderstatus)
                                ->where('slip.rejoindercycle', $history->rejoindercycle);
                            })->orWhere(function($q2) {
                                $q2->whereNull('slip.rejoinderstatus')
                                ->whereNull('slip.rejoindercycle');
                            });
                        });
                    }
                }
                // For processcode 'A', 'X' - Approved, Rejected
                elseif (in_array($history->processcode, ['A', 'X'])) {
                    $query->whereIn('slip.processcode', ['A', 'X', 'R'])
                        ->where('file_upload.usertypecode', 'A');
                }
                // For processcode 'T', 'R', 'M' - Team, Rejoinder, Modified
                elseif (in_array($history->processcode, ['T', 'R', 'M'])) {
                    $query->where('slip.processcode', $history->processcode);
                }

                // Handle rejoinder status matching
                if ($history->rejoinderstatus !== null || $history->rejoindercycle !== null) {
                    $query->where(function($q) use ($history) {
                        $q->where(function($q2) use ($history) {
                            $q2->where('slip.rejoinderstatus', $history->rejoinderstatus)
                            ->where('slip.rejoindercycle', $history->rejoindercycle);
                        })->orWhere(function($q2) {
                            $q2->whereNull('slip.rejoinderstatus')
                            ->whereNull('slip.rejoindercycle');
                        });
                    });
                } else {
                    $query->whereNull('slip.rejoinderstatus')
                        ->whereNull('slip.rejoindercycle');
                }

                // Special case for rejoinder in processcode 'F'
                if ($history->processcode === 'F' && $history->rejoinderstatus === 'Y' && $history->rejoindercycle === '1') {
                    $query->orWhere(function($q) {
                        $q->where('slip.processcode', 'R')
                        ->whereNull('slip.rejoinderstatus')
                        ->whereNull('slip.rejoindercycle')
                        ->where('file_upload.usertypecode', 'A');
                    });
                }
            });

            $files = $filesQuery->get();
            $historyFiles[$history->transhistoryid] = $files;
        }

        // Get all files for this slip (for backward compatibility)
        $allFiles = DB::table(self::$slipFileUpload_Table.' as slip')
            ->join(self::$FileUploadDetail_Table.' as file_upload', 'file_upload.fileuploadid', '=', 'slip.fileuploadid')
            ->leftJoin(self::$UserDetails_Table.' as auditor_user', function($join) {
                $join->on('auditor_user.deptuserid', '=', 'file_upload.uploadedby')
                    ->where('file_upload.usertypecode', '=', 'A');
            })
            ->leftJoin(self::$AuditeeUserDetail_Table.' as auditee_user', function($join) {
                $join->on('auditee_user.auditeeuserid', '=', 'file_upload.uploadedby')
                    ->where('file_upload.usertypecode', '=', 'I');
            })
            ->select(
                'slip.auditslipid',
                'file_upload.fileuploadid',
                'file_upload.filename',
                'file_upload.filepath',
                'file_upload.filesize',
                'file_upload.uploadedby',
                'file_upload.usertypecode',
                'slip.processcode',
                'slip.rejoinderstatus',
                'slip.rejoindercycle',
                DB::raw("TO_CHAR(file_upload.uploadedon, 'DD-MM-YYYY HH12:MI AM') as uploaded_date"),
                DB::raw("
                    CASE
                        WHEN file_upload.usertypecode = 'A' THEN auditor_user.username
                        WHEN file_upload.usertypecode = 'I' THEN auditee_user.username
                        ELSE 'Unknown'
                    END AS uploaded_by_username
                "),
                DB::raw("
                    CASE
                        WHEN file_upload.usertypecode = 'A' THEN 'Auditor'
                        WHEN file_upload.usertypecode = 'I' THEN 'Auditee'
                        ELSE 'Unknown'
                    END AS uploaded_by_role
                ")
            )
            ->where('slip.auditslipid', $slipId)
            ->get();

        $LiabilityDetails = DB::table('audit.liability as liability')
            ->where('liability.auditslipid', $slipId)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $slipDetails,
            'liability' => $LiabilityDetails,
            'all_files' => $allFiles,
            'history_files' => $historyFiles, // Files grouped by transhistoryid
        ]);
    }

    public static function getSlipHistoryDetails($slipId)
    {
        $auditSlips = DB::table(self::$SlipHistroyDetails_Table.' as hist')
            ->join(self::$TransAuditSlip_Table.' as trans_auditslip', 'trans_auditslip.auditslipid', '=', 'hist.auditslipid')
            ->join(self::$ProcessFlag_Table.' as p', 'p.processcode', '=', 'hist.processcode')
            ->join(self::$InstscheduleMem_Table.' as schteam', 'schteam.schteammemberid', '=', 'trans_auditslip.schteammemberid')
            ->join(self::$UserDetails_Table.' as dud_sch', 'dud_sch.deptuserid', '=', 'schteam.userid') // Team member details
            ->leftJoin(self::$UserDetails_Table.' as dud_forwardedby', 'dud_forwardedby.deptuserid', '=', 'hist.forwardedby') // Forwarded by (Auditor)
            ->leftJoin(self::$UserDetails_Table.' as dud_forwardedto', 'dud_forwardedto.deptuserid', '=', 'hist.forwardedto') // Forwarded to (Auditor)
            ->leftJoin(self::$AuditeeUserDetail_Table.' as aud_forwardedby', 'aud_forwardedby.auditeeuserid', '=', 'hist.forwardedby') // Forwarded by (Auditee)
            ->leftJoin(self::$AuditeeUserDetail_Table.' as aud_forwardedto', 'aud_forwardedto.auditeeuserid', '=', 'hist.forwardedto') // Forwarded to (Auditee)
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
                            THEN COALESCE(
                                CASE
                                    WHEN aud_forwardedby.username IS NOT NULL
                                        THEN CONCAT(aud_forwardedby.username, ' (Auditee)')
                                    ELSE '-'
                                END,
                            '-')
                        ELSE COALESCE(
                                CASE
                                    WHEN dud_forwardedby.username IS NOT NULL
                                        THEN CONCAT(dud_forwardedby.username, ' (Auditor)')
                                    ELSE '-'
                                END,
                            '-')
                    END AS forwardedby_username
                "),
                DB::raw("
                    CASE
                        WHEN hist.forwardedtousertypecode = 'I'
                            THEN COALESCE(
                                CASE
                                    WHEN aud_forwardedto.username IS NOT NULL
                                        THEN CONCAT(aud_forwardedto.username, ' (Auditee)')
                                    ELSE '-'
                                END,
                            '-')
                        ELSE COALESCE(
                                CASE
                                    WHEN dud_forwardedto.username IS NOT NULL
                                        THEN CONCAT(dud_forwardedto.username, ' (Auditor)')
                                    ELSE '-'
                                END,
                            '-')
                    END AS forwardedto_username
                ")


            )
            ->where('hist.auditslipid', $slipId)
            ->orderBy('hist.transhistoryid', 'asc') // Order by transhistoryid descending
            ->get();

        return response()->json(['status' => 'success', 'data' => $auditSlips]);
    }


    public static function getInspectionDetailHistory($inspectionid)
    {
        $table = self::$SlipHistroyDetails_Table;

        // Get slip details with all joins
        $slipDetails = DB::table($table)
            ->leftJoin(self::$ProcessFlag_Table.' as p', 'p.processcode', '=', $table.'.processcode')
            ->leftJoin(self::$MajorObj_Table.' as m', 'm.mainobjectionid', '=', $table.'.mainobjectionid')
            ->leftJoin(self::$SubObj_Table.' as s', function ($join) use ($table) {
                $join->on('s.subobjectionid', '=', $table.'.subobjectionid')
                    ->whereNotNull($table.'.subobjectionid');
            })
            ->leftJoin(self::$AuditPlan_Table.' as ap', 'ap.auditplanid', '=', $table.'.auditplanid')
            ->leftJoin(self::$AuditeeUserDetail_Table.' as auditee', 'ap.instid', '=', 'auditee.instid')
            ->leftJoin(self::$Institution_Table.' as inst', 'ap.instid', '=', 'inst.instid')
            ->leftJoin(self::$InstscheduleMem_Table.' as schteam', 'schteam.schteammemberid', '=', $table.'.schteammemberid')
            ->leftJoin(self::$UserDetails_Table.' as dud_forwardedby', 'dud_forwardedby.deptuserid', '=', $table.'.forwardedby')
            ->leftJoin(self::$AuditeeUserDetail_Table.' as aud_forwardedby', 'aud_forwardedby.auditeeuserid', '=', $table.'.forwardedby')
            ->select(
                $table.'.auditscheduleid',
                $table.'.transhistoryid',
                'm.objectionename',
                DB::raw("COALESCE(s.subobjectionename, 'N/A') AS subobjectionename"),
                $table.'.mainslipnumber',
                $table.'.slipdetails',
                $table.'.amtinvolved',
                $table.'.remarks',
                $table.'.forwardedbyusertypecode',
                'p.processelname',
                DB::raw("CASE WHEN $table.liability = 'Y' THEN 'Yes' ELSE 'No' END AS liability"),
                DB::raw("CASE
                    WHEN $table.severityid = 'M' THEN 'Medium'
                    WHEN $table.severityid = 'H' THEN 'High'
                    WHEN $table.severityid = 'L' THEN 'Low'
                    ELSE 'Unknown'
                END AS severity"),
                DB::raw("TO_CHAR($table.forwardedon, 'DD-MM-YYYY HH12:MI AM') as forwardedon"),
                DB::raw("
                CASE
                    WHEN $table.forwardedbyusertypecode = 'I'
                    THEN CONCAT(aud_forwardedby.username, ' (Auditee)')
                    ELSE CONCAT(dud_forwardedby.username, ' (Auditor)')
                END AS forwardedby_username
            ")
            )
            ->where($table.'.auditslipid', $slipId)
            ->orderBy($table.'.transhistoryid', 'asc')
            ->get();

        // Get all files for this slip
        $allFiles = DB::table(self::$slipFileUpload_Table.' as slip')
            ->join(self::$FileUploadDetail_Table.' as file_upload', 'file_upload.fileuploadid', '=', 'slip.fileuploadid')
            ->select(
                'slip.auditslipid',
                'file_upload.fileuploadid',
                'file_upload.filename',
                'file_upload.filepath',
                DB::raw("TO_CHAR(file_upload.uploadedon, 'DD-MM-YYYY HH12:MI AM') as uploaded_date")
            )
            ->where('slip.auditslipid', $slipId)
            ->get();

        $LiabilityDetails = DB::table('audit.liability as liability')
            ->where('liability.auditslipid', $slipId)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $slipDetails,
            'liability' => $LiabilityDetails,
            'all_files' => $allFiles,
        ]);
    }

    public static function getInspectionHistoryDetails($inspectionid)
    {
        try {
            // Get Inspection History with designation names
            $inspections = DB::table(self::$Inspection_History_Table.' as hist')
                ->join(self::$ProcessFlag_Table.' as p', 'p.processcode', '=', 'hist.processcode')
                ->leftJoin(self::$UserDetails_Table.' as dud_forwardedby', 'dud_forwardedby.deptuserid', '=', 'hist.forwardedby')
                ->leftJoin(self::$UserDetails_Table.' as dud_forwardedto', 'dud_forwardedto.deptuserid', '=', 'hist.forwardedto')
                ->leftJoin(self::$Designation_Table.' as desig_by', 'desig_by.desigcode', '=', 'dud_forwardedby.desigcode')
                ->leftJoin(self::$Designation_Table.' as desig_to', 'desig_to.desigcode', '=', 'dud_forwardedto.desigcode')
                ->select(
                    'hist.forwardedby',
                    'hist.forwardedto',
                    'hist.transhistoryid',
                    'p.processelname as status_name',
                    'hist.processcode',
                    'hist.rejoinderstatus',
                    'hist.rejoindercycle',
                    'hist.inspectcheckpoints',
                    'hist.inscheckpointremarks',
                    'hist.inscheckpointheadremarks',
                    'hist.slipremarks',
                    'hist.remarks',
                    'desig_by.desigelname as forwardedby_designation',
                    'desig_to.desigelname as forwardedto_designation',
                    DB::raw("TO_CHAR(hist.forwardedon, 'DD-MM-YYYY HH24:MI AM') as forwardedon"),
                    DB::raw("
                    CASE
                        WHEN dud_forwardedby.username IS NOT NULL THEN
                            CONCAT(
                                dud_forwardedby.username,
                                ' (',
                                COALESCE(desig_by.desigesname, '-'),
                                ')'
                            )
                        ELSE 'N/A'
                    END as forwardedby_username
                "),
                    DB::raw("
                    CASE
                        WHEN dud_forwardedto.username IS NOT NULL THEN
                            CONCAT(
                                dud_forwardedto.username,
                                ' (',
                                COALESCE(desig_to.desigesname, '-'),
                                ')'
                            )
                        ELSE 'N/A'
                    END as forwardedto_username
                ")
                )
                ->where('hist.auditinspectionid', $inspectionid)
                ->orderBy('hist.transhistoryid', 'asc')
                ->get();

            // Process checkpoints for each history record
            $historyWithCheckpoints = $inspections->map(function ($history) {
                $checkpoints = [];

                if ($history->inspectcheckpoints) {
                    $checkpointData = json_decode($history->inspectcheckpoints, true);
                    $remarkData = json_decode($history->inscheckpointremarks ?? '{}', true);
                    $headRemarkData = json_decode($history->inscheckpointheadremarks ?? '{}', true);

                    if (! empty($checkpointData)) {
                        $checkpointIds = array_keys($checkpointData);
                        $checkpointDetails = DB::table('audit.mst_auditinspection')
                            ->whereIn('aifid', $checkpointIds)
                            ->get()
                            ->keyBy('aifid');

                        foreach ($checkpointData as $checkpointId => $status) {
                            $detail = $checkpointDetails[$checkpointId] ?? null;

                            if ($detail) {
                                $checkpoints[] = [
                                    'heading_en' => $detail->heading_en,
                                    'heading_ta' => $detail->heading_ta,
                                    'checkpoint_en' => $detail->checkpoint_en,
                                    'checkpoint_ta' => $detail->checkpoint_ta,
                                    'objectiontype' => $detail->objectiontype,
                                    'checkpoint_status' => $status,
                                    'checkpoint_remarks' => $remarkData[$checkpointId] ?? '-',
                                    'head_remarks' => $headRemarkData[$checkpointId] ?? '-',
                                ];
                            }
                        }
                    }
                }

                $history->checkpoints = $checkpoints;

                $history->slipremarks_content = self::extractValidContent($history->slipremarks);

                $history->remarks_content = self::extractValidContent($history->remarks);

                unset($history->inspectcheckpoints);
                unset($history->inscheckpointremarks);
                unset($history->inscheckpointheadremarks);
                unset($history->slipremarks);
                unset($history->remarks);

                return $history;
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'history' => $historyWithCheckpoints,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching inspection details: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch inspection details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private static function extractValidContent($remarks)
    {
        if (empty($remarks)) {
            return null;
        }

        $trimmed = trim($remarks);

        // Directly check for the problematic patterns first
        $invalidPatterns = [
            '{"content":null}',
            '{"content":null}',
            '{"content":""}',
            '{"content":""}',
            'null',
            '""',
            "''",
            '{}',
            '[]',
        ];

        if (in_array($trimmed, $invalidPatterns)) {
            return null;
        }

        // If it's a JSON string, try to decode it
        if (is_string($trimmed) && self::isJson($trimmed)) {
            $decoded = json_decode($trimmed, true);

            // Check if decoding was successful
            if (json_last_error() !== JSON_ERROR_NONE) {
                return self::validateRawContent($trimmed);
            }

            // SPECIFICALLY REJECT {"content":null}
            if (isset($decoded['content']) && $decoded['content'] === null) {
                return null;
            }

            // SPECIFICALLY REJECT {"content":""}
            if (isset($decoded['content']) && $decoded['content'] === '') {
                return null;
            }

            // Only return content if it's a non-empty string
            if (isset($decoded['content']) &&
                is_string($decoded['content']) &&
                ! empty(trim($decoded['content']))) {
                return trim($decoded['content']);
            }

            // If we have content key but it's invalid, return null
            if (isset($decoded['content'])) {
                return null;
            }

            // If JSON decoding didn't give us valid content, try the raw string
            return self::validateRawContent($trimmed);
        }

        // For non-JSON strings, validate directly
        return self::validateRawContent($trimmed);
    }

    /**
     * Check if string is JSON
     */
    private static function isJson($string)
    {
        if (! is_string($string)) {
            return false;
        }

        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Validate raw content string
     */
    private static function validateRawContent($content)
    {
        if (empty($content)) {
            return null;
        }

        $trimmed = trim($content);

        // Reject empty strings and known invalid values
        if ($trimmed === '' ||
            $trimmed === 'null' ||
            $trimmed === '""' ||
            $trimmed === "''" ||
            $trimmed === '{"content":null}' ||
            $trimmed === '{"content":null}' ||
            $trimmed === '{"content":""}' ||
            $trimmed === '{"content":""}') {
            return null;
        }

        return $trimmed;
    }


   
 public static function getDFinancialyear(){
        $query = DB::table(self::$AuditPlan_Table . ' as plan')
            ->join(self::$auditplanmappingtable . ' as apm', 'apm.planmappingid', '=', 'plan.planmappingid')
            ->join('audit.mst_financialyear as year', 'year.financialyearcode', '=', 'apm.financialyearcode')
            ->select('year.financialyearcode', 'year.financialyear', 'year.financialyearid')
            ->where('year.statusflag', 'Y')
            ->wherein('apm.statusflag', ['F', 'P', 'Y'])
            ->distinct()
            ->orderBy('year.financialyearcode', 'desc')
            ->get();

        return $query;
    }


public static function getQuarter(){
    $query = DB::table(self::$AuditPlan_Table . ' as plan')
        ->select('plan.auditquartercode')
        ->distinct()
        ->orderby('plan.auditquartercode')
        ->get();

    return $query;
}




public static function getDept()
{
$query = DB::table(self::$Dept_Table)

->where('statusflag', 'Y')
->select('deptcode', 'deptelname', 'depttlname', 'deptesname', 'depttsname')
->orderby('deptcode','ASC')
->get();

return $query;
}


public static function getRegion()
{
$query = DB::table(self::$auditeinstmap . ' as inst')
->Join(self::$regionTable . ' as reg', 'reg.regioncode', '=', 'inst.regioncode')
->where('inst.statusflag', 'Y')
->select('reg.regioncode', 'reg.regionename', 'reg.regiontname')
->distinct()

->get();

return $query;
}


public static function getDistrict()
{
$query = DB::table(self::$auditeinstmap . ' as inst')
->Join(self::$District_Table . ' as dist', 'dist.distcode', '=', 'inst.distcode')
->where('inst.statusflag', 'Y')
->select('dist.distcode', 'dist.distename', 'dist.disttname')
->distinct()
->get();

return $query;
}





         public static function commondeptfetch()
          {
              return DB::table(self::$Dept_Table . ' as dept')
                  ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname') // Select required columns
                  ->where('dept.statusflag', '=', 'Y') // Use the correct table alias for `statusflag`
                  ->orderBy('dept.deptelname', 'ASC')
                  ->get();
          }

          public static function regionfetch()
          {
            $table = self::$regionTable;
     
            return DB::table($table . ' as reg')

                ->select('reg.regioncode', 'reg.regionename','reg.regiontname')
                ->distinct()
                ->where('reg.statusflag', 'Y')
                ->orderBy('reg.regionename', 'Asc')
                ->get();
          }

        public static function districtfetch()
        {
        $table = self::$District_Table;
    
        return DB::table($table . ' as dis')
            ->select('dis.distename', 'dis.distcode','dis.disttname')
            ->distinct()
            ->where('dis.statusflag', 'Y')
            ->get();
        }
    
     
        public static function getRegionsByDept($deptcode)
        {
            $table = self::$Institution_Table;
        
            $query = DB::table($table . ' as ins')
                ->join(self::$regionTable . ' as reg', 'ins.regioncode', '=', 'reg.regioncode')
                ->select('reg.regioncode', 'reg.regionename', 'reg.regiontname')
                ->distinct()
                ->where('ins.statusflag', 'Y')
                ->orderBy('reg.regionename', 'Asc');
        
            if (is_array($deptcode)) {
                $query->whereIn('ins.deptcode', $deptcode);
            } else {
                $query->where('ins.deptcode', $deptcode);
            }
        
            return $query->get();
        }
        
     
        public static function getdistrictByregion($regioncode, $deptcode)
        {
            $table = self::$Institution_Table;
        
            $query = DB::table($table . ' as ins')
                ->join(self::$District_Table . ' as dis', 'ins.distcode', '=', 'dis.distcode')
                ->select('dis.distename', 'dis.distcode', 'dis.disttname')
                ->distinct()
                ->where('ins.statusflag', 'Y');
        
            if (is_array($deptcode)) {
                $query->whereIn('ins.deptcode', $deptcode);
            } else {
                $query->where('ins.deptcode', $deptcode);
            }
        
            if (is_array($regioncode)) {
                $query->whereIn('ins.regioncode', $regioncode);
            } else {
                $query->where('ins.regioncode', $regioncode);
            }
        
            return $query->get();
        }
        
public static function fetch_deptbaseddata(
?array $audityearcode = null,   
?array $deptcode = null,
?array $regioncode = null,
?array $distcode = null,
string $getval,
?string $financialyear = null,
?array $auditquarter = null,
string $formname,
?string $actioncode = 'A',
?array $instmappingcode = null,
?int $maxslip = null,
string $logindate,
string $reportstatuscode,
?array $catcode = null,
?array $subcatcode = null,
?array $reportstatus,
?array $parareportstatus


) {


    $query = DB::table(self::$auditeinstmap . ' as inst')
    ->where('inst.statusflag', 'Y')
    ->when($deptcode, function ($query) use ($deptcode) {
        if (is_array($deptcode)) {
            // Multiple departments → use IN
            $query->whereIn('inst.deptcode', $deptcode);
        } else {
            // Single department → use normal where
            $query->where('inst.deptcode', $deptcode);
        }
    });

switch ($getval) {

case 'region':
$query->join(self::$regionTable . ' as re', 're.regioncode', '=', "inst.regioncode")
->select("inst.regioncode", 're.regionename', 're.regiontname')
->distinct();
$query->orderBy('re.regionename', 'ASC');

break;

case 'category':
$query->join(self::$MstAuditeeInsCategory_Table . ' as cat', 'inst.deptcode', '=', "cat.deptcode")
->select("cat.catcode", 'cat.if_subcategory', 'cat.catename', 'cat.catename')
->where('cat.statusflag', 'Y')
->when($deptcode, function ($query) use ($deptcode) {
    if (!is_array($deptcode)) {
        $deptcode = explode(',', $deptcode);
    }
    $query->whereIn('inst.deptcode', $deptcode);
})
->orderBy('cat.catename', 'asc')
->distinct();
break;


case 'catcode':
    $query->join(self::$MstAuditeeInsCategory_Table . ' as cat', 'inst.deptcode', '=', "cat.deptcode")
    ->select("cat.catcode", 'cat.if_subcategory', 'cat.catename', 'cat.catename')
    ->where('cat.statusflag', 'Y')
    ->when($deptcode, function ($query) use ($deptcode) {
        if (!is_array($deptcode)) {
            $deptcode = explode(',', $deptcode);
        }
        $query->whereIn('inst.deptcode', $deptcode);
    })
    ->orderBy('cat.catename', 'asc')
    ->distinct();
    break;



    case 'subcatcode':

        $table = self::$MstAuditeeInsCategory_Table;
    
        $query = DB::table($table . ' as aud')
            ->join('audit.mst_auditeeins_subcategory as sub', 'aud.catcode', '=', 'sub.catcode')
            ->select(
                'sub.subcatename',
                'sub.subcattname',
                'sub.auditeeins_subcategoryid',
                'aud.if_subcategory',
                'aud.catcode',
                'aud.catename',
                'aud.cattname'
            )
            ->where('aud.if_subcategory', 'Y')
            ->when($catcode, function ($query) use ($catcode) {
                if (!is_array($catcode)) {
                    $catcode = explode(',', $catcode);
                }
                $query->whereIn('sub.catcode', $catcode);
            })
            ->orderBy('sub.subcatename', 'asc')
            ->distinct();
    
        break;
    
 case 'auditquarter':
       // dd($financialyear);
        $query->join('audit.auditplanmapping as planmap', 'inst.deptcode', '=', 'planmap.deptcode')

            ->select('planmap.planname', 'planmap.auditquartercode','planmap.planmappingid')
            ->distinct()

            ->when($deptcode, function ($query) use ($deptcode) {
                if (!is_array($deptcode)) {
                    $deptcode = explode(',', $deptcode);
                }
                $query->whereIn('planmap.deptcode', $deptcode);
            })

            ->when($financialyear, function ($query) use ($financialyear) {
                $query->where('planmap.financialyearcode', $financialyear);
            })

            ->orderBy('planmap.auditquartercode', 'ASC');

        break;


case 'district':
    $query->join(self::$regionTable . ' as re', 're.regioncode', '=', 'inst.regioncode')
    ->join(self::$District_Table . ' as d', 'd.distcode', '=', 'inst.distcode')
    ->select('d.distcode', 'd.distename', 'd.disttname')
    ->distinct()
    ->when($regioncode, function ($query) use ($regioncode) {
        if (!is_array($regioncode)) {
            $regioncode = explode(',', $regioncode);
        }
        $query->whereIn('inst.regioncode', $regioncode);
    })
    ->orderBy('d.distename', 'ASC');

break;

case 'audityear':

    $table = 'audit.mst_auditperiod';

    $query = DB::table($table . ' as period')
        ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'period.deptcode')
        ->select(
            'dept.deptcode',
            'period.auditperiodid',
            'period.fromyear',
            'period.toyear',
            'period.lagacyyear',
            DB::raw("string_agg(CONCAT(period.fromyear, ' - ', period.toyear), ', ' ORDER BY period.fromyear) as audit_period")
        )
      
        ->when($deptcode, function ($query) use ($deptcode) {
            if (!is_array($deptcode)) {
                $deptcode = explode(',', $deptcode);
            }
            $query->whereIn('dept.deptcode', $deptcode);
        })        
        ->whereIn('period.lagacyyear', ['Y', 'B'])
        ->groupBy('period.auditperiodid','period.fromyear', 'period.toyear','dept.deptcode')  
        ->orderBy('period.fromyear', 'ASC');

    break;




case 'institution':
$query = DB::table('audit.mst_institution as insti')
    ->join(self::$AuditPlan_Table . ' as plan', 'insti.instid', '=', 'plan.instid')
    ->select('insti.instid', 'insti.instename', 'insti.insttname')
    ->where('insti.statusflag', 'Y')
    ->when($deptcode, function ($query) use ($deptcode) {
        if (!is_array($deptcode)) {
            $deptcode = explode(',', $deptcode);
        }
        $query->whereIn('insti.deptcode', $deptcode);
    })
    ->when($regioncode, function ($query) use ($regioncode) {
        if (!is_array($regioncode)) {
            $regioncode = explode(',', $regioncode);
        }
        $query->whereIn('insti.regioncode', $regioncode);
    })
    ->when($distcode, function ($query) use ($distcode) {
        if (!is_array($distcode)) {
            $distcode = explode(',', $distcode);
        }
        $query->whereIn('insti.distcode', $distcode);
    })
  
    ->orderBy('insti.instename', 'asc')
    ->distinct();

break;



case 'institutionforauditreport':

    $table = 'audit.mst_institution as inst';

$query = DB::table($table)
    ->join(self::$AuditPlan_Table . ' as plan', 'inst.instid', '=', 'plan.instid')
    ->join('audit.inst_auditschedule as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
    ->select('inst.instid', 'inst.instename', 'inst.insttname')
    ->where('inst.statusflag', 'Y')

    ->when($deptcode, function ($query) use ($deptcode) {
        if (!is_array($deptcode)) {
            $deptcode = explode(',', $deptcode);
        }
        $query->whereIn('inst.deptcode', $deptcode);
    })

    ->when($regioncode, function ($query) use ($regioncode) {
        if (!is_array($regioncode)) {
            $regioncode = explode(',', $regioncode);
        }
        $query->whereIn('inst.regioncode', $regioncode);
    })

    ->when($distcode, function ($query) use ($distcode) {
        if (!is_array($distcode)) {
            $distcode = explode(',', $distcode);
        }
        $query->whereIn('inst.distcode', $distcode);
    })

    ->whereNotNull('sch.exitmeetdate')
    ->where(function ($q) {
        $q->where('sch.sendintimation', 'Y')
          ->orWhere('sch.sendintimation', 'F');
    })

    ->whereNull('sch.issuedflag')

    ->whereNotExists(function ($sub) {
        $sub->select(DB::raw(1))
            ->from('audit.auditplan as ap2')
            ->whereColumn('ap2.instid', 'plan.instid')
            ->where('ap2.spilloverflag', 'Y');
    })

    ->orderBy('inst.instename', 'asc')
    ->distinct();


    break;



    case 'institutionforexitmeeting':

        $table = 'audit.mst_institution as inst';
    
    $query = DB::table($table)
        ->join(self::$AuditPlan_Table . ' as plan', 'inst.instid', '=', 'plan.instid')
        ->join('audit.inst_auditschedule as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
        ->select('inst.instid', 'inst.instename', 'inst.insttname')
    
        ->when($deptcode, function ($query) use ($deptcode) {
            if (!is_array($deptcode)) {
                $deptcode = explode(',', $deptcode);
            }
            $query->whereIn('inst.deptcode', $deptcode);
        })
    
        ->when($regioncode, function ($query) use ($regioncode) {
            if (!is_array($regioncode)) {
                $regioncode = explode(',', $regioncode);
            }
            $query->whereIn('inst.regioncode', $regioncode);
        })
    
        ->when($distcode, function ($query) use ($distcode) {
            if (!is_array($distcode)) {
                $distcode = explode(',', $distcode);
            }
            $query->whereIn('inst.distcode', $distcode);
        })

      
    
        ->where('inst.statusflag', 'Y')
        // ->where('plan.auditquartercode', $auditquarter)
        ->whereNotNull('sch.entrymeetdate')
        ->whereNull('sch.exitmeetdate')       
        ->orderBy('inst.instename', 'asc')
        ->distinct();
    
    
        break;


}

$result = $query->get();


$data = [
    'deptcode' => is_array($deptcode) ? $deptcode : [$deptcode ?? 'A'],
    'regioncode' => !empty($regioncode) ? $regioncode : 'A',
    'distcode' => !empty($distcode) ? $distcode : 'A',
    'actioncode' => !empty($actioncode) ? $actioncode : 'A',
    'financialyear' => !empty($financialyear) ? (string)$financialyear : 'A',
    'auditquarter' => is_array($auditquarter) ? $auditquarter : [$auditquarter ?? 'A'],  
    'instmappingcode' => !empty($instmappingcode) ? (array)$instmappingcode : [],
   

];

if ($formname === 'lagacystatus' || $formname === 'paraoverallcount')  {
    $data['audityearcode'] = !empty($audityearcode) ? $audityearcode : null;
}
if ($formname === 'inspectionreport') {
    $data['reportstatus'] = !empty($reportstatus) ? $reportstatus : null;
}

if ($formname === 'sliptotalparadetails' || $formname === 'moneyandnonmoneyparadetails') {
    $data['catcode'] = !empty($catcode) ? $catcode : null;
    $data['subcatcode'] = !empty($subcatcode) ? $subcatcode : null;

}
if ($formname === 'parareport') {
    $data['parareportstatus'] = !empty($parareportstatus) ? $parareportstatus : null;
}
if ($formname === 'scheduledcountreport') {
    $data['maxslip'] = isset($maxslip) && is_numeric($maxslip) ? (int)$maxslip : null;
    $data['catcode'] = !empty($catcode) ? $catcode : null;
    $data['subcatcode'] = !empty($subcatcode) ? $subcatcode : null;
}		

if ($formname === 'dailyloginstatus') {
    $data['logindate'] = !empty($logindate) ? $logindate : null;
}

if ($formname === 'auditreport') {
    $data['reportstatuscode'] = !empty($reportstatuscode) ? $reportstatuscode : null;
}
switch ($formname) {

case 'legacycount':
    $details = self::getlegacycount($data);
break;


case 'paraoverallcount':
    $details = self::paracount_fetchData($data);
break;

case 'idlereport':
    $details = self::getidlereportdetails($data);
break;

case 'epacsdetails':
    $details = self::getepacsdetails($data);
break;
	
case 'lagacystatus':
    $details = self::LagacyreportData($data);
break;
case 'droppedslipcountreport':
                $details = self::droppedslipfetchData($data);
                break;
case 'paradetailsreport':
                $details = self::paraDetailsFetch($data);
                break;
 	case 'auditeeintimationreport':
                $details = self::auditIntimationFetch($data);
                break;
case 'scheduledcountreport':
$details = self::getMinSlipCountData($data);
break;
case 'parareport':
    $details = self::paramanagement_fetchData($data);
break;
case 'auditreport':
$details = self::getAuditreportData($data);
break;
case 'auditreportcount':
    $details = self::auditReportCountData($data);
break;
case 'exitmeetingnotdone':
    $details = self::ExitmeetingnotdoneData($data);
break;

case 'inspectionreport':
$details = self::InspectionreportData($data);
break;
case 'plancountreport':
    $details = self::plancount_fetchData($data);
break;
case 'dailyloginstatus':
    $details = self::loginstatusCountData($data);
break;
case 'sliptotalparadetails':
$details = self::getConvertedparadetails($data);
break;

case 'unapprovedauditpara':
    $details = self::unapprovedauditparapendingdetails($data);
break;

case 'leavedetails':
    $details = self::getleavedetailsofauditors($data);
break;

case 'diarydetails':
    $details = self::diarysubmission_fetchData($data);
break;

case 'apmsalldetailscount':
    $details = self::apmsall_fetchData($data);
break;

case 'spilloverwithdiarydetails':
    $details = self::spilloverwithdiary_fetchData($data);
break;

case 'moneyandnonmoneyparadetails':
    $details = self::getmoneyandnonmoneyparadetails($data);
break;


case 'apmsdetails':
    $details = self::fetch_apmsdetails($data);
break;

default:
throw new InvalidArgumentException("formname 'formname' provided. Allowed values are 'checkschedulestatus' ");
}

return [
'data' => $result,
'details' => $details,


];
}



public static function getMinSlipCountData($data)
{
    $query = DB::select(
        'SELECT * FROM audit.get_underreview_audits(
            CAST(:p_deptcode AS character varying[]),
            CAST(:p_regioncode AS character varying[]),
            CAST(:p_districtcode AS character varying[]),
            CAST(:p_financialyearcode AS character varying),
            CAST(:p_auditquartercode AS integer[]),
            CAST(:p_instid AS integer[]),
            CAST(:p_catcode AS character varying[]),
            CAST(:p_auditeeins_subcategoryid AS integer[]),
            CAST(:p_maxslip AS integer)

        )',
        [

            'p_deptcode' => (
                is_array($data['deptcode']) && count($data['deptcode']) > 0
            ) ? '{' . implode(',', $data['deptcode']) . '}' : '{}',


            'p_regioncode' => (
                is_array($data['regioncode']) && count($data['regioncode']) > 0
            ) ? '{' . implode(',', $data['regioncode']) . '}' : '{}',


            'p_districtcode' => (
                is_array($data['distcode']) && count($data['distcode']) > 0
            ) ? '{' . implode(',', $data['distcode']) . '}' : '{}',


            'p_financialyearcode' => (string)$data['financialyear'],

            'p_auditquartercode' => is_null($data['auditquarter'])
            ? null
            : (
                is_array($data['auditquarter']) && count($data['auditquarter']) > 0
                ? '{' . implode(',', array_map('intval', $data['auditquarter'])) . '}'
                : null
            ),


           'p_instid' => (
                is_array($data['instmappingcode']) && count($data['instmappingcode']) > 0
            ) ? '{' . implode(',', array_map('intval', $data['instmappingcode'])) . '}' : '{}',


            'p_catcode' => (
                is_array($data['catcode']) && count($data['catcode']) > 0
            ) ? '{' . implode(',', $data['catcode']) . '}' : '{}',

            'p_auditeeins_subcategoryid' => (
                is_array($data['subcatcode']) && count($data['subcatcode']) > 0
            ) ? '{' . implode(',', array_map('intval', $data['subcatcode'])) . '}' : '{}',



            'p_maxslip' => isset($data['maxslip']) ? (int)$data['maxslip'] : 5


        ]
    );

    return $query;
}



// public static function getMinSlipCountData($data)
// {
//     $query = DB::select(
//         'SELECT * FROM audit.get_underreview_audits(
//             CAST(:p_deptcode AS character varying[]),
//             CAST(:p_regioncode AS character varying[]),
//             CAST(:p_districtcode AS character varying[]),
//             CAST(:p_financialyearcode AS character varying),
//             CAST(:p_auditquartercode AS character varying[]),
//             CAST(:p_instid AS integer[]),
//             CAST(:p_catcode AS character varying[]), 
//             CAST(:p_auditeeins_subcategoryid AS integer[]),
//             CAST(:p_maxslip AS integer)

//         )',
//         [

//             'p_deptcode' => (
//                 is_array($data['deptcode']) && count($data['deptcode']) > 0
//             ) ? '{' . implode(',', $data['deptcode']) . '}' : '{}',


//             'p_regioncode' => (
//                 is_array($data['regioncode']) && count($data['regioncode']) > 0
//             ) ? '{' . implode(',', $data['regioncode']) . '}' : '{}',


//             'p_districtcode' => (
//                 is_array($data['distcode']) && count($data['distcode']) > 0
//             ) ? '{' . implode(',', $data['distcode']) . '}' : '{}',


//             'p_financialyearcode' => (string)$data['financialyear'],

//             'p_auditquartercode' => (
//                 is_array($data['auditquarter']) && count($data['auditquarter']) > 0
//             ) ? '{' . implode(',', array_map(fn($val) => '"' . $val . '"', $data['auditquarter'])) . '}'
//             : '{}',


//            'p_instid' => (
//                 is_array($data['instmappingcode']) && count($data['instmappingcode']) > 0
//             ) ? '{' . implode(',', array_map('intval', $data['instmappingcode'])) . '}' : '{}',


//             'p_catcode' => (
//                 is_array($data['catcode']) && count($data['catcode']) > 0
//             ) ? '{' . implode(',', $data['catcode']) . '}' : '{}',

//             'p_auditeeins_subcategoryid' => (
//                 is_array($data['subcatcode']) && count($data['subcatcode']) > 0
//             ) ? '{' . implode(',', array_map('intval', $data['subcatcode'])) . '}' : '{}',



//             'p_maxslip' => isset($data['maxslip']) ? (int)$data['maxslip'] : 5


//         ]
//     );

//     return $query;
// }

public static function getAuditreportData($data)
{
    $reportstatus    = $data['reportstatuscode'] ?? 'A';

    $table = self::$AuditPlan_Table;

    $query = DB::table(self::$Institution_Table . ' as ins')
        ->join($table . ' as plan', 'ins.instid', '=', 'plan.instid')
        ->join(self::$Instschedule_Table . ' as sch', 'plan.auditplanid', '=', 'sch.auditplanid')
        ->join(self::$Dept_Table . ' as dept', 'dept.deptcode', '=', 'ins.deptcode')
        ->join(self::$regionTable . ' as r', 'r.regioncode', '=', 'ins.regioncode')
        ->join(self::$District_Table . ' as di', 'di.distcode', '=', 'ins.distcode')
        ->join('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', 'ins.catcode')
        ->join('audit.auditplanmapping as planmap', 'planmap.planmappingid', '=', 'plan.planmappingid')
        ->join('audit.mst_financialyear as f', 'f.financialyearcode', '=', 'planmap.financialyearcode')
        ->leftJoin('audit.mst_auditeeins_subcategory as subcat', 'subcat.auditeeins_subcategoryid', '=', 'ins.subcatid')
        ->where('ins.statusflag', 'Y')
        ->whereNotNull('sch.exitmeetdate')
        ->where(function($q) {
            $q->where('plan.carryforwardflag', 'N')
              ->orWhereNull('plan.carryforwardflag');
        });

        $applyFilter = function ($key, $column) use ($query, $data) {
            if (!empty($data[$key]) && !in_array('A', (array)$data[$key])) {
                $query->whereIn($column, (array)$data[$key]);
            }
        };

        $applyFilter('deptcode',        'dept.deptcode');
        $applyFilter('regioncode',      'r.regioncode');
        $applyFilter('distcode',        'di.distcode');
        $applyFilter('catcode',         'cat.catcode');
        $applyFilter('subcatcode',      'subcat.auditeeins_subcategoryid');
        $applyFilter('auditquarter',    'plan.planmappingid');
         $applyFilter('financialyear',    'planmap.financialyearcode');
        $applyFilter('instmappingcode', 'ins.instid');

    switch ($reportstatus) {
        case 'P':
            $query->where(function($q) {
                $q->where('sch.sendintimation', 'Y')
                  ->orWhereNull('sch.sendintimation');
            });
            break;
        case 'Y':
            $query->where('sch.issuedflag', 'Y')
                  ->where('sch.sendintimation', 'F');
            break;
        case 'F':
            $query->where('sch.sendintimation', 'F')
                  ->whereNull('sch.issuedflag');
            break;
        case 'A':
        default:
            break;
    }

    $select = [
        'ins.instename',
        'cat.catcode',
        'cat.catename',
        'subcat.auditeeins_subcategoryid',
        'subcat.subcatename',
        'subcat.subcattname',
        'planmap.planname',
        'f.financialyear',
        'f.financialyearcode',
        DB::raw("TO_CHAR(sch.entrymeetdate, 'DD-MM-YYYY') AS entrymeetdate"),
        DB::raw("TO_CHAR(sch.exitmeetdate, 'DD-MM-YYYY') AS exitmeetdate"),
        'ins.insttname',
        'dept.deptcode',
        'dept.deptesname',
        'dept.depttlname',
        'r.regionename',
        'r.regiontname',
        'di.distename',
        'di.disttname',
    ];

    if ($reportstatus === 'Y') {
        $select[] = 'sch.issuedflag';
    } elseif ($reportstatus === 'F') {
        $select[] = 'sch.sendintimation';
    } else {
        $select[] = 'sch.sendintimation';
        $select[] = 'sch.issuedflag';
    }

   // Return the query results
    return $query
        ->select($select)
        ->orderBy('dept.deptcode')
        ->orderBy('cat.catcode')
        ->orderByRaw("
        CASE
            WHEN sch.sendintimation = 'Y' OR sch.sendintimation IS NULL THEN 1
            WHEN sch.sendintimation = 'F' AND sch.issuedflag IS NULL THEN 2
            WHEN sch.issuedflag = 'Y' THEN 3
            ELSE 4
        END
    ")
       ->orderBy('sch.entrymeetdate')
       ->get();

}




public static function changerequestfetchData($table = null, $deptcode = null, $regioncode = null, $districtcode = null)
    {

        
        $sessiondet = session('charge');
        $sessiondeptcode =  $sessiondet->deptcode;
        $sessionregion =  $sessiondet->regioncode;
        $sessiondistrict =  $sessiondet->distcode;
        $table = self::$UserDetails_Table;

        $query = DB::table($table . ' as ut')
            ->join(self::$UserChargeDetails_Table . ' as uc', 'uc.userid', '=', 'ut.deptuserid')
            ->join(self::$Dept_Table . ' as dept', 'dept.deptcode', '=', 'ut.deptcode')
            ->join(self::$ChargeDetails_Table . ' as c', 'c.chargeid', '=', 'uc.chargeid')
            ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
            ->join(self::$Designation_Table . ' as d', 'd.desigcode', '=', 'ut.desigcode')
            ->join(self::$regionTable . ' as r', 'r.regioncode', '=', 'c.regioncode')
            ->join(self::$District_Table . ' as di', 'di.distcode', '=', 'c.distcode')
            ->where('ut.reservelist', 'N')
            ->where('uc.statusflag', 'Y')
            ->where('ut.statusflag', 'Y')
            ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'));

        if (!empty($deptcode) && !in_array('A', (array) $deptcode)) {
            $query->whereIn('ut.deptcode', (array) $deptcode);
        }
        
        if (!empty($regioncode) && !in_array('A', (array) $regioncode)) {
            $query->whereIn('c.regioncode', (array) $regioncode);
        }
        
        if (!empty($districtcode) && !in_array('A', (array) $districtcode)) {
            $query->whereIn('c.distcode', (array) $districtcode);
        }

        $query->orderBy('dept.deptcode')
		->orderBy('r.regionename')
->orderBy('di.distename')
->orderBy('d.desigtlname');
      
      

        return $query->select('ut.deptuserid', 'd.desigelname','d.desigtlname', 'ut.username', 'ut.usertamilname','dept.deptcode','dept.deptesname','dept.depttlname','r.regionename','r.regiontname','di.distename','di.disttname')->get();
    }

public static function InspectioncountData($data)
{
    $deptcodes     = $data['deptcode'] ?? [];
    $regioncode    = $data['regioncode'] ?? [];
    $distcode      = $data['distcode'] ?? [];
    $auditquarter  = $data['auditquarter'] ?? [];
   

    $query = DB::table(self::$Instschedule_Table . ' as schd')
        ->join(self::$AuditPlan_Table . ' as plan', 'plan.auditplanid', '=', 'schd.auditplanid')
        ->join(self::$Institution_Table . ' as inst', 'inst.instid', '=', 'plan.instid')
        ->join(self::$Dept_Table . ' as dept', 'dept.deptcode', '=', 'inst.deptcode')
        ->join(self::$regionTable . ' as reg', 'inst.regioncode', '=', 'reg.regioncode')
        ->join(self::$District_Table . ' as dist', 'inst.distcode', '=', 'dist.distcode')
        ->leftJoin(self::$transinspection_table . ' as trans', 'trans.auditscheduleid', '=', 'schd.auditscheduleid');

    // 🔹 Filters
    if (!(count($deptcodes) === 1 && $deptcodes[0] === 'A')) {
        if (!empty($deptcodes)) {
            $query->whereIn('inst.deptcode', $deptcodes);
        }
    }
    if (!empty($regioncode) && !in_array('A', $regioncode)) {
        $query->whereIn('inst.regioncode', $regioncode);
    }
    if (!empty($distcode) && !in_array('A', $distcode)) {
        $query->whereIn('inst.distcode', $distcode);
    }
    if (!empty($auditquarter) && !in_array('A', $auditquarter)) {
        $query->whereIn('plan.auditquartercode', $auditquarter);
    }

    // 🔹 Common CASE expressions
    $ongoingExpr = "SUM(CASE 
                        WHEN schd.entrymeetdate IS NOT NULL 
                          AND schd.exitmeetdate IS NULL 
                          AND trans.processcode <> 'C' 
                          AND schd.auditscheduleid IN (SELECT auditscheduleid FROM audit.trans_auditinspection)
                        THEN 1 ELSE 0 END)";

    $pendingExpr = "SUM(CASE 
                        WHEN schd.entrymeetdate IS NOT NULL 
                          AND schd.exitmeetdate IS NULL 
                          AND schd.statusflag = 'F' 
                          AND schd.auditscheduleid NOT IN (SELECT auditscheduleid FROM audit.trans_auditinspection)
                        THEN 1 ELSE 0 END)";

    $completedExpr = "SUM(CASE 
                        WHEN schd.entrymeetdate IS NOT NULL 
                          AND schd.exitmeetdate IS NOT NULL 
                          AND trans.processcode = 'C'
                        THEN 1 ELSE 0 END)";






    // 🔹 Selects
    if (count($deptcodes) === 1 && $deptcodes[0] === 'A') {
        $query->select(
            'dept.deptesname',
            'dept.deptcode',
            'plan.auditquartercode',
            DB::raw("'A' as regionename"),
            DB::raw("'A' as distename"),
            DB::raw('COUNT(*) as total_count'),
            DB::raw("$ongoingExpr as ongoing_count"),
            DB::raw("$pendingExpr as pending_count"),
            DB::raw("$completedExpr as completed_count"),
        )
        ->groupBy('dept.deptesname','plan.auditquartercode','dept.deptcode');
        $query->orderBy('dept.deptcode', 'asc');
    } else {
        $query->select(
            'dept.deptesname',
            'reg.regionename',
            'dept.deptcode',
            'dist.distename',
            'plan.auditquartercode',
            DB::raw("$ongoingExpr as ongoing_count"),
            DB::raw("$pendingExpr as pending_count"),
            DB::raw("$completedExpr as completed_count"),
        )
        ->groupBy('dept.deptesname', 'reg.regionename', 'dist.distename', 'plan.auditquartercode','dept.deptcode');
        $query->orderBy('dept.deptcode', 'asc')
        ->orderBy('plan.auditquartercode', 'asc');
    }

    return $query->get();
}


public static function auditReportCountData($data)
{


    $deptcodes = is_array($data['deptcode'] ?? null)
        ? $data['deptcode']
        : [$data['deptcode'] ?? 'A'];

    $auditquarters = is_array($data['auditquarter'] ?? null)
        ? $data['auditquarter']
        : [$data['auditquarter'] ?? ''];

     

    $query = DB::table('audit.mst_institution as inst')
        ->join('audit.auditplan as ap', 'ap.instid', '=', 'inst.instid')

        ->join('audit.auditplanmapping as planmap', 'ap.planmappingid', '=', 'planmap.planmappingid')
        ->join('audit.mst_financialyear as f', 'f.financialyearcode', '=', 'planmap.financialyearcode')


        ->join('audit.inst_auditschedule as insa', 'insa.auditplanid', '=', 'ap.auditplanid')
        ->join('audit.mst_dept as dp', 'dp.deptcode', '=', 'inst.deptcode')
        ->select(
           'dp.deptcode',
            'dp.deptesname',
            'planmap.planname',
            'f.financialyearcode',
            'f.financialyear',

            DB::raw("(COUNT(CASE WHEN insa.statusflag = 'F' AND insa.entrymeetdate IS NOT NULL THEN 1 END)
                - COUNT(CASE WHEN insa.sendintimation = 'F' THEN 1 END)) AS pendingtofinalise"),
            DB::raw("(COUNT(CASE WHEN insa.statusflag = 'F' AND insa.entrymeetdate IS NOT NULL THEN 1 END)
                - COUNT(CASE WHEN insa.issuedflag = 'Y' THEN 1 END)) AS pendingtoissue"),
            DB::raw("COUNT(CASE WHEN insa.statusflag = 'F' AND insa.entrymeetdate IS NOT NULL THEN 1 END) AS auditCompleted"),
            DB::raw("COUNT(CASE WHEN insa.sendintimation = 'F' THEN 1 END) AS reportFinalised"),
            DB::raw("COUNT(CASE WHEN insa.issuedflag = 'Y' THEN 1 END) AS issuedReport")
        )


        ->when(!(count($auditquarters) === 1 && $auditquarters[0] === 'A'), function ($query) use ($auditquarters) {
            $query->whereIn('ap.planmappingid', $auditquarters);
        })

        
        ->when(!(count($deptcodes) === 1 && $deptcodes[0] === 'A'), function ($query) use ($deptcodes) {
            $query->whereIn('inst.deptcode', $deptcodes);
        })

        ->where('planmap.financialyearcode', $data['financialyear'])



     ->where(function($q) {
      $q->where('ap.carryforwardflag', 'N')
       ->orWhereNull('ap.carryforwardflag');
     })
        ->whereNotNull('insa.exitmeetdate')

         ->groupBy(
            'dp.deptcode',
            'dp.deptesname',
            'planmap.planname',
            'f.financialyearcode',
            'f.financialyear'
        )

    ->orderBy('dp.deptcode', 'asc')
  ->orderBy('planmap.planname', 'asc')
        ->get();

    return $query;
}



public static function ExitmeetingnotdoneData($data)
    {

        $deptcode = $data['deptcode'];
        $regioncode = $data['regioncode'];
        $distcode = $data['distcode'];
        $financialyear = $data['financialyear'];
        $auditquarter = $data['auditquarter'];
        $instmappingcode = $data['instmappingcode'];


        $sessiondet = session('charge');
        $sessiondeptcode =  $sessiondet->deptcode;
        $sessionregion =  $sessiondet->regioncode;
        $sessiondistrict =  $sessiondet->distcode;
        $today = date('Y-m-d');
        $table = self::$AuditPlan_Table;

        $query = DB::table($table . ' as plan')
            ->join(self::$Institution_Table . ' as ins', 'ins.instid', '=', 'plan.instid')
            ->join(self::$Instschedule_Table . ' as sch', 'plan.auditplanid', '=', 'sch.auditplanid')
            ->join(self::$Dept_Table . ' as dept', 'dept.deptcode', '=', 'ins.deptcode')
            ->join(self::$regionTable . ' as r', 'r.regioncode', '=', 'ins.regioncode')
            ->join(self::$District_Table . ' as di', 'di.distcode', '=', 'ins.distcode')
            ->join('audit.inst_schteammember as mem', 'mem.auditscheduleid', '=', 'sch.auditscheduleid')
            ->join('audit.deptuserdetails as aud', 'aud.deptuserid', '=', 'mem.userid')
            ->join('audit.mst_designation as desig', 'aud.desigcode', '=', 'desig.desigcode')
             ->join('audit.auditplanmapping as planmap', 'plan.planmappingid', '=', 'planmap.planmappingid')
            ->join('audit.mst_financialyear as f', 'f.financialyearcode', '=', 'planmap.financialyearcode')


            ->where('ins.statusflag', 'Y')
            ->whereNotNull('sch.entrymeetdate')
            ->whereDate('sch.proposedexitmeetdate', '<=', $today)
            ->whereNull('sch.exitmeetdate');


        if (!empty($deptcode) && !in_array('A', (array) $deptcode)) {
            $query->whereIn('ins.deptcode', (array) $deptcode);
        }

        if (!empty($regioncode) && !in_array('A', (array) $regioncode)) {
            $query->whereIn('ins.regioncode', (array) $regioncode);
        }

        if (!empty($districtcode) && !in_array('A', (array) $districtcode)) {
            $query->whereIn('ins.distcode', (array) $districtcode);
        }

        if (!empty($auditquarter) && !in_array('A', (array)$auditquarter)) {
            $query->whereIn('plan.auditquartercode', (array)$auditquarter);

        }

        if (!empty($financialyear)) {
            $query->where('plan.financialyearcode', $financialyear);
        }


        if (!empty($instmappingcode) && !in_array('A', (array)$instmappingcode)) {
            $query->whereIn('ins.instid', (array)$instmappingcode);

        }



        return $query->select(
            'ins.instid',
            'ins.instename',
            'ins.insttname',
            'planmap.planname',
            'f.financialyear',
            'dept.deptcode',
            'dept.deptesname',
            'dept.depttlname',
            'r.regionename',
            'r.regiontname',
            'di.distename',
            'di.disttname',
            DB::raw("
            STRING_AGG(
                CASE
                    WHEN mem.auditteamhead = 'Y'
                        THEN aud.username || ' (' || desig.desigesname || ')'
                END,
                E'\n'
            ) AS team_heads
        "),
        DB::raw("
            STRING_AGG(
                CASE
                    WHEN mem.auditteamhead = 'N'
                        THEN aud.username || ' (' || desig.desigesname || ')'
                END,
                E'\n'
            ) AS team_members
        "),

            'sch.proposedexitmeetdate',
            'sch.entrymeetdate',
            'sch.exitmeetdate'

        )
        ->orderby('dept.deptcode')
        ->orderby('sch.entrymeetdate')
        ->orderby('r.regiontname')


        ->groupBy(
            'ins.instid',
            'ins.instename',
            'ins.insttname',
            'planmap.planname',
            'f.financialyear',
            'dept.deptcode',
            'dept.deptesname',
            'dept.depttlname',
            'r.regionename',
            'r.regiontname',
            'di.distename',
            'di.disttname',
            'sch.proposedexitmeetdate',
            'sch.entrymeetdate',
            'sch.exitmeetdate'
        )
        ->get();

    }

 public static function loginstatusCountData($data)
    {
            $deptcode = $data['deptcode'];
            $regioncode = $data['regioncode'];
            $distcode = $data['distcode'];
            $logindate = $data['logindate'];

        $table = self::$UserDetails_Table; // audit.deptuserdetails
    
        $query = DB::table('audit.userlogindetails as lo')
            ->join($table . ' as ut', 'ut.deptuserid', '=', 'lo.userid')
            ->join(self::$UserChargeDetails_Table . ' as uc', 'uc.userid', '=', 'ut.deptuserid')
            ->join(self::$Dept_Table . ' as dept', 'dept.deptcode', '=', 'ut.deptcode')
            ->join(self::$ChargeDetails_Table . ' as c', 'c.chargeid', '=', 'uc.chargeid')
            ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
            ->join('audit.mst_roleaction as rc', 'rc.roleactioncode', '=', 'rm.roleactioncode')
            ->join(self::$Designation_Table . ' as d', 'd.desigcode', '=', 'ut.desigcode')
            ->join(self::$regionTable . ' as r', 'r.regioncode', '=', 'c.regioncode')
            ->join(self::$District_Table . ' as di', 'di.distcode', '=', 'c.distcode')
    
            ->select(
                'dept.deptesname',
                'ut.username',
                'ut.usertamilname',
                'd.desigelname',
                'd.desigtlname',
                'r.regionename',
                'r.regiontname',
                'dept.deptcode',
                'di.distename',
                'di.disttname',
                DB::raw('MIN(lo.logintime) as logintime'),  // earliest login per user per day
                DB::raw('COUNT(lo.userid) as login_count')   // total number of logins today
            )
            ->where('uc.statusflag', 'Y')
            ->where('ut.statusflag', 'Y')
            ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))
            ->where('d.statusflag', 'Y')
            ->where('dept.statusflag', 'Y');
            if (!empty($logindate)) {
                $query->whereDate(
                    'lo.logintime',
                    \Carbon\Carbon::createFromFormat('d-m-Y', $logindate)->format('Y-m-d')
                );
            }

            $query->groupBy(
                'dept.deptesname',
                'dept.deptcode',
                'ut.username',
                'ut.usertamilname',
                'd.desigelname',
                'd.desigtlname',
                'r.regionename',
                'r.regiontname',
                'di.distename',
                'di.disttname',
    
            )

            ->orderBy('dept.deptesname');
            // filters
            if (!empty($deptcode) && !in_array('A', (array) $deptcode)) {
                $query->whereIn('dept.deptcode', (array) $deptcode);
            }

            if (!empty($regioncode) && !in_array('A', (array) $regioncode)) {
                $query->whereIn('r.regioncode', (array) $regioncode);
            }

            if (!empty($distcode) && !in_array('A', (array) $distcode)) {
                $query->whereIn('di.distcode', (array) $distcode);
            }

    
        return $query->get();
    }
public static function InspectionreportData($data)
{

    $deptcode = $data['deptcode'];
    $regioncode = $data['regioncode'];
    $distcode = $data['distcode'];
    $financialyear = $data['financialyear'];
    $auditquarter = $data['auditquarter'];
    $instmappingcode = $data['instmappingcode'];
    $reportstatus = $data['reportstatus'];


    $sessiondet = session('charge');
    $sessiondeptcode = $sessiondet->deptcode;
    $sessionregion = $sessiondet->regioncode;
    $sessiondistrict = $sessiondet->distcode;

    $reportstatus = (array) $reportstatus;

    // Subquery for audit periods
    $auditPeriodSubquery = DB::table(self::$MapYearcode_Table . ' as ycm')
        ->join('audit.mst_auditperiod as ap', 'ap.auditperiodid', '=', 'ycm.yearselected')
        ->select(
            'ycm.auditplanid',
            DB::raw("string_agg(CONCAT(ap.fromyear, ' - ', ap.toyear), ', ' ORDER BY ap.fromyear) as audit_period")
        )
        ->where('ycm.statusflag', 'Y')
        ->where('ap.statusflag', 'Y')
        ->where('ap.financestatus', 'N')
        ->groupBy('ycm.auditplanid');

    // Main query for schedules
    $query = DB::table(self::$Instschedule_Table . ' as schd')
        ->join(self::$AuditPlan_Table . ' as plan', 'plan.auditplanid', '=', 'schd.auditplanid')
        ->join(self::$Institution_Table . ' as inst', 'inst.instid', '=', 'plan.instid')
        ->join(self::$Dept_Table . ' as dept', 'dept.deptcode', '=', 'inst.deptcode')
        ->join(self::$regionTable . ' as reg', 'inst.regioncode', '=', 'reg.regioncode')
        ->join(self::$District_Table . ' as dist', 'inst.distcode', '=', 'dist.distcode')
        ->join('audit.mst_financialyear AS yr', 'plan.financialyearcode', '=', 'yr.financialyearcode')
        ->leftJoin(self::$transinspection_table . ' as trans', 'trans.auditscheduleid', '=', 'schd.auditscheduleid')
        ->join('audit.auditplanmapping as pmap', 'pmap.planmappingid', '=', 'plan.planmappingid')
        ->leftJoin('audit.deptuserdetails as du', 'du.deptuserid', '=', 'trans.createdby')
        ->leftJoin(self::$Designation_Table . ' as desig', 'desig.desigcode', '=', 'trans.initiatedbydesigcode')
        // ->leftJoin(self::$ProcessFlag_Table . ' as pro', 'pro.processcode', '=', 'trans.processcode')
        ->leftJoinSub($auditPeriodSubquery, 'aps', function ($join) {
            $join->on('aps.auditplanid', '=', 'plan.auditplanid');
        });


    // Apply dynamic status filters
    if (!empty($reportstatus)) {
        $query->where(function ($q) use ($reportstatus) {
            foreach ($reportstatus as $status) {
                if ($status == 'P') { //Not schedules
                    $q->orWhere(function ($sub) {
                        $sub->where('schd.statusflag', 'F')
                            ->whereNull('schd.entrymeetdate')
                            ->whereNull('schd.exitmeetdate');
                    });
                } elseif ($status == 'N') { // Pending
                    $q->orWhere(function ($sub) {
                        $sub->where('schd.statusflag', 'F')
                            ->whereNotNull('schd.entrymeetdate')
                           // ->whereNull('schd.exitmeetdate')
                            ->whereNotIn('schd.auditscheduleid', function($query) {
                                $query->select('auditscheduleid')
                                      ->from('audit.trans_auditinspection');
                            });

                    });
                } elseif ($status == 'O') {
                    $q->orWhere(function ($sub) {
                        $sub->whereNotNull('schd.entrymeetdate')
                            ->where('trans.processcode','<>','C');
                          //  ->whereNull('schd.exitmeetdate');
                    });
                } elseif ($status == 'C') {
                    $q->orWhere(function ($sub) {
                        $sub->whereNotNull('schd.entrymeetdate')
                        ->where('trans.processcode' , '=','C');
                       // ->whereNotNull('schd.exitmeetdate')
                    });
                }
            }
        });
    }

    // Filters for department, region, district, quarter, institution
    if (!empty($deptcode) && !in_array('A', (array)$deptcode)) {
        $query->whereIn('inst.deptcode', (array)$deptcode);
    }
    if (!empty($auditquarter) && !in_array('A', (array)$auditquarter)) {
        $query->whereIn('plan.auditquartercode', (array)$auditquarter);
    }
    if (!empty($regioncode) && !in_array('A', (array)$regioncode)) {
        $query->whereIn('inst.regioncode', (array)$regioncode);
    }
    if (!empty($districtcode) && !in_array('A', (array)$districtcode)) {
        $query->whereIn('inst.distcode', (array)$districtcode);
    }

    if (!empty($instmappingcode) && !in_array('A', (array)$instmappingcode)) {
        $query->whereIn('inst.instid', (array)$instmappingcode);
    }


    if (!empty($financialyear)) $query->where('plan.financialyearcode', $financialyear);


    $query->orderBy('dept.deptcode', 'asc')
          ->orderBy('trans.createdon', 'asc')
          ->orderBy('reg.regionename', 'asc');


        $selectCols = [
        'yr.financialyearcode',
        'yr.financialyear',
        'du.username',
        'du.usertamilname',
        'desig.desigesname',
        'pmap.planname',
        'plan.auditquartercode',
        'dept.deptesname',
        'reg.regionename',
        'reg.regiontname',
        'dist.disttname',
        'dist.distename',
        'inst.instename',
        'inst.insttname',
        'schd.entrymeetdate',
        'schd.exitmeetdate',
        'aps.audit_period',
        DB::raw("TO_CHAR(schd.fromdate, 'DD/MM/YY') || ' - ' || TO_CHAR(schd.todate, 'DD/MM/YY') AS schedule_period"),
        DB::raw("
        CASE
            WHEN trans.processcode = 'C' THEN trans.createdon
            WHEN trans.processcode <> 'C' THEN trans.createdon
            ELSE NULL
        END as createdon
    "),
     DB::raw("CASE WHEN trans.processcode = 'C' THEN trans.updatedon ELSE NULL END as updatedon"),
        DB::raw("
        CASE

        WHEN schd.entrymeetdate IS NOT NULL
            AND trans.processcode <> 'C'
        THEN 'Ongoing'

        WHEN schd.entrymeetdate IS NOT NULL
            AND schd.statusflag = 'F'
             AND schd.auditscheduleid NOT IN (
                SELECT auditscheduleid FROM audit.trans_auditinspection
            )
        THEN 'Pending'

        -- Not Scheduled
        WHEN schd.entrymeetdate IS NULL
            AND schd.exitmeetdate IS NULL
        THEN 'Not Scheduled'

        -- Completed
        WHEN schd.entrymeetdate IS NOT NULL
            AND trans.processcode = 'C'
        THEN 'Completed'
    END as inspection_status


    ")

    ];


  return $query->select($selectCols)->get();

}
  public static function GetSubCategoryData($catcode)
    {
         $query = DB::table('audit.mst_auditeeins_subcategory as sub')
            //->join(self::$category . ' as cat', 'cat.catcode', '=', 'sub.catcode')
            ->select('sub.auditeeins_subcategoryid', 'sub.subcattname', 'sub.subcatename')
            ->where('sub.statusflag', 'Y')
            ->where('sub.catcode', $catcode)
            ->orderBy('sub.auditeeins_subcategoryid', 'desc')
            ->get();

             return $query;
    }
public static function getAuditYear()
        {
            $deptcode = session('charge')->deptcode;
            $table = 'audit.mst_auditperiod';

            $query = DB::table($table . ' as period')
                ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'period.deptcode')
                ->select(
                    'period.auditperiodid',
                    'period.fromyear',
                    'period.toyear',
                    DB::raw("string_agg(CONCAT(period.fromyear, ' - ', period.toyear), ', ' ORDER BY period.fromyear) as audit_period")
                )

            ->when($deptcode, function ($query) use ($deptcode) {
                if (!is_array($deptcode)) {
                    $deptcode = explode(',', $deptcode);
                }
                $query->whereIn('dept.deptcode', $deptcode);
            })
            ->whereIn('period.lagacyyear', ['N', 'B'])
            ->groupBy('period.auditperiodid','period.fromyear', 'period.toyear','dept.deptcode')
            ->orderBy('period.fromyear', 'ASC');

            $query = $query->get();

            return $query;
        }
public static function LagacyreportData($data) {

        $deptcodes = is_array($data['deptcode'] ?? null)
            ? $data['deptcode']
            : [$data['deptcode'] ?? 'A'];

        $audityearcodes = is_array($data['audityearcode'] ?? null)
            ? $data['audityearcode']
            : [$data['audityearcode'] ?? 0];

      //  dd($audityearcodes);

        $query = DB::table('audit.trans_followup as fp')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'fp.instid')
            ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'inst.deptcode')
            ->join('audit.mst_auditperiod as ap', function ($join) {
                $join->on(DB::raw("CAST(ap.auditperiodid AS TEXT)"), '=', DB::raw("(fp.audityear->>0)"))
                     ->on('ap.deptcode', '=', 'dept.deptcode');
            })
            ->where('fp.paratype', 'L')
            ->whereIn('ap.lagacyyear', ['Y','B'])
           // ->where('fp.statusflag','F')
            ->when(!(count($deptcodes) === 1 && $deptcodes[0] === 'A'), function($query) use ($deptcodes) {
                            $query->whereIn('inst.deptcode', $deptcodes);
                        })
            ->when(!(count($audityearcodes) === 1 && $audityearcodes[0] === 'A'), function ($query) use ($audityearcodes) {
                $query->whereIn(DB::raw("(fp.audityear->>0)::int"), $audityearcodes);
            });

        if (count($deptcodes) === 1 && $deptcodes[0] === 'A') {

            $query->select('dept.deptesname','dept.deptcode',
                DB::raw("'A' as audit_period"),
                DB::raw("'A' as audityear"),
                DB::raw('COUNT(fp.followupid) as legacycount'),
                DB::raw("COUNT(CASE WHEN fp.statusflag = 'Y' THEN 1 END) as entrycount"),
                DB::raw("COUNT(CASE WHEN fp.statusflag = 'F' THEN 1 END) as legacycount"),
                DB::raw("COUNT(CASE WHEN fp.statusflag IN ('Y','F') THEN 1 END) as totalcount")



            )
            ->orderby('dept.deptcode')
            ->groupBy('dept.deptesname','dept.deptcode');
        }

        else {
            $query->select(
                'dept.deptesname',
                'dept.deptcode',
                DB::raw("ap.auditperiodid as audityear"),
                DB::raw("CONCAT(ap.fromyear, ' - ', ap.toyear) as audit_period"),
                DB::raw("COUNT(CASE WHEN fp.statusflag = 'Y' THEN 1 END) as entrycount"),
                DB::raw("COUNT(CASE WHEN fp.statusflag = 'F' THEN 1 END) as legacycount"),
                DB::raw("COUNT(CASE WHEN fp.statusflag IN ('Y','F') THEN 1 END) as totalcount")
            )

            ->orderBy('dept.deptcode')
            ->orderBy('ap.fromyear')
            ->groupBy('dept.deptesname', 'dept.deptcode','ap.auditperiodid','ap.fromyear');


        }


        return $query->get();
}


public static function GetExcludeHolidayAuditors($deptcode, $regioncode, $districtcode)
        {
        $deptStr     = is_array($deptcode) ? implode(',', $deptcode) : $deptcode;
        $regionStr   = is_array($regioncode) ? implode(',', $regioncode) : $regioncode;
        $districtStr = is_array($districtcode) ? implode(',', $districtcode) : $districtcode;
        $statusStr   = '0';
        $quarterStr  = $quarter ?? 'all';

        $row = DB::selectOne("
            SELECT audit.getidelauditors_current(?, ?, ?, ?, ?) AS data
        ", [$deptStr, $regionStr, $districtStr, $statusStr, $quarterStr]);

        return $row && $row->data ? json_decode($row->data, true) : [];
       }

   
       public static function GetHolidays()
       {
            return DB::table('audit.mst_holiday')
                ->where('statusflag', 'Y')
                ->pluck('holiday_date')
                ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))
                ->toArray();
       }


 public static function dailylogindeptfetch()
    {
        return DB::table('audit.mst_dept as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname')
            ->where('dept.statusflag', '=', 'Y')
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }
   
       public static function BuildWorkingDates($daysToShow = 60)
       {
        $today    = \Carbon\Carbon::today();
        $holidays = self::GetHolidays();
        $workingDates = [];

        for ($i = 0; $i < $daysToShow; $i++) {
            $date = $today->copy()->addDays($i);
            if ($date->isWeekend() || in_array($date->format('Y-m-d'), $holidays)) {
                continue;
            }
            $workingDates[] = $date->format('Y-m-d');
        }

        return $workingDates;
       }  

 public static function model_workallocationdeptfetch()
    {
        return DB::table(self::$deptTable . ' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname') // Select required columns
            ->where('dept.statusflag', '=', 'Y') // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }


public static function commonRegionsByDept($deptcode)
{
    $table = 'audit.mst_institution';

    return DB::table($table . ' as ins')
        ->join(self::$regionTable . ' as reg', 'ins.regioncode', '=', 'reg.regioncode')
        ->select('reg.regioncode','reg.regionename','reg.regiontname')
        ->distinct()
        ->where('ins.deptcode', $deptcode)
        ->where('ins.statusflag', 'Y')
        ->orderBy('reg.regionename', 'Asc')
        ->get();
    
}

public static function commondistrictByregion($regioncode, $deptcode)
{
    $table = 'audit.mst_institution';

    return DB::table($table . ' as ins')
        ->join(self::$distTable . ' as dis', 'ins.distcode', '=', 'dis.distcode')
        ->select('dis.distename','dis.disttname','dis.distcode')
        ->distinct()
        ->where('ins.deptcode', $deptcode)
        ->where('ins.regioncode', $regioncode)
        ->where('ins.statusflag', 'Y')
        ->get();
}

public static function userchangerequest_update(array $data,$userid, $table = null)
{
    try {

        if($data['statusflag'] === 'N'){
        //     $table = 'audit.deptuserdetails';

            $username =  $data['username'];
            $auditQuarter =  $data['currentquarter'];

            DB::beginTransaction();

            $now = View::shared('get_nowtime');


            DB::table('audit.deptuserdetails')
            ->where('deptuserid', $username)
            ->update([
                'statusflag' => 'N',
                'createdon'  => $now,
                'createdby'  => $userid,
                'updatedon'  => $now,
                'updatedby'  => $userid
            ]);


          

            DB::table('audit.userchargedetails')
                ->where('userid', $username)
                ->update([
                    'statusflag' => 'N',
                    'createdon'  => $now,
                    'createdby'  => $userid,
                    'updatedon'  => $now,
                    'updatedby'  => $userid
                ]);



            $chargeIds = DB::table('audit.userchargedetails')
            ->where('userid', $username)
            ->pluck('userchargeid')  
            ->map(fn($id) => (string) $id); 
            
            $chargeData = [
                "1" => $chargeIds->all()
            ];
            
            // Encode to JSON
            $chargeIdsJson = json_encode($chargeData);

            DB::table('audit.inactiveusers')->insert([
                'deptuserid'        => $username,
                'userchargeid'      => DB::raw("'" . $chargeIdsJson . "'::jsonb"),
                'reasonforinactive' => $data['reasonforinactive'],
                'createdon'         => $now,
                'createdby'         => $userid,
                'updatedon'         => $now,
                'updatedby'         => $userid
            ]);




                DB::commit();

            return;

        }else{
            throw new \Exception('InvalidDeactiveFlag'); 

    }
    } catch (\Exception $e) {
        DB::rollBack(); 
        throw new \Exception($e->getMessage());
    }
}



public static function getusernameforuserchangereq($regioncode, $deptcode,$distcode)
{

    $table = self::$UserDetails_Table;
    
    $query = DB::table($table . ' as ut')
        ->select('ut.deptuserid', 'ut.username', 'ut.usertamilname','d.desigesname','ut.ifhrmsno','dept.currentquarter')
        ->join(self::$UserChargeDetails_Table . ' as uc', 'uc.userid', '=', 'ut.deptuserid')
        ->join(self::$Dept_Table . ' as dept', 'dept.deptcode', '=', 'ut.deptcode')
        ->join(self::$ChargeDetails_Table . ' as c', 'c.chargeid', '=', 'uc.chargeid')
        ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
        ->join(self::$Designation_Table . ' as d', 'd.desigcode', '=', 'ut.desigcode')
        ->join(self::$regionTable . ' as r', 'r.regioncode', '=', 'c.regioncode')
        ->join(self::$District_Table . ' as di', 'di.distcode', '=', 'c.distcode')
        //->where('ut.reservelist', 'N')
        ->where('uc.statusflag', 'Y')
        ->where('ut.statusflag', 'Y')
        ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))
        ->where('ut.deptcode', $deptcode)
        ->where('c.regioncode', $regioncode)
        ->where('c.distcode', $distcode)

        ->distinct()
        ->orderBy('ut.username', 'asc')

        ->get();

        return $query;

}





public static function userchangerequest_fetchData($table=null)
{

    $table = self::$UserDetails_Table;
    
    $query = DB::table($table . ' as ut')
        ->select('ut.deptuserid', 'ut.username', 'ut.usertamilname','dept.deptcode','dept.deptesname','dept.deptelname','r.regionename','r.regiontname','d.desigesname','di.distename','di.disttname','ut.statusflag')
        ->join(self::$UserChargeDetails_Table . ' as uc', 'uc.userid', '=', 'ut.deptuserid')
        ->join(self::$Dept_Table . ' as dept', 'dept.deptcode', '=', 'ut.deptcode')
        ->join(self::$ChargeDetails_Table . ' as c', 'c.chargeid', '=', 'uc.chargeid')
        ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
        ->join(self::$Designation_Table . ' as d', 'd.desigcode', '=', 'ut.desigcode')
        ->join(self::$regionTable . ' as r', 'r.regioncode', '=', 'c.regioncode')
        ->join(self::$District_Table . ' as di', 'di.distcode', '=', 'c.distcode')
       // ->where('ut.reservelist', 'N')
       // ->where('uc.statusflag', 'Y')
      //  ->where('ut.statusflag', 'Y')
        ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))
      
        ->distinct()
        ->orderBy('dept.deptcode', 'asc')
        ->orderBy('r.regionename', 'asc')
        ->orderBy('ut.username', 'asc')


        ->get();

        return $query;

}



public static function LagacyRegionwiseData($data)
{
    $deptcodes = is_array($data['deptcode'] ?? null)
        ? $data['deptcode']
        : [$data['deptcode'] ?? 'A'];

    $audityearcodes = is_array($data['audityearcode'] ?? null)
        ? $data['audityearcode']
        : [$data['audityearcode'] ?? 'A'];

     $statusflag = $data['statusflag'] ?? 'ALL';

    // dd($statusflag);

    $query = DB::table('audit.trans_followup as fp')
        ->join('audit.mst_institution as inst', 'inst.instid', '=', 'fp.instid')
        ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'inst.regioncode')
        ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'inst.deptcode')

       // ->join('audit.mst_auditperiod as ap', 'ap.auditperiodid', '=', 'fp.audityear')

       ->join('audit.mst_auditperiod as ap', function ($join) {
        $join->on(DB::raw("CAST(ap.auditperiodid AS TEXT)"), '=', DB::raw("(fp.audityear->>0)"))
             ->on('ap.deptcode', '=', 'dept.deptcode');
    })
        ->where('fp.paratype', 'L')
        ->whereIn('ap.lagacyyear', ['Y', 'B'])

        ->when($statusflag !== 'ALL', function ($query) use ($statusflag) {
            $query->where('fp.statusflag', $statusflag);
        })
        ->when($statusflag === 'ALL', function ($query) {
            $query->whereIn('fp.statusflag', ['Y', 'F']);
        })
        ->when(!(count($deptcodes) === 1 && $deptcodes[0] === 'A'), function ($query) use ($deptcodes) {
            $query->whereIn('inst.deptcode', $deptcodes);
        })
        ->when(!(count($audityearcodes) === 1 && $audityearcodes[0] === 'A'), function ($query) use ($audityearcodes) {
            $query->whereIn(DB::raw("(fp.audityear->>0)::int"), $audityearcodes);
        });

    if (count($deptcodes) === 1 && $deptcodes[0] === 'A') {
        //dd();

        $query->select(
            'inst.deptcode',
            'reg.regionename',
            'reg.regioncode',
            DB::raw("'A' as audityear"),
            DB::raw('COUNT(fp.followupid) as regioncount')
        )
        ->groupBy('reg.regionename', 'reg.regioncode', 'inst.deptcode')
        ->orderBy('reg.regionename');
    } else {
        $query->select(
            'inst.deptcode',
            'reg.regionename',
            'reg.regioncode',
            DB::raw('COUNT(fp.followupid) as regioncount')
        )
        ->groupBy('reg.regionename', 'reg.regioncode', 'inst.deptcode')
        ->orderBy('reg.regionename');
    }

    return $query->get();


}

   public static function LagacyDistrictwiseData($data)
    {
        $deptcodes = is_array($data['deptcode'] ?? null)
            ? $data['deptcode']
            : [$data['deptcode'] ?? 'A'];

        $regioncodes = is_array($data['regioncode'] ?? null)
            ? $data['regioncode']
            : [$data['regioncode'] ?? 'A'];

        $audityearcodes = is_array($data['audityearcode'] ?? null)
            ? $data['audityearcode']
            : [$data['audityearcode'] ?? 'A'];

        $statusflag = $data['statusflag'] ?? 'ALL';

        $query = DB::table('audit.trans_followup as fp')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'fp.instid')
            ->join('audit.mst_district as dist', 'dist.distcode', '=', 'inst.distcode')
            ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'inst.regioncode')
            ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'inst.deptcode')
            ->join('audit.mst_auditperiod as ap', function ($join) {
                $join->on(DB::raw("CAST(ap.auditperiodid AS TEXT)"), '=', DB::raw("(fp.audityear->>0)"))
                     ->on('ap.deptcode', '=', 'dept.deptcode');
            })
            ->where('fp.paratype', 'L')
            ->whereIn('ap.lagacyyear', ['Y', 'B'])
            ->when($statusflag !== 'ALL', function ($query) use ($statusflag) {
                $query->where('fp.statusflag', $statusflag);
            })
            ->when($statusflag === 'ALL', function ($query) {
                $query->whereIn('fp.statusflag', ['Y', 'F']);
            })
            ->when(!(count($deptcodes) === 1 && $deptcodes[0] === 'A'), function ($query) use ($deptcodes) {
                $query->whereIn('dept.deptcode', $deptcodes);
            })
            ->when(!(count($regioncodes) === 1 && $regioncodes[0] === 'A'), function ($query) use ($regioncodes) {
                $query->whereIn('inst.regioncode', $regioncodes);
            })
            ->when(!(count($audityearcodes) === 1 && $audityearcodes[0] === 'A'), function ($query) use ($audityearcodes) {
                $query->whereIn(DB::raw("(fp.audityear->>0)::int"), $audityearcodes);
            });

        $query->select(
            'dept.deptesname',
            'reg.regionename',
            'dept.deptcode',
            'reg.regioncode',
            'dist.distename',
            'dist.distcode',
            DB::raw('COUNT(fp.followupid) as districtcount')
        )
        ->groupBy('dept.deptcode', 'reg.regioncode', 'dist.distename', 'dist.distcode')
        ->orderBy('dist.distename');

        return $query->get();
    }


    public static function LagacyInstitutionwiseData($data)
    {
        $deptcodes = is_array($data['deptcode'] ?? null)
            ? $data['deptcode']
            : [$data['deptcode'] ?? 'A'];

        $regioncodes = is_array($data['regioncode'] ?? null)
            ? $data['regioncode']
            : [$data['regioncode'] ?? 'A'];

            $audityearcodes = is_array($data['audityearcode'] ?? null)
            ? $data['audityearcode']
            : [$data['audityearcode'] ?? 'A'];


        $distcode = $data['distcode'] ?? null;

        $statusflag = $data['statusflag'] ?? 'ALL';


        $query = DB::table('audit.trans_followup as fp')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'fp.instid')
            ->join('audit.mst_district as dist', 'dist.distcode', '=', 'inst.distcode')
            ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'inst.regioncode')
            ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'inst.deptcode')
            ->join('audit.mst_auditperiod as ap', function ($join) {
                $join->on(DB::raw("CAST(ap.auditperiodid AS TEXT)"), '=', DB::raw("(fp.audityear->>0)"))
                     ->on('ap.deptcode', '=', 'dept.deptcode');
            })
            ->where('fp.paratype', 'L')
            ->whereIn('ap.lagacyyear', ['Y', 'B'])
            ->when($statusflag !== 'ALL', function ($query) use ($statusflag) {
                $query->where('fp.statusflag', $statusflag);
            })
            ->when($statusflag === 'ALL', function ($query) {
                $query->whereIn('fp.statusflag', ['Y', 'F']);
            })
            ->when(!(count($deptcodes) === 1 && $deptcodes[0] === 'A'), function ($query) use ($deptcodes) {
                $query->whereIn('dept.deptcode', $deptcodes);
            })
            ->when(!(count($regioncodes) === 1 && $regioncodes[0] === 'A'), function ($query) use ($regioncodes) {
                $query->whereIn('inst.regioncode', $regioncodes);
            })
            ->when(!(count($audityearcodes) === 1 && $audityearcodes[0] === 'A'), function ($query) use ($audityearcodes) {
                $query->whereIn(DB::raw("(fp.audityear->>0)::int"), $audityearcodes);
            })
            ->when(!empty($distcode), function ($query) use ($distcode) {
                $query->where('dist.distcode', $distcode);
            });

        $query->select(


            'inst.instid',
            'inst.instename',
            DB::raw('COUNT(fp.followupid) as institutioncount')
        )
        ->groupBy(

            'inst.instid',

            'inst.instename'
        )
        ->orderBy('inst.instid');

        return $query->get();
    }
public static function fetch_slipdetails($data)
    {
        $instid = $data['instid'] ?? null;
        $audityearcode = $data['audityearcode'] ?? null;
        $statusflag = $data['statusflag'] ?? 'ALL';

        $query = DB::table('audit.trans_followup as fp')
            ->join('audit.mst_auditperiod as ap', function ($join) {
                $join->on(DB::raw("CAST(ap.auditperiodid AS TEXT)"), '=', DB::raw("(fp.audityear->>0)"));
            })
            ->where('fp.paratype', 'L');

        if (!empty($instid)) {
            $query->where('fp.instid', $instid);
        }

        if (!empty($audityearcode) && $audityearcode !== 'ALL' && $audityearcode !== 'A') {
            $query->whereIn(DB::raw("(fp.audityear->>0)::int"), (array)$audityearcode);
        }

        // Status flag condition
        if ($statusflag !== 'ALL') {
            $query->where('fp.statusflag', $statusflag);
        } else {
            $query->whereIn('fp.statusflag', ['Y', 'F']);
        }

        $query->select(
            'fp.followupid',
            'fp.audityear',
            DB::raw("CONCAT(ap.fromyear, ' - ', ap.toyear) as audit_period"),
            'fp.instid',
            'fp.slipdetails'
        )

        ->groupBy(

            'fp.followupid',
            'ap.fromyear',
            'ap.toyear',
            'fp.instid',
            'fp.slipdetails'
        )
        ->orderBy('fp.instid');

        return $query->get();
    }

 public static function fetch_allslipdetails($data)
    {
        $instid = $data['instid'];
        $followupid = $data['followupid'];

        // Call the Postgres function
        $results = DB::select(
            'SELECT * FROM audit.get_followup_details(:instid, :followupid)',
            [
                'instid' => $instid,
                'followupid' => $followupid
            ]
        );

        return $results;
    }


    public static function paramanagement_fetchData($data)
    {
        $deptcode = $data['deptcode'] ?? [];
        $regioncode = $data['regioncode'] ?? [];
        $distcode = $data['distcode'] ?? [];
        $instmappingcode = $data['instmappingcode'] ?? [];
        $parareportstatus = $data['parareportstatus'] ?? [];

        $query = DB::table('audit.trans_para as para')
            ->select(
                'd.deptcode',
                'd.deptesname',
                'c.distename',
                'r.regionename',
                'r.regiontname',
                'ins.instename',
                'para.instid',
                'e.processelname',
                'para.processcode',
                DB::raw("ap.fromyear || ' - ' || ap.toyear as audit_period"),
                'main.objectionename',
                'main.objectiontname',
                'sub.subobjectionename',
                'sub.subobjectiontname'
            )
            ->join('audit.mst_institution as ins', 'para.instid', '=', 'ins.instid')
            ->join('audit.trans_followup as fo', 'para.followupid', '=', 'fo.followupid')
            ->join('audit.mst_mainobjection as main', 'main.mainobjectionid', '=', 'fo.mainobjectionid')
            ->join('audit.mst_subobjection as sub', 'sub.subobjectionid', '=', 'fo.subobjectionid')
            ->join('audit.mst_district as c', 'ins.distcode', '=', 'c.distcode')
            ->join('audit.mst_region as r', 'r.regioncode', '=', 'ins.regioncode')
            ->join('audit.mst_dept as d', 'd.deptcode', '=', 'ins.deptcode')
            ->join('audit.mst_auditperiod as ap', function ($join) {
                $join->on(DB::raw("CAST(ap.auditperiodid AS TEXT)"), '=', DB::raw("(para.audityear->>0)"))
                     ->on('ap.deptcode', '=', 'd.deptcode');
            })
            ->join('audit.mst_process as e', 'e.processcode', '=', 'para.processcode');

        $query->where('para.statusflag', 'Y');

        $query->where('para.paratype', 'L');

        if (!empty($deptcode) && !in_array('A', $deptcode)) {
            $query->whereIn('d.deptcode', $deptcode);
        }

        if (!empty($regioncode) && !in_array('A', $regioncode)) {
            $query->whereIn('r.regioncode', $regioncode);
        }

        if (!empty($distcode) && !in_array('A', $distcode)) {
            $query->whereIn('c.distcode', $distcode);
        }

        if (!empty($instmappingcode) && !in_array('A', $instmappingcode)) {
            $query->whereIn('ins.instid', $instmappingcode);
        }

        if (!empty($parareportstatus) && !in_array('B', $parareportstatus)) {
            $query->whereIn('para.processcode', $parareportstatus);
        }

        $query->orderBy('d.deptcode')
            ->orderBy('para.processcode')
            ->orderBy('ap.fromyear')
            ->orderBy('c.distename')
            ->orderBy('ins.instename');

        return $query->get();
    }


public static function getidlereportdetails($data)
{
    return DB::select(
        'SELECT * FROM audit.get_idle_report_details(
            CAST(:p_deptcode AS character varying[]),
            CAST(:p_regioncode AS character varying[]),
            CAST(:p_distcode AS character varying[])
        )',
        [
            'p_deptcode' => (
                is_array($data['deptcode']) && count($data['deptcode']) > 0
            ) ? '{' . implode(',', $data['deptcode']) . '}' : '{}',

            'p_regioncode' => (
                is_array($data['regioncode']) && count($data['regioncode']) > 0
            ) ? '{' . implode(',', $data['regioncode']) . '}' : '{}',

            'p_distcode' => (
                is_array($data['distcode']) && count($data['distcode']) > 0
            ) ? '{' . implode(',', $data['distcode']) . '}' : '{}',
        ]
    );
}


public static function getlegacycount($data)
{
    $deptcode   = $data['deptcode'] ?? null;
    $regioncode = $data['regioncode'] ?? null;
    $distcode   = $data['distcode'] ?? null;


      $loginDate       = Carbon::today()->format('Y-m-d');
    $yesterday       = Carbon::parse($loginDate)->subDay()->format('Y-m-d');
    $beforeYesterday = Carbon::parse($loginDate)->subDays(2)->format('Y-m-d');

    // Base Query
    $query = DB::table('audit.trans_followup as fp')
        ->select(
            'dept.deptesname',
            'reg.regionename',
            'dist.distename',
            'dist.disttname',
            'reg.regiontname',
            'dept.deptcode',
            'reg.regioncode',
            'dist.distcode',

            DB::raw("SUM(CASE WHEN DATE(fp.createdon) = '$loginDate' THEN 1 ELSE 0 END) as today"),

            DB::raw("SUM(CASE WHEN DATE(fp.createdon) = '$yesterday' THEN 1 ELSE 0 END) as yesterday"),

            DB::raw("SUM(CASE WHEN DATE(fp.createdon) <= '$beforeYesterday' THEN 1 ELSE 0 END) as before_yesterday")
        )
	
        ->join('audit.mst_institution as inst', 'inst.instid', '=', 'fp.instid')
        ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'inst.deptcode')
        ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'inst.regioncode')
        ->join('audit.mst_district as dist', 'dist.distcode', '=', 'inst.distcode')
	->join('audit.mst_auditperiod as ap', function ($join) {
            $join->on(DB::raw("CAST(ap.auditperiodid AS TEXT)"), '=', DB::raw("(fp.audityear->>0)"))
                 ->on('ap.deptcode', '=', 'dept.deptcode');
        })
        ->where('fp.paratype', 'L')
                 ->whereIn('ap.lagacyyear', ['Y','B'])
         ->whereIn('fp.statusflag', ['Y','F']);



    // Filters
    if (!empty($deptcode) && !in_array('A', (array)$deptcode)) {
        $query->whereIn('dept.deptcode', (array)$deptcode);
    }
    if (!empty($regioncode) && !in_array('A', (array)$regioncode)) {
        $query->whereIn('reg.regioncode', (array)$regioncode);
    }
    if (!empty($distcode) && !in_array('A', (array)$distcode)) {
        $query->whereIn('dist.distcode', (array)$distcode);
    }

    return $query
        ->groupBy('dept.deptcode','reg.regioncode','dist.distcode','dept.deptesname', 'reg.regionename', 'dist.distename', 'dist.disttname','reg.regiontname')
        ->orderBy('dept.deptcode')
        ->orderBy('reg.regioncode')
        ->orderBy('dist.distcode')
        ->get();
}

public static function paracount_fetchData($data)
{
    $deptcode        = $data['deptcode'] ?? null;
    $regioncode      = $data['regioncode'] ?? null;
    $distcode        = $data['distcode'] ?? null;
    $instmappingcode = $data['instmappingcode'] ?? null;
    $audityearcode   = $data['audityearcode'] ?? null;

    //dd($audityearcode);

    $query = DB::table('audit.trans_para as a')
        ->select(
            'd.deptesname',
            'c.distename',
            'r.regionename',
            'r.regiontname',
            'c.disttname',
            'b.instename',
            'b.insttname',
            'e.processelname',
            DB::raw("ap.fromyear || ' - ' || ap.toyear AS audit_year_range"),
            'a.paranumber'
        )
        ->join('audit.mst_institution as b', 'a.instid', '=', 'b.instid')
        ->join('audit.mst_district as c', 'b.distcode', '=', 'c.distcode')
        ->join('audit.mst_region as r', 'r.regioncode', '=', 'b.regioncode')
        ->join('audit.mst_dept as d', 'b.deptcode', '=', 'd.deptcode')
        ->join('audit.mst_process as e', 'a.processcode', '=', 'e.processcode')
        ->join('audit.mst_auditperiod as ap', DB::raw('(a.audityear ->> 0)::int'), '=', 'ap.auditperiodid')
        ->orderBy('d.deptcode')
        ->orderBy('r.regioncode')
        ->orderBy('c.distcode')
        ->orderBy('audit_year_range')
        ->orderBy('a.paranumber');

    // Apply filters only if not containing 'A'
    if (!empty($deptcode) && !in_array('A', (array)$deptcode)) {
        $query->whereIn('d.deptcode', (array)$deptcode);
    }

    if (!empty($regioncode) && !in_array('A', (array)$regioncode)) {
        $query->whereIn('r.regioncode', (array)$regioncode);
    }

    if (!empty($distcode) && !in_array('A', (array)$distcode)) {
        $query->whereIn('c.distcode', (array)$distcode);
    }

    if (!empty($instmappingcode) && !in_array('A', (array)$instmappingcode)) {
        $query->whereIn('b.instid', (array)$instmappingcode);
    }

    if (!empty($audityearcode)) {
        $audityearcodeArr = (array)$audityearcode;

        if (!in_array('A', $audityearcodeArr)) {
            $query->whereIn(DB::raw("(a.audityear ->> 0)::int"), $audityearcodeArr);
        }
    }


    return $query->get();
}


 public static function paradetails($instid, $viewtype, $paraprocesscode)
    {
        try {

            $isProcessed = ($viewtype === 'processedparas');
            $isPending   = ($viewtype === 'pendingparas');

            $table = self::$transfollowup;
            if ($isProcessed) {
                $table = self::$transpara;
            }

            $query = DB::table($table);

            if ($isProcessed) {
                $query->join(self::$transfollowup.' as tf', 'tf.followupid', '=', $table.'.followupid');
                $mainTable = 'tf';
            } else {
                $mainTable = $table;
            }

            $selectFollowupId = $isProcessed ? 'tf.followupid' : $table.'.followupid';
            $selectCreatedOn  = $isProcessed ? 'tf.createdon'  : $table.'.createdon';

            if ($isProcessed) {
                $query->join(self::$ProcessFlag_Table.' as p', 'p.processcode', '=', $table.'.processcode');
            } else {
                $query->join(self::$ProcessFlag_Table.' as p', 'p.processcode', '=', $mainTable.'.processcode');
            }

            $query->join(self::$MajorObj_Table.' as m', 'm.mainobjectionid', '=', $mainTable.'.mainobjectionid')
                ->leftJoin(self::$SubObj_Table.' as s', 's.subobjectionid', '=', $mainTable.'.subobjectionid')
                ->leftJoin(DB::raw(self::$AuditPeriod_Table.' as ap'), function($join) use ($mainTable) {
                    $join->on(DB::raw('CAST('.$mainTable.'.audityear->>0 AS INTEGER)'), '=', 'ap.auditperiodid')
                        ->where('ap.statusflag', '=', 'Y');
                })
                ->leftJoin(self::$UserDetails_Table.' as dud', 'dud.deptuserid', '=', $mainTable.'.createdby')
                ->leftJoin(self::$severitytable.' as sev', 'sev.severitycode', '=', $mainTable.'.severitycode')
                ->where($mainTable.'.instid', $instid)
                ->select(
                    $mainTable.'.audityear',
                    $mainTable.'.paranumber',
                    $mainTable.'.instid',
                    'm.objectionename',
                    $mainTable.'.amtinvolved',
                    $mainTable.'.severitycode',
                    'sev.severityelname',
                    $mainTable.'.slipdetails',
                    'p.processelname',
                    'p.processcode',
                    'dud.username as auditorname',
                    $mainTable.'.liability',
                    'ap.fromyear',
                    'ap.toyear',
                    DB::raw("CONCAT(ap.fromyear, '-', ap.toyear) as auditperiod"),
                    DB::raw("CASE WHEN $mainTable.subobjectionid IS NOT NULL THEN s.subobjectionename ELSE 'N/A' END AS subobjectionename"),
                    DB::raw("TO_CHAR($selectCreatedOn, 'DD-MM-YYYY hh:MI AM') as createddate"),
                    DB::raw("$selectFollowupId as followupid"),
			DB::raw("CASE
                                WHEN '$viewtype' = 'processedparas'
                                    AND $table.processcode IN ('E','U','F','K')
                                THEN DATE_PART('day', NOW() - $table.updatedon)
                                ELSE NULL
                            END AS pendingdays")
                )
                ->orderBy($selectCreatedOn, 'desc');

            if ($isPending) {
                $query->whereNotIn($mainTable.'.followupid', function($q) {
                    $q->select('followupid')->from(self::$transpara);
                });
            }

            if ($isProcessed) {
                switch($paraprocesscode) {
                    case 'P':
                        $query->whereIn($table.'.processcode', ['E','U','F','K']);
                        break;
 		    case 'U':
                        $query->whereIn($table.'.processcode', ['E','U']);
                        break;
                    case 'F':
                        $query->where($table.'.processcode', 'F');
                        break;
                    case 'K':
                        $query->where($table.'.processcode', 'K');
                        break;
                    case 'A':
                        $query->where($table.'.processcode', 'A');
                        break;
                    case 'I':
                        $query->where($table.'.processcode', 'I');
                        break;
                }
            }

            return $query->get();

        } catch (\Exception $e) {
            \Log::error('Error in paradetails: '.$e->getMessage());
            return [];
        }
    }


	    public static function getParaDetailHistory($followupid)
    {
        $table = self::$transparahistory;

        $paradetails = DB::table($table)
            ->leftJoin(self::$ProcessFlag_Table.' as p', 'p.processcode', '=', $table.'.processcode')
            ->Join(self::$transfollowup.' as tf', 'tf.followupid', '=', $table.'.followupid')
            ->leftJoin(self::$MajorObj_Table.' as m', 'm.mainobjectionid', '=', 'tf.mainobjectionid')
            ->leftJoin(self::$SubObj_Table.' as s', function ($join) use ($table) {
                $join->on('s.subobjectionid', '=', 'tf.subobjectionid')
                    ->whereNotNull('tf.subobjectionid');
            })
            ->leftJoin(self::$AuditPlan_Table.' as ap', 'ap.auditplanid', '=', DB::raw("CAST(tf.auditplanid AS INT)"))
            ->leftJoin(self::$AuditeeUserDetail_Table.' as auditee', 'ap.instid', '=', 'auditee.instid')
            ->leftJoin(self::$Institution_Table.' as inst', 'ap.instid', '=', 'inst.instid')
            ->leftJoin(self::$UserDetails_Table.' as dud_forwardedby', 'dud_forwardedby.deptuserid', '=', $table.'.forwardedbyuserid')
            ->leftJoin(self::$AuditeeUserDetail_Table.' as aud_forwardedby', 'aud_forwardedby.auditeeuserid', '=', $table.'.forwardedbyuserid')
            ->leftJoin(self::$UserDetails_Table.' as dud_forwardedto', 'dud_forwardedto.deptuserid', '=', $table.'.forwardedtouserid')
            ->leftJoin(self::$AuditeeUserDetail_Table.' as aud_forwardedto', 'aud_forwardedto.auditeeuserid', '=', $table.'.forwardedtouserid')
 	    ->leftJoin(self::$UserChargeDetails_Table.' as user_charge', 'user_charge.userchargeid', '=', $table.'.forwardedbychargeid')
            ->leftJoin(self::$ChargeDetails_Table.' as aud_forwardedbycharge', 'aud_forwardedbycharge.chargeid', '=', 'user_charge.chargeid')
            ->select(
                $table.'.paraid',
                $table.'.transparahistoryid',
                $table.'.followupid',
                $table.'.paranumber',
                'm.objectionename',
                DB::raw("COALESCE(s.subobjectionename, 'N/A') AS subobjectionename"),
                $table.'.paranumber',
                $table.'.para_remarks',
                $table.'.usertypecode',
                $table.'.processcode',
                $table.'.rejoinderstatus',
                $table.'.rejoindercycle',
                $table.'.forwardedbyuserid',
                $table.'.forwardedtouserid',
                'p.processelname',

                DB::raw("TO_CHAR($table.forwardedon, 'DD-MM-YYYY HH12:MI AM') as forwardedon"),
                DB::raw("
                CASE
                    WHEN $table.actroleactioncode = 'I'
                        THEN CONCAT(aud_forwardedby.username, ' (Auditee)')
                    WHEN $table.actroleactioncode = 'A'
                        THEN CONCAT(dud_forwardedby.username, ' (PSA Auditor)')
                    WHEN $table.actroleactioncode = 'AD'
                        THEN CONCAT(dud_forwardedby.username, ' (PSA AD)')
                    ELSE
                        CONCAT(dud_forwardedby.username, ' (Auditor)')
                END AS forwardedby_username
            "),

            DB::raw("
                CASE
                    WHEN $table.forwardedtouserid IS NULL
                        THEN '-'
                    WHEN $table.actroleactioncode = 'I'
                        THEN CONCAT(dud_forwardedto.username, ' (PSA Auditor)')
                    WHEN $table.actroleactioncode = 'A'
                        THEN CONCAT(aud_forwardedto.username, ' (PSA AD)')
                    WHEN $table.actroleactioncode = 'AD'
                        THEN CONCAT(aud_forwardedto.username, ' (Auditee)')
                    ELSE
                        CONCAT(aud_forwardedto.username, ' (Auditor)')
                END AS forwardedto_username
            "),

            DB::raw("
                CASE
                    WHEN $table.actroleactioncode = 'I'
                        THEN 'Auditee'
                    ELSE aud_forwardedbycharge.chargedescription
                END AS issued_by
            ")            )

            ->where($table.'.followupid', $followupid)
            // ->whereIn($table.'.transstatus', ['I', 'A'])
            ->where($table.'.statusflag', 'Y')
            ->orderBy($table.'.transparahistoryid', 'asc')
            ->get();


        $historyFiles = [];
        foreach ($paradetails as $history) {
            $filesQuery = DB::table(self::$parafileupload.' as slip')
                ->join(self::$FileUploadDetail_Table.' as file_upload', 'file_upload.fileuploadid', '=', 'slip.fileuploadid')
                ->leftJoin(self::$UserDetails_Table.' as auditor_user', function($join) {
                    $join->on('auditor_user.deptuserid', '=', 'file_upload.uploadedby')
                        ->where('file_upload.usertypecode', 'A');
                })
                ->leftJoin(self::$AuditeeUserDetail_Table.' as auditee_user', function($join) {
                    $join->on('auditee_user.auditeeuserid', '=', 'file_upload.uploadedby')
                        ->where('file_upload.usertypecode', 'I');
                })
                ->select(
                    'slip.paraid',
                    'file_upload.fileuploadid',
                    'file_upload.filename',
                    'file_upload.filepath',
                    'file_upload.filesize',
                    'file_upload.uploadedby',
                    'file_upload.usertypecode',
                    'slip.processcode',
                    'slip.rejoinderstatus',
                    'slip.rejoindercycle',
                    DB::raw("TO_CHAR(file_upload.uploadedon, 'DD-MM-YYYY HH12:MI AM') as uploaded_date"),
                    DB::raw("CASE WHEN file_upload.usertypecode = 'A' THEN auditor_user.username WHEN file_upload.usertypecode = 'I' THEN auditee_user.username ELSE 'Unknown' END AS uploaded_by_username"),
                    DB::raw("CASE WHEN file_upload.usertypecode = 'A' THEN 'Auditor' WHEN file_upload.usertypecode = 'I' THEN 'Auditee' ELSE 'Unknown' END AS uploaded_by_role")
                )
                ->where('slip.paraid', $history->paraid);


            $filesQuery->where(function($query) use ($history) {
                if ($history->processcode === 'F') {
                    $query->where(function($q) use ($history) {
                        $q->where('slip.processcode', 'F')
                        ->orWhere('slip.processcode', 'T');
                    });

                    if ($history->rejoinderstatus === 'Y') {
                        $query->where(function($q) use ($history) {
                            $q->where(function($q2) use ($history) {
                                $q2->where('slip.rejoinderstatus', $history->rejoinderstatus)
                                ->where('slip.rejoindercycle', $history->rejoindercycle);
                            })->orWhere(function($q2) {
                                $q2->whereNull('slip.rejoinderstatus')
                                ->whereNull('slip.rejoindercycle');
                            });
                        });
                    }
                }
                elseif (in_array($history->processcode, ['A', 'X'])) {
                    $query->whereIn('slip.processcode', ['A', 'X', 'R'])
                        ->where('file_upload.usertypecode', 'A');
                }
                elseif (in_array($history->processcode, ['T', 'R', 'M'])) {
                    $query->where('slip.processcode', $history->processcode);
                }

                if ($history->rejoinderstatus !== null || $history->rejoindercycle !== null) {
                    $query->where(function($q) use ($history) {
                        $q->where(function($q2) use ($history) {
                            $q2->where('slip.rejoinderstatus', $history->rejoinderstatus)
                            ->where('slip.rejoindercycle', $history->rejoindercycle);
                        })->orWhere(function($q2) {
                            $q2->whereNull('slip.rejoinderstatus')
                            ->whereNull('slip.rejoindercycle');
                        });
                    });
                } else {
                    $query->whereNull('slip.rejoinderstatus')
                        ->whereNull('slip.rejoindercycle');
                }

                if ($history->processcode === 'F' && $history->rejoinderstatus === 'Y') {
                    $query->orWhere(function($q) {
                        $q->where('slip.processcode', 'R')
                        ->whereNull('slip.rejoinderstatus')
                        ->whereNull('slip.rejoindercycle')
                        ->where('file_upload.usertypecode', 'A');
                    });
                }
            });

            $files = $filesQuery->get();
            $historyFiles[$history->transparahistoryid] = $files;
        }

        $allFiles = DB::table(self::$parafileupload.' as slip')
            ->join(self::$FileUploadDetail_Table.' as file_upload', 'file_upload.fileuploadid', '=', 'slip.fileuploadid')
            ->leftJoin(self::$UserDetails_Table.' as auditor_user', function($join) {
                $join->on('auditor_user.deptuserid', '=', 'file_upload.uploadedby')
                    ->where('file_upload.usertypecode', 'A');
            })
            ->leftJoin(self::$AuditeeUserDetail_Table.' as auditee_user', function($join) {
                $join->on('auditee_user.auditeeuserid', '=', 'file_upload.uploadedby')
                    ->where('file_upload.usertypecode', 'I');
            })
            ->select(
                'slip.paraid',
                'file_upload.fileuploadid',
                'file_upload.filename',
                'file_upload.filepath',
                'file_upload.filesize',
                'file_upload.uploadedby',
                'file_upload.usertypecode',
                'slip.processcode',
                'slip.rejoinderstatus',
                'slip.rejoindercycle',
                DB::raw("TO_CHAR(file_upload.uploadedon, 'DD-MM-YYYY HH12:MI AM') as uploaded_date"),
                DB::raw("CASE WHEN file_upload.usertypecode = 'A' THEN auditor_user.username WHEN file_upload.usertypecode = 'I' THEN auditee_user.username ELSE 'Unknown' END AS uploaded_by_username"),
                DB::raw("CASE WHEN file_upload.usertypecode = 'A' THEN 'Auditor' WHEN file_upload.usertypecode = 'I' THEN 'Auditee' ELSE 'Unknown' END AS uploaded_by_role")
            )
            ->whereIn('slip.paraid', $paradetails->pluck('paraid'))
            ->get();




        return response()->json([
            'status' => 'success',
            'data' => $paradetails,
            'all_files' => $allFiles,
            'history_files' => $historyFiles,
        ]);
    }


public static function paradetailscount($deptcode = null, $regioncode = null, $distcode = null)
{
    $data = DB::table('audit.trans_followup as fp')
        ->join('audit.trans_para as tp', function ($join) {
            $join->on('tp.followupid', '=', 'fp.followupid')
                 ->on('tp.audityear', '=', 'fp.audityear')
                 ->whereNotNull('tp.paraid');
        })
        ->join('audit.mst_institution as inst', 'inst.instid', '=', 'fp.instid')
        ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'inst.deptcode')
        ->join('audit.mst_auditperiod as ap', function ($join) {
            $join->whereRaw("ap.auditperiodid::text = (fp.audityear->>0)")
                 ->whereColumn('ap.deptcode', 'dept.deptcode')
                 ->whereIn('ap.lagacyyear', ['Y', 'B']);
        })
        ->join('audit.mst_district as dist', 'dist.distcode', '=', 'inst.distcode')
        ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'inst.regioncode')
        ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'tp.forwardedtouserid')
        ->join('audit.userchargedetails as uc', 'uc.userchargeid', '=', 'tp.forwardedtouserchargeid')
        ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
        ->join('audit.mst_district as dist2', 'dist2.distcode', '=', 'c.distcode')
        ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'c.desigcode')
        ->where('fp.paratype', 'L')
        ->whereIn('fp.statusflag', ['Y', 'F'])
        ->when($deptcode, fn ($q) => $q->where('dept.deptcode', $deptcode))
        ->when($regioncode, fn ($q) => $q->where('reg.regioncode', $regioncode))
        ->when($distcode, fn ($q) => $q->where('dist.distcode', $distcode))
        ->groupBy(
            'reg.regionename',
            'desig.desigelname',
            'dist.distename',
            'dist2.distename'
        )
        ->select(
            'reg.regionename',
            'desig.desigelname as designation',
            'dist.distename',
            'dist2.distename as dist2ename',

            DB::raw('COUNT(DISTINCT fp.followupid) as overall_total'),

            DB::raw("COUNT(DISTINCT CASE WHEN tp.processcode IS NOT NULL
                THEN fp.followupid END) as processed_total"),

            DB::raw("COUNT(DISTINCT CASE WHEN tp.processcode IN ('E','U','F','K')
                THEN fp.followupid END) as pending_total"),

            DB::raw("COUNT(DISTINCT CASE WHEN tp.processcode IN ('E','U')
                THEN fp.followupid END) as pending_auditee"),

            DB::raw("COUNT(DISTINCT CASE WHEN tp.processcode = 'F'
                THEN fp.followupid END) as pending_psa_auditor"),

            DB::raw("COUNT(DISTINCT CASE WHEN tp.processcode = 'K'
                THEN fp.followupid END) as pending_psa_ad"),

            DB::raw("COUNT(DISTINCT CASE WHEN tp.processcode = 'A'
                THEN fp.followupid END) as dropped"),

            DB::raw("COUNT(DISTINCT CASE WHEN tp.processcode = 'I'
                THEN fp.followupid END) as rejected"),

            DB::raw("COUNT(DISTINCT CASE WHEN tp.processcode = 'X'
                THEN fp.followupid END) as other_status"),

            DB::raw("
                COALESCE(
                    JSONB_AGG(
                        DISTINCT JSONB_BUILD_OBJECT(
                            'paraid', tp.paraid,
                            'followupid', fp.followupid,
                            'processcode', tp.processcode
                        )
                    ) FILTER (WHERE tp.paraid IS NOT NULL),
                    '[]'::jsonb
                ) as para_details
            ")
        )
        ->get();

    return response()->json($data);
}

public static function paradetailscountFiltered($deptcode = null, $regioncode = null, $distcode = null, $dist2code, $paraprocesscode)
{
    return DB::table('audit.trans_followup as fp')
        ->join('audit.trans_para as tp', function ($join) {
            $join->on('tp.followupid', '=', 'fp.followupid')
                ->on('tp.audityear', '=', 'fp.audityear')
                ->whereNotNull('tp.paraid');
        })
        ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'tp.forwardedtouserid')
        ->join('audit.userchargedetails as uc', 'uc.userchargeid', '=', 'tp.forwardedtouserchargeid')
        ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
        ->join('audit.mst_district as dist', 'dist.distcode', '=', 'c.distcode')
        ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'c.regioncode')
        ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'c.deptcode')
        ->join('audit.mst_district as dist2', 'dist2.distcode', '=', 'c.distcode')
        ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'c.desigcode')
        ->where('fp.paratype', 'L')
        ->whereIn('fp.statusflag', ['Y', 'F'])
        ->when($deptcode, fn ($q) => $q->where('c.deptcode', $deptcode))
        ->when($regioncode, fn ($q) => $q->where('c.regioncode', $regioncode))
        ->when($distcode, fn ($q) => $q->where('c.distcode', $distcode))
        ->when($dist2code, fn ($q) => $q->where('dist2.distcode', $dist2code))
        ->when($paraprocesscode, function ($q) use ($paraprocesscode) {
            if ($paraprocesscode === 'all') {
                $q->whereIn('tp.processcode', ['F', 'K']);
            } else {
                $q->where('tp.processcode', $paraprocesscode);
            }
        })
        ->groupBy(
            'du.username',
            'desig.desigelname',
            'dist.distename',
            'dist2.distename',
            'reg.regionename'
        )
        ->select(
            'du.username as auditorname',
            'desig.desigelname as designation',
            'dist.distename',
            'dist2.distename as dist2ename',
            'reg.regionename',
            DB::raw("
                COALESCE(
                    JSONB_AGG(
                        DISTINCT JSONB_BUILD_OBJECT(
                            'paranumber', tp.paranumber,
                            'processcode', tp.processcode,
                            'received_on', TO_CHAR(tp.updatedon, 'DD-MM-YYYY HH:MI AM'),
                            'pending_days', (
                                SELECT COUNT(*)::integer
                                FROM generate_series(
                                    DATE(tp.updatedon) + INTERVAL '1 day',
                                    CURRENT_DATE,
                                    INTERVAL '1 day'
                                ) AS dates
                                WHERE EXTRACT(DOW FROM dates) NOT IN (0, 6)
                                AND dates::date NOT IN (
                                    SELECT holiday_date
                                    FROM audit.mst_holiday
                                    WHERE statusflag = 'Y'
                                )
                            )
                        )
                    ) FILTER (WHERE tp.paraid IS NOT NULL),
                    '[]'::jsonb
                ) as para_details
            ")
        )
        ->get();
}
public static function unapprovedauditparapendingdetails($data)
{
    $deptcode       = $data['deptcode'] ?? null;
    $regioncode     = $data['regioncode'] ?? null;
    $distcode       = $data['distcode'] ?? null;
    $pendingparaslip = $data['pendingparaslip'] ?? null;

    // Set schema

    $query = DB::table('audit.trans_para as tp')
        ->select([
            'dept.deptcode',
            'dept.deptesname',
            'reg.regionename',
            'dist.distename',
            'fp.parano',
            'fp.slipdetails as Gist_of_Para',
            DB::raw('auditinfo.audit_period as Audit_Year'),
            'du.username as Auditor_Name',
            'dist2.distename as Auditor_District',
            'du.ifhrmsno as IFHRMS_No',
            'desig.desigelname as Designation',
            'mra.roleactionelname as Role',
            DB::raw("to_char(tp.updatedon, 'DD-MM-YYYY') as Received_Date"),
            DB::raw('wd.pending_days as Pending_Days')
        ])

        /* ---------- Pending days (LATERAL) ---------- */
        ->join(DB::raw("
            LATERAL (
                SELECT COUNT(*) AS pending_days
                FROM generate_series(
                    tp.updatedon::date + INTERVAL '1 day',
                    current_date,
                    INTERVAL '1 day'
                ) gs(d)
                WHERE EXTRACT(ISODOW FROM gs.d) BETWEEN 1 AND 5
                AND NOT EXISTS (
                    SELECT 1
                    FROM audit.mst_holiday h
                    WHERE h.holiday_date::date = gs.d::date
                )
            ) wd
        "), DB::raw('true'), DB::raw('true'))

        /* ---------- Tables ---------- */
        ->join('audit.trans_followup as fp', 'fp.followupid', '=', 'tp.followupid')

        ->join(DB::raw("
            LATERAL (
                SELECT string_agg(
                    DISTINCT CONCAT(yr.fromyear, ' - ', yr.toyear), ', '
                ) AS audit_period
                FROM jsonb_array_elements_text(tp.audityear) AS ay(auditperiodid)
                JOIN audit.mst_auditperiod yr
                    ON yr.auditperiodid = ay.auditperiodid::int
            ) auditinfo
        "), DB::raw('true'), DB::raw('true'))

        ->join('audit.mst_institution as inst', 'inst.instid', '=', 'tp.instid')
        ->join('audit.mst_dept as dept', 'inst.deptcode', '=', 'dept.deptcode')
        ->join('audit.mst_region as reg', 'inst.regioncode', '=', 'reg.regioncode')
        ->join('audit.mst_district as dist', 'inst.distcode', '=', 'dist.distcode')

        ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'tp.forwardedtouserid')
        ->join('audit.userchargedetails as uc', 'uc.userchargeid', '=', 'tp.forwardedtouserchargeid')
        ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
        ->join('audit.mst_district as dist2', 'dist2.distcode', '=', 'c.distcode')
        ->join('audit.rolemapping as rol', 'rol.rolemappingid', '=', 'c.rolemappingid')
        ->join('audit.mst_roleaction as mra', 'mra.roleactioncode', '=', 'rol.roleactioncode')
        ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'c.desigcode')

        /* ---------- Conditions ---------- */
        ->whereIn('tp.processcode', ['K', 'F']);

        if (!empty($pendingparaslip) && is_numeric($pendingparaslip)) {

            $pendingparaslip = (int) $pendingparaslip;

            if ($pendingparaslip == 5) {
                $query->whereBetween('wd.pending_days', [1, 5]);

            // } elseif ($pendingparaslip == 10) {
            //     $query->whereBetween('wd.pending_days', [6, 10]);

            // } elseif ($pendingparaslip == 15) {
            //     $query->whereBetween('wd.pending_days', [11, 15]);

            // } elseif ($pendingparaslip == 20) {
            //     $query->whereBetween('wd.pending_days', [16, 20]);

             }
            elseif ($pendingparaslip == 6) {
                $query->where('wd.pending_days', '>', 5);
            }
        }

    /* ---------- Filters ---------- */
    if (!empty($deptcode) && !in_array('A', (array)$deptcode)) {
        $query->whereIn('dept.deptcode', (array)$deptcode);
    }

    if (!empty($regioncode) && !in_array('A', (array)$regioncode)) {
        $query->whereIn('reg.regioncode', (array)$regioncode);
    }

    if (!empty($distcode) && !in_array('A', (array)$distcode)) {
        $query->whereIn('dist.distcode', (array)$distcode);
    }

    /* ---------- Order ---------- */
    $query->orderBy('dept.deptcode')
        ->orderBy('reg.regionename')
        ->orderBy('dist.distename')
        ->orderBy('inst.instename')
        ->orderBy('fp.parano')
        ->orderBy('tp.paraid');

    return $query->get();
}




public static function fetch_allmoneyandnondeptails($data)
{

    $auditPeriodSubquery = DB::table('audit.yearcode_mapping as ycm')
    ->join('audit.mst_auditperiod as ap', 'ap.auditperiodid', '=', 'ycm.yearselected')
    ->select(
        'ycm.auditplanid',
        DB::raw("string_agg(CONCAT(fromyear, ' - ', toyear), ', ' ORDER BY fromyear) as audit_period"),
        DB::raw("string_agg(yearselected::text, ', ') as yearkeys")
    )
    ->where('ap.financestatus', 'N')
    ->where('ycm.statusflag', 'Y')
    ->where('ap.statusflag', 'Y')
    ->groupBy('ycm.auditplanid');



    return DB::table('audit.trans_auditslip as s')
        ->join('audit.auditplan as plan', 'plan.auditplanid', '=', 's.auditplanid')

        ->join('audit.mst_mainobjection as main', 'main.mainobjectionid', '=', 's.mainobjectionid')
        ->join('audit.mst_subobjection as sub', 'sub.subobjectionid', '=', 's.subobjectionid')
        ->leftJoin('audit.auditeescheme as scheme', 'scheme.auditeeschemecode', '=', 's.auditeeschemecode')
        ->join('audit.mst_irregularities as ir', 'ir.irregularitiescode', '=', 's.irregularitiescode')
        ->join('audit.mst_irregularitiescategory as cat', 'cat.irregularitiescatcode', '=', 's.irregularitiescatcode')
        ->leftJoin('audit.slipfileupload as file', 's.auditslipid', '=', 'file.auditslipid')
        ->leftJoinSub($auditPeriodSubquery, 'aps', function ($join) {
            $join->on('aps.auditplanid', '=', 'plan.auditplanid');
        })
        ->leftJoin('audit.fileuploaddetail as upload', 'upload.fileuploadid', '=', 'file.fileuploadid')
        ->join('audit.mst_irregularitiessubcategory as subcat', 'subcat.irregularitiessubcatcode', '=', 's.irregularitiessubcatcode')
        ->join('audit.deptuserdetails as cb', 'cb.deptuserid', '=', 's.createdby')
        ->join('audit.mst_institution as ins', 'ins.instid', '=', 'plan.instid')
        ->leftJoin ('audit.liability AS l', 'l.auditslipid', '=' , 's.auditslipid')
        ->where('s.auditslipid', $data['auditslipid'])

        ->select([
            'main.objectionename',
            'sub.subobjectionename',
            's.mainslipnumber',
            's.processcode',
            's.mainobjectionid',
            's.subobjectionid',
            's.amtinvolved',
            's.severitycode',
            's.liability',
            'ins.instename',
            'cat.irregularitiescatelname',
            'subcat.irregularitiessubcatelname',
            's.slipdetails',
            's.schemastatus',
            's.auditeeschemecode',
            'ir.irregularitieselname',
            's.irregularitiescode',
            's.irregularitiescatcode',
            's.irregularitiessubcatcode',
            DB::raw("COALESCE(s.remarks::json->>'content', '') AS remarks"),
            'cb.username as createdbyusername',
            's.updatedon',
             'aps.audit_period',
             'aps.yearkeys',

            // ★ Correct File Aggregation ★
            DB::raw("
                STRING_AGG(
                    DISTINCT CASE
                        WHEN upload.statusflag = 'Y' THEN
                            CONCAT(
                                COALESCE(upload.filename, ''), '-',
                                COALESCE(upload.filepath, ''), '-',
                                COALESCE(upload.filesize::text, ''), '-',
                                COALESCE(upload.fileuploadid::text, '')
                            )
                        ELSE NULL
                    END, ','
                ) AS auditorfileupload
            "),

            DB::raw("
                STRING_AGG(
                    CONCAT(
                        COALESCE(l.notype::text, ''),
                        '-', COALESCE(l.liabilitygpfno::text, ''),
                        '-', COALESCE(l.liabilityname::text, ''),
                        '-', COALESCE(l.liabilitydesignation::text, ''),
                        '-', COALESCE(l.liabilityamount::text, '')
                    ), ','
                ) FILTER (WHERE l.statusflag = 'Y') AS liabilitydel
            "),


        ])
        ->groupBy(
            'main.objectionename',
            'sub.subobjectionename',
            's.mainslipnumber',
            's.processcode',
            's.mainobjectionid',
            's.subobjectionid',
            's.amtinvolved',
            's.severitycode',
            's.liability',
            'ins.instename',
            'cat.irregularitiescatelname',
            'subcat.irregularitiessubcatelname',
            's.slipdetails',
            's.schemastatus',
            's.auditeeschemecode',
            'ir.irregularitieselname',
            's.irregularitiescode',
            's.irregularitiescatcode',
            's.irregularitiessubcatcode',
            DB::raw("COALESCE(s.remarks::json->>'content', '')"),
            'cb.username',
            's.updatedon',
            'aps.audit_period',
            'aps.yearkeys'
        )
        ->get();
}


public static function getmoneyandnonmoneyparadetails($data)
{
    return DB::cursor(
        'SELECT * FROM audit.get_moneyandnonmonetpara_details(
            CAST(? AS character varying[]),
            CAST(? AS character varying[]),
            CAST(? AS character varying[]),
            CAST(? AS integer[]),
            CAST(? AS integer[]),
            CAST(? AS character varying[]),
            CAST(? AS integer[]),
            CAST(? AS character varying),
            CAST(? AS character varying)
        )',
        [
            // deptcode
            (is_array($data['deptcode']) && count($data['deptcode']) > 0)
                ? '{' . implode(',', $data['deptcode']) . '}'
                : '{}',

            // regioncode
            (is_array($data['regioncode']) && count($data['regioncode']) > 0)
                ? '{' . implode(',', $data['regioncode']) . '}'
                : '{}',

            // districtcode
            (is_array($data['distcode']) && count($data['distcode']) > 0)
                ? '{' . implode(',', $data['distcode']) . '}'
                : '{}',

            // instid
            (is_array($data['instmappingcode']) && count($data['instmappingcode']) > 0)
                ? '{' . implode(',', array_map('intval', $data['instmappingcode'])) . '}'
                : '{}',

            // auditquarter
            (is_array($data['auditquarter']) && count($data['auditquarter']) > 0)
                ? '{' . implode(',', $data['auditquarter']) . '}'
                : '{}',

            // catcode
            (is_array($data['catcode']) && count($data['catcode']) > 0)
                ? '{' . implode(',', $data['catcode']) . '}'
                : '{}',

            // subcatcode
            (is_array($data['subcatcode']) && count($data['subcatcode']) > 0)
                ? '{' . implode(',', array_map('intval', $data['subcatcode'])) . '}'
                : '{}',

            // money / non-money flag
            $data['financialyear'] ?? null,

            $data['moneyandnonmoney'] ?? null,
        ]
    );
}



public static function fetch_apmsdetails($data)
{
 $fromdateforapms = $data['fromdateforapms'] ?? null;
        $todateforapms = $data['todateforapms'] ?? null;

        $fromDate = null;
        $toDate   = null;

        if ($fromdateforapms && $todateforapms) {
            $fromDate = Carbon::createFromFormat('d-m-Y', $fromdateforapms)->toDateString();
            $toDate   = Carbon::createFromFormat('d-m-Y', $todateforapms)->toDateString();
        }
	

        $apmsstatus = $data['apmsstatuscode'] ?? null;
        $remarksOnly = (!empty($data['remarks']) && $data['remarks'] === 'Y');
        $paraid = $data['paraid'] ?? null;

        $query = DB::table('audit.trans_para as tp')
            ->select(
                $remarksOnly
                    ? [
                        // ✅ FULL DATA + remarks + PSA details
                        'dept.deptesname',
                        'reg.regionename',
                        'reg.regiontname',
                        'dist.distename',
                        'dist.disttname',
                        'cat.catename',
                        'cat.cattname',
                        'tp.paraid',

                        'inst.instename',
                        'inst.insttname',
                        'subcat.subcatename',
                        'subcat.subcattname',
                        'main.objectionename',
                        'main.objectiontname',
                        'tp.processcode',
                        'sub.subobjectionename',
                        'sub.subobjectiontname',

                        DB::raw("(
                    SELECT string_agg(DISTINCT CONCAT(yr.fromyear,' - ',yr.toyear), ', ')
                    FROM jsonb_array_elements_text(tp.audityear) AS ay(auditperiodid)
                    JOIN audit.mst_auditperiod yr
                        ON yr.auditperiodid = ay.auditperiodid::int
                ) AS audit_year"),

                        'fp.parano as para_number',
                        DB::raw('fp.amtinvolved as amount_involved'),
                        'sev.severityelname as severity',
                        'fp.slipdetails as gist_of_para',

                        // ✅ Only extra when remarks = 'Y'
                        DB::raw("regexp_replace(
                    regexp_replace(fp.remarks::json->>'content','<[^>]*>','','g'),
                    '&[a-zA-Z0-9#]+;','',
                    'g'
                ) as legacy_remarks"),

                        DB::raw("(
                    SELECT string_agg(
                        'Role: ' ||
                        CASE hist.actroleactioncode
                            WHEN 'I'  THEN 'Auditee'
                            WHEN 'A'  THEN 'PSA Auditor'
                            WHEN 'AD' THEN 'PSA AD'
                            ELSE 'Unknown'
                        END ||
                        ' | Action: ' || COALESCE(acp.actionename, '-') ||
                        ' | Date: ' || to_char(hist.createdon, 'DD-MM-YYYY HH24:MI') ||
                        ' | Remarks: ' ||
                        regexp_replace(
                            regexp_replace(hist.para_remarks->>'content','<[^>]*>','','g'),
                            '&[a-zA-Z0-9#]+;','',
                            'g'
                        ),
                    ' ||| ' ORDER BY hist.transparahistoryid
                    )
                    FROM audit.historytrans_para hist
                     LEFT JOIN audit.mst_actionsonpara acp
                         ON acp.actioncode = hist.actioncode
                    WHERE hist.paraid = tp.paraid
                      AND hist.statusflag = 'Y'
                ) AS para_remarks"),

                        DB::raw("(
                    SELECT 'Name: ' || du.username || ', District: ' || dis.distename || ', Action: '  || COALESCE(acp.actionename, '-')
                    FROM audit.historytrans_para hist
                    JOIN audit.userchargedetails uc ON uc.userchargeid = hist.forwardedtochargeid
                    JOIN audit.chargedetails c ON c.chargeid = uc.chargeid
                    JOIN audit.deptuserdetails du ON du.deptuserid = uc.userid
                    JOIN audit.mst_district dis ON dis.distcode = c.distcode
                     LEFT JOIN audit.mst_actionsonpara acp
                         ON acp.actioncode = hist.actioncode
                    WHERE hist.paraid = tp.paraid
                      AND hist.actroleactioncode = 'I'
                      AND hist.statusflag = 'Y'
                    ORDER BY hist.createdon DESC
                    LIMIT 1
                ) AS psa_auditor_details"),

                        DB::raw("(
                    SELECT 'Name: ' || du.username || ', District: ' || dis.distename
                    FROM audit.historytrans_para hist
                    JOIN audit.userchargedetails uc ON uc.userchargeid = hist.forwardedtochargeid
                    JOIN audit.chargedetails c ON c.chargeid = uc.chargeid
                    JOIN audit.deptuserdetails du ON du.deptuserid = uc.userid
                    JOIN audit.mst_district dis ON dis.distcode = c.distcode
                    WHERE hist.paraid = tp.paraid
                      AND hist.actroleactioncode = 'A'
                      AND hist.statusflag = 'Y'
                    ORDER BY hist.createdon DESC
                    LIMIT 1
                ) AS psa_ad_details")
                    ]
                    : [
                        // ✅ Normal select when remarks != 'Y'
                        'dept.deptesname',
                        'reg.regionename',
                        'reg.regiontname',
                        'dist.distename',
                        'dist.disttname',
                        'cat.catename',
                        'cat.cattname',
                        'tp.paraid',
                        'inst.instename',
                        'inst.insttname',
                        'subcat.subcatename',
                        'subcat.subcattname',
                        'main.objectionename',
                        'main.objectiontname',
                        'tp.processcode',
                        'sub.subobjectionename',
                        'sub.subobjectiontname',

                        DB::raw("(
                    SELECT string_agg(DISTINCT CONCAT(yr.fromyear,' - ',yr.toyear), ', ')
                    FROM jsonb_array_elements_text(tp.audityear) AS ay(auditperiodid)
                    JOIN audit.mst_auditperiod yr
                        ON yr.auditperiodid = ay.auditperiodid::int
                ) AS audit_year"),

                        'fp.parano as para_number',
                        DB::raw('fp.amtinvolved as amount_involved'),
                        'sev.severityelname as severity',
                        'fp.slipdetails as gist_of_para',
                    ]
            )

            // ------------------------------
            // JOINS
            // ------------------------------
            ->join('audit.trans_followup as fp', 'fp.followupid', '=', 'tp.followupid')
            ->join('audit.mst_mainobjection as main', 'main.mainobjectionid', '=', 'fp.mainobjectionid')
            ->join('audit.mst_subobjection as sub', 'sub.subobjectionid', '=', 'fp.subobjectionid')
            ->join('audit.mst_severity as sev', 'sev.severitycode', '=', 'fp.severitycode')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'tp.instid')
            ->join('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', 'inst.catcode')
            ->leftJoin('audit.mst_auditeeins_subcategory as subcat', 'subcat.auditeeins_subcategoryid', '=', 'inst.subcatid')
            ->join('audit.mst_dept as dept', 'inst.deptcode', '=', 'dept.deptcode')
            ->join('audit.mst_region as reg', 'inst.regioncode', '=', 'reg.regioncode')
            ->join('audit.mst_district as dist', 'inst.distcode', '=', 'dist.distcode');


if ($fromDate && $toDate) {
            $query->whereBetween(
                DB::raw('DATE(tp.createdon)'),
                [$fromDate, $toDate]
            );
        }


        $applyFilter = function ($key, $column) use (&$query, $data) {
            if (!empty($data[$key]) && !in_array('A', (array)$data[$key])) {
                $query->whereIn($column, (array)$data[$key]);
            }
        };

        $applyFilter('deptcode',   'dept.deptcode');
        $applyFilter('regioncode', 'reg.regioncode');
        $applyFilter('distcode',   'dist.distcode');
        $applyFilter('catcode',         'cat.catcode');
        $applyFilter('subcatcode',      'subcat.auditeeins_subcategoryid');
        $applyFilter('instmappingcode', 'inst.instid');

        // ------------------------------
        // PROCESSCODE FILTER
        // ------------------------------
        switch ($apmsstatus) {
            case 'B':
                $apmsstatus = ['A', 'U', 'I'];
                break;
            case 'A':
            case 'U':
            case 'I':
                $apmsstatus = [$apmsstatus];
                break;
            default:
                $apmsstatus = null;
        }

        if ($apmsstatus) {
            $query->whereIn('tp.processcode', $apmsstatus);
        }

        // ------------------------------
        // AUDITYEAR FILTER
        // ------------------------------
        if (!empty($data['audityearcode']) && !in_array('A', (array)$data['audityearcode'])) {
            $query->whereExists(function ($q) use ($data) {
                $q->select(DB::raw(1))
                    ->from(DB::raw("jsonb_array_elements_text(tp.audityear) AS ay(auditperiodid)"))
                    ->join('audit.mst_auditperiod as yr', 'yr.auditperiodid', '=', DB::raw('ay.auditperiodid::int'))
                    ->whereIn(DB::raw('yr.auditperiodid'), $data['audityearcode']);
            });
        }

        if ($remarksOnly && !empty($data['paraid'])) {
            $query->where('tp.paraid', $data['paraid']);
        }

        // ------------------------------
        // ORDER
        // ------------------------------
        $query->orderBy('dept.deptcode')
            ->orderBy('reg.regioncode')
            ->orderBy('cat.catcode')
            ->orderByRaw("
            CASE tp.processcode
                WHEN 'A' THEN 1
                WHEN 'U' THEN 2
                WHEN 'I' THEN 3
                ELSE 4
            END
        ")
            ->orderBy('fp.parano')
            ->orderBy('tp.paraid');

	// return $query->get();
	  return $query->cursor();
    }
  public static function quarterfetch()
        {
            return DB::table('audit.auditplan as a')
                ->leftjoin('audit.mst_dept as d', 'd.nextquarter', '=', 'a.auditquartercode')
                ->select('a.auditquartercode')
                ->distinct()
                ->orderBy('a.auditquartercode', 'desc')
                ->pluck('a.auditquartercode');
        }

public static function getConvertedparadetails($data)
{
    return DB::cursor(
        'SELECT * FROM audit.get_converttopata_details(
            CAST(? AS character varying[]),
            CAST(? AS character varying[]),
            CAST(? AS character varying[]),
            CAST(? AS integer[]),
            CAST(? AS integer[]),
            CAST(? AS character varying[]),
            CAST(? AS character varying),
            CAST(? AS integer[])
        )',
        [
            // deptcode
            (is_array($data['deptcode']) && count($data['deptcode']) > 0)
                ? '{' . implode(',', $data['deptcode']) . '}'
                : '{}',

            // regioncode
            (is_array($data['regioncode']) && count($data['regioncode']) > 0)
                ? '{' . implode(',', $data['regioncode']) . '}'
                : '{}',

            // districtcode
            (is_array($data['distcode']) && count($data['distcode']) > 0)
                ? '{' . implode(',', $data['distcode']) . '}'
                : '{}',

            // instid
            (is_array($data['instmappingcode']) && count($data['instmappingcode']) > 0)
                ? '{' . implode(',', array_map('intval', $data['instmappingcode'])) . '}'
                : '{}',

            // auditquarter
            (is_array($data['auditquarter']) && count($data['auditquarter']) > 0)
                ? '{' . implode(',', $data['auditquarter']) . '}'
                : '{}',

            // catcode
            (is_array($data['catcode']) && count($data['catcode']) > 0)
                ? '{' . implode(',', $data['catcode']) . '}'
                : '{}',

            $data['financialyear'] ?? null,

            // subcatcode
            (is_array($data['subcatcode']) && count($data['subcatcode']) > 0)
                ? '{' . implode(',', array_map('intval', $data['subcatcode'])) . '}'
                : '{}',
        ]
    );
}



public static function fetch_converttopara($data)
{
    return DB::table('audit.trans_auditslip as slip')
        ->join('audit.mst_mainobjection as main', 'main.mainobjectionid', '=', 'slip.mainobjectionid')
        ->join('audit.mst_subobjection as sub', 'sub.subobjectionid', '=', 'slip.subobjectionid')
        ->select(
            'slip.auditslipid',
            'slip.auditscheduleid',
            'slip.auditplanid',
            'slip.mainobjectionid',
            'slip.subobjectionid',
            'main.objectionename',
            'main.objectiontname',
            'sub.subobjectionename',
            'sub.subobjectiontname',
            'slip.slipdetails',
        )
        ->where('slip.auditscheduleid', $data['auditscheduleid'])
        ->where('slip.auditplanid', $data['auditplanid'])
        ->where('slip.createdby', $data['createdby'])
        ->where('slip.processcode', 'X')
        ->orderBy('slip.auditslipid')
        ->get();
}



public static function fetch_allparadeptails($data)
{

    $auditPeriodSubquery = DB::table('audit.yearcode_mapping as ycm')
    ->join('audit.mst_auditperiod as ap', 'ap.auditperiodid', '=', 'ycm.yearselected')
    ->select(
        'ycm.auditplanid',
        DB::raw("string_agg(CONCAT(fromyear, ' - ', toyear), ', ' ORDER BY fromyear) as audit_period"),
        DB::raw("string_agg(yearselected::text, ', ') as yearkeys")
    )
    ->where('ap.financestatus', 'N')
    ->where('ycm.statusflag', 'Y')
    ->where('ap.statusflag', 'Y')
    ->groupBy('ycm.auditplanid');



    return DB::table('audit.trans_auditslip as s')
        ->join('audit.auditplan as plan', 'plan.auditplanid', '=', 's.auditplanid')

        ->join('audit.mst_mainobjection as main', 'main.mainobjectionid', '=', 's.mainobjectionid')
        ->join('audit.mst_subobjection as sub', 'sub.subobjectionid', '=', 's.subobjectionid')
        ->leftJoin('audit.auditeescheme as scheme', 'scheme.auditeeschemecode', '=', 's.auditeeschemecode')
        ->join('audit.mst_irregularities as ir', 'ir.irregularitiescode', '=', 's.irregularitiescode')
        ->join('audit.mst_irregularitiescategory as cat', 'cat.irregularitiescatcode', '=', 's.irregularitiescatcode')
        ->leftJoin('audit.slipfileupload as file', 's.auditslipid', '=', 'file.auditslipid')
        ->leftJoinSub($auditPeriodSubquery, 'aps', function ($join) {
            $join->on('aps.auditplanid', '=', 'plan.auditplanid');
        })
        ->leftJoin('audit.fileuploaddetail as upload', 'upload.fileuploadid', '=', 'file.fileuploadid')
        ->join('audit.mst_irregularitiessubcategory as subcat', 'subcat.irregularitiessubcatcode', '=', 's.irregularitiessubcatcode')
        ->join('audit.deptuserdetails as cb', 'cb.deptuserid', '=', 's.createdby')
        ->join('audit.mst_institution as ins', 'ins.instid', '=', 'plan.instid')
        ->leftJoin ('audit.liability AS l', 'l.auditslipid', '=' , 's.auditslipid')
        ->where('s.mainobjectionid', $data['mainobjectionid'])
        ->where('s.subobjectionid', $data['subobjectionid'])
        ->where('s.auditslipid', $data['auditslipid'])

        ->select([
            'main.objectionename',
            'sub.subobjectionename',
            's.mainobjectionid',
            's.subobjectionid',
            's.amtinvolved',
            's.mainslipnumber',
            's.processcode',
            's.severitycode',
            's.liability',
            'ins.instename',
            'cat.irregularitiescatelname',
            'subcat.irregularitiessubcatelname',
            's.slipdetails',
            's.schemastatus',
            's.auditeeschemecode',
            'ir.irregularitieselname',
            's.irregularitiescode',
            's.irregularitiescatcode',
            's.irregularitiessubcatcode',
            DB::raw("COALESCE(s.remarks::json->>'content', '') AS remarks"),
            'cb.username as createdbyusername',
            's.updatedon',
             'aps.audit_period',
             'aps.yearkeys',

            DB::raw("
                STRING_AGG(
                    DISTINCT CASE
                        WHEN upload.statusflag = 'Y' THEN
                            CONCAT(
                                COALESCE(upload.filename, ''), '-',
                                COALESCE(upload.filepath, ''), '-',
                                COALESCE(upload.filesize::text, ''), '-',
                                COALESCE(upload.fileuploadid::text, '')
                            )
                        ELSE NULL
                    END, ','
                ) AS auditorfileupload
            "),

            DB::raw("
                STRING_AGG(
                    CONCAT(
                        COALESCE(l.notype::text, ''),
                        '-', COALESCE(l.liabilitygpfno::text, ''),
                        '-', COALESCE(l.liabilityname::text, ''),
                        '-', COALESCE(l.liabilitydesignation::text, ''),
                        '-', COALESCE(l.liabilityamount::text, '')
                    ), ','
                ) FILTER (WHERE l.statusflag = 'Y') AS liabilitydel
            "),


        ])
        ->groupBy(
            'main.objectionename',
            'sub.subobjectionename',
            's.mainobjectionid',
            's.subobjectionid',
            's.amtinvolved',
            's.mainslipnumber',
            's.processcode',
            's.severitycode',
            's.liability',
            'ins.instename',
            'cat.irregularitiescatelname',
            'subcat.irregularitiessubcatelname',
            's.slipdetails',
            's.schemastatus',
            's.auditeeschemecode',
            'ir.irregularitieselname',
            's.irregularitiescode',
            's.irregularitiescatcode',
            's.irregularitiessubcatcode',
            DB::raw("COALESCE(s.remarks::json->>'content', '')"),
            'cb.username',
            's.updatedon',
            'aps.audit_period',
            'aps.yearkeys'
        )
        ->get();
}




public static function getleavedetailsofauditors($data)
{

   // dd($data);
    $deptcode   = $data['deptcode'] ?? null;
    $regioncode = $data['regioncode'] ?? null;
    $distcode   = $data['distcode'] ?? null;
    $financialyear   = $data['financialyear'] ?? null;
    $start_date   = $data['start_date'] ?? null;
    $end_date   = $data['end_date'] ?? null;

    $table = 'audit.deptuserdetails';

    $query = DB::table('audit.ind_leavedetail as leave')
        ->select(
            'user.username',
            'user.usertamilname',
            DB::raw("TO_CHAR(leave.fromdate, 'DD-MM-YYYY') AS fromdate"),
            DB::raw("TO_CHAR(leave.todate, 'DD-MM-YYYY') AS todate"),
            'leave.reason',
            DB::raw("TO_CHAR(leave.createdon, 'DD-MM-YYYY') AS createdon"),
            'leave.leavedayscount',
            'dept.deptesname',
            'd.desigesname',
            'r.regionename',
            'r.regiontname',
            'dept.deptcode',
            'di.distename',
            'di.disttname',
            'y.financialyear',
            DB::raw("TO_CHAR(td.updatedon, 'DD-MM-YYYY') AS updatedon"),


            DB::raw("approver.username AS approvedby_username"),
            DB::raw("approver_d.desigesname AS approvedby_designation"),
            DB::raw("
            CASE
                WHEN EXTRACT(MONTH FROM leave.fromdate) BETWEEN 4 AND 6 THEN 'Q1'
                WHEN EXTRACT(MONTH FROM leave.fromdate) BETWEEN 7 AND 9 THEN 'Q2'
                WHEN EXTRACT(MONTH FROM leave.fromdate) BETWEEN 10 AND 12 THEN 'Q3'
                ELSE 'Q4'
            END AS auditquarter
        ")
        )

        ->join($table . ' as user', 'user.deptuserid', '=', 'leave.userid')

        ->join(self::$UserChargeDetails_Table . ' as uc', 'uc.userid', '=', 'user.deptuserid')
        ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'user.deptcode')
        ->join(self::$ChargeDetails_Table . ' as c', 'c.chargeid', '=', 'uc.chargeid')
        ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
        ->join('audit.mst_designation as d', 'd.desigcode', '=', 'user.desigcode')
        ->join(self::$regionTable . ' as r', 'r.regioncode', '=', 'c.regioncode')
        ->join(self::$District_Table . ' as di', 'di.distcode', '=', 'c.distcode')
        ->join('audit.mst_financialyear as y', 'y.financialyearcode', '=', 'y.financialyearcode')



        ->join('audit.transactiondetail as td', 'td.leaveid', '=', 'leave.leaveid')

        ->join(self::$UserChargeDetails_Table . ' as auc','auc.userchargeid','=','td.updatedbyuserchargeid')

        ->join($table . ' as approver','approver.deptuserid','=','auc.userid')

        ->join('audit.mst_designation as approver_d', 'approver_d.desigcode','=','approver.desigcode')

        // FILTERS
        ->where('y.financialyearcode', $financialyear)
        ->where('leave.processcode', 'P')
        ->where('user.statusflag', 'Y')
        ->where('uc.statusflag', 'Y')
        ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))
        ->where('d.statusflag', 'Y')
        ->where('dept.statusflag', 'Y');

    if (!empty($deptcode) && !in_array('A', (array)$deptcode)) {
        $query->whereIn('dept.deptcode', (array)$deptcode);
    }

    if (!empty($regioncode) && !in_array('A', (array)$regioncode)) {
        $query->whereIn('r.regioncode', (array)$regioncode);
    }

    if (!empty($distcode) && !in_array('A', (array)$distcode)) {
        $query->whereIn('di.distcode', (array)$distcode);
    }

    if ($start_date) {
        $query->whereDate('leave.fromdate', '>=', $start_date);
    }

    if ($end_date) {
        $query->whereDate('leave.todate', '<=', $end_date);
    }


    $query->orderBy('dept.deptcode')
          ->orderBy('r.regioncode')
          ->orderBy('leave.fromdate');

    return $query->get();
}


public static function diarysubmission_fetchData($data)
{
    $filters = [
        'd.deptcode' => $data['deptcode'] ?? null,
        'r.regioncode' => $data['regioncode'] ?? null,
        'c.distcode' => $data['distcode'] ?? null,
        'b.instid' => $data['instmappingcode'] ?? null,
        'a.planmappingid' => $data['auditquarter'] ?? null,
        'planmap.financialyearcode' => $data['financialyear'] ?? null,

    ];

    $query = DB::table('audit.auditplan as a')
        ->select(
            'd.deptesname', 'c.distename', 'r.regionename', 'r.regiontname','u.username',
            'c.disttname', 'b.instename', 'b.insttname', 'a.auditquartercode',
            'schmem.schteammemberid', 'u.deptuserid', 'schmem.diarystatus','planmap.planname',
            DB::raw("CASE WHEN schmem.diarystatus = 'F' THEN 'Finalized' ELSE 'Pending' END AS diary_status_text"),
            DB::raw("u.username || ' - ' || CASE WHEN schmem.auditteamhead = 'Y' THEN 'TH' ELSE 'TM' END AS username"),
            DB::raw("to_char(sch.fromdate, 'DD-MM-YYYY') as fromdate"),
            DB::raw("to_char(sch.todate, 'DD-MM-YYYY') as todate"),
            DB::raw("to_char(sch.entrymeetdate, 'DD-MM-YYYY') as entrymeetdate"),
            DB::raw("to_char(sch.exitmeetdate, 'DD-MM-YYYY') as exitmeetdate")
        )
        ->join('audit.inst_auditschedule as sch', 'a.auditplanid', '=', 'sch.auditplanid')
        ->join('audit.mst_institution as b', 'a.instid', '=', 'b.instid')
        ->join('audit.mst_district as c', 'b.distcode', '=', 'c.distcode')
        ->join('audit.mst_region as r', 'r.regioncode', '=', 'b.regioncode')
        ->join('audit.mst_dept as d', 'b.deptcode', '=', 'd.deptcode')
        ->join('audit.auditplanmapping as planmap', 'a.planmappingid', '=', 'planmap.planmappingid')
        ->join('audit.mst_financialyear as f', 'f.financialyearcode', '=', 'planmap.financialyearcode')

        ->join('audit.inst_schteammember as schmem', 'sch.auditscheduleid', '=', 'schmem.auditscheduleid')
        ->join('audit.deptuserdetails as u', 'u.deptuserid', '=', 'schmem.userid')
        ->where('b.statusflag', 'Y')
        ->orderBy('d.deptcode')
        ->orderBy('r.regioncode')
        ->orderBy('c.distcode')
        ->orderBy('b.instid');

    foreach ($filters as $column => $value) {
        if (!empty($value) && !in_array('A', (array)$value)) {
            $query->whereIn($column, (array)$value);
        }
    }

    $statusofdiary = $data['statusofdiary'] ?? 'A';

    if ($statusofdiary === '02') {
        $query->where('schmem.diarystatus', 'F');

    } elseif ($statusofdiary === '01') {
        $query->where(function ($q) {
            $q->whereNull('schmem.diarystatus')
              ->orWhere('schmem.diarystatus', '!=', 'F');
        });
    }

    return $query->get();
}


public static function fetch_diarydetails($data)
{


    $memberid = $data['memberid'];
    $userid = $data['userid'];


    $query = DB::table('audit.auditdiary as diary')
        ->join('audit.inst_schteammember as schmem', 'schmem.schteammemberid', '=', 'diary.schteammemberid')
        ->join('audit.trans_workallocation as tw', 'tw.workallocationid', '=', 'diary.workallocationid')
        ->join('audit.group as g', 'g.groupid', '=', 'tw.groupid')
        ->join('audit.map_allocation_objection as map', 'tw.workallocationtypeid', '=', 'map.mapallocationobjectionid')
        ->join('audit.mst_majorworkallocationtype as maj', 'maj.majorworkallocationtypeid', '=', 'map.majorworkallocationtypeid')

        ->where('schmem.schteammemberid', $memberid)
        ->where('schmem.userid', $userid)
        ->where('g.statusflag', '=' , 'Y')
        ->where('maj.statusflag', '=' , 'Y');


    $query->select(
        'diary.percentofcompletion',
        'diary.remarks',
        DB::raw("to_char(diary.fromdate, 'DD-MM-YYYY') as fromdate"),
        'g.groupename',
        'g.grouptname',
        'maj.majorworkallocationtypeename',
        'maj.majorworkallocationtypetname',
        'maj.majorworkallocationtypeid',

    );
    $query->orderby('g.groupename' ,'ASC')
    ->orderby('maj.majorworkallocationtypeename' ,'ASC');

    return $query->get();
}




public static function spilloverwithdiary_fetchData($data)
{
    $deptcode        = $data['deptcode'] ?? null;
    $regioncode      = $data['regioncode'] ?? null;
    $distcode        = $data['distcode'] ?? null;
    $instmappingcode = $data['instmappingcode'] ?? null;

    $query = DB::table('audit.auditplan as a')
        ->select(
            'd.deptesname',
            'c.distename',
            'r.regionename',
            'r.regiontname',
            'sch.entrymeetdate',
            'sch.exitmeetdate',
            'schmem.schteammemberid',
            'u.deptuserid',
            'c.disttname',
            'b.instename',
            'b.insttname',
            DB::raw("u.username || ' - ' || CASE WHEN schmem.auditteamhead = 'Y' THEN 'TH' ELSE 'TM' END AS username"),
            DB::raw("to_char(sch.fromdate, 'DD-MM-YYYY') as fromdate"),
            DB::raw("to_char(sch.todate, 'DD-MM-YYYY') as todate"),
            DB::raw("to_char(sch.entrymeetdate, 'DD-MM-YYYY') as entrymeetdate"),
            DB::raw("to_char(sch.exitmeetdate, 'DD-MM-YYYY') as exitmeetdate")

        )
        ->join('audit.inst_auditschedule as sch', 'a.auditplanid', '=', 'sch.auditplanid')
        ->join('audit.mst_institution as b', 'a.instid', '=', 'b.instid')
        ->join('audit.mst_district as c', 'b.distcode', '=', 'c.distcode')
        ->join('audit.mst_region as r', 'r.regioncode', '=', 'b.regioncode')
        ->join('audit.mst_dept as d', 'b.deptcode', '=', 'd.deptcode')
        ->join('audit.inst_schteammember as schmem', 'sch.auditscheduleid', '=', 'schmem.auditscheduleid')
        ->join('audit.deptuserdetails as u', 'u.deptuserid', '=', 'schmem.userid')

        ->where('a.spilloverflag', '=', 'Y')
        ->where('a.auditquartercode', '=', 'Q3')
        ->orderBy('d.deptcode')
        ->orderBy('r.regioncode')
        ->orderBy('c.distcode');

    // Apply filters only if not containing 'A'
    if (!empty($deptcode) && !in_array('A', (array)$deptcode)) {
        $query->whereIn('d.deptcode', (array)$deptcode);
    }

    if (!empty($regioncode) && !in_array('A', (array)$regioncode)) {
        $query->whereIn('r.regioncode', (array)$regioncode);
    }

    if (!empty($distcode) && !in_array('A', (array)$distcode)) {
        $query->whereIn('c.distcode', (array)$distcode);
    }

    if (!empty($instmappingcode) && !in_array('A', (array)$instmappingcode)) {
        $query->whereIn('b.instid', (array)$instmappingcode);
    }


    return $query->get();
}




public static function apmsall_fetchData($data)
{
    $deptcode   = $data['deptcode'] ?? null;     // array
    $regioncode = $data['regioncode'] ?? null;   // array
    $distcode   = $data['distcode'] ?? null;     // array
    $apmsstatus = $data['apmsstatus'] ?? null;   // single value

    return DB::select("
        SELECT * FROM audit.get_apmsfull_counts(:deptcode, :regioncode, :distcode, :statusflag)
    ", [
        'deptcode'   => in_array('A', (array)$deptcode) ? '{A}' : '{'.implode(',', $deptcode).'}',
        'regioncode' => in_array('A', (array)$regioncode) ? '{A}' : '{'.implode(',', $regioncode).'}',
        'distcode'   => in_array('A', (array)$distcode)   ? '{A}' : '{'.implode(',', $distcode).'}',

        'statusflag' => $apmsstatus
    ]);
}

public static function droppedslipfetchData($filters = [])
    {
        $filters = array_map(function ($v) {
            return is_array($v) ? $v : [$v];
        }, $filters);

        $query = DB::table('audit.trans_auditslip as slip')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'slip.auditplanid')
            ->join('audit.inst_auditschedule as sch', 'sch.auditplanid', '=', 'ap.auditplanid')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'inst.deptcode')
            ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'inst.regioncode')
            ->join('audit.auditplanmapping as planmap', 'ap.planmappingid', '=', 'planmap.planmappingid')
            ->join('audit.mst_financialyear as f', 'f.financialyearcode', '=', 'planmap.financialyearcode')
            ->join('audit.mst_district as dist', 'dist.distcode', '=', 'inst.distcode')
            ->Join('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', 'inst.catcode')
            ->leftJoin('audit.mst_auditeeins_subcategory as sub', 'sub.auditeeins_subcategoryid', '=', 'inst.subcatid')
            ->select(
                'dept.deptesname as department',
                'reg.regionename as region',
                'dist.distename as district',
                'cat.catename as category',
                'sub.subcatename as subcategory',
                'inst.instename as institution',
                'inst.instid',
                 'planmap.planname',
                'f.financialyearcode',
                'f.financialyear',

                'sch.entrymeetdate',
                'sch.exitmeetdate',
            )
            ->selectRaw("
            COUNT(slip.auditslipid) AS total_slips,
            SUM(CASE WHEN slip.processcode = 'X' THEN 1 ELSE 0 END) AS tpara_slips,
            SUM(CASE WHEN slip.processcode = 'A' THEN 1 ELSE 0 END) AS dropped_slips,
            SUM(CASE
                WHEN slip.processcode NOT IN ('X', 'A') OR slip.processcode IS NULL
                THEN 1 ELSE 0
            END) AS pending_slips,
            ROUND(
                (SUM(CASE WHEN slip.processcode = 'A' THEN 1 ELSE 0 END)::decimal
                / NULLIF(COUNT(slip.auditslipid), 0)) * 100,
            2
            ) AS dropped_slip_percentage
        ")


            ->where('slip.statusflag', 'Y')

            ->groupBy(
                'dept.deptcode',
                'cat.catcode',
                'dept.deptesname',
                'reg.regionename',
                'dist.distename',
                'cat.catename',
                'sub.subcatename',
                 'planmap.planname',
                'f.financialyearcode',
                'f.financialyear',
                'inst.instename',
                'inst.instid',
                'sch.entrymeetdate',
                'sch.exitmeetdate',
            );

        if (!empty($filters['deptcode']) && !in_array('A', $filters['deptcode'])) {
            $query->whereIn('dept.deptcode', $filters['deptcode']);
        }
        if (!empty($filters['regioncode']) && !in_array('A', $filters['regioncode'])) {
            $query->whereIn('inst.regioncode', $filters['regioncode']);
        }
        if (!empty($filters['distcode']) && !in_array('A', $filters['distcode'])) {
            $query->whereIn('inst.distcode', $filters['distcode']);
        }
        if (!empty($filters['category']) && !in_array('A', $filters['category'])) {
            $query->whereIn('inst.catcode', $filters['category']);
        }

      if (!empty($filters['financialyear']) && !in_array('A', $filters['financialyear'])) {
    $query->whereIn('planmap.financialyearcode', $filters['financialyear']);
}


        $skipSubcatDept = ['01', '05'];
        $selectedDept = $filters['deptcode'] ?? [];
        $selectedDept = is_array($selectedDept) ? $selectedDept : [$selectedDept];

        if (
            !empty($filters['subcategory']) &&
            !in_array('A', $filters['subcategory']) &&
            !in_array($selectedDept[0], $skipSubcatDept)
        ) {
            $query->whereIn('inst.subcatid', $filters['subcategory']);
        }
        // Quarter Filter
        if (!empty($filters['quarter']) && !in_array('A', $filters['quarter'])) {
            $query->whereIn('ap.planmappingid', $filters['quarter']);
        }


        if (!empty($filters['instmappingcode']) && !in_array('A', $filters['instmappingcode'])) {
            $query->whereIn('inst.instid', $filters['instmappingcode']);
        }


        $query->orderBy('dept.deptcode', 'asc')
            ->orderBy('cat.catcode', 'asc')
            ->orderBy('inst.instid', 'asc');

        return $query->cursor();

    }



public static function paraDetailsFetch($filters)
    {
        $query = DB::table('audit.trans_auditslip as slip')

            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'slip.auditplanid')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->join('audit.mst_dept as d', 'd.deptcode', '=', 'inst.deptcode')
            ->join('audit.mst_region as r', 'r.regioncode', '=', 'inst.regioncode')
            ->join('audit.mst_district as dist', 'dist.distcode', '=', 'inst.distcode')
            ->join('audit.mst_auditeeins_category as c', 'c.catcode', '=', 'inst.catcode')
            ->leftJoin('audit.mst_auditeeins_subcategory as sc', 'sc.auditeeins_subcategoryid', '=', 'inst.subcatid')
            ->join('audit.auditplanmapping as planmap', 'ap.planmappingid', '=', 'planmap.planmappingid')
            ->join('audit.mst_financialyear as f', 'f.financialyearcode', '=', 'planmap.financialyearcode')

            ->join('audit.mst_mainobjection as mo', 'mo.mainobjectionid', '=', 'slip.mainobjectionid')
            ->join('audit.mst_subobjection as so', 'so.subobjectionid', '=', 'slip.subobjectionid')


            ->select(
                'd.deptcode',
                'r.regioncode',
                'dist.distcode',
                'c.catcode',
                'd.deptesname as department',
                'r.regionename as region',
                'dist.distename as district',
                'c.catename as category',
                DB::raw("COALESCE(sc.subcatename, '') as subcategory"),
                'inst.instename as institution',
                'planmap.planname',
                'f.financialyearcode',
                'f.financialyear',
                'slip.mainslipnumber',
                'mo.objectionename as mainobjection',
                'so.subobjectionename as subobjection',
                'slip.irregularitiescode',
                'slip.auditslipid'

            );

        $query->where('slip.statusflag', 'Y');
        $apply = function ($key, $column) use ($filters, $query) {
            if (!empty($filters[$key]) && !in_array('A', $filters[$key])) {
                $query->whereIn($column, $filters[$key]);
            }
        };

        $apply('deptcode', 'd.deptcode');
        $apply('regioncode', 'r.regioncode');
        $apply('distcode', 'dist.distcode');
        $apply('category', 'c.catcode');
        $apply('subcategory', 'sc.auditeeins_subcategoryid');
        $apply('quarter', 'ap.planmappingid');
        $apply('irregularity', 'slip.irregularitiescode');
        $apply('instmappingcode', 'inst.instid');

        if (!empty($filters['financialyear']) && $filters['financialyear'] !== 'A') {
            $query->where('planmap.financialyearcode', $filters['financialyear']);

}

        $query->orderBy('d.deptcode')
            ->orderBy('r.regioncode')
            ->orderBy('dist.distcode')
            ->orderBy('planmap.planmappingid');

        return $query->cursor();

    }

  public static function auditIntimationFetch($filters)
    {
        $query = DB::table('audit.inst_auditschedule as sch')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'sch.auditplanid')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->join('audit.mst_dept as d', 'd.deptcode', '=', 'inst.deptcode')
            ->join('audit.auditplanmapping as planmap', 'ap.planmappingid', '=', 'planmap.planmappingid')
            ->join('audit.mst_financialyear as f', 'f.financialyearcode', '=', 'planmap.financialyearcode')
            ->join('audit.mst_region as r', 'r.regioncode', '=', 'inst.regioncode')
            ->join('audit.mst_district as dist', 'dist.distcode', '=', 'inst.distcode')
            ->select(
                'sch.auditscheduleid',
                DB::raw("
                      CASE
                       WHEN sch.auditeeresponse = 'A' THEN 'Accepted'
                       WHEN sch.statusflag = 'F' THEN 'Intimation Sent'
                    END AS status_name
                "),
                'd.deptesname as department',
                'r.regionename as region',
                'dist.distename as district',
                'inst.instename as institution',
                'ap.auditquartercode',
                'inst.instid',
                'sch.entrymeetdate',
                'sch.exitmeetdate',
                'planmap.planname'
            );

        $apply = function ($key, $column) use ($filters, $query) {
            $values = (array) ($filters[$key] ?? []);
            if (!empty($values) && !in_array('A', $values)) {
                $query->whereIn($column, $values);
            }
        };

        $apply('deptcode', 'd.deptcode');
        $apply('regioncode', 'r.regioncode');
        $apply('distcode', 'dist.distcode');
        $apply('instid', 'inst.instid');

        $quarters = (array) ($filters['quarter'] ?? []);
        if (!empty($quarters) && !in_array('A', $quarters)) {
            $query->whereIn('ap.planmappingid', $quarters);
        }

          if (!empty($filters['financialyear']) && $filters['financialyear'] !== 'A') {
            $query->where('planmap.financialyearcode', $filters['financialyear']);

}


        return $query
            ->orderBy('d.deptcode', 'ASC')
            ->orderBy('sch.auditscheduleid', 'ASC')
            ->cursor();

    }

// public static function getquarterDet()
//     {
//         return DB::table(self::$Dept_Table . ' as dept')
//             ->join(self::$AuditQuarter_Table . ' as quart', 'quart.auditquartercode', '=', 'dept.currentquarter')
//             ->select(
//                 'dept.currentquarter',
//                 'quart.auditquarter',
//                 'quart.auditquartercode',
//                 'quart.auditquartertname'
//             )
//             ->distinct()
//             ->first();
//     }


    public static function plancount_fetchData($data)
{
    $deptcode   = $data['deptcode'] ?? ['A'];
    $regioncode = $data['regioncode'] ?? ['A'];
    $distcode   = $data['distcode'] ?? ['A'];

    $regionIsAll = in_array('A', $regioncode);
    $distIsAll   = in_array('A', $distcode);

    $query = DB::table('audit.auditor_instmapping as aim')
        ->join('audit.mst_dept as dp', 'dp.deptcode', '=', 'aim.deptcode');

    $groupBy = ['dp.deptcode','dp.deptesname', 'dp.orderid'];

    if (!$regionIsAll) {
        $query->join('audit.mst_region as r', 'r.regioncode', '=', 'aim.regioncode')
              ->whereIn('aim.regioncode', $regioncode);
              $groupBy[] = 'aim.regioncode';

        $groupBy[] = 'r.regionename';
    }

    if (!$distIsAll) {
        $query->join('audit.mst_district as d', 'd.distcode', '=', 'aim.distcode')
              ->whereIn('aim.distcode', $distcode);
              $groupBy[] = 'aim.distcode';

        $groupBy[] = 'd.distename';
    }

    if (!in_array('A', $deptcode)) {
        $query->whereIn('aim.deptcode', $deptcode);
    }

    $query->selectRaw("
        dp.deptesname,
        dp.deptcode,
        " . ($regionIsAll ? "'A' as regioncode" : "aim.regioncode") . ",
        " . ($regionIsAll ? "'ALL' as regionename" : "r.regionename") . ",
        " . ($distIsAll ? "'A' as distcode" : "aim.distcode") . ",
        " . ($distIsAll ? "'ALL' as distename" : "d.distename") . ",

        COUNT(DISTINCT aim.distcode) AS total_districts,

        COUNT(DISTINCT CASE
            WHEN aim.pendinginststatus = 'Y'
            THEN aim.distcode
        END) AS qt_finalized,

        COUNT(DISTINCT CASE
            WHEN aim.pendinginststatus = 'N'
                 OR aim.pendinginststatus IS NULL
            THEN aim.distcode
        END) AS qt_pending,

        COUNT(DISTINCT CASE
            WHEN aim.autoplanstatus = 'F'
            THEN aim.distcode
        END) AS autoplan_finalized
    ");

    return $query->groupBy($groupBy)
                 ->orderBy('dp.orderid')
                 ->get();
}
public static function fetchDrilldown($data)
{
    $deptcode   = $data['deptcode'] ?? null;
    $regioncode = $data['regioncode'] ?? 'A';
    $distcode   = $data['distcode'] ?? 'A';
    $column     = $data['column'] ?? null;

    //dd($deptcode,$regioncode,$distcode,$column);

    $query = DB::table('audit.auditor_instmapping as aim')
        ->join('audit.mst_district as d', 'd.distcode', '=', 'aim.distcode')
        ->where('aim.deptcode', $deptcode);

    if ($regioncode != 'A') {
        $query->where('aim.regioncode', $regioncode);
    }

    if ($distcode != 'A') {
        $query->where('aim.distcode', $distcode);
    }

    switch ($column) {

        case 'qt_finalized':
            $query->where('aim.pendinginststatus', 'Y');
            break;

        case 'qt_pending':
            $query->where(function ($q) {
                $q->where('aim.pendinginststatus', 'N')
                  ->orWhereNull('aim.pendinginststatus');
            });
            break;

        case 'autoplan_finalized':
            $query->where('aim.autoplanstatus', 'F');
            break;

        case 'total_districts':
        default:
            break;
    }

    return $query->selectRaw("
                d.distcode,
                d.distename,
                COUNT(*) as districtcount            ")
            ->groupBy('d.distcode', 'd.distename')
            ->orderBy('d.distename')
            ->get();
}

public static function getquarterDet()
    {
        return DB::table(self::$Dept_Table . ' as dept')
            ->join(self::$AuditQuarter_Table . ' as quart', 'quart.auditquartercode', '=', 'dept.currentquarter')
            ->select(
                'dept.currentquarter',
                'quart.auditquarter',
                'quart.auditquartercode',
                'quart.auditquartertname'
            )
            ->distinct()
            ->first();
    }

       public static function GetInstitutionByDeptRegionDistrict(
        $deptcode,
        $region,
        $district,
        $catcode,
        $subcategory,
        $audityearcode
    ) {
        return DB::table(self::$Institution_Table . ' as inst')
            ->join(self::$auditplan_table . ' as ap', 'ap.instid', '=', 'inst.instid')
            ->join('audit.yearcode_mapping as ayear', 'ayear.auditplanid', '=', 'ap.auditplanid')
            ->join('audit.mst_auditperiod as ay', 'ay.auditperiodid', '=', 'ayear.yearselected')
            ->join(self::$Instschedule_Table . ' as i', 'i.auditplanid', '=', 'ap.auditplanid')
            ->select(
                'inst.instid',
                'inst.instename',
                'inst.insttname',
                'i.auditscheduleid',
                'ap.auditplanid',
                'ap.auditquartercode',
                DB::raw(
                    "inst.instename || ' - ( ' || ap.auditquartercode || ' )' as inst_display_name"
                )
            )
            ->where('inst.statusflag', 'Y')
            ->where('ayear.statusflag', 'Y')
            ->where('ay.auditperiodid', $audityearcode)
            ->where('inst.deptcode', $deptcode)
            ->where('inst.regioncode', $region)
            ->where('inst.distcode', $district)
            ->where('inst.catcode', $catcode)
            ->where('inst.subcatid', $subcategory)
            ->where('i.sendintimation', 'F')
            ->orderBy('inst.instid', 'ASC')
            ->get();
    }


    public static function GetInstForPrAuditReport(
        $deptcode,
        $region,
        $district,
        $catcode,
        $subcategory
    ) {
        return DB::table(self::$Institution_Table . ' as inst')
            ->join(self::$auditplan_table . ' as ap', 'ap.instid', '=', 'inst.instid')
            ->select(
                'inst.instid',
                'inst.instename',
                'inst.insttname',
                // 'i.auditscheduleid',
                'ap.auditplanid',
                'ap.auditquartercode',
                DB::raw("inst.instename || ' - ( ' || ap.auditquartercode || ' )' as inst_display_name")
            )
            ->where('inst.statusflag', 'Y')
            ->where('ap.auditmode', 'P')

            ->when($deptcode, function ($q) use ($deptcode) {
                $q->where('inst.deptcode', $deptcode);
            })

            ->when($region, function ($q) use ($region) {
                $q->where('inst.regioncode', $region);
            })

            ->when($district, function ($q) use ($district) {
                $q->where('inst.distcode', $district);
            })

            ->when($catcode, function ($q) use ($catcode) {
                $q->where('inst.catcode', $catcode);
            })

            ->when($subcategory, function ($q) use ($subcategory) {
                $q->where('inst.subcatid', $subcategory);
            })

            ->orderBy('inst.instid', 'ASC')
            ->get();
    }

public static function GetSlipDetailsData($instid, $auditplanid)
    {
        $GetauditSlips = DB::table(self::$TransAuditSlip_Table . ' as t')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 't.auditplanid')
            ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
            ->join('audit.mst_mainobjection as m', 'm.mainobjectionid', '=', 't.mainobjectionid')
            ->join(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', 't.subobjectionid')
            ->join('audit.mst_irregularities as i', 'i.irregularitiescode', '=', 't.irregularitiescode')
            ->join('audit.mst_irregularitiescategory as ir', 'ir.irregularitiescatcode', '=', 't.irregularitiescatcode')
            ->join('audit.mst_irregularitiessubcategory as irr', 'irr.irregularitiessubcatcode', '=', 't.irregularitiessubcatcode')
            ->leftjoin('audit.liability as li', 'li.auditslipid', '=', 't.auditslipid')
            ->select(
                't.auditslipid',
                't.transactionno',
                't.mainobjectionid',
                't.subobjectionid',
                't.slipdetails',
                't.remarks',
                'm.objectionename',
                's.subobjectionename',
                't.amtinvolved as amountinvolved',
                't.amtinvolved',
                't.severitycode',
                'i.irregularitieselname',
                'ir.irregularitiescatelname',
                'irr.irregularitiessubcatelname',
                't.liability',
                't.irregularitiescode',
                't.irregularitiescatcode',
                't.irregularitiessubcatcode',
                't.processcode',
                't.auditscheduleid',
                't.schteammemberid',
                't.auditplanid',
                't.tempslipnumber',
                't.mainslipnumber',
                't.schemastatus',
                't.auditeeschemecode',
                't.statusflag',
                't.rejoinderstatus',
                't.rejoindercycle',
                't.createdby',
                't.forwardedto',
                't.forwardedtousertypecode',
                't.updatedby',
                't.updatedbyusertypecode',
                't.quartercode',
                't.financialyear',
                't.paraorder',
                't.paraverifiedflag',
                't.paraverifiedby',
                't.paraverifiedon',
                'li.liabilityname',
                'li.liabilitygpfno',
                'li.liabilitydesignation',
                'li.liabilityamount',
                'mi.catcode'
            )
            ->where('t.statusflag', 'Y')
            ->where('t.processcode', 'X')
            ->where('mi.instid', $instid)
	    ->where('t.auditplanid', $auditplanid)
            ->orderBy('t.paraorder', 'asc')
            ->orderByRaw("CASE WHEN t.irregularitiescode = '01' THEN 0 ELSE 1 END")
            ->get();

        if ($GetauditSlips->isEmpty()) {
            return collect();
        }

        $orderedSlips = $GetauditSlips;

        $count = 1;
        foreach ($orderedSlips as $slip) {
            if (!empty($slip->paraorder)) {
                $slip->paranumber = str_pad($slip->paraorder, 4, '0', STR_PAD_LEFT);
            } else {
                $slip->paranumber = str_pad($count, 4, '0', STR_PAD_LEFT);
            }
            $count++;
        }

        return $orderedSlips;
    }

    public static function getParaRemarksDetails($auditslipid, $mainslipnumber)
    {
        $getParaRemarksDetails = DB::table(self::$TransAuditSlip_Table . ' as t')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 't.auditplanid')
            ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
            ->join('audit.mst_mainobjection as m', 'm.mainobjectionid', '=', 't.mainobjectionid')
            ->join(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', 't.subobjectionid')
            ->join('audit.mst_irregularities as i', 'i.irregularitiescode', '=', 't.irregularitiescode')
            ->join('audit.mst_irregularitiescategory as ir', 'ir.irregularitiescatcode', '=', 't.irregularitiescatcode')
            ->join('audit.mst_irregularitiessubcategory as irr', 'irr.irregularitiessubcatcode', '=', 't.irregularitiessubcatcode')
            ->leftjoin('audit.liability as li', 'li.auditslipid', '=', 't.auditslipid')
            ->select(
                't.auditslipid',
                't.slipdetails',
                't.remarks',
                't.paraorder',
                'm.objectionename',
                's.subobjectionename',
                't.amtinvolved as amountinvolved',
                't.severitycode',
                'i.irregularitieselname',
                'ir.irregularitiescatelname',
                'irr.irregularitiessubcatelname',
                't.liability',
                't.irregularitiescode',
                't.irregularitiescatcode',
                't.irregularitiessubcatcode',
                't.auditplanid',
                't.mainslipnumber',
                't.paraverifiedflag',
                't.paraverifiedby',
                't.paraverifiedon',
                'li.liabilityname',
                'li.liabilitygpfno',
                'li.liabilitydesignation',
                'li.liabilityamount',
                'mi.catcode'
            )
            ->where('t.statusflag', 'Y')
            ->where('t.processcode', 'X')
            ->where('t.auditslipid', $auditslipid)
            ->where('t.mainslipnumber', $mainslipnumber)
            ->orderBy('t.paraorder', 'asc')
            ->orderByRaw("CASE WHEN t.irregularitiescode = '01' THEN 0 ELSE 1 END")
            ->first();


        $slip = $getParaRemarksDetails;

        if ($slip) {
            if (!empty($slip->paraorder)) {
                $slip->paranumber = str_pad($slip->paraorder, 4, '0', STR_PAD_LEFT);
            } else {
                $slip->paranumber = str_pad(1, 4, '0', STR_PAD_LEFT);
            }
        }

        return $slip;
    }

    public static function getApproverParaRemarksDetails($auditslipid, $mainslipnumber, $tableType)
    {
        if ($tableType === 'selected') {
            $getParaRemarksDetails = DB::table('audit.consolidation_report as cr')
                ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
                ->join('audit.mst_mainobjection as m', 'm.mainobjectionid', '=', 'cr.mainobjectionid')
                ->join(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', 'cr.subobjectionid')
                ->join('audit.mst_irregularities as i', 'i.irregularitiescode', '=', 'cr.irregularitiescode')
                ->join('audit.mst_irregularitiescategory as ir', 'ir.irregularitiescatcode', '=', 'cr.irregularitiescatcode')
                ->join('audit.mst_irregularitiessubcategory as irr', 'irr.irregularitiessubcatcode', '=', 'cr.irregularitiessubcatcode')
                ->leftjoin('audit.liability as li', 'li.auditslipid', '=', 'cr.auditslipid')
                ->select(
                    'cr.auditslipid',
                    'cr.slipdetails',
                    'cr.remarks',
                    'cr.paraorder',
                    'm.objectionename',
                    's.subobjectionename',
                    'cr.amtinvolved as amountinvolved',
                    'cr.severitycode',
                    'i.irregularitieselname',
                    'ir.irregularitiescatelname',
                    'irr.irregularitiessubcatelname',
                    'cr.liability',
                    'cr.irregularitiescode',
                    'cr.irregularitiescatcode',
                    'cr.irregularitiessubcatcode',
                    'cr.auditplanid',
                    'cr.mainslipnumber',
                    'cr.paraverifiedflag',
                    'cr.paraverifiedby',
                    'cr.paraverifiedon',
                    'cr.approververifiedflag',
                    'li.liabilityname',
                    'li.liabilitygpfno',
                    'li.liabilitydesignation',
                    'li.liabilityamount',
                )
                ->where('cr.processcode', 'X')
                ->where('cr.auditslipid', $auditslipid)
                ->where('cr.mainslipnumber', $mainslipnumber)
                ->orderBy('cr.paraorder', 'asc')
                ->first();

        } else {
            $getParaRemarksDetails = DB::table(self::$TransAuditSlip_Table . ' as t')
                ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 't.auditplanid')
                ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
                ->join('audit.mst_mainobjection as m', 'm.mainobjectionid', '=', 't.mainobjectionid')
                ->join(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', 't.subobjectionid')
                ->join('audit.mst_irregularities as i', 'i.irregularitiescode', '=', 't.irregularitiescode')
                ->join('audit.mst_irregularitiescategory as ir', 'ir.irregularitiescatcode', '=', 't.irregularitiescatcode')
                ->join('audit.mst_irregularitiessubcategory as irr', 'irr.irregularitiessubcatcode', '=', 't.irregularitiessubcatcode')
                ->leftjoin('audit.liability as li', 'li.auditslipid', '=', 't.auditslipid')
                ->select(
                    't.auditslipid',
                    't.slipdetails',
                    't.remarks',
                    't.paraorder',
                    'm.objectionename',
                    's.subobjectionename',
                    't.amtinvolved as amountinvolved',
                    't.severitycode',
                    'i.irregularitieselname',
                    'ir.irregularitiescatelname',
                    'irr.irregularitiessubcatelname',
                    't.liability',
                    't.irregularitiescode',
                    't.irregularitiescatcode',
                    't.irregularitiessubcatcode',
                    't.auditplanid',
                    't.mainslipnumber',
                    't.paraverifiedflag',
                    't.paraverifiedby',
                    't.paraverifiedon',
                    DB::raw("'N' as approververifiedflag"),
                    'li.liabilityname',
                    'li.liabilitygpfno',
                    'li.liabilitydesignation',
                    'li.liabilityamount',
                    'mi.catcode'
                )
                ->where('t.statusflag', 'Y')
                ->where('t.processcode', 'X')
                ->where('t.auditslipid', $auditslipid)
                ->where('t.mainslipnumber', $mainslipnumber)
                ->orderBy('t.paraorder', 'asc')
                ->orderByRaw("CASE WHEN t.irregularitiescode = '01' THEN 0 ELSE 1 END")
                ->first();
        }

        $slip = $getParaRemarksDetails;

        if ($slip) {
            if (!empty($slip->paraorder)) {
                $slip->paranumber = str_pad($slip->paraorder, 4, '0', STR_PAD_LEFT);
            } else {
                $slip->paranumber = str_pad(1, 4, '0', STR_PAD_LEFT);
            }
        }

        return $slip;
    }

    public static function getSelectedParasDetails()
    {
        $GetauditSlips =  DB::table('audit.consolidation_report as cr')
            ->select([
                'cr.auditslipid',
                'cr.mainslipnumber',
                'm.objectionename',
                's.subobjectionename',
                'cr.slipdetails',
                'cr.amtinvolved as amountinvolved',
                'cr.amtinvolved',
                'cr.paraverifiedflag',
                'cr.paraverifiedby',
                'cr.paraverifiedon',
                'cr.auditscheduleid',
                'cr.remarks',
                'mi.instename as institution_name',
                'mi.catcode',
                'i.irregularitieselname as irrregularity',

            ])
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'cr.auditplanid')
            ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
            ->join('audit.mst_mainobjection as m', 'm.mainobjectionid', '=', 'cr.mainobjectionid')
            ->join('audit.mst_subobjection as s', 's.subobjectionid', '=', 'cr.subobjectionid')
            ->join('audit.mst_irregularities as i', 'i.irregularitiescode', '=', 'cr.irregularitiescode')
            ->join('audit.mst_irregularitiescategory as ir', 'ir.irregularitiescatcode', '=', 'cr.irregularitiescatcode')
            ->join('audit.mst_irregularitiessubcategory as irr', 'irr.irregularitiessubcatcode', '=', 'cr.irregularitiessubcatcode')
            ->where('cr.statusflag', 'Y')
            ->where('cr.parasavedby', session('user')->userid)
            ->where('cr.processcode', 'X')
            ->orderBy('cr.createdon', 'desc')
            ->get();

        return $GetauditSlips;
    }

    public static function UpdateVerifiedParaFlag($mainslipnumber, $auditslipid)
    {
        $userid = session('user')->userid ?? null;

        return DB::table(self::$TransAuditSlip_Table)
            ->where('auditslipid', $auditslipid)
            ->where('mainslipnumber', $mainslipnumber)
            ->update([
                'paraverifiedflag' => 'Y',
                'paraverifiedby'   => $userid,
                'paraverifiedon'        => now()
            ]);
    }
    public static function UpdateApprovedParaFlag($mainslipnumber, $auditslipid)
    {
        $userid = session('user')->userid ?? null;

        return DB::table(self::$TransAuditSlip_Table)
            ->where('auditslipid', $auditslipid)
            ->where('mainslipnumber', $mainslipnumber)
            ->update([
                'approververifiedflag' => 'Y',
                'approververifiedflagby' => $userid,
                'approververifiedflagon' => now()
            ]);
    }
    public static function UpdateSelectedApprovedParaFlag($mainslipnumber, $auditslipid)
    {
        $userid = session('user')->userid ?? null;

        return DB::table('audit.consolidation_report')
            ->where('auditslipid', $auditslipid)
            ->where('mainslipnumber', $mainslipnumber)
            ->update([
                'approververifiedflag' => 'Y',
                'approververifiedflagby' => $userid,
                'approververifiedflagon' => now()
            ]);
    }

  public static function GetselectedSlipDetailsData($instid, $auditplanid)	
    {
        $GetauditSlips = DB::table('audit.consolidation_report as t')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 't.auditplanid')
            ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
            ->join('audit.mst_mainobjection as m', 'm.mainobjectionid', '=', 't.mainobjectionid')
            ->join(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', 't.subobjectionid')
            ->join('audit.mst_irregularities as i', 'i.irregularitiescode', '=', 't.irregularitiescode')
            ->join('audit.mst_irregularitiescategory as ir', 'ir.irregularitiescatcode', '=', 't.irregularitiescatcode')
            ->join('audit.mst_irregularitiessubcategory as irr', 'irr.irregularitiessubcatcode', '=', 't.irregularitiessubcatcode')
            ->leftjoin('audit.liability as li', 'li.auditslipid', '=', 't.auditslipid')
            ->select(
                't.auditslipid',
                't.transactionno',
                't.mainobjectionid',
                't.subobjectionid',
                't.slipdetails',
                't.remarks',
                'm.objectionename',
                's.subobjectionename',
                't.amtinvolved as amountinvolved',
                't.amtinvolved',
                't.severitycode',
                'i.irregularitieselname',
                'ir.irregularitiescatelname',
                'irr.irregularitiessubcatelname',
                't.liability',
                't.irregularitiescode',
                't.irregularitiescatcode',
                't.irregularitiessubcatcode',
                't.processcode',
                't.auditscheduleid',
                't.schteammemberid',
                't.auditplanid',
                't.tempslipnumber',
                't.mainslipnumber',
                't.schemastatus',
                't.auditeeschemecode',
                't.statusflag',
                't.rejoinderstatus',
                't.rejoindercycle',
                't.createdby',
                't.forwardedto',
                't.forwardedtousertypecode',
                't.updatedby',
                't.updatedbyusertypecode',
                't.quartercode',
                't.financialyear',
                't.paraorder',
                't.paraverifiedflag',
                't.paraverifiedby',
                't.paraverifiedon',
                't.approververifiedflag',
                't.approververifiedflagon',
                't.approververifiedflagby',
                'li.liabilityname',
                'li.liabilitygpfno',
                'li.liabilitydesignation',
                'li.liabilityamount',
                'mi.catcode'
            )
            ->where('mi.instid', $instid)
	    ->where('t.auditplanid', $auditplanid)
            ->where(function ($q) {
                $q->whereIn('t.statusflag', ['P','A'])
                    ->orWhere('t.is_forwarded', 'Y');
            })
            ->orderBy('t.paraorder', 'asc')
            ->orderByRaw("CASE WHEN t.irregularitiescode = '01' THEN 0 ELSE 1 END")
            ->orderByDesc('t.approververifiedflagon')
            ->orderByDesc('t.paraverifiedon')
            ->get();

        if ($GetauditSlips->isEmpty()) {
            return collect();
        }

        $orderedSlips = $GetauditSlips;

        $count = 1;
        foreach ($orderedSlips as $slip) {
            if (!empty($slip->paraorder)) {
                $slip->paranumber = str_pad($slip->paraorder, 4, '0', STR_PAD_LEFT);
            } else {
                $slip->paranumber = str_pad($count, 4, '0', STR_PAD_LEFT);
            }
            $count++;
        }

        return $orderedSlips;
    }

public static function GetUnselectedSlipDetailsData($instid, $auditplanid)
    {
        $GetauditSlips = DB::table(self::$TransAuditSlip_Table . ' as t')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 't.auditplanid')
            ->join('audit.mst_institution as mi', 'ap.instid', '=', 'mi.instid')
            ->join('audit.mst_mainobjection as m', 'm.mainobjectionid', '=', 't.mainobjectionid')
            ->join(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', 't.subobjectionid')
            ->join('audit.mst_irregularities as i', 'i.irregularitiescode', '=', 't.irregularitiescode')
            ->join('audit.mst_irregularitiescategory as ir', 'ir.irregularitiescatcode', '=', 't.irregularitiescatcode')
            ->join('audit.mst_irregularitiessubcategory as irr', 'irr.irregularitiessubcatcode', '=', 't.irregularitiessubcatcode')
            ->leftJoin('audit.consolidation_report as css', function ($join) {
                $join->on('css.auditslipid', '=', 't.auditslipid');
            })
            ->leftjoin('audit.liability as li', 'li.auditslipid', '=', 't.auditslipid')
            ->select(
                't.auditslipid',
                't.transactionno',
                't.mainobjectionid',
                't.subobjectionid',
                't.slipdetails',
                't.remarks',
                'm.objectionename',
                's.subobjectionename',
                't.amtinvolved as amountinvolved',
                't.severitycode',
                'i.irregularitieselname',
                'ir.irregularitiescatelname',
                'irr.irregularitiessubcatelname',
                't.liability',
                't.irregularitiescode',
                't.irregularitiescatcode',
                't.irregularitiessubcatcode',
                't.processcode',
                't.auditscheduleid',
                't.schteammemberid',
                't.auditplanid',
                't.tempslipnumber',
                't.mainslipnumber',
                't.schemastatus',
                't.auditeeschemecode',
                'css.statusflag',
                't.rejoinderstatus',
                't.rejoindercycle',
                't.createdby',
                't.forwardedto',
                't.forwardedtousertypecode',
                't.updatedby',
                't.updatedbyusertypecode',
                't.quartercode',
                't.financialyear',
                't.paraorder',
                't.paraverifiedflag',
                't.paraverifiedby',
                't.paraverifiedon',
                't.approververifiedflag',
                't.approververifiedflagon',
                't.approververifiedflagby',
                'li.liabilityname',
                'li.liabilitygpfno',
                'li.liabilitydesignation',
                'li.liabilityamount',
                'mi.catcode'
            )
            ->where('t.statusflag', 'Y')
            ->where('t.processcode', 'X')
            ->where('mi.instid', $instid)
            ->where('t.auditplanid', $auditplanid)
            ->where(function ($q) {
                $q->whereNull('css.auditslipid')             // not selected at all
                ->orWhereNotIn('css.statusflag', ['F','P','A','C']); // selected but not finalized
            })
            ->orderBy('t.paraorder', 'asc')
            ->orderByRaw("CASE WHEN t.irregularitiescode = '01' THEN 0 ELSE 1 END")
            ->orderByDesc('t.approververifiedflagon')
            ->orderByDesc('t.paraverifiedon')
            ->get();

        if ($GetauditSlips->isEmpty()) {
            return collect();
        }

        $orderedSlips = $GetauditSlips;

        $count = 1;
        foreach ($orderedSlips as $slip) {
            if (!empty($slip->paraorder)) {
                $slip->paranumber = str_pad($slip->paraorder, 4, '0', STR_PAD_LEFT);
            } else {
                $slip->paranumber = str_pad($count, 4, '0', STR_PAD_LEFT);
            }
            $count++;
        }

        return $orderedSlips;
    }

public static function CategoryFetchData(){
    $sessionchargedel = session('charge');

    $deptcode  = optional($sessionchargedel)->deptcode ?? '';
    // $region    = optional($sessionchargedel)->regioncode ?? '';
    // $district  = optional($sessionchargedel)->distcode ?? '';


       // print_r($sessiondet);
        $table = self::$UserDetails_Table;

    $query = DB::table(self::$MstAuditeeInsCategory_Table . ' as cat')
        // ->join('audit.mst_financialyear as year', 'year.financialyearcode', '=', 'plan.financialyearcode')
        ->select('cat.catcode', 'cat.catename','cat.cattname')
        ->where('cat.statusflag', 'Y')
         ->where('cat.deptcode', $deptcode)

        ->get();

    return $query;
}


public static function fetch_epcsdetails($data)
{
    $deptcodes = is_array($data['deptcode'] ?? null)
        ? $data['deptcode']
        : [$data['deptcode'] ?? 'A'];

    $regioncodes = is_array($data['regioncode'] ?? null)
        ? $data['regioncode']
        : [$data['regioncode'] ?? 'A'];

    $distcodes = is_array($data['distcode'] ?? null)
        ? $data['distcode']
        : [$data['distcode'] ?? 'A'];

    $type = $data['type'] ?? null;

    $isAllDept   = count($deptcodes) === 1 && $deptcodes[0] === 'A';
    $isAllRegion = count($regioncodes) === 1 && $regioncodes[0] === 'A';
    $isAllDist   = count($distcodes) === 1 && $distcodes[0] === 'A';

    $query = DB::table('audit.auditplan as p')

        ->join('audit.mst_institution as inst', 'inst.instid', '=', 'p.instid')

        ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'inst.regioncode')

        ->join('audit.mst_district as dist', 'dist.distcode', '=', 'inst.distcode')

        ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'inst.deptcode')
        ->join('audit.auditplanmapping as pm', 'pm.planmappingid', '=', 'dept.planmappingid')

      ->leftJoin(DB::raw("
            (
                SELECT
                    ym.auditplanid,
                    STRING_AGG(
                        CONCAT(pd.fromyear, ' - ', pd.toyear),
                        ', '
                    ) as auditperiod
                FROM audit.yearcode_mapping ym
                JOIN audit.mst_auditperiod pd
                    ON pd.auditperiodid = ym.yearselected
                GROUP BY ym.auditplanid
            ) ap
        "), 'ap.auditplanid', '=', 'p.auditplanid')

        ->leftJoin('audit.inst_auditschedule as s', 's.auditplanid', '=', 'p.auditplanid')

        ->where('p.datafromapi', 'Y')
        ->where('inst.statusflag', 'Y')
        ->where('dept.statusflag', 'Y')

        ->when(!$isAllDept, function ($query) use ($deptcodes) {
            $query->whereIn('dept.deptcode', $deptcodes);
        })

        ->when(!$isAllRegion, function ($query) use ($regioncodes) {
            $query->whereIn('reg.regioncode', $regioncodes);
        })

        ->when(!$isAllDist, function ($query) use ($distcodes) {
            $query->whereIn('dist.distcode', $distcodes);
        });


    if ($type == 'P') {

       $query->where('p.datafromapi', 'Y');


    } elseif ($type == 'NS') {

       $query->whereNull('s.auditplanid');

    } elseif ($type == 'S') {

        $query->whereNotNull('s.auditplanid')
              ->whereNull('s.exitmeetdate');

    }
    // elseif ($type == 'N') {

    //     $query->whereNotNull('s.entrymeetdate')
    //           ->whereNull('s.exitmeetdate');

    // }
    elseif ($type == 'E') {

        $query->whereNotNull('s.entrymeetdate')
              ->whereNotNull('s.exitmeetdate');
    }

    $query->select(
        'dept.deptcode',
        'dept.deptesname',

        DB::raw("
            CASE
                WHEN " . ($isAllRegion ? "TRUE" : "FALSE") . "
                THEN 'A'
                ELSE MAX(reg.regioncode)::text
            END as regioncode
        "),
    DB::raw("MAX(ap.auditperiod) as auditperiod"),
      DB::raw("
            CASE
                WHEN " . ($isAllDist ? "TRUE" : "FALSE") . "
                THEN 'A'
                ELSE MAX(dist.distcode)::text
            END as distcode
        "),

        DB::raw("
            CASE
                WHEN " . ($isAllRegion ? "TRUE" : "FALSE") . "
                THEN 'All'
                ELSE MAX(reg.regionename)
            END as regionename
        "),

         DB::raw("
            CASE
                WHEN " . ($isAllDist ? "TRUE" : "FALSE") . "
                THEN 'All'
                ELSE MAX(dist.distename)
            END as distename
        "),

        'inst.instid',
        'inst.instename',
        'p.auditplanid',
        DB::raw("TO_CHAR(s.entrymeetdate, 'DD-MM-YYYY') as entrymeetdate"),
        DB::raw("TO_CHAR(s.exitmeetdate, 'DD-MM-YYYY') as exitmeetdate"),
    );

    $query->groupBy(
        'dept.deptcode',

        'dept.deptesname',
        'inst.instid',
        'inst.instename',
        'p.auditplanid',
        's.entrymeetdate',
        's.exitmeetdate'
    );

    $query->orderBy('dept.deptcode')
          ->orderBy('inst.instename');

    return $query->get();
}




public static function getepacsdetails($data)
{
    $deptcode   = $data['deptcode'] ?? null;
    $regioncode = $data['regioncode'] ?? null;
    $distcode   = $data['distcode'] ?? null;

    $isAllRegion = empty($regioncode) || in_array('A', (array)$regioncode);
    $isAllDist   = empty($distcode) || in_array('A', (array)$distcode);

    $query = DB::table('audit.mst_institution as ins')

        ->select(
            'dept.deptcode',
            'dept.deptesname',
               DB::raw("
                CASE
                    WHEN " . ($isAllRegion ? "TRUE" : "FALSE") . "
                    THEN 'A'
                    ELSE MAX(r.regioncode)::text
                END as regioncode
            "),

            DB::raw("
                CASE
                    WHEN " . ($isAllDist ? "TRUE" : "FALSE") . "
                    THEN 'A'
                    ELSE MAX(di.distcode)::text
                END as distcode
            "),

            DB::raw("
                CASE
                    WHEN " . ($isAllRegion ? "TRUE" : "FALSE") . "
                    THEN 'All'
                    ELSE r.regionename
                END as regionename
            "),

            DB::raw("
                CASE
                    WHEN " . ($isAllDist ? "TRUE" : "FALSE") . "
                    THEN 'All'
                    ELSE di.distename
                END as distename
            "),

            DB::raw("COUNT(DISTINCT p.auditplanid) as total_planned"),

            DB::raw("
            COUNT(DISTINCT CASE
                WHEN s.auditplanid IS NULL
                THEN p.auditplanid
            END) as not_scheduled_count
        "),

           DB::raw("
                COUNT(DISTINCT CASE
                    WHEN s.auditplanid IS NOT NULL AND s.exitmeetdate IS NULL
                    THEN s.auditplanid
                END) as scheduled_count
            "),

       
            DB::raw("
                COUNT(DISTINCT CASE
                    WHEN
                    s.entrymeetdate IS NOT NULL
                    AND s.exitmeetdate IS NOT NULL
                    THEN s.auditplanid
                END) as exit_meeting_count
            ")
        )

        ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'ins.deptcode')

        ->join(self::$regionTable . ' as r', 'r.regioncode', '=', 'ins.regioncode')

        ->join(self::$District_Table . ' as di', 'di.distcode', '=', 'ins.distcode')

        ->join('audit.auditplan as p', 'p.instid', '=', 'ins.instid')
        ->join('audit.auditplanmapping as pm', 'pm.planmappingid', '=', 'dept.planmappingid')

        ->leftJoin('audit.inst_auditschedule as s', 's.auditplanid', '=', 'p.auditplanid')

        ->where('dept.statusflag', 'Y')
        ->where('ins.statusflag', 'Y')
        ->where('p.datafromapi', 'Y');

    // Department Filter
    if (!empty($deptcode) && !in_array('A', (array)$deptcode)) {
        $query->whereIn('dept.deptcode', (array)$deptcode);
    }

    // Region Filter
    if (!$isAllRegion) {
        $query->whereIn('r.regioncode', (array)$regioncode);
    }

    // District Filter
    if (!$isAllDist) {
        $query->whereIn('di.distcode', (array)$distcode);
    }

    $query->groupBy(
        'dept.deptcode',
        'dept.deptesname',
        DB::raw("
            CASE
                WHEN " . ($isAllRegion ? "TRUE" : "FALSE") . "
                THEN 'All'
                ELSE r.regionename
            END
        "),

        DB::raw("
            CASE
                WHEN " . ($isAllDist ? "TRUE" : "FALSE") . "
                THEN 'All'
                ELSE di.distename
            END
        ")
    );

    $query->orderBy('dept.deptcode');

    return $query->get();
}



public static function getEprDept()
{
    $query = DB::table('audit.auditplan as ap')

        ->join(
            'audit.mst_institution as ins',
            'ins.instid',
            '=',
            'ap.instid'
        )

        ->join(
            'audit.mst_dept as d',
            'd.deptcode',
            '=',
            'ins.deptcode'
        )



        ->where('ap.datafromapi', 'Y')
        ->where('ins.statusflag', 'Y')

        ->select(
            'd.deptcode',
            'd.deptelname',
            'd.depttlname',
            'd.deptesname',
            'd.depttsname'
        )

        ->distinct()

        ->orderBy('d.deptcode', 'ASC')

        ->get();

    return $query;
}

}
