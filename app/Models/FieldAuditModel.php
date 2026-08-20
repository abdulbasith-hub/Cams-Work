<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class FieldAuditModel extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'audit.trans_auditslip';
    protected $primaryKey = 'auditslipid';
    public $incrementing = true;

    const CREATED_AT = 'createdon';
    const UPDATED_AT = 'updatedon';

    protected $fillable = [
        'auditscheduleid',
        'schteammemberid',
        'auditplanid',
        'mainobjectionid',
        'subobjectionid',
        'amtinvolved',
        'tempslipnumber',
        'tempslipnumber',
        'slipdetails',
        'remarks',
        'severitycode',
        'liability',
        'statusflag',
        'createdon',
        'updatedon',
        'createdby',
        'updatedby',
        'processcode',
        'rejoinderstatus',
        'rejoindercycle',
        'forwardedto',
        'forwardedtousertypecode',
        'updatedbyusertypecode',
        'mainslipnumber',
        'schemastatus',
        'auditeeschemecode',
        'irregularitiescode',
        'irregularitiescatcode',
        'irregularitiessubcatcode',
        'quartercode',
        'financialyear'
    ];

    public static function getscheduledel_basedonuser($sessionuserid, $acheduleid)
    {
        try {
            // Check if session user ID and audit schedule ID are provided
            if ($sessionuserid === null) {
                throw new \Exception('User ID not found');
            }

            if ($acheduleid === null) {
                throw new \Exception('Audit schedule ID not found');
            }

            // Query to fetch the data
            $inst_del = DB::table('audit.inst_schteammember as sm')
                ->join('audit.inst_auditschedule as sis', 'sis.auditscheduleid', '=', 'sm.auditscheduleid')
                ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'sis.auditplanid')
                ->join('audit.mst_institution as sin', 'sin.instid', '=', 'ap.instid')
                ->join('audit.mst_auditeeins_category as incat', 'incat.catcode', '=', 'sin.catcode')
                ->join('audit.mst_typeofaudit as ta', 'ta.typeofauditcode', '=', 'ap.typeofauditcode')
                ->join('audit.yearcode_mapping as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
                ->join(DB::raw('audit.mst_auditperiod d'), DB::raw('CAST(yrmap.yearselected AS INTEGER)'), '=', DB::raw('d.auditperiodid'))
                ->where('sm.userid', $sessionuserid)
                ->where('sis.auditscheduleid', $acheduleid)
                ->where('yrmap.statusflag', 'Y')
                ->select(
                    'sis.auditscheduleid',
                    'sm.auditscheduleid',
                    'sm.auditteamhead',
                    'sis.auditplanid',
                    'sis.fromdate',
                    'sis.exitmeetdate',
                    'sin.catcode',
                    'sin.subcatid',
                    'sis.todate',
                    'ap.instid',
                    'sin.instename',
                    'incat.catename',
                    'ap.mandays',
                    'sin.annadhanam_only',
                    'ap.financialyearcode',
                    'sin.deptcode',
                    'sm.auditteamhead',
                    'ta.typeofauditename',
                    'sm.schteammemberid',
                    'sis.proposedexitmeetdate',
                    DB::raw("STRING_AGG(DISTINCT d.fromyear || '-' || d.toyear, ', ') FILTER (WHERE d.financestatus = 'N') as yearname"),
                    DB::raw("STRING_AGG(DISTINCT d.fromyear || '-' || d.toyear, ', ') FILTER (WHERE d.financestatus = 'Y') as annadhanamyear"),
                    DB::raw("(
                    SELECT (date + INTERVAL '1 day')::date
                    FROM (
                        SELECT date::date
                        FROM generate_series(
                            COALESCE(sis.exitmeetdate, sis.proposedexitmeetdate) + INTERVAL '1 day',
                            COALESCE(sis.exitmeetdate, sis.proposedexitmeetdate) + INTERVAL '20 day',
                            INTERVAL '1 day'
                        ) AS date
                        WHERE EXTRACT(DOW FROM date) NOT IN (0, 6)
                        AND date::date NOT IN (
                            SELECT holiday_date
                            FROM audit.mst_holiday
                        )
                    ) AS working_dates
                    OFFSET 2 LIMIT 1
                ) AS todate_afterworking3days")
                )
                ->groupBy(
                    'sis.auditscheduleid',
                    'sm.auditscheduleid',
                    'sm.auditteamhead',
                    'sis.auditplanid',
                    'sis.fromdate',
                    'sis.todate',
                    'sis.exitmeetdate',
                    'ap.instid',
                    'sin.instename',
                    'ap.financialyearcode',
                    'incat.catename',
                    'ap.mandays',
                    'sin.annadhanam_only',
                    'sin.deptcode',
                    'sm.auditteamhead',
                    'ta.typeofauditename',
                    'sin.catcode',
                    'sin.subcatid',
                    'sm.schteammemberid',
                    'sis.proposedexitmeetdate',
                )
                ->get();

            // If no data is found
            if ($inst_del->isEmpty()) {
                throw new \Exception('No records found for the given user and audit schedule.');
            }

            return $inst_del;
        } catch (\Exception $e) {
            // Log the exception message for debugging
            // Log::error("Error in getscheduledel_basedonuser: " . $e->getMessage());
            // Re-throw the exception to propagate it to the controller
            throw new \Exception($e->getMessage());
        }
    }

    public static function getAuditScheduleHeaddel($auditscheduleid)
    {
        try {
            if ($auditscheduleid === null) {
                throw new \Exception('Audit schedule ID not found');
            }

            $teamheaddel = DB::table('audit.inst_schteammember as sm')
                ->where('auditscheduleid', $auditscheduleid)
                ->where('auditteamhead', 'Y')
                ->where('sm.statusflag', 'Y')
                ->select('sm.userid')
                ->get();

            return $teamheaddel;
        } catch (\Exception $e) {
            // Log the exception message for debugging
            // Log::error("Error in getscheduledel_basedonuser: " . $e->getMessage());
            // Re-throw the exception to propagate it to the controller
            throw new \Exception($e->getMessage());
        }
    }

    public static function getMainobjection($userid, $auditscheduleid, $financialyearcode, $api)
    {
        try {
            if ($api == 'Y') {
                $query1 = DB::table('audit.mst_mainobjection as mo')
                    ->select('mo.objectionename', 'mo.objectiontname', 'mo.mainobjectionid')
                    ->distinct()
                    ->orderBy('mo.objectionename', 'asc');
                // ->where('wa.statusflag', 'Y')
                // ->get();
            } else {
                if ($financialyearcode == '01') {
                    $query1 = DB::table('audit.trans_workallocation as wa')
                        // ->join('audit.map_workallocation as tw', 'wa.workallocationtypeid', '=', 'tw.workallocationtypeid')
                        ->join('audit.map_allocation_objection_old as mao', 'mao.mapallocationobjectionid', '=', 'wa.workallocationtypeid')
                        // ->join('audit.map_workallocation as tw', 'wa.workallocationtypeid', '=', 'tw.workallocationtypeid')
                        ->join('audit.mst_mainobjection as mo', 'mo.mainobjectionid', '=', 'mao.mainobjectionid')
                        ->join('audit.inst_schteammember as itm', 'itm.schteammemberid', '=', 'wa.schteammemberid')
                        ->where('itm.auditscheduleid', $auditscheduleid)
                        ->where('itm.userid', $userid)
                        ->where('mo.statusflag', '=', 'Y')
                        ->select('mo.objectionename', 'mo.objectiontname', 'mo.mainobjectionid')
                        ->distinct()
                        ->orderBy('mo.objectionename', 'asc');
                } else {
                    $query1 = DB::table('audit.trans_workallocation as wa')
                        // ->join('audit.map_workallocation as tw', 'wa.workallocationtypeid', '=', 'tw.workallocationtypeid')
                        ->join('audit.map_allocation_objection as mao', 'mao.mapallocationobjectionid', '=', 'wa.workallocationtypeid')
                        // ->join('audit.map_workallocation as tw', 'wa.workallocationtypeid', '=', 'tw.workallocationtypeid')
                        ->join('audit.mst_mainobjection as mo', 'mo.mainobjectionid', '=', 'mao.mainobjectionid')
                        ->join('audit.inst_schteammember as itm', 'itm.schteammemberid', '=', 'wa.schteammemberid')
                        ->where('itm.auditscheduleid', $auditscheduleid)
                        ->where('itm.userid', $userid)
                        ->where('mo.statusflag', '=', 'Y')
                        ->where('wa.statusflag', '=', 'Y')
                        ->select('mo.objectionename', 'mo.objectiontname', 'mo.mainobjectionid')
                        ->distinct()
                        ->orderBy('mo.objectionename', 'asc');
                }
            }
            return $query1->get();
            // print_r($query1);

            // Returns an array of user IDs
        } catch (\Exception $e) {
            // Throw a custom exception with the message from the model
            throw new \Exception($e->getMessage());
        }
    }

    public static function getSchemename()
    {
        try {
            $chargeData = session('charge');
            $session_deptcode = $chargeData->deptcode;
            return DB::table('audit.auditeescheme as s')
                ->join('audit.mst_dept as dept', 's.deptcode', '=', 'dept.deptcode')
                ->join('audit.mst_auditeeins_category as cat', 's.catcode', '=', 'cat.catcode')
                ->join('audit.mst_auditeeins_subcategory as sub', 's.auditeeins_subcategoryid', '=', 'sub.auditeeins_subcategoryid')
                ->where('s.statusflag', '=', 'Y')
                ->where('s.deptcode', '=', $session_deptcode)
                ->select('s.auditeeschemeelname', 's.auditeeschemecode', 's.auditeeschemetlname', 's.auditeeschemetsname', 's.auditeeschemeesname', 's.auditeeschemeid')
                ->orderBy('s.auditeeschemeelname', 'asc')
                ->get();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getSerious()
    {
        try {
            return DB::table('audit.mst_irregularities as s')
                ->where('s.statusflag', '=', 'Y')
                ->select('s.irregularitieselname', 's.irregularitiesesname', 's.irregularitiescode', 's.irregularitiesid', 's.irregularitiestlname', 's.irregularitiestsname')
                ->orderBy('s.irregularitiesid', 'asc')
                ->get();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getsubcategoryBasedCatgory($category)
    {
        return DB::table('audit.mst_irregularitiescategory as cat')
            ->join('audit.mst_irregularitiessubcategory as subcat', 'subcat.irregularitiescatcode', '=', 'cat.irregularitiescatcode')
            ->select('subcat.irregularitiessubcatcode', 'subcat.irregularitiessubcatelname', 'subcat.irregularitiessubcattlname', 'subcat.irregularitiessubcatesname', 'subcat.irregularitiessubcattsname')
            // ->distinct()
            ->where('subcat.irregularitiescatcode', $category)
            ->where('subcat.statusflag', 'Y')
            ->orderBy('subcat.irregularitiessubcatelname', 'Asc')
            ->get();
    }

    public static function getcategoryBasedSerious($serious)
    {
        return DB::table('audit.mst_irregularities as irr')
            ->join('audit.mst_irregularitiescategory as cat', 'cat.irregularitiescode', '=', 'irr.irregularitiescode')
            ->select('cat.irregularitiescatcode', 'cat.irregularitiescatesname', 'cat.irregularitiescatelname', 'cat.irregularitiescattsname', 'cat.irregularitiescattlname')
            ->distinct()
            ->where('cat.irregularitiescode', $serious)
            ->where('cat.statusflag', 'Y')
            ->orderBy('cat.irregularitiescatelname', 'Asc')
            ->get();
    }

    public static function getSeverity()
    {
        try {
            return DB::table('audit.mst_severity as s')
                ->where('s.statusflag', '=', 'Y')
                ->select('s.severitycode', 's.severityelname', 's.severitytlname')
                ->orderBy('s.orderid', 'asc')
                ->get();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // public static function getsubobjection($majorobjectionid)
    // {
    //     return DB::table('audit.mst_subobjection')
    //         ->where('mainobjectionid', $majorobjectionid)
    //         ->where('statusflag', 'Y')
    //         ->select('subobjectionename', 'subobjectiontname', 'subobjectionid')
    //         ->orderBy('subobjectionename', 'asc')
    //         ->get();
    // }

    // public static function getsubobjection($majorobjectionid,$catcode ,$subcatid)
    // {
    //     $results = DB::table('audit.mst_subobjection as su')
    //         ->distinct()
    //         ->select('subobjectionename', 'subobjectiontname', 'su.subobjectionid')
    //         ->join('audit.map_allocation_objection as mao', 'su.subobjectionid', '=', 'mao.subobjectionid')
    //         ->where('su.mainobjectionid',  $majorobjectionid)
    //         ->where('mao.catcode', $catcode)
    //          ->where('su.statusflag', 'Y');
    //         if($subcatid)
    //         {
    //              $results->where('mao.auditeeins_subcategoryid', $subcatid);
    //         }

    //         $results->orderBy('subobjectionename', 'asc');
    //         $results ->get();

    //     return $results;
    // }

    public static function getsubobjection($majorobjectionid, $catcode, $subcatid, $financialyearcode, $annadhanam_only, $datafromapi)
    {
        if ($datafromapi == 'Y') {
            $results = DB::table('audit.mst_subobjection')
                ->where('mainobjectionid', $majorobjectionid)
                ->where('statusflag', 'Y')
                ->select('subobjectionename', 'subobjectiontname', 'subobjectionid')
                ->orderBy('subobjectionename', 'asc');
            // ->get();
        } else {
            if ($financialyearcode == '01') {
                $results = DB::table('audit.mst_subobjection as su')
                    ->select('su.subobjectionid', 'su.subobjectionename')
                    ->leftJoin('audit.map_allocation_objection_old as mao', 'su.subobjectionid', '=', 'mao.subobjectionid')
                    ->where(function ($query) use ($majorobjectionid) {
                        $query
                            ->where('su.mainobjectionid', $majorobjectionid)
                            ->orWhere('mao.mainobjectionid', $majorobjectionid);
                    })
                    ->where('su.statusflag', 'Y')
                    ->distinct();
            } else {
                $results = DB::table('audit.mst_subobjection as su')
                    ->distinct()
                    ->select('subobjectionename', 'subobjectiontname', 'su.subobjectionid')
                    ->leftjoin('audit.map_allocation_objection as mao', 'su.subobjectionid', '=', 'mao.subobjectionid')
                    // ->where('mao.mainobjectionid', $majorobjectionid)
                    ->where(function ($query) use ($majorobjectionid) {
                        $query
                            ->where('su.mainobjectionid', $majorobjectionid)
                            ->orWhere('mao.mainobjectionid', $majorobjectionid);
                    })
                    // ->where('mao.catcode', $catcode)
                    ->where('su.statusflag', 'Y');

                if ($annadhanam_only == 'Y') {
                    // Allow both categories
                    $results->whereIn('mao.catcode', [$catcode, '08']);
                } else {
                    $results->where('mao.catcode', $catcode);
                }
                if ($subcatid) {
                    $results = $results->where('mao.auditeeins_subcategoryid', $subcatid);
                }

                $results = $results->orderBy('subobjectionename', 'asc');
            }
        }
        $results = $results->get();
        return $results;
    }

    // public static function getslipdetails($userid, $auditslipid = null, $auditteamhead = null, $auditscheduleid, $filter, $action, $teamhead)
    // {
    //     $statusFlag = 'Y';
    //     $data = collect();

    //     if (($action == 'fetch') || ($action == 'fetchwithdata')) {
    //         if ((($filter === 'A') || ($filter === 'P') || ($filter === 'C'))) {
    //             if ($teamhead == 'Y') {
    //                 $data = DB::table('audit.trans_auditslip as t1')
    //                     ->select([
    //                         't1.auditslipid',
    //                         DB::raw("CASE WHEN t1.createdby::int != {$userid} THEN 'M' ELSE 'H' END AS slipby"),
    //                         't1.processcode',
    //                         't1.tempslipnumber'
    //                     ])
    //                     ->where('t1.statusflag', 'Y')
    //                     ->where('t1.auditscheduleid', $auditscheduleid)
    //                     ->whereNotExists(function ($query) use ($userid) {
    //                         $query
    //                             ->select(DB::raw(1))
    //                             ->from('audit.trans_auditslip as t2')
    //                             ->whereRaw('t2.auditslipid = t1.auditslipid')
    //                             ->where('t2.createdby', '!=', (string) $userid)
    //                             ->where('t2.processcode', 'E');
    //                     });
    //             } else if ($teamhead == 'N') {
    //                 $data = DB::table('audit.trans_auditslip as t1')
    //                     ->select([
    //                         't1.auditslipid',
    //                         DB::raw("CASE WHEN t1.createdby::int != {$userid} THEN 'M' ELSE 'H' END AS slipby"),
    //                         't1.processcode',
    //                         't1.tempslipnumber'
    //                     ])
    //                     ->where('t1.statusflag', 'Y')
    //                     ->where('t1.auditscheduleid', $auditscheduleid)
    //                     ->where('t1.createdby', $userid);
    //             }
    //             if ($filter == 'C') {
    //                 $data = $data->whereIn('t1.processcode', ['A', 'X']);
    //             } else if ($filter == 'P') {
    //                 $data = $data->where('t1.forwardedto', $userid);
    //                 $data = $data->where('t1.forwardedtousertypecode', 'A');
    //             }
    //             $data = $data->get();
    //         } else {
    //             $data = DB::table('audit.trans_auditslip as t1')
    //                 ->join('audit.sliphistorytransactions as sh', 't1.auditslipid', '=', 'sh.auditslipid')
    //                 ->distinct()
    //                 ->select([
    //                     't1.auditslipid',
    //                     DB::raw("CASE WHEN t1.createdby != {$userid} THEN 'M' ELSE 'H' END AS slipby"),
    //                     't1.processcode',
    //                     't1.tempslipnumber'
    //                 ])
    //                 ->where('t1.statusflag', 'Y')
    //                 ->where('t1.auditscheduleid', $auditscheduleid)
    //                 ->where('sh.forwardedby', $userid)
    //                 ->where('sh.forwardedbyusertypecode', 'A')
    //                 ->wherenotIn('t1.processcode', ['A', 'X'])
    //                 ->orderBy('t1.auditslipid', 'asc')
    //                 ->get();
    //         }
    //         if (($action == 'fetch') && (!($data->isEmpty())) && (!($auditslipid))) {
    //             $auditslipid = $data[0]->auditslipid;
    //         }
    //     }

    //     if (($auditslipid)) {
    //         $historydel = DB::table('audit.trans_auditslip as tas')
    //             ->selectRaw("
    //             tas.auditslipid,
    //             tas.rejoinderstatus,
    //             tas.rejoindercycle,
    //             STRING_AGG(
    //                 DISTINCT
    //                 CASE
    //                     WHEN t3.statusflag IN ('Y')
    //                         AND t2.usertypecode = 'A'
    //                         AND (
    //                             (
    //                                 tas.processcode = 'R'
    //                                 AND   EXISTS (
    //                                     SELECT 1
    //                                     FROM audit.slipfileupload tas_r
    //                                     WHERE tas_r.fileuploadid = t2.fileuploadid
    //                                     AND tas_r.processcode  =  tas.processcode
    //                                 )And
    //                                 (
    //                                     (tas.rejoinderstatus = t3.rejoinderstatus OR (tas.rejoinderstatus IS NULL AND t3.rejoinderstatus IS NULL))
    //                                     AND
    //                                     (tas.rejoindercycle = t3.rejoindercycle OR (tas.rejoindercycle IS NULL AND t3.rejoindercycle IS NULL))
    //                                 )

    //                             )
    //                             OR (
    //                                 tas.processcode IN ('E')
    //                                 AND EXISTS (
    //                                     SELECT 1
    //                                     FROM audit.slipfileupload tas_r
    //                                     WHERE tas_r.fileuploadid = t2.fileuploadid
    //                                     AND tas_r.processcode  =  tas.processcode
    //                                 )

    //                             )
    //                                 OR (
    //                                 tas.processcode IN ('T')
    //                                 AND EXISTS (
    //                                     SELECT 1
    //                                     FROM audit.slipfileupload tas_r
    //                                     WHERE tas_r.fileuploadid = t2.fileuploadid
    //                                     AND tas_r.processcode  IN ('E','T')
    //                                 )

    //                             )
    //                         )
    //                     THEN
    //                         CONCAT(
    //                             COALESCE(TRIM(t2.filename), ''), '-',
    //                             COALESCE(TRIM(t2.filepath), ''), '-',
    //                             COALESCE(t2.filesize::TEXT, ''), '-',
    //                             COALESCE(t2.fileuploadid::TEXT, '')
    //                         )
    //                     ELSE NULL
    //                 END,
    //                 ','
    //             ) AS auditorfileupload
    //             ,
    //             STRING_AGG(
    //                 DISTINCT CASE
    //                     WHEN l.statusflag IN ('Y', 'C')
    //                     THEN CONCAT(
    //                         COALESCE(l.notype, ''), '-',
    //                         COALESCE(l.liabilitygpfno, ''), '-',
    //                         COALESCE(l.liabilityname, ''), '-',
    //                         COALESCE(l.liabilitydesignation::TEXT, ''), '-',
    //                         COALESCE(l.liabilityamount::TEXT, ''), '-',
    //                         COALESCE(l.liabilityid::TEXT, ''), '-',
    //                         COALESCE(l.statusflag::TEXT, '')
    //                     )
    //                 ELSE NULL
    //                 END,
    //                 ','
    //             ) AS liabilitydel,

    //             tas.tempslipnumber,
    //             tas.mainslipnumber,
    //             tas.subobjectionid,
    //             tas.mainobjectionid,
    //             tas.amtinvolved,
    //             tas.slipdetails,
    //             s.subobjectionename,
    //             CASE
    //                 WHEN (tas.updatedbyusertypecode = 'A' or tas.processcode = 'E') THEN COALESCE(tas.remarks::json->>'content', '')
    //                 ELSE ''
    //             END AS remarks,
    //             tas.severitycode,
    //             subcat.irregularitiessubcatcode,
    //             cat.irregularitiescatcode,
    //             tas.schemastatus,
    //             scheme.auditeeschemecode,
    //             ir.irregularitiescode,
    //             tas.processcode,
    //             tas.liability,
    //             p.processelname,
    //             cb.username AS createdbyusername,
    //             tas.createdby,
    //             tas.updatedon,
    //             COALESCE(CAST(tr.remarks::json->>'content' AS TEXT), '') AS historyremarks,
    //             tr.auditslipid AS trans_auditslipid,
    //             tr.transhistoryid,
    //             tr.forwardedby,
    //             tr.forwardedbyusertypecode,
    //             tr.rejoinderstatus AS tr_rejoinderstatus,
    //             tr.rejoindercycle AS tr_rejoindercycle,
    //             tr.processcode AS tr_processcode,
    //             tr.forwardedto,
    //             tr.forwardedon,

    //             CASE
    //                 WHEN tr.forwardedbyusertypecode = 'A' THEN dp.username
    //                 WHEN tr.forwardedbyusertypecode = 'I' THEN au.username
    //                 ELSE ''
    //             END AS username,

    //            COALESCE((
    //                 SELECT STRING_AGG(
    //                     DISTINCT CONCAT(
    //                         COALESCE(fu.filename, ''), '-',
    //                         COALESCE(fu.filepath, ''), '-',
    //                         COALESCE(fu.filesize::TEXT, ''), '-',
    //                         COALESCE(fu.fileuploadid::TEXT, '')
    //                     ), ','
    //                 )
    //                 FROM audit.slipfileupload sf
    //                 LEFT JOIN audit.fileuploaddetail fu ON fu.fileuploadid = sf.fileuploadid
    //                 WHERE sf.auditslipid = tr.auditslipid
    //                 AND (
    //                     ((
    //                         (tr.processcode = 'F' AND (sf.processcode = 'F' OR sf.processcode = 'T') AND ((sf.statusflag = 'Y' AND fu.statusflag = 'Y') ))
    //                         OR (tr.processcode IN ('A', 'X') AND (sf.processcode = 'A'  OR sf.processcode = 'X' OR sf.processcode = 'R') AND (sf.statusflag = 'Y') and fu.usertypecode = 'A')
    //                         OR (tr.processcode IN ('T', 'R', 'M') AND (sf.processcode = tr.processcode)

    //                         )
    //                     )
    //                     AND (
    //                         (tr.rejoinderstatus IS NULL AND tr.rejoindercycle IS NULL AND sf.rejoinderstatus IS NULL AND sf.rejoindercycle IS NULL)
    //                         OR (tr.rejoinderstatus IS NOT NULL AND sf.rejoinderstatus = tr.rejoinderstatus)
    //                         OR (tr.rejoindercycle IS NOT NULL AND sf.rejoindercycle = tr.rejoindercycle)
    //                     ))
    //                     or (tr.processcode = 'F' AND (tr.rejoinderstatus = 'Y' and tr.rejoindercycle = '1')) And (sf.processcode = 'R' AND sf.statusflag = 'Y' AND fu.statusflag = 'Y' and sf.rejoinderstatus is null and sf.rejoindercycle is null and fu.usertypecode = 'A')

    //                 )
    //             ), '') AS file_details

    //         ")
    //             ->join('audit.mst_irregularitiessubcategory as subcat', 'subcat.irregularitiessubcatcode', '=', 'tas.irregularitiessubcatcode')
    //             ->join('audit.mst_irregularitiescategory as cat', 'cat.irregularitiescatcode', '=', 'tas.irregularitiescatcode')
    //             ->join('audit.mst_irregularities as ir', 'ir.irregularitiescode', '=', 'tas.irregularitiescode')
    //             ->join('audit.mst_process as p', 'p.processcode', '=', 'tas.processcode')
    //             ->leftJoin('audit.auditeescheme as scheme', 'scheme.auditeeschemecode', '=', 'tas.auditeeschemecode')
    //             ->leftJoin('audit.sliphistorytransactions as tr', 'tr.auditslipid', '=', 'tas.auditslipid')
    //             ->leftJoin('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'tr.forwardedby')
    //             ->leftJoin('audit.audtieeuserdetails as au', 'au.auditeeuserid', '=', 'tr.forwardedby')
    //             ->leftJoin('audit.slipfileupload as t3', 't3.auditslipid', '=', 'tas.auditslipid')
    //             ->leftJoin('audit.fileuploaddetail as t2', 't2.fileuploadid', '=', 't3.fileuploadid')
    //             ->leftJoin('audit.liability as l', 'l.auditslipid', '=', 'tas.auditslipid')
    //             ->leftJoin('audit.deptuserdetails as cb', 'cb.deptuserid', '=', 'tas.createdby')
    //             ->leftJoin('audit.mst_subobjection as s', 's.subobjectionid', '=', 'tas.subobjectionid')
    //             ->where('tas.statusflag', 'Y')
    //             ->where('tas.auditslipid', $auditslipid)
    //             ->groupByRaw("
    //                 tas.auditslipid, tas.tempslipnumber, tas.mainslipnumber, tas.subobjectionid,
    //                 tas.mainobjectionid, tas.amtinvolved, tas.slipdetails, s.subobjectionename,
    //                 tas.remarks::json->>'content', tas.severitycode, subcat.irregularitiessubcatcode,
    //                 cat.irregularitiescatcode, tas.schemastatus, scheme.auditeeschemecode,
    //                 ir.irregularitiescode, tas.processcode, tas.liability, p.processelname,
    //                 cb.username, tas.createdby, tas.updatedon, tr.remarks::json->>'content', tr.auditslipid,
    //                 tr.transhistoryid, tr.forwardedby, tr.forwardedbyusertypecode,
    //                 tr.rejoinderstatus, tr.rejoindercycle, tr.processcode, tr.forwardedto,
    //                 tr.forwardedon, dp.username, au.username
    //             ")
    //             ->orderBy('tr.transhistoryid', 'asc')
    //             ->get();

    //         // $querySql = $historydel->toSql();
    //         // $bindings = $historydel->getBindings();

    //         // $finalQuery = vsprintf(
    //         //     str_replace('?', "'%s'", $querySql),
    //         //     array_map('addslashes', $bindings)
    //         // );

    //         // print_r($finalQuery);
    //         // exit;
    //     } else
    //         $historydel = collect();
    //     return collect([
    //         'auditDetails' => $data,
    //         'historydel' => $historydel
    //     ]);
    // }

    public static function getslipdetails($userid, $auditslipid = null, $auditteamhead = null, $auditscheduleid, $filter, $action, $teamhead, $instid, $spilloverflag)
    {
        $statusFlag = 'Y';
        $data = collect();

        if (($action == 'fetch') || ($action == 'fetchwithdata')) {
            if (in_array($filter, ['A', 'P', 'C'])) {
                $baseQuery = DB::table('audit.trans_auditslip as t1')
                    ->select([
                        't1.auditslipid',
                        DB::raw("CASE WHEN t1.createdby::int != {$userid} THEN 'M' ELSE 'H' END AS slipby"),
                        't1.processcode',
                        't1.tempslipnumber',
                        't1.apicode'
                    ])
                    ->where('t1.statusflag', $statusFlag)
                    ->where('t1.auditscheduleid', $auditscheduleid);

                // if ($spilloverflag === 'Y') {
                //     $baseQuery->whereIn('t1.auditscheduleid', function ($query) use ($instid) {
                //         $query
                //             ->select('inst.auditscheduleid')
                //             ->from('audit.auditplan as ap')
                //             ->join('audit.inst_auditschedule as inst', 'inst.auditplanid', '=', 'ap.auditplanid')
                //             ->where('ap.instid', $instid)
                //             ->where(function ($q) {
                //                 $q
                //                     ->where('ap.spilloverflag', 'Y')
                //                     ->orWhere('ap.carryforwardflag', 'Y');
                //             })
                //             ->where('inst.statusflag', 'F');
                //     });
                // } else {
                //     $baseQuery->where('t1.auditscheduleid', $auditscheduleid);
                // }

                if ($teamhead === 'Y') {
                    $baseQuery->whereNotExists(function ($query) use ($userid) {
                        $query
                            ->select(DB::raw(1))
                            ->from('audit.trans_auditslip as t2')
                            ->whereRaw('t2.auditslipid = t1.auditslipid')
                            ->where('t2.createdby', '!=', (string) $userid)
                            ->where('t2.processcode', 'E');
                    });
                } elseif ($teamhead === 'N') {
                    $baseQuery->where('t1.createdby', $userid);
                }

                if ($filter === 'C') {
                    $baseQuery->whereIn('t1.processcode', ['A', 'X']);
                } elseif ($filter === 'P') {
                    $baseQuery
                        ->where(function ($q) use ($userid) {
                            $q
                                ->where('t1.forwardedto', $userid)
                                ->where('t1.forwardedtousertypecode', 'A');
                        })
                        ->orWhere(function ($q) use ($userid) {
                            $q
                                ->where('t1.createdby', $userid)
                                ->where('t1.processcode', 'E');
                        });
                }

                $data = $baseQuery->orderBy('t1.auditslipid', 'asc')->get();
            } else {
                $data = DB::table('audit.trans_auditslip as t1')
                    ->join('audit.sliphistorytransactions as sh', 't1.auditslipid', '=', 'sh.auditslipid')
                    ->distinct()
                    ->select([
                        't1.auditslipid',
                        DB::raw("CASE WHEN t1.createdby != {$userid} THEN 'M' ELSE 'H' END AS slipby"),
                        't1.processcode',
                        't1.tempslipnumber'
                    ])
                    ->where('t1.statusflag', $statusFlag)
                    ->where('t1.auditscheduleid', $auditscheduleid)
                    ->where('sh.forwardedby', $userid)
                    ->where('sh.forwardedbyusertypecode', 'A')
                    ->whereNotIn('t1.processcode', ['A', 'X'])
                    ->orderBy('t1.auditslipid', 'asc')
                    ->get();
            }

            if ($action === 'fetch' && !$data->isEmpty() && !$auditslipid) {
                $auditslipid = $data[0]->auditslipid;
            }
        }

        if ($auditslipid) {
            $historydel = DB::table('audit.trans_auditslip as tas')
                ->selectRaw("
                tas.auditslipid,
                tas.rejoinderstatus,
                tas.rejoindercycle,
                STRING_AGG(
                    DISTINCT
                    CASE
                        WHEN t3.statusflag = 'Y' AND t2.usertypecode = 'A' AND (
                            (
                                tas.processcode = 'R' AND EXISTS (
                                    SELECT 1 FROM audit.slipfileupload tas_r
                                    WHERE tas_r.fileuploadid = t2.fileuploadid
                                    AND tas_r.processcode = tas.processcode
                                )
                                AND (
                                    (tas.rejoinderstatus = t3.rejoinderstatus OR (tas.rejoinderstatus IS NULL AND t3.rejoinderstatus IS NULL))
                                    AND (tas.rejoindercycle = t3.rejoindercycle OR (tas.rejoindercycle IS NULL AND t3.rejoindercycle IS NULL))
                                )
                            )
                            OR (
                                tas.processcode = 'E' AND EXISTS (
                                    SELECT 1 FROM audit.slipfileupload tas_r
                                    WHERE tas_r.fileuploadid = t2.fileuploadid
                                    AND tas_r.processcode = tas.processcode
                                )
                            )
                            OR (
                                tas.processcode = 'T' AND EXISTS (
                                    SELECT 1 FROM audit.slipfileupload tas_r
                                    WHERE tas_r.fileuploadid = t2.fileuploadid
                                    AND tas_r.processcode IN ('E', 'T')
                                )
                            )
                        )
                        THEN CONCAT(
                            COALESCE(TRIM(t2.filename), ''), '-',
                            COALESCE(TRIM(t2.filepath), ''), '-',
                            COALESCE(t2.filesize::TEXT, ''), '-',
                            COALESCE(t2.fileuploadid::TEXT, '')
                        )
                        ELSE NULL
                    END,
                    ','
                ) AS auditorfileupload,
                STRING_AGG(
                    DISTINCT CASE
                        WHEN l.statusflag IN ('Y', 'C')
                        THEN CONCAT(
                            COALESCE(l.notype, ''), '-',
                            COALESCE(l.liabilitygpfno, ''), '-',
                            COALESCE(l.liabilityname, ''), '-',
                            COALESCE(l.liabilitydesignation::TEXT, ''), '-',
                            COALESCE(l.liabilityamount::TEXT, ''), '-',
                            COALESCE(l.liabilityid::TEXT, ''), '-',
                            COALESCE(l.statusflag::TEXT, '')
                        )
                        ELSE NULL
                    END,
                    ','
                ) AS liabilitydel,
                tas.tempslipnumber,
                tas.mainslipnumber,
                tas.subobjectionid,
                tas.mainobjectionid,
                tas.amtinvolved,
                tas.slipdetails,
                s.subobjectionename,
                CASE
                    WHEN (tas.updatedbyusertypecode = 'A' or tas.processcode = 'E')
                    THEN COALESCE(tas.remarks::json->>'content', '')
                    ELSE ''
                END AS remarks,
                tas.severitycode,
                subcat.irregularitiessubcatcode,
                cat.irregularitiescatcode,
                tas.schemastatus,
                scheme.auditeeschemecode,
                ir.irregularitiescode,
                tas.processcode,
                tas.liability,
                tas.financialyear,
                p.processelname,
                cb.username AS createdbyusername,
                tas.createdby,
                tas.updatedon,
                COALESCE(CAST(tr.remarks::json->>'content' AS TEXT), '') AS historyremarks,
                tr.auditslipid AS trans_auditslipid,
                tr.transhistoryid,
                tr.forwardedby,
                tr.forwardedbyusertypecode,
                tr.rejoinderstatus AS tr_rejoinderstatus,
                tr.rejoindercycle AS tr_rejoindercycle,
                tr.processcode AS tr_processcode,
                tr.forwardedto,
                tr.forwardedon,
                CASE
                    WHEN tr.forwardedbyusertypecode = 'A' THEN dp.username
                    WHEN tr.forwardedbyusertypecode = 'I' THEN au.username
                    ELSE ''
                END AS username,
                COALESCE((
                    SELECT STRING_AGG(
                        DISTINCT CONCAT(
                            COALESCE(fu.filename, ''), '-',
                            COALESCE(fu.filepath, ''), '-',
                            COALESCE(fu.filesize::TEXT, ''), '-',
                            COALESCE(fu.fileuploadid::TEXT, '')
                        ), ','
                    )
                    FROM audit.slipfileupload sf
                    LEFT JOIN audit.fileuploaddetail fu ON fu.fileuploadid = sf.fileuploadid
                    WHERE sf.auditslipid = tr.auditslipid
                    AND (
                        (
                            (
                                tr.processcode = 'F' AND sf.processcode IN ('F', 'T') AND sf.statusflag = 'Y' AND fu.statusflag = 'Y'
                            ) OR (
                                tr.processcode IN ('A', 'X') AND sf.processcode IN ('A', 'X', 'R') AND sf.statusflag = 'Y' AND fu.usertypecode = 'A'
                            ) OR (
                                tr.processcode IN ('T', 'R', 'M') AND sf.processcode = tr.processcode
                            )
                        )
                        AND (
                            (tr.rejoinderstatus IS NULL AND tr.rejoindercycle IS NULL AND sf.rejoinderstatus IS NULL AND sf.rejoindercycle IS NULL)
                            OR (tr.rejoinderstatus IS NOT NULL AND sf.rejoinderstatus = tr.rejoinderstatus)
                            OR (tr.rejoindercycle IS NOT NULL AND sf.rejoindercycle = tr.rejoindercycle)
                        )
                        OR (
                            tr.processcode = 'F' AND tr.rejoinderstatus = 'Y' AND tr.rejoindercycle = '1' AND
                            sf.processcode = 'R' AND sf.statusflag = 'Y' AND fu.statusflag = 'Y' AND
                            sf.rejoinderstatus IS NULL AND sf.rejoindercycle IS NULL AND fu.usertypecode = 'A'
                        )
                    )
                ), '') AS file_details
            ")
                ->join('audit.mst_irregularitiessubcategory as subcat', 'subcat.irregularitiessubcatcode', '=', 'tas.irregularitiessubcatcode')
                ->join('audit.mst_irregularitiescategory as cat', 'cat.irregularitiescatcode', '=', 'tas.irregularitiescatcode')
                ->join('audit.mst_irregularities as ir', 'ir.irregularitiescode', '=', 'tas.irregularitiescode')
                ->join('audit.mst_process as p', 'p.processcode', '=', 'tas.processcode')
                ->leftJoin('audit.auditeescheme as scheme', 'scheme.auditeeschemecode', '=', 'tas.auditeeschemecode')
                ->leftJoin('audit.sliphistorytransactions as tr', 'tr.auditslipid', '=', 'tas.auditslipid')
                ->leftJoin('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'tr.forwardedby')
                ->leftJoin('audit.audtieeuserdetails as au', 'au.auditeeuserid', '=', 'tr.forwardedby')
                ->leftJoin('audit.slipfileupload as t3', 't3.auditslipid', '=', 'tas.auditslipid')
                ->leftJoin('audit.fileuploaddetail as t2', 't2.fileuploadid', '=', 't3.fileuploadid')
                ->leftJoin('audit.liability as l', 'l.auditslipid', '=', 'tas.auditslipid')
                ->leftJoin('audit.deptuserdetails as cb', 'cb.deptuserid', '=', 'tas.createdby')
                ->leftJoin('audit.mst_subobjection as s', 's.subobjectionid', '=', 'tas.subobjectionid')
                ->where('tas.statusflag', $statusFlag)
                ->where('tas.auditslipid', $auditslipid)
                ->groupByRaw("
                tas.auditslipid, tas.tempslipnumber, tas.mainslipnumber, tas.subobjectionid,
                tas.mainobjectionid, tas.amtinvolved, tas.slipdetails, s.subobjectionename,
                tas.remarks::json->>'content', tas.severitycode, subcat.irregularitiessubcatcode,
                cat.irregularitiescatcode, tas.schemastatus, scheme.auditeeschemecode,
                ir.irregularitiescode, tas.processcode, tas.liability, p.processelname,
                cb.username, tas.createdby, tas.updatedon, tr.remarks::json->>'content', tr.auditslipid,
                tr.transhistoryid, tr.forwardedby, tr.forwardedbyusertypecode,
                tr.rejoinderstatus, tr.rejoindercycle, tr.processcode, tr.forwardedto,
                tr.forwardedon, dp.username, au.username
            ")
                ->orderBy('tr.transhistoryid', 'asc')
                ->get();
        } else {
            $historydel = collect();
        }

        return collect([
            'auditDetails' => $data,
            'historydel' => $historydel
        ]);
    }

    public static function updateslipfileupload($updateprocesscode, $createdby, $processcode, $auditslipid, $usertypecode, $rejoinderstatus, $rejoindercount)
    {
        try {
            $isUpdated = false;
            $isInserted = false;
            $getfileuploadid = DB::table('audit.slipfileupload as t')
                ->join('audit.fileuploaddetail as t2', 't2.fileuploadid', '=', 't.fileuploadid')
                ->where('t.auditslipid', $auditslipid)
                ->where('t2.uploadedby', $createdby)
                ->where('t.processcode', $processcode)
                ->where('t2.usertypecode', $usertypecode);

            if ($rejoinderstatus == 'Y') {
                $getfileuploadid
                    ->where('t.rejoinderstatus', $rejoinderstatus)
                    ->where('t.rejoindercycle', $rejoindercount);
            }

            // Update and get affected rows
            $affectedRows = $getfileuploadid->update(['processcode' => $updateprocesscode]);

            // Check if update was successful
            $isUpdated = $affectedRows > 0;

            return true;

            // Return true only if both operations succeeded
            // if ($isUpdated || $isInserted) {
            //     return true;
            // } else {
            //     throw new \Exception("Neither update nor insert was successful.");
            // }
        } catch (\Exception $e) {
            // Throw a custom exception with the message from the model
            throw new \Exception($e->getMessage());
        }
    }

    public static function createIfNotExistsOrUpdate(array $data, $auditslipid = null, $auditscheduleid = null, $sessiondeptcode, $spilloverflag, $instid)
    {
        // echo $data['quartercode'];
        // echo $spilloverflag;
        // exit;
        try {
            // If an audit slip ID is provided, proceed to update the existing record
            if ($auditslipid) {
                $existingUser = self::find($auditslipid);

                if (!$existingUser) {
                    throw new \Exception('Audit slip not found.');
                }

                $existingUser->update($data);

                return [
                    'auditslipid' => $existingUser->auditslipid,
                    'slipnumber' => $existingUser->tempslipnumber,
                    'createdby' => $existingUser->createdby,
                    'auditscheduleid' => $existingUser->auditscheduleid,
                ];
            } else {
                $deptData = DB::table('audit.auditplan as ap')
                    ->Join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
                    ->Join('audit.mst_dept as du', 'du.deptcode', '=', 'inst.deptcode')
                    ->Join('audit.inst_auditschedule as ia', 'ia.auditplanid', '=', 'ap.auditplanid')
                    ->Join('audit.mst_auditquarter as aq', 'aq.auditquartercode', '=', 'ap.auditquartercode')
                    ->where('ia.auditscheduleid', $auditscheduleid)
                    ->where('du.deptcode', $sessiondeptcode)
                    ->select('du.slipno', 'du.financialyear', 'du.deptesname', 'ap.auditquartercode', 'aq.auditquartersname')
                    ->get();
                // print_r($deptData);

                if (!$deptData) {
                    throw new \Exception('Required department or audit schedule data not found.');
                }

                // // Get the max temp slip number for the given auditscheduleid
                // Get max tempslipnumber for this auditscheduleid + quarter
                $maxId = DB::table('audit.trans_auditslip')
                    ->where('auditscheduleid', $auditscheduleid)
                    ->where('quartercode', $data['quartercode'])
                    ->where('financialyear', '2026')
                    ->max('tempslipnumber');

                if (!$maxId) {
                    $maxId = 0;
                }

                // if (!$maxId && $spilloverflag === 'Y') {
                //     // fallback: max across all schedules of this institution
                //     $maxId = DB::table('audit.trans_auditslip as t1')
                //         ->whereIn('t1.auditscheduleid', function ($query) use ($instid) {
                //             $query
                //                 ->select('inst.auditscheduleid')
                //                 ->from('audit.auditplan as ap')
                //                 ->join('audit.inst_auditschedule as inst', 'inst.auditplanid', '=', 'ap.auditplanid')
                //                 ->where(function ($q) {
                //                     $q
                //                         ->where('ap.spilloverflag', 'Y')
                //                         ->orWhere('ap.carryforwardflag', 'Y');
                //                 })
                //                 ->where('ap.instid', $instid);
                //         })
                //         ->max('t1.tempslipnumber');
                // } elseif (!$maxId) {
                //     $maxId = 0;
                // }

                // Increment slip number, or set to 1 if null
                $slipno = $deptData[0]->slipno ? $deptData[0]->slipno + 1 : 1;

                $lastTwoDigits_financialyear = substr($deptData[0]->financialyear, -2);
                $firstLetter_deptsname = substr($deptData[0]->deptesname, 0, 1);
                $quartercode = $deptData[0]->auditquartersname;
                $slipnoWithPadding = str_pad($slipno, 6, '0', STR_PAD_LEFT);

                // Build the mainslip number
                $mainslipnumber = $lastTwoDigits_financialyear . $firstLetter_deptsname . $quartercode . $slipnoWithPadding;

                // Calculate the next temp slip number
                $code = $maxId + 1;

                // Check if the mainslipnumber already exists
                $mainslipnumberExists = DB::table('audit.trans_auditslip')
                    ->where('mainslipnumber', $mainslipnumber)
                    ->exists();

                if ($mainslipnumberExists) {
                    throw new \Exception('Mainslip number already exists.');
                }

                // Prepare the data for creation
                $data['tempslipnumber'] = $code;
                $data['mainslipnumber'] = $mainslipnumber;

                // Create a new record
                $newRecord = self::create($data);

                // Check if the record was created successfully by verifying the return object
                if (!$newRecord || !$newRecord->auditslipid) {
                    throw new \Exception('Failed to create the new audit slip.');
                }

                // Update the department slip number and check the result
                $updateCount = DB::table('audit.mst_dept')
                    ->where('deptcode', $sessiondeptcode)
                    ->update(['slipno' => $slipno]);

                // Check if the department slip number was successfully updated
                if ($updateCount === 0) {
                    throw new \Exception('Failed to update department slip number.');
                }

                // Return the created data
                return [
                    'auditslipid' => $newRecord->auditslipid,
                    'slipnumber' => $code,
                    'mainslipnumber' => $newRecord->mainslipnumber,
                ];
            }
        } catch (\Exception $e) {
            // Catch and rethrow the exception
            throw new \Exception($e->getMessage());
        }
    }

    public static function fetchdata_auditeeuserid($instid)
    {
        try {
            $query = DB::table('audit.audtieeuserdetails as au')
                ->where('au.instid', $instid);

            // Fetch and return user IDs
            return $query->pluck('au.auditeeuserid');  // Returns an array of user IDs
        } catch (\Exception $e) {
            // Throw a custom exception with the message from the model
            throw new \Exception($e->getMessage());
        }
    }

    public static function insert_historytransactiondel($data, $auditslipid = null)
    {
        try {
            // Ensure required key exists
            if (!isset($data['processcode'])) {
                throw new \Exception("Missing required 'processcode' in input data.");
            }

            DB::beginTransaction();

            $isUpdated = false;
            $isInserted = false;

            // Update existing transaction if auditslipid is provided
            if ($auditslipid !== null) {
                $slipidExists = DB::table('audit.sliphistorytransactions as ts')
                    ->where('ts.auditslipid', $auditslipid)
                    ->exists();

                if ($slipidExists) {
                    $updateCount = DB::table('audit.sliphistorytransactions')
                        ->where('auditslipid', $auditslipid)
                        ->update(['transstatus' => 'I']);

                    $isUpdated = $updateCount > 0;
                }
            }

            // Insert new history transaction
            $historytransdel = DB::table('audit.sliphistorytransactions')->insertGetId($data, 'transhistoryid');

            // Prepare safe rejoinder data
            $rejoinderstatus = isset($data['rejoinderstatus']) ? $data['rejoinderstatus'] : null;
            $rejoindercount = ($rejoinderstatus === 'Y' && isset($data['rejoindercount'])) ? $data['rejoindercount'] : null;
            $rejoindercycle = isset($data['rejoindercycle']) ? $data['rejoindercycle'] : null;

            // Insert related liabilities
            DB::statement('
            INSERT INTO audit.sliphistoryliability
            (liabilityid, auditslipid, notype, liabilityname, liabilitygpfno, liabilitydesignation,
             liabilityamount, statusflag,createdon,createdby, processcode, rejoinderstatus, rejoindercycle, historytransid)
            SELECT liabilityid, auditslipid, notype, liabilityname, liabilitygpfno, liabilitydesignation,
                   liabilityamount, la.statusflag,?, ?,?, ?, ?, ?
            FROM audit.liability la
            WHERE la.auditslipid = ?
        ', [
                View::shared('get_nowtime'),
                $data['forwardedby'],
                $data['processcode'],
                $rejoinderstatus,
                $rejoindercycle,
                $historytransdel,
                $auditslipid
            ]);

            $isInserted = true;

            DB::commit();

            // Return success if either update or insert was successful
            if ($isUpdated || $isInserted) {
                return true;
            } else {
                throw new \Exception('Neither update nor insert was successful.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public static function update_auditsliptable($data, $auditslipid = null)
    {
        try {
            if ($auditslipid === null) {
                throw new \Exception('Auditslip ID is required for updating.');
            }

            // Check if the record exists
            $slipidExists = DB::table('audit.trans_auditslip')
                ->where('auditslipid', $auditslipid)
                ->exists();

            if (!$slipidExists) {
                throw new \Exception("Auditslip ID {$auditslipid} does not exist.");
            }

            // Perform the update
            $updated = DB::table('audit.trans_auditslip')
                ->where('auditslipid', $auditslipid)
                ->update($data);

            // Log the update result
            // \Log::info("Update result: {$updated}");

            if ($updated) {
                return true;  // Update was successful
            } else {
                throw new \Exception('Update executed but no rows were affected. Check the data.');
            }
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    // public static function fetchdata_auditee($userid, $auditslipid = null, $action, $filter)
    // {
    //     $results1 = DB::table('audit.trans_auditslip')
    //         ->select(
    //             'trans_auditslip.auditslipid',
    //             DB::raw(
    //                 "
    //             STRING_AGG(
    //                 DISTINCT CASE
    //                     WHEN t3.statusflag = 'Y' AND t2.usertypecode = 'I' and ( (trans_auditslip.rejoinderstatus  = t3.rejoinderstatus and trans_auditslip.rejoindercycle = t3.rejoindercycle) or ( trans_auditslip.rejoinderstatus is null and trans_auditslip.rejoindercycle is null)  )  THEN
    //                         CONCAT(
    //                             COALESCE(t2.filename, ''), '-',
    //                             COALESCE(t2.filepath, ''), '-',
    //                             COALESCE(t2.filesize::TEXT, ''), '-',
    //                             COALESCE(t2.fileuploadid::TEXT, '')
    //                         )
    //                     ELSE NULL
    //                 END, ','
    //             ) AS auditeefileupload"
    //             ),
    //             DB::raw("
    //         STRING_AGG(
    //             DISTINCT CASE
    //                 WHEN  (l.statusflag = 'Y' ) THEN
    //                     CONCAT(
    //                         COALESCE(l.notype, ''), '-',
    //                         COALESCE(l.liabilitygpfno, ''), '-',
    //                         COALESCE(l.liabilityname, ''), '-',
    //                         COALESCE(l.liabilitydesignation::TEXT, ''), '-',
    //                         COALESCE(l.liabilityamount::TEXT, ''), '-',
    //                         COALESCE(l.liabilityid::TEXT, ''), '-',
    //                         COALESCE(l.statusflag::TEXT, '')

    //                     )
    //                 ELSE NULL
    //             END, ','
    //         ) AS liabilitydel
    //     "),
    //             DB::raw("COALESCE(CAST(trans_auditslip.remarks::json->>'content' AS TEXT), '') AS remarks"),
    //             'trans_auditslip.tempslipnumber',
    //             'trans_auditslip.subobjectionid',
    //             'trans_auditslip.mainobjectionid',
    //             'trans_auditslip.amtinvolved',
    //             'trans_auditslip.slipdetails',
    //             's.subobjectionename',
    //             'subcat.irregularitiessubcatcode',
    //             'subcat.irregularitiessubcatelname',
    //             'cat.irregularitiescatcode',
    //             'cat.irregularitiescatelname',
    //             'trans_auditslip.schemastatus',
    //             'scheme.auditeeschemecode',
    //             'scheme.auditeeschemeelname',
    //             //  //'trans_auditslip.auditeeschemecode',
    //             'ir.irregularitiescode',
    //             'ir.irregularitieselname',
    //             'trans_auditslip.severitycode',
    //             'trans_auditslip.processcode',
    //             'trans_auditslip.liability',
    //             'p.processelname',
    //             'trans_auditslip.rejoinderstatus',
    //             'trans_auditslip.rejoindercycle',
    //             'trans_auditslip.mainslipnumber',
    //             'trans_auditslip.processcode',
    //         )
    //         ->join('audit.mst_irregularitiessubcategory as subcat', 'subcat.irregularitiessubcatcode', '=', 'trans_auditslip.irregularitiessubcatcode')
    //         ->join('audit.mst_irregularitiescategory as cat', 'cat.irregularitiescatcode', '=', 'trans_auditslip.irregularitiescatcode')
    //         ->join('audit.mst_irregularities as ir', 'ir.irregularitiescode', '=', 'trans_auditslip.irregularitiescode')
    //         ->leftjoin('audit.auditeescheme as scheme', 'scheme.auditeeschemecode', '=', 'trans_auditslip.auditeeschemecode')
    //         // ->leftJoin('audit.sliptransactiondetail as tr', 'trans_auditslip.auditslipid', '=', 'tr.auditslipid')
    //         ->leftJoin('audit.slipfileupload as t3', 'trans_auditslip.auditslipid', '=', 't3.auditslipid')
    //         ->leftJoin('audit.liability as l', 'trans_auditslip.auditslipid', '=', 'l.auditslipid')
    //         ->leftJoin('audit.fileuploaddetail as t2', 't2.fileuploadid', '=', 't3.fileuploadid')
    //         ->join('audit.sliphistorytransactions as tr', 'trans_auditslip.auditslipid', '=', 'tr.auditslipid')
    //         ->leftjoin('audit.mst_subobjection as s', 's.subobjectionid', '=', 'trans_auditslip.subobjectionid')
    //         ->join('audit.mst_process as p', 'p.processcode', '=', 'trans_auditslip.processcode')
    //         ->where('trans_auditslip.statusflag', 'Y');
    //     // ->where(function ($query) {
    //     //     $query->where('tr.forwardedto', 2)
    //     //           ->where('tr.forwardedtousertypecode', 'I')
    //     //           ->orWhere(function ($query) {
    //     //               $query->where('t.forwardedby', 2)
    //     //                     ->where('t.forwardedbyusertypecode', 'I');
    //     //           });
    //     // })

    //     // self::applyFilters($results1, $userid, $filter);
    //     $results1->where(function ($query) use ($userid, $filter) {
    //         if ($filter === 'A') {
    //             $query->where(function ($query) use ($userid) {
    //                 $query
    //                     ->Where(function ($query) use ($userid) {
    //                         $query
    //                             ->where('tr.forwardedto', $userid)
    //                             ->where('tr.forwardedtousertypecode', 'I');
    //                     })
    //                     ->orWhere(function ($query) use ($userid) {
    //                         $query
    //                             ->where('tr.forwardedby', $userid)
    //                             ->where('tr.forwardedbyusertypecode', 'I');
    //                     });
    //                 // ->orWhere('trans_auditslip.approvedby', $userid);
    //             });
    //         } elseif ($filter === 'P') {
    //             $query->where(function ($query) use ($userid) {
    //                 // $query->where('trans_auditslip.createdby', $userid)
    //                 //     ->whereIn('trans_auditslip.processcode', ['E']);
    //                 $query
    //                     ->where('trans_auditslip.forwardedto', $userid)
    //                     ->where('trans_auditslip.forwardedtousertypecode', 'I')
    //                     ->whereIn('trans_auditslip.processcode', ['F', 'U']);
    //             });
    //             // ->orWhere(function ($query) use ($userid) {
    //             //     $query->where('trans_auditslip.forwardedto', $userid)
    //             //         ->where('trans_auditslip.forwardedtousertypecode', 'A')
    //             //         ->whereNotIn('trans_auditslip.processcode', ['X', 'A']);
    //             //                 });
    //         } elseif ($filter === 'C') {
    //             $query
    //                 ->where(function ($query) use ($userid) {
    //                     // $query->where('trans_auditslip.createdby', $userid)
    //                     //     //   ->orWhere('trans_auditslip.approvedby', $userid);
    //                     //     ->orWhere(function ($query) use ($userid) {
    //                     //         $query->where('tr.forwardedby', $userid)
    //                     //             ->where('tr.forwardedbyusertypecode', 'A')
    //                     //     });
    //                     $query->Where(function ($query) use ($userid) {
    //                         $query
    //                             ->where('tr.forwardedto', $userid)
    //                             ->where('tr.forwardedtousertypecode', 'I')
    //                             ->whereIn('trans_auditslip.processcode', ['X', 'A']);
    //                     });
    //                 })
    //                 ->whereIn('trans_auditslip.processcode', ['X', 'A']);
    //         } elseif ($filter === 'F') {
    //             $query->where(function ($query) use ($userid) {
    //                 // $query->where('trans_auditslip.createdby', $userid)
    //                 //     ->whereIn('trans_auditslip.processcode', ['E']);
    //                 $query
    //                     ->where('tr.forwardedby', $userid)
    //                     ->where('tr.forwardedbyusertypecode', 'I');
    //             });
    //         }
    //     });

    //     if (($auditslipid) && ($action == 'edit')) {
    //         $results1->when($auditslipid, function ($query) use ($auditslipid) {
    //             $query->where('trans_auditslip.auditslipid', $auditslipid);
    //         });
    //     }
    //     // $results1 ->where(function ($query) use ($userid) {
    //     //     $auditeelogin = View::shared('auditeelogin'); // Retrieve the shared value

    //     //     $query->where(function ($subquery) use ($userid, $auditeelogin) {
    //     //         $subquery->where('trans_auditslip.forwardedto', '=', $userid)
    //     //                 ->where('trans_auditslip.forwardedtousertypecode', '=', $auditeelogin);
    //     //     })->orWhere(function ($subquery) use ($userid, $auditeelogin) {
    //     //         $subquery->where('tr.forwardedby', '=', $userid)
    //     //                 ->where('tr.forwardedbyusertypecode', '=', $auditeelogin);
    //     //     });
    //     // })

    //     $results1
    //         ->groupBy(
    //             'trans_auditslip.auditslipid',
    //             'trans_auditslip.tempslipnumber',
    //             'trans_auditslip.mainobjectionid',
    //             'trans_auditslip.amtinvolved',
    //             'trans_auditslip.slipdetails',
    //             'trans_auditslip.severitycode',
    //             's.subobjectionename',
    //             'subcat.irregularitiessubcatcode',
    //             'subcat.irregularitiessubcatelname',
    //             'cat.irregularitiescatcode',
    //             'cat.irregularitiescatelname',
    //             'trans_auditslip.schemastatus',
    //             'scheme.auditeeschemecode',
    //             'scheme.auditeeschemeelname',
    //             //  //'trans_auditslip.auditeeschemecode',
    //             'ir.irregularitiescode',
    //             'ir.irregularitieselname',
    //             'trans_auditslip.subobjectionid',
    //             'trans_auditslip.auditscheduleid',
    //             'trans_auditslip.processcode',
    //             'trans_auditslip.liability',
    //             'p.processelname',
    //             'trans_auditslip.rejoinderstatus',
    //             'trans_auditslip.rejoindercycle',
    //             DB::raw("trans_auditslip.remarks::json->>'content'"),
    //         )
    //         ->orderBy('trans_auditslip.auditslipid', 'asc');

    //     $querySql = $results1->toSql();
    //     $bindings = $results1->getBindings();

    //     $finalQuery = vsprintf(
    //         str_replace('?', "'%s'", $querySql),
    //         array_map('addslashes', $bindings)
    //     );

    //     print_r($finalQuery);
    //     exit;

    //     // dd($results1->toSql());
    //     $results1 = $results1->get();

    //     if (($action == 'fetch') && (!($results1->isEmpty())) && (!($auditslipid))) {
    //         $auditslipid = $results1[0]->auditslipid;
    //     }

    //     if ($auditslipid) {
    //         $historydel =
    //             DB::table('audit.sliphistorytransactions as st')
    //                 ->leftjoin('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'st.forwardedby')
    //                 ->select(
    //                     DB::raw("COALESCE(CAST(st.remarks::json->>'content' AS TEXT), '') AS remarks"),  // Fix JSON issue
    //                     'st.auditslipid',
    //                     'st.transhistoryid',
    //                     'st.forwardedby',
    //                     'st.forwardedbyusertypecode',
    //                     'st.rejoinderstatus',
    //                     'st.rejoindercycle',
    //                     'st.processcode',
    //                     'st.forwardedto',
    //                     'dp.username',
    //                     'st.forwardedon',
    //                     DB::raw("COALESCE((
    //                     SELECT STRING_AGG(
    //                         DISTINCT CONCAT(
    //                             COALESCE(fu.filename, ''), '-',
    //                             COALESCE(fu.filepath, ''), '-',
    //                             COALESCE(fu.filesize::TEXT, ''), '-',
    //                             COALESCE(fu.fileuploadid::TEXT, '')
    //                         ), ','
    //                     )
    //                     FROM audit.slipfileupload sf
    //                     LEFT JOIN audit.fileuploaddetail fu ON fu.fileuploadid = sf.fileuploadid
    //                     WHERE sf.auditslipid = st.auditslipid
    //                     AND (
    //                         (st.processcode = 'F' AND (sf.processcode = 'F' OR sf.processcode = 'T') )
    //                         OR
    //                         (st.processcode != 'F' AND sf.processcode = st.processcode
    //                         AND fu.usertypecode = st.forwardedbyusertypecode
    //                         AND fu.uploadedby = st.forwardedby)
    //                     )And sf.statusflag = 'Y'  and fu.statusflag = 'Y'

    //                     AND (
    //                         (st.rejoinderstatus IS NULL OR sf.rejoinderstatus = st.rejoinderstatus)
    //                     )
    //                     AND (
    //                         (st.rejoindercycle IS NULL OR sf.rejoindercycle = st.rejoindercycle)
    //                     )
    //                 ), '') as file_details")
    //                 )
    //                 ->orderBy('st.transhistoryid', 'asc')
    //                 ->where('st.auditslipid', $auditslipid)
    //                 ->where(function ($query) use ($userid) {
    //                     $auditeelogin = View::shared('auditeelogin');  // Retrieve the shared value

    //                     $query
    //                         ->where(function ($subquery) use ($userid, $auditeelogin) {
    //                             $subquery
    //                                 ->where('st.forwardedto', '=', $userid)
    //                                 ->where('st.forwardedtousertypecode', '=', $auditeelogin);
    //                         })
    //                         ->orWhere(function ($subquery) use ($userid, $auditeelogin) {
    //                             $subquery
    //                                 ->where('st.forwardedby', '=', $userid)
    //                                 ->where('st.forwardedbyusertypecode', '=', $auditeelogin);
    //                         })
    //                         ->orWhereIn('st.processcode', ['X', 'A']);
    //                 })
    //                 // ;
    //                 // $querySql = $historydel->toSql();
    //                 // $bindings = $historydel->getBindings();
    //                 // $finalQuery = vsprintf(
    //                 //     str_replace('?', "'%s'", $querySql),
    //                 //     array_map('addslashes', $bindings)
    //                 // );
    //                 // print_r($finalQuery);
    //                 ->get();
    //     } else
    //         $historydel = [];

    //     //     $historydel = DB::table('audit.trans_auditslip')
    //     //     ->select(
    //     //         DB::raw("COALESCE(CAST(tr.remarks::json->>'content' AS TEXT), '') AS remarks"), // Fix JSON issue
    //     //         // DB::raw("
    //     //         //     STRING_AGG(
    //     //         //         CONCAT(
    //     //         //             COALESCE(t2.filename, ''), '-',
    //     //         //             COALESCE(t2.filepath, ''), '-',
    //     //         //             COALESCE(t2.filesize::TEXT, ''), '-',
    //     //         //             COALESCE(t2.fileuploadid::TEXT, '')
    //     //         //         ), ','
    //     //         //     ) AS file_details
    //     //         // "),
    //     //         DB::raw("
    //     //         STRING_AGG(
    //     //             DISTINCT CASE
    //     //                 WHEN t3.statusflag = 'Y' and t2.statusflag = 'Y' and t2.usertypecode = tr.forwardedbyusertypecode  THEN
    //     //                     CONCAT(
    //     //                         COALESCE(t2.filename, ''), '-',
    //     //                         COALESCE(t2.filepath, ''), '-',
    //     //                         COALESCE(t2.filesize::TEXT, ''), '-',
    //     //                         COALESCE(t2.fileuploadid::TEXT, '')
    //     //                     )
    //     //                 ELSE NULL
    //     //             END, ','
    //     //         ) AS file_details
    //     //     "),

    //     //         'tr.processcode',
    //     //         'trans_auditslip.rejoinderstatus',
    //     //         'trans_auditslip.rejoindercycle',
    //     //         'tr.forwardedbyusertypecode'
    //     //     )
    //     //     ->leftJoin('audit.slipfileupload as t3', 'trans_auditslip.auditslipid', '=', 't3.auditslipid')
    //     //     ->join('audit.sliphistorytransactions as tr', 'trans_auditslip.auditslipid', '=', 'tr.auditslipid')
    //     //     ->leftJoin('audit.fileuploaddetail as t2', 't3.fileuploadid', '=', 't2.fileuploadid')
    //     //     // ->where(function ($query) {
    //     //     //     $query->whereNotNull('t2.fileuploadid') // Ensure fileuploadid exists
    //     //     //           ->whereColumn('tr.processcode', 't3.processcode')
    //     //     //           ->where(function ($subQuery) {
    //     //     //               $subQuery->whereColumn('tr.rejoinderstatus', 't3.rejoinderstatus')
    //     //     //                        ->orWhereNull('tr.rejoinderstatus'); // Allow NULL rejoinderstatus
    //     //     //           })
    //     //     //           ->orWhere(function ($subQuery) {
    //     //     //               $subQuery->whereColumn('tr.rejoindercycle', 't3.rejoindercycle')
    //     //     //                        ->where('tr.rejoinderstatus', 'Y'); // Only check rejoindercycle if status is 'Y'
    //     //     //           });

    //     //     //     // Check if fileuploadid is NULL
    //     //     //     $query->orWhereNull('t2.fileuploadid');
    //     //     // })

    //     //     ->where('trans_auditslip.statusflag', 'Y')
    //     //     // ->where('trans_auditslip.auditscheduleid', $auditscheduleid);
    //     //     ->when($auditslipid, function ($query) use ($auditslipid) {
    //     //         $query->where('trans_auditslip.auditslipid', $auditslipid);
    //     //     })
    //     //     ->where(function ($query) use ($userid) {
    //     //         $auditeelogin = View::shared('auditeelogin'); // Retrieve the shared value

    //     //         $query->where(function ($subquery) use ($userid, $auditeelogin) {
    //     //             $subquery->where('tr.forwardedto', '=', $userid)
    //     //                     ->where('tr.forwardedtousertypecode', '=', $auditeelogin);
    //     //         })->orWhere(function ($subquery) use ($userid, $auditeelogin) {
    //     //             $subquery->where('tr.forwardedby', '=', $userid)
    //     //                     ->where('tr.forwardedbyusertypecode', '=', $auditeelogin);
    //     //         });
    //     //     });

    //     // //    self::applyFilters($historydel, $userid, $filter);
    //     //     if ($auditslipid) {
    //     //         $historydel->where('trans_auditslip.auditslipid', $auditslipid);
    //     //     }

    //     //     $historydel->groupBy('tr.processcode', 'trans_auditslip.rejoinderstatus', 'trans_auditslip.rejoindercycle', 'tr.forwardedbyusertypecode',
    //     //     DB::raw("tr.remarks::json->>'content'"),'trans_auditslip.auditslipid'); // Ensure GROUP BY for aggregation
    //     //     $historydel->orderBy('trans_auditslip.auditslipid', 'asc')
    //     //   ;

    //     // Combine the results as needed, here we are returning both results as an array
    //     return collect([
    //         'auditDetails' => $results1,
    //         'historydel' => $historydel,
    //         // 'rejoinderUploadfile'   =>  $results3
    //     ]);
    // }

    // public static function fetchdata_auditee($userid, $auditslipid = null, $action, $filter)
    // {
    //     $statusFlag = 'Y';
    //     $data = collect();

    //     if (in_array($filter, ['A', 'P', 'C']) && ($action === 'fetch' || ($action == 'fetchwithdata'))) {
    //         $results = DB::table('audit.trans_auditslip as t1')
    //             ->join('audit.sliphistorytransactions as sh', 't1.auditslipid', '=', 'sh.auditslipid')
    //             ->select(
    //                 DB::raw('DISTINCT t1.auditslipid'),
    //                 't1.processcode',
    //                 't1.tempslipnumber'
    //             )
    //             ->where('t1.statusflag', 'Y')
    //             ->where(function ($query) use ($userid) {
    //                 $query->where(function ($q) use ($userid) {
    //                     $q
    //                         ->where('sh.forwardedby', $userid)
    //                         ->where('sh.forwardedbyusertypecode', 'I');
    //                 })->orWhere(function ($q) use ($userid) {
    //                     $q
    //                         ->where('sh.forwardedto', $userid)
    //                         ->where('sh.forwardedtousertypecode', 'I');
    //                 });
    //             });

    //         // Apply additional filters based on $filter
    //         if ($filter === 'C') {
    //             $results->whereIn('t1.processcode', ['A', 'X']);
    //         } elseif ($filter === 'P') {
    //             $results
    //                 ->where('t1.forwardedto', $userid)
    //                 ->where('t1.forwardedtousertypecode', 'A');
    //         }

    //         // Final query execution
    //         $data = $results->orderBy('t1.auditslipid', 'asc')->get();

    //         // Set default auditslipid
    //         if ($action === 'fetch' && !$data->isEmpty() && !$auditslipid) {
    //             $auditslipid = $data[0]->auditslipid;
    //         }
    //     }

    //     if ($auditslipid) {
    //         // $auditslipId = 243554;
    //         // $userId = '12730';
    //         $userTypeCode = 'I';

    //         $historydel = DB::table('audit.trans_auditslip as tas')
    //             ->select([
    //                 'tas.auditslipid',
    //                 DB::raw("
    //                 STRING_AGG(
    //                     DISTINCT CONCAT(
    //                         COALESCE(fud.filename, ''), '-',
    //                         COALESCE(fud.filepath, ''), '-',
    //                         COALESCE(fud.filesize::TEXT, ''), '-',
    //                         COALESCE(fud.fileuploadid::TEXT, '')
    //                     ),
    //                     ','
    //                 )
    //                 FILTER (
    //                     WHERE sf.statusflag = 'Y'
    //                         AND fud.usertypecode = 'I'
    //                         AND (
    //                             (tas.rejoinderstatus = sf.rejoinderstatus AND tas.rejoindercycle = sf.rejoindercycle)
    //                             OR (tas.rejoinderstatus IS NULL AND tas.rejoindercycle IS NULL)
    //                         )
    //                 ) AS auditeefileupload
    //             "),
    //                                 DB::raw("
    //                 STRING_AGG(
    //                     DISTINCT CONCAT(
    //                         COALESCE(l.notype, ''), '-',
    //                         COALESCE(l.liabilitygpfno, ''), '-',
    //                         COALESCE(l.liabilityname, ''), '-',
    //                         COALESCE(l.liabilitydesignation::TEXT, ''), '-',
    //                         COALESCE(l.liabilityamount::TEXT, ''), '-',
    //                         COALESCE(l.liabilityid::TEXT, ''), '-',
    //                         COALESCE(l.statusflag::TEXT, '')
    //                     ),
    //                     ','
    //                 )
    //                 FILTER (WHERE l.statusflag = 'Y') AS liabilitydel
    //             "),
    //                 DB::raw("COALESCE(tas.remarks->>'content', '') AS remarks"),
    //                 DB::raw("MAX(COALESCE(CAST(sht.remarks->>'content' AS TEXT), '')) AS historyremarks"),
    //                 // Other direct columns
    //                 'tas.tempslipnumber',
    //                 'tas.subobjectionid',
    //                 'tas.mainobjectionid',
    //                 'tas.amtinvolved',
    //                 'tas.slipdetails',
    //                 's.subobjectionename',
    //                 'subcat.irregularitiessubcatcode',
    //                 'subcat.irregularitiessubcatelname',
    //                 'cat.irregularitiescatcode',
    //                 'cat.irregularitiescatelname',
    //                 'tas.schemastatus',
    //                 'scheme.auditeeschemecode',
    //                 'scheme.auditeeschemeelname',
    //                 'ir.irregularitiescode',
    //                 'ir.irregularitieselname',
    //                 'tas.severitycode',
    //                 'tas.processcode',
    //                 'tas.liability',
    //                 'p.processelname',
    //                 'tas.rejoinderstatus',
    //                 'tas.rejoindercycle',
    //                 'sht.rejoinderstatus as tr_rejoinderstatus',
    //                 'sht.rejoindercycle as tr_rejoindercycle',
    //                 'tas.mainslipnumber',
    //                 'tas.processcode',
    //                 'sht.forwardedbyusertypecode',
    //                 DB::raw("
    //                 COALESCE((
    //                     SELECT STRING_AGG(
    //                         DISTINCT CONCAT(
    //                             COALESCE(fu.filename, ''), '-',
    //                             COALESCE(fu.filepath, ''), '-',
    //                             COALESCE(fu.filesize::TEXT, ''), '-',
    //                             COALESCE(fu.fileuploadid::TEXT, '')
    //                         ), ','
    //                     )
    //                     FROM audit.slipfileupload sf
    //                     LEFT JOIN audit.fileuploaddetail fu ON fu.fileuploadid = sf.fileuploadid
    //                     WHERE sf.auditslipid = " . $auditslipid . "

    //                     AND ((
    //                     (
    //                         (sht.processcode = 'F' AND (sf.processcode = 'F' OR sf.processcode = 'T') AND sf.statusflag = 'Y' AND fu.statusflag = 'Y')
    //                         OR (sht.processcode IN ('A', 'X') AND (sf.processcode = 'A' OR sf.processcode = 'R' OR sf.processcode = 'X') AND sf.statusflag = 'Y')
    //                         OR (sht.processcode IN ('T', 'R', 'M') AND (sf.processcode = sht.processcode)
    //                             AND fu.usertypecode = sht.forwardedbyusertypecode
    //                             AND fu.uploadedby = sht.forwardedby
    //                             AND sf.statusflag = 'Y' AND fu.statusflag = 'Y')
    //                     )
    //                     AND
    //                     (
    //                         (sht.rejoinderstatus IS NULL AND sht.rejoindercycle IS NULL AND sf.rejoinderstatus IS NULL AND sf.rejoindercycle IS NULL)
    //                         OR (sht.rejoinderstatus IS NOT NULL AND sf.rejoinderstatus = sht.rejoinderstatus)
    //                         OR (sht.rejoindercycle IS NOT NULL AND sf.rejoindercycle = sht.rejoindercycle)
    //                     ))
    //                     or (sht.processcode = 'F' AND (sht.rejoinderstatus = 'Y' and sht.rejoindercycle = '1')) And (sf.processcode = 'R' AND sf.statusflag = 'Y' AND fu.statusflag = 'Y' and sf.rejoinderstatus is null and sf.rejoindercycle is null and fu.usertypecode = 'A')
    //                 )
    //                 ), '') AS file_details")
    //             ])
    //             ->leftJoin(DB::raw("(SELECT auditslipid, remarks::json->>'content' AS content FROM audit.trans_auditslip) as remarks"), 'remarks.auditslipid', '=', 'tas.auditslipid')
    //             ->join('audit.mst_irregularitiessubcategory as subcat', 'subcat.irregularitiessubcatcode', '=', 'tas.irregularitiessubcatcode')
    //             ->join('audit.mst_irregularitiescategory as cat', 'cat.irregularitiescatcode', '=', 'tas.irregularitiescatcode')
    //             ->join('audit.mst_irregularities as ir', 'ir.irregularitiescode', '=', 'tas.irregularitiescode')
    //             ->leftJoin('audit.auditeescheme as scheme', 'scheme.auditeeschemecode', '=', 'tas.auditeeschemecode')
    //             ->leftJoin('audit.slipfileupload as sf', 'sf.auditslipid', '=', 'tas.auditslipid')
    //             ->leftJoin('audit.fileuploaddetail as fud', 'fud.fileuploadid', '=', 'sf.fileuploadid')
    //             ->leftJoin('audit.liability as l', 'l.auditslipid', '=', 'tas.auditslipid')
    //             ->join('audit.sliphistorytransactions as sht', 'sht.auditslipid', '=', 'tas.auditslipid')
    //             ->leftJoin('audit.mst_subobjection as s', 's.subobjectionid', '=', 'tas.subobjectionid')
    //             ->join('audit.mst_process as p', 'p.processcode', '=', 'tas.processcode')
    //             ->where('tas.statusflag', '=', 'Y')
    //             ->where('sht.auditslipid', '=', $auditslipid)
    //             ->where(function ($query) use ($userid, $userTypeCode) {
    //                 $query->where(function ($q) use ($userid, $userTypeCode) {
    //                     $q
    //                         ->where('sht.forwardedto', '=', $userid)
    //                         ->where('sht.forwardedtousertypecode', '=', $userTypeCode);
    //                 })->orWhere(function ($q) use ($userid, $userTypeCode) {
    //                     $q
    //                         ->where('sht.forwardedby', '=', $userid)
    //                         ->where('sht.forwardedbyusertypecode', '=', $userTypeCode);
    //                 });
    //             })
    //             ->groupBy([
    //                 'tas.auditslipid',
    //                 'tas.tempslipnumber',
    //                 'tas.subobjectionid',
    //                 'tas.mainobjectionid',
    //                 'tas.amtinvolved',
    //                 'tas.slipdetails',
    //                 's.subobjectionename',
    //                 'subcat.irregularitiessubcatcode',
    //                 'subcat.irregularitiessubcatelname',
    //                 'cat.irregularitiescatcode',
    //                 'cat.irregularitiescatelname',
    //                 'tas.schemastatus',
    //                 'scheme.auditeeschemecode',
    //                 'scheme.auditeeschemeelname',
    //                 'ir.irregularitiescode',
    //                 'ir.irregularitieselname',
    //                 'tas.severitycode',
    //                 'tas.processcode',
    //                 'tas.liability',
    //                 'p.processelname',
    //                 'tas.rejoinderstatus',
    //                 'tas.rejoindercycle',
    //                 'tas.mainslipnumber',
    //                 'sht.processcode',
    //                 'sht.forwardedbyusertypecode',
    //                 'sht.forwardedby',
    //                 'sht.rejoinderstatus',
    //                 'sht.rejoindercycle',
    //                 'sht.transhistoryid'
    //                 // 'tas.remarks::json->>content',
    //                 // 'sht.remarks::json->>content'
    //             ])
    //             ->orderBy('sht.transhistoryid', 'asc')
    //             ->get();
    //     } else
    //         $historydel = collect();

    //     // Combine the results as needed, here we are returning both results as an array
    //     return collect([
    //         'auditDetails' => $data,
    //         'historydel' => $historydel,
    //         // 'rejoinderUploadfile'   =>  $results3
    //     ]);
    // }

    public static function fetchdata_auditee($userid, $auditslipid = null, $action, $filter, $instid, $spilloverflag, $auditscheduleid)
    {
        $statusFlag = 'Y';
        $data = collect();

        // Initial fetch based on filter and action
        if (in_array($filter, ['A', 'P', 'C']) && in_array($action, ['fetch', 'fetchwithdata'])) {
            $results = DB::table('audit.trans_auditslip as t1')
                ->join('audit.sliphistorytransactions as sh', 't1.auditslipid', '=', 'sh.auditslipid')
                ->select(DB::raw('DISTINCT t1.auditslipid'), 't1.processcode', 't1.tempslipnumber')
                ->where('t1.statusflag', 'Y')
                ->where(function ($query) use ($userid) {
                    $query->where(function ($q) use ($userid) {
                        $q
                            ->where('sh.forwardedby', $userid)
                            ->where('sh.forwardedbyusertypecode', 'I');
                    })->orWhere(function ($q) use ($userid) {
                        $q
                            ->where('sh.forwardedto', $userid)
                            ->where('sh.forwardedtousertypecode', 'I');
                    });
                });

            if ($spilloverflag === 'Y') {
                $results->whereIn('t1.auditscheduleid', function ($query) use ($instid) {
                    $query
                        ->select('inst.auditscheduleid')
                        ->from('audit.auditplan as ap')
                        ->join('audit.inst_auditschedule as inst', 'inst.auditplanid', '=', 'ap.auditplanid')
                        ->where('ap.instid', $instid)
                        ->where('inst.statusflag', 'F')
                        ->where(function ($q) {
                            $q
                                ->where('ap.spilloverflag', 'Y')
                                ->orWhere('ap.carryforwardflag', 'Y');
                        });
                    // ->where('ap.statusflag', 'F');;
                });
            } else {
                $results->where('t1.auditscheduleid', $auditscheduleid);
            }

            // if ($filter === 'C') {
            //     $results->whereIn('t1.processcode', ['A', 'X']);
            // } elseif ($filter === 'P') {
            //     $results
            //         ->where('t1.forwardedto', $userid)
            //         ->where('t1.forwardedtousertypecode', 'A');
            // }
            if ($filter === 'C') {
                $results->whereIn('t1.processcode', ['A', 'X']);
            } elseif ($filter === 'P') {
                $results
                    ->where('t1.forwardedto', $userid)
                    ->where('t1.forwardedtousertypecode', 'I')
                    ->whereIn('t1.processcode', ['F', 'U']);
            } elseif ($filter === 'F') {
                $results->whereIn('t1.processcode', ['R', 'M']);
            }

            $data = $results->orderBy('t1.auditslipid')->get();

            if ($action === 'fetch' && !$data->isEmpty() && !$auditslipid) {
                $auditslipid = $data[0]->auditslipid;
            }
        }

        // Fetch slip details
        if ($auditslipid) {
            $userTypeCode = 'I';

            $historydel = DB::table('audit.trans_auditslip as tas')
                ->select([
                    'tas.auditslipid',
                    'tas.tempslipnumber',
                    'tas.subobjectionid',
                    'tas.mainobjectionid',
                    'tas.amtinvolved',
                    'tas.slipdetails',
                    's.subobjectionename',
                    'subcat.irregularitiessubcatcode',
                    'subcat.irregularitiessubcatelname',
                    'cat.irregularitiescatcode',
                    'cat.irregularitiescatelname',
                    'tas.schemastatus',
                    'scheme.auditeeschemecode',
                    'scheme.auditeeschemeelname',
                    'ir.irregularitiescode',
                    'ir.irregularitieselname',
                    'tas.severitycode',
                    'tas.processcode',
                    'tas.liability',
                    'p.processelname',
                    'tas.rejoinderstatus',
                    'tas.rejoindercycle',
                    'tas.mainslipnumber',
                    'sht.forwardedbyusertypecode',
                    'sht.forwardedon',
                    'sht.rejoinderstatus as tr_rejoinderstatus',
                    'sht.rejoindercycle as tr_rejoindercycle',
                    DB::raw("COALESCE(tas.remarks->>'content', '') AS remarks"),
                    DB::raw("MAX(COALESCE(sht.remarks->>'content', '')) AS historyremarks"),
                    DB::raw("
                    STRING_AGG(
                        DISTINCT CONCAT(
                            COALESCE(fud.filename, ''), '-',
                            COALESCE(fud.filepath, ''), '-',
                            COALESCE(fud.filesize::TEXT, ''), '-',
                            COALESCE(fud.fileuploadid::TEXT, '')
                        ),
                        ','
                    ) FILTER (
                        WHERE sf.statusflag = 'Y'
                        AND fud.usertypecode = 'I'
                        AND (
                            (tas.rejoinderstatus = sf.rejoinderstatus AND tas.rejoindercycle = sf.rejoindercycle)
                            OR (tas.rejoinderstatus IS NULL AND tas.rejoindercycle IS NULL)
                        )
                    ) AS auditeefileupload
                "),
                    DB::raw("
                    STRING_AGG(
                        DISTINCT CONCAT(
                            COALESCE(l.notype, ''), '-',
                            COALESCE(l.liabilitygpfno, ''), '-',
                            COALESCE(l.liabilityname, ''), '-',
                            COALESCE(l.liabilitydesignation::TEXT, ''), '-',
                            COALESCE(l.liabilityamount::TEXT, ''), '-',
                            COALESCE(l.liabilityid::TEXT, ''), '-',
                            COALESCE(l.statusflag::TEXT, '')
                        ),
                        ','
                    ) FILTER (WHERE l.statusflag = 'Y') AS liabilitydel
                "),
                    DB::raw("
                    COALESCE((
                        SELECT STRING_AGG(
                            DISTINCT CONCAT(
                                COALESCE(fu.filename, ''), '-',
                                COALESCE(fu.filepath, ''), '-',
                                COALESCE(fu.filesize::TEXT, ''), '-',
                                COALESCE(fu.fileuploadid::TEXT, '')
                            ), ','
                        )
                        FROM audit.slipfileupload sf
                        LEFT JOIN audit.fileuploaddetail fu ON fu.fileuploadid = sf.fileuploadid
                        WHERE sf.auditslipid = " . intval($auditslipid) . "
                        AND (
                            (
                                (sht.processcode = 'F' AND sf.processcode IN ('F', 'T') AND sf.statusflag = 'Y' AND fu.statusflag = 'Y')
                                OR (sht.processcode IN ('A', 'X') AND sf.processcode IN ('A', 'R', 'X') AND sf.statusflag = 'Y')
                                OR (sht.processcode IN ('T', 'R', 'M') AND sf.processcode = sht.processcode
                                    AND fu.usertypecode = sht.forwardedbyusertypecode
                                    AND fu.uploadedby = sht.forwardedby
                                    AND sf.statusflag = 'Y' AND fu.statusflag = 'Y')
                            )
                            AND (
                                (sht.rejoinderstatus IS NULL AND sht.rejoindercycle IS NULL AND sf.rejoinderstatus IS NULL AND sf.rejoindercycle IS NULL)
                                OR (sht.rejoinderstatus IS NOT NULL AND sf.rejoinderstatus = sht.rejoinderstatus)
                                OR (sht.rejoindercycle IS NOT NULL AND sf.rejoindercycle = sht.rejoindercycle)
                            )

                        OR (
                            sht.processcode = 'F'
                            AND sht.rejoinderstatus = 'Y'
                            AND sht.rejoindercycle = '1'
                            AND sf.processcode = 'R'
                            AND sf.statusflag = 'Y'
                            AND fu.statusflag = 'Y'
                            AND sf.rejoinderstatus IS NULL
                            AND sf.rejoindercycle IS NULL
                            AND fu.usertypecode = 'A'
                        )
                            )
                    ), '') AS file_details
                ")
                ])
                ->join('audit.mst_irregularitiessubcategory as subcat', 'subcat.irregularitiessubcatcode', '=', 'tas.irregularitiessubcatcode')
                ->join('audit.mst_irregularitiescategory as cat', 'cat.irregularitiescatcode', '=', 'tas.irregularitiescatcode')
                ->join('audit.mst_irregularities as ir', 'ir.irregularitiescode', '=', 'tas.irregularitiescode')
                ->join('audit.mst_process as p', 'p.processcode', '=', 'tas.processcode')
                ->join('audit.sliphistorytransactions as sht', 'sht.auditslipid', '=', 'tas.auditslipid')
                ->leftJoin('audit.mst_subobjection as s', 's.subobjectionid', '=', 'tas.subobjectionid')
                ->leftJoin('audit.auditeescheme as scheme', 'scheme.auditeeschemecode', '=', 'tas.auditeeschemecode')
                ->leftJoin('audit.slipfileupload as sf', 'sf.auditslipid', '=', 'tas.auditslipid')
                ->leftJoin('audit.fileuploaddetail as fud', 'fud.fileuploadid', '=', 'sf.fileuploadid')
                ->leftJoin('audit.liability as l', 'l.auditslipid', '=', 'tas.auditslipid')
                ->where('tas.statusflag', '=', 'Y')
                ->where('sht.auditslipid', '=', $auditslipid)
                ->where(function ($query) use ($userid, $userTypeCode) {
                    $query->where(function ($q) use ($userid, $userTypeCode) {
                        $q
                            ->where('sht.forwardedto', '=', $userid)
                            ->where('sht.forwardedtousertypecode', '=', $userTypeCode);
                    })->orWhere(function ($q) use ($userid, $userTypeCode) {
                        $q
                            ->where('sht.forwardedby', '=', $userid)
                            ->where('sht.forwardedbyusertypecode', '=', $userTypeCode);
                    });
                })
                ->groupBy([
                    'tas.auditslipid', 'tas.tempslipnumber', 'tas.subobjectionid', 'tas.mainobjectionid',
                    'tas.amtinvolved', 'tas.slipdetails', 's.subobjectionename', 'subcat.irregularitiessubcatcode',
                    'subcat.irregularitiessubcatelname', 'cat.irregularitiescatcode', 'cat.irregularitiescatelname',
                    'tas.schemastatus', 'scheme.auditeeschemecode', 'scheme.auditeeschemeelname', 'ir.irregularitiescode',
                    'ir.irregularitieselname', 'tas.severitycode', 'tas.processcode', 'tas.liability', 'p.processelname',
                    'tas.rejoinderstatus', 'tas.rejoindercycle', 'tas.mainslipnumber', 'sht.processcode',
                    'sht.forwardedbyusertypecode', 'sht.forwardedby', 'sht.rejoinderstatus', 'sht.rejoindercycle',
                    'sht.transhistoryid'
                ])
                ->orderBy('sht.transhistoryid', 'asc')
                ->get();
        } else {
            $historydel = collect();
        }

        return collect([
            'auditDetails' => $data,
            'historydel' => $historydel
        ]);
    }

    public static function deleteLiability($liabilityid, $session_userid)
    {
        if ($liabilityid > 0) {
            for ($i = 0; $i < count($liabilityid); $i++) {
                if ($liabilityid[$i]) {
                    DB::table('audit.liability')
                        ->where('liabilityid', $liabilityid[$i])
                        ->update(array('statusflag' => 'N', 'updatedby' => $session_userid, 'updatedon' => View::shared('get_nowtime')));
                }
            }
        }
    }

    public static function insertupdateLiability($liabilityid, $notype, $name, $gpfno, $designation, $amount, $processcode, $auditslipid, $session_userid, $statusflag)
    {
        $liabilityidcount = count($liabilityid);

        if ($liabilityidcount > 0) {
            for ($i = 0; $i < $liabilityidcount; $i++) {
                $data = array(
                    'auditslipid' => $auditslipid,
                    'notype' => $notype[$i],
                    'liabilityname' => $name[$i],
                    'liabilitygpfno' => $gpfno[$i],
                    'liabilitydesignation' => $designation[$i],
                    'liabilityamount' => $amount[$i],
                );

                if ($liabilityid[$i]) {
                    if ($processcode == 'E')
                        $data['statusflag'] = 'Y';
                    else {
                        if ($statusflag[$i] == 1)
                            $data['statusflag'] = 'Y';
                        else
                            $data['statusflag'] = 'C';
                    }

                    $data['updatedby'] = $session_userid;
                    $data['updatedon'] = View::shared('get_nowtime');

                    DB::table('audit.liability')
                        ->where('liabilityid', $liabilityid[$i])
                        ->update($data);
                } else {
                    $data['statusflag'] = 'Y';
                    $data['createdby'] = $session_userid;
                    $data['createdon'] = View::shared('get_nowtime');

                    DB::table('audit.liability')->insert(
                        $data
                    );
                }
            }

            if ($i <= count($name)) {
                for ($l = $i; $l < count($name); $l++) {
                    $data = array(
                        'notype' => $notype[$i],
                        'auditslipid' => $auditslipid,
                        'liabilityname' => $name[$l],
                        'liabilitygpfno' => $gpfno[$l],
                        'liabilitydesignation' => $designation[$l],
                        'liabilityamount' => $amount[$i],
                        'statusflag' => 'Y',
                        'createdby' => $session_userid,
                        'createdon' => View::shared('get_nowtime'),
                    );
                    DB::table('audit.liability')->insert(
                        $data
                    );
                }
            }
        }
    }

    public static function FetchSuperCheckList($scheduleid, $deptcode, $catcode, $subcatid = null)
    {
        $query = DB::table('audit.super_check')
            ->where('deptcode', $deptcode)
            ->where('catcode', $catcode)
            ->where('statusflag', 'Y')
            ->orderBy('sl_no', 'asc');

        if ($subcatid !== null) {
            $query->where('subcatcode', $subcatid);
        }

        $FetchSuperCheckList = $query->get();

        return $FetchSuperCheckList;
    }

    public static function Supercheck_QuesAns($data)
    {
        $json_encode_quesno = json_encode($data['quesno'], true);
        $json_encode_questiontype = json_encode($data['questiontype'], true);
        $json_encode_remarks = json_encode($data['answer_remarks'], true);

        $Data = array(
            'schedule_id' => $data['auditscheduleid'],
            'auditplanid' => $data['auditplanid'],
            // 'supercheck_mapid' =>$json_encode_quesno,
            'questiontype' => $json_encode_questiontype,
            'remarks' => $json_encode_remarks
        );  // $data will override if keys overlap
        // Insert into audit_log
        return $inserted = DB::table('audit.supercheck_trans')->insert($Data);
    }

    public static function Slipexists($schid)
    {
        $countQuery = DB::table('audit.trans_auditslip as tas')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'tas.auditplanid')
            ->where('tas.auditscheduleid', $schid);

        // Add the selectRaw part for counting, with DISTINCT and GROUP BY to avoid duplicates
        $countQuery
            ->selectRaw("COUNT(DISTINCT CASE WHEN tas.processcode  IN ('A', 'X') THEN tas.auditslipid END) as slipcount
                      ")
            ->groupBy('tas.auditscheduleid');  // Group by 'auditscheduleid' to avoid duplicates

        // Execute the query and get the first result
        $slipexists = $countQuery->exists();
        if ($slipexists) {
            $slippresent = 'Y';
        } else {
            $slippresent = 'N';
        }

        return $slippresent;
    }

    public static function ExceedexitMeet($schid)
    {
        $ExceedexitMeet = DB::table('audit.inst_auditschedule')
            ->whereRaw("(todate + interval '3 days') < current_date")
            ->whereNull('exitmeetdate')
            ->where('auditscheduleid', $schid)
            ->exists();

        if ($ExceedexitMeet) {
            $ExceedexitMeetPresent = 'Y';
        } else {
            $ExceedexitMeetPresent = 'N';
        }

        return $ExceedexitMeetPresent;
    }

    public static function getPendingUsers($auditscheduleid)
    {
        $notCompletedUserIds = DB::table('audit.inst_schteammember')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('diarystatus', '!=', 'F')
            ->where('statusflag', '=', 'Y')
            ->pluck('userid')
            ->toArray();

        if (empty($notCompletedUserIds)) {
            return [];
        }

        $userDetails = DB::table('audit.deptuserdetails as du')
            ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'du.desigcode')
            ->whereIn('du.deptuserid', $notCompletedUserIds)
            ->select(
                DB::raw("du.username || ' - ' || desig.desigelname as user_en"),
                DB::raw("du.usertamilname || ' - ' || desig.desigtlname as user_ta")
            )
            ->get();

        return [
            'user_en' => $userDetails->pluck('user_en')->toArray(),
            'user_ta' => $userDetails->pluck('user_ta')->toArray()
        ];
    }

    public static function insertExitMeeting($auditscheduleid, $instid, $nextQuarterDate, $userid, $now)
    {
        $exists = DB::table('audit.trans_spillexit')
            ->where('auditscheduleid', $auditscheduleid)
            ->exists();

        if (!$exists) {
            DB::table('audit.trans_spillexit')->insert([
                'auditscheduleid' => $auditscheduleid,
                'instid' => $instid,
                'spillexitmeetdate' => $nextQuarterDate,
                'statusflag' => 'Y',
                'createdon' => $now,
                'createdby' => $userid,
                'updatedon' => $now,
                'updatedby' => $userid,
            ]);
        }
    }

    public static function updateDiaryFlag($auditscheduleid, $diaryflag, $nextQuarterDate, $userid, $now)
    {
        DB::table('audit.inst_auditschedule')
            ->where('auditscheduleid', $auditscheduleid)
            ->update([
                'exitmeetdate' => $nextQuarterDate,
                'diaryflag' => $diaryflag,
                'updatedon' => $now,
                'updatedby' => $userid,
                'spillover' => 'Y'
            ]);
    }

    public static function checkscheduleid($auditscheduleid, $auditslipid)
    {
        $query = DB::table('audit.trans_auditslip as tp')
            ->where('tp.auditslipid', '=', $auditslipid)  // Must be status 'F'// Exit meeting must be done/set
            ->select('tp.auditscheduleid')  // Select schedule ID
            ->get();

        if ($query[0]->auditscheduleid == $auditscheduleid)
            $status = 'success';
        else
            $status = 'false';

        return $status;
    }

    public static function checkalreadydraftexists($auditscheduleid, $sessionuserid)
    {
        $query = DB::table('audit.trans_auditslip as in')
            ->where('in.createdby', $sessionuserid)
            ->where('in.auditscheduleid', $auditscheduleid)
            ->where('in.processcode', 'E')
            ->count('auditslipid');
        return $query;
    }

    public static function check_entrymeetprerequisite($scheduleid)
    {
        try {
            $result = DB::select('SELECT audit.check_entrymeetprerequisite(?) AS response', [$scheduleid]);
            // print_r($result);
            // exit;

            return $result;

            // return response()->json($result);

            // Log::info('PostgreSQL function response: ' . json_encode($response));

            // return $response;
        } catch (\Illuminate\Database\QueryException $e) {
            // Log::error('Database Query Error: ' . $e->getMessage());
            throw new \Exception('Database error occurred while automating work allocation.');
        } catch (\Exception $e) {
            // Log::error('General Error: ' . $e->getMessage());
            throw new \Exception('Unexpected error occurred while automating work allocation.');
        }
    }

    public static function notresponseusererror($auditscheduleid)
    {
        return DB::table('audit.trans_auditslip as ta')
            ->join('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'ta.forwardedto')
            ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'dp.desigcode')
            ->select(
                'dp.username',
                'desig.desigesname',
                DB::raw('COUNT(*) AS total_count'),
                DB::raw("STRING_AGG(ta.tempslipnumber::text, ',') AS slip_numbers")
            )
            ->where('ta.auditscheduleid', $auditscheduleid)
            ->where('ta.forwardedtousertypecode', 'A')
            ->whereNotIn('ta.processcode', ['X', 'A'])
            ->groupBy('dp.username', 'desig.desigesname')
            ->get();
    }

    // public static function checkingsubobjection_undermainobjection($mainobjectionid, $subobjectionid)
    // {
    //     $query = DB::table('audit.mst_subobjection')
    //         ->where('mainobjectionid', $mainobjectionid)
    //         ->where('subobjectionid', $subobjectionid)
    //         ->count('subobjectionid');
    //     return $query;
    // }

    public static function checkingsubobjection_undermainobjection($mainobjectionid, $subobjectionid, $financialyear, $catcode = null, $subcatid = null, $annadhanam_only)
    {
        if ($financialyear == '2025') {
            $results = DB::table('audit.mst_subobjection as su')
                ->leftJoin('audit.map_allocation_objection_old as mao', 'su.subobjectionid', '=', 'mao.subobjectionid')
                ->where(function ($query) use ($mainobjectionid, $subobjectionid) {
                    $query
                        ->where(function ($q) use ($mainobjectionid, $subobjectionid) {
                            $q
                                ->where('su.mainobjectionid', $mainobjectionid)
                                ->where('su.subobjectionid', $subobjectionid);
                        })
                        ->orWhere(function ($q) use ($mainobjectionid, $subobjectionid) {
                            $q
                                ->where('mao.mainobjectionid', $mainobjectionid)
                                ->where('mao.subobjectionid', $subobjectionid);
                        });
                })
                ->where('su.statusflag', 'Y');
        } else {
            $results = DB::table('audit.mst_subobjection as su')
                ->distinct()
                ->select('subobjectionename', 'subobjectiontname', 'su.subobjectionid')
                ->leftjoin('audit.map_allocation_objection as mao', 'su.subobjectionid', '=', 'mao.subobjectionid')
                // ->where('mao.mainobjectionid', $mainobjectionid)
                // ->where('mao.subobjectionid', $subobjectionid)
                // ->where('mao.catcode', $catcode)
                ->where(function ($query) use ($mainobjectionid, $subobjectionid) {
                    $query
                        ->where(function ($q) use ($mainobjectionid, $subobjectionid) {
                            $q
                                ->where('su.mainobjectionid', $mainobjectionid)
                                ->where('su.subobjectionid', $subobjectionid);
                        })
                        ->orWhere(function ($q) use ($mainobjectionid, $subobjectionid) {
                            $q
                                ->where('mao.mainobjectionid', $mainobjectionid)
                                ->where('mao.subobjectionid', $subobjectionid);
                        });
                })
                ->where('su.statusflag', 'Y');

            if ($annadhanam_only == 'Y') {
                $results = $results->whereIn('mao.catcode', [$catcode, '08']);
            } else {
                $results = $results->where('mao.catcode', $catcode);
            }

            if ($subcatid) {
                $results->where('mao.auditeeins_subcategoryid', $subcatid);
            }

            $results->orderBy('subobjectionename', 'asc');
        }

        return $results->count();
    }

    public static function performanceSlipexists($schid)
    {
        $countQuery = DB::table('audit.praudit_transpara as per')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'per.auditplanid')
            ->where('per.auditscheduleid', $schid);

        // Add the selectRaw part for counting, with DISTINCT and GROUP BY to avoid duplicates
        $countQuery
            ->selectRaw("COUNT(DISTINCT CASE WHEN per.statusflag  IN ('Y') THEN per.praudittransid END) as slipcount
                      ")
            ->groupBy('per.auditscheduleid');  // Group by 'auditscheduleid' to avoid duplicates

        // Execute the query and get the first result
        $slipexists = $countQuery->exists();
        if ($slipexists) {
            $slippresent = 'Y';
        } else {
            $slippresent = 'N';
        }

        return $slippresent;
    }

    public static function getfinancialyear()
    {
        $chargeData = session('charge');
        $session_deptcode = $chargeData->deptcode;

        return DB::table('audit.mst_auditperiod')
            ->select(
                'auditperiodid',
                DB::raw("CONCAT(fromyear, ' - ', toyear) as auditperiod")
            )
            ->where('statusflag', 'Y')
            ->where('financestatus', 'N')
            ->where('deptcode', $session_deptcode)
              ->where(function ($query) {
            $query->where(function ($q) {
                $q->where('fromyear', 2024)
                  ->where('toyear', 2025);
            })
            ->orWhere(function ($q) {
                $q->where('fromyear', 2025)
                  ->where('toyear', 2026);
            });
        })
            ->whereIn('lagacyyear', ['N', 'B'])
            ->orderBy('auditperiodid', 'asc')
            ->get();
    }

    public static function checkCpsAllowed($instid)
    {
        $exists = DB::table('audit.mst_auditeeins_subcategory as s')
            ->join('audit.mst_institution as ins', function ($join) {
                $join
                    ->on('ins.catcode', '=', 's.catcode')
                    ->on('ins.subcatid', '=', 's.auditeeins_subcategoryid');
            })
            ->where('ins.instid', $instid)
            ->where('s.iscpsflag', 'Y')
            ->exists();

        return $exists ? 'Y' : 'N';
    }

    public static function CpsNotFinalized($instid, $auditscheduleid)
    {
        $record = DB::table('audit.inst_auditschedule as sch')
            ->join('audit.auditplan as plan', 'sch.auditplanid', '=', 'plan.auditplanid')
            ->join('audit.mst_institution as ins', 'plan.instid', '=', 'ins.instid')
            ->join('audit.mst_auditeeins_subcategory as sub', function ($join) {
                $join
                    ->on('ins.catcode', '=', 'sub.catcode')
                    ->on('ins.subcatid', '=', 'sub.auditeeins_subcategoryid');
            })
            ->where('sch.auditscheduleid', $auditscheduleid)
            ->where('ins.instid', $instid)
            ->where('sub.iscpsflag', 'Y')
            ->select('sch.cps_completed')
            ->first();

        if ($record) {
            return $record->cps_completed !== 'Y';
        }

        return false;
    }

    public static function getPacsId($instid)
    {
        $data = DB::table('audit.temp_readyforaudit')
            ->where('instid', $instid)
            ->where('statusflag', 'S')
            ->select('erpno', 'dccbid')
            ->first();
        return $data;
    }

public static function getspilloverslipdetails($lastauditplanid)
{
$results = DB::table('audit.trans_auditslip as tas')
->select(
'tas.auditslipid',
'apm.planname',
'tas.tempslipnumber',
'ma.objectionename',
's.subobjectionename',
'tas.slipdetails',
'tas.mainslipnumber',
'tas.severitycode',
'tas.liability',
'tas.amtinvolved',
'p.processelname',
'tas.processcode'
)
->join('audit.mst_mainobjection as ma', 'ma.mainobjectionid', '=', 'tas.mainobjectionid')
->join('audit.mst_subobjection as s', 's.subobjectionid', '=', 'tas.subobjectionid')
->join('audit.inst_auditschedule as sch', 'tas.auditscheduleid', '=', 'sch.auditscheduleid')->join('audit.spilloverdetails as sp', function ($join) use ($lastauditplanid) {
    $join->where('sp.lastauditplanid', $lastauditplanid);
    // $join->where('sp.completed', 'N');
})
->join('audit.mst_process as p', 'tas.processcode', '=', 'p.processcode')
->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'sch.auditplanid')
->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'ap.planmappingid')
->whereRaw('sch.auditplanid = ANY(sp.auditplanid)')
->whereColumn('sch.auditplanid', '<>', 'sp.lastauditplanid')
    ->orderBy('apm.planmappingid', 'ASC')
    ->orderBy('tas.tempslipnumber', 'ASC')->get();
    return $results;
    }

}
