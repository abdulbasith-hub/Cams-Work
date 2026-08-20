<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// class UserModel extends Model
// {
//     use HasFactory;
// }

class DashboardModel extends Model
{
    protected static $deptTable = BaseModel::DEPT_TABLE;

    protected static $distTable = BaseModel::DIST_Table;

    protected static $institutionTable = BaseModel::INSTITUTION_TABLE;

    protected static $sliphistorytable = BaseModel::SLIPHISTORYTRANSACTION_TABLE;

    protected static $AuditeeUserDetail_Table = BaseModel::AUDITEEUSERDETAIL_TABLE;

    protected static $auditplan_table = BaseModel::AUDITPLAN_TABLE;

    protected static $instauditschedule_table = BaseModel::INSTSCHEDULE_TABLE;

    public static function createdept_insertupdate(array $data, $currentDeptId, $table)
    {
        try {

            $query = DB::table($table);

            if ($currentDeptId) {
                $query->where('deptid', '!=', $currentDeptId);
            }

            $DeptesnameExists = (clone $query)->where('deptesname', $data['deptesname'])->exists();
            $DeptelnameExists = (clone $query)->where('deptelname', $data['deptelname'])->exists();
            $DepttsnameExists = (clone $query)->where('depttsname', $data['depttsname'])->exists();
            $DepttlnameExists = (clone $query)->where('depttlname', $data['depttlname'])->exists();
            $existingDept = (clone $query)
                ->where('deptesname', $data['deptesname'])
                ->where('deptelname', $data['deptelname'])
                ->where('depttsname', $data['depttsname'])
                ->where('depttlname', $data['depttlname'])
                ->first();

            // Duplicate validation
            if ($DeptesnameExists) {
                throw new \Exception('The Department English Short Name address is already exists.');
            }
            if ($DeptelnameExists) {
                throw new \Exception('The Department English Long Name is already associated exists.');
            }
            if ($DepttsnameExists) {
                throw new \Exception('The Department Tamil Short Name is already exists.');
            }
            if ($DepttlnameExists) {
                throw new \Exception('The Department Tamil Long Name is already exists.');
            }
            if ($existingDept) {
                throw new \Exception('The combination of email, mobile number, and IFHRMS number is already associated with a different user.');
            }

            // Create or update
            if ($currentDeptId) {
                DB::table($table)->where('deptid', $currentDeptId)->update($data);

                return DB::table($table)->where('deptid', $currentDeptId)->first();
            } else {
                $lastDeptCode = DB::table($table)->orderBy('deptid', 'desc')->value('deptcode');
                if ($lastDeptCode) {
                    $newDeptCode = str_pad((int) $lastDeptCode + 1, 2, '0', STR_PAD_LEFT);
                    $data['deptcode'] = $newDeptCode;
                } else {
                    $newDeptCode = '01';
                    $data['deptcode'] = $newDeptCode;
                }
                $newUserId = DB::table($table)->insertGetId($data, 'deptid');

                return DB::table($table)->where('deptid', $newUserId)->first();
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function fetchAlldata($table)
    {
        return DB::table($table)
            ->where('statusflag', 'Y')
            ->orderBy('updatedon', 'desc')
            ->get();
    }

    public static function fetchdept_data($deptid, $table)
    {
        return DB::table($table)
            ->where('deptid', $deptid)
            ->first();
    }

    /** ----------Fetch department details based on code  ------------*/
    public static function fetchDeptDetails($deptCode = null)
    {
        $query = DB::table(self::$deptTable)->where('statusflag', 'Y');

        if (! is_null($deptCode)) {
            $query->where('deptid', $deptCode);
        }

        return $query->orderBy('orderid', 'ASC')->get();
    }

    public static function fetchYearDetails($deptCode = null)
    {
        return DB::table('audit.mst_auditquarter')
            ->select('auditquarter', 'auditquartercode')
            ->where('statusflag', 'Y')
            ->orderBy('auditquarterid', 'ASC')
            ->get();
    }

    public static function fetchDistDetails($sessionroletype, $deptcode, $regioncode, $distcode)
    {
        $query = DB::table(self::$distTable.' as d')
            ->select('d.distcode', 'd.distename')
            ->join(self::$institutionTable.' as i', 'd.distcode', '=', 'i.distcode')
            ->distinct();

        if ($sessionroletype == view()->shared('Ho_roletypecode') || $sessionroletype == view()->shared('Re_roletypecode') || $sessionroletype == view()->shared('Dist_roletypecode')) {
            $query->where('i.deptcode', '=', $deptcode);

            if ($sessionroletype == view()->shared('Dist_roletypecode') || $sessionroletype == view()->shared('Re_roletypecode')) {
                $query->where('i.regioncode', '=', $regioncode);
            }
            if ($sessionroletype == view()->shared('Dist_roletypecode')) {
                $query->where('i.distcode', '=', $distcode);
            }
        }

        return $query->get();
    }

    public static function fetchInstitutionDetails($head)
    {
        DB::statement('SET search_path TO audit');

        $user = session('user');
        $userid = $user->userid;

        $query = DB::table('inst_auditschedule as aus')
            ->select(
                'inst.instename',
                'inst.insttname',
                'ap.instid',
                'instm.userid',
                'aus.auditscheduleid',
                'instm.auditteamhead'
            )
            ->join('auditplan as ap', 'aus.auditplanid', '=', 'ap.auditplanid')
            ->join('mst_institution as inst', 'ap.instid', '=', 'inst.instid')
            ->join('inst_schteammember as instm', 'instm.auditscheduleid', '=', 'aus.auditscheduleid')
            ->where('instm.userid', '=', $userid); // Get the session's user ID

        // Check if $head equals 'Y'
        if ($head == 'Y') {
            $query->where('instm.auditteamhead', '=', $head)
                ->where('instm.statusflag', '=', 'Y');
        }

        // Ensure distinct results
        $query->distinct();

        // Return the result of the query
        return $query->get();
    }

    public static function fetchCountDetails($deptCode, $regionCode, $distCode, $userChargeId, $userTypeCode, $roleTypeCode, $auditScheduleId)
    {
        // Set the schema to `audit`
        DB::statement('SET search_path TO audit');

        // Execute the PostgreSQL function and fetch the JSON response
        $result = DB::select('SELECT audit.fn_getDashboardCount(?, ?, ?, ?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $userChargeId,
            $userTypeCode,
            $roleTypeCode,
            $auditScheduleId,
        ]);

        // Decode the JSON response into a PHP array or object
        return json_decode($result[0]->data, true);
    }

    // public static function fetchDashboardDescription($deptCode, $regionCode, $distCode, $userId, $userTypeCode, $roleTypeCode, $auditScheduleId, $description)
    // {
    //     DB::statement('SET search_path TO audit');

    //     // Enable query logging
    //     DB::enableQueryLog();

    //     // Execute the function
    //     $result = DB::select('SELECT audit.fn_getdashboarddescription(?, ?, ?, ?, ?, ?, ?, ?) AS data', [
    //         $deptCode,
    //         $regionCode,
    //         $distCode,
    //         $userId,
    //         $userTypeCode,
    //         $roleTypeCode,
    //         $auditScheduleId,
    //         $description
    //     ]);

    //     // Get the last executed query
    //     $queryLog = DB::getQueryLog();

    //     // Print query log
    //     dd($queryLog);

    //     return json_decode($result[0]->data, true);
    // }

    public static function fetchDashboardDescription($deptCode, $regionCode, $distCode, $userId, $userTypeCode, $roleTypeCode, $auditScheduleId, $description)
    {
        DB::statement('SET search_path TO audit');

        // Manually construct the query string
        $sql = sprintf(
            "SELECT audit.fn_getdashboarddescription('%s', '%s', '%s', %d, '%s', '%s', %d, '%s') AS data",
            $deptCode,
            $regionCode,
            $distCode,
            $userId,
            $userTypeCode,
            $roleTypeCode,
            $auditScheduleId,
            $description
        );

        // Print the SQL query
        // dd($sql);

        // Execute the function
        $result = DB::select($sql);

        return json_decode($result[0]->data, true);
    }

    // public static function fetchSentDetails($userTypeCode, $userId)
    // {
    //     $result = DB::table('audit.sliphistorytransactions AS sht')
    //         ->select(
    //             'sht.auditslipid',
    //             'mo.objectionename',
    //             'so.subobjectionename',
    //             'se.severityelname',
    //             'a.amtinvolved',
    //             'a.tempslipnumber',
    //             'a.mainslipnumber',
    //             'a.slipdetails',
    //             //DB::raw('a.auditorremarks::json->>\'content\' AS auditorremarks'),
    //             //'a.auditeeremarks',
    //             //'a.rejoinder_auditeerremarks',
    //             'a.liability',
    //             'li.liabilityname',
    //             'a.processcode',
    //             'a.rejoinderstatus',
    //             // 'a.memberrejoinderremarks',
    //             // 'a.rejoinder_auditorremarks',
    //             // 'a.finalremarks',
    //             'li.liabilitydesignation',
    //             'li.liabilitygpfno',
    //             'p.processelname',
    //             'ins.auditplanid',
    //             'pla.instename',

    //             DB::raw("CASE
    //                         WHEN sht.forwardedtousertypecode = 'A' THEN dud.username
    //                         WHEN sht.forwardedtousertypecode = 'I' THEN aud.username
    //                         ELSE 'Unknown'
    //                     END AS forwardedtouser"),
    //                     DB::raw("CASE
    //                     WHEN sht.forwardedtousertypecode = 'A' THEN 'Auditor'
    //                     WHEN sht.forwardedtousertypecode = 'I' THEN 'Auditee'
    //                     ELSE 'Unknown'
    //                 END AS forwardedtousertype"),
    //             'sht.forwardedon'
    //         )
    //         ->join('audit.trans_auditslip AS a', 'sht.auditslipid', '=', 'a.auditslipid')
    //         ->join('audit.mst_mainobjection AS mo', 'a.mainobjectionid', '=', 'mo.mainobjectionid')
    //         ->join('audit.mst_severity AS se', 'se.severitycode', '=', 'a.severitycode')

    //         ->join('audit.liability AS li', 'li.auditslipid', '=', 'a.auditslipid')
    //         ->leftJoin('audit.mst_subobjection AS so', 'a.subobjectionid', '=', 'so.subobjectionid')
    //         ->leftJoin('audit.deptuserdetails AS dud', function ($join) {
    //             $join->on('sht.forwardedto', '=', 'dud.deptuserid')
    //                  ->where('sht.forwardedtousertypecode', '=', 'A');
    //         })
    //         ->leftJoin('audit.audtieeuserdetails AS aud', function ($join) {
    //             $join->on('sht.forwardedto', '=', 'aud.auditeeuserid')
    //                  ->where('sht.forwardedtousertypecode', '=', 'I');
    //         })
    //         ->join('audit.mst_process AS p', 'a.processcode', '=', 'p.processcode')

    //         ->join('audit.auditplan AS ins', 'a.auditplanid', '=', 'ins.auditplanid')  // Corrected join on auditplanid
    //         ->join('audit.mst_institution AS pla', 'ins.instid', '=', 'pla.instid')  // Link based on instid from inst_auditschedule

    //         ->where('sht.forwardedby', '=', $userId)
    //         ->where('sht.forwardedbyusertypecode', '=', $userTypeCode)
    //         ->orderBy( 'sht.forwardedon', 'DESC')
    //         ->get();

    //     return $result;
    // }

    public static function fetchSentDetails($userTypeCode, $userId)
    {
        $result = DB::table('audit.sliphistorytransactions AS sht')
            ->select(
                'sht.auditslipid',
                'mo.objectionename',
                'so.subobjectionename',
                'se.severityelname',
                'a.amtinvolved',
                'a.tempslipnumber',
                'a.mainslipnumber',
                'a.slipdetails',
                // DB::raw('a.auditorremarks::json->>\'content\' AS auditorremarks'),
                // 'a.auditeeremarks',
                // 'a.rejoinder_auditeerremarks',
                'a.liability',
                'li.liabilityname',
                'a.processcode',
                'a.rejoinderstatus',
                // 'a.memberrejoinderremarks',
                // 'a.rejoinder_auditorremarks',
                // 'a.finalremarks',
                'li.liabilitydesignation',
                'li.liabilitygpfno',
                'p.processelname',
                'ins.auditplanid',
                'pla.instename',

                DB::raw("CASE
                        WHEN sht.forwardedtousertypecode = 'A' THEN dud.username
                        WHEN sht.forwardedtousertypecode = 'I' THEN aud.username
                        ELSE p.processelname
                    END AS forwardedtouser"),
                DB::raw("CASE
                    WHEN sht.forwardedtousertypecode = 'A' THEN 'Auditor'
                    WHEN sht.forwardedtousertypecode = 'I' THEN 'Auditee'
                    ELSE 'Unknown'
                END AS forwardedtousertype"),
                'sht.forwardedon'
            )
            ->join('audit.trans_auditslip AS a', 'sht.auditslipid', '=', 'a.auditslipid')
            ->join('audit.mst_mainobjection AS mo', 'a.mainobjectionid', '=', 'mo.mainobjectionid')
            ->join('audit.mst_severity AS se', 'se.severitycode', '=', 'a.severitycode')

            ->leftJoin('audit.liability AS li', 'li.auditslipid', '=', 'a.auditslipid')
            ->leftJoin('audit.mst_subobjection AS so', 'a.subobjectionid', '=', 'so.subobjectionid')
            ->leftJoin('audit.deptuserdetails AS dud', function ($join) {
                $join->on('sht.forwardedto', '=', 'dud.deptuserid')
                    ->where('sht.forwardedtousertypecode', '=', 'A');
            })
            ->leftJoin('audit.audtieeuserdetails AS aud', function ($join) {
                $join->on('sht.forwardedto', '=', 'aud.auditeeuserid')
                    ->where('sht.forwardedtousertypecode', '=', 'I');
            })
            ->join('audit.mst_process AS p', 'a.processcode', '=', 'p.processcode')

            ->join('audit.auditplan AS ins', 'a.auditplanid', '=', 'ins.auditplanid')  // Corrected join on auditplanid
            ->join('audit.mst_institution AS pla', 'ins.instid', '=', 'pla.instid')  // Link based on instid from inst_auditschedule

            ->where('sht.forwardedby', '=', $userId)
            ->where('sht.forwardedbyusertypecode', '=', $userTypeCode)

            ->orderBy('sht.forwardedon', 'DESC')
            ->get();
        // ;
        // $querySql = $result->toSql();
        //         $bindings = $result->getBindings();

        //         $finalQuery = vsprintf(
        //             str_replace('?', "'%s'", $querySql),
        //             array_map('addslashes', $bindings)
        //         );

        //         print_r($finalQuery);

        return $result;
    }

    public static function getinitimationcount($userid, $userdeptcode)
    {
        return DB::table(self::$AuditeeUserDetail_Table.' as auditee')
            ->join(self::$institutionTable.' as inst', 'auditee.instid', '=', 'inst.instid')
            ->join(self::$auditplan_table.' as plan', 'plan.instid', '=', 'auditee.instid')
            ->join(self::$instauditschedule_table.' as schd', 'schd.auditplanid', '=', 'plan.auditplanid')
            ->where('auditee.auditeeuserid', $userid)
            ->where('auditee.statusflag', 'Y')
            ->where('schd.auditeeresponse', null)
            ->where('schd.statusflag', 'F')
            ->selectRaw('COUNT(auditee.auditeeuserid) as total_count')
            ->get();
    }

    // public static function GetcountDetails($deptCode, $regionCode, $distCode, $quarter)
    // {

    //     DB::statement(query: 'SET search_path TO audit');

    //     // Execute the PostgreSQL function and fetch the JSON response
    //     $result = DB::select('SELECT audit.fn_getcountofdeptwise(?, ?, ?,?) AS data', [
    //         $deptCode,
    //         $regionCode,
    //         $distCode,
    //         $quarter,
    //     ]);

    //     // Decode the JSON response into a PHP array or object
    //     return json_decode($result[0]->data, true);
    // }

     public static function GetcountDetails($deptCode, $regionCode, $distCode, $quarter ,$financialyearcode)
    {

        DB::statement(query: 'SET search_path TO audit');

        // Execute the PostgreSQL function and fetch the JSON response
        $result = DB::select('SELECT audit.fn_getcountofdeptwise(?, ?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $quarter,
            $financialyearcode
        ]);

        // Decode the JSON response into a PHP array or object
        return json_decode($result[0]->data, true);
    }
 public static function GetAuditeeCountDetails($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $catcode, $subcatcode , $financialyearcode)
    {
        DB::statement('SET search_path TO audit');

        $deptCode = ($deptCode === 'null' || $deptCode === '' || ! isset($deptCode)) ? null : $deptCode;
        $regionCode = ($regionCode === 'null' || $regionCode === '' || ! isset($regionCode)) ? null : $regionCode;
        $distCode = ($distCode === 'null' || $distCode === '' || ! isset($distCode)) ? null : $distCode;
        $quarter = ($quarter === 'null' || $quarter === '' || ! isset($quarter)) ? null : $quarter;
        $auditeeDeptCode = ($auditeeDeptCode === 'null' || $auditeeDeptCode === '' || ! isset($auditeeDeptCode)) ? null : $auditeeDeptCode;
        $catcode = ($catcode === 'null' || $catcode === '' || ! isset($catcode)) ? null : $catcode;
        $subcatcode = ($subcatcode === 'null' || $subcatcode === '' || ! isset($subcatcode)) ? null : $subcatcode;
        $financialyearcode = ($financialyearcode === 'null' || $financialyearcode === '' || ! isset($financialyearcode)) ? null : $financialyearcode;

        try {
            $result = DB::select('SELECT audit.fn_getauditeedeptcat(?, ?, ?, ?, ?, ?, ?, ?) AS data', [
                $deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $catcode, $subcatcode, $financialyearcode,
            ]);

            return json_decode($result[0]->data, true);
        } catch (\Exception $e) {
            Log::error('Database function error: '.$e->getMessage());

            return [
                'deptcount' => 0,
                'regioncount' => 0,
                'distcount' => 0,
                'auditeedeptcount' => 0,
                'alloc_inscount' => 0,
                'commencedinscount' => 0,
                'totalslipcount' => 0,
                'pendingslipcount' => 0,
                'convertedslipcount' => 0,
                'droppedslipcount' => 0,
            ];
        }
    }
    // public static function GetReportCounts($deptCode, $regionCode, $distCode, $quarter)
    // {

    //     DB::statement(query: 'SET search_path TO audit');

    //     $result = DB::select(
    //         'SELECT * FROM audit.get_audit_report_counts(?, ?, ?, ?)',
    //         [$quarter, $deptCode, $regionCode, $distCode]
    //     );

    //     return $result;
    // }

    public static function GetReportCounts($deptCode, $regionCode, $distCode, $quarter, $financialyearcode)
    {

        DB::statement(query: 'SET search_path TO audit');

        $result = DB::select(
            'SELECT * FROM audit.get_audit_report_counts(?, ?, ?, ?, ?)',
            [$quarter, $deptCode, $regionCode, $distCode, $financialyearcode]
        );

        return $result;
    }


    public static function GetTemplateAuditCounts($deptCode, $regionCode, $distCode, $quarter, $financialyearcode)
    {

        DB::statement(query: 'SET search_path TO audit');

        $result = DB::select('SELECT audit.getcountofdeptwise_templateaudit(?, ?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $quarter,
            $financialyearcode
        ]);

        // dd($result);

        return json_decode($result[0]->data, true);
    }


    // public static function GetTemplateAuditCounts($deptCode, $regionCode, $distCode, $quarter)
    // {

    //     DB::statement(query: 'SET search_path TO audit');

    //     $result = DB::select('SELECT audit.getcountofdeptwise_templateaudit(?, ?, ?,?) AS data', [
    //         $deptCode,
    //         $regionCode,
    //         $distCode,
    //         $quarter,
    //     ]);

    //     // dd($result);

    //     return json_decode($result[0]->data, true);
    // }

   public static function GetAuditeeTemplateAuditCounts($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $catcode, $subcatcode, $financialyearcode)
    {
        // dd($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $catcode, $subcatcode);

        $deptCode = ($deptCode === 'null' || $deptCode === '' || ! isset($deptCode)) ? null : $deptCode;
        $regionCode = ($regionCode === 'null' || $regionCode === '' || ! isset($regionCode)) ? null : $regionCode;
        $distCode = ($distCode === 'null' || $distCode === '' || ! isset($distCode)) ? null : $distCode;
        $quarter = ($quarter === 'null' || $quarter === '' || ! isset($quarter)) ? null : $quarter;
        $auditeeDeptCode = ($auditeeDeptCode === 'null' || $auditeeDeptCode === '' || ! isset($auditeeDeptCode)) ? null : $auditeeDeptCode;
        $catcode = ($catcode === 'null' || $catcode === '' || ! isset($catcode)) ? null : $catcode;
        $subcatcode = ($subcatcode === 'null' || $subcatcode === '' || ! isset($subcatcode)) ? null : $subcatcode;
        $financialyearcode = ($financialyearcode === 'null' || $financialyearcode === '' || ! isset($financialyearcode)) ? null : $financialyearcode;

        DB::statement(query: 'SET search_path TO audit');

        $result = DB::select('SELECT audit.getcountofdeptwise_auditeetemplateaudit(?, ?, ?, ?, ?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $quarter,
            $auditeeDeptCode,
            $catcode,
            $subcatcode,
            $financialyearcode
        ]);

        // dd($result);

        return json_decode($result[0]->data, true);
    }


    // public static function GetInspectionCounts($deptCode, $regionCode, $distCode, $quarter)
    // {

    //     DB::statement(query: 'SET search_path TO audit');
    //     $result = DB::select('SELECT audit.fn_getinspectioncountofdeptwise(?, ?, ?,?) AS data', [
    //         $deptCode,
    //         $regionCode,
    //         $distCode,
    //         $quarter,
    //     ]);
    //     // dd($result);

    //     return json_decode($result[0]->data, true);
    // }

      public static function GetInspectionCounts($deptCode, $regionCode, $distCode, $quarter, $financialyearcode)
    {

        DB::statement(query: 'SET search_path TO audit');
        $result = DB::select('SELECT audit.fn_getinspectioncountofdeptwise(?, ?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $quarter,
            $financialyearcode
        ]);
        // dd($result);

        return json_decode($result[0]->data, true);
    }


 public static function GetAuditeeParaReportCounts($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $catcode, $subcatcode, $financialyearcode)
    {

    // dd($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $catcode, $subcatcode);
        $deptCode = ($deptCode === 'null' || $deptCode === '' || ! isset($deptCode)) ? null : $deptCode;
        $regionCode = ($regionCode === 'null' || $regionCode === '' || ! isset($regionCode)) ? null : $regionCode;
        $distCode = ($distCode === 'null' || $distCode === '' || ! isset($distCode)) ? null : $distCode;
        $quarter = ($quarter === 'null' || $quarter === '' || ! isset($quarter)) ? null : $quarter;
        $auditeeDeptCode = ($auditeeDeptCode === 'null' || $auditeeDeptCode === '' || ! isset($auditeeDeptCode)) ? null : $auditeeDeptCode;
        $catcode = ($catcode === 'null' || $catcode === '' || ! isset($catcode)) ? null : $catcode;
        $subcatcode = ($subcatcode === 'null' || $subcatcode === '' || ! isset($subcatcode)) ? null : $subcatcode;
        $financialyearcode = ($financialyearcode === 'null' || $financialyearcode === '' || ! isset($financialyearcode)) ? null : $financialyearcode;

        DB::statement(query: 'SET search_path TO audit');

        $result = DB::select('SELECT audit.fn_getauditeeparareportcountofdeptwise(?, ?, ?, ?, ?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $quarter,
            $auditeeDeptCode,
            $catcode,
            $subcatcode,
            $financialyearcode
        ]);
        // dd($result);


        return json_decode($result[0]->data, true);
    }



  public static function GetAuditeeInspectionCounts($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $catcode, $subcatcode, $financialyearcode)
    {

        $deptCode = ($deptCode === 'null' || $deptCode === '' || ! isset($deptCode)) ? null : $deptCode;
        $regionCode = ($regionCode === 'null' || $regionCode === '' || ! isset($regionCode)) ? null : $regionCode;
        $distCode = ($distCode === 'null' || $distCode === '' || ! isset($distCode)) ? null : $distCode;
        $quarter = ($quarter === 'null' || $quarter === '' || ! isset($quarter)) ? null : $quarter;
        $auditeeDeptCode = ($auditeeDeptCode === 'null' || $auditeeDeptCode === '' || ! isset($auditeeDeptCode)) ? null : $auditeeDeptCode;
        $catcode = ($catcode === 'null' || $catcode === '' || ! isset($catcode)) ? null : $catcode;
        $subcatcode = ($subcatcode === 'null' || $subcatcode === '' || ! isset($subcatcode)) ? null : $subcatcode;
        $financialyearcode = ($financialyearcode === 'null' || $financialyearcode === '' || ! isset($financialyearcode)) ? null : $financialyearcode;

        DB::statement(query: 'SET search_path TO audit');

        $result = DB::select('SELECT audit.getcountofdeptwise_auditeeinspection(?, ?, ?, ?, ?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $quarter,
            $auditeeDeptCode,
            $catcode,
            $subcatcode,
            $financialyearcode
        ]);


        return json_decode($result[0]->data, true);
    }

     public static function GetAuditeeReportCounts($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $catcode, $subcatcode, $financialyearcode)
    {

        DB::statement(query: 'SET search_path TO audit');

        $result = DB::select(
            'SELECT * FROM audit.get_auditee_report_counts(?, ?, ?, ?, ?, ?, ?, ?)',
            [$quarter, $deptCode, $regionCode, $distCode, $auditeeDeptCode, $catcode, $subcatcode, $financialyearcode]
        );

        return $result;
    }


   
    // public static function getAuditReportRegionwise($quarter, $deptCode, $regionCode ,$distcode)
    // {
    //     return DB::select(
    //         'SELECT * FROM audit.get_audit_report_counts_regionwise(?, ?, ?, ?)',
    //         [$quarter, $deptCode, $regionCode ,$distcode  ]
    //     );
    // }

     public static function getAuditReportRegionwise($quarter, $deptCode, $regionCode ,$distcode, $financialyearcode)
    {
        return DB::select(
            'SELECT * FROM audit.get_audit_report_counts_regionwise(?, ?, ?, ?, ?)',
            [$quarter, $deptCode, $regionCode ,$distcode, $financialyearcode ]
        );
    }

    public static function getAuditeeReportRegionwise($quarter, $deptCode, $regionCode, $distCode, $auditeeDeptCode, $catcode, $subcatcode , $financialyearcode)
    {
        return DB::select(
            'SELECT * FROM audit.get_auditee_report_counts_regionwise(?, ?, ?, ?, ?, ?, ?, ?)',
            [$quarter, $deptCode, $regionCode, $distCode, $auditeeDeptCode, $catcode, $subcatcode, $financialyearcode]
        );
    }



    /**
     * Get audit report counts districtwise
     */
    // public static function getAuditReportDistrictwise($quarter, $deptCode, $regionCode, $distCode, $viewType = null)
    // {
    //     return DB::select(
    //         'SELECT * FROM audit.get_audit_report_counts_districtwise(?, ?, ?, ?, ?)',
    //         [$quarter, $deptCode, $regionCode, $distCode, $viewType]
    //     );
    // }

    public static function getAuditReportDistrictwise($quarter, $deptCode, $regionCode, $distCode, $viewType = null, $financialyearcode = null)
    {
        return DB::select(
            'SELECT * FROM audit.get_audit_report_counts_districtwise(?, ?, ?, ?, ?, ?)',
            [$quarter, $deptCode, $regionCode, $distCode, $viewType, $financialyearcode]
        );
    }


    public static function getAuditeeReportDistrictwise($quarter, $deptCode, $regionCode, $distCode, $viewType, $auditeeDeptCode, $catcode, $subcatcode, $financialyearcode)
    {
        return DB::select(
            'SELECT * FROM audit.get_auditee_report_counts_districtwise(?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$quarter, $deptCode, $regionCode, $distCode, $viewType, $auditeeDeptCode, $catcode, $subcatcode, $financialyearcode]
        );
    }


    /**
     * Get audit report institution details
     */
    // public static function getAuditReportInstitutionDetails($quarter, $deptCode, $regionCode, $distCode)
    // {
    //     return DB::select(
    //         'SELECT * FROM audit.get_audit_report_institution_details(?, ?, ?, ?)',
    //         [$quarter, $deptCode, $regionCode, $distCode]
    //     );
    // }
     public static function getAuditReportInstitutionDetails($quarter, $deptCode, $regionCode, $distCode, $financialyearcode)
    {
        return DB::select(
            'SELECT * FROM audit.get_audit_report_institution_details(?, ?, ?, ?, ?)',
            [$quarter, $deptCode, $regionCode, $distCode, $financialyearcode]
        );
    }


    public static function getAuditeeReportInstitutionDetails($quarter, $deptCode, $regionCode, $distCode, $auditeeDeptCode, $catcode, $subcatcode, $financialyearcode)
    {
        return DB::select(
            'SELECT * FROM audit.get_auditee_report_institution_details(?, ?, ?, ?, ?, ?, ?, ?)',
            [$quarter, $deptCode, $regionCode, $distCode, $auditeeDeptCode, $catcode, $subcatcode, $financialyearcode]
        );
    }


   public static function InstitutedetailsGet($deptCode, $regionCode, $distCode, $quarter, $financialyearcode)
    {

        DB::statement('SET search_path TO audit');

        $deptCode = $deptCode ?: null;
        $regionCode = $regionCode ?: null;
        $distCode = $distCode ?: null;

        // Execute the PostgreSQL function and fetch the JSON response
        $result = DB::select('SELECT audit.get_allocated_institute_details(?, ?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $quarter,
            $financialyearcode
        ]);

        $json = $result[0]->data;  // This will be JSON string

        $data = json_decode($json, true);

        return $data;
    }

  public static function TemplateAuditInstitutedetailsGet($deptCode, $regionCode, $distCode, $quarter, $financialyearcode)
    {

        DB::statement('SET search_path TO audit');

        $deptCode = $deptCode ?: null;
        $regionCode = $regionCode ?: null;
        $distCode = $distCode ?: null;

        // Execute the PostgreSQL function and fetch the JSON response
        $result = DB::select('SELECT audit.get_allocated_template_audit_institute_details(?, ?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $quarter,
            $financialyearcode
        ]);

        $json = $result[0]->data;  // This will be JSON string

        $data = json_decode($json, true);

        return $data;
    }

   public static function InspectionAuditInstitutedetailsGet($deptCode, $regionCode, $distCode, $quarter, $financialyearcode)
    {

        DB::statement('SET search_path TO audit');

        $deptCode = $deptCode ?: null;
        $regionCode = $regionCode ?: null;
        $distCode = $distCode ?: null;

        // Execute the PostgreSQL function and fetch the JSON response
        $result = DB::select('SELECT audit.get_inspection_audit_institute_details(?, ?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $quarter,
            $financialyearcode
        ]);

        $json = $result[0]->data;  // This will be JSON string

        $data = json_decode($json, true);

        return $data;
    }

   public static function AuditeeInstitutedetailsGet($deptCode, $regionCode, $distCode, $quarter, $auditeedeptCode, $catcode, $subcatcode, $financialyearcode)
    {
        DB::statement('SET search_path TO audit');

        $deptCode = $deptCode ?: null;
        $regionCode = $regionCode ?: null;
        $distCode = $distCode ?: null;
        $auditeedeptCode = $auditeedeptCode ?: null;
        $catcode = $catcode ?: null;
        $subcatcode = $subcatcode ?: null;
        $financialyearcode = $financialyearcode ?: null;

        // Execute the PostgreSQL function and fetch the JSON response
        $result = DB::select('SELECT audit.get_auditee_allocated_institute_details(?, ?, ?, ?, ?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $quarter,
            $auditeedeptCode,
            $catcode,
            $subcatcode,
            $financialyearcode

        ]);

        $json = $result[0]->data;  // This will be JSON string
        $data = json_decode($json, true);

        return $data;
    }



  public static function AuditeeTemplateInstitutedetailsGet($deptCode, $regionCode, $distCode, $quarter, $auditeedeptCode, $catcode, $subcatcode, $financialyearcode)
    {
        DB::statement('SET search_path TO audit');

        $deptCode = $deptCode ?: null;
        $regionCode = $regionCode ?: null;
        $distCode = $distCode ?: null;
        $auditeedeptCode = $auditeedeptCode ?: null;
        $catcode = $catcode ?: null;
        $subcatcode = $subcatcode ?: null;
        $financialyearcode = $financialyearcode ?: null;

        // Execute the PostgreSQL function and fetch the JSON response
        $result = DB::select('SELECT audit.get_auditee_template_allocated_institute_details(?, ?, ?, ?, ?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $quarter,
            $auditeedeptCode,
            $catcode,
            $subcatcode,
            $financialyearcode
        ]);

        $json = $result[0]->data;  // This will be JSON string
        $data = json_decode($json, true);

        return $data;
    }


 public static function AuditeeParaInstitutionwiseData($deptCode, $regionCode, $distCode, $quarter, $auditeedeptCode, $catcode, $subcatcode, $financialyearcode)
    {
        DB::statement('SET search_path TO audit');

        $deptCode = $deptCode ?: null;
        $regionCode = $regionCode ?: null;
        $distCode = $distCode ?: null;
        $auditeedeptCode = $auditeedeptCode ?: null;
        $catcode = $catcode ?: null;
        $subcatcode = $subcatcode ?: null;
        $financialyearcode = $financialyearcode ?: null;

        // Execute the PostgreSQL function and fetch the JSON response
        $result = DB::select('SELECT audit.auditeeparadetailsinstitutionwisedata(?, ?, ?, ?, ?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $quarter,
            $auditeedeptCode,
            $catcode,
            $subcatcode,
            $financialyearcode
        ]);

        $json = $result[0]->data;  // This will be JSON string
        $data = json_decode($json, true);

        return $data;
    }


   public static function AuditeeInspectionInstitutedetailsGet($deptCode, $regionCode, $distCode, $quarter, $auditeedeptCode, $catcode, $subcatcode , $financialyearcode)
    {
        DB::statement('SET search_path TO audit');

        $deptCode = $deptCode ?: null;
        $regionCode = $regionCode ?: null;
        $distCode = $distCode ?: null;
        $auditeedeptCode = $auditeedeptCode ?: null;
        $catcode = $catcode ?: null;
        $subcatcode = $subcatcode ?: null;
        $financialyearcode = $financialyearcode ?: null;

        // Execute the PostgreSQL function and fetch the JSON response
        $result = DB::select('SELECT audit.get_inspection_audit_auditee_institute_details(?, ?, ?, ?, ?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $quarter,
            $auditeedeptCode,
            $catcode,
            $subcatcode,
            $financialyearcode
        ]);

        $json = $result[0]->data;  // This will be JSON string
        $data = json_decode($json, true);

        return $data;
    }


    // public static function InstitutedetailsGet($deptCode,$regionCode,$distCode,$quarter)
    // {
    //     return  DB::table('audit.mst_institution as mi')
    //             ->join('audit.auditplan as ap', 'ap.instid', '=', 'mi.instid')
    //             ->join('audit.mst_district as dist','mi.distcode', '=', 'dist.distcode')
    //             ->join('audit.mst_region as reg','reg.regioncode', '=', 'mi.regioncode')
    //             ->leftJoin(DB::raw('(
    //                 SELECT DISTINCT ON (auditplanid) *
    //                 FROM audit.inst_auditschedule
    //             ) as sch'), 'sch.auditplanid', '=', 'ap.auditplanid')
    //             ->when($regionCode, function ($query) use ($regionCode) {
    //                 return $query->where('mi.regioncode', $regionCode);
    //             })
    //             ->when($deptCode, function ($query) use ($deptCode) {
    //                 return $query->where('mi.deptcode', $deptCode);
    //             })
    //             ->when($distCode, function ($query) use ($distCode) {
    //                 return $query->where('mi.distcode', $distCode);
    //             })
    //             ->select(
    //                 'mi.instename',
    //                 'mi.mandays',
    //                 'ap.auditplanid',
    //                 'sch.auditscheduleid',
    //                 'dist.distename','reg.regionename',
    //                 DB::raw("CASE WHEN sch.statusflag = 'F' THEN 'Scheduled' ELSE 'Not Scheduled' END as schedule_status"),
    //                 DB::raw("CASE WHEN sch.auditeeresponse = 'A' THEN 'Replied' ELSE 'Waiting for Response' END as response_status"),
    //                 DB::raw("CASE WHEN sch.workallocationflag = 'Y' THEN 'work allocated' ELSE 'not allocated' END as workallocation_status"),
    //                 DB::raw("CASE
    //                                 WHEN sch.entrymeetdate IS NOT NULL
    //                                 THEN TO_CHAR(sch.entrymeetdate, 'DD/MM/YYYY')
    //                                 ELSE 'No'
    //                             END as entrymeet_status"),
    //                 DB::raw("CASE
    //                             WHEN sch.exitmeetdate IS NOT NULL
    //                             THEN TO_CHAR(sch.exitmeetdate, 'DD/MM/YYYY')
    //                             ELSE 'No'
    //                         END as exitmeet_status"),
    //                 DB::raw("(
    //                             SELECT head.username || ' - ' || desig.desigelname
    //                             FROM audit.auditplanteammember AS head_tm
    //                             JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
    //                             JOIN audit.mst_designation AS desig ON desig.desigcode = head.desigcode
    //                             WHERE head_tm.auditplanteamid = ap.auditteamid AND head_tm.teamhead = 'Y' AND head_tm.statusflag ='Y'
    //                             LIMIT 1
    //                         ) AS team_head_en"),
    //                 DB::raw("(
    //                             SELECT COALESCE(STRING_AGG(member.username || ' - ' || desig2.desigelname, ', '), '')
    //                             FROM audit.auditplanteammember AS member_tm
    //                             JOIN audit.deptuserdetails AS member ON member.deptuserid = member_tm.userid
    //                             JOIN audit.mst_designation AS desig2 ON desig2.desigcode = member.desigcode
    //                             WHERE member_tm.auditplanteamid = ap.auditteamid AND member_tm.teamhead != 'Y' AND member_tm.statusflag ='Y'
    //                         ) AS team_members_en"),
    //                 DB::raw('(SELECT COUNT(*) FROM audit.auditplanteammember as sub_atm WHERE sub_atm.auditplanteamid = ap.auditteamid) as total_team_count'),
    //                 DB::raw("CASE
    //                  WHEN sch.fromdate IS NOT NULL
    //                  THEN TO_CHAR(sch.fromdate, 'DD/MM/YYYY')
    //                  ELSE '-'
    //                  END as fromdate"),
    //  DB::raw("CASE
    //                 WHEN sch.todate IS NOT NULL
    //                 THEN TO_CHAR(sch.todate, 'DD/MM/YYYY')
    //                 ELSE '-'
    //                 END as todate")

    //                 )
    //                 ->orderByRaw("CASE sch.statusflag WHEN 'F' THEN 0 ELSE 1 END")
    //                 ->orderByRaw("CASE sch.auditeeresponse WHEN 'A' THEN 0 ELSE 1 END")
    //                 ->orderByRaw("CASE sch.workallocationflag WHEN 'Y' THEN 0 ELSE 1 END")
    //                 ->orderByRaw("CASE WHEN sch.entrymeetdate IS NOT NULL THEN 0 ELSE 1 END")
    //                 ->orderByRaw("CASE WHEN sch.exitmeetdate IS NOT NULL THEN 0 ELSE 1 END")
    //                 ->orderBy('reg.regionename', 'asc')
    //                 ->orderBy('dist.distename', 'asc')
    //                 ->orderBy('sch.entrymeetdate', 'asc')
    //                 ->orderBy('sch.exitmeetdate', 'asc')
    //                 ->orderBy('mi.instename', 'asc')
    //                 ->get();

    // }

  public static function CommencedInstitutedetailsGet($deptCode, $regionCode, $distCode, $whichslip, $quarter, $financialyearcode)
    {
        $slipCounts = DB::table('audit.trans_auditslip as tas')
            ->select(
                'tas.auditplanid',
                DB::raw('COUNT(DISTINCT CASE WHEN tas.processcode IS NOT NULL THEN tas.auditslipid END) as totalslips'),
                DB::raw("COUNT(DISTINCT CASE WHEN tas.processcode = 'A' THEN tas.auditslipid END) as droppedslips"),
                DB::raw("COUNT(DISTINCT CASE WHEN tas.processcode = 'X' THEN tas.auditslipid END) as convertedslips"),
                DB::raw("COUNT(DISTINCT CASE WHEN tas.processcode NOT IN ('A', 'X') THEN tas.auditslipid END) as pendingslips")
            )
            ->groupBy('tas.auditplanid');

        $latestSchedule = DB::table('audit.inst_auditschedule as ias')
            ->select('ias.auditplanid', DB::raw('MAX(ias.auditscheduleid) as latest_schedule_id'))
            ->groupBy('ias.auditplanid');

        $teamHead = DB::table('audit.auditplanteammember as head_tm')
            ->join('audit.deptuserdetails as head', 'head.deptuserid', '=', 'head_tm.userid')
            ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'head.desigcode')
            ->select(
                'head_tm.auditplanteamid',
                DB::raw("head.username || ' - ' || desig.desigelname as team_head")
            )
            ->where('head_tm.teamhead', 'Y')
            ->where('head_tm.statusflag', 'Y');

        $teamMembers = DB::table('audit.auditplanteammember as member_tm')
            ->join('audit.deptuserdetails as member', 'member.deptuserid', '=', 'member_tm.userid')
            ->join('audit.mst_designation as desig2', 'desig2.desigcode', '=', 'member.desigcode')
            ->select(
                'member_tm.auditplanteamid',
                DB::raw("STRING_AGG(member.username || ' - ' || desig2.desigelname, ', ') as team_members")
            )
            ->where('member_tm.teamhead', '!=', 'Y')
            ->where('member_tm.statusflag', 'Y')
            ->groupBy('member_tm.auditplanteamid');

        $teamCount = DB::table('audit.auditplanteammember')
            ->select('auditplanteamid', DB::raw('COUNT(*) as total_team_count'))
            ->groupBy('auditplanteamid');

        $query = DB::table('audit.mst_institution as mi')
            ->join('audit.auditplan as ap', 'ap.instid', '=', 'mi.instid')
            ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'ap.planmappingid')
            ->join('audit.mst_district as dist', 'mi.distcode', '=', 'dist.distcode')
            ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'mi.regioncode')
            ->joinSub($latestSchedule, 'latest_sch', function ($join) {
                $join->on('latest_sch.auditplanid', '=', 'ap.auditplanid');
            })
            ->join('audit.inst_auditschedule as sch', 'sch.auditscheduleid', '=', 'latest_sch.latest_schedule_id')
            ->leftJoinSub($slipCounts, 'slip_counts', function ($join) {
                $join->on('slip_counts.auditplanid', '=', 'ap.auditplanid');
            })
            ->leftJoinSub($teamHead, 'team_head', function ($join) {
                $join->on('team_head.auditplanteamid', '=', 'ap.auditteamid');
            })
            ->leftJoinSub($teamMembers, 'team_members', function ($join) {
                $join->on('team_members.auditplanteamid', '=', 'ap.auditteamid');
            })
            ->leftJoinSub($teamCount, 'team_count', function ($join) {
                $join->on('team_count.auditplanteamid', '=', 'ap.auditteamid');
            })
            ->when($regionCode, function ($query) use ($regionCode) {
                return $query->where('mi.regioncode', $regionCode);
            })
            ->when($deptCode, function ($query) use ($deptCode) {
                return $query->where('mi.deptcode', $deptCode);
            })
            ->when($distCode, function ($query) use ($distCode) {
                return $query->where('mi.distcode', $distCode);
            })
            ->when($quarter, function ($query) use ($quarter) {
                return $query->where('apm.group_key', $quarter);
            })
            ->when($financialyearcode, function ($query) use ($financialyearcode) {
                return $query->where('apm.financialyearcode', $financialyearcode);
            })
            ->whereNotNull('sch.entrymeetdate');

        if ($whichslip == 'pendingslipcount') {
            $query->where('slip_counts.pendingslips', '>', 0);
        } elseif ($whichslip == 'totalslips') {
            $query->where('slip_counts.totalslips', '>', 0);
        } elseif ($whichslip == 'convertedslipcount') {
            $query->where('slip_counts.convertedslips', '>', 0);
        } elseif ($whichslip == 'droppedslipcount') {
            $query->where('slip_counts.droppedslips', '>', 0);
        }

        return $query->select(
            'mi.instename',
            'mi.mandays',
            'ap.auditplanid',
            'sch.auditscheduleid',
            'dist.distename',
            'reg.regionename',
            'team_head.team_head as team_head_en',
            'team_members.team_members as team_members_en',
            'team_count.total_team_count',
            DB::raw("CASE
            WHEN sch.entrymeetdate IS NOT NULL
            THEN TO_CHAR(sch.entrymeetdate, 'DD/MM/YYYY')
            ELSE 'No'
        END as entrymeet_status"),
            DB::raw("CASE
            WHEN sch.exitmeetdate IS NOT NULL
            THEN TO_CHAR(sch.exitmeetdate, 'DD/MM/YYYY')
            ELSE 'No'
        END as exitmeet_status"),
            DB::raw('COALESCE(slip_counts.totalslips, 0) as totalslips'),
            DB::raw('COALESCE(slip_counts.droppedslips, 0) as droppedslips'),
            DB::raw('COALESCE(slip_counts.convertedslips, 0) as convertedslips'),
            DB::raw('COALESCE(slip_counts.pendingslips, 0) as pendingslips'),
            DB::raw("CASE
            WHEN sch.fromdate IS NOT NULL
            THEN TO_CHAR(sch.fromdate, 'DD/MM/YYYY')
            ELSE '-'
        END as fromdate"),
            DB::raw("CASE
            WHEN sch.todate IS NOT NULL
            THEN TO_CHAR(sch.todate, 'DD/MM/YYYY')
            ELSE '-'
        END as todate")
        )
            ->orderByRaw("CASE sch.statusflag WHEN 'F' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE sch.auditeeresponse WHEN 'A' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE sch.workallocationflag WHEN 'Y' THEN 0 ELSE 1 END")
            ->orderByRaw('CASE WHEN sch.entrymeetdate IS NOT NULL THEN 0 ELSE 1 END')
            ->orderByRaw('CASE WHEN sch.exitmeetdate IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('reg.regionename', 'asc')
            ->orderBy('dist.distename', 'asc')
            ->orderBy('sch.entrymeetdate', 'asc')
            ->orderBy('sch.exitmeetdate', 'asc')
            ->orderBy('mi.instename', 'asc')
            ->get();
    }

    public static function AuditeeCommencedInstitutedetailsGet($deptCode, $regionCode, $distCode, $whichslip, $quarter, $auditeeDeptCode, $catcode, $subcatcode, $financialyearcode = null)
    {
        $slipCounts = DB::table('audit.trans_auditslip as tas')
            ->select([
                'tas.auditplanid',
                DB::raw('COUNT(DISTINCT CASE WHEN tas.processcode IS NOT NULL THEN tas.auditslipid END) as totalslips'),
                DB::raw("COUNT(DISTINCT CASE WHEN tas.processcode = 'A' THEN tas.auditslipid END) as droppedslips"),
                DB::raw("COUNT(DISTINCT CASE WHEN tas.processcode = 'X' THEN tas.auditslipid END) as convertedslips"),
                DB::raw("COUNT(DISTINCT CASE WHEN tas.processcode NOT IN ('A', 'X') THEN tas.auditslipid END) as pendingslips"),
            ])
            ->groupBy('tas.auditplanid');

        $latestSchedule = DB::table('audit.inst_auditschedule as ias')
            ->select(['ias.auditplanid', DB::raw('MAX(ias.auditscheduleid) as latest_schedule_id')])
            ->groupBy('ias.auditplanid');

        $teamHead = DB::table('audit.auditplanteammember as head_tm')
            ->join('audit.deptuserdetails as head', 'head.deptuserid', '=', 'head_tm.userid')
            ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'head.desigcode')
            ->select([
                'head_tm.auditplanteamid',
                DB::raw("head.username || ' - ' || desig.desigelname as team_head"),
            ])
            ->where([
                ['head_tm.teamhead', '=', 'Y'],
                ['head_tm.statusflag', '=', 'Y'],
            ]);

        $teamMembers = DB::table('audit.auditplanteammember as member_tm')
            ->join('audit.deptuserdetails as member', 'member.deptuserid', '=', 'member_tm.userid')
            ->join('audit.mst_designation as desig2', 'desig2.desigcode', '=', 'member.desigcode')
            ->select([
                'member_tm.auditplanteamid',
                DB::raw("STRING_AGG(member.username || ' - ' || desig2.desigelname, ', ') as team_members"),
            ])
            ->where('member_tm.teamhead', '!=', 'Y')
            ->where('member_tm.statusflag', '=', 'Y')
            ->groupBy('member_tm.auditplanteamid');

        $teamCount = DB::table('audit.auditplanteammember')
            ->select(['auditplanteamid', DB::raw('COUNT(*) as total_team_count')])
            ->groupBy('auditplanteamid');

        // Main query construction
        $query = DB::table('audit.mst_institution as mi')
            ->join('audit.auditplan as ap', 'ap.instid', '=', 'mi.instid')
            ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'ap.planmappingid')
            ->join('audit.mst_district as dist', 'mi.distcode', '=', 'dist.distcode')
            ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'mi.regioncode')
            ->join('audit.mst_auditeedept as mad', 'mi.auditeedeptcode', '=', 'mad.auditeedeptcode')
            ->joinSub($latestSchedule, 'latest_sch', 'latest_sch.auditplanid', '=', 'ap.auditplanid')
            ->join('audit.inst_auditschedule as sch', 'sch.auditscheduleid', '=', 'latest_sch.latest_schedule_id')
            ->leftJoinSub($slipCounts, 'slip_counts', 'slip_counts.auditplanid', '=', 'ap.auditplanid')
            ->leftJoinSub($teamHead, 'team_head', 'team_head.auditplanteamid', '=', 'ap.auditteamid')
            ->leftJoinSub($teamMembers, 'team_members', 'team_members.auditplanteamid', '=', 'ap.auditteamid')
            ->leftJoinSub($teamCount, 'team_count', 'team_count.auditplanteamid', '=', 'ap.auditteamid')
            ->whereNotNull('sch.entrymeetdate')
            ->where('mi.deptcode', $deptCode)
            ->when($quarter, function ($query) use ($quarter) {
                return $query->where('apm.group_key', $quarter);
            })
            ->when($financialyearcode, function ($query) use ($financialyearcode) {
                return $query->where('apm.financialyearcode', $financialyearcode);
            });
        // Apply filters
        if (! empty($regionCode)) {
            $query->where('mi.regioncode', $regionCode);
        }

        if (! empty($distCode)) {
            $query->where('mi.distcode', $distCode);
        }

        if (! empty($auditeeDeptCode)) {
            $query->where('mi.auditeedeptcode', $auditeeDeptCode);
        }

        // Handle category filtering
        if (! empty($catcode)) {
            if (strpos($catcode, ',') !== false) {
                $categoryIds = explode(',', $catcode);
                $query->whereIn('mi.catcode', $categoryIds);
            } else {
                $query->where('mi.catcode', $catcode);
            }
        }

        // Handle subcategory filtering
        if (! empty($subcatcode)) {
            if (strpos($subcatcode, ',') !== false) {
                $subcategoryIds = explode(',', $subcatcode);
                $query->whereIn('mi.subcatid', $subcategoryIds);
            } else {
                $query->where('mi.subcatid', $subcatcode);
            }
        }

        // Select fields
        $selectFields = [
            'mi.instename',
            'mi.mandays',
            'ap.auditplanid',
            'sch.auditscheduleid',
            'dist.distename',
            'reg.regionename',
            'mad.auditeedeptename',
            'team_head.team_head as team_head_en',
            'team_members.team_members as team_members_en',
            'team_count.total_team_count',
            DB::raw("CASE
            WHEN sch.entrymeetdate IS NOT NULL
            THEN TO_CHAR(sch.entrymeetdate, 'DD/MM/YYYY')
            ELSE 'No'
        END as entrymeet_status"),
            DB::raw("CASE
            WHEN sch.exitmeetdate IS NOT NULL
            THEN TO_CHAR(sch.exitmeetdate, 'DD/MM/YYYY')
            ELSE 'No'
        END as exitmeet_status"),
            DB::raw('COALESCE(slip_counts.totalslips, 0) as totalslips'),
            DB::raw('COALESCE(slip_counts.droppedslips, 0) as droppedslips'),
            DB::raw('COALESCE(slip_counts.convertedslips, 0) as convertedslips'),
            DB::raw('COALESCE(slip_counts.pendingslips, 0) as pendingslips'),
            DB::raw("CASE
            WHEN sch.fromdate IS NOT NULL
            THEN TO_CHAR(sch.fromdate, 'DD/MM/YYYY')
            ELSE '-'
        END as fromdate"),
            DB::raw("CASE
            WHEN sch.todate IS NOT NULL
            THEN TO_CHAR(sch.todate, 'DD/MM/YYYY')
            ELSE '-'
        END as todate"),
        ];



        return $query->select($selectFields)
            ->orderByRaw("CASE sch.statusflag WHEN 'F' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE sch.auditeeresponse WHEN 'A' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE sch.workallocationflag WHEN 'Y' THEN 0 ELSE 1 END")
            ->orderByRaw('CASE WHEN sch.entrymeetdate IS NOT NULL THEN 0 ELSE 1 END')
            ->orderByRaw('CASE WHEN sch.exitmeetdate IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('reg.regionename')
            ->orderBy('dist.distename')
            ->orderBy('sch.entrymeetdate')
            ->orderBy('sch.exitmeetdate')
            ->orderBy('mi.instename')
            ->get();
    }


    public static function GetallDept($deptcode = null)
    {
        $deptdel = DB::table('audit.mst_dept')
            ->select('deptesname', 'deptelname', 'deptcode')
            ->where('statusflag', '=', 'Y')
            ->when($deptcode, function ($query, $deptcode) {
                return $query->where('deptcode', $deptcode);
            })
            ->orderBy('deptcode', 'asc')
            ->get();

        return $deptdel;
    }

    public static function getslipcount($audit_scheduleid)
    {
        // Start building the count query
        $countQuery = DB::table('audit.trans_auditslip as tas')->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'tas.auditplanid')
            ->where('tas.auditscheduleid', $audit_scheduleid);

        // Add the selectRaw part for counting, with DISTINCT and GROUP BY to avoid duplicates
        $countQuery->selectRaw("COUNT(DISTINCT CASE WHEN tas.processcode IS NOT NULL THEN tas.auditslipid END) as totalslips,
                    COUNT(DISTINCT CASE WHEN tas.processcode = 'A' THEN tas.auditslipid END) as droppedslips,
                    COUNT(DISTINCT CASE WHEN tas.processcode = 'X' THEN tas.auditslipid END) as convertedslips,
                    COUNT(DISTINCT CASE WHEN tas.processcode NOT IN ('A', 'X') THEN tas.auditslipid END) as pendingslips
                    ")
            ->groupBy('tas.auditscheduleid'); // Group by 'auditscheduleid' to avoid duplicates

        // Execute the query and get the first result
        $countQuery = $countQuery->first();

        $data = json_decode(json_encode($countQuery), true); // Convert stdClass to array

        return $data;
    }

    // public static function RegionwiseDetails($deptCode, $regionCode = null, $distCode = null,$quarter)
    // {
    //     $query = DB::table('audit.auditor_instmapping as ais')
    //         ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ais.regioncode')
    //         ->select('reg.regionename', 'reg.regioncode')
    //         ->where('reg.statusflag', 'Y')
    //         ->where('ais.deptcode', $deptCode)
    //         ->orderBy('reg.regionename', 'asc');

    //     // Conditionally add regionCode filter
    //     if (!empty($regionCode)) {
    //         $query->where('ais.regioncode', $regionCode);
    //     }

    //     // Conditionally add distCode filter
    //     if (!empty($distCode)) {
    //         $query->where('ais.distcode', $distCode);
    //     }

    //     return $query->distinct()
    //                  ->orderBy('reg.regioncode', 'asc')
    //                  ->get();
    // }

    public static function RegionwiseDetails($deptCode, $regionCode, $distCode, $quarter = null, $financialyearcode)
    {
        $query = DB::table('audit.auditplan as ais')
            ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'ais.planmappingid')

            ->when(!empty($quarter), function ($q) use ($quarter) {
                $q->where('apm.group_key', $quarter);
            })

            ->where('apm.financialyearcode', $financialyearcode)

            ->join('audit.mst_institution as ins', function ($join) use ($deptCode, $regionCode, $distCode) {
                $join->on('ins.instid', '=', 'ais.instid')
                    ->where('ins.deptcode', $deptCode);

                if (!empty($regionCode)) {
                    $join->where('ins.regioncode', $regionCode);
                }

                if (!empty($distCode)) {
                    $join->where('ins.distcode', $distCode);
                }
            })

            ->join('audit.mst_region as reg', function ($join) {
                $join->on('reg.regioncode', '=', 'ins.regioncode')
                    ->where('reg.statusflag', 'Y');
            })

            ->select('reg.regionename', 'reg.regioncode')
            ->groupBy('reg.regionename', 'reg.regioncode')
            ->orderBy('reg.regioncode', 'asc');

        return $query->get();
    }


  public static function RegionTemplatewiseDetails($deptCode, $regionCode, $distCode, $quarter = null, $financialyearcode)
    {
        $query = DB::table('audit.templateauditplan as ais')
            ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'ais.planmappingid')

            ->when(!empty($quarter), function ($q) use ($quarter) {
                $q->where('apm.group_key', $quarter);
            })

            ->where('apm.financialyearcode', $financialyearcode)
            ->join('audit.mst_institution as ins', function ($join) use ($deptCode, $regionCode, $distCode) {
                $join->on('ins.instid', '=', 'ais.instid')
                    ->where('ins.deptcode', $deptCode);

                if (! empty($regionCode)) {
                    $join->where('ins.regioncode', $regionCode);
                }
                if (! empty($distCode)) {
                    $join->where('ins.distcode', $distCode);
                }
            })
            ->join('audit.mst_region as reg', function ($join) {
                $join->on('reg.regioncode', '=', 'ins.regioncode')
                    ->where('reg.statusflag', 'Y');
            })
            ->select('reg.regionename', 'reg.regioncode')
            ->groupBy('reg.regionename', 'reg.regioncode')
            ->orderBy('reg.regioncode', 'asc');

        return $query->get();
    }



  public static function AuditeeRegionwiseDetails($deptCode, $regionCode, $distCode, $quarter, $auditeedeptCode, $catcode, $subcatcode, $financialyearcode)
    {
        $query = DB::table('audit.auditplan as ais')
            ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'ais.planmappingid')

            ->when(!empty($quarter), function ($q) use ($quarter) {
                $q->where('apm.group_key', $quarter);
            })
            ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ais.instid')
            ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ins.regioncode')
            ->select('reg.regionename', 'reg.regioncode')
            ->where('ins.statusflag', 'Y')
            ->where('ins.deptcode', $deptCode)
            ->where('apm.financialyearcode', $financialyearcode)

            ->orderBy('reg.regionename', 'asc');

        if (!empty($regionCode)) {
            $query->where('ins.regioncode', $regionCode);
        }

        if (!empty($distCode)) {
            $query->where('ins.distcode', $distCode);
        }

        if (!empty($auditeedeptCode)) {
            $query->where('ins.auditeedeptcode', $auditeedeptCode);
        }
        if (!empty($catcode)) {
            if (strpos($catcode, ',') !== false) {
                $categoryIds = explode(',', $catcode);
                $query->whereIn('ins.catcode', $categoryIds);
            } else {
            $query->where('ins.catcode', $catcode);
        }
        }

        if (!empty($subcatcode)) {
            if (strpos($subcatcode, ',') !== false) {
                $subcategoryIds = array_map('intval', explode(',', $subcatcode));
                $query->whereIn('ins.subcatid', $subcategoryIds);
            } else {
                $query->where('ins.subcatid', (int)$subcatcode);
            }
        }


        return $query->distinct()
            ->orderBy('reg.regioncode', 'asc')
            ->get();

    }



    public static function AuditeeTemplateRegionwiseDetails($deptCode, $regionCode, $distCode, $quarter, $auditeedeptCode, $catcode, $subcatcode, $financialyearcode)
    {
        $query = DB::table('audit.templateauditplan as ais')
                    ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'ais.planmappingid')

            ->when(!empty($quarter), function ($q) use ($quarter) {
                $q->where('apm.group_key', $quarter);
            })
            ->where('apm.financialyearcode', $financialyearcode)

            ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ais.instid')
            ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ins.regioncode')
            ->select('reg.regionename', 'reg.regioncode')
            ->where('ins.statusflag', 'Y')
            ->where('ins.deptcode', $deptCode)
            ->orderBy('reg.regionename', 'asc');

        if (!empty($regionCode)) {
            $query->where('ins.regioncode', $regionCode);
        }

        if (!empty($distCode)) {
            $query->where('ins.distcode', $distCode);
        }

        if (!empty($auditeedeptCode)) {
            $query->where('ins.auditeedeptcode', $auditeedeptCode);
        }
        if (!empty($catcode)) {
            if (strpos($catcode, ',') !== false) {
                $categoryIds = explode(',', $catcode);
                $query->whereIn('ins.catcode', $categoryIds);
            } else {
            $query->where('ins.catcode', $catcode);
        }
        }

        if (!empty($subcatcode)) {
            if (strpos($subcatcode, ',') !== false) {
                $subcategoryIds = array_map('intval', explode(',', $subcatcode));
                $query->whereIn('ins.subcatid', $subcategoryIds);
            } else {
                $query->where('ins.subcatid', (int)$subcatcode);
            }
        }


        return $query->distinct()
            ->orderBy('reg.regioncode', 'asc')
            ->get();

    }



    public static function DistrictwiseDetails($deptCode, $regionCode, $distCode, $quarter, $financialyearcode)
    {
        $query = DB::table('audit.auditplan as ais')
            ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'ais.planmappingid')
            ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ais.instid')
            ->join('audit.mst_district as dist', 'ins.distcode', '=', 'dist.distcode')
            ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ins.regioncode')
            ->select('dist.distename', 'reg.regionename', 'reg.regioncode', 'dist.distcode')
            ->where('ins.statusflag', 'Y')
            ->orderBy('reg.regionename', 'asc')
            ->orderBy('dist.distename', 'asc');


        if (! empty($deptCode)) {
            $query->where('ins.deptcode', $deptCode);
        }

        if (! empty($regionCode)) {
            $query->where('ins.regioncode', $regionCode);
        }

        if (! empty($distCode)) {
            $query->where('ins.distcode', $distCode);
        }

        if (! empty($quarter)) {
            $query->where('apm.group_key', $quarter);
        }

        if (! empty($financialyearcode)) {
            $query->where('apm.financialyearcode', $financialyearcode);
        }

        return $query->distinct()
            ->orderBy('reg.regioncode', 'asc')
            ->get();
    }


   public static function AuditeeDistrictwiseDetails($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $catcode ,$subcatcode , $financialyearcode)
    {
        $query = DB::table('audit.auditplan as ais')
            ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'ais.planmappingid')
            ->when(!empty($quarter), function ($q) use ($quarter) {
                $q->where('apm.group_key', $quarter);
            })
            ->where('apm.financialyearcode', $financialyearcode)
            ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ais.instid')
            ->join('audit.mst_district as dist', 'ins.distcode', '=', 'dist.distcode')
            ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ins.regioncode')
            ->join('audit.mst_auditeedept as mad', 'ins.auditeedeptcode', '=', 'mad.auditeedeptcode') // Auditee department join
            ->select(
                'dist.distcode',
                'dist.distename',
                'reg.regioncode',
                'reg.regionename',
                'mad.auditeedeptename'
            )
            ->where('ins.statusflag', 'Y')
            ->where('ins.deptcode', $deptCode)
            ->orderBy('reg.regionename', 'asc')
            ->orderBy('dist.distename', 'asc');

        // Optional filter by region
        if (! empty($regionCode)) {
            $query->where('ins.regioncode', $regionCode);
        }

        // Optional filter by district
        if (! empty($distCode)) {
            $query->where('ins.distcode', $distCode);
        }

        // Optional filter by auditee department
        if (! empty($auditeeDeptCode)) {
            $query->where('ins.auditeedeptcode', $auditeeDeptCode);
        }
        if (! empty($catcode)) {
            if (strpos($catcode, ',') !== false) {
                $categoryIds = explode(',', $catcode);
                $query->whereIn('ins.catcode', $categoryIds);
            } else {
            $query->where('ins.catcode', $catcode);
        }
        }

        // Handle subcategory filtering
        if (! empty($subcatcode)) {
            if (strpos($subcatcode, ',') !== false) {
                $subcategoryIds = explode(',', $subcatcode);
                $query->whereIn('ins.subcatid', $subcategoryIds);
            } else {
            $query->where('ins.subcatid', $subcatcode);
        }
        }

        return $query->distinct()
            ->get();
    }

    public static function AuditeeTemplateDistrictwiseDetails($deptCode, $regionCode, $distCode, $quarter, $auditeeDeptCode, $catcode ,$subcatcode , $financialyearcode)
    {
        $query = DB::table('audit.templateauditplan as ais')
            ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'ais.planmappingid')
            ->when(!empty($quarter), function ($q) use ($quarter) {
                $q->where('apm.group_key', $quarter);
            })
            ->where('apm.financialyearcode', $financialyearcode)
            ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ais.instid')
            ->join('audit.mst_district as dist', 'ins.distcode', '=', 'dist.distcode')
            ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ins.regioncode')
            ->join('audit.mst_auditeedept as mad', 'ins.auditeedeptcode', '=', 'mad.auditeedeptcode') // Auditee department join
            ->select(
                'dist.distcode',
                'dist.distename',
                'reg.regioncode',
                'reg.regionename',
                'mad.auditeedeptename'
            )
            ->where('ins.statusflag', 'Y')
            ->where('ins.deptcode', $deptCode)
            ->orderBy('reg.regionename', 'asc')
            ->orderBy('dist.distename', 'asc');

        // Optional filter by region
        if (! empty($regionCode)) {
            $query->where('ins.regioncode', $regionCode);
        }

        // Optional filter by district
        if (! empty($distCode)) {
            $query->where('ins.distcode', $distCode);
        }

        // Optional filter by auditee department
        if (! empty($auditeeDeptCode)) {
            $query->where('ins.auditeedeptcode', $auditeeDeptCode);
        }
        if (! empty($catcode)) {
            if (strpos($catcode, ',') !== false) {
                $categoryIds = explode(',', $catcode);
                $query->whereIn('ins.catcode', $categoryIds);
            } else {
            $query->where('ins.catcode', $catcode);
        }
        }

        // Handle subcategory filtering
        if (! empty($subcatcode)) {
            if (strpos($subcatcode, ',') !== false) {
                $subcategoryIds = explode(',', $subcatcode);
                $query->whereIn('ins.subcatid', $subcategoryIds);
            } else {
            $query->where('ins.subcatid', $subcatcode);
        }
        }

        return $query->distinct()
            ->get();
    }


 public static function GetLegacyReportCounts($deptCode, $regionCode, $distCode, $quarter)
    {

        DB::statement(query: 'SET search_path TO audit');
        $result = DB::select('SELECT audit.fn_getlegacyreportcountofdeptwise(?, ?, ?,?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $quarter,
        ]);

        return json_decode($result[0]->data, true);
    }

    public static function GetParaReportCounts ($deptCode, $regionCode, $distCode)
    {

        DB::statement(query: 'SET search_path TO audit');
        $result = DB::select('SELECT audit.fn_getparareportcountofdeptwise(?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode
        ]);

        return json_decode($result[0]->data, true);
    }

public static function GetParaCountDetails ($deptCode, $regionCode, $distCode)
    {

        DB::statement(query: 'SET search_path TO audit');
        $result = DB::select('SELECT audit.fn_getparareportcountdetailofuserwise(?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode
        ]);

        return json_decode($result[0]->data, true);
    }



    public static function LegacyInstitutionwiseData($deptCode, $regionCode, $distCode)
    {
        try {
            DB::statement('SET search_path TO audit');

            $deptCode = $deptCode ?: null;
            $regionCode = $regionCode ?: null;
            $distCode = $distCode ?: null;

            $result = DB::select('SELECT audit.legacyInstitutionwiseData(?, ?, ?) AS data', [
                $deptCode,
                $regionCode,
                $distCode,
            ]);

            if (empty($result) || !isset($result[0]->data)) {
                return [];
            }

            $json = $result[0]->data;
            $data = json_decode($json, true);

            return $data ?: [];

        } catch (\Exception $e) {
            \Log::error('LegacyInstitutionwiseData Error: ' . $e->getMessage());
            return [];
        }
        }

    public static function ParaInstitutionwiseData($deptCode, $regionCode, $distCode)
    {
        try {
            DB::statement('SET search_path TO audit');

            $deptCode = $deptCode ?: null;
            $regionCode = $regionCode ?: null;
            $distCode = $distCode ?: null;

            $result = DB::select('SELECT audit.paradetailsinstitutionwisedata(?, ?, ?) AS data', [
                $deptCode,
                $regionCode,
                $distCode,
            ]);

            if (empty($result) || !isset($result[0]->data)) {
                return [];
            }

            $json = $result[0]->data;
            $data = json_decode($json, true);

            return $data ?: [];

        } catch (\Exception $e) {
            \Log::error('LegacyInstitutionwiseData Error: ' . $e->getMessage());
            return [];
        }
     }

public static function ParaCountwiseData($deptCode, $regionCode, $distCode)
    {
        try {
            DB::statement('SET search_path TO audit');

            $deptCode = $deptCode ?: null;
            $regionCode = $regionCode ?: null;
            $distCode = $distCode ?: null;

            $result = DB::select('SELECT audit.paradetailscount(?, ?, ?) AS data', [
                $deptCode,
                $regionCode,
                $distCode,
            ]);

            if (empty($result) || !isset($result[0]->data)) {
                return [];
            }

            $json = $result[0]->data;
            $data = json_decode($json, true);

            return $data ?: [];

        } catch (\Exception $e) {
            \Log::error('LegacyInstitutionwiseData Error: ' . $e->getMessage());
            return [];
        }
        }

}
