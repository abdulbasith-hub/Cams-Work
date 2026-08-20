<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\View;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class ApmsModel extends Model
{
  
    protected static $historytrans_hlc = BaseModel::HISTORYTRANS_HLC;
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
    //-----------------------------new ---------------//
    protected static $apms_hlc_table = BaseModel::APMS_HLC_TABLE;
    protected static $irregularirites_table = BaseModel::IRREGULARITIES_TABLE;
    protected static $irregularirites_category_table = BaseModel::IRREGULARITIESCATEGORY_TABLE;
    protected static $irregularirites_subcategory_table = BaseModel::IRREGULARITIESSUBCATEGORY_TABLE;
    protected static $auditeinstmap = BaseModel::AUDITOR_INSTMAPPING_TABLE;




    //----------------------------------------------HLC WORKFLOW----------------------------------//

    public static function fetchDeptBasedMasterData($deptcode)
    {
        try {

            $regions = DB::table(self::$roletypemappingTable . ' as rtm')
                ->join(self::$region_table . ' as rt', 'rt.deptcode', '=', 'rtm.parentcode')
                ->join(self::$auditeinstmap . ' as map', 'map.regioncode', '=', 'rt.regioncode')
                ->where([
                    ['map.statusflag', '=', 'Y'],
                    ['rtm.deptcode', '=', $deptcode]
                ])
                ->select('rt.regioncode', 'rt.regionename', 'rt.regiontname')
                ->distinct()
                ->get();

            $categories = DB::table(self::$mstauditeeinscategory_table)
                ->where('deptcode', $deptcode)
                ->select('catcode', 'catename', 'cattname', 'if_subcategory')
                ->distinct()
                ->get();

            return [
                'regions'    => $regions,
                'categories' => $categories
            ];
        } catch (\Exception $e) {
            throw new \Exception('Database fetch failed');
        }
    }


   

    private static function getProcessCodesByRole(string $roleCode): array
    {
        $roleMap = [
            view::shared('dlc_roleactioncode')  => 'dist_hlc_allowed',
            view::shared('dehc_roleactioncode') => 'dept_hlc_allowed',
            view::shared('shlc_roleactioncode')   => 'state_hlc_allowed',
        ];

        $sharedKey = $roleMap[$roleCode] ?? null;

        return $sharedKey ? View::shared($sharedKey) : [];
    }




    public static function fetch_parahistory($paraid)
    {
        try {

            $historydata = DB::table(self::$historytranspara_table . ' as hist')
                ->join(self::$transpara_table . ' as para', 'para.paraid', '=', 'hist.paraid')
                ->leftJoin(self::$actionpara_table . ' as act', 'hist.actioncode', '=', 'act.actioncode')
                ->leftJoin(self::$userdetail_table . ' as ud', 'hist.forwardedbyuserid', '=', 'ud.deptuserid')
                ->leftJoin(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'ud.desigcode')
                ->leftJoin(self::$auditeeuserdetails_table . ' as ad', 'hist.forwardedbyuserid', '=', 'ad.auditeeuserid')
                ->leftJoin(self::$apms_hlc_table . ' as hlc', 'hlc.apms_hlcid', '=', 'hist.apms_hlcid')
                ->leftJoin('audit.fileuploaddetail as t2', 't2.fileuploadid', '=', 'hlc.fileuploadid')
                ->leftJoin(DB::raw("
                    LATERAL (
                        SELECT STRING_AGG(
                            CONCAT(
                                COALESCE(t2.filename, ''), '-',
                                COALESCE(t2.filepath, ''), '-',
                                COALESCE(t2.filesize::TEXT, ''), '-',
                                COALESCE(t2.fileuploadid::TEXT, '')
                            ), ','
                        ) AS minutesfileupload
                        FROM audit.fileuploaddetail t2
                        WHERE t2.fileuploadid = hlc.fileuploadid
                          AND t2.statusflag = 'Y'
                    ) AS mfiles
                "), DB::raw('true'), DB::raw('true'))

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
                    DB::raw("COALESCE(mfiles.minutesfileupload, '') AS minutesfileupload"),

                    'hlc.mom_date',
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

                    DB::raw("COALESCE(hist.para_remarks::json->>'content', '') AS para_historyremarks"),
                    'ad.username as auditeename',
                    'ud.username',
                    DB::raw("COALESCE(hfiles.auditeefileupload, '') AS auditeefileupload")
                )
                ->where('hist.statusflag', 'Y')
                ->orderBy('hist.transparahistoryid', 'desc');
            $historydata->when($paraid, function ($q) use ($paraid) {
                $q->where('para.paraid', '=', $paraid);
            });

            // $querySql = $historydata->toSql();
            // $bindings = $historydata->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);



            return $historydata->get();
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while fetching datas';
            throw new \Exception($e->getMessage(), 500);
            //  throw new \Exception($customMessage, 500);
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
                ->whereJsonContains('roleaction', $sessionroleactioncode)
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

 






    public static function getforwarddetails($instid, $roleactioncode, $inst_data)
    {
        try {
            $result = DB::select("SELECT * FROM audit.fetch_convenoruser(?, ?::jsonb)", [$roleactioncode, json_encode($inst_data)]);
            return $result[0];
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while fetching forward user details. Please contact the administrator.';

            throw new \Exception($customMessage(), 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function fetch_paraflow($paraid)
    { {
            try {
                $session = session('user');
                $charge = session('charge');
                $usertypecode = $charge->usertypecode;

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
                        'ad.username as auditeename',
                        'ud.username',
                        'auditinfo.audit_period',
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
    }

    public static function fetch_minutes($paraid, $apms_hlcid)
    {
        try {
            $query = DB::table(self::$apms_hlc_table . ' as hlc')
                // ->Join(self::$transpara_table . ' as para', 'para.apms_hlcid', '=', 'hlc.apms_hlcid')
                ->Join('audit.fileuploaddetail as t2', 't2.fileuploadid', '=', 'hlc.fileuploadid')

                ->select(
                    'hlc.mom_date',
                    DB::raw("
            STRING_AGG(
                DISTINCT CASE
                    WHEN t2.statusflag = 'Y' THEN
                        CONCAT(
                            COALESCE(t2.filename, ''), '-',
                            COALESCE(t2.filepath, ''), '-',
                            COALESCE(t2.filesize::TEXT, ''), '-',
                            COALESCE(t2.fileuploadid::TEXT, '')
                        )
                    ELSE NULL
                END, ','
            ) AS minutesfileupload
        "),
                )
                ->when($paraid, function ($query) use ($apms_hlcid) {
                    $query->where('hlc.apms_hlcid', '=', $apms_hlcid);
                })
                // ->when($paraid, function ($query) use ($paraid) {
                //     $query->where('para.paraid', '=', $paraid);
                // })
                ->groupBy(['hlc.mom_date'])->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching folder details. Please contact the administrator.';
            throw new \Exception($e->getMessage(), 409);
            //  throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }
    public static function fetch_paradetails($paraid)
    {


        $data = DB::table(self::$transpara_table . ' as para')
            ->join(self::$transfollowup_table . ' as fp', 'para.followupid', '=', 'fp.followupid')

            ->select([
                'inst.instename',
                'inst.insttname',
                'tp.typeofparaename',
                'tp.typeofparatname',
                'sp.stateofparaename',
                'sp.stateofparatname',
                'fp.parano',
                'fp.lastactionyear',
                'fp.lastactionmonth',
                'main.objectionename',
                'main.objectiontname',
                'sub.subobjectionename',
                'sub.subobjectiontname',
                'fp.amtinvolved',
                'sev.severityelname',
                'sev.severitytlname',
                'fp.schemastatus',
                'scheme.auditeeschemeelname',
                'scheme.auditeeschemetlname',
                'ir.irregularitieselname',
                'ir.irregularitiestlname',
                'cat.irregularitiescatelname',
                'cat.irregularitiescattlname',
                'subcat.irregularitiessubcatelname',
                'subcat.irregularitiessubcattlname',
                'fp.slipdetails',
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

                DB::raw("
            STRING_AGG(
                DISTINCT CASE
                    WHEN (l.statusflag = 'Y' OR l.statusflag = 'C') THEN
                        CONCAT(
                            COALESCE(l.notype, ''), '~',
                            COALESCE(l.liabilitygpfno, ''), '~',
                            COALESCE(l.liabilityname, ''), '~',
                            COALESCE(l.liabilitydesignation::TEXT, ''), '~',
                            COALESCE(l.liabilityamount::TEXT, ''), '~',
                            COALESCE(l.retirementyear::TEXT, ''), '~',
                            COALESCE(l.retirementmonth::TEXT, ''), '~',
                             COALESCE(l.retiredflag::TEXT, ''), '~',
                            COALESCE(l.statusflag::TEXT, ''),'~',
                           COALESCE(l.followupliabilityid::TEXT, '')    
                        )
                    ELSE NULL
                END, ','
            ) AS liabilitydel
        "),
                // 'fp.audityear',
                DB::raw("COALESCE(fp.remarks::json->>'content', '') AS remarks"),
                'fp.liability',
                'cb.username as createdbyusername',
                'fp.updatedon',
                'type.typeofauditename',
                'type.typeofaudittname'
            ])
            ->Join(self::$institution_table . ' as inst', 'inst.instid', '=', 'fp.instid')
            ->leftJoin('audit.mst_typeofpara as tp', 'tp.typeofparacode', '=', 'fp.typeofparacode')
            ->leftJoin('audit.mst_stateofpara as sp', 'sp.stateofparacode', '=', 'fp.stateofparacode')
            ->join('audit.mst_typeofaudit as type', 'type.typeofauditcode', '=', 'fp.typeofauditcode')
            ->join('audit.mst_mainobjection as main', 'main.mainobjectionid', '=', 'fp.mainobjectionid')
            ->join('audit.mst_subobjection as sub', 'sub.subobjectionid', '=', 'fp.subobjectionid')
            ->join('audit.mst_severity as sev', 'sev.severitycode', '=', 'fp.severitycode')
            ->join('audit.mst_irregularitiessubcategory as subcat', 'subcat.irregularitiessubcatcode', '=', 'fp.irregularitiessubcatcode')
            ->join('audit.mst_irregularitiescategory as cat', 'cat.irregularitiescatcode', '=', 'fp.irregularitiescatcode')
            ->join('audit.mst_irregularities as ir', 'ir.irregularitiescode', '=', 'fp.irregularitiescode')

            ->leftJoin('audit.auditeescheme as scheme', 'scheme.auditeeschemecode', '=', 'fp.auditeeschemecode')
            ->leftJoin('audit.followup_liability as l', 'l.followupid', '=', 'fp.followupid')
            ->leftJoin('audit.deptuserdetails as cb', 'cb.deptuserid', '=', 'fp.createdby')
            ->leftJoin('audit.lagacyfileupload as t3', 't3.followupid', '=', 'fp.followupid')
            ->leftJoin('audit.fileuploaddetail as t2', 't2.fileuploadid', '=', 't3.fileuploadid')




            ->groupBy([
                'inst.instid',
                'sev.severitycode',
                'fp.audityear',
                'type.typeofauditcode',
                'fp.followupid',
                'fp.paranumber',
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
                'fp.updatedon',
                'tp.typeofparaename',
                'tp.typeofparatname',
                'sp.stateofparaename',
                'sp.stateofparatname',
                'sub.subobjectionename',
                'sub.subobjectiontname',
                'subcat.irregularitiessubcatelname',
                'subcat.irregularitiessubcattlname',
                'scheme.auditeeschemeelname',
                'scheme.auditeeschemetlname',
                'ir.irregularitieselname',
                'ir.irregularitiestlname'
            ])

            ->when($paraid, function ($query) use ($paraid) {
                $query->where('para.paraid', $paraid);
            })

            ->orderBy('fp.followupid')
            ->orderBy('fp.paranumber', 'asc')
            ->limit(1)
            ->first();

        return $data;
    }



 public static function getmeetdate($data, $roleactioncode)
    {
        try {


            $subcatid = $data['subcatid'];

            $query = DB::table(self::$apms_hlc_table . ' as hlc')
                ->select(
                    'hlc.mom_date'

                )
                ->when($subcatid, function ($query) use ($subcatid) {
                    $query->where('hlc.subcatid', $subcatid);
                });
            if (in_array($roleactioncode, view::shared('dlc_roleaction'))) {
                $query->where('committee_level', $roleactioncode)
                    ->whereIn('hlc.statusflag', ['F', 'Y']);
            }
            if ($roleactioncode == view::shared('RJD_roleactioncode')) {
                $query->where('processcode', view::shared('frwd_to_approver'))
                    ->where('committee_level', $data['committee_level'])

                    ->whereIn('hlc.statusflag', ['F']);
            }
            // if (!empty($data['instid']) && !in_array('A', $data['instid'], true)) {
            //     $query->whereIn('inst.instid', $data['instid']);
            // }

            $query->where('hlc.catcode',   $data['catcode'])
                ->where('hlc.deptcode',  $data['deptcode'])
                ->where('hlc.distcode',  $data['distcode'])
                ->where('hlc.regioncode', $data['regioncode'])
                ->orderBy('hlc.updatedon', 'desc');

            return $query->get();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            $customMessage = 'An error occured while fetching details';
            throw new \Exception($e->getMessage(), 500);

            //  throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            DB::rollBack();

            throw new \Exception($e->getMessage(), 409);
        }
    }


    //------------------------------------------Retirement Para -start----------------------------------------------------//
    public static function deptfetch()
    {
        return DB::table(self::$deptartment_table . ' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname') // Select required columns
            ->where('dept.statusflag', '=', 'Y') // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }


    public static function regionfetch($deptcode)
    {
        return DB::table(self::$roletypemappingTable . ' as rtm')
            ->join(self::$region_table . ' as rt', 'rt.deptcode', '=', 'rtm.parentcode')
            ->join(self::$deptartment_table . ' as md', 'md.deptcode', '=', 'rtm.parentcode')
            ->join(self::$auditeinstmap . ' as map', 'map.regioncode', '=', 'rt.regioncode')
            ->where('map.statusflag', 'Y')
            ->where('rtm.deptcode', $deptcode)
            // ->where('rtm.roletypecode', $request->roletypecode)
            ->select('md.deptcode', 'rt.regionename', 'rt.regiontname', 'rt.regioncode')
            ->distinct()
            ->orderBy('rt.regionename', 'asc')
            ->get();
    }

    public static function fetch_districts($regioncode)
    {

        return DB::table(self::$auditeinstmap . ' as d')
            ->join(self::$dist_table . ' as di', 'di.distcode', '=', 'd.distcode')

            ->select('di.distename', 'di.distcode', 'di.disttname')
            ->where('d.regioncode', $regioncode) // Select required columns

            ->where('d.statusflag', '=', 'Y') // Use the correct table alias for `statusflag`
            ->distinct()
            ->orderBy('di.distcode', 'asc')

            ->get();
    }


    public static function getusernameforpsaauditors($deptcode, $regioncode, $distcode)
    {

        $table = self::$userdetail_table;

        $query = DB::table($table . ' as ut')
            ->select('ut.deptuserid', 'ut.username', 'ut.usertamilname', 'd.desigesname', 'ut.ifhrmsno', 'dept.currentquarter')
            ->join(self::$userchargedetails_table . ' as uc', 'uc.userid', '=', 'ut.deptuserid')
            ->join(self::$deptartment_table . ' as dept', 'dept.deptcode', '=', 'ut.deptcode')
            ->join(self::$chargedetails_table . ' as c', 'c.chargeid', '=', 'uc.chargeid')
            ->join(self::$rolemappingTable . ' as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
            ->join(self::$designation_table . ' as d', 'd.desigcode', '=', 'ut.desigcode')
            ->join(self::$region_table . ' as r', 'r.regioncode', '=', 'c.regioncode')
            ->join(self::$dist_table . ' as di', 'di.distcode', '=', 'c.distcode')
            //->where('ut.reservelist', 'N')
            ->where('uc.statusflag', 'Y')
            ->where('ut.statusflag', 'Y')
            ->where('ut.psaflag', 'Y')
            ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))
            ->where('ut.deptcode', $deptcode)
            ->where('c.regioncode', $regioncode)
            ->where('c.distcode', $distcode)

            ->distinct()
            ->orderBy('ut.username', 'asc')
            //              $querySql = $query->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            //      print_r($finalQuery);
            ->get();

        return $query;
    }


    public static function removepsaAuditors($pasuser, $userid)
    {

        return DB::table(self::$userdetail_table . ' as dept')
            ->where('dept.deptuserid', $pasuser)
            ->update([
                'psaflag' => 'N',   // or 1 based on your logic
                'updatedby' => $userid,
                'updatedon' => View::shared('get_nowtime')
            ]);
    }


    public static function fetchauditpsausers()
    {

        $table = self::$userdetail_table;

        $query = DB::table($table . ' as ut')
            ->select('ut.deptuserid', 'ut.username', 'ut.usertamilname', 'd.desigesname', 'r.regionename', 'ut.ifhrmsno', 'dept.currentquarter', 'dept.deptesname', 'di.distename', 'ut.psaflag')
            ->join(self::$userchargedetails_table . ' as uc', 'uc.userid', '=', 'ut.deptuserid')
            ->join(self::$deptartment_table . ' as dept', 'dept.deptcode', '=', 'ut.deptcode')
            ->join(self::$chargedetails_table . ' as c', 'c.chargeid', '=', 'uc.chargeid')
            ->join(self::$rolemappingTable . ' as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
            ->join(self::$designation_table . ' as d', 'd.desigcode', '=', 'ut.desigcode')
            ->join(self::$region_table . ' as r', 'r.regioncode', '=', 'c.regioncode')
            ->join(self::$dist_table . ' as di', 'di.distcode', '=', 'c.distcode')
            //->where('ut.reservelist', 'N')
            ->where('uc.statusflag', 'Y')
            ->where('ut.statusflag', 'Y')
            // ->where('ut.psaflag', 'Y')
            ->whereIn('rm.roleactioncode', [18, 19]) // ? KEY FIX
            // ->where('ut.deptcode', $deptcode)
            // ->where('c.regioncode', $regioncode)
            // ->where('c.distcode', $distcode)

            ->distinct()
            ->orderBy('ut.username', 'asc')
            //              $querySql = $query->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            //      print_r($finalQuery);
            ->get();

        return $query;
    }


    //----------------------------------------------------------------------PSA Retired Para Start------------------------------------------------------------------------------

public static function fetchresponsibilityremove()
    {

     $chargeData = session('charge');
    $userchargeid = $chargeData->userchargeid ?? null;
// dd($chargeData);
        $data = DB::table('audit.trans_para as tp')
            ->join('audit.trans_followup as tf', 'tf.followupid', '=', 'tp.followupid')
            ->join('audit.followup_liability as fl', 'fl.followupid', '=', 'tf.followupid')
            //->join('audit.apms_hlc as hl', 'hl.paraid', '=', 'tp.paraid')
            // ->where('tp.com_action', 'F')
              ->join('audit.apms_hlc as hl', function ($join) {
            $join->whereRaw('hl.paraid @> to_jsonb(array[tp.paraid])');
        })
        ->where('hl.createdbyuserchargeid', $userchargeid)
           //  ->where('hl.createdbyuserchargeid', $userchargeid)
            // ->where('fl.statusflag', 'C')
            ->distinct('tp.followupid')
            ->orderBy('tp.followupid', 'asc')
            ->get([
                'tp.actioncode',
                'tp.com_action',
                'tp.rejoindercycle',
                'tp.auditee_liability',
                'tp.paraid',
                'tf.paranumber',
                'tf.slipdetails',
                'fl.statusflag'
            ]);

        return $data;   // ✅ return collection directly
    }
    public static function fetchretirenmentparsa()
    {
        $chargedet = session('charge');
        $deptcode   = $chargedet->deptcode;
        $regioncode = $chargedet->regioncode;
        $distcode   = $chargedet->distcode;
       $userchargeid   = $chargedet->userchargeid;


        // ✅ Fetch rejoinder limit for dept
        $rejoinderLimit = DB::table('audit.mst_dept')
            ->where('deptcode', $chargedet->deptcode)
            ->value('pararejoinderlimit');

        // ✅ Fetch retirement paras
        $paras = DB::table('audit.trans_para as tp')
            ->join('audit.trans_followup as tf', 'tf.followupid', '=', 'tp.followupid')
            ->join('audit.followup_liability as fl', 'fl.followupid', '=', 'tf.followupid')
            ->join('audit.mst_institution as i', 'i.instid', '=', 'tf.instid')
            ->where('fl.retiredflag', 'L')
            ->where('tp.processcode', view::shared('auditee_to_rtdcom'))
	   ->where('tp.forwardedtouserchargeid',$userchargeid)
	    ->distinct('tp.followupid')
            ->orderBy('tp.followupid', 'asc')
	    ->orderBy('tp.updatedon', 'desc')
            ->get([
                'tp.actioncode',
                'tp.com_action',
                'tp.rejoindercycle',
                'tp.auditee_liability',
                'tp.paraid',
                'tf.parano',
                'tf.slipdetails',
		'tp.updatedon as retirement_para_date',
            ]);

        return [
            'paras' => $paras,
            'rejoinderLimit' => $rejoinderLimit,
        ];
    }

    public static function fetch_para_datasforremoval($paraid)
    {


        $data = DB::table(self::$transpara_table . ' as para')
            ->join(self::$transfollowup_table . ' as fp', 'para.followupid', '=', 'fp.followupid')

            ->select([
                'inst.instename',
                'inst.insttname',
                'tp.typeofparaename',
                'tp.typeofparatname',
                'sp.stateofparaename',
                'sp.stateofparatname',
                'fp.paranumber',
                'fp.lastactionyear',
                'fp.lastactionmonth',
                'main.objectionename',
                'main.objectiontname',
                'sub.subobjectionename',
                'sub.subobjectiontname',
                'fp.amtinvolved',
                'sev.severityelname',
                'sev.severitytlname',
                'fp.schemastatus',
                'scheme.auditeeschemeelname',
                'scheme.auditeeschemetlname',
                'ir.irregularitieselname',
                'ir.irregularitiestlname',
                'cat.irregularitiescatelname',
                'cat.irregularitiescattlname',
                'subcat.irregularitiessubcatelname',
                'subcat.irregularitiessubcattlname',
                'fp.slipdetails',
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

                DB::raw("
            STRING_AGG(
                DISTINCT CASE
                    WHEN (l.statusflag = 'Y') THEN
                        CONCAT(
                            COALESCE(l.notype, ''), '-',
                            COALESCE(l.liabilitygpfno, ''), '-',
                            COALESCE(l.liabilityname, ''), '-',
                            COALESCE(l.liabilitydesignation::TEXT, ''), '-',
                            COALESCE(l.liabilityamount::TEXT, ''), '-',
                            COALESCE(l.retirementyear::TEXT, ''), '-',
                            COALESCE(l.retirementmonth::TEXT, ''), '-',
                             COALESCE(l.retiredflag::TEXT, ''), '-',
                            COALESCE(l.statusflag::TEXT, ''),'-',
                           COALESCE(l.followupliabilityid::TEXT, '')
                           


                            
                        )
                    ELSE NULL
                END, ','
            ) AS liabilitydel
        "),

                // 'fp.audityear',
                DB::raw("COALESCE(fp.remarks::json->>'content', '') AS remarks"),
                'fp.liability',
                'cb.username as createdbyusername',
                'fp.updatedon',
                'type.typeofauditename',
                'type.typeofaudittname'
            ])
            ->Join(self::$institution_table . ' as inst', 'inst.instid', '=', 'fp.instid')
            ->leftJoin('audit.mst_typeofpara as tp', 'tp.typeofparacode', '=', 'fp.typeofparacode')
            ->leftJoin('audit.mst_stateofpara as sp', 'sp.stateofparacode', '=', 'fp.stateofparacode')
            ->join('audit.mst_typeofaudit as type', 'type.typeofauditcode', '=', 'fp.typeofauditcode')
            ->join('audit.mst_mainobjection as main', 'main.mainobjectionid', '=', 'fp.mainobjectionid')
            ->join('audit.mst_subobjection as sub', 'sub.subobjectionid', '=', 'fp.subobjectionid')
            ->join('audit.mst_severity as sev', 'sev.severitycode', '=', 'fp.severitycode')
            ->join('audit.mst_irregularitiessubcategory as subcat', 'subcat.irregularitiessubcatcode', '=', 'fp.irregularitiessubcatcode')
            ->join('audit.mst_irregularitiescategory as cat', 'cat.irregularitiescatcode', '=', 'fp.irregularitiescatcode')
            ->join('audit.mst_irregularities as ir', 'ir.irregularitiescode', '=', 'fp.irregularitiescode')

            ->leftJoin('audit.auditeescheme as scheme', 'scheme.auditeeschemecode', '=', 'fp.auditeeschemecode')
            ->leftJoin('audit.followup_liability as l', 'l.followupid', '=', 'fp.followupid')
            ->leftJoin('audit.deptuserdetails as cb', 'cb.deptuserid', '=', 'fp.createdby')
            ->leftJoin('audit.lagacyfileupload as t3', 't3.followupid', '=', 'fp.followupid')
            ->leftJoin('audit.fileuploaddetail as t2', 't2.fileuploadid', '=', 't3.fileuploadid')




            ->groupBy([
                'inst.instid',
                'sev.severitycode',
                'fp.audityear',
                'type.typeofauditcode',
                'fp.followupid',
                'fp.paranumber',
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
                'fp.updatedon',
                'tp.typeofparaename',
                'tp.typeofparatname',
                'sp.stateofparaename',
                'sp.stateofparatname',
                'sub.subobjectionename',
                'sub.subobjectiontname',
                'subcat.irregularitiessubcatelname',
                'subcat.irregularitiessubcattlname',
                'scheme.auditeeschemeelname',
                'scheme.auditeeschemetlname',
                'ir.irregularitieselname',
                'ir.irregularitiestlname'
            ])

            ->when($paraid, function ($query) use ($paraid) {
                $query->where('para.paraid', $paraid);
            })

            ->orderBy('fp.followupid')
            ->orderBy('fp.paranumber', 'asc')
            ->limit(1)
            ->first();

        return $data;
    }


    public static function fetchparaction($sesroleactioncode)
    {
        return DB::table('audit.mst_actionsonpara as ap')
            ->select('ap.*')
            ->where('ap.statusflag', 'Y')
            ->whereJsonContains('ap.roleaction', (string) $sesroleactioncode)
            ->orderBy('ap.actionid', 'asc')
            ->get();
    }

    public static function fetch_liabiltydetails($paraid)
    {
        return DB::table('audit.followup_liability as ty')
            ->join('audit.trans_para as tp', 'tp.followupid', '=', 'ty.followupid')
            ->join('audit.mst_auditperiod as ap', function ($join) {
                $join->whereRaw(
                    'ap.auditperiodid IN (
                    SELECT jsonb_array_elements_text(tp.audityear)::int
                )'
                );
            })
            ->where('tp.statusflag', 'Y')
            ->where('tp.paraid', $paraid)
            ->select(
                'ty.*',
                DB::raw("concat(ap.fromyear, ' - ', ap.toyear) as auditperiod")
            )
            ->orderBy('ty.followupliabilityid', 'asc')
            ->get();
    }

    public static function updateLiabilityStatus(
        $followupliabilityid,
        $statusflag,
        $deptcode,
        $regioncode,
        $distcode
    ) {
        $user = session('user');
        return DB::table('audit.followup_liability')
            ->where('followupliabilityid', $followupliabilityid)
            // ->where('deptcode', $deptcode)
            // ->where('regioncode', $regioncode)
            // ->where('distcode', $distcode)
            ->update([
                'statusflag' => $statusflag,
                'updatedby' => $user->userid,
                'updatedon' =>  View::shared('get_nowtime')
            ]);
    }



  
    public static function updateRetirementParaAction($paraid, $actionid, $mode, $fileuploadId, $mom_date, $deptcode, $regioncode, $distcode, $Remarks)
    {
        DB::beginTransaction();

        try {

            $chargedet = session('charge');
            $user = session('user');
            $now = View::shared('get_nowtime');

            if ($actionid === '01') {
                $processcode = 'A';
            } elseif ($actionid === '02') {
                $processcode = 'X';
            } elseif ($actionid === '03') {
                $processcode = 'L';
            } else {
                $processcode = 'L';
            }

            if ($mode === 'draft') {

                DB::table('audit.trans_para')
                    ->where('paraid', $paraid)
                    ->update([
                        'actioncode' => $actionid,
                        'com_action' => 'Y',
                        'updatedon' => $now,
                        'updatedby' => $user->userid,
                    ]);

                DB::commit();
                return true;
            }

            $apmsHlcId = DB::table('audit.apms_hlc')->insertGetId([
                'paraid' => json_encode([$paraid]),
                'fileuploadid' => $fileuploadId,
                'mom_date' => $mom_date,
                'deptcode' => $deptcode,
                'regioncode' => $regioncode,
                'distcode' => $distcode,
                'statusflag' => 'Y',
                'committee_level' => $chargedet->roleactioncode,
                'createdby' => $user->userid,
                'createdbyuserchargeid' => $chargedet->userchargeid,
                'createdon' => $now,
                'updatedby' => $user->userid,
                'updatedbyuserchargeid' => $chargedet->userchargeid,
                'updatedon' => $now,
            ], 'apms_hlcid');

            $updateData = [
                'actioncode' => $actionid,
                'com_action' => 'F',
                'processcode' => $processcode,
                'remarks' => $Remarks,
                'usertypecode' => $chargedet->usertypecode,
                'actroleactioncode' => View::shared('rtdcom_actroleactioncode'),
                'forwardedtouserid' => DB::raw('createdby'),
                'apms_hlcid' => $apmsHlcId,
                'updatedon' => $now,
                'updatedby' => $user->userid,
            ];

            if ($processcode === 'L' && $actionid = '03') {
                $updateData['rejoindercycle'] = DB::raw('COALESCE(rejoindercycle, 0) + 1');
                $updateData['rejoinderstatus'] = 'Y';
            }
            if ($actionid === '02') {
                $updateData['rejectcount'] = DB::raw('COALESCE(rejectcount, 0) + 1');
            }
            $updated = DB::table('audit.trans_para')
                ->where('paraid', $paraid)
                ->update($updateData);


            if (!$updated) {
                throw new \Exception('Failed to update trans_para');
            }

            $currentPara = DB::table('audit.trans_para')
                ->where('paraid', $paraid)
                ->first();

            if (!$currentPara) {
                throw new \Exception('Para not found after update');
            }

            $historyExists = DB::table('audit.historytrans_para')
                ->where('paraid', $paraid)
                ->exists();

            if ($historyExists) {
                DB::table('audit.historytrans_para')
                    ->where('paraid', $paraid)
                    ->where('transstatus', 'A')
                    ->update([
                        'transstatus' => 'I'
                    ]);
            }

            DB::table('audit.historytrans_para')->insert([
                'instid' => $currentPara->instid,
                'paraid' => $currentPara->paraid,
                'audityear' => $currentPara->audityear,
                'followupid' => $currentPara->followupid,
                'paranumber' => $currentPara->paranumber,
                'para_remarks' => 'null',
                'processcode' => $currentPara->processcode,
                'statusflag' => $currentPara->statusflag,
                'rejoinderstatus' => $currentPara->rejoinderstatus,
                'rejoindercycle' => $currentPara->rejoindercycle,
                'createdby' => $user->userid,
                'createdon' => $now,
                'transactionno' => $currentPara->transactionno,
                'transstatus' => 'A',
                'usertypecode' => $chargedet->usertypecode,

                'actioncode' => $currentPara->actioncode,
                'actroleactioncode' => $currentPara->actroleactioncode,
                'paratype' => $currentPara->paratype,
                'rejectcount' => $currentPara->rejectcount,
                'com_action' => $currentPara->com_action,
                'remarks' => $currentPara->remarks,

                'forwardedbyuserid' => $currentPara->updatedby,
                'forwardedbychargeid' => $chargedet->userchargeid,
                'forwardedtouserid' => $currentPara->forwardedtouserid,
                'forwardedtochargeid' => $currentPara->forwardedtouserchargeid,
                'forwardedon' => $now,
                'apms_hlcid' => $currentPara->apms_hlcid,
            ]);


            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


 
    //------------------------------------------Retirement Para -end------------------------------------------------------//

    public static function get_removedLiability($paraid)
    {
        try {

            $query = DB::table(self::$transpara_table . ' as para')
                ->join(self::$transfollowup_table . ' as tf', 'para.followupid', '=', 'tf.followupid')
                ->join(self::$followupliability_table . ' as l', 'l.followupid', '=', 'tf.followupid')
                ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'tf.instid')

                ->Join(DB::raw("
                        LATERAL (
                            SELECT string_agg(DISTINCT CONCAT(yr.fromyear, ' - ', yr.toyear), ', ') AS audit_period
                            FROM jsonb_array_elements_text(tf.audityear) AS ay(auditperiodid)
                            JOIN audit.mst_auditperiod AS yr ON yr.auditperiodid = ay.auditperiodid::int
                        ) AS auditinfo
                    "), DB::raw('TRUE'), '=', DB::raw('TRUE'))
                ->where('l.statusflag', 'C')
                ->where('para.paraid', $paraid)
                //   ->where('tf.followupid', $followupid)
                ->select('l.liabilityname', 'l.liabilitygpfno', 'auditinfo.audit_period', 'tf.parano', 'inst.instename', 'insttname', 'l.updatedon')
                ->get();
            return $query;
        } catch (\Illuminate\Database\QueryException $e) {


            $customMessage = 'An error occured while fetching details';
            throw new \Exception($e->getMessage(), 500);

            //  throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function get_inactiveLiability($followupid)
    {
        try {

            $query = DB::table(self::$transfollowup_table . ' as tf')
                ->join(self::$followupliability_table . ' as l', 'l.followupid', '=', 'tf.followupid')
                ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'tf.instid')

                ->Join(DB::raw("
                        LATERAL (
                            SELECT string_agg(DISTINCT CONCAT(yr.fromyear, ' - ', yr.toyear), ', ') AS audit_period
                            FROM jsonb_array_elements_text(tf.audityear) AS ay(auditperiodid)
                            JOIN audit.mst_auditperiod AS yr ON yr.auditperiodid = ay.auditperiodid::int
                        ) AS auditinfo
                    "), DB::raw('TRUE'), '=', DB::raw('TRUE'))
                ->where('l.statusflag', 'C')
                ->where('tf.followupid', $followupid)
                ->select('l.liabilityname', 'l.liabilitygpfno', 'auditinfo.audit_period', 'tf.parano', 'inst.instename', 'insttname', 'l.updatedon')
                ->get();
            return $query;
        } catch (\Illuminate\Database\QueryException $e) {


            $customMessage = 'An error occured while fetching details';
            throw new \Exception($e->getMessage(), 500);

            //  throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }


     public static function get_responsible_Liability($followupid)
    {
        try {

            $query = DB::table(self::$transfollowup_table . ' as tf')
                ->join(self::$followupliability_table . ' as l', 'l.followupid', '=', 'tf.followupid')
                ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'tf.instid')

                ->Join(DB::raw("
                        LATERAL (
                            SELECT string_agg(DISTINCT CONCAT(yr.fromyear, ' - ', yr.toyear), ', ') AS audit_period
                            FROM jsonb_array_elements_text(tf.audityear) AS ay(auditperiodid)
                            JOIN audit.mst_auditperiod AS yr ON yr.auditperiodid = ay.auditperiodid::int
                        ) AS auditinfo
                    "), DB::raw('TRUE'), '=', DB::raw('TRUE'))
                ->where('l.statusflag', 'C')
                ->where('tf.followupid', $followupid)
                ->select('l.liabilityname', 'l.liabilitygpfno', 'auditinfo.audit_period', 'tf.parano', 'inst.instename', 'insttname', 'l.updatedon')
                ->get();
            return $query;
        } catch (\Illuminate\Database\QueryException $e) {


            $customMessage = 'An error occured while fetching details';
            throw new \Exception($e->getMessage(), 500);

            //  throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function region_valfetch()
    {
        $table = self::$region_table;

        $session  = session('charge');
        $deptcode = $session->deptcode;

        return DB::table($table . ' as reg')

            ->select('reg.regioncode', 'reg.regionename', 'reg.regiontname')
            ->distinct()
            ->where('reg.statusflag', 'Y')
            ->when($deptcode, function ($query) use ($deptcode) {
                $query->where('reg.deptcode', '=', $deptcode);
            })
            ->orderBy('reg.regionename', 'ASC')
            ->get();
    }

    public static function fetch_apms_hlcdetails($data, $roleactioncode)
    {
        try {




            $fileAgg = DB::table(self::$apms_hlc_table . ' as h')
                ->leftJoin(self::$fileuploaddetail_table . ' as fu', 'fu.fileuploadid', '=', 'h.fileuploadid')
                ->select(
                    'h.apms_hlcid',
                    DB::raw("
                     STRING_AGG(
                         CASE
                             WHEN h.fileuploadid != 0
                             THEN CONCAT(fu.filename, '-', fu.filepath, '-', fu.filesize, '-', fu.fileuploadid)
                             ELSE '-'
                         END,
                         ',' ORDER BY fu.fileuploadid
                     ) AS filedetails
                 ")
                )
                ->groupBy('h.apms_hlcid');


            $query = DB::table(self::$apms_hlc_table . ' as hlc')
                ->leftJoinSub($fileAgg, 'fa', function ($join) {
                    $join->on('fa.apms_hlcid', '=', 'hlc.apms_hlcid');
                })
                ->join(self::$roleactionTable . ' as role', 'hlc.committee_level', '=', 'role.roleactioncode')
                ->join(self::$deptartment_table . ' as dept', 'hlc.deptcode', '=', 'dept.deptcode')
                ->join(self::$region_table . ' as reg', 'hlc.regioncode', '=', 'reg.regioncode')
                ->join(self::$dist_table . ' as dist', 'hlc.distcode', '=', 'dist.distcode')
                ->join(self::$mstauditeeinscategory_table . ' as cat', 'hlc.catcode', '=', 'cat.catcode')
                ->leftJoin(self::$subcategory_table . ' as subcat', 'hlc.subcatid', '=', 'subcat.auditeeins_subcategoryid')


                ->when($roleactioncode ?? null, fn($q, $v) => $q->where('hlc.committee_level', $v))
                ->when($data['subcatid'] ?? null, fn($q, $v) => $q->where('hlc.subcatid', $v))

                ->when($data['catcode'] ?? null, fn($q, $v) => $q->where('hlc.catcode', $v))
                ->when($data['deptcode'] ?? null, fn($q, $v) => $q->where('hlc.deptcode', $v))
                ->when($data['distcode'] ?? null, fn($q, $v) => $q->where('hlc.distcode', $v))
                ->when($data['regioncode'] ?? null, fn($q, $v) => $q->where('hlc.regioncode', $v))
                ->when($data['apms_hlcid'] ?? null, fn($q, $v) => $q->where('hlc.apms_hlcid', $v))
                ->select(
                    'hlc.apms_hlcid',
                    'hlc.updatedbyuserchargeid',
                    'hlc.approved_para',
                    'hlc.rejected_para',
                    'hlc.mom_date',
                    'hlc.deptcode',
                    'hlc.regioncode',
                    'hlc.distcode',
                    'hlc.instid',
                    'hlc.paraid',
                    'hlc.catcode',
                    'hlc.subcatid',
                    'hlc.actioncode',
                    'hlc.processcode',
                    'hlc.committee_level',
                    'hlc.followup_action_map',
                    'role.roleactionelname',
                    'reg.regionename',
                    'reg.regiontname',
                    'dist.distename',
                    'dist.disttname',
                    'dept.deptesname',
                    'dept.deptelname',
                    'cat.catename',
                    'cat.cattname',
                    'subcat.subcatename',
                    'subcat.subcattname',
                    DB::raw("COALESCE(fa.filedetails, '-') as filedetails")
                );

            $apmsRejected = DB::table(self::$apms_hlc_table)
                ->where('committee_level', $roleactioncode)
                ->where('processcode', View::shared('reject_dlcpara'))
                ->latest('updatedon')
                ->select('apms_hlcid', 'rejected_para')
                ->first();

            $apms_hlcid_rejected = $apmsRejected->apms_hlcid ?? null;
            $rejectedFollowupIds = json_decode($apmsRejected->rejected_para ?? '[]', true);

            if (!empty($rejectedFollowupIds)) {

                $query->where('hlc.apms_hlcid', $apms_hlcid_rejected);
                return $query->get();
            }
            if ($roleactioncode == view::share('RJD_roleactioncode')) {
                $query->where('hlc.statusflag', 'F');
            } else {
                $query->where('hlc.statusflag', 'Y');
            }
            // $querySql = $query->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);


            return $query->get();
        } catch (\Illuminate\Database\QueryException $e) {

            $customMessage = 'An error occured while fetching details';
            throw new \Exception($e->getMessage(), 500);

            //  throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {


            throw new \Exception($e->getMessage(), 409);
        }
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


    public static function fetch_dhlcparadetails($data, $apms_hlcid, $roleactioncode, $actroleactioncode)
    {
        try {

            $subcatid = $data['subcatid'];

            $allowedProcessCodes = self::getProcessCodesByRole($roleactioncode);
 $sessioncharge = session('charge');
            $sessiondeptcode = $sessioncharge->deptcode;
			
            /*
        |--------------------------------------------------------------------------
        | Liability Aggregation
        |--------------------------------------------------------------------------
        */

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

            /*
        |--------------------------------------------------------------------------
        | Main Query
        |--------------------------------------------------------------------------
        */

            $query = DB::table(self::$transfollowup_table . ' as fp')

                //     ->Leftjoin(DB::raw("
                //       audit.apms_hlc hlc
                //       CROSS JOIN LATERAL jsonb_to_recordset(hlc.followup_action_map)
                //       AS m(followupid int, actioncode text)
                //   "), 'fp.followupid', '=', 'm.followupid')

                ->leftJoin(self::$apms_hlc_table . ' as hlc', 'fp.apms_hlcid', '=', 'hlc.apms_hlcid')

                ->leftJoin(self::$transpara_table . ' as para', 'fp.followupid', '=', 'para.followupid')

                ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'fp.instid')

                ->leftJoin(self::$mst_stateofpara_table . ' as st', 'st.stateofparacode', '=', 'fp.stateofparacode')

                ->join(self::$mainobjection_table . ' as main', 'main.mainobjectionid', '=', 'fp.mainobjectionid')

                ->join(self::$irregularirites_table . ' as ir', 'ir.irregularitiescode', '=', 'fp.irregularitiescode')

                ->join(self::$subobjection_table . ' as sub', 'sub.subobjectionid', '=', 'fp.subobjectionid')

                ->join(self::$irregularirites_subcategory_table . ' as subcat', 'subcat.irregularitiessubcatcode', '=', 'fp.irregularitiessubcatcode')

                ->join(self::$irregularirites_category_table . ' as cat', 'cat.irregularitiescatcode', '=', 'fp.irregularitiescatcode')

                /*
            |--------------------------------------------------------------------------
            | Audit Period Extraction
            |--------------------------------------------------------------------------
            */

                ->Join(DB::raw("
                LATERAL (
                    SELECT string_agg(DISTINCT CONCAT(yr.fromyear,' - ',yr.toyear), ', ') AS audit_period
                    FROM jsonb_array_elements_text(fp.audityear) AS ay(auditperiodid)
                    JOIN audit.mst_auditperiod yr
                    ON yr.auditperiodid = ay.auditperiodid::int
                ) auditinfo
            "), DB::raw('TRUE'), '=', DB::raw('TRUE'))

                ->leftJoinSub($liabAgg, 'liab', 'liab.followupid', '=', 'fp.followupid')

                /*
            |--------------------------------------------------------------------------
            | Select Fields
            |--------------------------------------------------------------------------
            */

                ->select(
                    'fp.parano',
                    'fp.slipdetails',
                    'fp.followupid',

                    'fp.instid',
                    'main.objectionename',
                    'main.objectiontname',
                    'hlc.approver_remarks',

                    'para.paraid',

                    'para.processcode',

                    'ir.irregularitieselname',
                    'ir.irregularitiestlname',

                    'auditinfo.audit_period',

                    'subcat.irregularitiessubcatelname',
                    'subcat.irregularitiessubcattlname',

                    'cat.irregularitiescatelname',
                    'cat.irregularitiescattlname',

                    'liab.liabilitydel',

                    'inst.instename',
                    'inst.insttname',

                    'hlc.apms_hlcid'
                );
            $apmsRejected = DB::table(self::$apms_hlc_table)
                ->where('committee_level', $roleactioncode)
                ->where('processcode', View::shared('reject_dlcpara'))
                ->latest('updatedon')
                ->select('apms_hlcid', 'rejected_para', 'approved_para', 'selected_paras')
                ->first();


            $apms_hlcid_rejected = $apmsRejected->apms_hlcid ?? null;
            $rejectedFollowupIds = json_decode($apmsRejected->rejected_para ?? '[]', true);
            $rejectedFollowupIds = $rejectedFollowupIds ?? [];

            $approvedFollowupIds = json_decode($apmsRejected->approved_para ?? '[]', true);
            $approvedFollowupIds = $approvedFollowupIds ?? [];

            $selectedFollowupIds = json_decode($apmsRejected->selected_paras ?? '[]', true);
            $selectedFollowupIds = $selectedFollowupIds ?? [];


            $query->where(function ($q) use ($roleactioncode) {
                $q->whereNull('hlc.apms_hlcid')
                    ->orWhere(function ($q2) use ($roleactioncode) {
                        $q2->where('hlc.committee_level', '!=', $roleactioncode)
                            ->orWhere('hlc.statusflag', '!=', 'F')
                            ->orWhere('hlc.processcode', '=', 'R');
                    });
            });


            /*
        |--------------------------------------------------------------------------
        | Optional Filters for inst if subcategory
        |--------------------------------------------------------------------------
        */

            $query->when($subcatid, function ($q) use ($subcatid) {
                $q->where('inst.subcatid', $subcatid);
            });

            if (!empty($data['instid']) && !in_array('A', $data['instid'], true)) {
                $query->whereIn('inst.instid', $data['instid']);
            }


            /*
        |--------------------------------------------------------------------------
        | Role Based State Filter
        |--------------------------------------------------------------------------
        */

            $stateMap = [
                view::shared('dlc_roleactioncode')  => view::shared('district_hlc_state'),
                view::shared('dehc_roleactioncode') => view::shared('dept_hlc_state'),
                view::shared('shlc_roleactioncode') => view::shared('state_hlc_state'),
            ];

            /*
        |--------------------------------------------------------------------------
        | Filter to inlxude DLC paras, and 2 times rejected paras of particular categories
        |--------------------------------------------------------------------------
        */
            $query->where(function ($q) use ($roleactioncode, $stateMap, $allowedProcessCodes, $sessiondeptcode) {

                // State condition
                if (isset($stateMap[$roleactioncode])) {
                    $q->where('fp.stateofparacode', $stateMap[$roleactioncode])
                        ->where(function ($sub) {

                            $sub->whereNotIn(
                                'para.processcode',
                                [
                                    View::shared('rtdcom_to_auditee'),
                                    View::shared('auditee_to_rtdcom'),
                                    View::shared('Reject'),
                                    View::shared('Forward'),
                                    View::shared('parts_removal_processcode'),
                                    View::shared('respons_removal_processcode'),
                                    'K',
                                    View::shared('dehlc_to_auditee'),
                                    View::shared('paraaccept'),
                                    View::shared('DLC_to_auditee'),
                                    View::shared('slc_to_auditee')
                                ]
                            )
                                ->orWhereNull('para.processcode');
                        });
                }

                // DLC reject rule
                if ($roleactioncode == View::shared('dlc_roleactioncode')) {
                    $q->orWhere(function ($sub) use ($sessiondeptcode) {
                        $sub->where('para.rejectcount', 2)
                            ->whereNotExists(function ($hlc) use ($sessiondeptcode) {

                               $hlc->select(DB::raw(1))
                                    ->from('audit.mst_auditeeins_subcategory as subcat')
                                    ->whereColumn('subcat.catcode', 'inst.catcode')
                                    ->whereColumn('subcat.auditeeins_subcategoryid', 'inst.subcatid')
                                    ->where(function ($q) {
                                        $q->where('subcat.boardflag', 'N')
                                            ->orWhereNull('subcat.boardflag');
                                    })->where('subcat.statusflag', 'Y');
                            });
                    });
                }

                if ($roleactioncode == View::shared('dehc_roleactioncode')) {
                    $q->orWhere(function ($sub) use ($sessiondeptcode) {
                        $sub->where('para.rejectcount', 2)
                            ->whereIn('para.processcode', ['I', 'X'])
                            ->whereExists(function ($hlc) use ($sessiondeptcode) {
                                $hlc->select(DB::raw(1))
                                    ->from('audit.mst_auditeeins_subcategory as subcat')
                                    ->whereColumn('subcat.catcode', 'inst.catcode')
                                    ->whereColumn('subcat.auditeeins_subcategoryid', 'inst.subcatid')
                                    ->where('subcat.boardflag', 'Y')
                                    ->where('subcat.statusflag', 'Y');
                            });
                    });
                }



                /*
                 |--------------------------------------------------------------------------
                 | Allowed processcode OR HLC condition
                 |--------------------------------------------------------------------------
                  */

                $q->orWhere(function ($q2) use ($roleactioncode, $allowedProcessCodes) {

                    // Allow paras with allowed processcodes


                    $filteredCodes = array_diff($allowedProcessCodes, ['I', 'X']);

                    if (!empty($filteredCodes)) {
                        $q2->whereIn('para.processcode', $filteredCodes);
                    }

                                   });
            });

            // statusflag should be mandatory
            $query->where('fp.statusflag', 'F');


            /*
        |--------------------------------------------------------------------------
        | Institution Filters
        |--------------------------------------------------------------------------
        */

            $query->where('inst.deptcode', $data['deptcode'])
                ->where('inst.distcode', $data['distcode'])
                ->where('inst.regioncode', $data['regioncode'])
                ->where('inst.catcode', $data['catcode']);

            /*
        |--------------------------------------------------------------------------
        | Final Filters-- for differentiating selected,remaining,rejected paras
        |--------------------------------------------------------------------------
        */
            $query->addSelect(
                DB::raw("
                 CASE
                     WHEN fp.followupid IN (" . (count($rejectedFollowupIds) ? implode(',', $rejectedFollowupIds) : '0') . ")
                     THEN 'Y'
                     ELSE 'N'
                 END as is_rejected
             ")
            );


            $query->addSelect(
                DB::raw("
                 CASE
                     WHEN fp.followupid IN (" . (count($approvedFollowupIds) ? implode(',', $approvedFollowupIds) : '0') . ")
                     THEN 'Y'
                     ELSE 'N'
                 END as is_approved
             ")
            );

            $query->addSelect(
                DB::raw("
                 CASE
                     WHEN fp.followupid IN (" . (count($selectedFollowupIds) ? implode(',', $selectedFollowupIds) : '0') . ")
                     THEN 'Y'
                     ELSE 'N'
                 END as is_selected
             ")
            );


            $query->where('fp.statusflag', 'F')
                ->orderBy('fp.updatedon', 'asc');
            // $querySql = $query->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);
            return $query->get();
        } catch (\Illuminate\Database\QueryException $e) {


            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {


            throw new \Exception($e->getMessage(), 409);
        }
    }
    public static function insert_apms_hlc($data, $apms_hlcid, $roleactioncode)
    {
        try {
            $table = self::$apms_hlc_table;
            $query = DB::table($table);
            if ($apms_hlcid) {
                $query->where('apms_hlcid', '!=', $apms_hlcid);
            }

            $existingdata = (clone $query)
                ->where('deptcode', $data['deptcode'])
                ->where('distcode', $data['distcode'])
                ->where('catcode', $data['catcode'])
                ->where('subcatid', $data['subcatid'])
                ->where('committee_level', $roleactioncode)
                ->where('mom_date', $data['mom_date'])
                ->whereIn('statusflag', ['Y', 'F'])
                ->first();

            // $querySql = $existingdata->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);
            //return $existingdata;
            if ($existingdata) {
                throw new \Exception('The APMS HLC details was already exists for this date ');
            }
            if ($apms_hlcid) {

                $affectedRows = DB::table($table)
                    ->where('apms_hlcid', $apms_hlcid)
                    ->update($data);

                if ($affectedRows === 0) {

                    throw new \Exception('Failed to update the record.');
                }

                $insertedRecord = DB::table($table)
                    ->select('apms_hlcid')
                    ->where('apms_hlcid', $apms_hlcid)
                    ->first();
                return $insertedRecord;
            } else {
                // return $data['instid'];

                $newRecordId = DB::table($table)->insertGetId($data, 'apms_hlcid');
                $insertedRecord = DB::table($table)
                    ->select('apms_hlcid')
                    ->where('apms_hlcid', $newRecordId)
                    ->first();
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }

                return $insertedRecord;
            }    // Return the ID of the newly in
        } catch (\Illuminate\Database\QueryException $e) {
            //$customMessage = 'A error occurred while saving minutes of meeting data';

            //throw new \Exception($customMessage, 500);
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }


    public static function getparavalues(array $params, $cond)
    {
        try {

            $query = DB::table(self::$transpara_table)
                ->select($params)
                ->where('paraid', $cond)
                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while fetching para data';

            //throw new \Exception($customMessage, 500);
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function updatepara_dlc($data, $paraid)
    {

        try {
            throw_if(empty($paraid),      new \Exception("Para Details not found"));

            $updatedIDS = DB::table(self::$transpara_table)
                ->where('paraid', $paraid)
                ->update($data);

            if ($updatedIDS) {
                $insertedRecord = DB::table(self::$transpara_table)
                    ->select(
                        'instid',
                        'followupid',
                        'instid',
                        'audityear',
                        'paranumber',
                        'createdby',
                        'rejectcount',
                        'rejoinderstatus',
                        'paratype',
                        'rejoindercycle',
                        // DB::raw('(audityear) as audityear'),
                        'transactionno',

                    )
                    ->where('paraid', $paraid)
                    ->first();

                return $insertedRecord;
            } else
                return false;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while saving dlc para data';

            //throw new \Exception($customMessage, 500);
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function insert_paradlchistorydata($historydata, $paraid)
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

            // Insert the new history transaction record
            $historytransdel = DB::table(self::$historytranspara_table)
                ->insertGetId($historydata, 'transparahistoryid');


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

            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function insert_apms_historydata($historydata, $paraid)
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

            // Insert the new history transaction record
            $historytransdel = DB::table(self::$historytranspara_table)
                ->insertGetId($historydata, 'transparahistoryid');
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
    public static function insert_apmshlc_historydata($historydata, $apms_hlcid)
    {

        throw_if(empty($apms_hlcid), new \Exception("Para details not found"));

        DB::beginTransaction();
        try {
            $isUpdated = false;
            $isInserted = false;

            // Check if the auditslipid condition is provided and exists
            if ($apms_hlcid !== null) {
                $paraidExists = DB::table(self::$historytrans_hlc . ' as hist')
                    ->where('hist.apms_hlcid', $apms_hlcid)
                    ->exists();

                // Update the existing record if auditslipid exists
                if ($paraidExists) {
                    $updateCount = DB::table(self::$historytrans_hlc)
                        ->where('apms_hlcid', $apms_hlcid)
                        ->update(['transstatus' => 'I']);

                    $isUpdated = $updateCount > 0;
                }
            }

            // Insert the new history transaction record
            $historytransdel = DB::table(self::$historytrans_hlc)
                ->insertGetId($historydata, 'historyhlcid');


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

            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), 409);
        }
    }


    public static function fetch_parastatus_hlc($apms_hlcid)
    {
        try {




            $query = DB::table(self::$apms_hlc_table . ' as hlc')
                ->join(self::$transfollowup_table . ' as fp', 'hlc.apms_hlcid', '=', 'fp.apms_hlcid')
                ->leftJoin(self::$transpara_table . ' as para', 'para.followupid', '=', 'fp.followupid')
                ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'fp.instid')
                ->leftJoin(self::$fileuploaddetail_table . ' as fu', 'fu.fileuploadid', '=', 'hlc.fileuploadid')


                ->Join(DB::raw("
                        LATERAL (
                            SELECT string_agg(DISTINCT CONCAT(yr.fromyear, ' - ', yr.toyear), ', ') AS audit_period
                            FROM jsonb_array_elements_text(fp.audityear) AS ay(auditperiodid)
                            JOIN audit.mst_auditperiod AS yr ON yr.auditperiodid = ay.auditperiodid::int
                        ) AS auditinfo
                    "), DB::raw('TRUE'), '=', DB::raw('TRUE'))
                ->select(
                    DB::raw("
                     STRING_AGG(
                         CASE
                             WHEN hlc.fileuploadid != 0
                             THEN CONCAT(fu.filename, '-', fu.filepath, '-', fu.filesize, '-', fu.fileuploadid)
                             ELSE '-'
                         END,
                         ',' ORDER BY fu.fileuploadid
                     ) AS filedetails
                 "),
                    'para.actroleactioncode',
                    'hlc.processcode',
                    'inst.instename',
                    'insttname',
                    'auditinfo.audit_period',
                    'fp.parano',
                    'fp.followupid',
                    'fp.instid',
                    'para.paraid',
                    'hlc.apms_hlcid',
                    'hlc.followup_action_map',
                    'hlc.approved_para',
                    'hlc.rejected_para',
                    'hlc.approver_remarks',
                    'hlc.selected_paras',
                )
                ->groupBy(
                    'hlc.apms_hlcid',
                    'para.actroleactioncode',
                    'para.processcode',
                    'inst.instename',
                    'insttname',
                    'auditinfo.audit_period',
                    'fp.parano',
                    'fp.followupid',
                    'fp.instid',
                    'para.paraid',
                    'hlc.selected_paras',
                    'hlc.followup_action_map',

                )
                ->where('hlc.processcode', view::shared('frwd_to_approver'))
                ->when($apms_hlcid, function ($query) use ($apms_hlcid) {
                    $query->where('hlc.apms_hlcid', $apms_hlcid);
                })
                ->orderBy('hlc.updatedon', 'desc');



            // if ($roleactioncode == View::shared('RJD_roleactioncode')) {
            //     $query->whereIn('committee_level', view::shared('dlc_roleaction'));
            // } else {
            //     $query->where('committee_level', '=', $roleactioncode);
            // }

            // if ($roleactioncode == View::shared('RJD_roleactioncode')) {
            //     $query->whereIn('hlc.statusflag', ['F']);
            // } else {
            //     $query->whereIn('hlc.statusflag', ['F', 'Y']);
            // }


            // if (!empty($data['instid']) && !in_array('A', $data['instid'], true)) {
            //     $query->whereIn('inst.instid', $data['instid']);
            // }


            // $query->when($data['catcode'], function ($query) use ($data) {
            //     $query->where('hlc.catcode', $data['catcode']);
            // })   // ->where('fl.retiredflag', 'L')

            //     ->when($data['deptcode'], function ($query) use ($data) {
            //         $query->where('hlc.deptcode', $data['deptcode']);
            //     })
            //     ->when($data['distcode'], function ($query) use ($data) {
            //         $query->where('hlc.distcode', $data['distcode']);
            //     })

            //     ->when($data['regioncode'], function ($query) use ($data) {
            //         $query->where('hlc.regioncode', $data['regioncode']);
            //     })
            //     ->when($data['subcatid'], function ($query) use ($data) {
            //         $query->where('hlc.subcatid', $data['subcatid']);
            //     })

            // $query->where('inst.catcode',   $data['catcode'])
            //     ->where('inst.deptcode',  $data['deptcode'])
            //     ->where('inst.distcode',  $data['distcode'])
            //     ->where('inst.regioncode', $data['regioncode'])
            //     ->where('hlc.mom_date', $data['mom_date'])
            //     ->where('hlc.committee_level', $roleactioncode)
            //     ->orderBy('para.updatedon', 'desc');

            return $query->get();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            $customMessage = 'An error occured while fetching details';
            throw new \Exception($e->getMessage(), 500);

            //  throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            DB::rollBack();

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function fetch_dlcparas($params = [])
    {
        try {

            $query = DB::table(self::$apms_hlc_table . ' as hlc')
                ->join(self::$roleactionTable . ' as role', 'hlc.committee_level', '=', 'role.roleactioncode')
                ->join(self::$deptartment_table . ' as dept', 'hlc.deptcode', '=', 'dept.deptcode')
                ->join(self::$region_table . ' as reg', 'hlc.regioncode', '=', 'reg.regioncode')
                ->join(self::$dist_table . ' as dist', 'hlc.distcode', '=', 'dist.distcode')
                ->join(self::$mstauditeeinscategory_table . ' as cat', 'hlc.catcode', '=', 'cat.catcode')
                ->leftJoin(self::$subcategory_table . ' as subcat', 'hlc.subcatid', '=', 'subcat.auditeeins_subcategoryid')
                ->leftJoin('audit.mst_institution as inst', function ($join) {
                    $join->on(DB::raw("inst.instid::text"), '=', DB::raw("ANY (SELECT jsonb_array_elements_text(hlc.instid))"))
                        ->whereRaw("NOT ('A' = ANY (SELECT jsonb_array_elements_text(hlc.instid)))");
                })

                // ->where('hlc.statusflag', 'F')

                // dynamic processcode
                ->when(!empty($params['processcode']), function ($q) use ($params) {
                    $q->where('hlc.processcode', $params['processcode']);
                })

                // if specific HLC ID
                ->when(!empty($params['apms_hlcid']), function ($q) use ($params) {
                    $q->where('hlc.apms_hlcid', $params['apms_hlcid']);
                })

                ->when(!empty($params['region']), fn($qq) => $qq->where('hlc.regioncode', $params['region']))
                ->when(!empty($params['dept']), fn($qq) => $qq->where('hlc.deptcode', $params['dept']))
                ->when(!empty($params['district']), fn($qq) => $qq->where('hlc.distcode', $params['district']))
                ->when(!empty($params['catcode']), fn($qq) => $qq->where('hlc.catcode', $params['catcode']))
                ->when(!empty($params['subcatid']), fn($qq) => $qq->where('hlc.subcatid', $params['subcatid']))


                ->when(!empty($params['roleactioncode']), function ($q) use ($params) {
                    $q->where('hlc.committee_level', $params['roleactioncode']);
                })
                ->select(
                    'hlc.apms_hlcid',
                    'hlc.mom_date',
                    'hlc.deptcode',
                    'hlc.regioncode',
                    'hlc.distcode',
                    'hlc.instid',
                    'hlc.paraid',
                    'hlc.catcode',
                    'hlc.subcatid',
                    'hlc.actioncode',
                    'hlc.processcode',
                    'role.roleactionelname',
                    'reg.regionename',
                    'reg.regiontname',
                    'dist.distename',
                    'dist.disttname',
                    'dept.deptesname',
                    'dept.deptelname',
                    'cat.catename',
                    'cat.cattname',
                    'subcat.subcatename',
                    'subcat.subcattname',
                    'inst.instid',
                    'inst.instename',
                    'inst.insttname',
                    DB::raw("
        CASE
            WHEN 'A' = ANY (SELECT jsonb_array_elements_text(hlc.instid))
            THEN 'All Institutes'
            ELSE inst.instename
        END as instename
        "),

                    DB::raw('jsonb_array_length(hlc.selected_paras) AS para_count')
                )
                // $querySql = $query->toSql();
                // $bindings = $query->getBindings();

                // $finalQuery = vsprintf(
                //     str_replace('?', "'%s'", $querySql),
                //     array_map('addslashes', $bindings)
                // );
                // print_r($finalQuery);
                ->get();

            return $query;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }

    public static function fetch_returned_dlcparas(
        $region,
        $dept,
        $district = null,
        $roleactioncode = null,
        $catcode = null,
        $subcatid = null
    ) {
        try {

            $query = DB::table(self::$apms_hlc_table . ' as hlc')
                ->join(self::$roleactionTable . ' as role', 'hlc.committee_level', '=', 'role.roleactioncode')
                ->join(self::$deptartment_table . ' as dept', 'hlc.deptcode', '=', 'dept.deptcode')
                ->join(self::$region_table . ' as reg', 'hlc.regioncode', '=', 'reg.regioncode')
                ->join(self::$dist_table . ' as dist', 'hlc.distcode', '=', 'dist.distcode')
                ->join(self::$mstauditeeinscategory_table . ' as cat', 'hlc.catcode', '=', 'cat.catcode')
                ->leftJoin(self::$subcategory_table . ' as subcat', 'hlc.subcatid', '=', 'subcat.auditeeins_subcategoryid')

                // ->where('hlc.statusflag', 'F')
                ->where('hlc.processcode', view::shared('reject_dlcpara'))
                ->where('hlc.deptcode', $dept)
                // ->where('hlc.regioncode', $region)

                // optional filters
                ->when($region, function ($q) use ($region) {
                    $q->where('hlc.regioncode', $region);
                })
                ->when($district, function ($q) use ($district) {
                    $q->where('hlc.distcode', $district);
                })

                ->when($roleactioncode, function ($q) use ($roleactioncode) {
                    $q->where('hlc.committee_level', $roleactioncode);
                })

                ->when($catcode, function ($q) use ($catcode) {
                    $q->where('hlc.catcode', $catcode);
                })

                ->when($subcatid, function ($q) use ($subcatid) {
                    $q->where('hlc.subcatid', $subcatid);
                })

                ->select(
                    'hlc.apms_hlcid',
                    'hlc.mom_date',
                    'hlc.deptcode',
                    'hlc.regioncode',
                    'hlc.distcode',
                    'hlc.instid',
                    'hlc.paraid',
                    'hlc.catcode',
                    'hlc.subcatid',
                    'hlc.actioncode',
                    'hlc.processcode',
                    'role.roleactionelname',
                    'reg.regionename',
                    'reg.regiontname',
                    'dist.distename',
                    'dist.disttname',
                    'dept.deptesname',
                    'dept.deptelname',
                    'cat.catename',
                    'cat.cattname',
                    'subcat.subcatename',
                    'subcat.subcattname',
                    DB::raw('jsonb_array_length(hlc.paraid) AS para_count')
                )

                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {

            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }


    public static function upadate_followup($apms_hlcid, $followupid)
    {
        $updateCount = DB::table(self::$transfollowup_table)
            ->where('followupid', $followupid)
            ->update(['apms_hlcid' => $apms_hlcid]);

        if ($updateCount)
            return true;
    }


    public static function gethlcvalues($cond)
    {
        try {

            $query = DB::table(self::$apms_hlc_table)
                ->where('apms_hlcid', $cond)
                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while fetching para data';

            //throw new \Exception($customMessage, 500);
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getfollowupvalues($cond)
    {
        try {

            $query = DB::table(self::$transfollowup_table . ' as fp')
                ->leftJoin(self::$transpara_table . ' as para', 'para.followupid', '=', 'fp.followupid')
                ->select('fp.*', 'para.paraid', 'para.rejoindercycle', 'para.rejoinderstatus', 'para.rejectcount')
                ->where('fp.followupid', $cond)
                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while fetching para data';

            //throw new \Exception($customMessage, 500);
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getInstdet($instid)
    {
        $query = DB::table(self::$institution_table . ' as inst')
            ->join(self::$auditeeuserdetails_table . ' as ad', 'ad.instid', '=', 'inst.instid')
            ->where('inst.instid', $instid)
            ->where('inst.statusflag', 'Y')
            ->where('ad.statusflag', 'Y')
            ->select('inst.deptcode', 'inst.distcode', 'inst.regioncode', 'inst.catcode', 'inst.subcatid', 'ad.auditeeuserid');

        return $query->get();
    }

}
