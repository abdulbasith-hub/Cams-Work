<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\View;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class LagacyModel extends Model
{

    protected static $logfollowup_table = BaseModel::LOGFOLLOWUP_TABLE;
    protected static $deptartment_table = BaseModel::DEPARTMENT_TABLE;
    protected static $institution_table = BaseModel::INSTITUTION_TABLE;
    protected static $auditplan_table = BaseModel::AUDITPLAN_TABLE;
    protected static $temprankusers_table = BaseModel::TEMPRANKUSERS_TABLE;
    protected static $designation_table = BaseModel::DESIGNATION_TABLE;
    protected static $userdetail_table = BaseModel::USERDETAIL_TABLE;
    protected static $auditplanteam_table = BaseModel::AUDITPLANTEAM_TABLE;
    protected static $auditplanteammem_table = BaseModel::AUDITPLANTEAMMEM_TABLE;
    protected static $mstauditeeinscategory_table = BaseModel::MSTAUDITEEINSCATEGORY_TABLE;
    protected static $instauditschedule_table = BaseModel::INSTSCHEDULE_TABLE;
    protected static $instauditschedulemem_table = BaseModel::INSTSCHEDULEMEM_TABLE;
    protected static $yearcodemapping_table = BaseModel::MAPYEARCODE_TABLE;
    protected static $transaccountdetails_table = BaseModel::TRANSACCOUNTDETAILS_TABLE;
    protected static $accountparticulars_table = BaseModel::ACCOUNTPARTICULARS_TABLE;
    protected static $fileuploaddetail_table = BaseModel::FILEUPLOAD_TABLE;
    protected static $transcallforrec_table = BaseModel::TRANSCALLFORRECORDS_TABLE;
    protected static $automate_function = BaseModel::AUTOMATE_FUNCTION;
    protected static $finaliseplan_function = BaseModel::FINALISEPLAN_FUNCTION;
    protected static $auditquarter_table = BaseModel::AUDITQUARTER_TABLE;
    protected static $subcategory_table = BaseModel::SUBCATEGORY_TABLE;
    protected static $typeofaudit_table = BaseModel::TYPEOFAUDIT_TABLE;
    protected static $mapallocationobjection_table = BaseModel::MAPALLOCATIONOBJECTION_TABLE;
    protected static $mainobjection_table = BaseModel::MAINOBJECTION_TABLE;
    protected static $subobjection_table = BaseModel::SUBOBJ_TABLE;
    protected static $lagacy_table = BaseModel::LAGACY_TABLE;

    protected static $region_table = BaseModel::REGION_TABLE;
    protected static $dist_table = BaseModel::DIST_Table;
    protected static $transpara_table = BaseModel::TRANSPARA_TABLE;
    protected static $parafileupload_table = BaseModel::PARAFILEUPLOAD_TABLE;
    protected static $historytranspara_table = BaseModel::HISTORYTRANSPARA_TABLE;
    protected static $actionpara_table = BaseModel::ACTIONPARA_TABLE;
    //------------------------Auditslip-----------------------------------
    protected static $liability_table = BaseModel::LIABILITY_TABLE;
    protected static $slipfileupload_table = BaseModel::SLIPFILEUPLOAD_TABLE;

    protected static $apms_hlc_table = BaseModel::APMS_HLC_TABLE;
    protected static $irregularirites_table = BaseModel::IRREGULARITIES_TABLE;
    protected static $irregularirites_category_table = BaseModel::IRREGULARITIESCATEGORY_TABLE;
    protected static $irregularirites_subcategory_table = BaseModel::IRREGULARITIESSUBCATEGORY_TABLE;


    protected static $userchargedetails_table = BaseModel::USERCHARGEDETAIL_TABLE;
    protected static $chargedetails_table = BaseModel::CHARGEDETAIL_TABLE;
    protected static $roletypeTable = BaseModel::ROLETYPE_TABLE;
    protected static $roletypemappingTable = BaseModel::ROLETYPEMAPPING_TABLE;
    protected static $roleactionTable = BaseModel::ROLEACTION_TABLE;
    protected static $rolemappingTable = BaseModel::ROLEMAPPING_TABLE;


    protected static $auditeeuserdetails_table = BaseModel::AUDITEEUSERDETAIL_TABLE;
    protected static $leave_table = BaseModel::INDLEAVEDETAIL_TABLE;
    protected static $severity_table = BaseModel::SEVERITY_TABLE;




    protected static $auditperiod_table = BaseModel::AUDITPERIOD_TABLE;
    protected static $callforrec_table = BaseModel::CALLFORRECORDS_AUDITEE_TABLE;
    protected static $auditeescheme_table = BaseModel::AUDITEESCHEME_TABLE;


    //------------------------lagacy-----------------------------------
    protected static $transfollowup_table = BaseModel::TRANSFOLLOWUP_TABLE;
    protected static $followupliability_table = BaseModel::FOLLOWUPLIABILITY_TABLE;
    protected static $lagacyfielupload_table = BaseModel::LAGACYFILEUPLOAD_TABLE;
    protected static $mst_stateofpara_table = BaseModel::MST_STATEOFPARA_TABLE;
    protected static $mst_typeofpara_table = BaseModel::MST_TYPEOFPARA_TABLE;


    public static function commondeptfetch()
    {
        return DB::table(self::$deptartment_table . ' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname') // Select required columns
            ->where('dept.statusflag', '=', 'Y') // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }



    public static function getinstbasedonsubcat($district, $regioncode, $deptcode, $catcode, $subcatcode)
    {
        $table = self::$institution_table;

        return DB::table($table  . ' as ins')

            ->select('ins.instename', 'ins.insttname', 'ins.instid')
            ->distinct()
            ->where('ins.distcode', $district)
            ->where('ins.deptcode', $deptcode)
            ->where('ins.regioncode', $regioncode)
            ->where('ins.catcode', $catcode)
            ->where('ins.subcatid', $subcatcode)
            ->where('ins.statusflag', 'Y')

            ->get();
    }


    public static function getcategoryBydistrictchange($district, $regioncode, $deptcode)
    {
        $table = self::$institution_table;

        return DB::table($table . ' as ins')

            ->join(self::$mstauditeeinscategory_table . ' as cat', 'ins.catcode', '=', 'cat.catcode')
            ->select('cat.catcode', 'cat.catename', 'cat.cattname', 'cat.if_subcategory')
            ->distinct()
            ->where('ins.distcode', $district)
            ->where('ins.deptcode', $deptcode)
            ->where('ins.regioncode', $regioncode)
            ->where('cat.statusflag', 'Y')
            ->where('cat.deptcode', $deptcode)

            ->get();
    }


    // public static function getcategoryByDept($deptcode)
    // {
    //     return DB::table(self::$mstauditeeinscategory_table)
    //         ->where('deptcode', $deptcode)

    //         ->where('statusflag', 'Y')
    //         ->get(['catcode', 'catename', 'cattname','if_subcategory']);
    // }



    // public static function getcategoryByDept($deptcode)
    // {
    //     return DB::table(self::$mstauditeeinscategory_table)
    //         ->where('deptcode', $deptcode)

    //         ->where('statusflag', 'Y')
    //         ->get(['catcode', 'catename', 'cattname','if_subcategory']);
    // }


    public static function getSubcategoryByCategory($category)
    {
        $table = self::$mstauditeeinscategory_table;

        return DB::table($table . ' as aud')
            ->leftJoin(self::$subcategory_table . ' as sub', 'aud.catcode', '=', 'sub.catcode')
            ->select('sub.subcatename', 'sub.subcattname', 'sub.auditeeins_subcategoryid', 'aud.if_subcategory', 'aud.catcode', 'aud.catename', 'aud.if_subcategory', 'aud.cattname')
            ->where('sub.catcode', $category)
            ->where('aud.if_subcategory', 'Y')
            ->orderBy('sub.subcatename', 'Asc')
            //  dd($date);
            ->get();
    }


    public static function getRegionsByDept($deptcode)
    {
        $table = self::$institution_table;

        return DB::table($table . ' as ins')
            ->join(self::$region_table . ' as reg', 'ins.regioncode', '=', 'reg.regioncode')
            ->select('reg.regioncode', 'reg.regionename', 'reg.regiontname')
            ->distinct()
            ->where('ins.deptcode', $deptcode)
            ->where('ins.statusflag', 'Y')
            ->orderBy('reg.regionename', 'Asc')
            ->get();
    }


    public static function getdistrictByregion($regioncode, $deptcode)
    {
        $table = self::$institution_table;

        return DB::table($table . ' as ins')
            ->join(self::$dist_table . ' as dis', 'ins.distcode', '=', 'dis.distcode')
            ->select('dis.distename', 'dis.distcode', 'dis.disttname')
            ->distinct()
            ->where('ins.deptcode', $deptcode)
            ->where('ins.regioncode', $regioncode)
            ->where('ins.statusflag', 'Y')
            ->get();
    }



    //---------------------------extra for followup-----------------------------

    public static function regionfetch()
    {
        $table = self::$region_table;

        return DB::table($table . ' as reg')

            ->select('reg.regioncode', 'reg.regionename', 'reg.regiontname')
            ->distinct()
            ->where('reg.statusflag', 'Y')
            ->orderBy('reg.regionename', 'Asc')
            ->get();
    }

    public static function districtfetch()
    {
        $table = self::$dist_table;

        return DB::table($table . ' as dis')
            ->select('dis.distename', 'dis.distcode', 'dis.disttname')
            ->distinct()
            ->where('dis.statusflag', 'Y')
            ->get();
    }


    public static function followup_dropdown($instid)
    {

        print_r($instid);
        exit;


        $sessiondet = session('charge');
        $sessiondeptcode =  $sessiondet->deptcode;

        $instdet = DB::table(self::$institution_table . ' as ins')
            ->where('ins.statusflag', 'Y')
            ->where('instid', $instid)
            ->select('ins.instid', 'ins.catcode', 'ins.subcatid', 'ins.instename', 'ins.insttname', 'ins.deptcode');

        $inst      = $instdet->get();
        $instData  = $inst->first();

        $catId     = $instData->catcode;
        $subcatId  = $instData->subcatid;

        // return 'asd';
        $catDet = DB::table(self::$mstauditeeinscategory_table . ' as cat')
            ->leftJoin(self::$subcategory_table . ' as sub', 'cat.catcode', '=', 'sub.catcode')
            ->where('cat.statusflag', 'Y')
            ->where('cat.catcode', $catId)
            ->get();

        $issubcat = optional($catDet->first())->if_subcategory;

        if ($issubcat === 'Y') {
            $subcatDet = DB::table(self::$subcategory_table . ' as sub')
                ->where('sub.statusflag', 'Y')
                ->where('sub.catcode', $catId)
                ->where('sub.auditeeins_subcategoryid', $subcatId)
                ->get();
        } else {
            $subcatDet = collect();
        }

        $typeofpara = DB::table(self::$mst_typeofpara_table)
            ->select('typeofparacode', 'typeofparaename', 'typeofparatname')
            ->where('statusflag', 'Y')
            ->orderBy('orderid', 'asc')
            ->get();

        $stateofpara = DB::table(self::$mst_stateofpara_table)
            ->select('stateofparacode', 'stateofparaename', 'stateofparatname')
            ->where('statusflag', 'Y')
            ->orderBy('orderid', 'asc')
            ->get();

        $typeofaudit = DB::table(self::$typeofaudit_table)
            ->select('typeofauditcode', 'typeofauditename', 'typeofaudittname')
            ->where('deptcode', $sessiondeptcode)
            ->where('statusflag', 'Y')
            ->whereIn('typeofauditcode', View::shared('financialaudit'))
            ->orderBy('typeofauditename', 'asc')
            ->get();

        $yearofaudit = DB::table(self::$auditperiod_table)
            ->select(
                'auditperiodid',
                DB::raw("CONCAT(fromyear, ' - ', toyear) as audit_period")
            )
            ->where('deptcode', $sessiondeptcode)
            ->where('statusflag', 'Y')
            ->where('financestatus', 'N')
            ->whereIn('lagacyyear', ['Y', 'B'])
            ->orderBy('fromyear', 'asc')
            ->get();

        $fasliyear = DB::table(self::$auditperiod_table)
            ->select(
                'auditperiodid',
                DB::raw("CONCAT(fromyear, ' - ', toyear) as audit_period")
            )
            ->where('deptcode', $sessiondeptcode)
            ->where('statusflag', 'Y')
            ->where('financestatus', 'N')
            ->where('lagacyyear', 'Y')
            ->orderBy('fromyear', 'asc')
            ->get();

        $objection  = DB::table(self::$mainobjection_table . ' as mainobj')

            ->where('mainobj.deptcode', $instData->deptcode)
            ->where('mainobj.statusflag', 'Y')
            ->orderBy('mainobj.mainobjectionid', 'asc')
            ->distinct('mainobj.mainobjectionid')
            ->get();


        $data = [
            'inst'          => $inst,
            'catDet'         => $catDet,
            'subcatDet'     => $subcatDet,
            'typeofaudit'   => $typeofaudit,
            'objection'     => $objection,
            'yearofaudit'     => $yearofaudit,
            'stateofpara'     => $stateofpara,
            'typeofpara'      => $typeofpara,

        ];

        return $data;
    }


    public static function getminorobjection($mainobjectionid)
    {
        return DB::table(self::$subobjection_table . ' as subobj')
            // ->join(self::$mapallocationobjection_table . ' as map', 'subobj.mainobjectionid', '=', 'map.mainobjectionid')
            // ->join(self::$mainobjection_table . ' as mainobj', 'map.mainobjectionid', '=', 'mainobj.mainobjectionid')

            ->where('subobj.mainobjectionid', $mainobjectionid)
            ->where('subobj.statusflag', 'Y')
            ->get();
    }

    public static function createorinsertLagacydet($data, $lagacyid)
    {
        $table = self::$transfollowup_table;
        $query = DB::table($table);
        if ($lagacyid) {
            $query->where('followupid', '!=', $lagacyid);
        }

        $existingdata = (clone $query)
            ->where('instid', $data['instid'])
            ->where('typeofauditcode', $data['typeofauditcode'])
            ->whereJsonContains('audityear', (int) $data['audityear'])
            ->where('parano', $data['parano'])
            ->where('paratype', View::shared('lagacyparatype'))
            ->whereIn('statusflag', ['Y', 'F'])
            ->first();

        if ($existingdata) {
            throw new \Exception('The lagacy details for this instituion was already exists');
        }
        if ($lagacyid) {

            $affectedRows = DB::table($table)
                ->where('followupid', $lagacyid)
                ->update($data);

            if ($affectedRows === 0) {
                throw new \Exception('Failed to update the record.');
            }

            $insertedRecord = DB::table($table)
                ->select('paranumber', 'followupid', 'audityear')
                ->where('followupid', $lagacyid)
                ->first();
            return $insertedRecord;
        } else {
            // return $data['instid'];
            $maxId = DB::table($table)
                ->where('instid', $data['instid'])
                ->whereJsonContains('audityear', json_decode($data['audityear']))
                ->where('typeofauditcode', $data['typeofauditcode'])
                ->whereIn('statusflag', ['Y', 'F'])
                ->max('paranumber');

            $maxId = $maxId ?? 0;
            $code = $maxId + 1;
            $maxserialNo = DB::table($table)

                ->max('followupid');
            $max = $maxserialNo ?? 0;
            $maxNo = $max + 1;
            $data['paratype'] = View::shared('lagacyparatype');
            $data['auditplanid'] =   $maxNo;
            $data['auditscheduleid'] = $maxNo;
            $data['paranumber'] = $code;
            $newRecordId = DB::table($table)->insertGetId($data, 'followupid');
            $insertedRecord = DB::table($table)
                ->select('paranumber', 'followupid', DB::raw('(audityear->>0)::int as audityear'))
                ->where('followupid', $newRecordId)
                ->first();
            if (!$newRecordId) {
                throw new \Exception('Failed to insert the new record.');
            }
            return $insertedRecord; // Return the ID of the newly inserted record
        }
    }


    public static function fetch_lagacydata($followupid, $instid, $action, $yearcode)

    {

        $table = self::$transfollowup_table;

        $fileAgg = DB::table('audit.lagacyfileupload as t3')
            ->leftJoin('audit.fileuploaddetail as t2', 't2.fileuploadid', '=', 't3.fileuploadid')
            ->select(
                't3.followupid',
                DB::raw("
            STRING_AGG(
                DISTINCT CONCAT(
                    COALESCE(t2.filename, ''), '-',
                    COALESCE(t2.filepath, ''), '-',
                    COALESCE(t2.filesize::TEXT, ''), '-',
                    COALESCE(t2.fileuploadid::TEXT, '')
                ), ','
            ) AS auditorfileupload
        ")
            )
            ->where('t3.statusflag', 'Y')
            ->groupBy('t3.followupid');

        $liabAgg = DB::table('audit.followup_liability as l')
            ->select(
                'l.followupid',
                DB::raw("
            STRING_AGG(
                DISTINCT CONCAT(
                    COALESCE(l.notype, ''), '-',
                    COALESCE(l.liabilitygpfno, ''), '-',
                    COALESCE(l.liabilityname, ''), '-',
                    COALESCE(l.liabilitydesignation::TEXT, ''), '-',
                    COALESCE(l.liabilityamount::TEXT, ''), '-',
                    COALESCE(l.followupliabilityid::TEXT, ''), '-',
                    COALESCE(l.statusflag::TEXT, '')
                ), ','
            ) AS liabilitydel
        ")
            )
            ->whereIn('l.statusflag', ['Y', 'C'])
            ->groupBy('l.followupid');


        $query = DB::table($table . ' as fp')
            ->join(self::$typeofaudit_table . ' as type', 'type.typeofauditcode', '=', 'fp.typeofauditcode')
            ->join(self::$mainobjection_table . ' as main', 'main.mainobjectionid', '=', 'fp.mainobjectionid')
            ->join(self::$subobjection_table . ' as sub', 'sub.subobjectionid', '=', 'fp.subobjectionid')
            ->join('audit.mst_irregularitiessubcategory as subcat', 'subcat.irregularitiessubcatcode', '=', 'fp.irregularitiessubcatcode')
            ->join('audit.mst_irregularitiescategory as cat', 'cat.irregularitiescatcode', '=', 'fp.irregularitiescatcode')
            ->join('audit.mst_irregularities as ir', 'ir.irregularitiescode', '=', 'fp.irregularitiescode')
            ->leftJoin('audit.auditeescheme as scheme', 'scheme.auditeeschemecode', '=', 'fp.auditeeschemecode')
            ->leftJoin('audit.deptuserdetails as cb', 'cb.deptuserid', '=', 'fp.createdby')
            // ->leftjoin(self::$followupliability_table . ' as l', 'l.followupid', '=', 'fp.followupid')
            ->leftJoinSub($fileAgg, 'files', 'files.followupid', '=', 'fp.followupid')
            ->leftJoinSub($liabAgg, 'liab', 'liab.followupid', '=', 'fp.followupid');


        if ($action == 'edit') {
            $query  = $query->select(
                'fp.liability',
                'fp.typeofparacode',
                'fp.stateofparacode',
                'fp.parano',
                'fp.lastactionyear',
                'fp.lastactionmonth',
                'fp.statusflag',
                'fp.followupid',
                'fp.paranumber',
                'fp.subobjectionid',
                'fp.mainobjectionid',
                'fp.amtinvolved',
                'fp.slipdetails',
                'fp.schemastatus',
                'fp.processcode',
                'fp.createdby',
                'fp.updatedon',
                'fp.severitycode',
                'type.typeofauditename',
                'type.typeofaudittname',
                'main.objectionename',
                'sub.subobjectionename',
                'cat.irregularitiescatcode',
                'subcat.irregularitiessubcatcode',
                'scheme.auditeeschemecode',
                'ir.irregularitiescode',
                'cb.username as createdbyusername',
                DB::raw('(fp.audityear->>0)::int as audityear'),
                DB::raw("COALESCE(fp.remarks::json->>'content', '') AS remarks"),
                'files.auditorfileupload',
                'liab.liabilitydel',
                DB::raw("
        CASE
            WHEN EXISTS (
                SELECT 1
                FROM (
                    SELECT
                        fp2.followupid,
                        COUNT(*) OVER (
                            PARTITION BY fp2.instid, f2.filename
                        ) AS file_count
                    FROM audit.trans_followup fp2
                    JOIN audit.lagacyfileupload lf2
                        ON lf2.followupid = fp2.followupid
                    JOIN audit.fileuploaddetail f2
                        ON f2.fileuploadid = lf2.fileuploadid
                    WHERE fp2.instid = fp.instid
                ) x
                WHERE x.followupid = fp.followupid
                  AND x.file_count > 1
            )
            THEN 'Y'
            ELSE 'N'
        END AS duplicate_file
    "),
                'fp.typeofauditcode',
                DB::raw("
CASE
WHEN EXISTS (
SELECT 1
FROM audit.trans_para p
WHERE p.followupid = fp.followupid
) THEN 'Y'
ELSE 'N'
END AS in_para
")

            );
        } else {
            $query = $query->select(
                'fp.statusflag',
                'fp.followupid',
                'fp.paranumber',
                'fp.processcode',
                'fp.typeofauditcode',
                'type.typeofauditename',
                'fp.lastactionyear',
                'fp.lastactionmonth',
                'fp.typeofparacode',
                'fp.stateofparacode',
                DB::raw("
CASE
WHEN EXISTS (
SELECT 1
FROM audit.trans_para p
WHERE p.followupid = fp.followupid
) THEN 'Y'
ELSE 'N'
END AS in_para
")


            );
        }



        if ($action == 'edit') {
            $query->where('fp.followupid', $followupid);
        }

        if ($yearcode) {
            $query->whereJsonContains('fp.audityear', (int)$yearcode);
        }

        if ($instid) {
            $query->where('fp.instid', $instid);
        }

        $query
            ->where('fp.paratype', 'L')
            ->whereIn('fp.statusflag', ['Y', 'F'])
            ->orderBy('fp.followupid');

        return $query->get();

        // $querySql = $query->toSql();
        // $bindings = $query->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );
    }

    public static function getSchemename($catId, $subcatId)
    {
        try {
            $chargeData = session('charge');
            $session_deptcode = $chargeData->deptcode;
            return DB::table(self::$auditeescheme_table . ' as s')
                ->join(self::$deptartment_table . ' as dept', 's.deptcode', '=', 'dept.deptcode')
                ->join(self::$mstauditeeinscategory_table . ' as cat', 's.catcode', '=', 'cat.catcode')
                ->leftJoin(self::$subcategory_table . ' as sub', 's.auditeeins_subcategoryid', '=', 'sub.auditeeins_subcategoryid')
                ->where('s.statusflag', '=', 'Y')
                ->where('s.deptcode', '=', $session_deptcode)
                ->when($subcatId, function ($query) use ($subcatId) {
                    $query->where('s.auditeeins_subcategoryid', '=', $subcatId);
                })
                ->where('s.catcode', $catId)
                ->select('s.auditeeschemeelname', 's.auditeeschemecode', 's.auditeeschemetlname', 's.auditeeschemetsname', 's.auditeeschemeesname', 's.auditeeschemeid')
                ->orderBy('s.auditeeschemeelname', 'asc')
                ->get();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
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

    public static function deleteLiability($liabilityid, $session_userid)
    {
        if ($liabilityid > 0) {
            for ($i = 0; $i < count($liabilityid); $i++) {
                if ($liabilityid[$i]) {
                    DB::table(self::$followupliability_table)
                        ->where('followupliabilityid', $liabilityid[$i])
                        ->update(array('statusflag' => 'N', 'updatedby'  =>  $session_userid, 'updatedon' => View::shared('get_nowtime')));
                }
            }
        }
    }


    public static function insertupdateLiability($liabilityid, $notype, $name, $gpfno, $designation, $amount, $processcode, $followupid, $session_userid,)
    {

        $liabilityidcount  =   count($liabilityid);

        if ($liabilityidcount > 0) {

            for ($i = 0; $i < $liabilityidcount; $i++) {

                $data   = array(
                    'followupid'   =>  $followupid,
                    'notype'     =>  $notype[$i],
                    'liabilityname'     =>  $name[$i],
                    'liabilitygpfno'    =>  $gpfno[$i],
                    'liabilitydesignation'    =>  $designation[$i],
                    'liabilityamount'     =>  $amount[$i],
                    'lagacyflag'     =>  'Y',
                );

                if ($liabilityid[$i]) {

                    $data['statusflag']  =  'Y';



                    //  return $liabilityid[$i];

                    $data['updatedby']  =  $session_userid;
                    $data['updatedon']  = View::shared('get_nowtime');

                    DB::table(self::$followupliability_table)
                        ->where('followupliabilityid', $liabilityid[$i])
                        ->update($data);
                } else {
                    $data['statusflag']  =  'Y';
                    $data['createdby']  =  $session_userid;
                    $data['createdon']  = View::shared('get_nowtime');

                    DB::table(self::$followupliability_table)->insert(
                        $data
                    );
                }
            }

            if ($i <= count($name)) {
                for ($l = $i; $l < count($name); $l++) {
                    $data   = array(
                        'notype'     =>  $notype[$i],
                        'followupid'   =>  $followupid,
                        'liabilityname'     =>  $name[$l],
                        'liabilitygpfno'    =>  $gpfno[$l],
                        'liabilitydesignation'    =>  $designation[$l],
                        'liabilityamount'     =>  $amount[$i],
                        'lagacyflag'     =>  'Y',
                        'statusflag'  => 'Y',
                        'createdby'  =>  $session_userid,
                        'createdon'  =>  View::shared('get_nowtime'),
                    );
                    DB::table(self::$followupliability_table)->insert(
                        $data
                    );
                }
            }
        }
    }



    public static function fetch_paradata($followupid, $paraid, $userid, $sessionusertypecode)
    {
        try {
            $session_charge = session('charge');

            $sessionroleactioncode = $sessionusertypecode == View::shared('auditeelogin') ? '' : $session_charge->roleactioncode;
            $statusFlags = ['Y'];  // default

            if ($sessionroleactioncode == view::shared('PUroleactioncode')) {
                $statusFlags = ['Y'];   // example
            } elseif ($sessionroleactioncode ==  view::shared('PUADroleactioncode')) {
                $statusFlags = ['Y',];
            }
            $statusFlagsStr = "'" . implode("','", $statusFlags) . "'";

            if ($sessionusertypecode)
                // Base para query: each para row once
                $query = DB::table(self::$transpara_table . ' as para')
                    ->leftJoin(self::$actionpara_table . ' as act', 'para.actioncode', '=', 'act.actioncode')
                    ->leftjoin(self::$followupliability_table . ' as l', 'l.followupid', '=', 'para.followupid')
                    // LATERAL subquery to aggregate files for this para (created by $userid)
                    ->leftJoin(DB::raw("
                LATERAL (
                    SELECT STRING_AGG(
                        CONCAT(
                            COALESCE(t4.filename, ''), '-',
                            COALESCE(t4.filepath, ''), '-',
                            COALESCE(t4.filesize::TEXT, ''), '-',
                            COALESCE(t4.fileuploadid::TEXT, '')
                        ), ','
                    ) AS fileuploaddet
                    FROM " . self::$parafileupload_table . " pfile
                    JOIN audit.fileuploaddetail t4 ON t4.fileuploadid = pfile.fileuploadid
                    WHERE pfile.statusflag = 'Y'
                      AND pfile.createdby = {$userid}
                      AND pfile.paraid = para.paraid
                      AND pfile.rejoinderstatus IS NOT DISTINCT FROM para.rejoinderstatus
                      AND pfile.rejoindercycle IS NOT DISTINCT FROM para.rejoindercycle
                      AND pfile.rejectcount IS NOT DISTINCT FROM para.rejectcount
                      AND pfile.processcode = para.processcode
                ) AS pfiles
            "), DB::raw('true'), DB::raw('true'))
                    ->select(
                        'para.auditee_liability',
                        'para.rejectcount',
                        'para.audityear',
                        'para.rejoindercycle',
                        'para.rejoinderstatus',
                        'para.actroleactioncode',
                        'para.actioncode',
                        'para.updatedby',
                        'para.paraid',
                        'para.processcode',

                        'para.paratype',
                        'act.actionename',
                        'act.actiontname',
                        DB::raw("
                        STRING_AGG(
                            DISTINCT CASE
                                WHEN l.statusflag IN ($statusFlagsStr) THEN
                                    CONCAT(
                                        COALESCE(l.notype, ''), '|~|',
                                        COALESCE(l.liabilitygpfno, ''), '|~|',
                                        COALESCE(l.liabilityname, ''), '|~|',
                                        COALESCE(l.liabilitydesignation::TEXT, ''), '|~|',
                                        COALESCE(l.liabilityamount::TEXT, ''), '|~|',
                                        COALESCE(l.followupliabilityid::TEXT, ''), '|~|',
                                        COALESCE(l.remarks::TEXT, ''), '|~|',
                                        COALESCE(l.statusflag::TEXT, ''), '|~|',
                                        COALESCE(l.retiredflag::TEXT, ''), '|~|',
                                        COALESCE(l.retirementyear, ''), '|~|',
                                       COALESCE(l.retirementmonth::TEXT, ''),'|~|',
          COALESCE(l.lagacyflag::TEXT, '')
                                    )
                                ELSE NULL
                            END, ','
                        ) AS liabilitydel
                    "),

                        DB::raw("COALESCE(para.para_remarks::json->>'content', '') AS para_remarks"),
                        DB::raw("COALESCE(pfiles.fileuploaddet, '') AS fileuploaddet")
                    );

            $query->when($followupid, function ($q) use ($followupid) {
                $q->where('para.followupid', '=', $followupid);
            });

            $query->when($paraid, function ($q) use ($paraid) {
                $q->where('para.paraid', '=', $paraid);
            });
            $query->groupBy(
                'para.auditee_liability',
                'para.liabilty_type',
                'pfiles.fileuploaddet',
                'para.rejectcount',
                'para.audityear',
                'para.rejoindercycle',
                'para.rejoinderstatus',
                'para.actroleactioncode',
                'para.actioncode',
                'para.updatedby',
                'para.paraid',
                'para.processcode',

                'para.paratype',
                'act.actionename',
                'act.actiontname',

            );
            // HISTORY query: get each history row once + aggregated files for that history row
            $historydata = DB::table(self::$historytranspara_table . ' as hist')
                ->join(self::$transpara_table . ' as para', 'para.paraid', '=', 'hist.paraid')
                ->leftJoin(self::$actionpara_table . ' as act', 'hist.actioncode', '=', 'act.actioncode')
                ->leftJoin(self::$userdetail_table . ' as ud', 'hist.forwardedbyuserid', '=', 'ud.deptuserid')
                ->leftJoin(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'ud.desigcode')
                ->leftJoin(self::$auditeeuserdetails_table . ' as ad', 'hist.forwardedbyuserid', '=', 'ad.auditeeuserid')
                // LATERAL subquery to aggregate files for this history row (created by hist.forwardedbyuserid)
                ->leftJoin(DB::raw("
                  LATERAL (
                      SELECT STRING_AGG(
                          CONCAT(
                              COALESCE(t4.filename, ''), '-',
                              COALESCE(t4.filepath, ''), '-',
                              COALESCE(t4.filesize::TEXT, ''), '-',
                              COALESCE(t4.fileuploadid::TEXT, '')
                          ), ','
                    ) AS auditeefileupload
                    FROM " . self::$parafileupload_table . " pfile
                    JOIN audit.fileuploaddetail t4 ON t4.fileuploadid = pfile.fileuploadid
                    WHERE pfile.statusflag = 'Y'
                      AND pfile.createdby = hist.forwardedbyuserid
                      AND pfile.processcode = hist.processcode
                      AND pfile.paraid = hist.paraid
                      AND pfile.rejoinderstatus IS NOT DISTINCT FROM hist.rejoinderstatus
                      AND pfile.rejoindercycle IS NOT DISTINCT FROM hist.rejoindercycle
                      AND pfile.rejectcount IS NOT DISTINCT FROM hist.rejectcount
                ) AS hfiles
            "), DB::raw('true'), DB::raw('true'))
                ->select(
                    'act.actionename',
                    'act.actiontname',
                    'hist.instid',
                    'hist.para_remarks',
                    'hist.processcode',
                    'hist.rejoinderstatus',
                    'hist.rejoindercycle',
                    'hist.forwardedbyuserid',
                    'hist.forwardedtochargeid',
                    'hist.forwardedon',
                    'hist.createdby',
                    'hist.createdon',
                    'hist.usertypecode',
                    'hist.forwardedtouserid',
                    'hist.forwardedbychargeid',
                    'hist.actioncode',
                    'hist.actroleactioncode',
                    'hist.paratype',
                    'desig.desigesname',
                    'hist.rejectcount',
                    'hist.remarks',
                    DB::raw("COALESCE(hist.para_remarks::json->>'content', '') AS para_historyremarks"),
                    'ad.username as auditeename',
                    'ud.username',
                    DB::raw("COALESCE(hfiles.auditeefileupload, '') AS auditeefileupload")
                )
                ->where('hist.statusflag', 'Y')
                ->orderBy('hist.transparahistoryid', 'asc');

            $historydata->when($followupid, function ($q) use ($followupid) {
                $q->where('para.followupid', '=', $followupid);
            });

            $historydata->when($paraid, function ($q) use ($paraid) {
                $q->where('para.paraid', '=', $paraid);
            });

            $data = [
                'data' => $query->get(),
                'historydata' => $historydata->get()
            ];

            return $data;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while fetching history details';
            throw new \Exception($e->getMessage(), 500);
            // throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }


    public static function fetch_lagacy_paradata($paraid, $paratype, $followupid)
    {
        try {
            $table = self::$transpara_table;

            $session_charge = session('charge');
            $usertypecode = $session_charge->usertypecode;

            // Base query and joins (no pfile/t4/t2/t3/l joins that would force GROUP BY)
            $query = DB::table($table . ' as para')
                ->join(self::$transfollowup_table . ' as fp', 'para.followupid', '=', 'fp.followupid')
                ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'para.instid')
                ->join(self::$region_table . ' as reg', 'inst.regioncode', '=', 'reg.regioncode')
                ->join(self::$dist_table . ' as dist', 'inst.distcode', '=', 'dist.distcode')
                ->join(self::$mstauditeeinscategory_table . ' as catm', 'inst.catcode', '=', 'catm.catcode')
                ->leftJoin(self::$subcategory_table . ' as subm', 'inst.subcatid', '=', 'subm.auditeeins_subcategoryid')
                ->join(self::$typeofaudit_table . ' as type', 'type.typeofauditcode', '=', 'fp.typeofauditcode')
                ->join(self::$mainobjection_table . ' as main', 'main.mainobjectionid', '=', 'fp.mainobjectionid')
                ->join(self::$subobjection_table .  ' as sub', 'sub.subobjectionid', '=', 'fp.subobjectionid')
                ->join('audit.mst_irregularitiessubcategory as subcat', 'subcat.irregularitiessubcatcode', '=', 'fp.irregularitiessubcatcode')
                ->join('audit.mst_irregularitiescategory as cat', 'cat.irregularitiescatcode', '=', 'fp.irregularitiescatcode')
                ->join('audit.mst_irregularities as ir', 'ir.irregularitiescode', '=', 'fp.irregularitiescode')
                ->leftJoin('audit.auditeescheme as scheme', 'scheme.auditeeschemecode', '=', 'fp.auditeeschemecode')
                ->leftJoin('audit.deptuserdetails as cb', 'cb.deptuserid', '=', 'fp.createdby');

            // Build auditor file lateral subquery (different source depending on paratype)
            if ($paratype == View::shared('lagacyparatype')) {
                // legacy files are linked by fp.followupid -> lagacyfielupload.followupid
                $auditorFilesLateral = "
                LATERAL (
                    SELECT STRING_AGG(
                        CONCAT(
                            COALESCE(t2.filename, ''), '-',
                            COALESCE(t2.filepath, ''), '-',
                            COALESCE(t2.filesize::TEXT, ''), '-',
                            COALESCE(t2.fileuploadid::TEXT, '')
                        ), ','
                    ) AS auditorfileupload
                    FROM " . self::$lagacyfielupload_table . " t3
                    JOIN audit.fileuploaddetail t2 ON t2.fileuploadid = t3.fileuploadid
                    WHERE t3.statusflag = 'Y'
                      AND t3.followupid = fp.followupid
                ) AS auditorfiles
            ";
                // liability lateral: linked by l.followupid

            } else { // normalparatype
                $auditorFilesLateral = "
                LATERAL (
                    SELECT STRING_AGG(
                        CONCAT(
                            COALESCE(t2.filename, ''), '-',
                            COALESCE(t2.filepath, ''), '-',
                            COALESCE(t2.filesize::TEXT, ''), '-',
                            COALESCE(t2.fileuploadid::TEXT, '')
                        ), ','
                    ) AS auditorfileupload
                    FROM " . self::$slipfileupload_table . " t3
                    JOIN audit.fileuploaddetail t2 ON t2.fileuploadid = t3.fileuploadid
                    WHERE t3.statusflag = 'Y'
                      AND t3.auditslipid = fp.auditslipid
                ) AS auditorfiles
            ";
                // liability lateral: linked by l.auditslipid, returns liabilityid instead of followupliabilityi
            }
            $liabilityLateral = "
                LATERAL (
                    SELECT STRING_AGG(
                        CONCAT(
                            COALESCE(l.notype, ''), '-',
                            COALESCE(l.liabilitygpfno, ''), '-',
                            COALESCE(l.liabilityname, ''), '-',
                            COALESCE(l.liabilitydesignation::TEXT, ''), '-',
                            COALESCE(l.liabilityamount::TEXT, ''), '-',
                            COALESCE(l.followupliabilityid::TEXT, ''), '-',
                             COALESCE(l.remarks::TEXT, ''), '-',
                            COALESCE(l.statusflag::TEXT, '-'),'-',
                            COALESCE(l.retiredflag::TEXT, ''), '-',
                            COALESCE(l.retirementyear, ''), '-',
                            COALESCE(l.retirementmonth::TEXT, '')

                        ), ','
                    ) AS liabilitydel
                    FROM " . self::$followupliability_table . " l
                    WHERE (l.statusflag = 'Y' OR l.statusflag = 'C')
                      AND l.followupid = fp.followupid
                ) AS liabilityfiles
            ";
            // Auditee file lateral (files uploaded against para)
            $auditeeFilesLateral = "
            LATERAL (
                SELECT STRING_AGG(
                    CONCAT(
                        COALESCE(t4.filename, ''), '-',
                        COALESCE(t4.filepath, ''), '-',
                        COALESCE(t4.filesize::TEXT, ''), '-',
                        COALESCE(t4.fileuploadid::TEXT, '')
                    ), ','
                ) AS auditeefileupload
                FROM " . self::$parafileupload_table . " pfile
                JOIN audit.fileuploaddetail t4 ON t4.fileuploadid = pfile.fileuploadid
                WHERE pfile.statusflag = 'Y'
                  AND pfile.paraid = para.paraid
            ) AS auditeefiles
        ";

            // Attach lateral joins
            $query->leftJoin(DB::raw($auditorFilesLateral), DB::raw('true'), DB::raw('true'));
            $query->leftJoin(DB::raw($auditeeFilesLateral), DB::raw('true'), DB::raw('true'));
            $query->leftJoin(DB::raw($liabilityLateral), DB::raw('true'), DB::raw('true'));

            // Selects (use COALESCE on lateral fields)
            $query->select(
                'para.rejectcount',
                'para.paratype',
                'inst.instid',
                'inst.instename',
                'inst.insttname',
                'reg.regionename',
                'reg.regiontname',
                'dist.distename',
                'dist.distename', // kept as in your original; check if one should be dist.tname
                'catm.catename',
                'catm.cattname',
                'subm.subcatename',
                'subm.subcattname',
                'fp.typeofparacode',
                'fp.stateofparacode',
                'fp.parano as paranumber',
                'fp.lastactionyear',
                'fp.lastactionmonth',
                'fp.followupid',
                'fp.schemastatus',
                'fp.subobjectionid',
                'fp.mainobjectionid',
                'fp.amtinvolved',
                'fp.slipdetails',
                'fp.severitycode',
                'para.paraid',
                'para.statusflag as statusflag',
                'para.processcode',
                DB::raw('(para.audityear) as audityear'),
                DB::raw("COALESCE(auditorfiles.auditorfileupload, '') AS auditorfileupload"),
                DB::raw("COALESCE(auditeefiles.auditeefileupload, '') AS auditeefileupload"),
                DB::raw("COALESCE(para.para_remarks::json->>'content', '') AS para_remarks"),
                DB::raw("COALESCE(fp.remarks::json->>'content', '') AS remarks"),
                'subcat.irregularitiessubcatcode',
                'cat.irregularitiescatcode',
                'scheme.auditeeschemecode',
                'ir.irregularitiescode',
                'fp.liability',
                'cb.username AS createdbyusername',
                'type.typeofauditcode',
                'type.typeofauditename',
                'type.typeofaudittname',
                'main.mainobjectionid',
                'main.objectionename',
                'main.objectiontname',
                'sub.subobjectionid',
                'sub.subobjectionename',
                'sub.subobjectiontname',
                DB::raw("COALESCE(liabilityfiles.liabilitydel, '') AS liabilitydel")
            );

            // Filters
            $query->where('para.paratype', '=', $paratype);
            $query->when($paraid, function ($q) use ($paraid) {
                $q->where('para.paraid', '=', $paraid);
            });
            $query->when($followupid, function ($q) use ($followupid) {
                $q->where('fp.followupid', '=', $followupid);
            });
            $query->orderBy('para.paraid', 'asc');
            // $querySql = $query->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);
            $data = [
                'data' => $query->get()
            ];

            return $data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }








    public static function auditee_dropdown($instid, $paradets)
    {

        try {
            $session = session('charge');
            $sessionUserType = $session->usertypecode;
            $sessionDept = $session->deptcode;

            //-----instdetails------------//
            $instData = DB::table(self::$institution_table . ' as ins')
                ->where([
                    ['ins.statusflag', 'Y'],
                    ['ins.instid', $instid],
                ])
                ->select('ins.instid', 'ins.catcode', 'ins.subcatid', 'ins.instename', 'ins.insttname', 'ins.deptcode')
                ->first();

            if (!$instData) {
                return []; // Defensive return
            }

            $catId = $instData->catcode;
            $subcatId = $instData->subcatid;

            //-----Category------------//
            $catDet = DB::table(self::$mstauditeeinscategory_table . ' as cat')
                ->leftJoin(self::$subcategory_table . ' as sub', 'cat.catcode', '=', 'sub.catcode')
                ->select('cat.catcode', 'cat.catename', 'cat.cattname', 'cat.if_subcategory')
                ->where([
                    ['cat.statusflag', 'Y'],
                    ['cat.catcode', $catId],
                ])
                ->get();

            $issubcat = optional($catDet->first())->if_subcategory;
            //-----Sub-Category------------//
            $subcatDet = collect();
            if ($issubcat === 'Y') {
                $subcatDet = DB::table(self::$subcategory_table . ' as sub')
                    ->where([
                        ['sub.statusflag', 'Y'],
                        ['sub.catcode', $catId],
                        ['sub.auditeeins_subcategoryid', $subcatId],
                    ])
                    ->select('sub.auditeeins_subcategoryid', 'sub.subcatename', 'sub.subcattname')
                    ->get();
            }
            //-----Lagacy Year------------//
            $yearofaudit = DB::table(self::$auditperiod_table . ' as yr')
                ->join(self::$transfollowup_table . ' as fp', function ($join) {
                    $join->whereRaw("
            yr.auditperiodid IN (
                SELECT value::INT
                FROM jsonb_array_elements_text(fp.audityear)
            )
        ");
                })
                ->select(
                    'yr.auditperiodid',
                    DB::raw("CONCAT(yr.fromyear, ' - ', yr.toyear) as audit_period"),
                    DB::raw("COUNT(fp.followupid) as total_paras")
                )
                ->where([
                    ['fp.statusflag', 'F'],
                    ['fp.instid', $instid],
                    ['yr.statusflag', 'Y'],
                    ['yr.deptcode', $sessionDept],
                    ['yr.financestatus', 'N'],
                ])
                ->whereIn('yr.lagacyyear', ['Y', 'B', 'N'])
                ->groupBy(
                    'yr.auditperiodid',
                    'yr.fromyear',
                    'yr.toyear'
                )
                ->orderBy('yr.fromyear')
            //    $querySql = $yearofaudit->toSql();
            // $bindings = $yearofaudit->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);

                ->get();


            $normalyear = DB::table(self::$auditperiod_table . ' as yr')
                ->select(
                    'yr.auditperiodid',
                    DB::raw("CONCAT(yr.fromyear, ' - ', yr.toyear) as audit_period")
                )
                ->where([
                    ['yr.deptcode', $sessionDept],
                    ['yr.statusflag', 'Y'],
                    ['yr.financestatus', 'N'],
                ])
                //->whereIn('yr.lagacyyear', ['N', 'B'])
                ->orderBy('yr.fromyear')
                ->get();


            return [
                'inst'            => $instData,
                'catDet'          => $catDet,
                'subcatDet'       => $subcatDet,
                'yearofaudit'     => $yearofaudit,
                'normalaudityear' => $normalyear,
            ];

        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while fetching datas';

            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }


    public static function apms_dropdown($instid, $paradets)
    {

        try {
            $session = session('charge');
            $sessionUserType = $session->usertypecode;
            $sessionDept = $session->deptcode;
            $sessionroleactioncode =  $sessionUserType == view::shared('auditeelogin') ? $sessionUserType : $session->roleactioncode;
            //-----instdetails------------//
            $instData = DB::table(self::$institution_table . ' as ins')
                ->where([
                    ['ins.statusflag', 'Y'],
                    ['ins.instid', $instid],
                ])
                ->select('ins.instid', 'ins.catcode', 'ins.subcatid', 'ins.instename', 'ins.insttname', 'ins.deptcode')
                ->first();

            if (!$instData) {
                return []; // Defensive return
            }

            $catId = $instData->catcode;
            $subcatId = $instData->subcatid;

            //-----Category------------//
            $catDet = DB::table(self::$mstauditeeinscategory_table . ' as cat')
                ->leftJoin(self::$subcategory_table . ' as sub', 'cat.catcode', '=', 'sub.catcode')
                ->select('cat.catcode', 'cat.catename', 'cat.cattname', 'cat.if_subcategory')
                ->where([
                    ['cat.statusflag', 'Y'],
                    ['cat.catcode', $catId],
                ])
                ->get();

            $issubcat = optional($catDet->first())->if_subcategory;
            //-----Sub-Category------------//
            $subcatDet = collect();
            if ($issubcat === 'Y') {
                $subcatDet = DB::table(self::$subcategory_table . ' as sub')
                    ->where([
                        ['sub.statusflag', 'Y'],
                        ['sub.catcode', $catId],
                        ['sub.auditeeins_subcategoryid', $subcatId],
                    ])
                    ->select('sub.auditeeins_subcategoryid', 'sub.subcatename', 'sub.subcattname')
                    ->get();
            }
            //-----Type of Para------------//
            $typeofpara = DB::table(self::$mst_typeofpara_table)
                ->select('typeofparacode', 'typeofparaename', 'typeofparatname')
                ->where('statusflag', 'Y')
                ->orderBy('orderid')
                ->get();
            //-----State of Para------------//
            $stateofpara = DB::table(self::$mst_stateofpara_table)
                ->select('stateofparacode', 'stateofparaename', 'stateofparatname')
                ->where('statusflag', 'Y')
                ->orderBy('orderid')
                ->get();
            //-----Type of Audit------------//
            $typeofaudit = DB::table(self::$typeofaudit_table)
                ->select('typeofauditcode', 'typeofauditename', 'typeofaudittname')
                ->where([
                    ['deptcode', $sessionDept],
                    ['statusflag', 'Y'],
                ])
                ->whereIn('typeofauditcode', View::shared('financialaudit'))
                ->orderBy('typeofauditename')
                ->get();

            //-----Lagacy Year------------//
            $yearofaudit = DB::table(self::$auditperiod_table . ' as yr')
                ->select(
                    'yr.auditperiodid',
                    DB::raw("CONCAT(yr.fromyear, ' - ', yr.toyear) as audit_period")
                )
                ->where([
                    ['yr.deptcode', $sessionDept],
                    ['yr.statusflag', 'Y'],
                    ['yr.financestatus', 'N'],
                ])
                ->whereIn('yr.lagacyyear', ['Y', 'B'])
                ->groupBy('yr.auditperiodid', 'yr.fromyear', 'yr.toyear')
                ->orderBy('yr.fromyear')
                ->get();

            //-----Main Objection------------//
            $objection = DB::table(self::$mainobjection_table . ' as mainobj')
                ->where('mainobj.deptcode', $instData->deptcode)
                ->where('mainobj.statusflag', 'Y')
                ->select('mainobj.mainobjectionid', 'mainobj.objectionename', 'mainobj.objectiontname')
                ->distinct('mainobj.mainobjectionid')
                ->orderBy('mainobj.mainobjectionid')
                ->get();

            //-----Action of Para------------//
            $actiondata  = LagacyModel::fetchactions_basedonroleaction($sessionroleactioncode);
            //-----Config details------------//
            $configdatas = DB::table(self::$deptartment_table)
                ->select('parafilecount', 'pararejoinderlimit', 'pararejectcount')
                ->where('deptcode', $sessionDept)
                ->get();
            //-----Category------------//
            $normalyear = DB::table(self::$auditperiod_table . ' as yr')
                ->select(
                    'yr.auditperiodid',
                    DB::raw("CONCAT(yr.fromyear, ' - ', yr.toyear) as audit_period")
                )
                ->where([
                    ['yr.deptcode', $sessionDept],
                    ['yr.statusflag', 'Y'],
                    ['yr.financestatus', 'N'],
                ])
                //->whereIn('yr.lagacyyear', ['N', 'B'])
                ->orderBy('yr.fromyear')
                ->get();


            return [
                'inst'            => $instData,
                'catDet'          => $catDet,
                'subcatDet'       => $subcatDet,
                'typeofaudit'     => $typeofaudit,
                'objection'       => $objection,
                'yearofaudit'     => $yearofaudit,
                'stateofpara'     => $stateofpara,
                'typeofpara'      => $typeofpara,
                'actiondata'      => $actiondata,
                'configdatas'     => $configdatas,
                'normalaudityear' => $normalyear,
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while fetching datas';

            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }
    public static function fetchactions_basedonroleaction($sessionroleactioncode)
    {

        try {
            $query = DB::table(self::$actionpara_table)
                ->select('actioncode', 'actionename', 'actiontname')
                ->where('statusflag', 'Y')
                ->when($sessionroleactioncode != 'I', function ($query) use ($sessionroleactioncode) {
                    $query->whereJsonContains('roleaction', $sessionroleactioncode);
                })


                ->orderBy('orderid')
                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while fetching action data';

            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }


    public static function getfrwd_details($paraid)
    {
        $query = DB::table(self::$historytranspara_table . ' as hist')
            ->where('paraid', $paraid)
            ->where('statusflag', 'Y')
            ->where('transstatus', 'A')
            ->select('forwardedbychargeid', 'forwardedbyuserid', 'actroleactioncode')
            ->get();

        return $query;
    }

    public static function createorinsertparadet($data, $paraid)
    {
        DB::beginTransaction();

        try {

            $table = self::$transpara_table;
            $query = DB::table($table);

            // Exclude same para id
            if ($paraid) {
                $query->where('paraid', '!=', $paraid);
            }

            //AUDIT YEAR JSONB PROCESSING
            if (is_array($data['audityear'])) {
                if (count($data['audityear']) === 1 && str_contains($data['audityear'][0], ',')) {
                    $years = array_map('intval', explode(',', $data['audityear'][0]));
                } else {
                    $years = array_map('intval', $data['audityear']);
                }
            } else {
                $years = array_map('intval', explode(',', $data['audityear']));
            }

            $data['audityear'] = json_encode($years);

            //already exist check
            $existingdata = (clone $query)
                ->where('followupid', $data['followupid'])
                ->where('paratype', View::shared('lagacyparatype'))
                ->where(function ($q) use ($years) {
                    foreach ($years as $year) {
                        $q->orWhereJsonContains('audityear', (int)$year);
                    }
                })
                ->where('paranumber', $data['paranumber'])
                ->first();

            if ($existingdata) {
                throw new \Exception('The para details for this institution already exist.');
            }

            //update existing record
            if ($paraid) {

                $affectedRows = DB::table($table)
                    ->where('paraid', $paraid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }

                $insertedRecord = DB::table($table)
                    ->select(
                        'paratype',
                        'rejoinderstatus',
                        'rejoindercycle',
                        'actroleactioncode',
                        'actioncode',
                        'processcode',
                        'paranumber',
                        'followupid',
                        DB::raw('(audityear) as audityear'),
                        'para_remarks',
                        'paraid',
                        'transactionno',
                        'forwardedtouserchargeid',
                        'forwardedtouserid'
                    )
                    ->where('paraid', $paraid)
                    ->first();

                DB::commit();
                return $insertedRecord;
            }

            //insert new record
            $newRecordId = DB::table($table)->insertGetId($data, 'paraid');

            if (!$newRecordId) {
                throw new \Exception('Failed to insert the new record.');
            }

            $insertedRecord = DB::table($table)
                ->select(
                    'paratype',
                    'rejoinderstatus',
                    'rejoindercycle',
                    'actroleactioncode',
                    'actioncode',
                    'processcode',
                    'paranumber',
                    'followupid',
                    DB::raw('(audityear) as audityear'),
                    'para_remarks',
                    'paraid',
                    'transactionno',
                    'forwardedtouserchargeid',
                    'forwardedtouserid'
                )
                ->where('paraid', $newRecordId)
                ->first();

            DB::commit();
            return $insertedRecord;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while saving para details';

            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }




    public static function getforwarddetails(
        $instid,
        $sesroleactioncode,
        $actionfor,
        $paraid,
        $rejoinderstatus,
        $actroleactioncode,
        $rejectcount,
        $stateofparacode,
        $retirementpara,
        $processcode
    ) {
        try {

            /*---------------------------------------
        | Cache shared values (performance)
        ---------------------------------------*/
            $PU     = View::shared('PUroleactioncode');
            $PUAD   = View::shared('PUADroleactioncode');
            $RESP   = View::shared('respons_removal_processcode');
            $PARTS  = View::shared('parts_removal_processcode');
            $RTD    = View::shared('rtd_committee_roleaction');

            /*=====================================
        = 1. RETIREMENT PARA FLOW
        =====================================*/
            if ($retirementpara) {

                $instdata = DB::table(self::$institution_table)
                    ->where('instid', $instid)
                    ->where('statusflag', 'Y')
                    ->select('deptcode', 'regioncode', 'distcode', 'catcode', 'subcatid')
                    ->first();

                $result = DB::select(
                    "SELECT * FROM audit.fetch_convenoruser(?, ?::jsonb)",
                    [$RTD, json_encode($instdata)]
                );

                return $result[0] ?? null;
            }

            if (in_array($processcode, View::shared('auditee_to_DLC_process'))) {

                $instdata = DB::table(self::$institution_table)
                    ->where('instid', $instid)
                    ->where('statusflag', 'Y')
                    ->select('deptcode', 'regioncode', 'distcode', 'catcode', 'subcatid')
                    ->first();

                $toroleactioncode = self::getprocesscode_hlc($processcode, 'roleactioncode');

                $result = DB::select(
                    "SELECT * FROM audit.fetch_convenoruser(?, ?::jsonb)",
                    [$toroleactioncode, json_encode($instdata)]
                );

                return $result[0] ?? null;
            }
            /*=====================================
             = 2. REJOINDER / REMOVAL FLOW
             =====================================*/
            $isRemovalProcess = in_array($processcode, [$RESP, $PARTS], true);

            if (
                ($actionfor === 'rejoinder' || $isRemovalProcess)
                && $sesroleactioncode === $PUAD
            ) {

                if ($isRemovalProcess) {
                    $actionfor = 'auditee';
                }

                return DB::selectOne(
                    "SELECT * FROM audit.fetch_apms_forwarduser(?, ?, ?)",
                    [$paraid, $actionfor, 'I']
                );
            }

            /*=====================================
        = 3. NORMAL FORWARD (FIRST TIME)
        =====================================*/
            if (
                $actionfor === 'forward'
                && $rejoinderstatus === null
                && $rejectcount === null
            ) {

                $toforward_actioncode =
                    ($sesroleactioncode === $PU) ? $PUAD : $PU;

                $result = DB::select(
                    "SELECT * FROM audit.fetch_random_psa(?, ?)",
                    [$instid, $toforward_actioncode]
                );

                return $result[0] ?? null;
            }

            /*=====================================
        = 4. FORWARD AFTER REJOINDER / REJECT
        =====================================*/
            if (
                $actionfor === 'forward'
                && ($rejoinderstatus === 'Y' || $rejectcount !== null)
            ) {

                $to_actioncode = ($actroleactioncode === 'I') ? 'I' : 'A';

                $forwardUser = DB::selectOne(
                    "SELECT * FROM audit.fetch_apms_forwarduser(?, ?, ?)",
                    [$paraid, $actionfor, $to_actioncode]
                );

                if (empty($forwardUser?->msg)) {
                    return $forwardUser;
                }

                // fallback → random PSA
                $toforward_actioncode =
                    ($sesroleactioncode === $PU) ? $PUAD : $PU;

                $result = DB::select(
                    "SELECT * FROM audit.fetch_random_psa(?, ?)",
                    [$instid, $toforward_actioncode]
                );

                return $result[0] ?? null;
            }

            return null;
        } catch (\Illuminate\Database\QueryException $e) {

            \Log::error('SQL Error while fetching forward user details', [
                'message' => $e->getMessage()
            ]);

            throw new \Exception(
                'An error occurred while fetching forward user details.',
                500
            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function  getfilefolderdet($param, $instid)
    {
        try {


            // Fetch the relevant data from the database
            $filequery = DB::table('audit.mst_institution as inst')
                ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'inst.deptcode')
                ->join('audit.mst_district as d', 'd.distcode', '=', 'inst.distcode')
                ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'inst.regioncode')
                ->join('audit.mst_typeofaudit as ta', 'ta.typeofauditcode', '=', 'inst.typeofauditcode')
                ->where('instid', $instid)
                ->where('inst.statusflag', 'Y')
                ->select('inst.deptcode', 'inst.regioncode', 'inst.distcode', 'inst.instid', 'inst.typeofauditcode')
                ->get();

            //  $uploadfolder = storage_path('app/public'); // Correct base path for storage files

            $deptcode = $filequery[0]->deptcode;
            $regioncode = $filequery[0]->regioncode;
            $distcode = $filequery[0]->distcode;
            $instid = $filequery[0]->instid;
            $typeofauditcode = $filequery[0]->typeofauditcode;
            switch ($param) {
                case 'para':
                    $fileuploadpath = View::shared('parafileuploadpath');
                    break;

                default:
                    throw new InvalidArgumentException("Invalid 'parameter' provided.");
            }



            // Construct the base path and break it into components
            $pathParts = [
                $deptcode,
                $regioncode,
                $distcode,
                $instid,
                $typeofauditcode,
                $fileuploadpath
            ];

            return $pathParts;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching folder details. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            $customMessage = 'An error occured while fetching Scheme';
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getmaxtranactionno()
    {
        try {
            $latestcode =   DB::table(self::$transpara_table . ' as trans')
                ->whereNotNull('transactionno') // Ensure we're considering only non-null values
                ->max(DB::raw('CAST(transactionno AS INTEGER)'));

            $newcode = $latestcode !== null ? str_pad($latestcode + 1, 2, '0', STR_PAD_LEFT) : '01';

            return $newcode;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching folder details. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }




    public static function insert_parahistorydata($historydata, $paraid, $liabilitydel, $sesroleactioncode, $liabilityval)
    {

        throw_if(empty($paraid), new \Exception("Para details not found"));
        DB::beginTransaction();
        try {
            $isUpdated = false;
            $isInserted = false;

            // Check if the auditslipid condition is provided and exists
            if ($paraid !== null) {
                $paraidExists = DB::table(self::$historytranspara_table . ' as hist')
                    ->where('hist.paraid', $paraid)
                    ->exists();

                // Update the existing record if auditslipid exists
                if ($paraidExists) {
                    $updateCount = DB::table(self::$historytranspara_table)
                        ->where('paraid', $paraid)
                        ->update(['transstatus' => 'I']);

                    $isUpdated = $updateCount > 0;
                }
            }
            if (is_array($historydata['audityear'])) {
                if (count($historydata['audityear']) === 1 && str_contains($historydata['audityear'][0], ',')) {
                    $years = array_map('intval', explode(',', $historydata['audityear'][0]));
                } else {
                    $years = array_map('intval', $historydata['audityear']);
                }
            } else {
                $years = array_map('intval', explode(',', $historydata['audityear']));
            }
            $jsonYears = json_encode($years);

            $historydata['audityear'] = $jsonYears;
            // Insert the new history transaction record
            $historytransdel = DB::table(self::$historytranspara_table)
                ->insertGetId($historydata, 'transparahistoryid');


            //  if ($sesroleactioncode == view::shared('PUADroleactioncode')) {

            $rejoinderstatus = isset($historydata['rejoinderstatus']) ? $historydata['rejoinderstatus'] : null;
            $rejoindercycle  = isset($historydata['rejoindercycle']) ? $historydata['rejoindercycle'] : null;
            $rejectcount     = isset($historydata['rejectcount']) ? $historydata['rejectcount'] : null;

            DB::statement('
                INSERT INTO audit.parahistoryliability
                (lagacyflag,liabilityid, followupid, notype, liabilityname, liabilitygpfno, liabilitydesignation,
                 liabilityamount,remarks ,retiredflag,retirementyear,retirementmonth,statusflag,createdon,createdby, processcode, rejoinderstatus, rejoindercycle,historytransparaid)
                SELECT lagacyflag,followupliabilityid, followupid, notype, liabilityname, liabilitygpfno, liabilitydesignation,
                       liabilityamount,remarks,retiredflag,retirementyear,retirementmonth, la.statusflag,?, ?,?, ?, ?, ?
                FROM audit.followup_liability la
                WHERE la.followupid = ?
            ', [
                View::shared('get_nowtime'),
                $historydata['forwardedbychargeid'],
                $historydata['processcode'],
                $rejoinderstatus,
                $rejoindercycle,
                $historytransdel,
                $historydata['followupid']
            ]);
            //  }



            // Insert related liabilities


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
            DB::rollback();
            $customMessage = $e->getMessage();

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), 409);
        }
    }









    public static function fetch_instparadetails($userid, $chargeid)
    {
        try {
            $query = DB::table(self::$transpara_table . ' as para')
                ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'para.instid')
                ->join(self::$deptartment_table . ' as dept', 'inst.deptcode', '=', 'dept.deptcode')
                ->join(self::$region_table . ' as reg', 'inst.regioncode', '=', 'reg.regioncode')
                ->join(self::$dist_table . ' as dist', 'inst.distcode', '=', 'dist.distcode')
                ->join(self::$mstauditeeinscategory_table . ' as cat', 'inst.catcode', '=', 'cat.catcode')
                ->LeftJoin(self::$subcategory_table . ' as sub', 'inst.subcatid', '=', 'sub.auditeeins_subcategoryid')
                ->join(self::$typeofaudit_table . ' as at', 'inst.typeofauditcode', '=', 'at.typeofauditcode')
                ->join(self::$userdetail_table . ' as ud', 'ud.deptuserid', '=', 'para.forwardedtouserid')
                ->join(self::$designation_table . ' as desig', 'ud.desigcode', '=', 'desig.desigcode')
                ->select(
                    'para.actroleactioncode',
                    'para.paranumber',
                    'para.paraid',
                    'para.followupid',
                    'para.paratype',
                    'para.updatedon',
                    'inst.instid',
                    'inst.instename',
                    'inst.insttname',
                    'dept.deptesname',
                    'reg.regionename',
                    'reg.regiontname',
                    'dist.distename',
                    'dist.distename',
                    'cat.catename',
                    'cat.cattname',
                    'sub.subcatename',
                    'sub.subcattname',
                    'at.typeofauditename',
                    'at.typeofaudittname',
                    'ud.username',
                    'ud.usertamilname',
                    'desig.desigesname',
                    'desig.desigtsname'
                )
                ->where('para.forwardedtouserid', $userid)
                ->where('para.forwardedtouserchargeid', $chargeid)
                ->orderBy('para.updatedon', 'asc')
                ->get();
            return $query;
        } catch (\Illuminate\Database\QueryException $e) {

            $customMessage = 'A database error occurred while fetching individual para details. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }


    public static function fetch_transparadetails()
    {
        try {
            $query = DB::table(self::$transpara_table . ' as para')
                ->Join(DB::raw("
        LATERAL (
            SELECT string_agg(DISTINCT CONCAT(yr.fromyear, ' - ', yr.toyear), ', ') AS audit_period
            FROM jsonb_array_elements_text(para.audityear) AS ay(auditperiodid)
            JOIN audit.mst_auditperiod AS yr ON yr.auditperiodid = ay.auditperiodid::int
        ) AS auditinfo
    "), DB::raw('TRUE'), '=', DB::raw('TRUE'))
                // ->join(self::$auditperiod_table . ' as yr', function ($join) {
                //     $join->whereRaw("para.audityear::jsonb @> to_jsonb(yr.auditperiodid)::jsonb");
                // })
                ->join(self::$userdetail_table . ' as ud', 'ud.deptuserid', '=', 'para.forwardedtouserid')
                ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'para.instid')
                ->join(self::$deptartment_table . ' as dept', 'inst.deptcode', '=', 'dept.deptcode')
                ->join(self::$region_table . ' as reg', 'inst.regioncode', '=', 'reg.regioncode')
                ->join(self::$dist_table . ' as dist', 'inst.distcode', '=', 'dist.distcode')
                ->select(
                    'auditinfo.audit_period',
                    'para.actroleactioncode',
                    'para.paranumber',
                    'para.updatedon',
                    'inst.instename',
                    'inst.insttname',
                    'dept.deptesname',
                    'reg.regionename',
                    'reg.regiontname',
                    'dist.distename',
                    'dist.distename',
                    'ud.username',
                    'ud.usertamilname',
                    'ud.email',
                    'ud.ifhrmsno'
                )
                ->orderBy('para.updatedon', 'asc')
                ->get();
            // $querySql = $query->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);
            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while fetching para details';

            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }



    public static function fetch_paramanagement_auditee($followupid, $instid, $action, $yearcode, $paratype, $parano)
    {

        try {
            $table = self::$transfollowup_table;

            $session_charge = session('charge');
            $session_user = session('user');
            $usertypecode = $session_charge->usertypecode;
            $sessionuserid = $session_user->userid;

            if ($action == 'P') {


                $query =  DB::table($table . ' as fp')
                    ->join(self::$mainobjection_table . ' as main', 'main.mainobjectionid', '=', 'fp.mainobjectionid')
                    ->join(self::$subobjection_table .  ' as sub', 'sub.subobjectionid', '=', 'fp.subobjectionid')
                    ->join(self::$irregularirites_table .  ' as ir', 'ir.irregularitiescode', '=', 'fp.irregularitiescode')
                    ->leftJoin(self::$transpara_table .  ' as para', 'para.followupid', '=', 'fp.followupid')
                    ->leftJoin(DB::raw("
                       LATERAL (
                           SELECT string_agg(DISTINCT CONCAT(yr.fromyear, ' - ', yr.toyear), ', ') AS audit_period
                           FROM jsonb_array_elements_text(fp.audityear) AS ay(auditperiodid)
                           JOIN audit.mst_auditperiod AS yr ON yr.auditperiodid = ay.auditperiodid::int
                       ) AS auditinfo
                    "), DB::raw('TRUE'), '=', DB::raw('TRUE'))
                    ->leftJoin('audit.trans_auditslip as t', function ($join) {
                        $join->on('t.auditslipid', '=', 'fp.auditslipid')
                            ->where('fp.paratype', '=', 'C');   // join only when paratype = C
                    });
                if ($usertypecode == 'I') {
                    $query->where('fp.statusflag', '=', 'F');
                }


                $query->when($yearcode, function ($query) use ($yearcode) {
                    if (is_array($yearcode)) {
                        // Multiple years selected
                        $query->where(function ($q) use ($yearcode) {
                            foreach ($yearcode as $year) {
                                $q->orWhereJsonContains('fp.audityear', (int)$year);
                            }
                        });
                    } else {
                        // Single year selected
                        $query->whereJsonContains('fp.audityear', (int)$yearcode);
                    }
                });
                $query->when($instid, function ($query) use ($instid) {
                    $query->where('fp.instid', '=', $instid);
                });
                $query->when($parano, function ($query) use ($parano) {
                    $query->where('fp.parano', '=', $parano);
                });

                $query->when($paratype, function ($query) use ($paratype) {
                    $query->where('fp.paratype', '=', $paratype);
                });

                $query
                    ->select(
                        'fp.followupid',
                        'fp.instid',
                        'fp.parano as paranumber',
                        'fp.paratype',
                        'main.objectionename',
                        'main.objectiontname',
                        'sub.subobjectionename',
                        'sub.subobjectiontname',
                        'ir.irregularitieselname',
                        'ir.irregularitiestlname',
                        'auditinfo.audit_period',
                        'fp.amtinvolved',
                        'fp.slipdetails',
                        'fp.severitycode',
                        'para.processcode',
                        'para.paraid',
                        'para.processcode',
                        'para.rejoinderstatus',
                        'para.liabilty_type',
                        'fp.stateofparacode',
                    );
                $query->orderByRaw("
                    CASE
                        WHEN fp.paratype = 'L' THEN 1
                        WHEN fp.paratype = 'C' THEN 2
                        ELSE 3
                    END
                ");
                $query->orderByRaw("
                    CASE
                        WHEN fp.paratype = 'L' THEN fp.paranumber
                        WHEN fp.paratype = 'C' THEN COALESCE(t.paraorder, 999999)
                        ELSE fp.paranumber
                    END
                ");
                // $querySql = $query->toSql();
                // $bindings = $query->getBindings();

                // $finalQuery = vsprintf(
                //     str_replace('?', "'%s'", $querySql),
                //     array_map('addslashes', $bindings)
                // );

                // print_r($finalQuery);
                // exit;

                // ->orderBy('fp.paranumber', 'asc')
                // ->orderBy('fp.followupid', 'asc');


                $data = [
                    'data' => $query->get(),
                ];

                return $data;
            } else {
                $query = DB::table($table . ' as fp')
                    ->leftJoin(self::$transpara_table .  ' as para', 'para.followupid', '=', 'fp.followupid')
                    ->join(self::$typeofaudit_table . ' as type', 'type.typeofauditcode', '=', 'fp.typeofauditcode')
                    ->join(self::$mainobjection_table . ' as main', 'main.mainobjectionid', '=', 'fp.mainobjectionid')
                    ->join(self::$subobjection_table .  ' as sub', 'sub.subobjectionid', '=', 'fp.subobjectionid')
                    ->join('audit.mst_irregularitiessubcategory as subcat', 'subcat.irregularitiessubcatcode', '=', 'fp.irregularitiessubcatcode')
                    ->join('audit.mst_irregularitiescategory as cat', 'cat.irregularitiescatcode', '=', 'fp.irregularitiescatcode')
                    ->join('audit.mst_irregularities as ir', 'ir.irregularitiescode', '=', 'fp.irregularitiescode')
                    ->leftjoin('audit.auditeescheme as scheme', 'scheme.auditeeschemecode', '=', 'fp.auditeeschemecode')
                    ->leftJoin('audit.deptuserdetails as cb', 'cb.deptuserid', '=', 'fp.createdby')
                    ->leftjoin(self::$followupliability_table . ' as l', 'l.followupid', '=', 'fp.followupid')
                    ->leftJoin(DB::raw("
                       LATERAL (
                           SELECT string_agg(DISTINCT CONCAT(yr.fromyear, ' - ', yr.toyear), ', ') AS audit_period
                           FROM jsonb_array_elements_text(fp.audityear) AS ay(auditperiodid)
                           JOIN audit.mst_auditperiod AS yr ON yr.auditperiodid = ay.auditperiodid::int
                       ) AS auditinfo
                    "), DB::raw('TRUE'), '=', DB::raw('TRUE'));

                if ($paratype == View::shared('lagacyparatype')) {
                    $query
                        ->leftJoin(self::$lagacyfielupload_table . ' as t3', 'fp.followupid', '=', 't3.followupid')
                        ->leftJoin('audit.fileuploaddetail as t2', 't2.fileuploadid', '=', 't3.fileuploadid');
                } else if ($paratype == View::shared('normalparatype')) {
                    $query
                        ->leftJoin(self::$slipfileupload_table . ' as t3', 'fp.auditslipid', '=', 't3.auditslipid')
                        ->leftJoin('audit.fileuploaddetail as t2', 't2.fileuploadid', '=', 't3.fileuploadid');
                }


                $query->select(
                    'para.rejectcount',
                    DB::raw('(fp.audityear) as audityear'),
                    DB::raw('auditinfo.audit_period'),
                    'para.paraid',
                    'fp.paratype',
                    'fp.auditslipid',
                    'fp.statusflag',
                    'fp.parano as paranumber',
                    'fp.typeofparacode',
                    'fp.stateofparacode',
                    'fp.lastactionyear',
                    'fp.lastactionmonth',
                    'fp.followupid',
                    DB::raw("
                    STRING_AGG(
                        DISTINCT CASE
                            WHEN t3.statusflag = 'Y' THEN
                                CONCAT(
                                    COALESCE(t2.filename, ''), '-',
                                    COALESCE(t2.filepath, ''), '-',
                                    COALESCE(t2.filesize::TEXT, ''), '-',
                                    COALESCE(t2.fileuploadid::TEXT, '')
                                )
                            ELSE NULL
                        END, ','
                    ) AS auditorfileupload
                "),


                    'fp.subobjectionid',
                    'fp.mainobjectionid',
                    'fp.amtinvolved',
                    'fp.slipdetails',
                    //   'fp.audityear',
                    DB::raw("COALESCE(fp.remarks::json->>'content', '') AS remarks"),
                    'fp.severitycode',

                    'subcat.irregularitiessubcatcode',
                    'cat.irregularitiescatcode',
                    'fp.schemastatus',
                    'scheme.auditeeschemecode',
                    //  //'trans_auditslip.auditeeschemecode',
                    'ir.irregularitiescode',
                    'para.processcode',
                    'fp.liability',
                    'cb.username AS createdbyusername',
                    'fp.createdby',
                    'fp.updatedon',
                    'type.typeofauditcode',
                    'type.typeofauditename',
                    'type.typeofaudittname',
                    'main.mainobjectionid',
                    'main.objectionename',
                    'main.objectiontname',
                    'sub.subobjectionid',
                    'sub.subobjectionename',
                    'sub.subobjectiontname',

                    'para.auditee_liability',
                    'para.liabilty_type',
                    DB::raw("CASE
                            WHEN EXISTS (
                                SELECT 1
                                FROM audit.followup_liability li

                                where li.followupid =fp.followupid and li.statusflag = 'C'

                            ) THEN 'Y' ELSE 'N'
                         END AS para_removed"),
                    (DB::raw("
                    STRING_AGG(
                        DISTINCT CASE
                            WHEN  (l.statusflag = 'Y'  ) THEN
                                CONCAT(
                                    COALESCE(l.notype, ''), '|~|',
                                    COALESCE(l.liabilitygpfno, ''), '|~|',
                                    COALESCE(l.liabilityname, ''), '|~|',
                                    COALESCE(l.liabilitydesignation::TEXT, ''), '|~|',
                                    COALESCE(l.liabilityamount::TEXT, ''), '|~|',
                                    COALESCE(l.followupliabilityid::TEXT, ''), '|~|',
                                    COALESCE(l.remarks::TEXT, ''), '|~|',
                                    COALESCE(l.statusflag::TEXT, ''),'|~|',
                                    COALESCE(l.retiredflag::TEXT, ''), '|~|',
                                    COALESCE(l.retirementyear, ''), '|~|',
					COALESCE(l.retirementmonth::TEXT, ''),'|~|',
          				COALESCE(l.lagacyflag::TEXT, '')

                                )
                            ELSE NULL
                        END, ','
                    ) AS liabilitydel
                "))

                    // DB::raw("
                    //     CASE
                    //         WHEN fp.uploadid != 0 THEN CONCAT(fu.filename, '-', fu.filepath, '-', fu.filesize, '-', fu.fileuploadid)
                    //         ELSE '-'
                    //     END AS filedetails
                    // "),
                );
            }



            if ($action == 'edit') {
                $query->when($followupid, function ($query) use ($followupid) {
                    $query->where('fp.followupid', '=', $followupid);
                });
            }
            if ($usertypecode == 'I') {

                $query->where('fp.statusflag', '=', 'F');
            }
            // ->where('inst.statusflag', 'Y');
            $query->when($yearcode, function ($query) use ($yearcode) {
                if (is_array($yearcode)) {
                    // Multiple years selected
                    $query->where(function ($q) use ($yearcode) {
                        foreach ($yearcode as $year) {
                            $q->orWhereJsonContains('fp.audityear', (int)$year);
                        }
                    });
                } else {
                    // Single year selected
                    $query->whereJsonContains('fp.audityear', (int)$yearcode);
                }
            });
            $query->when($instid, function ($query) use ($instid) {
                $query->where('fp.instid', '=', $instid);
            });

            $query->orderBy('fp.followupid', 'asc');
            $query->groupBy(
                'para.rejectcount',
                'auditinfo.audit_period',
                'fp.auditslipid',
                'fp.paratype',
                'para.paraid',
                'type.typeofauditcode',
                'fp.followupid',
                'fp.parano',
                'fp.mainobjectionid',
                'fp.amtinvolved',
                'fp.slipdetails',
                'main.mainobjectionid',
                'sub.subobjectionid',
                'subcat.irregularitiessubcatcode',
                'cat.irregularitiescatcode',
                'fp.schemastatus',
                'scheme.auditeeschemecode',
                'ir.irregularitiescode',
                'fp.severitycode',
                'fp.subobjectionid',
                'fp.auditscheduleid',
                'fp.processcode',
                'fp.liability',
                'fp.statusflag',
                'cb.username',
                'fp.createdby',
                'fp.updatedon'
            );




            $data = [
                'data' => $query->get(),
            ];

            return $data;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while fetching para number details';
            throw new \Exception($e->getMessage(), 500);
            // throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }




    public static function update_parafileupload($updateprocesscode, $createdby, $processcode, $paraid, $usertypecode, $rejoinderstatus, $rejoindercount, $oldrejectcount, $rejectcount)

    {
        try {
            $isUpdated = false;
            $isInserted = false;
            $getfileuploadid = DB::table('audit.parafileupload as t')
                ->join('audit.fileuploaddetail as t2', 't2.fileuploadid', '=', 't.fileuploadid')
                ->where('t.paraid', $paraid)
                ->where('t.statusflag', 'Y')
                ->where('t2.uploadedby', $createdby)
                ->where('t.processcode', $processcode)
                ->where('t.rejectcount', $oldrejectcount)
                ->where('t2.usertypecode', $usertypecode);

            if ($rejoinderstatus == 'Y') {
                $getfileuploadid->where('t.rejoinderstatus', $rejoinderstatus)
                    ->where('t.rejoindercycle', $rejoindercount);
            }


            // Update and get affected rows
            $affectedRows = $getfileuploadid->update([
                'processcode' => $updateprocesscode,
                'rejoinderstatus' => $rejoinderstatus ?? null,
                'rejectcount' => $rejectcount ?? null,
                'rejoindercycle' => $rejoindercount ?? null
            ]);

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

    public static function getlegacyyear($deptcode)
    {
        $query = DB::table(self::$auditperiod_table . ' as  yr')
            ->select(
                'yr.auditperiodid',
                DB::raw("CONCAT(yr.fromyear, ' - ', yr.toyear) as audit_period")
            )
            ->where('yr.deptcode', $deptcode)
            ->where('yr.statusflag', 'Y')
            ->where('yr.financestatus', 'N')
            ->whereIN('yr.lagacyyear', ['B', 'Y'])
            ->orderBy('yr.fromyear', 'asc')
            ->get();

        return $query;
    }

    public static function getaudityear($deptcode)
    {
        $query = DB::table(self::$auditperiod_table . ' as  yr')
            ->select(
                'yr.auditperiodid',
                DB::raw("CONCAT(yr.fromyear, ' - ', yr.toyear) as audit_period")
            )
            ->where('yr.deptcode', $deptcode)
            ->where('yr.statusflag', 'Y')
            ->where('yr.financestatus', 'N')
            // ->whereIN('yr.lagacyyear', ['B', 'N'])
            ->orderBy('yr.fromyear', 'asc')
            ->get();

        return $query;
    }

    public static function fetch_parastatus($yearcode, $usertypecode)
    {
        try {
            $session = session('user');
            $charge = session('charge');
            $userchargeid =  $usertypecode == 'A' ? $charge->userchargeid : '';
            $userid = $session->userid;
            $query = DB::table(self::$historytranspara_table . ' as hist')
                ->join(self::$transpara_table . ' as para', 'hist.paraid', '=', 'para.paraid')
                ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'para.instid')
                ->join(self::$deptartment_table . ' as dept', 'inst.deptcode', '=', 'dept.deptcode')
                ->join(self::$region_table . ' as reg', 'inst.regioncode', '=', 'reg.regioncode')
                ->join(self::$dist_table . ' as dist', 'inst.distcode', '=', 'dist.distcode')
                ->join(self::$mstauditeeinscategory_table . ' as cat', 'inst.catcode', '=', 'cat.catcode')
                ->LeftJoin(self::$subcategory_table . ' as sub', 'inst.subcatid', '=', 'sub.auditeeins_subcategoryid')
                ->join(self::$typeofaudit_table . ' as at', 'inst.typeofauditcode', '=', 'at.typeofauditcode')
                ->Join(DB::raw("
                 LATERAL (
                     SELECT string_agg(DISTINCT CONCAT(yr.fromyear, ' - ', yr.toyear), ', ') AS audit_period
                     FROM jsonb_array_elements_text(para.audityear) AS ay(auditperiodid)
                     JOIN audit.mst_auditperiod AS yr ON yr.auditperiodid = ay.auditperiodid::int
                 ) AS auditinfo
             "), DB::raw('TRUE'), '=', DB::raw('TRUE'));
            $query->when($yearcode, function ($query) use ($yearcode) {
                if (is_array($yearcode)) {
                    // Multiple years selected
                    $query->where(function ($q) use ($yearcode) {
                        foreach ($yearcode as $year) {
                            $q->orWhereJsonContains('para.audityear', (int)$year);
                        }
                    });
                } else {
                    // Single year selected
                    $query->whereJsonContains('para.audityear', (int)$yearcode);
                }
            });
            $query->select(
                'auditinfo.audit_period',
                'inst.instid',
                'inst.instename',
                'inst.insttname',
                'dept.deptesname',
                'reg.regionename',
                'reg.regiontname',
                'dist.distename',
                'dist.distename',
                'cat.catename',
                'cat.cattname',
                'sub.subcatename',
                'sub.subcattname',
                'at.typeofauditename',
                'at.typeofaudittname',
                'para.processcode',
                'para.paraid',
                'para.paratype',
                'para.paranumber'
            )
                ->where('hist.statusflag', 'Y')
                ->distinct('hist.paraid');
            if ($usertypecode == 'A') {
                $query->where('hist.forwardedtochargeid', $userchargeid);
            } else if ($usertypecode == 'I') {
                $query->where('hist.forwardedtouserid',  $userid);
            }

            return $query->get();
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching para details. Please contact the administrator.';

            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function fetch_historyparastatus($paraid)
    {
        try {
            $session = session('user');
            $charge = session('charge');
            $usertypecode = $charge->usertypecode;
            $userchargeid =  $usertypecode == 'A' ? $charge->userchargeid : '';
            $userid = $session->userid;

            $historydata = DB::table(self::$historytranspara_table . ' as hist')
                ->join(self::$transpara_table . ' as para', 'para.paraid', '=', 'hist.paraid')
                ->Join(DB::raw("
                 LATERAL (
                     SELECT string_agg(DISTINCT CONCAT(yr.fromyear, ' - ', yr.toyear), ', ') AS audit_period
                     FROM jsonb_array_elements_text(para.audityear) AS ay(auditperiodid)
                     JOIN audit.mst_auditperiod AS yr ON yr.auditperiodid = ay.auditperiodid::int
                 ) AS auditinfo
             "), DB::raw('TRUE'), '=', DB::raw('TRUE'))
                ->leftJoin(self::$actionpara_table . ' as act', 'hist.actioncode', '=', 'act.actioncode')
                ->leftJoin(self::$userdetail_table . ' as ud', 'hist.forwardedbyuserid', '=', 'ud.deptuserid')
                ->leftJoin(self::$auditeeuserdetails_table . ' as ad', 'hist.forwardedbyuserid', '=', 'ad.auditeeuserid')
                ->select(
                    'act.actionename',
                    'act.actiontname',
                    'hist.instid',
                    'hist.para_remarks',
                    'hist.processcode',
                    'hist.rejoinderstatus',
                    'hist.rejoindercycle',
                    'hist.forwardedbyuserid',
                    'hist.forwardedtochargeid',
                    'hist.forwardedon',
                    'hist.createdby',
                    'hist.createdon',
                    'hist.usertypecode',
                    'hist.forwardedtouserid',
                    'hist.forwardedbychargeid',
                    'hist.actioncode',
                    'hist.actroleactioncode',
                    'hist.paratype',

                    //  DB::raw("COALESCE(hist.para_remarks::json->>'content', '') AS para_historyremarks"),
                    'ad.username as auditeename',
                    'ud.username',

                    'auditinfo.audit_period',
                    DB::raw("COALESCE(hist.para_remarks::json->>'content', '') AS remarks"),
                );

            $historydata->when($paraid, function ($query) use ($paraid) {
                $query->where('para.paraid', '=', $paraid);
            });
            $historydata->groupBy(
                'hist.transparahistoryid',
                'ad.username',
                'ud.username',
                'act.actionename',
                'act.actiontname',
                'auditinfo.audit_period',
                // 'yr.fromyear',
                // 'yr.toyear',

            )
                ->where('hist.statusflag', 'Y')
                ->orderBy('hist.transparahistoryid', 'asc');

            return $historydata->get();
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching folder details. Please contact the administrator.';

            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }


    public static function delete_lagacydata($data, $userid)
    {
        try {
            if (empty($data)) {
                throw new \InvalidArgumentException('No data provided');
            }

            DB::beginTransaction();

            $newRecordId = DB::table(self::$logfollowup_table)->insertGetId($data, 'updhistoryid');

            if (!$newRecordId) {
                throw new \Exception('Failed to insert the new record.');
            }

            $affectedRows = DB::table(self::$transfollowup_table)
                ->where('followupid', $data['followupid'])
                ->update([
                    'statusflag' => 'D',
                    'updatedon' => View::shared('get_nowtime'),
                    'updatedby' => $userid
                ]);

            if ($affectedRows === 0) {
                throw new \Exception('Failed to update the record.');
            }

            DB::commit();

            return $newRecordId;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            $customMessage = 'An error occured while deleting Legacy detail';

            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            DB::rollBack();

            throw new \Exception($e->getMessage(), 409);
        }
    }


    public static function update_lagacydata($data, $userid, $parano, $typeofparacode)
    {
        try {
            if (empty($data)) {
                throw new \InvalidArgumentException('No data provided');
            }

            DB::beginTransaction();

            $existingdata = DB::table(self::$transfollowup_table)
                ->where('instid', $data['instid'])
                ->whereJsonContains('audityear', json_decode($data['newaudityear']))
                ->where('parano', $parano)
                ->where('statusflag', '!=', 'D')
                ->where('typeofparacode', $typeofparacode)
                ->where('paratype', View::shared('lagacyparatype'))
                ->first();


            if ($existingdata) {
                throw new \Exception('The legacy details for this instituion was already exists');
            }

            $maxId = DB::table(self::$transfollowup_table)
                ->where('instid', $data['instid'])
                ->whereJsonContains('audityear', json_decode($data['newaudityear']))
                ->where('statusflag', '!=', 'D')
                ->max('paranumber');

            $maxId = $maxId ?? 0;
            $code = $maxId + 1;

            $data['newparanumber'] = $code;

            $newRecordId = DB::table(self::$logfollowup_table)->insertGetId($data, 'updhistoryid');

            if (!$newRecordId) {
                throw new \Exception('Failed to insert the new record.');
            }



            $affectedRows = DB::table(self::$transfollowup_table)
                ->where('followupid', $data['followupid'])
                ->update([
                    'audityear' => $data['newaudityear'],
                    'paranumber' => $code,
                    'updatedon' => View::shared('get_nowtime'),
                    'updatedby' => $userid
                ]);

            if ($affectedRows === 0) {
                throw new \Exception('Failed to update the record.');
            }

            DB::commit();

            return $newRecordId;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            $customMessage = 'An error occured while updating Legacy detail';

            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            DB::rollBack();

            throw new \Exception($e->getMessage(), 409);
        }
    }


    public static function insertupdateparaLiability($liabilityid, $liabilitydata, $processcode, $followupid, $session_userid, $auditeeflag, $sesroleactioncode)
    {

        // return $liabilitydata;

        $liabilityidcount  =   count($liabilityid);
        //return $liabilityidcount;
        if ($liabilityidcount > 0) {

            for ($i = 0; $i < $liabilityidcount; $i++) {
                //return   $liabilitydata['retirementyear'][$i];
                $data   = array(
                    'followupid'              => $followupid,
                    'notype'                  => $liabilitydata['notype'][$i],
                    'liabilityname'           => $liabilitydata['name'][$i],
                    'liabilitygpfno'          => $liabilitydata['gpfno'][$i],
                    'liabilitydesignation'    => $liabilitydata['designation'][$i],
                    'liabilityamount'         => $liabilitydata['amount'][$i],
                    'retiredflag'             => $liabilitydata['retiredflag'][$i],
                    'auditeeflag'             => $auditeeflag,
                    'lagacyflag'     =>  'Y',

                );
                if (!empty($liabilitydata['retirementyear'][$i])) {
                    $data['retirementyear'] = $liabilitydata['retirementyear'][$i];
                }

                if (!empty($liabilitydata['retirementmonth'][$i])) {
                    $data['retirementmonth'] = $liabilitydata['retirementmonth'][$i];
                }




                if ($auditeeflag == '' || $auditeeflag == 'N' || $auditeeflag == NULL) {

                    $data['remarks'] = $liabilitydata['liability_remarks'][$i];
                }
                // return $liabilityid;

                if ($liabilityid[$i]) {

                    //$data['statusflag']  =  'Y';
                    if ($sesroleactioncode == view::shared('PUADroleactioncode')) {

                        if ($liabilitydata['activestatus'][$i] == 1)
                            $data['statusflag'] = 'Y';
                        else
                            $data['statusflag'] = 'C';
                    }

                    //  else {
                    //     $data['statusflag'] = 'Y';
                    // }


                    $data['updatedby']  =  $session_userid;
                    $data['updatedon']  = View::shared('get_nowtime');
                    // return $liabilitydata;
                    DB::table(self::$followupliability_table)
                        ->where('followupliabilityid', $liabilityid[$i])
                        ->update($data);
                } else {

                    $data['statusflag']  =  'Y';
                    $data['createdby']  =  $session_userid;
                    $data['createdon']  = View::shared('get_nowtime');

                    DB::table(self::$followupliability_table)->insert(
                        $data
                    );
                }
            }

            if ($i <= count($liabilitydata['name'])) {
                for ($l = $i; $l < count($liabilitydata['name']); $l++) {
                    $data   = array(
                        'notype'                    =>  $liabilitydata['notype'][$i],
                        'followupid'                =>  $followupid,
                        'liabilityname'             => $liabilitydata['name'][$l],
                        'liabilitygpfno'            => $liabilitydata['gpfno'][$l],
                        'liabilitydesignation'      => $liabilitydata['designation'][$l],
                        'liabilityamount'           => $liabilitydata['amount'][$i],
                        'retiredflag'               => $liabilitydata['retiredflag'][$i],
                        'auditeeflag'                => $auditeeflag,
                        'lagacyflag'     =>  'Y',
                        'statusflag'  => 'Y',
                        'createdby'  =>  $session_userid,
                        'createdon'  =>  View::shared('get_nowtime'),
                    );
                    // return $data;
                    if ($liabilitydata['retiredflag'][$i] == 'L' || $liabilitydata['retiredflag'][$i] == 'M') {
                        $data['retirementyear']  = $liabilitydata['retirementyear'][$i];
                        $data['retirementmonth'] = $liabilitydata['retirementmonth'][$i];
                    }
                    if ($auditeeflag == '' || $auditeeflag == 'N' || $auditeeflag == NULL) {
                        $data['remarks'] = $liabilitydata['liability_remarks'][$i];
                    }

                    DB::table(self::$followupliability_table)->insert(
                        $data
                    );
                }
                //
            }
            // return $data;
        }
    }

    public static function getprocesscode_hlc($stateofparacode, $action)
    {

        $map_processcode = [
            View::shared('dehlc_to_auditee')    => View::shared('auditee_to_DEHLC'),
            View::shared('DLC_to_auditee')        => View::shared('auditee_to_DLC'),
            View::shared('slc_to_auditee')       => View::shared('auditee_to_SHLC'),

        ];

        $map_roleactioncode = [
            View::shared('auditee_to_DLC')    => View::shared('dlc_roleactioncode'),
            View::shared('auditee_to_DEHLC')        => View::shared('dehc_roleactioncode'),
            View::shared('auditee_to_SHLC')       => View::shared('shlc_roleactioncode'),

        ];


        switch ($action) {

            case 'process':
                $map = $map_processcode;
                break;
            case 'roleactioncode':
                $map = $map_roleactioncode;
                break;

            default:
                return '';
        }
        $processcode = $map[$stateofparacode];

        if (empty($processcode)) {
            throw new \Exception("Process code not found for this state of para");
        }

        return $processcode;
    }


    //------------------

    public static function fetch_parano($instid, $yearcode)
    {




        $query =  DB::table(self::$transfollowup_table . ' as fp')

            ->when($yearcode, function ($query) use ($yearcode) {
                if (is_array($yearcode)) {
                    // Multiple years selected
                    $query->where(function ($q) use ($yearcode) {
                        foreach ($yearcode as $year) {
                            $q->orWhereJsonContains('fp.audityear', (int)$year);
                        }
                    });
                } else {
                    // Single year selected
                    $query->whereJsonContains('fp.audityear', (int)$yearcode);
                }
            })
            ->where('fp.statusflag', '=', 'F');
        $query->when($instid, function ($query) use ($instid) {
            $query->where('fp.instid', '=', $instid);
        });




        $query
            ->select(
                'fp.followupid',
                'fp.instid',
                'fp.parano as paranumber',
                'fp.paratype',

            );


        $data = [
            'data' => $query->get(),
        ];

        return $data;
    }
}
