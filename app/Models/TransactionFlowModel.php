<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use DateTime;
use InvalidArgumentException;

class TransactionFlowModel extends Model
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
    protected static $rolemapping_table = BaseModel::ROLEMAPPING_TABLE;
    protected static $chargedetail_table = BaseModel::CHARGEDETAIL_TABLE;
    protected static $transactionflow_table = BaseModel::TRANSACTIONFLOW_TABLE;
    protected static $userchargedetail_table = BaseModel::USERCHARGEDETAIL_TABLE;
    protected static $roleaction_table = BaseModel::ROLEACTION_TABLE;
    protected static $leavetype_table = BaseModel::LEAVETYPE_TABLE;
    protected static $transactiondetail_table = BaseModel::TRANSACTIONDETAILTABLE;
    protected static $historytransaction_table = BaseModel::HISTORYTRANSACTION_TABLE;
    protected static $indleavedetail_table = BaseModel::INDLEAVEDETAIL_TABLE;
    protected static $instschedule_table = BaseModel::INSTSCHEDULE_TABLE;
    protected static $instschedulemem_table = BaseModel::INSTSCHEDULEMEM_TABLE;
    protected static $auditplan_table = BaseModel::AUDITPLAN_TABLE;
    protected static $instituiion_table = BaseModel::INSTITUTION_TABLE;
    protected static $dataTransferFromTofun_table = BaseModel::DATATRANSFERFROMTOUSER;
    protected static $migrateallocationslip_table = BaseModel::WORKALLOCATIONDISTRIBUTION;
    protected static $auditor_instmapping_table = BaseModel::AUDITOR_INSTMAPPING_TABLE;
    protected static $nodatachange_fun = BaseModel::NODATACHANGE_FUNCTION;
    protected static $logothertransschedule = BaseModel::LOGOTHERTRANS_SCHEDULEDELTABLE;
    protected static $mandaysextension_table = BaseModel::MANDAYSEXTENSION;
    protected static $process_table = BaseModel::PROCESSFLAG_TABLE;
    protected static $indleaveindetail_table = BaseModel::INDLEAVEINDETAIL_TABLE;
    protected static $futureplanheadtransfer_table = BaseModel::FUTUREPLANHEADTRANSFER;
    protected static $logothertransplandel_table = BaseModel::LOGOTHERTRANSPLANDEL;
    protected static $logothertransscheduledel_table = BaseModel::LOGOTHERTRANS_SCHEDULEDELTABLE;
    protected static $auditplanteammember_table = BaseModel::AUDITPLANTEAMMEM_TABLE;
    protected static $fn_headchangeforplan = BaseModel::fn_headchangeforplan;
    protected static $fn_calculateTodateWithMandaysTeamsize = BaseModel::fn_calculateTodateWithMandaysTeamsize;
	protected static $mst_transferfromtodept_table = BaseModel::MST_TRANSFERFROMTODEPT_TABLE;


    public static function getdeptbasedonsession()
    {
        $userData = session('charge');
        $session_deptcode = $userData->deptcode ?? null;

        $query = DB::table(self::$department_table)
            ->where('statusflag', 'Y');

        if ($session_deptcode) {
            $query->where('deptcode', $session_deptcode);
        }

        $query->orderBy('orderid', 'asc');

        $departments = $query->get();

        return $departments;
    }

///leave apply start
	    public static function getShortLeaveScheduleDetails($userid, $deptcode, $fromDate, $toDate)
    {
        return DB::table(self::$auditplanteammember_table . ' as aptm')
            ->join(self::$auditplan_table . ' as ap', 'ap.auditteamid', '=', 'aptm.auditplanteamid')
            ->join(BaseModel::AUDITPLANTEAM_TABLE . ' as apt', 'apt.auditplanteamid', '=', 'aptm.auditplanteamid')
            ->join(self::$instschedule_table . ' as sch', 'sch.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$instschedulemem_table . ' as schmem', function ($join) {
                $join
                    ->on('schmem.auditscheduleid', '=', 'sch.auditscheduleid')
                    ->on('schmem.userid', '=', 'aptm.userid');
            })
            ->join(self::$instituiion_table . ' as inst', 'inst.instid', '=', 'ap.instid')
            ->leftJoin(self::$district_table . ' as dist', 'dist.distcode', '=', 'inst.distcode')
            ->where('aptm.userid', $userid)
            // ->where('aptm.statusflag', 'Y')
            // ->where('ap.statusflag', 'Y')
            // ->where('apt.statusflag', 'Y')
            ->where('schmem.statusflag', 'Y')
            ->where('inst.statusflag', 'Y')
            ->where('inst.deptcode', $deptcode)
            ->where('sch.statusflag', 'F')
            ->whereNotNull('sch.entrymeetdate')
            ->whereNull('sch.exitmeetdate')
            ->whereRaw('COALESCE(sch.proposedexitmeetdate, sch.todate) >= ?', [$fromDate])
            ->where('sch.entrymeetdate', '<=', $toDate)
            ->select([
                'ap.auditplanid',
                'ap.mandays',
                'ap.teamsize',
                'apt.auditplanteamid',
                'apt.teamname',
                'inst.instename',
                'dist.distename',
                'sch.auditscheduleid',
                'sch.fromdate',
                'sch.todate',
                'sch.entrymeetdate',
                'sch.proposedexitmeetdate',
                'sch.exitmeetdate',
                'schmem.leaveextention',
                DB::raw("(
                    SELECT STRING_AGG(du.username || ' (' || des.desigesname || ')', ', ')
                    FROM audit.auditplanteammember AS head_tm
                    JOIN audit.deptuserdetails AS du ON du.deptuserid = head_tm.userid
                    LEFT JOIN audit.mst_designation AS des ON des.desigcode = du.desigcode
                    WHERE head_tm.auditplanteamid = apt.auditplanteamid
                    AND head_tm.statusflag = 'Y'
                    AND head_tm.teamhead = 'Y'
                ) AS teamhead"),
                DB::raw("(
                    SELECT STRING_AGG(du.username || ' (' || des.desigesname || ')', ', ')
                    FROM audit.auditplanteammember AS member_tm
                    JOIN audit.deptuserdetails AS du ON du.deptuserid = member_tm.userid
                    LEFT JOIN audit.mst_designation AS des ON des.desigcode = du.desigcode
                    WHERE member_tm.auditplanteamid = apt.auditplanteamid
                    AND member_tm.statusflag = 'Y'
                    AND COALESCE(member_tm.teamhead, 'N') <> 'Y'
                ) AS teammembers"),
            ])
            ->distinct()
	            ->orderBy('sch.fromdate', 'asc')
	            ->orderBy('inst.instename', 'asc')
	            ->get();
	    }

	    public static function autoApproveMandaysForLeave($data)
	    {
	        try {
	            $result = DB::selectOne('CALL audit.auto_approve_mandays_for_leave(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
			                $data['leaveid'],
			                $data['userid'],
			                $data['deptcode'],
		                $data['fromdate'],
		                $data['todate'],
		                $data['createdbyroletypecode'] ?? null,
		                $data['transactiontypecode'],
			                $data['approveprocesscode'],
			                $data['remarks'],
		                $data['systemuserid'],
		                $data['sessionuserchargeid'],
		                null,
		                null,
		                null,
	            ]);

	            return [
	                'status' => $result->o_status ?? 'success',
	                'message' => $result->o_message ?? null,
	                'updated_count' => (int) ($result->o_updated_count ?? 0),
	            ];
	        } catch (\Exception $e) {
	            return [
	                'status' => 'error',
	                'message' => $e->getMessage(),
	                'updated_count' => 0,
	            ];
	        }
	    }
///leave apply end
	
	 public static function get_fromtodeptdet($fromdeptcode, $transtype)
	    {
        try {

            $data = DB::table((self::$mst_transferfromtodept_table) . ' as fromto')
                ->join(self::$department_table . ' as dept', 'dept.deptcode', '=', 'fromto.todeptcode')

                ->select('todeptcode', 'dept.deptelname', 'dept.depttlname')
                ->where('fromdeptcode', $fromdeptcode)
                ->where('transactioncode', $transtype)
                ->where('fromto.statusflag', 'Y')
                ->get();

            return $data;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching  details. Please contact the administrator.';
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }
    public static function getdeptbased_desig(string $deptcode, $instid, $for)
    {
        if (empty($deptcode)) {
            throw new InvalidArgumentException('Invalid arguments provided.');
        }

        $query = DB::table('audit.userchargedetails as uc')
            ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
            ->join('audit.mst_designation as des', 'des.desigcode', '=', 'ch.desigcode')
            ->select('ch.desigcode', 'des.desigelname', 'des.desigtlname')
            ->where('uc.statusflag', 'Y')
            ->where('ch.deptcode', $deptcode);

        if ($instid) {
            $query->where('ch.instmappingcode', $instid);
        }

        if ($for == 'desig') {
            $userData = session('charge');
            $session_roletypecode = $userData->roletypecode ?? null;
            if ($session_roletypecode)
                $query->where('des.roletypecode', $session_roletypecode);

            //       $querySql = $query->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);
            // exit;
        }

        $query->groupBy('ch.desigcode', 'des.desigelname', 'des.desigtlname');

        $result = $query->get();
        return $result;
    }

    public static function desigbaseduser($table, $paramcheck)
    {
        $distcode = $paramcheck['distcode'] ?? NULL;

        $timestamp = strtotime('first day of last month');
        $CurrYear = date('Y', $timestamp);
        $previousMonth = date('m', $timestamp);

        $result = DB::table(self::$userdet_table . ' as dp')
            ->join(self::$userchargedetail_table . ' as uc', 'uc.userid', '=', 'dp.deptuserid')
            ->join(self::$chargedetail_table . ' as c', 'c.chargeid', '=', 'uc.chargeid')
            ->join(self::$rolemapping_table . ' as r', 'r.rolemappingid', '=', 'c.rolemappingid')
            ->where('dp.deptcode', $paramcheck['deptcode'])
            ->where('dp.desigcode', $paramcheck['desigcode'])
            ->when(
                empty($paramcheck['distcode']) || $paramcheck['distcode'] === 'null',
                function ($query) {
                    return $query->whereNull('dp.distcode');
                },
                function ($query) use ($paramcheck) {
                    return $query->where('dp.distcode', $paramcheck['distcode']);
                }
            )
            ->when(
                isset($paramcheck['transtype']) && $paramcheck['transtype'] === 'superannuation',
                function ($query) use ($previousMonth, $CurrYear) {
                    return $query
                        ->whereYear('dp.dor', $CurrYear)
                        ->whereMonth('dp.dor', $previousMonth);
                }
            )
            ->where('uc.statusflag', 'Y')
            ->where('dp.statusflag', 'Y')
            ->where('dp.reservelist', 'Y')
            ->select('dp.username', 'dp.usertamilname', 'dp.deptuserid', 'dp.dor')
            ->distinct()
            ->get();

        return $result;
    }

    public static function regionbaseddist($table, $regioncode, $deptcode)
    {
        $del = DB::table($table . ' as dist')
            ->join(self::$auditor_instmapping_table . ' as inst', 'inst.distcode', '=', 'dist.distcode')
            ->where('inst.regioncode', $regioncode)
            ->where('inst.deptcode', $deptcode)
            ->select(
                'dist.distcode',
                'dist.distename',
                'dist.disttname',
            )
            ->where('dist.statusflag', 'Y')
            ->distinct()
            ->get();
        return $del;
    }

    public static function deptbasedregion($table, $deptcode)
    {
        $del = DB::table($table . ' as rtm')
            ->join(self::$auditor_instmapping_table . ' as inst', 'inst.deptcode', '=', 'rtm.deptcode')
            ->join(self::$region_table . ' as rt', 'rt.deptcode', '=', 'rtm.parentcode')
            ->join(self::$department_table . ' as md', 'md.deptcode', '=', 'rtm.parentcode')
            ->where('rtm.deptcode', $deptcode)
            ->where('inst.statusflag', 'Y')
            ->where('rt.statusflag', 'Y')
            // ->where('rtm.roletypecode', $request->roletypecode)
            ->select('md.deptcode', 'rt.regionename', 'rt.regiontname', 'rt.regioncode')
            ->distinct()
            ->get();

        return $del;
    }

    public static function deptbaseddesignation($table, $deptcode)
    {
        $del = DB::table($table . ' as desig')
            ->join(self::$department_table . ' as md', 'md.deptcode', '=', 'desig.deptcode')
            ->where('desig.deptcode', $deptcode)
            ->select('md.deptcode', 'desig.desigelname', 'desig.desigtlname', 'desig.desigcode', 'desig.orderid')
            ->where('desig.statusflag', 'Y')
            ->orderby('desig.orderid', 'ASC')
            ->distinct()
            ->get();
        return $del;
    }

    public static function getdata_regdistinst(string $deptcode, ?string $regioncode = null, ?string $distcode = null, string $getval, string $roletypecode)
    {
        $userData = session('charge');
        $session_regioncode = $userData->regioncode ?? null;
        $session_distcode = $userData->distcode ?? null;

        if (empty($deptcode) || empty($getval)) {
            throw new InvalidArgumentException('Invalid arguments provided.');
        }
        $query = DB::table(self::$auditor_instmapping_table . ' as instm')
            ->where('instm.statusflag', 'Y')
            ->where('instm.deptcode', $deptcode);
        switch ($getval) {
            case 'region':
                $query
                    ->join(self::$region_table . ' as re', 're.regioncode', '=', 'instm.regioncode')
                    ->select('instm.regioncode', 're.regionename', 're.regiontname')
                    ->distinct();
                if ($session_regioncode) {
                    $query->where('instm.regioncode', $session_regioncode);
                }
                $query->orderBy('re.regionename', 'ASC');
                break;
            case 'district':
                $query
                    ->join(self::$region_table . ' as re', 're.regioncode', '=', 'instm.regioncode')
                    ->join(self::$district_table . ' as d', 'd.distcode', '=', 'instm.distcode')
                    ->select('instm.distcode', 'd.distename', 'd.disttname')
                    ->distinct();
                $query->where('instm.regioncode', $regioncode);
                if ($session_distcode) {
                    $query->where('instm.distcode', $session_distcode);
                }
                $query->orderBy('d.distename', 'ASC');
                break;
            case 'institution':
                $query
                    ->join(self::$region_table . ' as re', 're.regioncode', '=', 'instm.regioncode')
                    ->select('instm.instmappingid', 'instm.instename', 'instm.instmappingcode', 'instm.insttname');
                $query->where('instm.regioncode', $regioncode);
                if ($roletypecode == '01') {
                    $query
                        ->join(self::$district_table . ' as d', 'd.distcode', '=', 'instm.distcode')
                        ->where('instm.distcode', $distcode);
                }
                $query->orderBy('instm.instename', 'ASC');
                break;

            default:
                throw new InvalidArgumentException("Invalid 'getval' provided. Allowed values are 'region', 'district', or 'institution'.");
        }
        return $query->get();
    }

    public static function getdataforToInst(string $deptcode, ?string $regioncode = null, ?string $distcode = null, string $getval, ?string $fromdistcode = null, string $roletypecode)
    {
        if (empty($deptcode) || empty($getval)) {
            throw new InvalidArgumentException('Invalid arguments provided.');
        }
        $query = DB::table(self::$auditor_instmapping_table . ' as instm')
            ->where('instm.statusflag', 'Y')
            ->where('instm.deptcode', $deptcode);
        switch ($getval) {
            case 'region':
                $query
                    ->join(self::$region_table . ' as re', 're.regioncode', '=', 'instm.regioncode')
                    ->select('instm.regioncode', 're.regionename', 're.regiontname')
                    ->distinct();
                $query->orderBy('re.regionename', 'ASC');
                break;
            case 'district':
                $query
                    ->join(self::$region_table . ' as re', 're.regioncode', '=', 'instm.regioncode')
                    ->join(self::$district_table . ' as d', 'd.distcode', '=', 'instm.distcode')
                    ->select('instm.distcode', 'd.distename', 'd.disttname')
                    ->distinct();
                $query->where('instm.regioncode', $regioncode);
                if (!empty($fromdistcode)) {
                    $query->whereNot('d.distcode', $fromdistcode);
                }
                $query->orderBy('d.distename', 'ASC');
                break;

            case 'institution':
                $query
                    ->join(self::$region_table . ' as re', 're.regioncode', '=', 'instm.regioncode')
                    ->select('instm.instmappingid', 'instm.instename', 'instm.instmappingcode', 'instm.insttname');
                $query->where('instm.regioncode', $regioncode);
                // }
                if ($roletypecode == '01') {
                    $query
                        ->join(self::$district_table . ' as d', 'd.distcode', '=', 'instm.distcode')
                        ->where('instm.distcode', $distcode);
                }
                $query->orderBy('instm.instename', 'ASC');
                break;

            default:
                throw new InvalidArgumentException("Invalid 'getval' provided. Allowed values are 'region', 'district', or 'institution'.");
        }
        return $query->get();
    }

    /* Other Transaction Form */

    public static function fetchothertransdel($othertransid)
    {
        $userData = session('user');
        $session_userid = $userData->userid;

        $chargeData = session('charge');
        $session_userchargeid = $chargeData->userchargeid;

        $query = DB::table(self::$othertrans_table . ' as other')
            ->join(self::$transtype_table . ' as transtype', 'transtype.transactiontypecode', '=', 'other.transactiontypecode')
            ->leftJoin(self::$auditor_instmapping_table . ' as instmap', 'instmap.instmappingcode', '=', 'other.toinstmappingcode')
            ->Join(self::$auditor_instmapping_table . ' as frominstmap', 'frominstmap.instmappingcode', '=', 'other.frominstmappingcode')
            ->join(self::$userdet_table . ' as user', 'user.deptuserid', '=', 'other.userid')
            ->join(self::$department_table . ' as dept', 'dept.deptcode', '=', 'frominstmap.deptcode')
            ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'user.desigcode')
            ->join(self::$region_table . ' as region', 'region.regioncode', '=', 'frominstmap.regioncode')
            ->join(self::$district_table . ' as dist', 'dist.distcode', '=', 'frominstmap.distcode')
            ->leftjoin(self::$department_table . ' as todept', 'todept.deptcode', '=', 'instmap.deptcode')
            ->leftJoin(self::$region_table . ' as toregion', 'toregion.regioncode', '=', 'instmap.regioncode')
            ->leftJoin(self::$district_table . ' as todist', 'todist.distcode', '=', 'instmap.distcode')
            ->join(self::$roletype . ' as roletype', 'roletype.roletypecode', '=', 'frominstmap.roletypecode')
            ->join('audit.fileuploaddetail as fu', 'fu.fileuploadid', '=', 'other.uploadid')
            ->select(
                'instmap.instmappingcode',
                'frominstmap.roletypecode',
                'instmap.instename',
                'instmap.insttname',
                'frominstmap.instmappingcode as frominstmapcode',
                'instmap.deptcode as dev_deptcode',
                'instmap.regioncode as div_region',
                'instmap.distcode as div_dist',
                DB::raw("
            CASE
                WHEN other.uploadid != 0 THEN CONCAT(fu.filename, '-', fu.filepath, '-', fu.filesize, '-', fu.fileuploadid)
                ELSE '-'
            END AS filedetails
        "),
                'other.userid',
                'frominstmap.instename as from_instename',
                'frominstmap.insttname as from_insttname',
                'frominstmap.deptcode',
                'frominstmap.regioncode',
                'frominstmap.distcode',
                'desig.desigcode',
                'desig.desigelname',
                'desig.desigtlname',
                'dist.distename',
                'dist.disttname',
                'region.regionename',
                'region.regiontname',
                'todist.distename as to_distename',
                'todist.disttname as to_disttname',
                'toregion.regionename as to_regionename',
                'toregion.regiontname as to_regiontname',
                'other.frominstmappingcode',
                'other.fromdesigcode',
                'other.todesigcode',
                'other.othertransid',
                'other.orderdate',
                'other.userid',
                'other.toinstmappingcode',
                'other.processcode',
                'other.orderno',
                'transtype.transactiontypelname',
                'transtype.transactiontypecode',
                'user.username',
                'user.deptuserid',
                'user.dob',
                'user.dor',
                'user.ifhrmsno',
                'user.usertamilname',
                'user.dor',
                'dept.deptelname',
                'dept.depttlname',
                'dept.depttsname',
                'dept.deptesname',
                'todept.deptelname as div_deptelname',
                'todept.depttlname as div_depttlname',
                'todept.depttsname as div_depttsname',
                'todept.deptesname as div_deptesname'
            )
            ->distinct();

        $query->when($othertransid, function ($query) use ($othertransid) {
            $query->where('other.othertransid', '=', $othertransid);
        });
        $query->where('other.createdbyuserchargeid', '=', $session_userchargeid);
        return $query->get();
    }

    // public static function insertorUpdateOthertrans($table, $data, $sessionuserid, $othertransid)
    // {
    //     if ($othertransid) {

    //         DB::table($table)->where('othertransid', $othertransid)->update($data);
    //         return DB::table($table)->where('othertransid', $othertransid)->first();
    //     } else {

    //         $othertransid = DB::table($table)->insertGetId($data, 'othertransid');
    //         return  $othertransid;
    //     }
    // }

    // public static function insertorUpdateOthertrans($data, $othertransid)
    // {
    //     try {

    //         if ($othertransid) {
    //             $updated = DB::table(self::$othertrans_table)->where('othertransid', $othertransid)->update($data);

    //             if ($updated) {
    //                 $
    //
    //  = DB::table(self::$othertrans_table)->where('othertransid', $othertransid)->first();
    //                 return ['status' => 'updated', 'data' => $record];
    //             } else {
    //                 return ['status' => 'failed', 'message' => 'No rows updated'];
    //             }
    //         } else {
    //             $insertedId = DB::table(self::$othertrans_table)->insertGetId($data, 'othertransid');

    //             if ($insertedId) {
    //                 return ['status' => 'inserted', 'othertransid' => $insertedId];
    //             } else {
    //                 return ['status' => 'failed', 'message' => 'Insertion failed'];
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         return ['status' => 'error', 'message' => $e->getMessage()];
    //     }
    // }

    // public static function insertorUpdateOthertrans($data, $othertransid)
    // {
    //     try {
    //         if ($othertransid) {
    //             // Attempt to update the record
    //             $updated = DB::table(self::$othertrans_table)->where('othertransid', $othertransid)->update($data);

    //             // Check if the update was successful (at least one row was affected)
    //             if ($updated > 0) {
    //                 // Fetch the updated record to return
    //                 $record = DB::table(self::$othertrans_table)->where('othertransid', $othertransid)->first();
    //                 return ['status' => 'updated', 'data' => $record];
    //             } else {
    //                 // No rows were updated (maybe no changes were made)
    //                 return ['status' => 'failed', 'message' => 'No rows updated (data might be the same)'];
    //             }
    //         } else {
    //             // Attempt to insert a new record
    //             $insertedId = DB::table(self::$othertrans_table)->insertGetId($data, 'othertransid');

    //             // Check if the insert was successful
    //             if ($insertedId) {
    //                 return ['status' => 'inserted', 'othertransid' => $insertedId];
    //             } else {
    //                 // Insertion failed
    //                 return ['status' => 'failed', 'message' => 'Insertion failed'];
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         // Handle any exceptions that occur during the process
    //         return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    //     }
    // }

    // Method to insert or update other transactions

    public static function insertorUpdateOthertrans($data, $othertransid, $formname)
    {
        try {
            if ($formname != 'processtable') {
                // Create a copy of $data without 'updatedbyuserchargeid' and 'updatedon'
                $copiedData = $data;
                unset($copiedData['updatedbyuserchargeid'], $copiedData['updatedon'], $copiedData['uploadid'], $copiedData['createdbyuserchargeid'], $copiedData['createdon']);
            }

            if ($othertransid) {
                if ($formname != 'processtable') {
                    $checkexists = DB::table(self::$othertrans_table)
                        ->where('orderno', '=', $copiedData['orderno'])
                        ->where('othertransid', '!=', $othertransid)
                        ->exists();

                    if ($checkexists) {
                        return [
                            'status' => 'failed',
                            'message' => 'A record with the order number already exists.'
                        ];
                    }

                    // Check if a record already exists with the same data (excluding the current record)
                    $checkexists = DB::table(self::$othertrans_table)
                        ->where($copiedData)
                        ->where('othertransid', '!=', $othertransid)
                        ->exists();

                    if ($checkexists) {
                        return [
                            'status' => 'failed',
                            'message' => 'A record with the same data already exists .'
                        ];
                    }
                }

                // Update the record
                $updatedRows = DB::table(self::$othertrans_table)
                    ->where('othertransid', $othertransid)
                    ->update($data);

                if ($updatedRows > 0) {
                    $record = DB::table(self::$othertrans_table)
                        ->where('othertransid', $othertransid)
                        ->first();
                    return [
                        'status' => 'updated',
                        'data' => $record
                    ];
                } else {
                    return [
                        'status' => 'failed',
                        'message' => 'No rows updated. Data might be identical or update failed.'
                    ];
                }
            } else {
                // Additional check for 'Super Annuation' if transactiontypecode = 02
                if (($data['transactiontypecode'] === '02') || ($data['transactiontypecode'] === '03') || ($data['transactiontypecode'] === '04')) {
                    $checkexists = DB::table(self::$othertrans_table)
                        ->where('transactiontypecode', '02')
                        ->where('userid', $data['userid'])
                        ->exists();

                    if ($checkexists) {
                        return [
                            'status' => 'failed',
                            'message' => 'User already exists for Super Annuation.'
                        ];
                    }
                    $checkexists = DB::table(self::$othertrans_table)
                        ->where('transactiontypecode', '03')
                        ->where('userid', $data['userid'])
                        ->exists();

                    if ($checkexists) {
                        return [
                            'status' => 'failed',
                            'message' => 'User already exists for VRS.'
                        ];
                    }
                    $checkexists = DB::table(self::$othertrans_table)
                        ->where('transactiontypecode', '04')
                        ->where('userid', $data['userid'])
                        ->exists();

                    if ($checkexists) {
                        return [
                            'status' => 'failed',
                            'message' => 'User already exists for Death.'
                        ];
                    }
                }

                if ($data['transactiontypecode'] === '02') {
                    $getuserdel = DB::table('audit.deptuserdetails')
                        ->where('deptuserid', $data['userid'])
                        ->first();

                    if ($getuserdel) {
                        $dor = $getuserdel->dor;

                        // Extract the month and year from DOR
                        $dormonth = date('m', strtotime($dor));
                        $doryear = date('Y', strtotime($dor));

                        // Get the current month and year
                        $currentmonth = date('m');
                        $currentyear = date('Y');

                        // Calculate previous month and its year
                        $prevmonth = date('m', strtotime('-1 month'));
                        $prevyear = date('Y', strtotime('-1 month'));

                        // Check if DOR is not in the current or previous month
                        if (!(($dormonth == $currentmonth && $doryear == $currentyear) ||
                                ($dormonth == $prevmonth && $doryear == $prevyear))) {
                            return [
                                'status' => 'failed',
                                'message' => 'DOR is not in the current or previous month.'
                            ];
                        }
                    } else {
                        return [
                            'status' => 'failed',
                            'message' => 'User details not found.'
                        ];
                        // Handle case where no record found
                    }
                }

                $checkexists = DB::table(self::$othertrans_table)
                    ->where('orderno', '=', $copiedData['orderno'])
                    ->exists();

                if ($checkexists) {
                    return [
                        'status' => 'failed',
                        'message' => 'A record with the order number already exists .'
                    ];
                }

                // Check if a record with the same data already exists (for insertion)
                $checkexists = DB::table(self::$othertrans_table)
                    ->where($copiedData)
                    ->exists();

                if ($checkexists) {
                    return [
                        'status' => 'failed',
                        'message' => 'A record with the same data already exists.'
                    ];
                }

                // Insert a new record
                $insertedId = DB::table(self::$othertrans_table)
                    ->insertGetId($data, 'othertransid');

                if ($insertedId) {
                    return [
                        'status' => 'inserted',
                        'othertransid' => $insertedId
                    ];
                } else {
                    return [
                        'status' => 'failed',
                        'message' => 'Insertion failed due to unknown error.'
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error in insertorUpdateOthertrans: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }

    /* Other Transaction Form */

    public static function forwardtonextlevel($transactiontypecode, $userid, $action)
    {
        $userData = session('charge');
        $session_rolemappingid = $userData->rolemappingid ?? null;
        $session_deptcode = $userData->deptcode ?? null;
        $session_regioncode = $userData->regioncode ?? null;
        $session_distcode = $userData->distcode ?? null;
        $leaveTransactionCode = View::shared('Leavetransactiontypecode');

        $data = DB::table('audit.userchargedetails as uc')
            ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
            ->join('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'uc.userid')
            ->join('audit.transactionflow as tf', 'tf.torolemappingid', '=', 'ch.rolemappingid')
            ->select('uc.userid', 'uc.chargeid', 'ch.chargedescription', 'tf.flowflag', 'uc.userchargeid', 'dp.username', 'dp.email')
            ->where('ch.statusflag', 'Y')
            ->where('uc.statusflag', 'Y')
            ->where('ch.deptcode', $session_deptcode)
            ->where('ch.regioncode', $session_regioncode)
            ->where('ch.distcode', $session_distcode)
            ->when(
                $action === 'first',
                function ($query) {
                    $query->where('tf.flowflag', 'S');
                },
                function ($query) {
                    $query->where('tf.flowflag', '<>', 'S');
                }
            )
            ->where('tf.transactiontypecode', $transactiontypecode)
            ->whereExists(function ($query) use ($transactiontypecode, $userid, $session_rolemappingid, $leaveTransactionCode) {
                $query
                    ->select(DB::raw(1))
                    ->from('audit.userchargedetails as uc2')
                    ->join('audit.chargedetails as ch2', 'ch2.chargeid', '=', 'uc2.chargeid')
                    ->where('ch2.statusflag', 'Y')
                    ->where('uc2.statusflag', 'Y')
                    ->whereColumn('ch.deptcode', 'ch2.deptcode')
                    ->whereColumn('ch.regioncode', 'ch2.regioncode')
                    ->whereColumn('ch.rolemappingid', 'tf.torolemappingid')
                    ->whereColumn('tf.fromrolemappingid', 'ch2.rolemappingid');

                // Handle nullable distcode conditionally
                $query->where(function ($subQuery) {
                    $subQuery
                        ->whereNull('ch2.distcode')
                        ->orWhereColumn('ch.distcode', 'ch2.distcode');
                });

                // User vs role logic depending on transaction type
                if ($transactiontypecode == $leaveTransactionCode) {
                    $query->where('uc2.userid', $userid);
                } else {
                    $query->where('ch2.rolemappingid', $session_rolemappingid);
                }
            })
            ->get();

        return $data;
    }

    // Method to insert or update transaction details
    public static function insertupdate_transdet($data, $where)
    {
        try {
            // Check if a matching record already exists in transactiondetail
            $record = DB::table(self::$transactiondetail_table)->where($where)->first();

            if ($record) {
                // If it exists, try to update in audit.transactiondetail using leaveid
                // if (isset($record->leaveid)) {
                // Update the transaction status to 'I' in the audit.transactiondetail table
                $updatedRows = DB::table('audit.transactiondetail')
                    ->where($where)
                    ->update($data);

                // Check how many rows were updated
                if ($updatedRows > 0) {
                    // echo 'ho';
                    return [
                        'status' => 'updated',
                        'message' => "{$updatedRows} row(s) updated in audit.transactiondetail",
                        // 'leaveid' => $record->processcode
                    ];
                } else {
                    return [
                        'status' => 'no_change',
                        'message' => 'No rows updated in audit.transactiondetail',
                        // 'leaveid' => $record->processcode
                    ];
                }  // } else {
                //     return [
                //         'status' => 'error',
                //         'message' => 'leaveid not found in existing record'
                //     ];
                // }
            } else {
                // If no record found, insert a new one into transactiondetail table
                $inserted = DB::table(self::$transactiondetail_table)->insertGetId($data, 'transactiondetailid');

                if ($inserted) {
                    return [
                        'status' => 'inserted',
                        'message' => 'New transaction detail inserted successfully'
                    ];
                } else {
                    return [
                        'status' => 'failed',
                        'message' => 'Failed to insert new transaction detail'
                    ];
                }
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // Method to insert or update history transaction details
    // public static function insert_historyTransDetail($data, $where)
    // {
    //     try {
    //         // Check if a matching record already exists in historytransaction
    //         $record = DB::table(self::$historytransaction_table)->where($where)->first();

    //         $updatedRows    =   1;
    //         if ($record) {

    //             // If it exists, try to update in audit.transactiondetail using leaveid
    //             if (isset($record->leaveid)) {
    //                 // Update the transaction status to 'I' in the audit.transactiondetail table
    //                 $updatedRows = DB::table(self::$historytransaction_table)
    //                     ->where($where)
    //                     ->update(['transstatus' => 'I']);

    //                 // Check how many rows were updated

    //             }
    //         }
    //         echo 'if';
    //         if ($updatedRows > 0) {
    //             echo 'hi';

    //             print_r($data);

    //             echo self::$historytransaction_table;

    //             // $inserted = DB::table(self::$historytransaction_table)->insert($data);
    //             $insertedId = DB::table(self::$historytransaction_table)->insertGetId($data);

    //             if ($inserted) {
    //                 echo'else';
    //                 return [
    //                     'status' => 'inserted',
    //                     'message' => 'New transaction detail inserted successfully'
    //                 ];
    //             } else {
    //                 echo'else';
    //                 return [
    //                     'status' => 'failed',
    //                     'message' => 'Failed to insert new transaction detail'
    //                 ];
    //             }
    //         }

    //     } catch (\Exception $e) {
    //         return [
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ];
    //     }
    // }

    public static function insert_historyTransDetail($data, $where)
    {
        try {
            // Check if a matching record already exists in historytransaction
            $record = DB::table(self::$historytransaction_table)->where($where)->first();

            // Default value for $updatedRows
            $updatedRows = 0;

            if ($record) {
                // If a record exists, try to update in the audit.transactiondetail table using leaveid
                if (isset($record->leaveid)) {
                    // Update the transaction status to 'I' in the audit.transactiondetail table
                    $updatedRows = DB::table(self::$historytransaction_table)
                        ->where($where)
                        ->update(['transstatus' => 'I']);
                }
            }

            // If no matching record exists, proceed with inserting a new record
            // Insert the data into the historytransaction table and get the inserted ID
            $insertedId = DB::table(self::$historytransaction_table)->insertGetId($data, 'historytransactionsid');

            if ($insertedId) {
                return [
                    'status' => 'inserted',
                    'message' => 'New transaction detail inserted successfully',
                    'inserted_id' => $insertedId  // Returning the inserted ID
                ];
            } else {
                return [
                    'status' => 'failed',
                    'message' => 'Failed to insert new transaction detail'
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // $results = DB::table('audit.transactiondetail as ht')
    //     ->leftJoin('audit.ind_leavedetail as ld', 'ld.leaveid', '=', 'ht.leaveid')
    //     ->leftJoin('audit.othertransactions as other', 'other.othertransid', '=', 'ht.othertransid')
    //     ->join('audit.deptuserdetails as du', function($join) {
    //         $join->on('du.deptuserid', '=', 'ld.userid')
    //              ->orOn('du.deptuserid', '=', 'other.userid');
    //     })
    //     ->join('audit.mst_designation as md', 'md.desigcode', '=', 'du.desigcode')
    //     ->join('audit.mst_district as dist', 'dist.distcode', '=', 'du.distcode')
    //     ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'du.deptcode')
    //     ->leftJoin('audit.mst_leavetype as mlt', 'mlt.leavetypeid', '=', 'ld.leavetypecode')
    //     ->join('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'ht.transactiontypecode')
    //     ->join('audit.userchargedetails as uc', function($join) {
    //         $join->on('uc.userchargeid', '=', 'ht.updatedbyuserchargeid')
    //              ->where('uc.statusflag', '=', 'Y');
    //     })
    //     ->join('audit.chargedetails as fbch', 'fbch.chargeid', '=', 'uc.chargeid')
    //     ->join('audit.deptuserdetails as fbdu', 'uc.userid', '=', 'fbdu.deptuserid')
    //     ->join('audit.mst_designation as fbde', 'fbdu.desigcode', '=', 'fbde.desigcode')
    //     ->select(
    //         'other.userid as othertrans_userid',
    //         'other.othertransid',
    //         'other.orderdate as othertrans_date',
    //         'other.inoutstatus',
    //         'ld.fromdate',
    //         'ld.todate',
    //         'ld.leaveid',
    //         'ld.userid as leavedetail_userid',
    //         'ld.reason',
    //         'mlt.leavetypeelname',
    //         DB::raw('COALESCE(other.userid, ld.userid) as final_userid'),
    //         'ht.transactiondetailid',
    //         'ht.transactiontypecode',
    //         'ht.forwardedtouserchargeid as historyfwduc',
    //         'tt.transactiontypelname',
    //         DB::raw('COALESCE(other.processcode, ld.processcode) as processcode'),
    //         DB::raw('CASE
    //                     WHEN ld.leaveid IS NOT NULL THEN (SELECT processelname FROM audit.mst_process WHERE processcode = ld.processcode LIMIT 1)
    //                     WHEN other.othertransid IS NOT NULL THEN (SELECT processelname FROM audit.mst_process WHERE processcode = other.processcode LIMIT 1)
    //                  END as processelname'),
    //         'md.desigesname',
    //         'du.username',
    //         'du.ifhrmsno',
    //         'dist.distename',
    //         'dept.deptesname',
    //         'fbdu.username as fbdu_username',
    //         'fbde.desigesname as fbde_desigesname',
    //         'fbch.chargedescription',
    //         'ht.updatedon'
    //     )
    //     ->where('ht.forwardedtouserchargeid', '=', $userchargeid);
    //     // ->get();

    public static function fetchTransactionFlowData($forwardedToUserChargeId)
    {
        $chargedel = session('charge');
        $userdel = session('user');

        $session_rolemappingid = $chargedel->rolemappingid;

        $session_userid = $userdel->userid;

        // use Illuminate\Support\Facades\DB;

        $query = DB::table('audit.transactiondetail as ht')
            ->select([
                'other.userid as othertrans_userid',
                'other.othertransid',
                'other.orderdate as othertrans_date',
                'other.inoutstatus',
                'ld.fromdate',
                'ld.todate',
                'ld.leaveid',
                'ld.userid as leavedetail_userid',
                'ld.reason',
                'ld.processcode as leave_processcode',
                'mlt.leavetypeelname',
                DB::raw('COALESCE(other.userid, ld.userid) AS trans_userid'),
                'ht.transactiondetailid',
                'ht.transactiontypecode',
                'ht.forwardedtouserchargeid as historyfwduc',
                'tt.transactiontypelname',
                'md.desigesname',
                'md.desigelname',
                'du.username',
                'du.dob',
                'du.dor',
                'du.ifhrmsno',
                // 'dist.distename',
                // 'dept.deptesname',
                'fbdu.username as fbdu_username',
                'fbde.desigesname as fbde_desigesname',
                'fbch.chargedescription',
                'ht.updatedon',
                'eu.userchargeid as forwardto',
                DB::raw('COALESCE(other.processcode, ld.processcode,me.processcode,lind.processcode) AS processcode'),
                DB::raw('COALESCE(proc_ld.processelname, proc_ot.processelname,proc_me.processelname,proc_lin.processelname) AS processelname'),
                DB::raw("
                    STRING_AGG(
                        TRIM(BOTH ' - ' FROM
                            CONCAT(
                                dept.deptesname,
                                CASE
                                    WHEN re.regionename IS NOT NULL THEN ' - ' || re.regionename
                                    ELSE ''
                                END,
                                CASE
                                    WHEN dist.distename IS NOT NULL THEN ' - ' || dist.distename
                                    ELSE ''
                                END,
                                \t\t\t\t' (' || ch.chargedescription || ')'
                            )
                        ),
                        ', '
                    ) AS chargedel
                "),
                'ht.mandaysextensionid',
                'ins.instename',
                'me.extramandays',
                'lind.leaveinid'
            ])
            ->leftJoin('audit.ind_leavedetail as ld', 'ld.leaveid', '=', 'ht.leaveid')
            ->leftJoin('audit.ind_leavein_detail as lind', 'lind.leaveinid', '=', 'ht.leaveinid')
            ->leftJoin('audit.othertransactions as other', 'other.othertransid', '=', 'ht.othertransid')
            ->leftJoin(self::$mandaysextension_table . ' as me', 'me.mandaysextensionid', '=', 'ht.mandaysextensionid')
            ->leftJoin('audit.inst_auditschedule as aschid', 'aschid.auditscheduleid', '=', 'me.auditscheduleid')
            ->leftJoin('audit.auditplan  as ap', 'ap.auditplanid', '=', 'aschid.auditplanid')
            ->leftJoin('audit.mst_institution  as ins', 'ins.instid', '=', 'ap.instid')
            ->leftJoin('audit.mst_process as proc_ot', function ($join) {
                $join->on('proc_ot.processcode', '=', DB::raw("COALESCE(NULLIF(ld.processcode, ''), other.processcode)"));
            })
            ->leftJoin('audit.mst_process as proc_ld', 'proc_ld.processcode', '=', 'ld.processcode')
            ->leftJoin('audit.mst_process as proc_me', 'proc_me.processcode', '=', 'me.processcode')
            ->leftJoin('audit.mst_process as proc_lin', 'proc_lin.processcode', '=', 'lind.processcode')
            ->leftJoin('audit.deptuserdetails as du', function ($join) {
                $join->on('du.deptuserid', '=', DB::raw('COALESCE(ld.userid, other.userid,lind.userid)'));
            })
            ->leftJoin('audit.mst_designation as md', 'md.desigcode', '=', 'du.desigcode')
            // ->leftJoin('audit.mst_district as dist', 'dist.distcode', '=', 'du.distcode')
            // ->leftJoin('audit.mst_dept as dept', 'dept.deptcode', '=', 'du.deptcode')
            ->leftJoin('audit.userchargedetails as ucu', 'ucu.userid', '=', 'du.deptuserid')
            ->leftJoin('audit.chargedetails as ch', 'ch.chargeid', '=', 'ucu.chargeid')
            ->leftJoin('audit.mst_dept as dept', 'dept.deptcode', '=', 'ch.deptcode')
            ->leftJoin('audit.mst_region as re', 're.regioncode', '=', 'ch.regioncode')
            ->leftJoin('audit.mst_district as dist', 'dist.distcode', '=', 'ch.distcode')
            ->leftJoin('audit.mst_leavetype as mlt', 'mlt.leavetypecode', '=', 'ld.leavetypecode')
            ->leftJoin('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'ht.transactiontypecode')
            ->leftJoin('audit.userchargedetails as uc', function ($join) {
                $join
                    ->on('uc.userchargeid', '=', 'ht.updatedbyuserchargeid')
                    ->where('uc.statusflag', 'Y');
            })
            ->leftJoin('audit.chargedetails as fbch', 'fbch.chargeid', '=', 'uc.chargeid')
            ->leftJoin('audit.deptuserdetails as fbdu', 'fbdu.deptuserid', '=', 'uc.userid')
            ->leftJoin('audit.mst_designation as fbde', 'fbde.desigcode', '=', 'fbdu.desigcode')
            // ->leftJoin(DB::raw('(SELECT * FROM audit.userchargedetails AS uc
            //                         INNER JOIN audit.chargedetails AS ch ON ch.chargeid = uc.chargeid
            //                         INNER JOIN audit.transactionflow AS tf ON tf.torolemappingid = ch.rolemappingid
            //                         WHERE ch.statusflag = \'Y\' AND uc.statusflag = \'Y\' AND tf.fromteamhead = \'Y\'
            //                         AND EXISTS (SELECT 1 FROM audit.userchargedetails AS uc2
            //                                     INNER JOIN audit.chargedetails AS ch2 ON ch2.chargeid = uc2.chargeid
            //                                     WHERE (tf.transactiontypecode = \'01\' AND uc2.userid = {$session_userid})
            //                                     OR (tf.transactiontypecode <> \'01\' AND ch2.rolemappingid = {$session_rolemappingid})
            //                                     AND ch2.statusflag = \'Y\' AND uc2.statusflag = \'Y\'
            //                                     AND ch.deptcode = ch2.deptcode
            //                                     AND ch.regioncode = ch2.regioncode
            //                                     AND ch.distcode = ch2.distcode
            //                                     AND ch.rolemappingid = tf.torolemappingid
            //                                     AND tf.fromrolemappingid = ch2.rolemappingid)) AS eu'), function($join) {
            //                                         $join->on(DB::raw('TRUE'), '=', DB::raw('TRUE'));
            // })
            ->leftJoin(DB::raw("
                (
                    SELECT * FROM audit.userchargedetails AS uc
                    INNER JOIN audit.chargedetails AS ch ON ch.chargeid = uc.chargeid
                    INNER JOIN audit.transactionflow AS tf ON tf.torolemappingid = ch.rolemappingid
                    WHERE ch.statusflag = 'Y' AND uc.statusflag = 'Y' AND tf.fromteamhead = 'Y'
                    AND EXISTS (
                        SELECT 1 FROM audit.userchargedetails AS uc2
                        INNER JOIN audit.chargedetails AS ch2 ON ch2.chargeid = uc2.chargeid
                        WHERE (
                            (tf.transactiontypecode = '01' AND uc2.userid = {$session_userid})
                            OR (tf.transactiontypecode <> '01' AND ch2.rolemappingid = {$session_rolemappingid})
                        )
                        AND ch2.statusflag = 'Y' AND uc2.statusflag = 'Y'
                        AND ch.deptcode = ch2.deptcode
                        AND ch.regioncode = ch2.regioncode
                        AND ch.distcode = ch2.distcode
                        AND ch.rolemappingid = tf.torolemappingid
                        AND tf.fromrolemappingid = ch2.rolemappingid
                    )
                ) AS eu
            "), function ($join) {
                $join->on(DB::raw('TRUE'), '=', DB::raw('TRUE'));
            })
            ->groupBy(
                'other.userid',
                'other.othertransid',
                'other.orderdate',
                'other.inoutstatus',
                'ld.fromdate',
                'ld.todate',
                'ld.leaveid',
                'ld.userid',
                'ld.reason',
                'mlt.leavetypeelname',
                'ht.transactiondetailid',
                'ht.transactiontypecode',
                'ht.forwardedtouserchargeid',

                'tt.transactiontypelname',
                'md.desigesname',
                'md.desigelname',
                'du.username',
                'du.dob',
                'du.dor',
                'du.ifhrmsno',
                'fbdu.username',
                'fbde.desigesname',
                'fbch.chargedescription',
                'ht.updatedon',
                'eu.userchargeid',
                DB::raw('COALESCE(other.processcode, ld.processcode,me.processcode,lind.processcode)'),
                DB::raw('COALESCE(proc_ld.processelname, proc_ot.processelname, proc_me.processelname,proc_lin.processelname) '),
                'ins.instename',
                'me.extramandays',
                'lind.leaveinid',
            )
            ->where('ht.forwardedtouserchargeid', $forwardedToUserChargeId)
            ->whereNot('ld.processcode', 'P');

        // $querySql = $query->toSql();
        // $bindings = $query->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );
        // print_r($finalQuery);

        // exit;

        $query = $query->get();
        return $query;
    }

    public static function getting_pendingdel($transid, $transtypecode, $userid, $roleactioncode)
    {
        $chargedel = session('charge');
        $deptcode = $chargedel->deptcode;

        $query = DB::table(self::$department_table)
            ->where('statusflag', 'Y')
            ->where('deptcode', $deptcode)
            ->get();
        $planmappingid = $query[0]->planmappingid;

        if ($roleactioncode == view::shared('AuditorRoleactioncode')) {
            if ($transtypecode == View::shared('Leavetransactiontypecode')) {
                $currentscheduledetails = DB::table('audit.auditplanteammember as aptm')
                    ->join('audit.auditplan as ap', 'ap.auditteamid', '=', 'aptm.auditplanteamid')
                    ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
                    ->join('audit.inst_auditschedule as ins', 'ins.auditplanid', '=', 'ap.auditplanid')
                    ->join('audit.inst_schteammember as insm', function ($join) {
                        $join
                            ->on('ins.auditscheduleid', '=', 'insm.auditscheduleid')
                            ->on('aptm.userid', '=', 'insm.userid');
                    })
                    ->join('audit.ind_leavedetail as ot', 'ot.userid', '=', 'aptm.userid')
                    ->where('aptm.statusflag', 'Y')
                    ->whereIn('ins.statusflag', ['Y', 'F'])
                    ->where('insm.statusflag', 'Y')
                    ->whereNull('ins.exitmeetdate')
                    ->where('ot.leaveid', $transid)
                    ->where('ot.transactiontypecode', $transtypecode)
                    ->where(function ($query) {
                        $query
                            ->where(function ($q) {
                                $q
                                    ->whereNull('ins.entrymeetdate')
                                    ->whereColumn('ins.todate', '>=', 'ot.fromdate')
                                    ->whereColumn('ins.fromdate', '<=', 'ot.todate');
                            })
                            ->orWhere(function ($q) {
                                $q
                                    ->whereNotNull('ins.entrymeetdate')
                                    ->whereColumn('ins.proposedexitmeetdate', '>=', 'ot.fromdate')
                                    ->whereColumn('ins.entrymeetdate', '<=', 'ot.todate');
                            });
                        // ->orWhere(function ($q) {
                        //     $q->whereNotNull('ins.entrymeetdate')
                        //         ->whereNull('ins.exitmeetdate');
                        //     // ->whereColumn('ins.todate', '>=', 'ot.fromdate')
                        //     // ->whereColumn('ins.entrymeetdate', '<=', 'ot.todate');
                        // });
                    })
                    ->select([
                        'aptm.planteammemberid',
                        'ap.auditplanid',
                        'inst.instename',
                        'ins.workallocationflag',
                        'ins.entrymeetdate',
                        'ins.exitmeetdate',
                        'insm.schteammemberid',
                        'ins.auditscheduleid',
                        'insm.auditteamhead',
                        // Subquery for slipcount
                        DB::raw("(
\t\t\tSELECT COUNT(*)
\t\t\tFROM audit.trans_auditslip
\t\t\tWHERE auditscheduleid = insm.auditscheduleid
\t\t\tAND createdby = insm.userid
\t\t) as slipcount"),
                        // Subquery for membercount
                        DB::raw("(
\t\t\tSELECT COUNT(*)
\t\t\tFROM audit.inst_schteammember
\t\t\tWHERE auditscheduleid = insm.auditscheduleid
\t\t\tAND statusflag = 'Y'
\t\t\tAND auditteamhead = 'N'
\t\t) as membercount")
                    ])
                    ->get();

                $planteammberdata = collect();
            } else {
                $currentscheduledetails = DB::table('audit.auditplanteammember as aptm')
                    ->select([
                        'aptm.planteammemberid',
                        'ap.auditplanid',
                        'inst.instename',
                        'ins.workallocationflag',
                        'ins.entrymeetdate',
                        'ins.exitmeetdate',
                        'insm.schteammemberid',
                        'ins.auditscheduleid',
                        'insm.auditteamhead',
                        DB::raw('(select count(*) from audit.trans_auditslip where auditscheduleid = insm.auditscheduleid and createdby = insm.userid) as slipcount'),
                        DB::raw("(select count(*) from audit.inst_schteammember where auditscheduleid = insm.auditscheduleid and statusflag='Y' and auditteamhead='N') as membercount")
                    ])
                    ->join('audit.auditplan as ap', 'ap.auditteamid', '=', 'aptm.auditplanteamid')
                    ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
                    ->join('audit.inst_auditschedule as ins', 'ins.auditplanid', '=', 'ap.auditplanid')
                    ->Join('audit.othertransactions as ot', 'ot.userid', '=', 'aptm.userid')
                    ->join('audit.inst_schteammember as insm', function ($join) {
                        $join
                            ->on('ins.auditscheduleid', '=', 'insm.auditscheduleid')
                            ->on('aptm.userid', '=', 'insm.userid');
                    })
                    // ->where('aptm.userid', $userId)
                    ->where('aptm.statusflag', 'Y')
                    ->where('ap.planmappingid', $planmappingid)
                    ->whereIn('ins.statusflag', ['Y', 'F'])
                    ->where('insm.statusflag', 'Y')
                    ->whereNull('ins.exitmeetdate')
                    ->where('ot.othertransid', $transid)
                    ->where('ot.transactiontypecode', $transtypecode)
                    ->get();

                $planteammberdata = DB::table('audit.auditplanteammember as aptm')
                    ->join('audit.auditplan as ap', 'ap.auditteamid', '=', 'aptm.auditplanteamid')
                    ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ap.instid')
                    ->join('audit.othertransactions as ot', 'ot.userid', '=', 'aptm.userid')
                    ->where('ot.othertransid', $transid)
                    ->where('aptm.statusflag', 'Y')
                    ->where('ot.transactiontypecode', $transtypecode)
                    ->where('ap.planmappingid', $planmappingid)
                    ->whereNotIn('ap.auditplanid', function ($query) use ($transid, $transtypecode, $planmappingid) {
                        $query
                            ->select('ap.auditplanid')
                            ->from('audit.auditplanteammember as aptm')
                            ->join('audit.auditplan as ap', 'ap.auditteamid', '=', 'aptm.auditplanteamid')
                            ->join('audit.inst_auditschedule as ins', 'ins.auditplanid', '=', 'ap.auditplanid')
                            ->join('audit.othertransactions as ot', 'ot.userid', '=', 'aptm.userid')
                            ->join('audit.inst_schteammember as insm', function ($join) {
                                $join
                                    ->on('ins.auditscheduleid', '=', 'insm.auditscheduleid')
                                    ->on('aptm.userid', '=', 'insm.userid');
                            })
                            ->where('aptm.statusflag', 'Y')
                            ->where('ap.planmappingid', $planmappingid)
                            ->whereIn('ins.statusflag', ['Y', 'F'])
                            ->where('insm.statusflag', 'Y')
                            // ->whereNull('ins.exitmeetdate')
                            ->where('ot.othertransid', $transid)
                            ->where('ot.transactiontypecode', $transtypecode);
                    })
                    ->select('ins.instename', 'ap.auditplanid')
                    ->get();
            }
            return $get_pendingdetails = array(
                'schedulependings' => $currentscheduledetails,
                'planpenings' => $planteammberdata
            );
        } else if ($roleactioncode == view::shared('AdminplanviewRoleactioncode')) {
        } else if ($roleactioncode == view::shared('AdminentryRoleactioncode')) {
        } else {
        }
    }

    public static function fetch_usedrdata_transfer($id, $transtypecode, $inoutstatus, $roleactioncode)
    {
        if ($transtypecode == View::shared('Leavetransactiontypecode')) {
            $othertrans = DB::table('audit.ind_leavedetail as other')
                ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'other.userid')
                ->join('audit.userchargedetails as uc', function ($join) {
                    $join
                        ->on('uc.userid', '=', 'other.userid')
                        ->where('uc.statusflag', '=', 'Y');
                })
                ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
                ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'cd.deptcode')
                ->join('audit.mst_region as re', 're.regioncode', '=', 'cd.regioncode')
                ->join('audit.mst_district as dist', 'dist.distcode', '=', 'cd.distcode')
                ->join('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'other.transactiontypecode')
                ->selectRaw("
                    other.userid,
                    du.username,
                    du.ifhrmsno,
                    du.dob,
                    du.dor,
                    other.fromdate,
                    other.todate,
                     other.reason,
                    tt.transactiontypelname,
                    other.transactiontypecode,
                    'O' as inoutstatus,
                    other.leaveid,
                    STRING_AGG(
                        TRIM(BOTH ' - ' FROM CONCAT(
                            dept.deptesname,
                            CASE WHEN re.regionename IS NOT NULL THEN ' - ' || re.regionename ELSE '' END,
                            CASE WHEN dist.distename IS NOT NULL THEN ' - ' || dist.distename ELSE '' END,
                            ' (' || cd.chargedescription || ')'
                        )), ', '
                    ) AS chargedel,
                    STRING_AGG(DISTINCT cd.regioncode, ',') AS regioncodes,
                    STRING_AGG(DISTINCT cd.deptcode, ',') AS deptcodes
                ")
                ->where('other.leaveid', '=', $id)
                ->where('other.statusflag', '=', 'Y')
                ->groupBy([
                    'other.userid',
                    'du.username',
                    'du.ifhrmsno',
                    'du.dob',
                    'du.dor',
                    'other.fromdate',
                    'other.todate',
                    'other.reason',
                    'tt.transactiontypelname',
                    'other.transactiontypecode',
                    'other.leaveid'
                ])
                ->get();

            // $querySql = $data->toSql();
            // $bindings = $data->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);

            // exit;

            // print_r( $data);

            // exit;
        } else {
            if ($inoutstatus == View::shared('Outflag')) {
                $othertrans = DB::table('audit.othertransactions as other')
                    ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'other.userid')
                    ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'other.userid')
                    ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
                    ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'cd.deptcode')
                    ->join('audit.mst_region as re', 're.regioncode', '=', 'cd.regioncode')
                    ->join('audit.mst_district as dist', 'dist.distcode', '=', 'cd.distcode')
                    ->join('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'other.transactiontypecode')
                    ->join('audit.fileuploaddetail as fu', 'fu.fileuploadid', '=', 'other.uploadid')
                    ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'cd.rolemappingid')
                    ->leftJoin(self::$auditor_instmapping_table . ' as instmap', 'instmap.instmappingcode', '=', 'other.toinstmappingcode')
                    ->leftJoin(self::$department_table . ' as todept', 'todept.deptcode', '=', 'instmap.deptcode')
                    ->leftJoin(self::$region_table . ' as toregion', 'toregion.regioncode', '=', 'instmap.regioncode')
                    ->leftJoin(self::$district_table . ' as todist', 'todist.distcode', '=', 'instmap.distcode')
                    ->select([
                        'other.userid',
                        'du.username',
                        'du.ifhrmsno',
                        'du.dob',
                        'du.dor',
                        'other.orderdate',
                        'other.orderno',
                        'tt.transactiontypelname',
                        'other.transactiontypecode',
                        'other.inoutstatus',
                        'other.othertransid',
                        'todist.distename as to_distename',
                        'todist.disttname as to_disttname',
                        'toregion.regionename as to_regionename',
                        'toregion.regiontname as to_regiontname',
                        'todept.deptelname as div_deptelname',
                        'todept.depttlname as div_depttlname',
                        'todept.depttsname as div_depttsname',
                        'todept.deptesname as div_deptesname',
                        'instmap.instename',
                        'instmap.insttname',
                        // STRING_AGG for detailed charge info
                        DB::raw("
\t\t\t\t\tSTRING_AGG(
\t\t\t\t\t\tTRIM(BOTH ' - ' FROM
\t\t\t\t\t\t\tCONCAT(
\t\t\t\t\t\t\t\tdept.deptesname,
\t\t\t\t\t\t\t\tCASE
\t\t\t\t\t\t\t\t\tWHEN re.regionename IS NOT NULL THEN ' - ' || re.regionename
\t\t\t\t\t\t\t\t\tELSE ''
\t\t\t\t\t\t\t\tEND,
\t\t\t\t\t\t\t\tCASE
\t\t\t\t\t\t\t\t\tWHEN dist.distename IS NOT NULL THEN ' - ' || dist.distename
\t\t\t\t\t\t\t\t\tELSE ''
\t\t\t\t\t\t\t\tEND,
\t\t\t\t\t\t\t\t' (' || cd.chargedescription || ')'
\t\t\t\t\t\t\t)
\t\t\t\t\t\t),
\t\t\t\t\t\t', '
\t\t\t\t\t) AS chargedel
\t\t\t\t"),
                        // STRING_AGG for region codes
                        DB::raw("STRING_AGG(cd.regioncode, ',') AS regioncodes"),
                        DB::raw("STRING_AGG(cd.deptcode, ',') AS deptcodes"),
                        // Conditional file details
                        DB::raw("
\t\t\t\t\tCASE
\t\t\t\t\t\tWHEN other.uploadid != 0
\t\t\t\t\t\tTHEN CONCAT(fu.filename, '-', fu.filepath, '-', fu.filesize, '-', fu.fileuploadid)
\t\t\t\t\t\tELSE '-'
\t\t\t\t\tEND AS filedetails
\t\t\t\t")
                    ])
                    ->where('other.othertransid', '=', $id)
                    ->where('other.statusflag', '=', 'Y')
                    ->where('uc.statusflag', '=', 'Y')
                    ->where('rm.roleactioncode', '=', '04')
                    ->groupBy(
                        'other.userid',
                        'du.username',
                        'du.ifhrmsno',
                        'du.dob',
                        'du.dor',
                        'other.orderdate',
                        'other.orderno',
                        'other.uploadid',
                        'tt.transactiontypelname',
                        'other.transactiontypecode',
                        'other.inoutstatus',
                        'fu.filename',
                        'fu.filepath',
                        'fu.filesize',
                        'fu.fileuploadid',
                        'other.othertransid',
                        'todist.distename',
                        'todist.disttname',
                        'toregion.regionename',
                        'toregion.regiontname',
                        'todept.deptelname',
                        'todept.depttlname',
                        'todept.depttsname',
                        'todept.deptesname',
                        'instmap.instename',
                        'instmap.insttname',
                    )
                    ->get();
            } else {
                $othertrans = DB::table('audit.othertransactions as other')
                    ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'other.userid')
                    ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'other.userid')
                    ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
                    ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'cd.deptcode')
                    ->join('audit.mst_region as re', 're.regioncode', '=', 'cd.regioncode')
                    ->join('audit.mst_district as dist', 'dist.distcode', '=', 'cd.distcode')
                    ->join('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'other.transactiontypecode')
                    ->join('audit.fileuploaddetail as fu', 'fu.fileuploadid', '=', 'other.uploadid')
                    ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'cd.rolemappingid')
                    ->leftJoin(self::$auditor_instmapping_table . ' as frominstmap', 'frominstmap.instmappingcode', '=', 'other.frominstmappingcode')
                    ->leftJoin(self::$department_table . ' as fromdept', 'fromdept.deptcode', '=', 'frominstmap.deptcode')
                    ->leftJoin(self::$region_table . ' as fromregion', 'fromregion.regioncode', '=', 'frominstmap.regioncode')
                    ->leftJoin(self::$district_table . ' as fromdist', 'dist.distcode', '=', 'frominstmap.distcode')
                    ->select([
                        'other.userid',
                        'du.username',
                        'du.ifhrmsno',
                        'du.dob',
                        'du.dor',
                        'other.orderdate',
                        'other.orderno',
                        'tt.transactiontypelname',
                        'other.transactiontypecode',
                        'other.inoutstatus',
                        'other.othertransid',
                        'fromdist.distename',
                        'fromdist.disttname',
                        'fromregion.regionename',
                        'fromregion.regiontname',
                        'fromdept.deptelname',
                        'fromdept.depttlname',
                        'fromdept.depttsname',
                        'fromdept.deptesname',
                        'frominstmap.instename',
                        // STRING_AGG for detailed charge info
                        DB::raw("
\t\t\t\t\tSTRING_AGG(
\t\t\t\t\t\tTRIM(BOTH ' - ' FROM
\t\t\t\t\t\t\tCONCAT(
\t\t\t\t\t\t\t\tdept.deptesname,
\t\t\t\t\t\t\t\tCASE
\t\t\t\t\t\t\t\t\tWHEN re.regionename IS NOT NULL THEN ' - ' || re.regionename
\t\t\t\t\t\t\t\t\tELSE ''
\t\t\t\t\t\t\t\tEND,
\t\t\t\t\t\t\t\tCASE
\t\t\t\t\t\t\t\t\tWHEN dist.distename IS NOT NULL THEN ' - ' || dist.distename
\t\t\t\t\t\t\t\t\tELSE ''
\t\t\t\t\t\t\t\tEND,
\t\t\t\t\t\t\t\t' (' || cd.chargedescription || ')'
\t\t\t\t\t\t\t)
\t\t\t\t\t\t),
\t\t\t\t\t\t', '
\t\t\t\t\t) AS chargedel
\t\t\t\t"),
                        // STRING_AGG for region codes
                        DB::raw("STRING_AGG(cd.regioncode, ',') AS regioncodes"),
                        DB::raw("STRING_AGG(cd.deptcode, ',') AS deptcodes"),
                        // Conditional file details
                        DB::raw("
\t\t\t\t\tCASE
\t\t\t\t\t\tWHEN other.uploadid != 0
\t\t\t\t\t\tTHEN CONCAT(fu.filename, '-', fu.filepath, '-', fu.filesize, '-', fu.fileuploadid)
\t\t\t\t\t\tELSE '-'
\t\t\t\t\tEND AS filedetails
\t\t\t\t")
                    ])
                    ->where('other.othertransid', '=', $id)
                    ->where('other.statusflag', '=', 'Y')
                    ->where('uc.statusflag', '=', 'Y')
                    ->where('rm.roleactioncode', '=', '04')
                    ->groupBy(
                        'other.userid',
                        'du.username',
                        'du.ifhrmsno',
                        'du.dob',
                        'du.dor',
                        'other.orderdate',
                        'other.orderno',
                        'other.uploadid',
                        'tt.transactiontypelname',
                        'other.transactiontypecode',
                        'other.inoutstatus',
                        'fu.filename',
                        'fu.filepath',
                        'fu.filesize',
                        'fu.fileuploadid',
                        'other.othertransid',
                        'fromdist.disttname',
                        'fromdist.distename',
                        'fromregion.regionename',
                        'fromregion.regiontname',
                        'fromdept.deptelname',
                        'fromdept.depttlname',
                        'fromdept.depttsname',
                        'fromdept.deptesname',
                        'frominstmap.instename',
                    )
                    ->get();
            }
        }

        if ($roleactioncode == view::shared('auditor_roleactioncode')) {
            $todeptcode = $othertrans[0]->deptcodes;
            $toregioncode = $othertrans[0]->regioncodes;

            $touserdata = DB::table(self::$userdet_table . ' as ut')
                ->join(self::$district_table . ' as dt', 'ut.distcode', '=', 'dt.distcode')
                ->join(self::$userchargedetail_table . ' as uc', 'uc.userid', '=', 'ut.deptuserid')
                ->join(self::$chargedetail_table . ' as c', 'c.chargeid', '=', 'uc.chargeid')
                ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
                ->join(self::$designation_table . ' as d', 'd.desigcode', '=', 'ut.desigcode')
                ->where('c.regioncode', $toregioncode)
                ->where('ut.deptcode', $todeptcode)
                ->where('ut.reservelist', 'N')
                ->where('uc.statusflag', 'Y')
                ->where('ut.statusflag', 'Y')
                ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))
                ->whereNotIn('ut.deptuserid', function ($query) {
                    $query
                        ->select('userid')
                        ->from('audit.othertransactions')
                        ->where('processcode', 'F')
                        ->where('inoutstatus', 'I');
                })
                ->select('ut.deptuserid', 'd.desigesname', 'ut.username', 'ut.usertamilname', 'dt.distcode', 'dt.distename')
                ->orderBy('d.desigesname', 'asc')
                ->orderBy('dt.distename', 'asc')
                ->orderBy('ut.username', 'asc')
                ->orderBy('ut.usertamilname', 'asc')
                ->get();
        } else if ($roleactioncode == view::shared('AdminplanviewRoleactioncode')) {
            $touserdata = DB::table(self::$userdet_table . ' as ut')
                ->join(self::$district_table . ' as dt', 'ut.distcode', '=', 'dt.distcode')
                ->join(self::$userchargedetail_table . ' as uc', 'uc.userid', '=', 'ut.deptuserid')
                ->join(self::$chargedetail_table . ' as c', 'c.chargeid', '=', 'uc.chargeid')
                ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
                ->join(self::$designation_table . ' as d', 'd.desigcode', '=', 'ut.desigcode')
                ->where('uc.statusflag', 'Y')
                ->where('ut.statusflag', 'Y')
                ->where('rm.roleactioncode', View::shared('AdminplanviewRoleactioncode'))
                ->select('ut.deptuserid', 'd.desigesname', 'ut.username', 'ut.usertamilname', 'dt.distcode', 'dt.distename')
                ->orderBy('d.desigesname', 'asc')
                ->orderBy('ut.username', 'asc')
                ->orderBy('ut.usertamilname', 'asc')
                ->get();
        } else if ($roleactioncode == view::shared('AdminentryRoleactioncode')) {
        } else {
        }

        $data = [
            'othertransdet' => $othertrans,
            'touser' => $touserdata,
        ];

        return $data;
    }

    public static function fetch_otherteamhead($userid)
    {
        $chargedel = session('charge');
        $deptcode = $chargedel->deptcode;
        $distcode = $chargedel->distcode;

        $results = DB::table('audit.inst_auditschedule as ina')
            ->join('audit.inst_schteammember as mem', function ($join) {
                $join
                    ->on('ina.auditscheduleid', '=', 'mem.auditscheduleid')
                    ->where('mem.auditteamhead', '=', 'Y')
                    ->where('mem.statusflag', '=', 'Y');
            })
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ina.auditplanid')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->join('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'mem.userid')
            ->join('audit.mst_designation as des', 'des.desigcode', '=', 'dp.desigcode')
            ->where('inst.distcode', $distcode)
            ->where('inst.deptcode', $deptcode)
            ->where('mem.userid', '!=', $userid)
            ->where('ina.statusflag', 'F')
            ->whereNotNull('ina.entrymeetdate')
            ->whereNull('ina.exitmeetdate')
            ->select('mem.userid', 'dp.username', 'des.desigesname')
            ->distinct()
            ->get();

        return $results;
    }

    public static function getothermembers($scheduleid)
    {
        $query = DB::table('audit.inst_auditschedule as ina')
            ->join('audit.inst_schteammember as mem', function ($join) {
                $join
                    ->on('ina.auditscheduleid', '=', 'mem.auditscheduleid')
                    ->where('mem.auditteamhead', '=', 'N')
                    ->where('mem.statusflag', '=', 'Y');
            })
            ->join('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'mem.userid')
            ->join('audit.mst_designation as des', 'des.desigcode', '=', 'dp.desigcode');

        if (is_array($scheduleid)) {
            $query->whereIn('ina.auditscheduleid', $scheduleid);
        } else {
            $query->where('ina.auditscheduleid', $scheduleid);
        }

        $query = $query
            ->select('mem.userid', 'dp.username', 'des.desigesname')
            ->distinct()
            ->get();

        return $query;
    }

    public static function getworkalloactionbasedonSchedulemember($auditscheduleid, $schememberid = null)
    {
        try {
            $query = DB::table('audit.trans_workallocation as wa')
                ->join('audit.map_allocation_objection as mao', 'mao.mapallocationobjectionid', '=', 'wa.workallocationtypeid')
                ->join('audit.mst_mainobjection as mo', 'mo.mainobjectionid', '=', 'mao.mainobjectionid')
                ->join('audit.mst_majorworkallocationtype as mw', 'mw.majorworkallocationtypeid', '=', 'mao.majorworkallocationtypeid')
                ->join('audit.inst_schteammember as itm', 'itm.schteammemberid', '=', 'wa.schteammemberid')
                ->join('audit.group as gro', 'gro.groupid', '=', 'mao.groupid')
                ->where('itm.auditscheduleid', $auditscheduleid)
                ->where('mo.statusflag', 'Y');

            if (!empty($schememberid)) {
                $query->where('itm.schteammemberid', $schememberid);
            }

            $query = $query
                ->select(
                    'mw.majorworkallocationtypeename',
                    'mw.majorworkallocationtypetname',
                    'gro.groupename',
                    'gro.grouptname'
                )
                ->distinct()
                ->orderBy('mw.majorworkallocationtypeename', 'asc')
                ->get();

            return $query;

            // $querySql = $query->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );

            // print_r($finalQuery);
            // exit;
        } catch (\Exception $e) {
            throw new \Exception('Error in getworkalloactionbasedonSchedulemember: ' . $e->getMessage());
        }
    }

    public static function getslipdetailsbasedon_schedulemember($auditscheduleid, $schememberid)
    {
        try {
            $results = DB::table('audit.trans_auditslip as asl')
                ->join('audit.mst_mainobjection as mo', 'mo.mainobjectionid', '=', 'asl.mainobjectionid')
                ->join('audit.mst_subobjection as sob', 'sob.subobjectionid', '=', 'asl.subobjectionid')
                ->join('audit.inst_schteammember as itm', function ($join) {
                    $join
                        ->on('itm.userid', '=', 'asl.createdby')
                        ->on('itm.auditscheduleid', '=', 'asl.auditscheduleid');
                })
                ->join('audit.mst_process as p', 'p.processcode', '=', 'asl.processcode')
                ->join('audit.mst_severity as s', 's.severitycode', '=', 'asl.severitycode')
                ->where('asl.auditscheduleid', '=', $auditscheduleid);
            if (!empty($schememberid)) {
                $results->where('itm.schteammemberid', $schememberid);
            }

            return $results
                ->select(
                    'mo.objectionename',
                    'mo.objectiontname',
                    'mo.mainobjectionid',
                    's.severityelname',
                    's.severitytlname',
                    'p.processelname',
                    'p.processtlname',
                    'sob.subobjectiontname',
                    'sob.subobjectionename',
                    'asl.mainslipnumber'
                )  // You can customize the select statement to return specific columns
                ->get();
            return $results;  // Returns an array of user IDs
        } catch (\Exception $e) {
            // Throw a custom exception with the message from the model
            throw new \Exception($e->getMessage());
        }
    }

    // public static function createleave_insertupdate($data, $leave_id, $table, $userid,$for)
    // {

    //     try {
    //         $query = DB::table($table);

    //         if ($leave_id) {
    //             $query->where('leaveid', '!=', $leave_id);
    //         }

    //         if($for == 'form')
    //         {
    //             $leaveexists = (clone $query)
    //             ->where(function ($q) use ($data, $userid) {
    //                 $q->where(function ($subQuery) use ($data) {
    //                     // Overlapping condition
    //                     $subQuery->where('fromdate', '<=', $data['todate'])
    //                         ->where('todate', '>=', $data['fromdate']);
    //                 })
    //                     ->where('userid', '=', $userid)
    //                     ->orWhere(function ($subQuery) use ($data) {
    //                         // Special case where both dates are the same
    //                         $subQuery->where('fromdate', '=', $data['fromdate'])
    //                             ->where('todate', '=', $data['todate']);
    //                     });
    //             })
    //             ->exists();
    //             if ($leaveexists) {
    //                 // return 'excess';
    //                 // return response()->json(['error' => 'Leave for the particular date was already applied.'], 400);
    //                 throw new \Exception('Leave for the particular date was already applied.');
    //             }
    //         }

    //         if ($leave_id) {
    //             DB::table($table)->where('leaveid', $leave_id)->update($data);
    //             return DB::table($table)->where('leaveid', $leave_id)->first();
    //         } else {
    //             $insert_leavedet = DB::table($table)->insertGetId($data, 'leaveid');
    //         }

    //         if ($insert_leavedet) {
    //             return DB::table($table)->where('leaveid', $insert_leavedet)->first();
    //         } else {
    //             return response()->json(['success' => false, 'message' => 'Failed to insert leave details. Please try again.'], 500);
    //         }
    //     } catch (\Exception $e) {
    //         throw new \Exception($e->getMessage());
    //     }
    // }
///leave apply start
	    public static function createleave_insertupdate($data, $leave_id, $table, $userid, $for)
    {
        try {
            $query = DB::table($table);

            if ($leave_id) {
                $query->where('leaveid', '!=', $leave_id);
            }

            if ($for === 'form') {
                $leaveExists = (clone $query)
                    ->where(function ($q) use ($data, $userid) {
                        $q
                            ->where(function ($subQuery) use ($data) {
                                // Overlapping condition
                                $subQuery
                                    ->where('fromdate', '<=', $data['todate'])
                                    ->where('todate', '>=', $data['fromdate']);
                            })
                            ->where('userid', '=', $userid)
                            ->where('processcode', '<>', 'I');
                        // ->orWhere(function ($subQuery) use ($data) {
                        // Special case where both dates are the same
                        //   $subQuery->where('fromdate', '=', $data['fromdate'])
                        //   ->where('todate', '=', $data['todate']);
                        //  });
                    })
                    ->exists();

                if ($leaveExists) {
                    return ['status' => 'failed', 'message' => 'Leave for the particular date was already applied.'];
                }
            }

            if ($leave_id) {
                $updatedRows = DB::table($table)->where('leaveid', $leave_id)->update($data);

                if ($updatedRows > 0) {
                    $record = DB::table($table)->where('leaveid', $leave_id)->first();
                    return ['status' => 'updated', 'data' => $record];
                } else {
                    return ['status' => 'failed', 'message' => 'No rows updated.'];
                }
            } else {
                $insertedId = DB::table($table)->insertGetId($data, 'leaveid');

                if ($insertedId) {
                    $record = DB::table($table)->where('leaveid', $insertedId)->first();
                    return ['status' => 'inserted', 'data' => $record];
                } else {
                    return ['status' => 'failed', 'message' => 'Insertion failed.'];
                }
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public static function getholidaydates()
    {
        $holidaydates = DB::table('audit.mst_holiday')
            ->where('statusflag', 'Y')
            ->select('holiday_date')
            ->orderBy('updatedon', 'desc')
            ->get();
        return $holidaydates;
    }

    public static function fetchalldata($userid)
    {
        $all_leavedet = DB::table('audit.ind_leavedetail')
            ->join('audit.mst_leavetype as mlt', 'mlt.leavetypecode', '=', 'audit.ind_leavedetail.leavetypecode')
            ->where('audit.ind_leavedetail.userid', $userid)
            ->orderBy('audit.ind_leavedetail.updatedon', 'desc')
            ->get();
        return $all_leavedet;
    }

    public static function fetchsingle_data($leaveid, $table)
    {
        $single_leavedet = DB::table($table)
            ->where('leaveid', $leaveid)
            ->orderBy('audit.ind_leavedetail.updatedon', 'desc')
            ->get();
	        return $single_leavedet;
	    }
///leave apply end
	
	    public static function getinstitutiondel($auditscheduleid)
    {
        try {
            $query1 = DB::table('audit.inst_auditschedule as ins')
                ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ins.auditplanid')
                ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
                ->join('audit.inst_schteammember as insm', 'insm.auditscheduleid', '=', 'ins.auditscheduleid')
                ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'insm.userid')
                ->join('audit.mst_designation as desig', 'du.desigcode', '=', 'desig.desigcode')
                ->select(
                    'ins.auditscheduleid',
                    'inst.instename',
                    DB::raw("
                        STRING_AGG(
                            CASE WHEN insm.auditteamhead = 'Y'
                                 THEN du.username || ' (' || desig.desigesname || ')'
                                 ELSE NULL END, ', '
                        ) AS teamhead
                    "),
                    DB::raw("
                        STRING_AGG(
                            CASE WHEN insm.auditteamhead = 'N'
                                 THEN du.username || ' (' || desig.desigesname || ')'
                                 ELSE NULL END, ', '
                        ) AS memberdel
                    "),
                    'ins.entrymeetdate',
                    'ins.exitmeetdate',
                    'inst.mandays',
                    'ins.fromdate',
                    'ins.todate'
                )
                ->where('ins.auditscheduleid', $auditscheduleid)
                ->where('ins.statusflag', 'F')
                ->where('insm.statusflag', 'Y')
                ->groupBy(
                    'ins.auditscheduleid',
                    'inst.instename',
                    'ins.entrymeetdate',
                    'ins.exitmeetdate',
                    'inst.mandays',
                    'ins.fromdate',
                    'ins.todate'
                );

            return $query1->get();  // Returns an array of user IDs
        } catch (\Exception $e) {
            // Throw a custom exception with the message from the model
            throw new \Exception($e->getMessage());
        }
    }

    public static function insert_datatransfer($request, $sessionuserid, $sessionuserchargeid)
    {
        try {
            DB::beginTransaction();

            $request = $request->all();  // Ensure input is an array
            $inoutstatus = $request['inoutstatus'] ?? null;

            // Initialize values
            $auditscheduleids = '{}';
            $datatypes = '{}';
            $transferusers = '{}';
            $plandatatypecode = null;
            $plantransuser = null;

            $auditplanids = '{}';

            // Process array parameters only if not in 'I' status
            if ($inoutstatus !== 'I') {
                if (isset($request['auditscheduleid']) && is_array($request['auditscheduleid']) && count($request['auditscheduleid']) > 0) {
                    $auditscheduleids = '{' . implode(',', $request['auditscheduleid']) . '}';
                }

                if (isset($request['datatransfercode']) && is_array($request['datatransfercode']) && count($request['datatransfercode']) > 0) {
                    $quoted = array_map(fn($v) => '"' . $v . '"', $request['datatransfercode']);
                    $datatypes = '{' . implode(',', $quoted) . '}';
                }

                // if (isset($request['transuser']) && is_array($request['transuser']) && count($request['transuser']) > 0) {
                //     $transuserFormatted = array_map(
                //         fn($v) => ($v === null || $v === '') ? 'NULL' : $v,
                //         $request['transuser']
                //     );
                //     $transferusers = '{' . implode(',', $transuserFormatted) . '}';

                // }

                if (isset($request['selectedUserIds']) && is_array($request['selectedUserIds']) && count($request['selectedUserIds']) > 0) {
                    $transuserFormatted = array_map(
                        fn($v) => ($v === null || $v === '') ? 'NULL' : $v,
                        $request['selectedUserIds']
                    );
                    $transferusers = '{' . implode(',', $transuserFormatted) . '}';
                }

                if (isset($request['plandatatransfercode'][0])) {
                    $plandatatypecode = $request['plandatatransfercode'][0] ?? null;
                }

                if (isset($request['plantransuser'][0])) {
                    $plantransuser = $request['plantransuser'][0] ?? null;
                }

                if (isset($request['auditplanid']) && is_array($request['auditplanid']) && count($request['auditplanid']) > 0) {
                    $auditplanids = '{' . implode(',', $request['auditplanid']) . '}';
                }
            }

            // echo  $request['userid'];
            // echo "<br>";
            // echo  $auditscheduleids;
            // echo "<br>";
            // echo  $datatypes;
            // echo "<br>";
            // echo  $transferusers;
            // echo "<br>";
            // echo  $plandatatypecode;
            // echo "<br>";
            // echo  $plantransuser;
            // echo "<br>";
            // echo  $auditplanids;
            // echo "<br>";
            // echo  $sessionuserid;
            // echo "<br>";
            // echo  $sessionuserchargeid;
            // echo "<br>";
            // echo  $request['transactiontypecode'];
            // echo "<br>";
            // echo  $request['othertransid'];
            // echo "<br>";

            // exit;

            // echo  $request['userid'];
            // echo "<br>";
            // echo  $auditscheduleids;
            // echo "<br>";
            // echo  $datatypes;
            // echo "<br>";
            // echo  $transferusers;
            // echo "<br>";

            // echo  $sessionuserid;
            // echo "<br>";
            // echo  $sessionuserchargeid;
            // echo "<br>";
            // echo  $request['transactiontypecode'];
            // echo "<br>";
            // echo  $request['othertransid'];
            // echo "<br>";
            // exit;

            // Handle transaction type and call respective stored procedure
            if ($request['transactiontypecode'] === View::shared('Leavetransactiontypecode')) {
                $fromdate = DateTime::createFromFormat('d-m-Y', date('d-m-Y', strtotime($request['fromdate'])));

                $today = new DateTime(date('Y-m-d'));

                if ($fromdate->format('Y-m-d') <= $today->format('Y-m-d')) {
                    DB::statement('CALL audit.leave_management(?, ?, ?, ?, ?, ?, ?, ?)', [
                        $request['userid'],
                        $auditscheduleids,
                        $datatypes,
                        $transferusers,
                        $sessionuserid,
                        $sessionuserchargeid,
                        $request['transactiontypecode'],
                        $request['othertransid']
                    ]);
                } else {
                    DB::statement('CALL audit.leave_management_process(?, ?, ?, ?, ?, ?, ?, ?)', [
                        $request['userid'],
                        $auditscheduleids,
                        $datatypes,
                        $transferusers,
                        $sessionuserid,
                        $sessionuserchargeid,
                        $request['transactiontypecode'],
                        $request['othertransid']
                    ]);
                }
            } else {
                DB::statement('CALL audit.process_transfer(?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)', [
                    $request['userid'],
                    $auditscheduleids,
                    $datatypes,
                    $transferusers,
                    $plandatatypecode,
                    $plantransuser,
                    $auditplanids,
                    $sessionuserid,
                    $sessionuserchargeid,
                    $request['transactiontypecode'],
                    $request['othertransid'],
                    $inoutstatus
                ]);
            }

            DB::commit();
            return ['status' => 'success'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public static function getapproveddetails($deptcode, $regioncode, $distcode)
    {
        // First query
        $firstQuery = DB::table('audit.othertransactions as ot')
            ->select([
                'ot.orderdate',
                'ot.orderno',
                'fu.filename',
                'fu.filepath',
                'fdp.deptesname as fromdeptename',
                'fre.regionename as fromregionename',
                'fdist.distename as fromdistename',
                'finsm.instename as frominstename',
                'tdp.deptesname as todeptename',
                'tre.regionename as toregionename',
                'tdist.distename as todistename',
                'tinsm.instename as toinstename',
                'us.username',
                'us.ifhrmsno',
                'us.dob',
                'us.dor',
                'tt.transactiontypelname',
                DB::raw('NULL as fromdate'),
                DB::raw('NULL as todate'),
                'abdu.username as approvedby_username',
                'abch.chargedescription',
                'abdp.deptesname',
                'abre.regionename',
                'abdist.distename',
                'abdes.desigesname',
                'p.processelname',
                'abdu.desigcode',
                'abuc.userchargeid',
                'tt.transactiontypecode',
                'td.updatedon',
                'ot.othertransid as id',
                DB::raw('(
                    SELECT 1
                    FROM audit.logothertrans_scheduledel
                    WHERE othertransid = ot.othertransid
                    LIMIT 1
                ) AS schedulechange'),
                DB::raw('(
                    SELECT 1
                    FROM audit.logothertrans_plandel
                    WHERE othertransid = ot.othertransid
                    LIMIT 1
                ) AS planchange')
            ])
            ->join('audit.deptuserdetails as us', 'us.deptuserid', '=', 'ot.userid')
            ->join('audit.transactiondetail as td', 'td.othertransid', '=', 'ot.othertransid')
            ->join('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'ot.transactiontypecode')
            ->join('audit.auditor_instmapping as finsm', 'finsm.instmappingcode', '=', 'ot.frominstmappingcode')
            ->join('audit.mst_dept as fdp', 'fdp.deptcode', '=', 'finsm.deptcode')
            ->join('audit.mst_region as fre', 'fre.regioncode', '=', 'finsm.regioncode')
            ->join('audit.mst_district as fdist', 'fdist.distcode', '=', 'finsm.distcode')
            ->join('audit.fileuploaddetail as fu', 'fu.fileuploadid', '=', 'ot.uploadid')
            ->leftJoin('audit.auditor_instmapping as tinsm', 'tinsm.instmappingcode', '=', 'ot.toinstmappingcode')
            ->leftJoin('audit.mst_dept as tdp', 'tdp.deptcode', '=', 'tinsm.deptcode')
            ->leftJoin('audit.mst_region as tre', 'tre.regioncode', '=', 'tinsm.regioncode')
            ->leftJoin('audit.mst_district as tdist', 'tdist.distcode', '=', 'tinsm.distcode')
            ->join('audit.userchargedetails as abuc', 'abuc.userchargeid', '=', 'td.updatedbyuserchargeid')
            ->join('audit.chargedetails as abch', 'abch.chargeid', '=', 'abuc.chargeid')
            ->join('audit.deptuserdetails as abdu', 'abdu.deptuserid', '=', 'abuc.userid')
            ->join('audit.mst_dept as abdp', 'abdp.deptcode', '=', 'abch.deptcode')
            ->join('audit.mst_region as abre', 'abre.regioncode', '=', 'abch.regioncode')
            ->join('audit.mst_district as abdist', 'abdist.distcode', '=', 'abch.distcode')
            ->join('audit.mst_designation as abdes', 'abdes.desigcode', '=', 'abdu.desigcode')
            ->join('audit.mst_process as p', 'p.processcode', '=', 'ot.processcode')
            ->where('p.processcode', '=', 'P');

        if ($deptcode) {
            $firstQuery = $firstQuery->where(function ($query) use ($deptcode) {
                $query
                    ->where('finsm.deptcode', $deptcode)
                    ->orWhere('tinsm.deptcode', $deptcode);
            });
        }

        if ($regioncode) {
            $firstQuery = $firstQuery->where(function ($query) use ($regioncode) {
                $query
                    ->where('finsm.regioncode', $regioncode)
                    ->orWhere('tinsm.regioncode', $regioncode);
            });
        }

        if ($distcode) {
            $firstQuery = $firstQuery->where(function ($query) use ($distcode) {
                $query
                    ->where('finsm.distcode', $distcode)
                    ->orWhere('tinsm.distcode', $distcode);
            });
        }

        // Second query
        $secondQuery = DB::table('audit.ind_leavedetail as ot')
            ->select([
                DB::raw('NULL as orderdate'),
                DB::raw('NULL as orderno'),
                DB::raw("'' as filename"),
                DB::raw("'' as filepath"),
                DB::raw("'' as fromdeptename"),
                DB::raw("'' as fromregionename"),
                DB::raw("'' as fromdistename"),
                DB::raw("'' as frominstename"),
                DB::raw("'' as todeptename"),
                DB::raw("'' as toregionename"),
                DB::raw("'' as todistename"),
                DB::raw("'' as toinstename"),
                'us.username',
                'us.ifhrmsno',
                'us.dob',
                'us.dor',
                'tt.transactiontypelname',
                'ot.fromdate',
                'ot.todate',
                'abdu.username as approvedby_username',
                'abch.chargedescription',
                'abdp.deptesname',
                'abre.regionename',
                'abdist.distename',
                'abdes.desigesname',
                'p.processelname',
                'abdu.desigcode',
                'abuc.userchargeid',
                'tt.transactiontypecode',
                'td.updatedon',
                'ot.leaveid as id',
                DB::raw('(
                    SELECT 1
                    FROM audit.logothertrans_scheduledel le
                    WHERE le.leaveid = ot.leaveid
                    LIMIT 1
                ) AS schedulechange'),
                DB::raw('0 AS planchange')
            ])
            ->join('audit.deptuserdetails as us', 'us.deptuserid', '=', 'ot.userid')
            ->join('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'ot.transactiontypecode')
            ->join('audit.transactiondetail as td', 'td.leaveid', '=', 'ot.leaveid')
            ->join('audit.userchargedetails as abuc', 'abuc.userchargeid', '=', 'td.updatedbyuserchargeid')
            ->join('audit.chargedetails as abch', 'abch.chargeid', '=', 'abuc.chargeid')
            ->join('audit.deptuserdetails as abdu', 'abdu.deptuserid', '=', 'abuc.userid')
            ->join('audit.mst_dept as abdp', 'abdp.deptcode', '=', 'abch.deptcode')
            ->join('audit.mst_region as abre', 'abre.regioncode', '=', 'abch.regioncode')
            ->join('audit.mst_district as abdist', 'abdist.distcode', '=', 'abch.distcode')
            ->join('audit.mst_designation as abdes', 'abdes.desigcode', '=', 'abdu.desigcode')
            ->join('audit.mst_process as p', 'p.processcode', '=', 'ot.processcode')
            ->where('p.processcode', '=', 'P');

        if ($deptcode)
            $secondQuery = $secondQuery->where('us.deptcode', $deptcode);
        if ($distcode)
            $secondQuery = $secondQuery->where('us.distcode', $distcode);

        // Union the queries
        $finalQuery = $firstQuery->union($secondQuery);

        // // Execute the query and get results
        $results = $finalQuery->get();

        // $querySql = $finalQuery->toSql();
        // $bindings = $finalQuery->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );

        // print_r($finalQuery);

        return $results;
    }

    public static function getdatatransferdel($id, $transactiontypecode)
    {
        // Query 1
        $query1 = DB::table('audit.logothertrans_plandel as pldel')
            ->join(DB::raw("LATERAL (
            SELECT jsonb_array_elements_text(pldel.auditplanid -> 'plan_ids')::int AS plan_id
        ) as plan_ids_split"), DB::raw('TRUE'), DB::raw('TRUE'))
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'plan_ids_split.plan_id')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->leftJoin('audit.deptuserdetails as fdp', 'fdp.deptuserid', '=', 'pldel.fromuserid')
            ->leftJoin('audit.deptuserdetails as tdp', 'tdp.deptuserid', '=', 'pldel.touserid')
            ->leftJoin('audit.mst_designation as fdes', 'fdes.desigcode', '=', 'fdp.desigcode')
            ->leftJoin('audit.mst_designation as tdes', 'tdes.desigcode', '=', 'tdp.desigcode')
            ->select(
                'ap.instid',
                'inst.instename',
                'pldel.datatransfertypecode',
                DB::raw("fdp.username || ' ( ' || fdes.desigesname || ' )' as from_user_details"),
                DB::raw("tdp.username || ' ( ' || tdes.desigesname || ' )' as to_user_details")
            )
            ->where('pldel.othertransid', $id)
            // ->where('pldel.datatransfertypecode', $transactiontypecode)
            ->get();

        // $querySql = $query1->toSql();
        // $bindings = $query1->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );

        // print_r($finalQuery);

        // Query 2
        $query2 = DB::table('audit.historytrans_workallocation as wa')
            // LEFT JOIN on othertransid with alias 'lsc'
            ->leftJoin('audit.logothertrans_scheduledel as lsc', 'lsc.othertransid', '=', 'wa.othertransid')
            // LEFT JOIN on leaveid with DIFFERENT alias 'lscl' (important fix!)
            ->leftJoin('audit.logothertrans_scheduledel as lscl', 'lscl.leaveid', '=', 'wa.leaveid')
            // Inner joins for work allocation and objection mapping
            ->join('audit.map_allocation_objection as mao', 'mao.mapallocationobjectionid', '=', 'wa.workallocationtypeid')
            ->join('audit.mst_mainobjection as mo', 'mo.mainobjectionid', '=', 'mao.mainobjectionid')
            ->join('audit.mst_majorworkallocationtype as mw', 'mw.majorworkallocationtypeid', '=', 'mao.majorworkallocationtypeid')
            // Team member (from)
            ->join('audit.inst_schteammember as itm', 'itm.schteammemberid', '=', 'wa.schteammemberid')
            ->join('audit.deptuserdetails as fdp', function ($join) {
                $join
                    ->on('fdp.deptuserid', '=', 'itm.userid')
                    ->on('wa.auditscheduleid', '=', 'itm.auditscheduleid');
            })
            // Team member (to)
            ->join('audit.inst_schteammember as titm', 'titm.schteammemberid', '=', 'wa.toschteammemberid')
            ->join('audit.deptuserdetails as tdp', function ($join) {
                $join
                    ->on('tdp.deptuserid', '=', 'titm.userid')
                    ->on('wa.auditscheduleid', '=', 'titm.auditscheduleid');
            })
            // Group info
            ->join('audit.group as gro', 'gro.groupid', '=', 'mao.groupid')
            // Only active objections
            ->where('mo.statusflag', '=', 'Y');

        // Conditional filter based on transaction type
        if ($transactiontypecode == '01') {
            $query2 = $query2->where('wa.leaveid', $id);
        } else {
            $query2 = $query2->where('wa.othertransid', $id);
        }

        // Final select and sort
        $query2 = $query2
            ->select(
                'mw.majorworkallocationtypeename',
                'mw.majorworkallocationtypetname',
                'gro.groupename',
                'gro.grouptname',
                'fdp.username as fromuser',
                'tdp.username as touser'
            )
            ->distinct()
            ->orderBy('mw.majorworkallocationtypeename', 'asc')
            ->get();
        // $querySql = $query2->toSql();
        // $bindings = $query2->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );

        // print_r($finalQuery);
        // exit;

        // Query 3
        $query3 = DB::table('audit.logothertrans_scheduledel as ls')
            ->join('audit.inst_auditschedule as ina', 'ina.auditscheduleid', '=', 'ls.auditscheduleid')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ina.auditplanid')
            ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ap.instid')
            ->join('audit.deptuserdetails as fdp', 'fdp.deptuserid', '=', 'ls.fromuserid')
            ->leftjoin('audit.deptuserdetails as tdp', 'tdp.deptuserid', '=', 'ls.touserid');
        // ->where('ls.othertransid', $id);
        if ($transactiontypecode == '01') {
            $query3 = $query3->where('ls.leaveid', $id);
        } else {
            $query3 = $query3->where('ls.othertransid', $id);
        }
        $query3 = $query3
            ->select(
                'ins.instename',
                'ins.insttname',
                'ls.datatransfertypecode',
                'ls.workallocationstatus',
                'ls.slipcount',
                'fdp.username as fromuser',
                'tdp.username as touser'
            )
            ->get();

        return [
            'query1' => $query1,
            'query2' => $query2,
            'query3' => $query3
        ];
    }

    public static function getTodept()
    {
        $query = DB::table(self::$department_table)
            ->where('statusflag', 'Y');

        $query->orderBy('orderid', 'asc');

        $departments = $query->get();

        return $departments;
    }

    public static function getholidays()
    {
        $holidayDates = DB::table('audit.mst_holiday')
            ->where('statusflag', 'Y')
            ->pluck('holiday_date')
            ->toArray();
        return $holidayDates;
    }

    public static function getsessionrequestdel($userid)
    {
        $query = DB::table(self::$auditplan_table . ' as ap')
            ->join(self::$instituiion_table . ' as inst', 'inst.instid', '=', 'ap.instid')
            ->join(self::$department_table . ' as dp', function ($join) {
                $join
                    ->on('dp.deptcode', '=', 'inst.deptcode')
                    ->on('ap.auditquartercode', '=', 'dp.currentquarter');
            })
            ->join('audit.auditplanteammember as aptm', 'ap.auditteamid', '=', 'aptm.auditplanteamid')
            ->where('aptm.teamhead', 'Y')
            ->where('aptm.statusflag', 'Y')
            ->where('userid', $userid)
            ->select('ap.auditplanid')
            ->get();

        $userstatus = $query->isNotEmpty() ? 'H' : 'M';

        return $userstatus;
    }

    public static function getscheduledel($reasoncode, $userid, $auditscheduleid, $session_roletypecode)
    {
        if ($reasoncode == View::shared('MandaysExtenstion')) {
            if (View::shared('DGA_roletypecode') == $session_roletypecode) {
                $data = DB::table(self::$instschedule_table . ' as ins')
                    ->join(self::$auditplan_table . ' as ap', 'ap.auditplanid', '=', 'ins.auditplanid')
                    ->join(self::$instituiion_table . ' as inst', 'inst.instid', '=', 'ap.instid')
                    ->join(self::$instschedulemem_table . ' as instm', 'ins.auditscheduleid', '=', 'instm.auditscheduleid')
                    ->join(self::$userdet_table . ' as dep', 'dep.deptuserid', '=', 'instm.userid')
                    ->whereNotNull('entrymeetdate')
                    ->whereNull('exitmeetdate')
                    ->select(
                        'inst.instename',
                        'ins.auditscheduleid',
                        'ap.mandays',
                        'ap.teamsize',
                        'ins.entrymeetdate',
                        'ins.proposedexitmeetdate',
                        'dep.username',
                        'dep.email'
                    )  // optional: select only needed columns
                    ->where('ins.auditscheduleid', $auditscheduleid)
                    ->where('instm.auditteamhead', 'Y');
                // ->get();
            } else {
                $data = DB::table(self::$indleavedetail_table . ' as ild')
                    ->distinct()
                    ->select(
                        'inst.instename',
                        'insa.auditscheduleid'
                    )
                    ->join(self::$logothertransschedule . ' as sc', 'sc.leaveid', '=', 'ild.leaveid')
                    ->join(self::$instschedule_table . ' as insa', 'insa.auditscheduleid', '=', 'sc.auditscheduleid')
                    ->join(self::$auditplan_table . ' as ap', 'ap.auditplanid', '=', 'insa.auditplanid')
                    ->join(self::$instituiion_table . ' as inst', 'inst.instid', '=', 'ap.instid')
                    ->join('audit.mst_dept as dp', function ($join) {
                        $join
                            ->on('dp.deptcode', '=', 'inst.deptcode')
                            ->on('ap.auditquartercode', '=', 'dp.currentquarter');
                    })
                    ->join(self::$userdet_table . ' as dep', 'dep.deptuserid', '=', 'ild.userid')
                    ->join(self::$designation_table . ' as des', 'des.desigcode', '=', 'dep.desigcode')
                    ->join(DB::raw("LATERAL (
                    SELECT d
                    FROM (
                        SELECT d::date
                        FROM generate_series(
                            insa.proposedexitmeetdate - INTERVAL '10 day',
                            insa.proposedexitmeetdate - INTERVAL '1 day',
                            INTERVAL '1 day'
                        ) AS d
                        WHERE EXTRACT(ISODOW FROM d) < 6
                        AND d NOT IN (
                            SELECT holiday_date
                            FROM audit.mst_holiday
                            WHERE statusflag = 'Y'
                        )
                        ORDER BY d DESC
                        LIMIT 1 OFFSET 1
                    ) AS working_days
                ) AS second_working_day"), DB::raw('TRUE'), '=', DB::raw('TRUE'))
                    ->where('processcode', 'P')
                    ->wherenotnull('insa.entrymeetdate')
                    ->whereNull('insa.exitmeetdate')
                    ->whereIn('sc.datatransfertypecode', ['AH', 'MH', 'WA'])
                    ->whereIn('insa.auditscheduleid', function ($query) use ($userid) {
                        $query
                            ->select('auditscheduleid')
                            ->from('audit.inst_schteammember as isch')
                            ->where('userid', $userid)
                            ->where('auditteamhead', 'Y');
                    })
                    // ✅ Overlap condition
                    ->whereRaw('
                        GREATEST(ild.fromdate, insa.entrymeetdate) <=
                        LEAST(ild.todate, COALESCE(insa.exitmeetdate, insa.proposedexitmeetdate))
                  ')
                    ->whereRaw('second_working_day.d <= CURRENT_DATE');

                if ($auditscheduleid) {
                    $data
                        ->where('insa.auditscheduleid', $auditscheduleid)
                        ->select(
                            'inst.instename',
                            'insa.auditscheduleid',
                            'ap.mandays',
                            'ap.teamsize',
                            'insa.entrymeetdate',
                            'insa.proposedexitmeetdate',
                            'dep.username',
                            'des.desigesname',
                            'ild.fromdate',
                            'ild.todate',
                            DB::raw('audit.get_working_days_between(
            GREATEST(ild.fromdate, insa.entrymeetdate),
            LEAST(ild.todate, COALESCE(insa.exitmeetdate, insa.proposedexitmeetdate))
        ) as working_days')
                        );
                } else {
                    $data
                        ->whereNotIn('insa.auditscheduleid', function ($query) {
                            $query
                                ->select('auditscheduleid')
                                ->from(self::$mandaysextension_table)
                                ->whereNotNull('auditscheduleid');
                        })
                        ->distinct('inst.instename');
                }
            }

            // $querySql = $data->toSql();
            // $bindings = $data->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);
            // exit;
            $result = $data->get();
            // print_r($result);
            // exit;
            return $result;
        }
    }

    public static function schedulerequestinsert($data)
    {
        try {
            $exists = DB::table(self::$mandaysextension_table)
                ->where('auditscheduleid', $data['auditscheduleid'])
                ->where('transactiontypecode', $data['transactiontypecode'])
                ->where('statusflag', 'Y')
                ->exists();

            if ($exists) {
                return ['status' => 'fail', 'error' => 'Mandays extension already exists for this schedule.'];
            }

            $id = DB::table(self::$mandaysextension_table)->insertGetId($data, 'mandaysextensionid');

            return $id
                ? ['status' => 'success', 'data' => $id]
                : ['status' => 'fail', 'error' => 'Mandays extension insert failed.'];
        } catch (\Exception $e) {
            return ['status' => 'fail', 'error' => $e->getMessage()];
        }
    }

    public static function fetchschedulesrequest($sessionuserid, $mandaysextensionid)
    {
        $data = DB::table(self::$mandaysextension_table . ' as me')
            ->join(self::$instschedule_table . ' as ina', 'ina.auditscheduleid', '=', 'me.auditscheduleid')
            ->join(self::$transtype_table . ' as tp', 'tp.transactiontypecode', '=', 'me.transactiontypecode')
            ->join(self::$auditplan_table . ' as ap', 'ap.auditplanid', '=', 'ina.auditplanid')
            ->join(self::$instituiion_table . ' as inst', 'inst.instid', '=', 'ap.instid')
            ->join(self::$process_table . ' as p', 'p.processcode', '=', 'me.processcode')
            ->select(
                'inst.instename',
                'inst.insttname',
                'me.oldmandays',
                'me.extramandays',
                'me.newmandays',
                'me.oldpurposedexitmeetdate',
                'me.newpurposedexitmeetdate',
                'p.processelname',
                'tp.transactiontypelname',
                'me.remarks',
                'ap.teamsize',
                'ina.entrymeetdate',
                'me.approvedremarks',
                'me.processcode',
                'me.mandaysextensionid',
                'me.transactiontypecode',
                'inst.deptcode',
                'inst.regioncode',
                'inst.distcode',
                'me.auditscheduleid'
            )
            ->where('me.createdby', $sessionuserid);
        if ($mandaysextensionid) {
            $data = $data->where('mandaysextensionid', $mandaysextensionid);
        }
        $data = $data->get();
        return $data;
    }

    public static function getschedulereqdel($id, $reasoncode)
    {
        if ($reasoncode == View::shared('MandaysExtenstion')) {
            $data = DB::table(self::$indleavedetail_table . ' as ild')
                ->distinct()
                ->join(self::$logothertransschedule . ' as sc', 'sc.leaveid', '=', 'ild.leaveid')
                ->join(self::$mandaysextension_table . ' as me', 'me.auditscheduleid', '=', 'sc.auditscheduleid')
                ->join(self::$instschedule_table . ' as insa', 'insa.auditscheduleid', '=', 'me.auditscheduleid')
                ->join(self::$auditplan_table . ' as ap', 'ap.auditplanid', '=', 'insa.auditplanid')
                ->join(self::$instituiion_table . ' as inst', 'inst.instid', '=', 'ap.instid')
                ->join(self::$transtype_table . ' as tp', 'tp.transactiontypecode', '=', 'me.transactiontypecode')
                ->join('audit.mst_dept as dp', function ($join) {
                    $join
                        ->on('dp.deptcode', '=', 'inst.deptcode')
                        ->on('ap.auditquartercode', '=', 'dp.currentquarter');
                })
                ->join(self::$userdet_table . ' as dep', 'dep.deptuserid', '=', 'ild.userid')
                ->join(self::$designation_table . ' as des', 'des.desigcode', '=', 'dep.desigcode')
                ->where('me.processcode', '<>', 'P')
                ->whereIn('sc.datatransfertypecode', ['AH', 'MH', 'WA'])
                ->wherenotnull('insa.entrymeetdate')
                ->whereNull('insa.exitmeetdate')
                // ✅ Overlap condition
                ->whereRaw('
        GREATEST(ild.fromdate, insa.entrymeetdate) <=
        LEAST(ild.todate, COALESCE(insa.exitmeetdate, insa.proposedexitmeetdate))
        ')
                ->where('me.mandaysextensionid', $id)
                ->select(
                    'inst.instename',
                    'insa.auditscheduleid',
                    'ap.mandays',
                    'ap.teamsize',
                    'insa.entrymeetdate',
                    'insa.proposedexitmeetdate',
                    'dep.username',
                    'des.desigesname',
                    'ild.fromdate',
                    'ild.todate',
                    DB::raw('audit.get_working_days_between(
            GREATEST(ild.fromdate, insa.entrymeetdate),
            LEAST(ild.todate, COALESCE(insa.exitmeetdate, insa.proposedexitmeetdate))
        ) as working_days'),
                    'insa.auditscheduleid',
                    'me.oldmandays',
                    'me.teamsize',
                    // 'me.extramandays',
                    // 'me.requestedexitmeetdate',
                    'me.mandaysextensionid',
                    'insa.proposedexitmeetdate as oldpurposedexitmeetdate',
                    'inst.instename',
                    'insa.entrymeetdate',
                    'tp.transactiontypelname',
                    'tp.transactiontypecode',
                    'insa.workallocationflag',
                    'insa.exitmeetdate',
                    'ap.teamsize',
                    // Team Members
                    DB::raw("(
                        SELECT string_agg(dp.username, ', ')
                        FROM " . self::$instschedulemem_table . ' insch
                        JOIN ' . self::$userdet_table . " dp ON dp.deptuserid = insch.userid
                        WHERE insch.auditscheduleid = insa.auditscheduleid
                          AND insch.auditteamhead = 'N' and insch.statusflag = 'Y'
                    ) as teammember"),
                    // Team Head
                    DB::raw("(
                        SELECT string_agg(dp.username, ', ')
                        FROM " . self::$instschedulemem_table . ' insch
                        JOIN ' . self::$userdet_table . " dp ON dp.deptuserid = insch.userid
                        WHERE insch.auditscheduleid = insa.auditscheduleid
                          AND insch.auditteamhead = 'Y' and insch.statusflag = 'Y'
                    ) as teamhead"),
                    // Head User ID
                    DB::raw('(
                        SELECT insch.userid
                        FROM ' . self::$instschedulemem_table . " insch
                        WHERE insch.auditscheduleid = insa.auditscheduleid
                          AND insch.auditteamhead = 'Y' and insch.statusflag = 'Y'
                        LIMIT 1
                    ) as headuserid"),
                    // Audit Slip Count
                    DB::raw('(
                        SELECT COUNT(*)
                        FROM audit.trans_auditslip
                        WHERE auditscheduleid = insa.auditscheduleid
                    ) as slipcount')
                );

            // $querySql = $data->toSql();
            // $bindings = $data->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);
            // exit;
            $result = $data->get();

            return $result;
        }
        // $data = DB::table(self::$mandaysextension_table . ' as me')
        //     ->join(self::$transactiondetail_table . ' as td', 'td.mandaysextensionid', '=', 'me.mandaysextensionid')
        //     ->join(self::$instschedule_table . ' as inst', 'inst.auditscheduleid', '=', 'me.auditscheduleid')
        //     ->join(self::$auditplan_table . ' as ap', 'ap.auditplanid', '=', 'inst.auditplanid')
        //     ->join(self::$instituiion_table . ' as instut', 'instut.instid', '=', 'ap.instid')
        //     ->join(self::$transtype_table . ' as tp', 'tp.transactiontypecode', '=', 'me.transactiontypecode')
        //     ->select(
        //         'inst.auditscheduleid',
        //         'me.oldmandays',
        //         'me.teamsize',
        //         // 'me.extramandays',
        //         // 'me.requestedexitmeetdate',
        //         'me.mandaysextensionid',
        //         'inst.proposedexitmeetdate as oldpurposedexitmeetdate',
        //         'instut.instename',
        //         'inst.entrymeetdate',
        //         'tp.transactiontypelname',
        //         'tp.transactiontypecode',
        //         'inst.workallocationflag',
        //         'inst.exitmeetdate',
        //         'ap.teamsize',

        //         // Team Members
        //         DB::raw("(
        //         SELECT string_agg(dp.username, ', ')
        //         FROM " . self::$instschedulemem_table . " insch
        //         JOIN " . self::$userdet_table . " dp ON dp.deptuserid = insch.userid
        //         WHERE insch.auditscheduleid = inst.auditscheduleid
        //           AND insch.auditteamhead = 'N' and insch.statusflag = 'Y'
        //     ) as teammember"),

        //         // Team Head
        //         DB::raw("(
        //         SELECT string_agg(dp.username, ', ')
        //         FROM " . self::$instschedulemem_table . " insch
        //         JOIN " . self::$userdet_table . " dp ON dp.deptuserid = insch.userid
        //         WHERE insch.auditscheduleid = inst.auditscheduleid
        //           AND insch.auditteamhead = 'Y' and insch.statusflag = 'Y'
        //     ) as teamhead"),

        //         // Head User ID
        //         DB::raw("(
        //         SELECT insch.userid
        //         FROM " . self::$instschedulemem_table . " insch
        //         WHERE insch.auditscheduleid = inst.auditscheduleid
        //           AND insch.auditteamhead = 'Y' and insch.statusflag = 'Y'
        //         LIMIT 1
        //     ) as headuserid"),

        //         // Audit Slip Count
        //         DB::raw("(
        //         SELECT COUNT(*)
        //         FROM audit.trans_auditslip
        //         WHERE auditscheduleid = inst.auditscheduleid
        //     ) as slipcount")
        //     )
        //     ->where('me.mandaysextensionid', $id)
        //     ->where('me.processcode', '<>', 'P')
        //     ->get();

        // return $data;
    }

    public static function schedulerequest_approve($request, $sessionuserid, $sessionuserchargeid)
    {
        try {
            DB::beginTransaction();

            $request = $request->all();  // Ensure input is an array

            $transactiontypecode = $request['transactiontypecode'];

            if ($transactiontypecode == '09') {
                DB::statement('CALL audit.schdeulerequest(?, ?, ?, ?, ?, ?,?)', [
                    $request['mandaysextension'],
                    $request['newpurposedexitmeetdate'],
                    $request['extramandays'],
                    $request['remarks'],
                    $sessionuserid,
                    $sessionuserchargeid,
                    $transactiontypecode,
                ]);
            }

            DB::commit();
            return ['status' => 'success'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public static function getforwardtouserid($transactiontypecode, $deptcode, $distcode, $session_rolemappingid)
    {
        try {
            $rolemappingdel = DB::table(self::$transactionflow_table . ' as ro')
                ->join(self::$chargedetail_table . ' as c', 'c.rolemappingid', '=', 'ro.torolemappingid')
                ->join(self::$userchargedetail_table . ' as uc', 'uc.chargeid', '=', 'c.chargeid')
                ->join(self::$userdet_table . ' as dp', 'dp.deptuserid', '=', 'uc.userid')
                ->where('ro.transactiontypecode', $transactiontypecode)
                ->where('ro.fromrolemappingid', $session_rolemappingid)
                ->where('ro.deptcode', $deptcode)
                ->where('c.distcode', $distcode)
                ->where('uc.statusflag', 'Y')
                ->select('uc.userchargeid', 'uc.userid', 'dp.username', 'dp.email')
                ->get();

            // $querySql = $rolemappingdel->toSql();
            // $bindings = $rolemappingdel->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);
            // exit;

            if ($rolemappingdel->isEmpty()) {
                return ['status' => 'fail', 'error' => 'No AD in that district'];
            }

            if ($rolemappingdel->count() === 1) {
                return ['status' => 'success', 'data' => $rolemappingdel->first()];
            } else {
                return ['status' => 'fail', 'error' => 'More than one AD found in the district'];
            }
        } catch (\Exception $e) {
            return ['status' => 'fail', 'error' => $e->getMessage()];
        }
    }

    public static function leaveindetails($userid)
    {
        $result = DB::table('audit.ind_leavedetail as ins')
            ->leftJoin('audit.ind_leavein_detail as insin', 'ins.leaveid', '=', 'insin.leaveid')
            ->leftJoin('audit.mst_process as p', 'p.processcode', '=', 'insin.processcode')
            ->select('ins.fromdate', 'ins.todate', 'insin.processcode', 'ins.reason', 'p.processelname', 'ins.leaveid', 'ins.leavein', 'ins.userid')
            // ->where('ins.longleave', 'Y')
            ->where('ins.userid', $userid)
            ->get();

        // $querySql = $result->toSql();
        // $bindings = $result->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );
        // print_r($finalQuery);
        // exit;

        return $result;
    }

    public static function leaveindata($leaveinid)
    {
        $result =
            // DB::table('audit.ind_leavedetail as ins')
            // ->Join('audit.ind_leavein_detail as insin', 'ins.leaveid', '=', 'insin.leaveid')
            // ->Join('audit.deptuserdetails as dp', 'ins.userid', '=', 'dp.deptuserid')
            // ->Join('audit.mst_process as p', 'p.processcode', '=', 'insin.processcode')
            // ->select('ins.fromdate', 'ins.todate', 'insin.processcode', 'ins.reason', 'p.processelname',
            //     'ins.leaveid', 'ins.leaveinid', 'ins.userid', 'dp.username', 'dp.ifhrmsno', 'dp.dor', 'dp.dob', 'dp.dob', 'insin.createdon')
            // ->where('ins.longleave', 'Y')
            // ->where('insin.leaveinid', $leaveinid)
            // ->get();
            $othertrans = DB::table('audit.ind_leavedetail as other')
                ->Join('audit.ind_leavein_detail as insin', 'other.leaveid', '=', 'insin.leaveid')
                ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'other.userid')
                ->join('audit.userchargedetails as uc', function ($join) {
                    $join
                        ->on('uc.userid', '=', 'other.userid')
                        ->where('uc.statusflag', '=', 'Y');
                })
                ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
                ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'cd.deptcode')
                ->join('audit.mst_region as re', 're.regioncode', '=', 'cd.regioncode')
                ->join('audit.mst_district as dist', 'dist.distcode', '=', 'cd.distcode')
                ->join('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'insin.transactiontypecode')
                ->selectRaw("
                    other.userid,
                    du.username,
                    du.ifhrmsno,
                    du.dob,
                    du.dor,
                    other.fromdate,
                    other.todate,
                     other.reason,
                    tt.transactiontypelname,
                    insin.transactiontypecode,
                    'O' as inoutstatus,
                    other.leaveid,
                    insin.createdon as intime,
                    STRING_AGG(
                        TRIM(BOTH ' - ' FROM CONCAT(
                            dept.deptesname,
                            CASE WHEN re.regionename IS NOT NULL THEN ' - ' || re.regionename ELSE '' END,
                            CASE WHEN dist.distename IS NOT NULL THEN ' - ' || dist.distename ELSE '' END,
                            ' (' || cd.chargedescription || ')'
                        )), ', '
                    ) AS chargedel,
                    STRING_AGG(DISTINCT cd.regioncode, ',') AS regioncodes,
                    STRING_AGG(DISTINCT cd.deptcode, ',') AS deptcodes,
                    insin.leaveinid
                ")
                ->where('other.statusflag', '=', 'Y')
                ->where('insin.leaveinid', $leaveinid)
                ->groupBy([
                    'other.userid',
                    'du.username',
                    'du.ifhrmsno',
                    'du.dob',
                    'du.dor',
                    'other.fromdate',
                    'other.todate',
                    'other.reason',
                    'insin.createdon',
                    'tt.transactiontypelname',
                    'insin.transactiontypecode',
                    'other.leaveid',
                    'insin.leaveinid'
                ])
                ->get();

        return $result;
    }

    public static function leavein_insertupdate($data, $leaveinid = null, $for = null)
    {
        try {
            $table = self::$indleaveindetail_table;

            // Base query
            $query = DB::table($table);

            // Exclude current leaveinid if updating
            if ($leaveinid) {
                $query->where('leaveinid', '!=', $leaveinid);
            }

            // Check if leave already exists (only if $for is 'form' and $userid provided)
            if ($for === 'form') {
                $leaveExists = (clone $query)
                    ->where('leaveid', '=', $data['leaveid'])
                    ->exists();

                if ($leaveExists) {
                    return ['status' => 'failed', 'message' => 'Leave in already applied.'];
                }
            }

            if ($leaveinid) {
                // Update existing record
                $updatedRows = DB::table($table)->where('leaveinid', $leaveinid)->update($data);

                if ($updatedRows > 0) {
                    $record = DB::table($table)->where('leaveinid', $leaveinid)->first();
                    return ['status' => 'updated', 'data' => $record];
                } else {
                    return ['status' => 'failed', 'message' => 'No rows updated.'];
                }
            } else {
                // Insert new record
                $insertedId = DB::table($table)->insertGetId($data, 'leaveinid');

                if ($insertedId) {
                    $record = DB::table($table)->where('leaveinid', $insertedId)->first();
                    return ['status' => 'inserted', 'data' => $record];
                } else {
                    return ['status' => 'failed', 'message' => 'Insertion failed.'];
                }
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public static function planmemberdetails($auditplanid)
    {
        $results = DB::table('audit.auditplanteammember as apt')
            ->join(self::$auditplan_table . ' as ap', 'ap.auditteamid', '=', 'apt.auditplanteamid')
            ->join(self::$userdet_table . ' as dp', 'dp.deptuserid', '=', 'apt.userid')
            ->join(self::$designation_table . ' as des', 'des.desigcode', '=', 'dp.desigcode')
            ->where('ap.auditplanid', $auditplanid)
            ->where('apt.teamhead', 'N')
            ->where('apt.statusflag', 'Y')
            ->select('dp.username', 'des.desigesname', 'apt.userid')
            ->get();
        return $results;
    }

    public static function futureplanheadtransfer_insertupdate($auditplanid, $fromuserid, $touserid, $datatransfertypecode, $sessionuserid, $remarks)
    {
        try {
            // Call the function - assuming it returns JSON as a string in 'response' column
            $result = DB::select('SELECT ' . self::$fn_headchangeforplan . '(?, ?, ?, ?,?, ?) AS response', [
                $auditplanid,
                $fromuserid,
                $touserid,
                $datatransfertypecode,
                $remarks,
                $sessionuserid  // Assuming session user ID from auth, adjust if needed
            ]);

            if (empty($result)) {
                throw new \Exception('No response from database function.');
            }

            // The result is an array with one object, property 'response' contains JSON string
            $responseJson = $result[0]->response;

            // Decode JSON to PHP array/object
            $response = json_decode($responseJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from database function.');
            }

            // Check status
            if (isset($response['status']) && $response['status'] === 'success') {
                // Return the entire response or just message, up to you
                return $response;
            } else {
                $message = $response['message'] ?? 'Unknown failure from database function.';
                throw new \Exception("Function failed: {$message}");
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Log::error('Database Query Error: ' . $e->getMessage());
            throw new \Exception('Database error occurred while automating work allocation.');
        } catch (\Exception $e) {
            // Log::error('General Error: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    public static function getplandetails($userid, $planid)
    {
        $query = DB::table(self::$auditplan_table . ' AS ap')
            ->select('ap.mandays', 'ap.teamsize', 'ap.auditplanid', 'dp.username', 'des.desigesname', 'inst.instename')
            ->join(self::$auditplanteammember_table . ' AS apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->leftJoin(self::$instschedule_table . ' AS ins', 'ins.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$instituiion_table . ' AS inst', 'inst.instid', '=', 'ap.instid')
            ->join(self::$department_table . ' AS de', 'de.deptcode', '=', 'inst.deptcode')
            ->join(self::$userdet_table . ' AS dp', 'dp.deptuserid', '=', 'apt.userid')
            ->join(self::$designation_table . ' AS des', 'des.desigcode', '=', 'dp.desigcode')
            ->whereColumn('ap.auditquartercode', 'de.currentquarter')
            ->whereNull('ins.auditscheduleid')
            ->where('apt.teamhead', 'Y')
            ->where('apt.userid', $userid)
            ->where('apt.statusflag', 'Y')
            ->whereRaw('NOT EXISTS (
                SELECT 1 FROM ' . self::$logothertransplandel_table . " ltp,
                jsonb_array_elements_text(ltp.auditplanid->'plan_ids') AS pid
                WHERE ltp.futureplanheadtransferid IS NOT NULL
                AND pid::int = ap.auditplanid
            )");

        if ($planid) {
            $query = $query->where('ap.auditplanid', $planid);
        }

        $data = $query->get();
        return $data;
    }

    public static function getauditornamesbasedondist($deptcode, $distcode)
    {
        // $results = DB::table('audit.deptuserdetails as dp')
        //     ->distinct()
        //     ->join('audit.auditplanteammember as apt', 'apt.userid', '=', 'dp.deptuserid')
        //     ->join('audit.mst_designation as des', 'des.desigcode', '=', 'dp.desigcode')
        //     ->where('dp.deptcode', $deptcode)
        //     ->where('dp.distcode', $distcode)
        //     ->where('apt.statusflag', 'Y')
        //     ->where('apt.teamhead', 'Y')
        //     ->select('dp.deptuserid', 'dp.username', 'des.desigesname');
        // $querySql = $results->toSql();
        // $bindings = $results->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );
        // print_r($finalQuery);
        // exit;

        $results = DB::select('
            SELECT DISTINCT
                dp.deptuserid,
                dp.username,
                des.desigesname
            FROM ' . self::$userdet_table . ' AS dp
            INNER JOIN ' . self::$auditplanteammember_table . ' AS apt ON apt.userid = dp.deptuserid
            INNER JOIN ' . self::$auditplan_table . ' AS ap ON ap.auditteamid = apt.auditplanteamid
            LEFT JOIN ' . self::$instschedule_table . ' AS inst ON inst.auditplanid = ap.auditplanid
            INNER JOIN ' . self::$designation_table . ' AS des ON des.desigcode = dp.desigcode
            WHERE dp.deptcode = ?
                AND dp.distcode = ?
                AND apt.statusflag = ?
                AND apt.teamhead = ?
                AND inst.auditscheduleid IS NULL
                AND NOT EXISTS (
                    SELECT 1
                    FROM ' . self::$logothertransplandel_table . " AS ltp,
                        jsonb_array_elements_text(ltp.auditplanid->'plan_ids') AS pid
                    WHERE ltp.futureplanheadtransferid IS NOT NULL
                    AND pid::int = ap.auditplanid
                )
        ", [$deptcode, $distcode, 'Y', 'Y']);  // binding the flags too

        // Convert to a collection if needed
        $data = collect($results);

        return $data;
    }

    public static function reverselistusers($deptcode, $regioncode)
    {
        $touserdata = DB::table(self::$userdet_table . ' as ut')
            ->join(self::$district_table . ' as dt', 'ut.distcode', '=', 'dt.distcode')
            ->join(self::$userchargedetail_table . ' as uc', 'uc.userid', '=', 'ut.deptuserid')
            ->join(self::$chargedetail_table . ' as c', 'c.chargeid', '=', 'uc.chargeid')
            ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
            ->join(self::$designation_table . ' as d', 'd.desigcode', '=', 'ut.desigcode')
            ->where('c.regioncode', $regioncode)
            ->where('ut.deptcode', $deptcode)
            ->where('ut.reservelist', 'N')
            ->where('uc.statusflag', 'Y')
            ->where('ut.statusflag', 'Y')
            ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))
            ->whereNotIn('ut.deptuserid', function ($query) {
                $query
                    ->select('userid')
                    ->from('audit.othertransactions')
                    ->where('processcode', 'F')
                    ->where('inoutstatus', 'I');
            })
            ->select([
                'ut.deptuserid',
                'd.desigesname',
                'ut.username',
                'ut.usertamilname',
                'dt.distcode',
                'dt.distename',
            ])
            ->orderBy('d.desigesname')
            ->orderBy('dt.distename')
            ->orderBy('ut.username')
            ->orderBy('ut.usertamilname')
            ->get();
        return $touserdata;
    }

    public static function futureplanheadtransferdel(
        $deptcode, $distcode
    ) {
        $data = DB::table(self::$futureplanheadtransfer_table . ' as fp')
            ->join(self::$userdet_table . ' as fdp', 'fdp.deptuserid', '=', 'fp.fromuserid')
            ->join(self::$userdet_table . ' as tdp', 'tdp.deptuserid', '=', 'fp.touserid')
            ->join(self::$designation_table . ' as fdes', 'fdes.desigcode', '=', 'fdp.desigcode')
            ->join(self::$designation_table . ' as tdes', 'tdes.desigcode', '=', 'tdp.desigcode')
            ->join(self::$auditplan_table . ' as ap', 'ap.auditplanid', '=', 'fp.auditplanid')
            ->join(self::$instituiion_table . ' as inst', 'inst.instid', '=', 'ap.instid')
            ->where('fp.processcode', '=', 'P')
            ->where('inst.deptcode', $deptcode)
            ->where('inst.distcode', $distcode)
            ->select([
                'fdp.username as from_username',
                'tdp.username as to_username',
                'fdes.desigesname as from_designation',
                'tdes.desigesname as to_designation',
                'inst.instename',
                'inst.insttname',
                'ap.teamsize',
                'ap.mandays',
                'fp.datatransfertypecode',
                'fp.remarks'
            ]);

        return $data->get();
    }

    // //mandays extension stateadmin /////////////

    public static function fetch_deptbaseddata(
        ?string $deptcode = null,
        ?string $regioncode = null,
        ?string $distcode = null,
        string $getval,
    ) {
        try {
            $query = DB::table(self::$instituiion_table . ' as inst')
                ->where('inst.statusflag', 'Y')
                ->when($deptcode, function ($query) use ($deptcode) {
                    $query->where('inst.deptcode', $deptcode);
                });
            // ->where('inst.deptcode', $deptcode);
            switch ($getval) {
                // protected static $auditorinstmappingTable = BaseModel::AUDITORINSTMAPPING_TABLE;
                case 'region':
                    $query
                        ->join(self::$region_table . ' as re', 're.regioncode', '=', 'inst.regioncode')
                        ->select('inst.regioncode', 're.regionename', 're.regiontname')
                        ->distinct();
                    $query->orderBy('re.regionename', 'ASC');
                    break;

                case 'district':
                    $query
                        ->join(self::$region_table . ' as re', 're.regioncode', '=', 'inst.regioncode')
                        ->join(self::$district_table . ' as d', 'd.distcode', '=', 'inst.distcode')
                        ->select('inst.distcode', 'd.distename', 'd.disttname')
                        ->distinct();
                    $query->where('inst.regioncode', $regioncode);
                    $query->orderBy('d.distename', 'ASC');
                    break;

                case 'inst':
                    $query
                        ->join(self::$region_table . ' as re', 're.regioncode', '=', 'inst.regioncode')
                        ->join(self::$district_table . ' as d', 'd.distcode', '=', 'inst.distcode')
                        ->join(self::$department_table . ' as dept', 'dept.deptcode', '=', 'inst.deptcode')
                        ->join(self::$auditplan_table . ' as plan', 'plan.instid', '=', 'inst.instid')
                        ->join(self::$instschedule_table . ' as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
                        ->select('inst.instename', 'inst.insttname', 'sch.auditscheduleid')
                        ->distinct();
                    $query
                        ->where('inst.regioncode', $regioncode)
                        ->where('inst.distcode', $distcode)
                        ->where('inst.deptcode', $deptcode)
                        ->whereColumn('dept.currentquarter', 'plan.auditquartercode')
                        ->whereNotNull('sch.entrymeetdate')
                        ->whereNull('sch.exitmeetdate');
                    $query->orderBy('inst.instename', 'ASC');
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

            return [
                'data' => $result,
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching data. Please contact the administrator.';

            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function update_scheduledel($data, $auditscheduleid)
    {
        $updatedRows = DB::table(self::$instschedule_table)
            ->where('auditscheduleid', $auditscheduleid)
            ->update($data);

        return $updatedRows > 0;
    }

    public static function update_plandel($data, $auditscheduleid)
    {
        $updatedRows = DB::table('audit.auditplan')
            ->join(self::$instschedule_table . ' as inst', 'inst.auditplanid', '=', 'auditplan.auditplanid')
            ->where('inst.auditscheduleid', $auditscheduleid)
            ->update([
                'auditplan.mandays' => $data['mandays'],
                'auditplan.updatedby' => $data['sessionuserid'],
                'auditplan.updatedon' => now(),
            ]);

        return $updatedRows > 0;
    }

    public static function mandaysextension_insert($data, $mandaysextensionid, $sessionroletypecode)
    {
        try {
            $table = self::$mandaysextension_table;
            $criteria = [
                ['auditscheduleid', '=', $data['auditscheduleid']],
                ['transactiontypecode', '=', $data['transactiontypecode']],
                ['createdbyroletypecode', '=', $sessionroletypecode],
                ['statusflag', '=', 'Y']
            ];

            if ($mandaysextensionid) {
                // Check if another conflicting active record exists
                $conflictExists = DB::table($table)
                    ->where($criteria)
                    ->where('mandaysextensionid', '<>', $mandaysextensionid)
                    ->exists();

                if ($conflictExists) {
                    return [
                        'status' => 'fail',
                        'error' => 'Mandays extension already exists for this schedule.'
                    ];
                }

                // Update the record
                $updated = DB::table($table)
                    ->where('mandaysextensionid', $mandaysextensionid)
                    ->update($data);

                return $updated
                    ? ['status' => 'success', 'data' => $mandaysextensionid, 'message' => 'Mandays extension updated successfully.']
                    : ['status' => 'fail', 'error' => 'Update failed or no changes detected.'];
            }

            // Insert case
            $exists = DB::table($table)
                ->where($criteria)
                ->exists();

            if ($exists) {
                return [
                    'status' => 'fail',
                    'error' => 'Mandays extension already exists for this schedule.'
                ];
            }

            // Insert the new record
            $id = DB::table($table)->insertGetId($data, 'mandaysextensionid');

            return $id
                ? ['status' => 'success', 'data' => $id, 'message' => 'Mandays extension inserted successfully.']
                : ['status' => 'fail', 'error' => 'Mandays extension insert failed.'];
        } catch (\Exception $e) {
            return ['status' => 'fail', 'error' => $e->getMessage()];
        }
    }

    public static function leaveinschdel($leaveinid)
    {
        $results = DB::table('audit.ind_leavedetail as inl')
            ->join('audit.ind_leavein_detail as indl', 'indl.leaveid', '=', 'inl.leaveid')
            ->join('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'indl.userid')
            ->join('audit.mst_designation as des', 'des.desigcode', '=', 'dp.desigcode')
            ->join('audit.mst_district as dist', 'dist.distcode', '=', 'dp.distcode')
            ->join('audit.logothertrans_scheduledel as lsch', 'lsch.leaveid', '=', 'inl.leaveid')
            ->join('audit.inst_auditschedule as ins', 'ins.auditscheduleid', '=', 'lsch.auditscheduleid')
            ->join('audit.inst_schteammember as insch', function ($join) {
                $join
                    ->on('insch.auditscheduleid', '=', 'lsch.auditscheduleid')
                    ->on('insch.userid', '=', 'inl.userid');
            })
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ins.auditplanid')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->where('indl.leaveinid', $leaveinid)
            ->where('datatransfertypecode', 'AH')
            ->whereNull('ins.exitmeetdate')
            ->select([
                'inst.instename',
                'ins.entrymeetdate',
                'ins.proposedexitmeetdate',
                'ins.workallocationflag',
                'inl.userid',
                'lsch.touserid',
                'lsch.auditscheduleid',
                'ins.exitmeetdate',
                'dp.username',
                'des.desigesname',
                'dist.distename',
                // Slip count by lsch.touserid
                DB::raw('(
            SELECT COUNT(auditslipid)
            FROM audit.trans_auditslip
            WHERE auditscheduleid = ins.auditscheduleid
              AND createdby = lsch.touserid
        ) AS slipcount'),
                // Member count where statusflag = 'Y'
                DB::raw("(
            SELECT COUNT(*)
            FROM audit.inst_schteammember
            WHERE auditscheduleid = ins.auditscheduleid
              AND statusflag = 'Y'
        ) AS membercount"),
                // Get schteammemberid for touserid with statusflag = 'Y'
                DB::raw("(
            SELECT schteammemberid
            FROM audit.inst_schteammember
            WHERE auditscheduleid = ins.auditscheduleid
              AND statusflag = 'Y'
              AND userid = lsch.touserid
            LIMIT 1
        ) AS schteammemberid"),
            ])
            ->get();
        return $results;
    }

    public static function approveleavein($request, $sessionuserid, $sessionuserchargeid)
    {
        // Initialize values
        $auditscheduleids = '{}';
        $datatypes = '{}';

        $foruserid = '{}';

        $auditplanids = '{}';
        $userid = $request->userid;

        // Process array parameters only if not in 'I' status

        if (isset($request['auditscheduleid']) && is_array($request['auditscheduleid']) && count($request['auditscheduleid']) > 0) {
            $auditscheduleids = '{' . implode(',', $request['auditscheduleid']) . '}';
        }

        if (isset($request['datatransfercode']) && is_array($request['datatransfercode']) && count($request['datatransfercode']) > 0) {
            $quoted = array_map(fn($v) => '"' . $v . '"', $request['datatransfercode']);
            $datatypes = '{' . implode(',', $quoted) . '}';
        }

        if (isset($request['foruserid']) && is_array($request['foruserid']) && count($request['foruserid']) > 0) {
            $transuserFormatted = array_map(
                fn($v) => ($v === null || $v === '') ? 'NULL' : $v,
                $request['foruserid']
            );
            $foruserid = '{' . implode(',', $transuserFormatted) . '}';
        }

        // print_r($foruserid);
        // print_r($auditscheduleids);
        // print_r($datatypes);
        // echo $userid;
        // echo '<br>';
        // echo $sessionuserid;
        // echo '<br>';
        // echo $sessionuserchargeid;

        // call audit.leavein('{494}','{6675}', '{"CD"}', 464,30,5028,'11',1)

        // exit;
        DB::beginTransaction();

        $request = $request->all();  // Ensure input is an array
        DB::statement('CALL audit.leavein(?, ?, ?, ?)', [
            $request['leaveinid'],
            $sessionuserid,
            $sessionuserchargeid,
            $request['transactiontypecode'],
        ]);
        DB::commit();
        return ['status' => 'success'];
    }

 public static function get_changeusererp()
    {
        try {

            $today = view()->shared('get_nowtime')->toDateString();
            $data = DB::table('audit.mst_institution as inst')

                // Audit Plan
                ->join('audit.auditplan as ap', 'ap.instid', '=', 'inst.instid')

                // Schedule
                ->join('audit.inst_auditschedule as sch', 'sch.auditplanid', '=', 'ap.auditplanid')

                // Transfer Schedule
                ->join('audit.logothertrans_scheduledel as lsch', 'lsch.auditscheduleid', '=', 'sch.auditscheduleid')

                // Leave Details
                ->join('audit.ind_leavedetail as ld', 'ld.leaveid', '=', 'lsch.leaveid')

                // Transaction Details
                ->join('audit.transactiondetail as td', 'td.leaveid', '=', 'ld.leaveid')

                // Approved By Details
                ->join('audit.userchargedetails as uc', 'uc.userchargeid', '=', 'td.updatedbyuserchargeid')
                ->join('audit.deptuserdetails as adp', 'adp.deptuserid', '=', 'uc.userid')
                ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
                ->join('audit.mst_designation as ades', 'ades.desigcode', '=', 'ch.desigcode')

                // From User Details
                ->join('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'lsch.fromuserid')
                ->join('audit.mst_designation as des', 'des.desigcode', '=', 'dp.desigcode')

                // To User Details
                ->join('audit.deptuserdetails as dp1', 'dp1.deptuserid', '=', 'lsch.touserid')
                ->join('audit.mst_designation as des1', 'des1.desigcode', '=', 'dp1.desigcode')

                // Main Conditions
                ->where([
                    ['ap.datafromapi', '=', 'Y'],
                    ['ld.processcode', '=', 'P'],
                ])

                // API Not Sent
                ->where(function ($query) {
                    $query->whereNull('lsch.apisent')
                        ->orWhere('lsch.apisent', 'N');
                })

                // Current Date Between From and To Date
                ->whereDate('ld.fromdate', '<=', $today)
                ->whereDate('ld.todate', '>=', $today)
->whereIn('lsch.datatransfertypecode', ['AH', 'CD'])

                ->select([
                    'sch.auditscheduleid',
                    'lsch.leaveid',

                    DB::raw('COALESCE(lsch.othertransid, 0) AS othertransid'),

                    'inst.instename',

                    // From User
                    'dp.username',
                    'des.desigesname',

                    // Leave Details
                    'ld.fromdate',
                    'ld.todate',
                    'ld.reason',

                    // Schedule Dates
                    'sch.entrymeetdate',
                    'sch.exitmeetdate',
                    'sch.proposedexitmeetdate',

                    // To User
                    'dp1.username as tousername',
                    'des1.desigesname as todesignesname',

                    // Approved By
                    'adp.username as approvedbyusername',
                    'ades.desigesname as approvedbydesignation',

                    'td.updatedon',
                ])

                ->orderByDesc('ld.fromdate')
                ->orderByDesc('td.updatedon')
                ->get();
            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Illuminate\Database\QueryException $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Database error',
                'error'   => $e->getMessage()
            ], 500);
        } catch (\Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Unexpected error',
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile()
            ], 500);
        }
    }

}
