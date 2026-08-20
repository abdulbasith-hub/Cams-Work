<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
// class UserModel extends Model
// {ṇ
//     use HasFactory;
// }

class InspectionModel extends Model

{
    protected static $auditeinstmap = BaseModel::AUDITOR_INSTMAPPING_TABLE;

    protected static $regionTable = BaseModel::REGION_TABLE;


    protected static $distTable = BaseModel::DIST_Table;


    protected static $department_table = BaseModel::DEPARTMENT_TABLE;
    protected static $inst_table = BaseModel::INSTITUTION_TABLE;

    protected static $auditplan_table = BaseModel::AUDITPLAN_TABLE;
    protected static $instauditschedule_table = BaseModel::INSTSCHEDULE_TABLE;

    protected static $instauditschedulemem_table = BaseModel::INSTSCHEDULEMEM_TABLE;

    protected static $subcategory_table = BaseModel::SUBCATEGORY_TABLE;
    protected static $mstauditeeinscategory_table = BaseModel::MSTAUDITEEINSCATEGORY_TABLE;

    protected static $userchargedetail_table = BaseModel::USERCHARGEDETAIL_TABLE;
    protected static $chargedetail_table = BaseModel::CHARGEDETAIL_TABLE;
    protected static $rolemapping_table = BaseModel::ROLEMAPPING_TABLE;

    protected static $ProcessFlag_Table             =  BaseModel::PROCESSFLAG_TABLE;
    protected static $MajorObj_Table                =  BaseModel::MAINOBJ_TABLE;
    protected static $SubObj_Table                  =  BaseModel::SUBOBJ_TABLE;
    protected static $TransAuditSlip_Table          =  BaseModel::TRANSAUDITSLIP_TABLE;

    protected static $TransWorkAllocation_Table     =  BaseModel::TRANSWORKALLOCATION_TABLE;
    protected static $MapWorkAllocation_Table       =  BaseModel::MAPWORKALLOCATION_TABLE;
    protected static $MajWorkAllocation_Table       =  BaseModel::MAJWORKALLOCATION_TABLE;
    protected static $SlipHistroyDetails_Table      =  BaseModel::SLIPHISTORYDETAILS_TABLE;

    protected static $UserDetails_Table             =  BaseModel::USERDETAIL_TABLE;
    protected static $AuditQuarter_Table            =  BaseModel::AUDITQUARTER_TABLE;
    protected static $AuditeeUserDetail_Table       =  BaseModel::AUDITEEUSERDETAIL_TABLE;

    protected static $auditinspection_table       = BaseModel::AUDITINSPECTION_TABLE;
    protected static $transauditinspection_table  = BaseModel::TRANSAUDITINSPECTION_TABLE;
    protected static $inspectionhistory_table     = BaseModel::INSPECTIONHISTORY_TABLE;

    protected static $designation_table = BaseModel::DESIGNATION_TABLE;

    protected static $auditplanteammem_table = BaseModel::AUDITPLANTEAMMEM_TABLE;
    protected static $roletypemapping_table = BaseModel::ROLETYPEMAPPING_TABLE;
    protected static $roletype_table = BaseModel::ROLETYPE_TABLE;
    protected static $roleaction_table = BaseModel::ROLEACTION_TABLE;





    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon'; // Custom column name for `updated_at` (if you have it)

    protected $table = 'audit.mst_designation';


    //==========================================================================Common Department Fetch-==========================================================
    public static function getDept()
    {
        try {
            $query = DB::table(self::$department_table)
                ->where('statusflag', 'Y')
                ->select('deptcode', 'deptelname', 'depttlname', 'deptesname', 'depttsname')
                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {


            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        }
    }

    public static function getRegion()
    {
        try {
            $query = DB::table(self::$auditeinstmap . ' as inst')
                ->join(self::$regionTable . ' as reg', 'reg.regioncode', '=', 'inst.regioncode')
                ->where('inst.statusflag', 'Y')
                ->select('reg.regioncode', 'reg.regionename', 'reg.regiontname')
                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';
            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        }
    }

    public static function getDistrict()
    {
        try {
            $query = DB::table(self::$auditeinstmap . ' as inst')
                ->join(self::$distTable . ' as dist', 'dist.distcode', '=', 'inst.distcode')
                ->where('inst.statusflag', 'Y')
                ->select('dist.distcode', 'dist.distename', 'dist.disttname')
                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {

            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        }
    }

    public static function getInstdetails($data)
    {
        try {
            $deptcode = $data['deptcode'];
            $regioncode = $data['regioncode'];
            $distcode = $data['distcode'];

            $session = session('charge');
            $sessionuser = session('user');


            $sessionuserid = $sessionuser->userid;

           $isTeamHead = DB::table('audit.inst_schteammember as mem')
                ->Join(self::$instauditschedule_table . ' as schd', 'schd.auditscheduleid', '=', 'mem.auditscheduleid')
                ->Join(self::$auditplan_table . ' as plan', 'plan.auditplanid', '=', 'schd.auditplanid')
                ->Join(self::$inst_table . ' as inst', 'plan.instid', '=', 'inst.instid')
                ->Join(self::$department_table . ' as dept', 'dept.deptcode', '=', 'inst.deptcode')
                ->Join(self::$UserDetails_Table . ' as details', 'details.deptuserid', '=', 'mem.userid')
                ->Join(self::$userchargedetail_table . ' as uc', 'details.deptuserid', '=', 'uc.userid')
                ->Join(self::$designation_table . ' as desig', 'details.desigcode', '=', 'desig.desigcode')
                ->whereColumn('plan.planmappingid', 'dept.planmappingid')
                ->where('uc.statusflag', 'Y')
                ->where('auditteamhead', 'Y')
                ->where('mem.statusflag', 'Y')
                ->where('details.deptuserid', $sessionuserid)
                ->exists();

            if ($isTeamHead) {
                $auditteamhead = 'Y';
            } else {
                $auditteamhead = '';
            }

            $query = DB::table(self::$inst_table . ' as inst')
                ->Join(self::$department_table . ' as dept', 'dept.deptcode', '=', 'inst.deptcode')
                ->Join(self::$regionTable . ' as reg', 'reg.regioncode', '=', 'inst.regioncode')
                ->Join(self::$distTable . ' as dist', 'dist.distcode', '=', 'inst.distcode')
                ->Join(self::$auditplan_table . ' as plan', 'plan.instid', '=', 'inst.instid')
                ->Join(self::$instauditschedule_table . ' as schd', 'schd.auditplanid', '=', 'plan.auditplanid')
                ->leftJoin(self::$transauditinspection_table . ' as inspect', 'inspect.auditscheduleid', '=', 'schd.auditscheduleid')
                ->leftJoin(self::$UserDetails_Table . ' as details', 'details.deptuserid', '=', 'inspect.createdby')
                ->leftJoin(self::$designation_table . ' as desig', 'details.desigcode', '=', 'desig.desigcode');
		if ($auditteamhead == 'Y') {
                $query->Join(self::$inspectionhistory_table . ' as hist', 'inspect.auditscheduleid', '=', 'hist.auditscheduleid');
          	  }

                // ->leftJoin(DB::raw("(
                //     SELECT DISTINCT ON (auditscheduleid) *
                //     FROM " . self::$transauditinspection_table . "
                //     ORDER BY auditscheduleid, createdon DESC
                // ) AS inspect"), 'inspect.auditscheduleid', '=', 'schd.auditscheduleid')

                $query->select(
                    'inspect.auditinspectionid as inspectionid',
                    'inspect.processcode as inspectprocesscode',
                    'inspect.createdby as initiatedId',
                    'inspect.activeinspection',
                    'details.username',
                    'desig.desigelname',
                    'reg.regionename',
                    'reg.regiontname',
                    'dist.distename',
                    'dist.disttname',
                    'inst.instename',
                    'inst.insttname',
                    'inst.instid',
                    'dept.deptesname',
                    'dept.depttsname',
                    'dept.deptcode',
                    'schd.auditscheduleid',
                    'schd.entrymeetdate',
                    'schd.exitmeetdate',
                    'schd.statusflag',
                    'schd.fromdate',
                    'schd.todate',
                    'schd.workallocationflag',
                    DB::raw("(
                    SELECT head_tm.userid
                    FROM audit.inst_schteammember AS head_tm
                    WHERE head_tm.auditscheduleid = schd.auditscheduleid AND head_tm.auditteamhead = 'Y' AND head_tm.statusflag='Y'
                    LIMIT 1
                ) AS teamhead_userid"),
                    DB::raw("(
                    SELECT head.usertamilname || ' - ' || desig.desigtlname
                    FROM audit.inst_schteammember AS head_tm
                    JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
                    JOIN audit.mst_designation AS desig ON desig.desigcode = head.desigcode
                    WHERE head_tm.auditscheduleid = schd.auditscheduleid AND head_tm.auditteamhead = 'Y' AND head_tm.statusflag='Y'
                    LIMIT 1
                ) AS team_head_ta"),
                    DB::raw("(
                    SELECT COALESCE(STRING_AGG(member.usertamilname || ' - ' || desig2.desigtlname, ', '), '')
                    FROM audit.inst_schteammember AS member_tm
                    JOIN audit.deptuserdetails AS member ON member.deptuserid = member_tm.userid
                    JOIN audit.mst_designation AS desig2 ON desig2.desigcode = member.desigcode
                    WHERE member_tm.auditscheduleid = schd.auditscheduleid AND member_tm.auditteamhead != 'Y'  AND member_tm.statusflag='Y'
                ) AS team_members_ta"),
                    DB::raw("(
                    SELECT head.username || ' - ' || desig.desigelname
                    FROM audit.inst_schteammember AS head_tm
                    JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
                    JOIN audit.mst_designation AS desig ON desig.desigcode = head.desigcode
                    WHERE head_tm.auditscheduleid = schd.auditscheduleid AND head_tm.auditteamhead = 'Y' AND head_tm.statusflag='Y'
                    LIMIT 1
                ) AS team_head_en"),
                    DB::raw("(
                    SELECT COALESCE(STRING_AGG(member.username || ' - ' || desig2.desigelname, ', '), '')
                    FROM audit.inst_schteammember AS member_tm
                    JOIN audit.deptuserdetails AS member ON member.deptuserid = member_tm.userid
                    JOIN audit.mst_designation AS desig2 ON desig2.desigcode = member.desigcode
                    WHERE member_tm.auditscheduleid = schd.auditscheduleid AND member_tm.auditteamhead != 'Y'  AND member_tm.statusflag='Y'
                ) AS team_members_en"),

                )
		->whereColumn('plan.planmappingid', 'dept.planmappingid')
                ->when($auditteamhead === 'Y', function ($query) use ($auditteamhead, $sessionuserid) {
                    $query->join(self::$instauditschedulemem_table . ' as mem', 'inspect.auditscheduleid', '=', 'mem.auditscheduleid')
                        ->join(self::$UserDetails_Table . ' as du', 'du.deptuserid', '=', 'mem.userid')
                        // ->whereColumn('inspect.auditscheduleid', 'mem.auditscheduleid')
                        ->whereIn('inspect.processcode', ['H', 'T', 'C', 'E'])
                        ->where('mem.auditteamhead', $auditteamhead)
                        ->where('mem.userid', $sessionuserid);
                })
                ->when($deptcode, function ($query) use ($deptcode) {
                    $query->where('inst.deptcode', $deptcode);
                })
                ->when($regioncode, function ($query) use ($regioncode) {
                    $query->where('inst.regioncode', $regioncode);
                })
                ->when($distcode, function ($query) use ($distcode) {
                    $query->where('inst.distcode', $distcode);
                })
  		->where('schd.statusflag', 'F')
              
                // ->distinct()
                ->orderBy('entrymeetdate', 'asc')
                ->orderBy('instename', 'asc');
                
            // $querySql = $query->toSql();
            // //  return $querySql;
            // $querySql = $query->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);
            return $query->get();
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }


    public static function fetch_deptbaseddata(
        ?string $deptcode = null,
        ?string $regioncode = null,
        ?string $distcode = null,
        string $getval,
        string $formname
    ) {

        try {
            $query = DB::table(self::$auditeinstmap . ' as inst')
                ->where('inst.statusflag', 'Y')
                ->when($deptcode, function ($query) use ($deptcode) {
                    $query->where('inst.deptcode', $deptcode);
                });
            //->where('inst.deptcode', $deptcode);
            switch ($getval) {
                // protected static $auditorinstmappingTable = BaseModel::AUDITORINSTMAPPING_TABLE;
                case 'region':
                    $query->join(self::$regionTable . ' as re', 're.regioncode', '=', "inst.regioncode")
                        ->select("inst.regioncode", 're.regionename', 're.regiontname')
                        ->distinct();
                    $query->orderBy('re.regionename', 'ASC');
                    break;


                case 'district':
                    $query->join(self::$regionTable . ' as re', 're.regioncode', '=', "inst.regioncode")
                        ->join(self::$distTable . ' as d', 'd.distcode', '=', "inst.distcode")

                        ->select("inst.distcode", 'd.distename', 'd.disttname')
                        ->distinct();
                    $query->where("inst.regioncode", $regioncode);
                    $query->orderBy("d.distename", 'ASC');
                    break;




                default:
                    throw new InvalidArgumentException("Invalid 'getval' provided. Allowed values are 'region', 'district', or 'institution'.");
            }

            $result = $query->get();
            $data = [
                'deptcode' => $deptcode,
                'regioncode' => $regioncode,
                'distcode' => $distcode,
            ];

            switch ($formname) {

                case 'checkschedulestatus':
                    $details = self::getInstdetails($data);
                    break;

                default:
                    throw new InvalidArgumentException("formname 'formname' provided. Allowed values are 'checkschedulestatus' ");
            }

            return [
                'data' => $result,
                'details' => $details,

            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }


    public static function getscheduledetails($auditscheduleid)
    {
        try {
            if (empty($auditscheduleid)) {
                throw new InvalidArgumentException("Invalid arguments provided.");
            }
            $query = DB::table(self::$instauditschedule_table . ' as schd')
                ->join(self::$instauditschedulemem_table . ' as mem', 'mem.auditscheduleid', '=', "schd.auditscheduleid")
                ->join(self::$auditplan_table . ' as plan', 'plan.auditplanid', '=', "schd.auditplanid")
                ->join(self::$inst_table . ' as inst', 'inst.instid', '=', "plan.instid")
                ->join(self::$AuditQuarter_Table . ' as quat', 'inst.audit_quarter', '=', "quat.auditquartercode")
                ->leftJoin(self::$subcategory_table . ' as sub', 'sub.auditeeins_subcategoryid', '=', "inst.subcatid")
                ->join(self::$mstauditeeinscategory_table . ' as cat', 'cat.catcode', '=', "inst.catcode")
                ->join(self::$department_table . ' as dept', 'inst.deptcode', '=', "dept.deptcode")


                ->select(
		    'inst.instid',
                    'mem.schteammemberid',
                    'dept.inspectionrejoinderlimit',
                    'plan.auditplanid',
                    'quat.auditquarter',
                    'inst.mandays',
                    'inst.instename',
                    'inst.insttname',
                    'cat.catename',
                    'cat.cattname',
                    'sub.subcatename',
                    'sub.subcattname',
                    'schd.fromdate',
                    'schd.todate',
                    'schd.entrymeetdate',
                    'schd.exitmeetdate',
		DB::raw("(
                SELECT COALESCE(STRING_AGG(member_tm.userid::text, ', '), '')
                FROM audit.inst_schteammember AS member_tm
                WHERE member_tm.auditscheduleid = schd.auditscheduleid
                AND member_tm.auditteamhead != 'Y'
                AND member_tm.statusflag='Y'
           	 ) AS team_members_userids"),
                    DB::raw("(
                    SELECT head_tm.userid
                    FROM audit.inst_schteammember AS head_tm
                    WHERE head_tm.auditscheduleid = schd.auditscheduleid AND head_tm.auditteamhead = 'Y' AND head_tm.statusflag='Y'
                    LIMIT 1
                ) AS teamhead_userid"),
                    DB::raw("(
                SELECT head.usertamilname || ' - ' || desig.desigtlname
                FROM audit.inst_schteammember AS head_tm
                JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
                JOIN audit.mst_designation AS desig ON desig.desigcode = head.desigcode
                WHERE head_tm.auditscheduleid = schd.auditscheduleid AND head_tm.auditteamhead = 'Y' AND head_tm.statusflag='Y'
                LIMIT 1
            ) AS team_head_ta"),
                    DB::raw("(
                SELECT COALESCE(STRING_AGG(member.usertamilname || ' - ' || desig2.desigtlname, ', '), '')
                FROM audit.inst_schteammember AS member_tm
                JOIN audit.deptuserdetails AS member ON member.deptuserid = member_tm.userid
                JOIN audit.mst_designation AS desig2 ON desig2.desigcode = member.desigcode
                WHERE member_tm.auditscheduleid = schd.auditscheduleid AND member_tm.auditteamhead != 'Y'  AND member_tm.statusflag='Y'
            ) AS team_members_ta"),
                    DB::raw("(
                SELECT head.username || ' - ' || desig.desigelname
                FROM audit.inst_schteammember AS head_tm
                JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
                JOIN audit.mst_designation AS desig ON desig.desigcode = head.desigcode
                WHERE head_tm.auditscheduleid = schd.auditscheduleid AND head_tm.auditteamhead = 'Y' AND head_tm.statusflag='Y'
                LIMIT 1
            ) AS team_head_en"),
                    DB::raw("(
                SELECT COALESCE(STRING_AGG(member.username || ' - ' || desig2.desigelname, ', '), '')
                FROM audit.inst_schteammember AS member_tm
                JOIN audit.deptuserdetails AS member ON member.deptuserid = member_tm.userid
                JOIN audit.mst_designation AS desig2 ON desig2.desigcode = member.desigcode
                WHERE member_tm.auditscheduleid = schd.auditscheduleid AND member_tm.auditteamhead != 'Y'  AND member_tm.statusflag='Y'
            ) AS team_members_en"),
                )
                // ->distinct()
                //  ->where('mem.statusflag', 'Y')
                ->where('schd.auditscheduleid', $auditscheduleid)
                ->get();

            // $querySql = $query->toSql();
            // //  return $querySql;
            // $querySql = $query->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);
            // exit;
            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getpendingparadetails(
        $auditscheduleid,
        $slipsts,
        $filterapply,
    ) {
        // return $auditscheduleid;
        try {
            if (empty(trim($auditscheduleid))) {
                throw new \Exception('auditscheduleid is empty or invalid');
            }
            $table = self::$TransAuditSlip_Table;
            $schteammem = self::$instauditschedulemem_table;
            $userdetails = self::$UserDetails_Table;

            $query =  DB::table($table . ' as trans')
                ->join(self::$ProcessFlag_Table . ' as p', 'p.processcode', '=', 'trans.processcode')
                ->join(self::$MajorObj_Table . ' as m', 'm.mainobjectionid', '=',  'trans.mainobjectionid')
                ->leftJoin(self::$SubObj_Table . ' as s', 's.subobjectionid', '=',  'trans.subobjectionid')
                ->join(self::$auditplan_table . ' as ap', 'ap.auditplanid', '=',  'trans.auditplanid')
                ->join(self::$AuditQuarter_Table . ' as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
                ->join(self::$instauditschedulemem_table . ' as schteam', 'schteam.schteammemberid', '=',  'trans.schteammemberid')
                ->leftJoin(self::$UserDetails_Table . ' as dud', 'dud.deptuserid', '=',  'trans.createdby')
                // ->leftJoin(self::$transauditinspection_table . ' as inspect', 'inspect.auditscheduleid', '=',  'trans.auditscheduleid')

                ->where('trans.auditscheduleid', $auditscheduleid)
                ->select(
                    // 'inspect.processcode as inspectprocesscode',
                    // 'inspect.createdby as initiated',
                    'm.objectionename',
                    // 'schteam.auditteamhead',

                    'trans.mainslipnumber',
                    'trans.amtinvolved',
                    'trans.slipdetails',
                    'p.processelname',
                    'p.processcode',
                    'dud.username as auditorname_en',
                    'dud.usertamilname as auditorname_ta',
                    'trans.liability',
                    'trans.auditslipid',
                    'maq.auditquartercode',
                    DB::raw("CASE WHEN trans.subobjectionid IS NOT NULL THEN s.subobjectionename
                                                        ELSE 'N/A'
                                                    END AS subobjectionename"), // Conditionally show subobjectionename or 'N/a'
                    DB::raw("TO_CHAR(trans.createdon, 'DD-MM-YYYY hh:MI AM') as createddate"),
                    DB::raw("CASE WHEN trans.rejoinderstatus = 'Y' THEN 'Yes' ELSE 'No' END AS rejoinderstatus"),
                    DB::raw("(SELECT dud.username FROM $schteammem AS schteam
                                                JOIN $userdetails AS dud ON dud.deptuserid = schteam.userid
                                                WHERE schteam.auditscheduleid = trans.auditscheduleid
                                                AND schteam.auditteamhead = 'Y' LIMIT 1) AS teamheadname_en"),
                    // DB::raw("(SELECT dud.usertamilname FROM $schteammem AS schteam
                    //                                 JOIN $userdetails AS dud ON dud.deptuserid = schteam.userid
                    //                                 WHERE schteam.auditscheduleid = $table.auditscheduleid
                    //                                 AND schteam.auditteamhead = 'Y' LIMIT 1) AS teamheadname_ta")
                )
                ->orderBy('trans.auditslipid', 'asc')
                ->distinct();

            // Apply filters conditionally
            if ($filterapply == true) {
                if ($slipsts != 'all') {
                    if ($slipsts == 'P') {
                        $query->whereNotIn('trans.processcode', ['A', 'X']);
                    } else {
                        $query->where('trans.processcode', $slipsts);
                    }
                }

                // if ($quartercode != 'all') {
                //     $query->where('maq.auditquartercode', $quartercode);
                // }
            }

            // Execute the query to fetch data
            $query = $query->get();
            // $querySql = $query->toSql();
            // //  return $querySql;
            // $querySql = $query->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);


            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getSlipDetailsHistory($slipId)
    {
        try {
            if (empty(trim($slipId))) {
                throw new \Exception('slip ID is empty or invalid');
            }
            $table = self::$SlipHistroyDetails_Table;
            // Simulate fetching slip details from the database (replace this with actual logic)
            $slipDetails = DB::table($table)
                ->leftjoin(self::$ProcessFlag_Table . ' as p', 'p.processcode', '=', $table . '.processcode')
                ->leftjoin(self::$MajorObj_Table . ' as m', 'm.mainobjectionid', '=', $table . '.mainobjectionid')
                ->leftjoin(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', $table . '.subobjectionid')
                ->leftjoin(self::$auditplan_table . ' as ap', 'ap.auditplanid', '=', $table . '.auditplanid')
                //->leftjoin(self::$AuditQuarter_Table . ' as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
                ->leftjoin(self::$AuditeeUserDetail_Table . ' as auditee', 'ap.instid', '=', 'auditee.instid')
                ->leftjoin(self::$inst_table . '  as inst', 'ap.instid', '=', 'inst.instid')
                ->leftjoin(self::$instauditschedulemem_table . ' as schteam', 'schteam.schteammemberid', '=', $table . '.schteammemberid')
                ->leftjoin(self::$UserDetails_Table . ' as du', 'schteam.userid', '=', 'du.deptuserid')

                ->select(
                    $table . '.auditscheduleid',
                    $table . '.forwardedon',
                    $table . '.forwardedbyusertypecode',
                    'du.username',
                    'auditee.username as auditeename',
                    'schteam.auditteamhead',
                    'm.objectionename',
                    DB::raw("CASE

                            WHEN $table.subobjectionid IS NOT NULL THEN s.subobjectionename
                                    ELSE 'N/A'
                                END AS subobjectionename"),
                    $table . '.mainslipnumber',
                    $table . '.slipdetails',
                    $table . '.amtinvolved',
                    $table . '.remarks',
                    $table . '.remarks',
                    'p.processelname',
                    DB::raw("CASE WHEN $table.liability = 'Y' THEN 'Yes' ELSE 'No' END AS liability"), // Transform in query
                    DB::raw("CASE WHEN $table.severityid = 'M' THEN 'Medium'
                                        WHEN $table.severityid = 'H' THEN 'High'
                                        WHEN $table.severityid = 'L' THEN 'Low'
                                        ELSE 'Unknown'
                                    END AS severity"),
                )
                ->where($table . '.auditslipid', $slipId)
                ->orderBy($table . '.transhistoryid', 'desc')
                ->get();



            $LiabilityDetails = DB::table('audit.liability as liability')
                ->where('liability.auditslipid', $slipId)
                ->get();


            return response()->json([
                'status' => 'success',
                'data' => $slipDetails,
                'liability' => $LiabilityDetails
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getSlipHistoryDetails($slipId)
    {
        try {
            if (empty(trim($slipId))) {
                throw new \Exception('Slip ID is empty or invalid');
            }

            $query = DB::table(self::$SlipHistroyDetails_Table . ' as hist')
                ->join(self::$TransAuditSlip_Table . ' as trans_auditslip', 'trans_auditslip.auditslipid', '=', 'hist.auditslipid')
                ->join(self::$ProcessFlag_Table . ' as p', 'p.processcode', '=', 'hist.processcode')
                ->join(self::$instauditschedulemem_table . ' as schteam', 'schteam.schteammemberid', '=', 'trans_auditslip.schteammemberid')
                ->join(self::$UserDetails_Table . ' as dud_sch', 'dud_sch.deptuserid', '=', 'schteam.userid') // Team member details
                ->leftJoin(self::$UserDetails_Table . ' as dud_forwardedby', 'dud_forwardedby.deptuserid', '=', 'hist.forwardedby') // Forwarded by (Auditor)
                ->leftJoin(self::$UserDetails_Table . ' as dud_forwardedto', 'dud_forwardedto.deptuserid', '=', 'hist.forwardedto') // Forwarded to (Auditor)
                ->leftJoin(self::$AuditeeUserDetail_Table . ' as aud_forwardedby', 'aud_forwardedby.auditeeuserid', '=', 'hist.forwardedby') // Forwarded by (Auditee)
                ->leftJoin(self::$AuditeeUserDetail_Table . ' as aud_forwardedto', 'aud_forwardedto.auditeeuserid', '=', 'hist.forwardedto') // Forwarded to (Auditee)
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
                ->orderBy('hist.transhistoryid', 'asc') // Order by transhistoryid descending
                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
        // $querySql = $query->toSql();
        // //  return $querySql;
        // $querySql = $query->toSql();
        // $bindings = $query->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );
        // print_r($finalQuery);

        //   return response()->json(['status' => 'success', 'data' => $auditSlips]);
    }

    public static function getchecklistdetails($auditscheduleid, $sessiondesigcode, $teamheadFlag, $auditinspectionid)
    {
        try {
            if (empty(trim($auditscheduleid))) {
                throw new \Exception('Audit schedule ID is empty or invalid');
            }

            $sessioncharge = session('charge');
            $rolemappingid = $sessioncharge->rolemappingid;
            $details = DB::table(self::$instauditschedule_table . ' as schd')
                ->join(self::$auditplan_table . ' as plan', 'plan.auditplanid', '=', 'schd.auditplanid')
                ->join(self::$inst_table . ' as inst', 'inst.instid', '=', 'plan.instid')
                ->join(self::$mstauditeeinscategory_table . ' as cat', 'cat.catcode', '=', "inst.catcode")
                ->where('schd.auditscheduleid', $auditscheduleid)
                ->select('cat.if_subcategory', 'inst.deptcode')
                ->get();


            $deptcode = $details[0]->deptcode;

            $subcatid = $details[0]->if_subcategory;
            $query = DB::table(self::$instauditschedule_table . ' as schd')
                ->join(self::$auditplan_table . ' as plan', 'plan.auditplanid', '=', 'schd.auditplanid')
                ->join(self::$inst_table . ' as inst', 'inst.instid', '=', 'plan.instid')
                ->join(self::$auditinspection_table . ' as ains', function ($join) use ($subcatid) {
                    $join->on('ains.deptcode', '=', 'inst.deptcode')
                        ->on('ains.catcode', '=', 'inst.catcode');
                    //    ->on('ains.subcatid', '=', 'inst.subcatid');
                    if ($subcatid == 'Y') {
                        $join->on('ains.subcatid', '=', 'inst.subcatid');
                    }
                });


            if (empty($teamheadFlag) || $teamheadFlag === 'N') {
                if ($sessiondesigcode == '01' && $rolemappingid == '2') {

                    $dirdesigcode = self::getdirectordesig($deptcode);

                    $query->where('ains.desigcode', $dirdesigcode);
                } else {
                    $query->where('ains.desigcode', $sessiondesigcode);
                }
            } else if ($teamheadFlag === 'Y') {
                $dirdesigcode = self::getdirectordesig($deptcode);

                $query->whereExists(function ($subQuery) use ($auditinspectionid, $dirdesigcode) {
                    $subQuery->select(DB::raw(1))
                        ->from(self::$transauditinspection_table . ' as trans')
                        ->where('trans.auditinspectionid', $auditinspectionid)
                        ->where(function ($inner) use ($dirdesigcode) {
                            $inner->where(function ($q) use ($dirdesigcode) {
                                // case: initiatedbydesigcode = '01'
                                $q->where('trans.initiatedbydesigcode', '01')
                                    ->where('ains.desigcode', $dirdesigcode);
                            })
                                ->orWhere(function ($q) {
                                    // case: anything else
                                    $q->whereColumn('trans.initiatedbydesigcode', 'ains.desigcode');
                                });
                        });
                });
            }


            $query->select('ains.*')
                ->where('schd.auditscheduleid', $auditscheduleid)
                ->where('ains.statusflag', 'Y')
		->orderBy('ains.aifid', 'asc')
                ->orderByDesc('ains.heading_en')
                ->orderByDesc('ains.checkpoint_en');
            // $querySql = $query->toSql();
            // //  return $querySql;
            // $querySql = $query->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);
            return $query->get();
        } catch (\Illuminate\Database\QueryException $e) {

            // Customize the error message while logging the actual one
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';
            // Optionally log actual SQL error
            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {
            // Handle other errors
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function inspectchecklist_insert($data, $auditinspectionid)
    {
        DB::beginTransaction(); // Start the transaction
        try {
            $table = self::$transauditinspection_table;
            $query = DB::table($table);

            if ($auditinspectionid) {
                $query->where('auditinspectionid', '!=', $auditinspectionid);
            }

            $existingInspection = (clone $query)
                ->where('auditscheduleid', $data['auditscheduleid'])
                ->where('activeinspection', 'A')
                ->first();

            if ($existingInspection) {
                throw new \Exception('The audit inspection for this institution is currently in progress');
            }
            if ($auditinspectionid) {
                $updated = DB::table($table)->where('auditinspectionid', $auditinspectionid)->update($data);

                if ($updated === 0) {
                    throw new \Exception('Failed to update the Inspection Check Points');
                }

                $inspectnewDet = DB::table($table)->where('auditinspectionid', $auditinspectionid)->first();
                $fetchauditinspectionid = $inspectnewDet->auditinspectionid;

                DB::commit();
                return $inspectnewDet;
            } else {
                $insertdata  = DB::table($table)->insertGetId($data, 'auditinspectionid');
                $inspectnewDet = DB::table($table)->where('auditinspectionid', $insertdata)->first();
                $fetchauditinspectionid = $inspectnewDet->auditinspectionid;

                if (!$fetchauditinspectionid) {
                    throw new \Exception('Failed to insert the Inspection Check Points');
                }

                DB::commit();
                return $inspectnewDet;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Customize the error message while logging the actual one
            $customMessage = 'A database error occurred while saving the inspection checklist. Please contact the administrator.';
            // Optionally log actual SQL error
            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage(), 409);
        }
    }
    public static function slipremarks_insert($data, $auditinspectionid, $auditscheduleid)
    {
        DB::beginTransaction(); // Start the transaction
        try {
            $table = self::$transauditinspection_table;
            $query = DB::table($table);

            if ($auditinspectionid) {
                $query->where('auditinspectionid', '!=', $auditinspectionid);
            }
            $existingInspection = (clone $query)
                ->where('auditscheduleid', $auditscheduleid)
                ->where('activeinspection', 'A')
                ->first();

            if ($existingInspection) {
                throw new \Exception('The audit inspection for this institution is currently in progress');
            }
            if ($auditinspectionid) {



                $updated = DB::table($table)->where('auditinspectionid', $auditinspectionid)->update($data);

                if ($updated === 0) {
                    throw new \Exception('Failed to update the Slip Remarks');
                }

                $inspectnewDet = DB::table($table)->where('auditinspectionid', $auditinspectionid)->first();
                $fetchauditinspectionid = $inspectnewDet->auditinspectionid;

                DB::commit();
                return $inspectnewDet;
            } else {
                $insertdata  = DB::table($table)->insertGetId($data, 'auditinspectionid');
                $inspectnewDet = DB::table($table)->where('auditinspectionid', $insertdata)->first();
                $fetchauditinspectionid = $inspectnewDet->auditinspectionid;

                if (!$fetchauditinspectionid) {
                    throw new \Exception('Failed to insert the Slip remarks');
                }

                DB::commit();
                return $inspectnewDet;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Customize the error message while logging the actual one
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';
            // Optionally log actual SQL error
            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getInspectiondetbyId(?string $auditscheduleid = null, ?string $auditinspectionid = null, $teamHead)

    {
        try {
            if (empty($auditscheduleid)) {
                return collect(); // Return empty collection if ID is null or empty
            }

            $sessioncharge = session('charge');
            $sessiondesigcode = $sessioncharge->desigcode;
            //return $auditinspectionid;
            $query = DB::table(self::$transauditinspection_table . ' as inspect');

            $query
                ->join(self::$instauditschedule_table . ' as schd', 'inspect.auditscheduleid', '=', 'schd.auditscheduleid')
                ->join(self::$auditplan_table . ' as plan', 'plan.auditplanid', '=', 'schd.auditplanid')
                ->join(self::$inst_table . ' as inst', 'inst.instid', '=', 'plan.instid')

                ->select(
                    'inspect.processcode as inspectprocesscode',
                    'inspect.createdby as initiated',
                    'inspect.initiatedbydesigcode as initiated',
                    'inspect.auditinspectionid',
                    'inspect.activeinspection',
                    'inspect.transactionno',
                    'inspect.activeinspection',
                    'inspect.rejoinderstatus',
                    'inspect.rejoindercycle',
                    DB::raw("COALESCE(inspect.slipremarks::json->>'content', '') AS slipremarks"),
                )
                ->where('inspect.auditscheduleid', $auditscheduleid);

            $historydata = DB::table(self::$inspectionhistory_table . ' as hist')
                ->join(self::$instauditschedule_table . ' as schd', 'hist.auditscheduleid', '=', 'schd.auditscheduleid')
                ->join(self::$transauditinspection_table . ' as inspect', 'inspect.auditinspectionid', '=', 'hist.auditinspectionid')
                ->join(self::$auditplan_table . ' as plan', 'plan.auditplanid', '=', 'schd.auditplanid')
                ->join(self::$inst_table . ' as inst', 'inst.instid', '=', 'plan.instid')
                ->join(self::$UserDetails_Table . ' as du', 'hist.forwardedby', '=', 'du.deptuserid')
                ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'du.desigcode')
                ->select(
                    //'ains.*',
                    'desig.desigelname',
                    'desig.desigtlname',
                    'du.username',
                    'du.usertamilname',
                    'hist.*',
                    DB::raw("COALESCE(hist.slipremarks::json->>'content', '') AS slipremarks"),
                    DB::raw("COALESCE(hist.remarks::json->>'content', '') AS headremarks"),
                    DB::raw("COALESCE(hist.remarks::json->>'content', '') AS inspectremarks")
                );
            // if ($teamHead != 'Y') {
            //     $historydata->where('inspect.initiatedbydesigcode', $sessiondesigcode);
            // }
            $historydata->where('inspect.auditscheduleid', $auditscheduleid)
                ->orderBy('hist.forwardedon', 'asc');
            $result = $query->get();
            $historydata = $historydata->get();

            return [
                'data' => $result,
                'historydata' => $historydata
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getinspectionDetails($auditscheduleid, $teamHead, $sessionuserid)
    {
        if (empty(trim($auditscheduleid))) {
            throw new \Exception('Audit scheduleid ID is empty or invalid');
        }
        $query = DB::table(self::$transauditinspection_table . ' as inspect');
        $historydata = DB::table(self::$inspectionhistory_table . ' as hist');

        $sessioncharge = session('charge');
        $sessiondesigcode = $sessioncharge->desigcode;
        // if ($teamHead != 'Y') {
        $query
            ->join(self::$instauditschedule_table . ' as schd', 'inspect.auditscheduleid', '=', 'schd.auditscheduleid')
            ->join(self::$auditplan_table . ' as plan', 'plan.auditplanid', '=', 'schd.auditplanid')
            ->join(self::$inst_table . ' as inst', 'inst.instid', '=', 'plan.instid')

            ->select(

                'inspect.*',
                DB::raw("COALESCE(inspect.remarks::json->>'content', '') AS headremarks"),
                DB::raw("COALESCE(inspect.remarks::json->>'content', '') AS inspectremarks"),
            )
            ->where('schd.auditscheduleid', $auditscheduleid);

        $historydata->join(self::$instauditschedule_table . ' as schd', 'hist.auditscheduleid', '=', 'schd.auditscheduleid')
            ->join(self::$transauditinspection_table . ' as inspect', 'inspect.auditinspectionid', '=', 'hist.auditinspectionid')
            ->join(self::$auditplan_table . ' as plan', 'plan.auditplanid', '=', 'schd.auditplanid')
            //  ->leftjoin(self::$auditplanteammem_table . ' as planmem', 'planmem.userid', '=', 'hist.updatedby')
            ->join(self::$inst_table . ' as inst', 'inst.instid', '=', 'plan.instid')
            ->join(self::$UserDetails_Table . ' as du', 'hist.forwardedby', '=', 'du.deptuserid')
            ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'du.desigcode')
            ->select(
                //'ains.*',
                'desig.desigelname',
                'desig.desigtlname',
                'du.username',
                'du.usertamilname',
                'hist.*',
                //    'planmem.teamhead',

                DB::raw("COALESCE(hist.remarks::json->>'content', '') AS headremarks"),
                DB::raw("COALESCE(hist.remarks::json->>'content', '') AS inspectremarks")
            );
        if ($teamHead != 'Y') {
            $historydata->where('inspect.initiatedbydesigcode', $sessiondesigcode);
        }
        $historydata->where('schd.auditscheduleid', $auditscheduleid)
            ->orderBy('hist.forwardedon', 'asc');
        $result = $query->get();
        $historydata = $historydata->get();

        return [
            'data' => $result,
            'historydata' => $historydata
        ];
    }

    public static function getforwardtoDetails($auditscheduleid, $teamHead)
    {
        try {
            if (empty(trim($auditscheduleid))) {
                throw new \Exception('Audit schedule ID is empty or invalid');
            }

            $query = DB::table(self::$instauditschedule_table . ' as schd');

            if ($teamHead != 'Y') {
                $query->join(self::$instauditschedulemem_table . ' as mem', 'mem.auditscheduleid', '=', 'schd.auditscheduleid')
                    ->join(self::$UserDetails_Table . ' as du', 'mem.userid', '=', 'du.deptuserid')
                    ->join(self::$userchargedetail_table . ' as uc', 'uc.userid', '=', 'du.deptuserid')
                    ->join(self::$chargedetail_table . ' as cd', 'cd.chargeid', '=', 'uc.chargeid')
                    ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'cd.rolemappingid')
                    ->where('du.statusflag', 'Y')
                    ->where('mem.statusflag', 'Y')
                    ->where('mem.auditteamhead', 'Y')
                    ->where('uc.statusflag', 'Y')
                    ->where('du.reservelist', 'Y')
                    ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))
                    ->where('schd.auditscheduleid', $auditscheduleid)
                    ->select('du.deptuserid');
            }
            // $querySql = $query->toSql();
            // //  return $querySql;
            // $querySql = $query->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);

            // Fetch results and return
            return $query->first();  // This is important!
        }
        // catch (\Illuminate\Database\QueryException $e) {
        //     // Handle database specific errors
        //     throw new \Exception('Database error occurred. Please contact admin.', 500);
        // }
        catch (\Exception $e) {
            // Handle other errors
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function insert_inspecthistorydetails($historytrans, $auditinspectionid)
    {
        DB::beginTransaction(); // Begin the transaction

        try {
            $isUpdated = false;
            $isInserted = false;

            // Check if the auditslipid condition is provided and exists
            if ($auditinspectionid !== null) {
                $auditinspectionidExists = DB::table(self::$inspectionhistory_table . ' as hist')
                    ->where('hist.auditinspectionid', $auditinspectionid)
                    ->exists();

                // Update the existing record if auditslipid exists
                if ($auditinspectionidExists) {
                    $updateCount = DB::table(self::$inspectionhistory_table)
                        ->where('auditinspectionid', $auditinspectionid)
                        ->update(['transstatus' => 'I']);

                    $isUpdated = $updateCount > 0;
                }
            }

            // Insert the new history transaction record
            $historytransdel = DB::table(self::$inspectionhistory_table)
                ->insertGetId($historytrans, 'transhistoryid');

            if ($historytransdel) {
                $isInserted = true;
            }

            if ($isUpdated || $isInserted) {
                DB::commit(); // Commit only on success
                return true;
            } else {
                throw new \Exception("Neither update nor insert was successful.");
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack(); // Rollback on SQL error
            \Log::error('SQL Error in insert_inspecthistorydetails: ' . $e->getMessage());
            throw new \Exception('A database error occurred while updating inspection history. Please contact the administrator.', 500);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on any exception
            throw new \Exception($e->getMessage(), 409);
        }
    }


    public static function completeinspect($data, $auditinspectionid)
    {
        if ($auditinspectionid) {

            $updated = DB::table(self::$transauditinspection_table)->where('auditinspectionid', $auditinspectionid)->update($data);

            if ($updated === 0) {

                throw new \Exception('Failed to update the Inspection Check Points');
            }
            $inspectnewDet = DB::table(self::$transauditinspection_table)->where('auditinspectionid', $auditinspectionid)->first();
            $fetchauditinspectionid = $inspectnewDet->auditinspectionid;
            $updateCount = DB::table(self::$inspectionhistory_table)
                ->where('auditinspectionid', $auditinspectionid)
                ->update(['transstatus' => 'I']);

            $historytransdata = [
                'auditinspectionid'    => $fetchauditinspectionid,
                'auditscheduleid'      =>  $inspectnewDet->auditscheduleid,
                'auditplanid'          => $inspectnewDet->auditplanid,
                'remarks'              => $data['remarks'],
                'inspectcheckpoints'   => $inspectnewDet->inspectcheckpoints,
                'statusflag'           => 'F',
                'forwardedto'          => $data['forwardedto'],
                'forwardedby'          => $data['forwardedby'],
                'transstatus'          => 'A',
                'forwardedon'          => View::shared('get_nowtime'),
                'inscheckpointsanswer' => $jsonActions ?? null,
                'inscheckpointremarks' => $jsonRemarks ?? null,
                'processcode'          => 'C'
            ];
            $historytransdel = DB::table(self::$inspectionhistory_table)->insertGetId($historytransdata, 'transhistoryid');
            return $fetchauditinspectionid;
        }
    }

    public static function getmaxtranactionno()
    {
        $latestcode =   DB::table(self::$transauditinspection_table . ' as inspect')
            ->whereNotNull('transactionno') // Ensure we're considering only non-null values
            ->max(DB::raw('CAST(transactionno AS INTEGER)'));

        $newcode = $latestcode !== null ? str_pad($latestcode + 1, 2, '0', STR_PAD_LEFT) : '01';

        return $newcode;
    }

    public static function checkisteamhead($auditscheduleid)
    {
        $session = session('user');
        $session_userId = $session->userid;
        return DB::table('audit.inst_schteammember')
            ->where('auditteamhead', 'Y')
            ->where('statusflag', 'Y')
            ->where('userid', $session_userId)
            ->where('auditscheduleid', $auditscheduleid)
            ->exists();
    }
    public static function getdirectordesig($deptcode)
    {
        $dirdesigcode = DB::table(self::$rolemapping_table . ' as rm')
            ->join(self::$roletypemapping_table . ' as rt', 'rt.roletypemappingcode', '=', 'rm.roletypemappingcode')
            ->join(self::$roletype_table . ' as mr', 'mr.roletypecode', '=', 'rt.roletypecode')
            ->join(self::$roleaction_table . ' as ro', 'ro.roleactioncode', '=', 'rm.roleactioncode')
            ->join(self::$chargedetail_table . ' as cd', 'cd.rolemappingid', '=', 'rm.rolemappingid')
            ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'cd.desigcode')
            ->where('rm.roleactioncode', View::shared('Dirroleactioncode'))
            ->where('rt.deptcode', $deptcode)
            ->value('desig.desigcode');

        return $dirdesigcode;
    }

  public static function teammember_check()
    {
        $session = session('user');
        $userid  = $session->userid;

        $team = DB::table('audit.inst_schteammember as mem')
            ->Join(self::$instauditschedule_table . ' as schd', 'schd.auditscheduleid', '=', 'mem.auditscheduleid')
            ->Join(self::$auditplan_table . ' as plan', 'plan.auditplanid', '=', 'schd.auditplanid')
            ->Join(self::$inst_table . ' as inst', 'plan.instid', '=', 'inst.instid')
            ->Join(self::$department_table . ' as dept', 'dept.deptcode', '=', 'inst.deptcode')
            ->Join(self::$UserDetails_Table . ' as details', 'details.deptuserid', '=', 'mem.userid')
            ->Join(self::$userchargedetail_table . ' as uc', 'details.deptuserid', '=', 'uc.userid')
            ->Join(self::$chargedetail_table . ' as ch', 'ch.chargeid', '=', 'uc.chargeid')
            ->Join(self::$rolemapping_table . ' as ro', 'ro.rolemappingid', '=', 'ch.rolemappingid')
            ->Join(self::$designation_table . ' as desig', 'details.desigcode', '=', 'desig.desigcode')
            ->whereColumn('plan.planmappingid', 'dept.planmappingid')
            ->where('ro.roleactioncode', View::shared('auditor_roleactioncode'))
            ->where('uc.statusflag', 'Y')
            ->where('mem.statusflag', 'Y')
            ->where('details.deptuserid', $userid)
             ->select('mem.auditteamhead')
            ->get();


        $isteam = $team->contains(fn($item) => $item->auditteamhead === 'Y');

        if ($team && $isteam) {

            $access = 'team_head';
        } else if ($team) {
            $access  = 'team_mem';
        } else {
            $access = '';
        }
        return $access;
    }
}
