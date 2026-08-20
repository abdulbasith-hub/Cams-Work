<?php

namespace App\Models;

use App\Services\PHPMailerService;
use App\Services\SmsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class YearcodeMapping extends Model
{
    protected $connection = 'pgsql';  // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon';  // Custom column name for `created_at`

    const UPDATED_AT = 'updatedon';  // Custom column name for `updated_at` (if you have it)

    // Define the table associated with this model
    protected $table = 'audit.yearcode_mapping';

    // Set primary key if it differs from the default 'id'
    protected $primaryKey = 'yearcodemappingid';  // Assuming `yearcodemapping_id` is the primary key

    // Set the primary key type if it's not an auto-incrementing integer
    protected $keyType = 'int';  // If `userid` is an integer

    protected static $regionTable = BaseModel::REGION_TABLE;

    protected static $auditplanmapping_table = BaseModel::AUDITPLANMAPPING_TABLE;

    protected static $get_auditplanning_func = BaseModel::GET_AUDITPLANNING_FUNC;

    protected static $mst_financialyearcode_table = BaseModel::MST_FINANCIAL_TABLE;

    // Disable auto-incrementing if necessary
    public $incrementing = true;  // If true, it will be treated as an auto-incrementing column

    // Set the fillable fields
    protected $fillable = ['auditplanid', 'yearselected', 'statusflag', 'financestatus', 'createdby', 'createdon', 'updatedon'];

    public static function fetchYearmapById($AuditId, $finance = '')
    {
        return self::where('auditplanid', $AuditId)
            ->where('statusflag', 'Y')
            ->where(function ($query) use ($finance) {
                $query
                    ->where('financestatus', $finance)
                    ->orWhereNull('financestatus')
                    ->orWhere('financestatus', '');
            })
            ->get();
    }
}

class StoreCFR extends Model
{
    protected $connection = 'pgsql';  // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon';  // Custom column name for `created_at`

    const UPDATED_AT = 'updatedon';  // Custom column name for `updated_at` (if you have it)

    // Define the table associated with this model
    protected $table = 'audit.selected_cfr';

    // Set primary key if it differs from the default 'id'
    protected $primaryKey = 'selected_cfrid';  // Assuming `yearcodemapping_id` is the primary key

    // Set the primary key type if it's not an auto-incrementing integer
    protected $keyType = 'int';  // If `userid` is an integer

    // Disable auto-incrementing if necessary
    public $incrementing = true;  // If true, it will be treated as an auto-incrementing column

    // Set the fillable fields
    protected $fillable = ['auditscheduleid', 'selected_cfr', 'statusflag'];
}

class AuditManagementModel extends Model
{
    // protected $smsService;
    // protected $mailService;

    // Combine both services in the constructor
    // public function __construct(SmsService $smsService, PHPMailerService $mailService)
    //  {
    //    $this->smsService = $smsService;
    //  $this->mailService = $mailService;
    // }
    protected static $mst_financialyearcode_table = BaseModel::MST_FINANCIAL_TABLE;

    protected static $auditplanmapping_table = BaseModel::AUDITPLANMAPPING_TABLE;

    protected static $get_auditplanning_func = BaseModel::GET_AUDITPLANNING_FUNC;

    protected static $mst_prfauditTitle_table = BaseModel::MST_PRAUDIT_TITLE_TABLE;

    protected static $praudit_instmap_table = BaseModel::PRAUDIT_INSTMAP_TABLE;

    protected static $teamassignments_table = BaseModel::TEAMASSIGNMENTS_TABLE;

    protected static $loop_untilfinished_function = BaseModel::LOOP_UNTILFINISHED_FUNCTION;

    protected static $mapinst_table = BaseModel::MAPINST_TABLE;

    protected static $rolemapping_table = BaseModel::ROLEMAPPING_TABLE;

    protected static $deptartment_table = BaseModel::DEPARTMENT_TABLE;

    protected static $institution_table = BaseModel::INSTITUTION_TABLE;

    protected static $auditplan_table = BaseModel::AUDITPLAN_TABLE;

    protected static $temprankusers_table = BaseModel::TEMPRANKUSERS_TABLE;

    protected static $designation_table = BaseModel::DESIGNATION_TABLE;

    protected static $userdetail_table = BaseModel::USERDETAIL_TABLE;

    protected static $auditplanteam_table = BaseModel::AUDITPLANTEAM_TABLE;

    protected static $auditplanteammem_table = BaseModel::AUDITPLANTEAMMEM_TABLE;

    protected static $typeofaudit_table = BaseModel::TYPEOFAUDIT_TABLE;

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

    protected static $auditperiod_table = BaseModel::AUDITPERIOD_TABLE;

    protected static $callforrec_table = BaseModel::CALLFORRECORDS_AUDITEE_TABLE;

    protected static $mapregiondistrict_table = BaseModel::MAPREGIONDISTRICT_TABLE;

    protected static $dist_table = BaseModel::DIST_Table;

    protected static $auditdistrict_table = BaseModel::AUDITDISTRICT_TABLE;

    protected static $userchargedetail_table = BaseModel::USERCHARGEDETAIL_TABLE;

    protected static $chargedetail_table = BaseModel::CHARGEDETAIL_TABLE;

    protected static $readyforautomate_function = BaseModel::READYFORAUTOMATE_FUNCTION;

    protected static $auditor_instmapping_table = BaseModel::AUDITOR_INSTMAPPING_TABLE;

    protected static $subcategory_table = BaseModel::SUBCATEGORY_TABLE;

    protected static $regionTable = BaseModel::REGION_TABLE;

    protected $connection = 'pgsql';  // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon';  // Custom column name for `created_at`

    const UPDATED_AT = 'updatedon';  // Custom column name for `updated_at` (if you have it)

    // Specify that the primary key is `userid` instead of `id`
    protected $primaryKey = 'auditplanid';

    // Specify the table name
    protected $table = 'audit.auditplan';

    // Set the primary key type if it's not an auto-incrementing integer
    protected $keyType = 'int';  // If `userid` is an integer

    // If your primary key is not auto-incrementing, set `incrementing` to false
    public $incrementing = true;  // Set to `false` if `userid` is not auto-incrementing

    // Define the fillable fields
    protected $fillable = [
        'instid',
        'auditteamid',
        'typeofauditcode',
        'auditperiodid',
        'auditquartercode',
        'statusflag',
    ];

    /**
     * Create a new user if it doesn't already exist based on email, phone, name, and address.
     * Otherwise, update the user if it already exists, based on email, phone, and name (excluding current id).
     *
     * @param  array  $data
     * @param  int|null  $currentUserId  (optional: pass the current user's id for updates)
     * @return User|false
     */
    public static function getquarterDet($deptcode)
    {
        return DB::table(self::$deptartment_table.' as dept')
            ->join(self::$auditquarter_table.' as quart', 'quart.auditquartercode', '=', 'dept.currentquarter')
            ->where('dept.deptcode', $deptcode)
            ->select('dept.currentquarter', 'quart.auditquarter', 'quart.auditquartercode', 'quart.auditquartertname')
            ->distinct()
            ->get();
    }

    public static function createIfNotExistsOrUpdate(array $data, $currentUserId, array $yeararr, $VarDel = null)
    {
        $YearcodeMapArr = [];
        $data['statusflag'] = $data['statusflag'];
        // $data['yearcode'] = '0';

        try {
            // If currentUserId is provided, we are doing an update operation
            if ($currentUserId) {
                // for delete particular record
                if (isset($data['statusflag']) && $VarDel == 'Delete') {
                    // Set the new status flag value
                    $data['statusflag'] = 'N';

                    DB::enableQueryLog();

                    // Search for the record with statusflag = 1 and matching auditplanid
                    $existingRecord = self::where('statusflag', 'Y')
                        ->where('auditplanid', $currentUserId)
                        ->first();

                    // Check if the record exists
                    if ($existingRecord) {
                        // Update the record with the new statusflag value
                        // $existingRecord->update(['statusflag' => $data['statusflag']]);

                        // Manually perform the update using the Query Builder
                        $connection = 'pgsql';  // Name of the database connection
                        $table = 'audit.auditplan';  // Full table name (including schema)

                        $UpdateAuditDelete = DB::connection($connection)
                            ->table($table)
                            ->where('auditplanid', $currentUserId)
                            ->update(['statusflag' => $data['statusflag']]);

                        // Return the updated record
                        return $UpdateAuditDelete;
                    }
                }

                $existingUser = self::query()
                    ->whereIn('statusflag', ['Y', 'F'])
                    // ->where('statusflag', '=', 'Y')
                    ->where('auditteamid', '=', $data['auditteamid'])
                    ->where('instid', '=', $data['instid'])
                    ->where('auditplanid', '!=', $currentUserId)
                    ->first();

                if ($existingUser) {
                    // If a user exists, return false or throw an exception (depending on your need)
                    return false;
                }

                // If no such user exists, update the existing record with the provided data
                $existingUser = self::find($currentUserId);
                $existingUser->update($data);

                $existingMappingarr = YearcodeMapping::fetchYearmapById($currentUserId);
                $yearSelectedArr = $existingMappingarr->pluck('yearselected')->toarray();

                $findNewArr = array_diff($yeararr, $yearSelectedArr);
                $RemoveExistArr = array_diff($yearSelectedArr, $yeararr);

                if (count($findNewArr) > 0) {
                    self::updateyearcodemapping($findNewArr, $currentUserId);
                }

                if (count($RemoveExistArr) > 0) {
                    self::updateyearcodemapping($RemoveExistArr, $currentUserId, 'Updatestatusflag');
                }

                return $existingUser;
            } else {
                // Check if a data with the same institute, auditteamcode, year already exists for insertion
                $existingUser = self::query()
                    ->whereIn('statusflag', ['Y', 'F'])
                    // ->where('statusflag', '=', 'Y')
                    ->where('auditteamid', '=', $data['auditteamid'])
                    ->where('instid', '=', $data['instid'])
                    ->first();

                if ($existingUser) {
                    // If data exists, return false or handle it as needed
                    return false;
                }
                // Otherwise, create and return the new user

                $CreateAuditPlanid = self::create($data);
                $GetAuditPlanId = $CreateAuditPlanid->auditplanid;
                self::updateyearcodemapping($yeararr, $GetAuditPlanId);

                return $GetAuditPlanId;
            }
        } catch (QueryException $e) {
            // Handle any database-specific exceptions (e.g., duplicate entry)
            Log::error('Database error: '.$e->getMessage());
            throw new Exception('Database error occurred. Please try again later.');
        } catch (Exception $e) {
            // Handle any other general exceptions
            Log::error('General error: '.$e->getMessage());
            throw new Exception('Something went wrong: '.$e->getMessage());
        }
    }

    public static function updateyearcodemapping(array $data, $currentUserId, $statusflagupdate = '', $financestatus = '')
    {
        if ($statusflagupdate == 'Updatestatusflag') {
            foreach ($data as $YearVal) {
                // Check if the mapping already exists
                $yearmapping = YearcodeMapping::where('auditplanid', $currentUserId)
                    ->where('yearselected', $YearVal)
                    ->where('statusflag', 'Y')
                    ->first();
                if ($yearmapping) {
                    // If it exists, update the record
                    YearcodeMapping::where('auditplanid', $currentUserId)
                        ->where('yearselected', $YearVal)
                        ->where('statusflag', 'Y')
                        ->update(['statusflag' => 'N', 'financestatus' => $financestatus]);
                }
            }
        } else {
            foreach ($data as $YearVal) {
                // Check if the mapping already exists
                $yearmapping = YearcodeMapping::where('auditplanid', $currentUserId)
                    ->where('yearselected', $YearVal)
                    ->where('statusflag', 'Y')
                    ->first();
                if ($yearmapping) {
                    // If it exists, update the record
                    $yearmapping->update(['yearselected' => $YearVal, 'financestatus' => $financestatus]);
                } else {
                    // If it doesn't exist, create a new mapping
                    YearcodeMapping::create([
                        'auditplanid' => $currentUserId,
                        'yearselected' => $YearVal,
                        'createdby' => $currentUserId,
                        'statusflag' => 'Y',
                        'financestatus' => $financestatus,
                    ]);
                }
            }
        }
    }

    public static function fetchAllusers()
    {
        // Fetch all records where statusflag is 1
        // $AllData = self::whereIn('statusflag', ['Y','F'])->get();

        $sessioncharge = session('charge');
        $deptcode = $sessioncharge->deptcode;
        $regioncode = $sessioncharge->regioncode;
        $distcode = $sessioncharge->distcode;

        $AllData = DB::table('audit.auditplan as auditPlan')
            // Join with mst_institution
            ->join('audit.mst_institution as inst', 'auditPlan.instid', '=', 'inst.instid')
            // Join with mst_dept
            ->join('audit.mst_dept as dept', 'inst.deptcode', '=', 'dept.deptcode')
            // Join with mst_region
            ->join('audit.mst_region as region', 'inst.regioncode', '=', 'region.regioncode')
            // Join with mst_district
            ->join('audit.mst_district as dist', 'inst.distcode', '=', 'dist.distcode')
            // Join with mst_auditeeins_category
            ->join('audit.mst_auditeeins_category as instCategory', 'inst.catcode', '=', 'instCategory.catcode')
            // Join with auditplanteam
            ->join('audit.auditplanteam as auditTeam', 'auditPlan.auditteamid', '=', 'auditTeam.auditplanteamid')
            // Join with mst_typeofaudit
            ->join('audit.mst_typeofaudit as typeOfAudit', 'auditPlan.typeofauditcode', '=', 'typeOfAudit.typeofauditcode')
            // Join with mst_auditquarter
            ->join('audit.mst_auditquarter as auditQuarter', function ($join) {
                $join
                    ->on('auditQuarter.deptcode', '=', 'inst.deptcode')
                    ->on('auditQuarter.auditquartercode', '=', 'auditPlan.auditquartercode');
            })
            // Add where conditions for filtering the active records
            ->whereIn('auditPlan.statusflag', ['Y', 'F'])  // Only active or flagged records from auditplan
            ->where('inst.statusflag', '=', 'Y')  // Only active institution records
            ->where('dept.statusflag', '=', 'Y')  // Only active department records
            ->where('region.statusflag', '=', 'Y')  // Only active region records
            ->where('dist.statusflag', '=', 'Y')  // Only active district records
            ->where('instCategory.statusflag', '=', 'Y')  // Only active institute category records
            ->where('auditTeam.statusflag', '=', 'F')  // Active audit team (assuming 'F' means active)
            ->where('typeOfAudit.statusflag', '=', 'Y')  // Only active Plan Period records
            ->where('auditQuarter.statusflag', '=', 'Y');  // Only active audit quarter records

        // Conditional WHERE clauses based on deptcode, regioncode, distcode
        if (! empty($deptcode)) {
            $AllData->where('inst.deptcode', '=', $deptcode);
        }

        if (! empty($regioncode)) {
            $AllData->where('inst.regioncode', '=', $regioncode);
        }

        if (! empty($distcode)) {
            $AllData->where('inst.distcode', '=', $distcode);
        }

        $AllData = $AllData->select(
            'auditPlan.*',  // All columns from auditplan
            'inst.deptcode',  // Department code from mst_institution
            'inst.instename',
            'dept.deptesname',  // Department name from mst_dept
            'region.regionename',  // Region name from mst_region
            'dist.distename',  // District name from mst_district
            'instCategory.catename',  // Institute Category name from mst_auditeeins_category
            'auditTeam.teamname',  // Audit Team name from auditplanteam
            'typeOfAudit.typeofauditename',  // Plan Period name from mst_typeofaudit
            'auditQuarter.auditquarter'  // Audit Quarter name from mst_auditquarter
        )->get();

        // Log the result for debugging (better than using print_r)
        \Log::info('Fetched Audit Records:', $AllData->toArray());  // Logs as array for better readability

        // Return the data (can be used in a controller to return the response)
        return $AllData;  // Eloquent collection
    }

    /**
     * Insert the yearcode mapping into the yearcode_mapping table and return the generated yearmapping_id.
     *
     * @param  string  $yearcodes  (comma-separated string of year codes)
     * @return int $yearmappingId (the primary key of the inserted record)
     */
    public static function fetchUserById($userId)
    {
        return self::find($userId);
    }

    public static function auditplandet($auditplanid, $userid)
    {
        $table = self::$auditplan_table;

        return DB::table($table)
            ->join('audit.mst_institution as ai', 'ai.instid', '=', 'auditplan.instid')
            ->join('audit.auditplanteam as at', 'at.auditplanteamid', '=', 'auditplan.auditteamid')
            ->join(self::$auditplanmapping_table.' as mpl', 'mpl.planmappingid', '=', 'auditplan.planmappingid')  // TODO::FINANCIAL_YR_CHANGE
            ->leftJoin('audit.inst_auditschedule as sch', 'auditplan.auditplanid', '=', 'sch.auditplanid')
            ->join('audit.auditplanteammember as atm', 'atm.auditplanteamid', '=', 'auditplan.auditteamid')
            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'atm.userid')
            ->join('audit.deptuserdetails as du', 'atm.userid', '=', 'du.deptuserid')
            ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
            ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'ai.deptcode')
            ->select(
                DB::raw("
                    CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM audit.logothertrans_plandel ltp
                            WHERE auditplan.auditplanid IN (
                                SELECT (jsonb_array_elements_text(ltp.auditplanid->'plan_ids'))::int
                            )
                        )
                        THEN 'Y'
                        ELSE 'N'
                    END AS _isparalelinst
                "),
                DB::raw("
                        CASE
                            WHEN sch.statusflag = 'Y' THEN 'Y'
                            WHEN sch.statusflag = 'F' THEN 'F'
                            ELSE 'N'
                        END AS isscheduled
                "),
                'auditplan.spilloverflag',
                'dept.rcno as dept_rcno',
                'dept.deptesname',
                'sch.auditscheduleid',
                'sch.rcno',
                'mpl.fromdate',
                'mpl.todate',
                'mpl.scheduleenddate',  // TODO::FINANCIAL_YR_CHANGE
                'auditplan.planmappingid',
                'auditplan.teamsize',
                'auditplan.auditmode',
                'ai.instename',
                'ai.insttname',
                'auditplan.mandays',
                'ai.instid',
                'ai.catcode',
                'ai.subcatid',
                'ai.deptcode',
                'ai.annadhanam_only',
                'de.desigelname',
                'de.desigtlname',
                'auditplan.auditteamid',
                'auditplan.auditplanid',
                'at.auditplanteamid',
                'atm.userid',
                'uc.userchargeid',
                'du.username',
                'du.usertamilname',
                'cd.chargedescription',
                'ai.carryforward',
                'auditplan.auditquartercode',
                DB::raw("(
                SELECT COUNT(*)
                FROM audit.auditplanteammember AS sub_atm
                WHERE sub_atm.auditplanteamid = auditplan.auditteamid
\t\t        and sub_atm.statusflag = 'Y'
                ) AS team_member_count"),
                'auditplan.auditquartercode',
            )
            ->where('auditplan.auditplanid', '=', $auditplanid)  // Use the decrypted or plain auditplanid
            ->where('atm.userid', '=', $userid)
            ->where('atm.teamhead', '=', 'Y')
            ->where('atm.statusflag', '=', 'Y')
            ->where('auditplan.statusflag', '=', 'F')
            ->get();
    }

    // public static function  fetch_auditplandetails($userid)
    // {

    //     return self::query()
    //         ->join('audit.mst_institution as ai', 'ai.instid', '=', 'auditplan.instid')
    //         ->join('audit.auditplanteam as at', 'at.auditplanteamid', '=', 'auditplan.auditteamid')
    //         ->join('audit.auditplanteammember as atm', 'atm.auditplanteamid', '=', 'auditplan.auditteamid')
    //         ->join('audit.mst_typeofaudit as mst', 'mst.typeofauditcode', '=', 'auditplan.typeofauditcode')
    //         // ->join('audit.mst_auditperiod as map', 'map.auditperiodid', '=', 'auditplan.auditperiodid')
    //         ->join('audit.mst_dept as msd', 'msd.deptcode', '=', 'ai.deptcode')
    //         ->join('audit.mst_auditeeins_category as mac', 'mac.catcode', '=', 'ai.catcode')
    //         ->join(
    //             DB::raw('(SELECT DISTINCT ON (auditquartercode) * FROM audit.mst_auditquarter) AS maq'),
    //             'maq.auditquartercode',
    //             '=',
    //             'auditplan.auditquartercode'
    //         )

    //         ->select(
    //             'ai.instename',
    //             'ai.insttname',
    //             'ai.deptcode',
    //             'ai.instid',
    //             'auditplan.auditteamid',
    //             'auditplan.auditplanid',
    //             'at.auditplanteamid',
    //             'atm.userid',
    //             'at.teamname',
    //             'mst.typeofauditename',
    //             'mst.typeofaudittname',
    //             // 'map.fromyear',
    //             // 'map.toyear',
    //             'msd.deptesname',
    //             'msd.depttsname',
    //             'mac.catename',
    //             'mac.cattname',
    //             'maq.auditquarter',
    //             'auditplan.statusflag',
    //             DB::raw('(
    //     SELECT COUNT(*)
    //     FROM audit.auditplanteammember AS sub_atm
    //     WHERE sub_atm.auditplanteamid = auditplan.auditteamid
    //     AND sub_atm.teamhead = \'N\'
    // ) AS team_member_count')
    //         )
    //         ->where('atm.userid', '=', $userid)
    //         ->where('atm.statusflag', '=', 'Y')
    //         ->where('atm.teamhead', '=', 'Y')
    //         ->where('auditplan.statusflag', '=', 'F')
    //         ->get();
    // }

    // public static function fetch_plandetail()
    // {
    //     return self::query()
    //         ->join('audit.mst_institution as ai', 'ai.instid', '=', 'auditplan.instid')
    //         ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'ai.deptcode')
    //         ->select(
    //             'dept.deptelname',
    //             // Subquery for total_team_count
    //             DB::raw('(SELECT COUNT( auditplanid) FROM audit.auditplan WHERE statusflag = \'F\') AS total_plan_count'),
    //             // DB::raw('(SELECT COUNT( auditplanid) FROM audit.auditplan WHERE statusflag = \'F\' and ) AS dept_plan_count'),
    //             // Subquery for team_member_count
    //             // DB::raw('(SELECT COUNT(DISTINCT at.planteammemberid) FROM audit.auditplanteammember as at WHERE at.statusflag = \'Y\' AND at.auditplanteamid = auditplanteam.auditplanteamid) AS team_member_count')
    //         )
    //         ->where('auditplan.statusflag', 'F')
    //         ->get();
    // }

    public static function fetch_plandetail()
    {
        return DB::table('audit.mst_dept AS dept')
            ->leftJoin('audit.mst_institution AS ai', 'ai.deptcode', '=', 'dept.deptcode')
            ->leftJoin('audit.auditplan AS auditplan', function ($join) {
                $join
                    ->on('auditplan.instid', '=', 'ai.instid')
                    ->where('auditplan.statusflag', '=', 'F');
            })
            ->select(
                'dept.deptelname',
                DB::raw("(SELECT COUNT(DISTINCT auditplanid) FROM audit.auditplan WHERE statusflag =  'F'  ) AS total_auditplan_count"),
                DB::raw('COUNT(DISTINCT auditplan.auditplanid) AS dept_plan_count')
            )
            ->groupBy('dept.deptelname')
            ->get();
    }

    public static function automate_plan($deptcode, $distcode, $auditquartercode)
    {
        return DB::select('SELECT * FROM  '.self::$automate_function.'(:distcode, :quartercode, :deptcode)', [
            'distcode' => $distcode,
            'quartercode' => $auditquartercode,
            'deptcode' => $deptcode,
        ]);
    }

    public static function checkfordetails($data)
    {
        return DB::select('SELECT * FROM  '.self::$readyforautomate_function.'(:distcode, :quartercode, :deptcode)', [
            // 'distcode'       => $data['distcode'],
            'quartercode' => $data['auditquartercode'],
            // 'deptcode'       => $data['deptcode'],
            'distcode' => $data['distcode'],
            // 'quartercode'    => 'Q1',
            'deptcode' => $data['deptcode'],
        ]);
    }

    public static function getUser_planStatus($data)
    {
        return DB::table(self::$auditor_instmapping_table)
            ->select('autoplanstatus', 'userverified')
            ->where('deptcode', $data['deptcode'])
            ->where('distcode', $data['distcode'])
            // ->where('regioncode', $checkdata['regioncode'])
            ->get();

        //      $querySql = $query->toSql();
        // $bindings = $query->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );
        //      print_r($finalQuery);
    }

    public static function finalize_plan($deptcode, $distcode, $auditquartercode)
    {
        // return  DB::select('SELECT * FROM ' . self::$finaliseplan_function . '(:distcode, :quartercode, :deptcode)', [
        //     'distcode' => $distcode,
        //     'quartercode' => $auditquartercode,
        //     'deptcode' => $deptcode,
        // ]);

        $query = DB::select(
            'SELECT * FROM '.self::$finaliseplan_function.'(:distcode, :quartercode, :deptcode)',
            [
                'distcode' => $distcode,
                'quartercode' => $auditquartercode,
                'deptcode' => $deptcode,
            ]
        );

        return $query;
        // Replacing bindings for debugging purposes
        // foreach ($bindings as $key => $value) {
        //     $query = str_replace(':' . $key, "'" . addslashes($value) . "'", $query);
        // }

        // dd($query);
    }

    // public static function getAuditPlan($deptcode, $distcode)
    // {
    //     // return self::$auditplan_table;
    //     return DB::table(self::$auditplan_table . ' as tmr')
    //         ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'tmr.instid')
    //         ->where('inst.deptcode', $deptcode)
    //         ->where('inst.distcode', $distcode)
    //         ->get();
    // }
    public static function getAuditPlanStatus($checkdata)
    {
        return DB::table(self::$auditdistrict_table)
            // if( $checkdata['deptcode'])
            ->select('planflag', 'userflag')
            ->where('auditdeptcode', $checkdata['deptcode'])
            ->where('auditdistcode', $checkdata['distcode'])
            // ->where('regioncode', $checkdata['regioncode'])
            ->get();

        // dd($query->toSql());
    }

    public static function updateuserStatus($checkdata)
    {
        DB::table(self::$auditdistrict_table)
            ->where('auditdeptcode', $checkdata['deptcode'])
            ->where('auditdistcode', $checkdata['distcode'])
            ->update(['userflag' => 'Y']);
    }

    public static function getAuditors($deptcode, $distcode)
    {
        return DB::table(self::$temprankusers_table.' as tmr')
            ->join(self::$institution_table.' as inst', 'inst.instid', '=', 'tmr.instid')
            ->join(self::$userdetail_table.' as du', 'tmr.deptuserid', '=', 'du.deptuserid')
            ->join(self::$designation_table.' as dd', 'dd.desigcode', '=', 'du.desigcode')
            ->where('tmr.deptcode', $deptcode)
            ->where('tmr.distcode', $distcode)
            ->orderBy('du.desigcode', 'asc')
            ->orderBy('du.deptuserid', 'asc')
            ->get();
    }

    public static function getAuditorUser($checkdata)
    {
        // Query to fetch all user details
        $users = DB::table(self::$userdetail_table.' as du')
            ->join(self::$userchargedetail_table.' as uc', 'uc.userid', '=', 'du.deptuserid')
            ->join(self::$chargedetail_table.' as cd', 'cd.chargeid', '=', 'uc.chargeid')
            ->join(self::$rolemapping_table.' as rm', 'rm.rolemappingid', '=', 'cd.rolemappingid')
            ->join(self::$designation_table.' as dd', 'dd.desigcode', '=', 'du.desigcode')
            ->join(self::$deptartment_table.' as dept', 'dept.deptcode', '=', 'du.deptcode')
            ->where('du.deptcode', $checkdata['deptcode'])
            ->where('du.distcode', $checkdata['distcode'])
            ->where('du.statusflag', 'Y')
            ->where('uc.statusflag', 'Y')
            ->where('du.reservelist', 'Y')
            ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))
            // ->where('du.chargeassigned', 'Y')
            // ->where('du.auditorflag', 'Y')
            ->orderBy('dd.desigcode', 'asc')
            ->groupBy('dd.desigcode', 'dd.desigelname', 'dd.desigtlname', 'du.deptuserid', 'du.username', 'du.usertamilname', 'dept.deptesname', 'dept.depttsname')
            ->select('du.deptuserid', 'du.username', 'du.usertamilname', 'dd.desigelname', 'dd.desigtlname', 'dept.deptesname', 'dept.depttsname')
            ->get();

        // Query to get distinct designations with count
        $designationCounts = DB::table(self::$userdetail_table.' as du')
            ->join(self::$userchargedetail_table.' as uc', 'uc.userid', '=', 'du.deptuserid')
            ->join(self::$chargedetail_table.' as cd', 'cd.chargeid', '=', 'uc.chargeid')
            ->join(self::$rolemapping_table.' as rm', 'rm.rolemappingid', '=', 'cd.rolemappingid')
            ->join(self::$designation_table.' as dd', 'dd.desigcode', '=', 'du.desigcode')
            ->join(self::$deptartment_table.' as dept', 'dept.deptcode', '=', 'du.deptcode')
            ->where('du.deptcode', $checkdata['deptcode'])
            ->where('du.distcode', $checkdata['distcode'])
            ->where('du.statusflag', 'Y')
            // ->where('du.chargeassigned', 'Y')
            // ->where('du.auditorflag', 'Y')
            ->where('uc.statusflag', 'Y')
            ->where('du.reservelist', 'Y')
            ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))
            ->groupBy('dd.desigcode', 'dd.desigelname', 'dd.desigtlname')
            ->orderBy('dd.desigcode', 'asc')
            ->select('dd.desigcode', 'dd.desigelname', 'dd.desigtlname', DB::raw('COUNT(du.deptuserid) as count'))
            ->get();

        $inst_det = DB::table(self::$institution_table.' as inst')
            ->join(self::$deptartment_table.' as dept', 'dept.deptcode', '=', 'inst.deptcode')
            ->join(self::$mstauditeeinscategory_table.' as cat', 'cat.catcode', '=', 'inst.catcode')
            ->where('inst.deptcode', $checkdata['deptcode'])
            ->where('inst.distcode', $checkdata['distcode'])
            ->where('inst.statusflag', 'Y')
            ->whereColumn('inst.audit_quarter', 'dept.currentquarter')
            ->select(
                'inst.instid',
                'inst.instename',
                'inst.insttname',
                'cat.catename',
                'cat.cattname',
            )
            ->get();

        // Return both results
        return [
            'users' => $users,
            'designation_counts' => $designationCounts,
            'inst_det' => $inst_det,
        ];
    }

    public static function createauditschedule_dropdown($userid, $auditplanid)
    {
        $table = self::$auditplan_table;

        return DB::table($table)
            ->join('audit.mst_institution as ai', 'ai.instid', '=', 'auditplan.instid')
            ->join('audit.auditplanteam as at', 'at.auditplanteamid', '=', 'auditplan.auditteamid')
            ->join('audit.auditplanteammember as atm', 'atm.auditplanteamid', '=', 'auditplan.auditteamid')
            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'atm.userid')
            ->join('audit.deptuserdetails as du', 'atm.userid', '=', 'du.deptuserid')
            ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
            ->select(
                'ai.instename',
                'ai.insttname',
                'ai.instid',
                'ai.mandays',
                'ai.instid',
                'ai.catcode',
                'ai.deptcode',
                'de.desigelname',
                'de.desigtlname',
                'auditplan.auditteamid',
                'auditplan.auditplanid',
                'at.auditplanteamid',
                'atm.userid',
                'uc.userchargeid',
                'du.username',
                'cd.chargedescription',
                'auditplan.auditquartercode',
                DB::raw('(
            SELECT COUNT(*)
            FROM audit.auditplanteammember AS sub_atm
            WHERE sub_atm.auditplanteamid = auditplan.auditteamid
        ) AS team_member_count')
            )
            ->where('auditplan.auditplanid', '=', $auditplanid)  // Use the decrypted or plain auditplanid
            ->where('atm.userid', '=', $userid)
            ->where('auditplan.statusflag', '=', 'F')
            ->get();
    }

    public static function audit_members($planid)
    {
        $table = self::$auditplan_table;

        return DB::table($table)
            ->join(self::$auditplanteam_table.' as at', 'at.auditplanteamid', '=', $table.'.auditteamid')
            ->join(self::$auditplanteammem_table.' as atm', 'atm.auditplanteamid', '=', $table.'.auditteamid')
            ->join(self::$userdetail_table.' as du', function ($join) {
                $join
                    ->on('du.deptuserid', '=', 'atm.userid')
                    ->where('atm.teamhead', '=', 'N')  // Filter for team members
                    ->where('atm.statusflag', '=', 'Y');  // Filter for team members
            })
            // ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
            // ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join(self::$designation_table.' as de', 'de.desigcode', '=', 'du.desigcode')
            ->where($table.'.statusflag', '=', 'F')
            ->where($table.'.auditplanid', '=', $planid)
            /*->where('auditplan.auditteamid', function ($query) {
        $query->select('auditteamid')
            ->from('audit.auditplan')
            ->whereColumn('auditteamid', 'auditplan.auditteamid')
            ->where('statusflag', 'F')
            ->limit(1); // Ensure only one value is returned
    })*/
            ->select(
                $table.'.auditteamid',
                // 'uc.userchargeid',
                $table.'.auditplanid',
                // 'cd.chargedescription',
                'de.desigelname',
                'de.desigtlname',
                'du.username',
                'du.usertamilname',
                'du.deptuserid',
                'atm.teamhead',
                'atm.userid'
            )
            ->orderBy('du.desigcode', 'asc')  // Order by desigcode
            ->orderBy('du.deptuserid', 'asc')  // Order by userid in descending order
            ->get();
    }

    public static function fetchAllScheduleData($deptcode)
    {
        $table = self::$instauditschedule_table;

        $user = session('user');
        $userId = $user->userid ?? null;

        $yearSelectedQuery = DB::table('audit.yearcode_mapping as yrmap')
            ->join('audit.mst_auditperiod as d', DB::raw('CAST(yrmap.yearselected AS INTEGER)'), '=', 'd.auditperiodid')
            ->select(
                'yrmap.auditplanid',
                DB::raw("STRING_AGG(DISTINCT d.fromyear || '-' || d.toyear, ', ') as yearselected")
            )
            ->where('yrmap.statusflag', 'Y')
            ->where('yrmap.financestatus', 'N')
            ->groupBy('yrmap.auditplanid')
            ->get();

        $auditscheduleIdsSubquery = DB::table(self::$instauditschedulemem_table)
            ->select('auditscheduleid')
            ->where('userid', $userId)
            ->whereIn('statusflag', ['Y', 'C', 'R', 'S']);

        $mainQuery = DB::table($table)
            ->join('audit.auditplan as ap', $table.'.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$institution_table.' as mi', 'mi.instid', '=', 'ap.instid')
            ->join(self::$deptartment_table.' as dept', 'dept.deptcode', '=', 'mi.deptcode')
            ->join(self::$instauditschedulemem_table.' as at', function ($join) use ($table) {
                $join
                    ->on('at.auditscheduleid', '=', $table.'.auditscheduleid')
                    ->whereIn('at.statusflag', ['Y', 'C', 'R', 'S']);  // Apply the status flag condition here
            })
            // ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'at.userid')
            ->join(self::$userdetail_table.' as du', 'at.userid', '=', 'du.deptuserid')
            // ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join(self::$designation_table.' as de', 'de.desigcode', '=', 'du.desigcode')
            ->select(
                $table.'.auditscheduleid',
                $table.'.fromdate',
                $table.'.todate',
                $table.'.rcno',
                $table.'.statusflag',
                'mi.instename',
                'mi.insttname',
                'mi.mandays',
                'ap.auditplanid',
                DB::raw("
                    MIN(
                        CASE
                            WHEN at.auditteamhead = 'Y' AND at.statusflag IN ('Y', 'C','R','S') THEN du.username::text
                            ELSE NULL
                        END
                    ) AS teamhead
                "),
                DB::raw("
                    MIN(
                        CASE
                            WHEN at.auditteamhead = 'Y' AND at.statusflag IN ('Y', 'C','R','S') THEN du.usertamilname::text
                            ELSE NULL
                        END
                    ) AS teamheadtamil
                "),
                DB::raw("
                    STRING_AGG(
                        CASE
                            WHEN at.auditteamhead = 'N' AND at.statusflag IN ('Y', 'C','R','S') THEN du.username::text
                            ELSE NULL
                        END, ', ' ORDER BY du.desigcode ASC, du.deptuserid ASC
                    ) AS teammembers
                "),
                DB::raw("
                STRING_AGG(
                    CASE
                        WHEN at.auditteamhead = 'N' AND at.statusflag IN ('Y', 'C','R','S') THEN du.usertamilname::text
                        ELSE NULL
                    END, ', ' ORDER BY du.desigcode ASC, du.deptuserid ASC
                ) AS teammemberstamil
                "),
                DB::raw("
                COUNT(
                    CASE
                        WHEN at.statusflag IN ('Y', 'C','R','S') THEN 1
                        ELSE NULL
                    END
                ) AS team_count
            ")
            )
            ->where(function ($query) use ($table) {
                $query
                    ->where($table.'.statusflag', '=', 'Y')
                    ->orWhere($table.'.statusflag', '=', 'F')
                    ->orWhere($table.'.statusflag', '=', 'C')
                    ->orWhere($table.'.statusflag', '=', 'S')
                    ->orWhere($table.'.statusflag', '=', 'R');
            })
            ->where('mi.deptcode', $deptcode)
            // ->whereColumn('ap.auditquartercode', 'dept.currentquarter')
            ->whereColumn('ap.planmappingid', 'dept.planmappingid')
            ->whereIn($table.'.auditscheduleid', $auditscheduleIdsSubquery)
            ->groupBy(
                $table.'.auditscheduleid',
                $table.'.fromdate',
                $table.'.todate',
                $table.'.rcno',
                $table.'.statusflag',
                'mi.instename',
                'mi.insttname',
                'mi.mandays',
                'ap.auditplanid'
            )
            ->orderByRaw("CASE {$table}.statusflag WHEN 'Y' THEN 0 ELSE 1 END")
            ->get();

        // Get the main query results
        $mainResults = $mainQuery;

        // Get the yearselected values separately
        $yearSelectedResults = $yearSelectedQuery->keyBy('auditplanid');  // Use auditplanid as key

        // Merge the yearselected values into the main query results
        foreach ($mainResults as $result) {
            // Check if yearselected exists for this auditplanid
            if (isset($yearSelectedResults[$result->auditplanid])) {
                $result->yearselected = $yearSelectedResults[$result->auditplanid]->yearselected;
            } else {
                $result->yearselected = null;  // Or some default value if not found
            }
        }

        // Now $mainResults contains both the main data and the merged yearselected values
        return $mainResults;
    }

    public static function createIfNotExistsOrUpdateAuditSchedule(array $data, $currentScheduleid, $userid)
    {
        try {
            // If no conflicts, proceed with create or update
            if (! empty($currentScheduleid)) {
                $AlreadyExists = DB::table(self::$instauditschedule_table)
                    ->where('auditplanid', $data['auditplanid'])
                    ->where('auditscheduleid', '!=', $currentScheduleid)
                    ->whereNotIn('statusflag', ['C', 'R', 'S', 'N'])  // Exclude rows where status is either 'C' or 'R'
                    ->exists();
                if ($AlreadyExists) {
                    throw new \Exception('Audit already scheduled');
                }
                $existingUser = DB::table(self::$instauditschedule_table)
                    ->where('auditscheduleid', $currentScheduleid)
                    ->first();

                if ($existingUser) {
                    $data['updatedby'] = $userid;
                    $data['updatedon'] = View::shared('get_nowtime');

                    $yearsselected = $data['yearselected'];

                    $annadhanam_yearselected = $data['annadhanam_yearselected'];

                    unset($data['yearselected']);

                    unset($data['annadhanam_yearselected']);

                    DB::table(self::$instauditschedule_table)
                        ->where('auditscheduleid', $currentScheduleid)
                        ->update($data);

                    if ($yearsselected) {
                        $auditplanid = $existingUser->auditplanid;
                        $existingMappingarr = YearcodeMapping::fetchYearmapById($auditplanid, 'N');
                        $yearSelectedArr = $existingMappingarr->pluck('yearselected')->toarray();
                        $findNewArr = array_diff($yearsselected, $yearSelectedArr);
                        $RemoveExistArr = array_diff($yearSelectedArr, $yearsselected);

                        if (count($findNewArr) > 0) {
                            self::updateyearcodemapping($findNewArr, $auditplanid, '', 'N');
                        }

                        if (count($RemoveExistArr) > 0) {
                            self::updateyearcodemapping($RemoveExistArr, $auditplanid, 'Updatestatusflag', 'N');
                        }
                    }

                    if ($annadhanam_yearselected) {
                        $auditplanid = $existingUser->auditplanid;
                        $existingMappingarr = YearcodeMapping::fetchYearmapById($auditplanid, 'Y');
                        $yearSelectedArr = $existingMappingarr->pluck('yearselected')->toarray();
                        $findNewArr = array_diff($annadhanam_yearselected, $yearSelectedArr);
                        $RemoveExistArr = array_diff($yearSelectedArr, $annadhanam_yearselected);

                        if (count($findNewArr) > 0) {
                            self::updateyearcodemapping($findNewArr, $auditplanid, '', 'Y');
                        }

                        if (count($RemoveExistArr) > 0) {
                            self::updateyearcodemapping($RemoveExistArr, $auditplanid, 'Updatestatusflag', 'Y');
                        }
                    }

                    // Optionally fetch the updated record if needed
                    $updatedUser = DB::table(self::$instauditschedule_table)->where('auditscheduleid', $currentScheduleid)->first();

                    return $updatedUser;
                } else {
                    throw new \Exception("Record not found with ID: $currentScheduleid");
                }
            } else {
                $data['createdby'] = $userid;

                $data['createdon'] = View::shared('get_nowtime');

                $auditplanid = $data['auditplanid'];
                $yeararr = $data['yearselected'];
                $annadhanam_yearselected = $data['annadhanam_yearselected'];

                unset($data['yearselected']);
                unset($data['annadhanam_yearselected']);
                self::updateyearcodemapping($yeararr, $auditplanid, '', 'N');
                self::updateyearcodemapping($annadhanam_yearselected, $auditplanid, '', 'Y');

                $AlreadyExists = DB::table(self::$instauditschedule_table)
                    ->where('auditplanid', $data['auditplanid'])
                    ->whereNotIn('statusflag', ['C', 'R', 'S', 'N'])  // Exclude rows where status is either 'C' or 'R'
                    ->exists();

                if ($AlreadyExists) {
                    throw new \Exception('Audit already scheduled');
                } else {
                    return DB::table(self::$instauditschedule_table)->insertGetId($data, 'auditscheduleid');
                }
            }
        } catch (\Exception $e) {
            // Throwing a custom exception with the message from the model
            throw new \Exception($e->getMessage());
        }
    }

    // public static function DatecheckAuditschedule($userId, $fromDate, $toDate, $parallelinst)
    // {
    //     $session = session('charge');
    //     $deptcode = $session->deptcode;
    //     $quarterdetails = self::getquarterdetails($deptcode);
    //     $currentquarter = $quarterdetails[0]->auditquartercode;

    //     //    $fetchexitmeet = DB::table(self::$instauditschedule_table . ' as ist')
    //     //                        ->join(self::$instauditschedulemem_table . ' as ism', 'ism.auditscheduleid', '=', 'ist.auditscheduleid')
    //     //                        ->where('ism.userid', $userId)
    //     //                        ->select('ist.exitmeetdate')
    //     //                        ->first();

    //     // updated on 06-06-2025 by krishnaveni

    //     $fetchexitmeet = DB::table(self::$instauditschedule_table . ' as ist')
    //         ->join(self::$instauditschedulemem_table . ' as ism', 'ism.auditscheduleid', '=', 'ist.auditscheduleid')
    //         ->join(self::$auditplan_table . ' as plan', 'ist.auditplanid', '=', 'plan.auditplanid')
    //         ->where('ism.userid', $userId)
    //         ->where('ism.statusflag', 'Y')
    //         ->where('plan.auditquartercode', $currentquarter)
    //         ->whereNotNull('ist.exitmeetdate')
    //         ->where('ist.statusflag', 'F')
    //         ->select('ist.exitmeetdate')
    //         ->orderby('ist.exitmeetdate', 'desc')
    //         ->first();

    //     $hasExitMeetDate = $fetchexitmeet && !empty($fetchexitmeet->exitmeetdate);

    //     if ($hasExitMeetDate) {
    //         $table = DB::table(self::$instauditschedulemem_table . ' as ism')
    //             ->join(self::$instauditschedule_table . ' as ist', 'ism.auditscheduleid', '=', 'ist.auditscheduleid')
    //             ->join(self::$auditplan_table . ' as plan', 'ist.auditplanid', '=', 'plan.auditplanid')
    //             //   ->where('plan.auditquartercode','Q2 ')
    //             ->where('ism.userid', $userId)
    //             ->where('ism.statusflag', 'Y')
    //             ->whereIn('ist.statusflag', ['F', 'Y'])
    //             ->where('plan.auditquartercode', $currentquarter)
    //             ->whereNotIn('ist.auditscheduleid', function ($query) use ($userId) {
    //                 $query
    //                     ->select('lc.auditscheduleid')
    //                     ->from('audit.logothertrans_scheduledel as lc')
    //                     ->join('audit.inst_auditschedule as ins', 'ins.auditscheduleid', '=', 'lc.auditscheduleid')
    //                     ->join('audit.inst_schteammember as insch', function ($join) {
    //                         $join
    //                             ->on('insch.auditscheduleid', '=', 'lc.auditscheduleid')
    //                             ->on('lc.touserid', '=', 'insch.userid')
    //                             ->where('insch.statusflag', 'Y');
    //                     })
    //                     ->where('lc.touserid', $userId)
    //                     ->where('lc.datatransfertypecode', 'AH');
    //             });
    //     } else {
    //         $table = DB::table(self::$instauditschedulemem_table . ' as ism')
    //             ->join(self::$instauditschedule_table . ' as ist', 'ism.auditscheduleid', '=', 'ist.auditscheduleid')
    //             ->join(self::$auditplan_table . ' as plan', 'ist.auditplanid', '=', 'plan.auditplanid')
    //             ->where('ism.userid', $userId)
    //             ->where('ism.statusflag', 'Y')
    //             ->whereIn('ist.statusflag', ['F', 'Y'])
    //             ->where('plan.auditquartercode', $currentquarter)
    //             ->whereNotIn('ism.auditscheduleid', function ($query) use ($userId) {
    //                 $query
    //                     ->select('lc.auditscheduleid')
    //                     ->from('audit.logothertrans_scheduledel as lc')
    //                     ->join('audit.inst_auditschedule as ins', 'ins.auditscheduleid', '=', 'lc.auditscheduleid')
    //                     ->join('audit.inst_schteammember as insch', function ($join) {
    //                         $join
    //                             ->on('insch.auditscheduleid', '=', 'lc.auditscheduleid')
    //                             ->on('lc.touserid', '=', 'insch.userid')
    //                             ->where('insch.statusflag', 'Y');
    //                     })
    //                     ->where('lc.touserid', $userId)
    //                     ->where('lc.datatransfertypecode', 'AH');
    //             });
    //     }

    //     if ($parallelinst == 'N') {
    //         $table->whereNotIn('plan.auditplanid', function ($query) use ($userId) {
    //             $query
    //                 ->select(DB::raw("(jsonb_array_elements_text(ltp.auditplanid->'plan_ids'))::int"))
    //                 ->from('audit.logothertrans_plandel as ltp')
    //                 ->where('ltp.touserid', $userId);
    //         });
    //     }

    //     $table->where(function ($query) use ($hasExitMeetDate, $fromDate, $toDate) {
    //         if ($hasExitMeetDate) {
    //             $query->where(function ($q) use ($fromDate, $toDate) {
    //                 $q
    //                     ->where(function ($sub) use ($fromDate, $toDate) {
    //                         $sub
    //                             ->whereBetween('ism.auditfromdate', [$fromDate, $toDate])
    //                             ->orWhereBetween('ism.audittodate', [$fromDate, $toDate])
    //                             ->orWhere(function ($sub2) use ($fromDate, $toDate) {
    //                                 $sub2
    //                                     ->where('ism.auditfromdate', '<=', $fromDate)
    //                                     ->where('ism.audittodate', '>=', $toDate);
    //                             });
    //                     })
    //                     ->where(function ($sub) use ($fromDate) {  // Block if exitmeetdate is NULL or not before new fromDate
    //                         $sub
    //                             ->whereNull('ist.exitmeetdate')
    //                             ->orWhere('ist.exitmeetdate', '>=', $fromDate);
    //                     });
    //             });
    //         } else {
    //             $query
    //                 ->whereBetween('ism.auditfromdate', [$fromDate, $toDate])
    //                 ->orWhereBetween('ism.audittodate', [$fromDate, $toDate])
    //                 ->orWhere(function ($sub) use ($fromDate, $toDate) {
    //                     $sub
    //                         ->where('ism.auditfromdate', '<=', $fromDate)
    //                         ->where('ism.audittodate', '>=', $toDate);
    //                 });
    //         }
    //     });

    //     return $table;
    // }

    public static function DatecheckAuditschedule($userId, $fromDate, $toDate, $parallelinst, $planmappingid)
    {
        $session = session('charge');
        $deptcode = $session->deptcode;

        $fetchexitmeet = DB::table(self::$instauditschedule_table.' as ist')
            ->join(self::$instauditschedulemem_table.' as ism', 'ism.auditscheduleid', '=', 'ist.auditscheduleid')
            ->join(self::$auditplan_table.' as plan', 'ist.auditplanid', '=', 'plan.auditplanid')
            ->join(self::$institution_table.' as inst', 'inst.instid', '=', 'plan.instid')
            ->join(self::$deptartment_table.' as dept', 'dept.deptcode', '=', 'inst.deptcode')
            ->where('ism.userid', $userId)
            ->where('ism.statusflag', 'Y')
            ->whereColumn('plan.planmappingid', 'dept.planmappingid')
            ->whereNotNull('ist.exitmeetdate')
            ->where('ist.statusflag', 'F')
            ->select('ist.exitmeetdate')
            ->orderby('ist.exitmeetdate', 'desc')
            ->first();

        $hasExitMeetDate = $fetchexitmeet && ! empty($fetchexitmeet->exitmeetdate);

        if ($hasExitMeetDate) {
            $table = DB::table(self::$instauditschedulemem_table.' as ism')
                ->join(self::$instauditschedule_table.' as ist', 'ism.auditscheduleid', '=', 'ist.auditscheduleid')
                ->join(self::$auditplan_table.' as plan', 'ist.auditplanid', '=', 'plan.auditplanid')
                ->join(self::$institution_table.' as inst', 'inst.instid', '=', 'plan.instid')
                ->join(self::$deptartment_table.' as dept', 'dept.deptcode', '=', 'inst.deptcode')
                //   ->where('plan.auditquartercode','Q2 ')
                ->where('ism.userid', $userId)
                ->where('ism.statusflag', 'Y')
                ->whereIn('ist.statusflag', ['F', 'Y'])
                ->whereColumn('plan.planmappingid', 'dept.planmappingid')
                ->whereNotIn('ist.auditscheduleid', function ($query) use ($userId) {
                    $query
                        ->select('lc.auditscheduleid')
                        ->from('audit.logothertrans_scheduledel as lc')
                        ->join('audit.inst_auditschedule as ins', 'ins.auditscheduleid', '=', 'lc.auditscheduleid')
                        ->join('audit.inst_schteammember as insch', function ($join) {
                            $join
                                ->on('insch.auditscheduleid', '=', 'lc.auditscheduleid')
                                ->on('lc.touserid', '=', 'insch.userid')
                                ->where('insch.statusflag', 'Y');
                        })
                        ->where('lc.touserid', $userId)
                        ->where('lc.datatransfertypecode', 'AH');
                });
        } else {
            $table = DB::table(self::$instauditschedulemem_table.' as ism')
                ->join(self::$instauditschedule_table.' as ist', 'ism.auditscheduleid', '=', 'ist.auditscheduleid')
                ->join(self::$auditplan_table.' as plan', 'ist.auditplanid', '=', 'plan.auditplanid')
                ->join(self::$institution_table.' as inst', 'inst.instid', '=', 'plan.instid')
                ->join(self::$deptartment_table.' as dept', 'dept.deptcode', '=', 'inst.deptcode')
                ->where('ism.userid', $userId)
                ->where('ism.statusflag', 'Y')
                ->whereIn('ist.statusflag', ['F', 'Y'])
                ->whereColumn('plan.planmappingid', 'dept.planmappingid')
                ->whereNotIn('ism.auditscheduleid', function ($query) use ($userId) {
                    $query
                        ->select('lc.auditscheduleid')
                        ->from('audit.logothertrans_scheduledel as lc')
                        ->join('audit.inst_auditschedule as ins', 'ins.auditscheduleid', '=', 'lc.auditscheduleid')
                        ->join('audit.inst_schteammember as insch', function ($join) {
                            $join
                                ->on('insch.auditscheduleid', '=', 'lc.auditscheduleid')
                                ->on('lc.touserid', '=', 'insch.userid')
                                ->where('insch.statusflag', 'Y');
                        })
                        ->where('lc.touserid', $userId)
                        ->where('lc.datatransfertypecode', 'AH');
                });
        }

        if ($parallelinst == 'N') {
            $table->whereNotIn('plan.auditplanid', function ($query) use ($userId) {
                $query
                    ->select(DB::raw("(jsonb_array_elements_text(ltp.auditplanid->'plan_ids'))::int"))
                    ->from('audit.logothertrans_plandel as ltp')
                    ->where('ltp.touserid', $userId);
            });
        }

        $table->where(function ($query) use ($hasExitMeetDate, $fromDate, $toDate) {
            if ($hasExitMeetDate) {
                $query->where(function ($q) use ($fromDate, $toDate) {
                    $q
                        ->where(function ($sub) use ($fromDate, $toDate) {
                            $sub
                                ->whereBetween('ism.auditfromdate', [$fromDate, $toDate])
                                ->orWhereBetween('ism.audittodate', [$fromDate, $toDate])
                                ->orWhere(function ($sub2) use ($fromDate, $toDate) {
                                    $sub2
                                        ->where('ism.auditfromdate', '<=', $fromDate)
                                        ->where('ism.audittodate', '>=', $toDate);
                                });
                        })
                        ->where(function ($sub) use ($fromDate) {  // Block if exitmeetdate is NULL or not before new fromDate
                            $sub
                                ->whereNull('ist.exitmeetdate')
                                ->orWhere('ist.exitmeetdate', '>=', $fromDate);
                        });
                });
            } else {
                $query
                    ->whereBetween('ism.auditfromdate', [$fromDate, $toDate])
                    ->orWhereBetween('ism.audittodate', [$fromDate, $toDate])
                    ->orWhere(function ($sub) use ($fromDate, $toDate) {
                        $sub
                            ->where('ism.auditfromdate', '<=', $fromDate)
                            ->where('ism.audittodate', '>=', $toDate);
                    });
            }
        });

        return $table;
    }

    public static function fetchsingle_scheduledata($auditscheduleid)
    {
        // print_r($auditscheduleid);
        $table = self::$instauditschedule_table;

        return DB::table($table)
            ->join(self::$auditplan_table.' as ap', $table.'.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$institution_table.' as mi', 'mi.instid', '=', 'ap.instid')
            ->join(self::$instauditschedulemem_table.' as at', function ($join) use ($table) {
                $join
                    ->on('at.auditscheduleid', '=', $table.'.auditscheduleid')
                    ->where('at.auditteamhead', '=', 'Y');
            })
            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'at.userid')
            ->join('audit.deptuserdetails as du', 'at.userid', '=', 'du.deptuserid')
            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
            ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->leftJoin('audit.inst_schteammember as sub_atm', function ($join) use ($table) {
                $join
                    ->on('sub_atm.auditscheduleid', '=', $table.'.auditscheduleid')
                    ->where('sub_atm.auditteamhead', '=', 'N');
            })
            ->leftJoin('audit.yearcode_mapping as yrmap', function ($join) {
                $join
                    ->on('yrmap.auditplanid', '=', 'inst_auditschedule.auditplanid')
                    ->where('yrmap.statusflag', '=', 'Y');
            })
            ->leftJoin('audit.deptuserdetails as sub_du', 'sub_atm.userid', '=', 'sub_du.deptuserid')
            ->select(
                $table.'.auditscheduleid',
                $table.'.fromdate',
                $table.'.todate',
                $table.'.rcno',
                'mi.instename',
                'mi.insttname',
                'mi.instid',
                'mi.mandays',
                'at.auditscheduleid',
                'at.userid as team_head_userid',
                'du.username as team_head_name_en',
                'du.username as team_head_name_ta',
                'cd.chargedescription',
                'teammembers.userid as team_member_userid',
                'teammembers.username as team_member_name',
                'cd.chargedescription',
                'de.desigelname',
                DB::raw("STRING_AGG(yrmap.yearselected::text, ',' ORDER BY yrmap.yearselected)
                FILTER (WHERE yrmap.financestatus = 'N') as yearselected"),
                DB::raw("STRING_AGG(yrmap.yearselected::text, ',' ORDER BY yrmap.yearselected)
                FILTER (WHERE yrmap.financestatus = 'Y') as annadhanam_yearselected"),
                DB::raw("(
                SELECT COUNT(*)
                FROM audit.auditplanteammember as sub_atm
                WHERE sub_atm.auditplanteamid = ap.auditteamid AND sub_atm.statusflag ='Y'

            ) as total_team_count")
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
                $table.'.auditscheduleid'
            )
            ->where(function ($query) use ($table) {
                $query->whereIn($table.'.statusflag', ['Y', 'C', 'R', 'S']);
                // ->orWhere('inst_auditschedule.statusflag', '=', 'N');
            })
            ->where($table.'.auditscheduleid', '=', $auditscheduleid)
            ->groupBy(
                $table.'.fromdate',
                $table.'.todate',
                $table.'.rcno',
            )
            ->groupBy('inst_auditschedule.auditscheduleid')
            ->groupBy('mi.instename')
            ->groupBy('mi.insttname')
            ->groupBy('mi.instid')
            ->groupBy('at.auditscheduleid')
            ->groupBy('at.userid')
            ->groupBy('du.username')
            ->groupBy('cd.chargedescription')
            ->groupBy('teammembers.userid')
            ->groupBy('teammembers.username')
            ->groupBy('de.desigelname')
            ->groupBy('ap.auditteamid')
            ->get();
    }

    public static function fetchteamMembers($auditscheduleid)
    {
        $teammember = DB::table(self::$instauditschedulemem_table)
            ->where(self::$instauditschedulemem_table.'.auditscheduleid', '=', $auditscheduleid)
            ->get();  // Use get() to return all matching records

        // Return the team members
        return $teammember;
    }

    public static function updateAuditScheduleMem($membersToRemove, $audit_scheduleid, $memberId, $teamhead, $status, $userid)
    {
        return DB::table(self::$instauditschedulemem_table)
            ->whereIn('userid', $membersToRemove)
            ->where('auditscheduleid', $audit_scheduleid)
            ->where('userid', $memberId)
            ->where('auditteamhead', $teamhead)
            ->update(['statusflag' => $status, 'updatedby' => $userid, 'updatedon' => View::shared('get_nowtime')]);
    }

    public static function insertAuditScheduleMem(
        $audit_scheduleid,
        $memberId,
        $fromdate,
        $todate,
        $teamhead,
        $userid,
    ) {
        return DB::table(self::$instauditschedulemem_table)->insert([
            'auditscheduleid' => $audit_scheduleid,
            'userid' => $memberId,
            'auditteamhead' => $teamhead,
            'auditfromdate' => $fromdate,
            'audittodate' => $todate,
            'diarystatus' => 'N',
            'statusflag' => 'Y',
            'createdby' => $userid,
            'updatedby' => $userid,
            'createdon' => View::shared('get_nowtime'),
            'updatedon' => View::shared('get_nowtime'),
            // other fields as necessary
        ]);
    }

    public static function update_teamstatus($statusflag, $auditscheduleid, $fromdate, $todate)
    {
        $sessiondet = session('user');
        $sessionuserid = $sessiondet->userid;

        $query = DB::table(self::$instauditschedulemem_table)
            ->where('auditscheduleid', $auditscheduleid)
            ->whereNot('statusflag', 'N')
            ->update([
                'statusflag' => $statusflag,
                'auditfromdate' => $fromdate,
                'audittodate' => $todate,
                'diarystatus' => 'N',
                'updatedon' => View::shared('get_nowtime'),
                'updatedby' => $sessionuserid,
            ]);

        return $query;
    }

    // public static function get_prfauditDetails($auditplanid)
    // {
    //     $query = DB::table(self::$auditplan_table . ' as plan')
    //         ->join(self::$praudit_instmap_table . ' as prfmap', function ($join) {
    //             $join
    //                 ->on('prfmap.instid', '=', 'plan.instid')
    //                 ->on('prfmap.finyearcode', '=', 'plan.financialyearcode')
    //                 ->on('prfmap.quartercode', '=', 'plan.auditquartercode');
    //         })
    //         ->join(self::$mst_prfauditTitle_table . ' as prf', 'prf.praudittitleid', '=', 'prfmap.praudittitleid')
    //         ->Join(self::$fileuploaddetail_table . ' as fu', 'fu.fileuploadid', '=', 'prf.fileuploadid')
    //         ->select(
    //             DB::raw("
    //             COALESCE(
    //                 STRING_AGG(
    //                     CASE
    //                         WHEN fu.fileuploadid IS NOT NULL THEN
    //                             CONCAT(fu.filename, '-', fu.filepath, '-', fu.filesize, '-', fu.fileuploadid)
    //                         ELSE '-'
    //                     END,
    //                     ',' ORDER BY fu.fileuploadid
    //                 ),
    //             '-') AS filedetails
    //         "),
    //             'prf.titleename',
    //             'prf.titletname'
    //         )
    //         ->where('plan.auditplanid', $auditplanid)
    //         ->groupBy(
    //             'prf.titleename',
    //             'prf.titletname'
    //         );
    //     return $query->get();
    // }

    public static function get_prfauditDetails($auditplanid)
    {
        $query = DB::table(self::$auditplan_table.' as plan')
            // ->join(self::$praudit_instmap_table . ' as prfmap', function ($join) {
            //     $join->on('prfmap.instid', '=', 'plan.instid')
            //         ->on('prfmap.finyearcode', '=', 'plan.financialyearcode')
            //         ->on('prfmap.quartercode', '=', 'plan.auditquartercode');
            // })
            ->join(self::$praudit_instmap_table.' as prfmap', 'prfmap.planmappingid', '=', 'prfmap.planmappingid')
            ->join(self::$mst_prfauditTitle_table.' as prf', 'prf.praudittitleid', '=', 'prfmap.praudittitleid')
            ->Join(self::$fileuploaddetail_table.' as fu', 'fu.fileuploadid', '=', 'prf.fileuploadid')
            ->select(
                DB::raw("
                COALESCE(
                    STRING_AGG(
                        CASE
                            WHEN fu.fileuploadid IS NOT NULL THEN
                                CONCAT(fu.filename, '-', fu.filepath, '-', fu.filesize, '-', fu.fileuploadid)
                            ELSE '-'
                        END,
                        ',' ORDER BY fu.fileuploadid
                    ),
                '-') AS filedetails
            "),
                'prf.titleename',
                'prf.titletname'
            )
            ->where('plan.auditplanid', $auditplanid)
            ->groupBy(
                'prf.titleename',
                'prf.titletname'
            );

        return $query->get();
    }

    public static function updateRcno($deptcode, $incrementedRcno)
    {
        return DB::table(self::$deptartment_table)
            ->where('deptcode', $deptcode)
            ->update(['rcno' => $incrementedRcno]);
    }

    public static function getLastinsertedId($data)
    {
        $existingRecord = DB::table(self::$instauditschedule_table)
            ->where('statusflag', 'Y')
            ->orWhere('statusflag', 'F')
            ->first();

        if ($existingRecord) {
            $id = DB::table(self::$instauditschedule_table)
                ->insertGetId($data, 'auditscheduleid');
        } else {
            $id = null;
        }

        return $id;
    }

    public static function fetch_auditscheduledetails($userid, $planmappingid)
    {
        $table = self::$instauditschedule_table;

        return DB::table($table)
            ->join(self::$auditplan_table.' as ap', $table.'.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$institution_table.' as mi', 'mi.instid', '=', 'ap.instid')
            ->join(self::$instauditschedulemem_table.' as at', function ($join) use ($table) {
                $join
                    ->on('at.auditscheduleid', '=', $table.'.auditscheduleid')
                    ->where('at.auditteamhead', '=', 'Y');
            })
            ->join(self::$auditplanteam_table.' as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            // ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'at.userid')
            ->join(self::$userdetail_table.' as du', 'at.userid', '=', 'du.deptuserid')
            // ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join(self::$yearcodemapping_table.' as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
            ->join(
                self::$auditperiod_table.' as period',
                DB::raw('CAST(yrmap.yearselected AS INTEGER)'),
                '=',
                'period.auditperiodid'
            )
            ->select(
                $table.'.auditscheduleid',
                $table.'.fromdate',
                $table.'.todate',
                $table.'.rcno',
                $table.'.statusflag',
                $table.'.auditeeresponse',
                $table.'.auditeeresponsedt',
                $table.'.auditeeproposeddate',
                $table.'.auditeeremarks',
                $table.'.nodalname',
                $table.'.nodalmobile',
                $table.'.nodalemail',
                $table.'.nodaldesignation',
                $table.'.workallocationflag',
                'ap.auditplanid',
                'apt.teamname',
                'mi.instename',
                'mi.insttname',
                'mi.mandays',
                'at.auditscheduleid',
                'at.userid',
                'du.username',
                // 'cd.chargedescription',
                DB::raw("STRING_AGG(DISTINCT period.fromyear || '-' || period.toyear, ', ')
                FILTER (WHERE period.financestatus = 'N') as yearname"),
                // DB::raw('STRING_AGG(DISTINCT period.fromyear || \'-\' || period.toyear, \', \') as yearname'),
                DB::raw('(
    SELECT COUNT(*)
    FROM '.self::$instauditschedulemem_table.' as sub_atm
    WHERE sub_atm.auditscheduleid = '.$table.".auditscheduleid
    AND (sub_atm.statusflag =  'Y' )
    AND sub_atm.auditteamhead = 'N'
) AS team_member_count")
            )
            ->groupBy(
                $table.'.auditscheduleid',
                $table.'.fromdate',
                $table.'.todate',
                $table.'.rcno',
                $table.'.statusflag',
                $table.'.auditeeresponse',
                $table.'.auditeeresponsedt',
                $table.'.auditeeproposeddate',
                $table.'.auditeeremarks',
                $table.'.nodalname',
                $table.'.nodalmobile',
                $table.'.nodalemail',
                $table.'.nodaldesignation',
                $table.'.workallocationflag',
                'ap.auditplanid',
                'apt.teamname',
                'mi.instename',
                'mi.insttname',
                'mi.mandays',
                'at.auditscheduleid',
                'at.userid',
                'du.username',
                // 'cd.chargedescription',
            )
            // ->where(function ($query) {
            //     $query->where('inst_auditschedule.statusflag', '=', 'F')
            //         ->whereNotNull('inst_auditschedule.auditeeresponse') // Check auditeeresponse is not null
            //         ->where('inst_auditschedule.auditeeresponse', '!=', '');
            // })
            ->where('at.userid', $userid)
            ->where('inst_auditschedule.statusflag', '=', 'F')
            // ->when($quartercode, function ($query) use ($quartercode) {
            //     $query->where('ap.auditquartercode', $quartercode);
            // })
            ->when($planmappingid, function ($query) use ($planmappingid) {
                $query->where('ap.planmappingid', $planmappingid);
            })
            //  ->whereNotNull('inst_auditschedule.auditeeresponse')
            // ->where('inst_auditschedule.auditeeresponse', '!=', '')
            ->where('yrmap.statusflag', 'Y')
            ->get();
    }

    public static function fetch_Accountaccepteddetails($auditscheduleid)
    {
        $table = self::$instauditschedule_table;

        $session = session('charge');

        $spilloverdet = DB::table($table.' as s')
            ->join(self::$auditplan_table.' as ap', 's.auditplanid', '=', 'ap.auditplanid')
            ->where('s.auditscheduleid', $auditscheduleid)
            ->select('ap.spilloverflag', 'ap.instid')
            ->get();

        $instid = $spilloverdet[0]->instid;
        $spilloverFlag = $spilloverdet[0]->spilloverflag;
        //  return  $spilloverFlag;

        $query = DB::table($table)
            ->join(self::$transaccountdetails_table.' as ad', 'ad.auditscheduleid', '=', $table.'.auditscheduleid')
            ->join(self::$accountparticulars_table.' as map', 'map.accountparticularsid', '=', 'ad.accountcode')
            ->join(self::$auditplan_table.' as ap', $table.'.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$auditplanteam_table.' as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->join(self::$institution_table.' as mi', 'mi.instid', '=', 'ap.instid')
            ->leftJoin(self::$fileuploaddetail_table.' as fu', 'fu.fileuploadid', '=', 'ad.fileuploadid')
            ->select(
                DB::raw("
                STRING_AGG(
                    CASE
                        WHEN ad.fileuploadid != 0 THEN CONCAT(fu.filename, '-', fu.filepath, '-', fu.filesize, '-', fu.fileuploadid)
                        ELSE '-'
                    END,
                    ',' ORDER BY fu.fileuploadid
                ) AS filedetails
            "),
                $table.'.auditscheduleid',
                $table.'.nodalname',
                $table.'.nodalmobile',
                $table.'.nodaldesignation',
                $table.'.nodalemail',
                $table.'.auditeeremarks',
                'ad.accountcode',
                'ad.fileuploadid',
                'ad.remarks',
                'map.accountparticularsename',
                'map.accountparticularstname',
                'map.accountparticularsid'
            )
            ->groupBy(
                $table.'.auditscheduleid',
                $table.'.nodalname',
                $table.'.nodalmobile',
                $table.'.nodaldesignation',
                $table.'.nodalemail',
                $table.'.auditeeremarks',
                'ad.accountcode',
                'ad.fileuploadid',
                'ad.remarks',
                'map.accountparticularsename',
                'map.accountparticularstname',
                'map.accountparticularsid'
            )
            ->where($table.'.statusflag', '=', 'F')
            ->whereNotNull($table.'.auditeeresponse');
        if ($spilloverFlag === 'Y') {
            $query->whereIn($table.'.auditscheduleid', function ($subquery) use ($instid, $table) {
                $subquery
                    ->select('auditscheduleid')
                    ->from(self::$auditplan_table.' as p')
                    ->join($table.' as sc', 'sc.auditplanid', '=', 'p.auditplanid')
                    ->where('p.instid', $instid);
            });
        } else {
            $query->where($table.'.auditscheduleid', '=', $auditscheduleid);
        }

        return $query
            ->orderBy('map.accountparticularsename', 'desc')
            ->get();

        // $querySql = $query->toSql();
        // $bindings = $query->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );

        // print_r($finalQuery);
        // exit;
    }

    public static function fetch_cfraccepteddetails($auditscheduleid)
    {
        $table = self::$instauditschedule_table;
        $session = session('charge');
        $spilloverdet = DB::table($table.' as s')
            ->join(self::$auditplan_table.' as ap', 's.auditplanid', '=', 'ap.auditplanid')
            ->where('s.auditscheduleid', $auditscheduleid)
            ->select('ap.spilloverflag', 'ap.instid')
            ->get();

        $instid = $spilloverdet[0]->instid;
        $spilloverFlag = $spilloverdet[0]->spilloverflag;
        $query = DB::table($table)
            ->join(self::$transcallforrec_table.' as cfr', 'cfr.auditscheduleid', '=', $table.'.auditscheduleid')
            // ->join('audit.mst_subworkallocationtype as msw', 'msw.subworkallocationtypeid', '=', 'cfr.subtypecode')
            // ->join('audit.mst_majorworkallocationtype as mmw', 'mmw.majorworkallocationtypeid', '=', 'msw.majorworkallocationtypeid')
            ->join(self::$callforrec_table.' as cfra', 'cfra.callforrecordsid', '=', 'cfr.subtypecode')
            ->join(self::$auditplan_table.' as ap', $table.'.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$auditplanteam_table.' as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->join(self::$institution_table.' as mi', 'mi.instid', '=', 'ap.instid')
            ->select(
                $table.'.auditscheduleid',
                $table.'.nodalname',
                $table.'.nodalmobile',
                $table.'.nodaldesignation',
                $table.'.nodalemail',
                $table.'.auditeeremarks',
                'cfr.subtypecode',
                'cfr.remarks as cfr_remarks',
                'cfr.replystatus',
                'cfra.callforrecordsename',
                'cfra.callforrecordstname',
                'cfra.callforrecordsid',
            )
            ->where($table.'.statusflag', '=', 'F')
            //  ->where('cfr.auditscheduleid', '=', $auditscheduleid)
            ->whereNotNull($table.'.auditeeresponse');
        if ($spilloverFlag === 'Y') {
            $query->whereIn($table.'.auditscheduleid', function ($subquery) use ($instid, $table) {
                $subquery
                    ->select('auditscheduleid')
                    ->from(self::$auditplan_table.' as p')
                    ->join($table.' as sc', 'sc.auditplanid', '=', 'p.auditplanid')
                    ->where('p.instid', $instid);
            });
        } else {
            $query->where($table.'.auditscheduleid', '=', $auditscheduleid);
        }
        $query->orderBy('cfra.callforrecordsename', 'desc');

        return $query->get();
    }

    public static function CFRStoreData($audit_scheduleid, $JsonData)
    {
        try {
            $record = StoreCFR::create([
                'auditscheduleid' => $audit_scheduleid,
                'selected_cfr' => $JsonData,
                'statusflag' => 'Y',
            ]);

            return $record ? true : false;
        } catch (\Exception $e) {
            // Optionally log error if needed
            \Log::error('CFRStoreData Error: '.$e->getMessage());

            return false;
        }
    }

    public static function getCurrentQuarter($deptcode, $quartercode)
    {
        // return DB::table(self::$deptartment_table . ' as msd')
        //     ->join(self::$auditquarter_table . ' as maq', 'maq.auditquartercode', '=', 'msd.currentquarter')
        //     ->select('maq.quarterfrom', 'maq.quarterto')
        //     ->where('maq.deptcode', $deptcode)
        //     /// ->where('maq.auditquartercode',$quartercode)
        //     ->first();
        return DB::table(self::$auditquarter_table.' as maq')
            ->select('maq.quarterfrom', 'maq.quarterto')
            ->where('maq.auditquartercode', $quartercode)
            ->where('maq.deptcode', $deptcode)
            ->first();
    }

    public static function Selected_CFR($audit_scheduleid)
    {
        // Fetch the record for the given audit_scheduleid
        $Selected_CFR = StoreCFR::where('auditscheduleid', $audit_scheduleid)
            ->where('statusflag', 'Y')
            ->first();

        // Check if the record exists
        if (! $Selected_CFR) {
            // Return or handle error if no record is found
            return response()->json(['error' => 'No record found'], 404);
        }

        // Decode the selected_cfr JSON field
        $JsonDecode_CFR = json_decode($Selected_CFR->selected_cfr, true);

        // Check if JSON is valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Handle invalid JSON error
            return response()->json(['error' => 'Invalid JSON data'], 500);
        }

        // If there are no CFR values in the decoded JSON
        if (empty($JsonDecode_CFR)) {
            return response()->json(['error' => 'No CFR values found in JSON'], 404);
        }

        // Fetch all records for the selected CFR values in a single query using whereIn
        $records = DB::table('audit.callforrecords_auditee as cfra')
            ->select('callforrecordsid', 'callforrecordsename', 'callforrecordstname')
            ->whereIn('cfra.callforrecordsid', $JsonDecode_CFR)  // Use whereIn to fetch multiple records
            ->get();  // Fetch all matching records in a single query

        // Initialize an empty array to hold the final result
        $CallforRecords = [];
        // Loop through all fetched records and add them to the final array
        foreach ($records as $record) {
            $CallforRecords[] = [
                'callforrecordsid' => $record->callforrecordsid,
                'callforrecordsename' => $record->callforrecordsename,
                'callforrecordstname' => $record->callforrecordstname,
            ];
        }

        return $CallforRecords;
    }

    /*  public static function CancelSchedule($scheduleid, $remarksdata)
    {
        // Update the 'statusflag' column to 'C' for the specific schedule
        $Instschedule = DB::table(self::$instauditschedule_table)
                           ->where('auditscheduleid', $scheduleid)
                           ->update(['statusflag' => 'C', 'cancel_remarks' => $remarksdata]);

        $InstscheduleMem = DB::table(self::$instauditschedulemem_table)
                             ->where('auditscheduleid', $scheduleid)
                             ->update(['statusflag' => 'C']);

        return $Instschedule;

        // You can also include other fields that need to be updated.
    }*/

    public static function CancelorReSchedule($data)
    {
        if ($data['statusflag'] == 'R') {
            $Instschedule = DB::table(self::$instauditschedule_table)
                ->where('auditscheduleid', $data['auditscheduleid'])
                ->first();  // Use first() to get a single row of data

            DB::table('audit.auditschedule_history')->insert([
                'auditscheduleid' => $Instschedule->auditscheduleid,
                'auditplanid' => $Instschedule->auditplanid,
                'fromdate' => $Instschedule->fromdate,
                'todate' => $Instschedule->todate,
                'entrymeetdate' => $Instschedule->entrymeetdate,
                'exitmeetdate' => $Instschedule->exitmeetdate,
                'auditeeresponse' => $Instschedule->auditeeresponse,
                'auditeeresponsedt' => $Instschedule->auditeeresponsedt,
                'auditeeremarks' => $Instschedule->auditeeremarks,
                'auditeeproposeddate' => $Instschedule->auditeeproposeddate,
                'auditorresponse' => $Instschedule->auditorresponse,
                'rcno' => $Instschedule->rcno,
                'statusflag' => $Instschedule->statusflag,
                'createdon' => $Instschedule->createdon,
                'createdby' => $Instschedule->createdby,
                'updatedby' => $Instschedule->updatedby,
                'updatedon' => $Instschedule->updatedon,
                'nodalname' => $Instschedule->nodalname,
                'nodalmobile' => $Instschedule->nodalmobile,
                'nodalemail' => $Instschedule->nodalemail,
                'nodaldesignation' => $Instschedule->nodaldesignation,
                'workallocationflag' => $Instschedule->workallocationflag,
                'remarks' => $Instschedule->remarks,
                'history_createdon' => $data['updatedon'],
                'history_createdby' => $data['updatedby'],  // Timestamp when the record was created in history
            ]);
        }

        $InstscheduleUpdate = DB::table(self::$instauditschedule_table)
            ->where('auditscheduleid', $data['auditscheduleid'])
            ->update([
                'statusflag' => $data['statusflag'],
                'remarks' => $data['remarks'],
                'updatedby' => $data['updatedby'],
                'updatedon' => $data['updatedon'],
            ]);

        $InstscheduleMemUpdate = DB::table(self::$instauditschedulemem_table)
            ->where('auditscheduleid', $data['auditscheduleid'])
            ->update([
                'statusflag' => $data['statusflag'],
                'updatedby' => $data['updatedby'],
                'updatedon' => $data['updatedon'],
            ]);

        if ($InstscheduleUpdate && $InstscheduleMemUpdate) {
            return response()->json([
                'message' => 'Audit schedule updated successfully, and data stored in history.',
                'status' => true,
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to update audit schedule.',
                'status' => false,
            ]);
        }
    }

    public static function checkrandomizedWA($auditscheduleid)
    {
        return DB::table(self::$instauditschedule_table)
            ->where('auditscheduleid', $auditscheduleid)
            ->where('statusflag', 'F')
            ->where('auditeeresponse', 'A')
            ->select('workallocationflag')
            ->get();
    }

    public static function sent_intimation($audit_scheduleid)
    {
        echo $audit_scheduleid;
        $institutions = DB::table('audit.inst_auditschedule as ins')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ins.auditplanid')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->where('ins.auditscheduleid', $audit_scheduleid)
            ->select('inst.instename', 'inst.mobile', 'ins.fromdate')
            ->get();

        $instename = $institutions[0]->instename;
        $mobileNumber = $institutions[0]->mobile;
        $fromdate = $institutions[0]->fromdate;

        $data = ['instename' => $instename,
            'mobileNumber' => $mobileNumber,
            'fromdate' => $fromdate];

        // $otp    =   rand(1000,9999);

        $mobileNumber = '8148958988';
        $response = $this->smsService->sendSms($mobileNumber, '', 'sent_initimation', $data);

        //  print_r($response);
    }

    public static function UserMatchCheck($auditplanid)
    {
        $InstidGet = DB::table('audit.auditplan as ap')
            ->select('ap.instid', 'ap.modifiedplan', 'ap.auditplanid')
            ->where('ap.auditplanid', $auditplanid)
            ->where('ap.statusflag', 'F')
            ->first();
        // return $InstidGet;
        if ($InstidGet->modifiedplan == 'Y') {
            return 'success';
        } else {
            /* Team Member Check */
            $MapinstUsercountGet = DB::table('audit.auditplan as ap')
                ->where('ap.auditplanid', $auditplanid)
                ->select('ap.teamsize')
                ->get();
            $MapinstUsercountGet = $MapinstUsercountGet[0]->teamsize;

            $auditplan = DB::table('audit.auditplan as ap')
                ->join(self::$auditplanteam_table.' as at', 'at.auditplanteamid', '=', 'ap.auditteamid')
                ->join(self::$auditplanteammem_table.' as atm', 'atm.auditplanteamid', '=', 'ap.auditteamid')
                // ->where('atm.teamhead', '=', 'N')
                ->where('atm.statusflag', '=', 'Y')
                ->where('ap.auditplanid', $auditplanid)
                ->count();

            if ($MapinstUsercountGet != $auditplan) {
                $response = 'insufficient_user_count';

                return $response;
            }

            // /**Team Head Check */
            // $MapinstHeadcountGet = DB::table('audit.map_instdesig as mid')
            //     ->where('mid.instid', $InstidGet->instid)
            //     ->where('mid.teamhead', 'Y')
            //     ->count();

            // $auditplan_Head = DB::table('audit.auditplan as ap')
            //     ->join(self::$auditplanteam_table . ' as at', 'at.auditplanteamid', '=', 'ap.auditteamid')
            //     ->join(self::$auditplanteammem_table . ' as atm', 'atm.auditplanteamid', '=', 'ap.auditteamid')
            //     ->where('atm.teamhead', '=', 'Y')
            //     ->where('atm.statusflag', '=', 'Y')
            //     ->where('ap.auditplanid', $auditplanid)
            //     ->count(); // Filter for team members

            // if ($MapinstHeadcountGet !== $auditplan_Head) {
            //     $response = 'insufficient_head_count';
            //     return $response;
            // }
        }
    }

    public static function fetchInstitutionData($deptcode = null, $regioncode = null, $distcode = null)
    {
        $auditplanTable = self::$auditplan_table;

        $query = DB::table("$auditplanTable as ap")
            ->join('audit.auditplanteam as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ap.instid')
            ->join('audit.auditplanteammember as aptm', 'aptm.auditplanteamid', '=', 'ap.auditteamid')
            ->join('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'aptm.userid')
            ->leftJoin('audit.inst_auditschedule as ias', function ($join) {
                $join
                    ->on('ias.auditplanid', '=', 'ap.auditplanid')
                    ->whereNull('ias.entrymeetdate')
                    ->WhereIn('ias.workallocationflag', ['N', null])
                    // ->WhereIn('ias.workallocationflag',['N',null])
                    ->whereIn('ias.statusflag', ['Y', 'F']);
                //  ->whereNull('ias.auditeeresponse');
            });
        // ->whereNull('ias.auditeeresponse');

        if (! empty($deptcode)) {
            $query->where('ins.deptcode', $deptcode);
        }
        if (! empty($regioncode)) {
            $query->where('ins.regioncode', $regioncode);
        }
        if (! empty($distcode)) {
            $query->where('ins.distcode', $distcode);
        }

        $query->whereNotExists(function ($subquery) {
            $subquery
                ->select(DB::raw(1))
                ->from('audit.inst_auditschedule as ias2')
                ->whereRaw('ias2.auditplanid = ap.auditplanid')
                ->where(function ($cond) {
                    $cond
                        ->whereNotNull('ias2.entrymeetdate')
                        ->orWhereNotIn('ias2.workallocationflag', ['N', ''])
                        ->orWhereNotIn('ias2.statusflag', ['Y', 'F']);
                });
        });
        $query
            ->select(
                'ap.auditplanid',
                'ins.instename',
                'ins.insttname',
            )
            ->groupBy(
                'ap.auditplanid',
                // 'ins.instid',
                'ins.instename',
                'ins.insttname'
            );

        return $query->get();
    }

    public static function fetchTeamData($deptcode = null, $regioncode = null, $distcode = null, $auditteamid = null, $instid = null, $teamHeadNames = [], $teamMemberNames = [])
    {
        $auditplanTable = self::$auditplan_table;

        $scheduledauditors = DB::table("$auditplanTable as ap")
            ->join('audit.auditplanteam as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            // ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ap.instid')
            // ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ins.regioncode')
            // ->join('audit.mst_district as dist', 'dist.distcode', '=', 'ins.distcode')
            // ->join('audit.mst_dept as d', 'd.deptcode', '=', 'ins.deptcode')
            ->join('audit.auditplanteammember as aptm', 'aptm.auditplanteamid', '=', 'ap.auditteamid')
            ->join('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'aptm.userid')
            ->join(self::$designation_table.' as desig', 'dp.desigcode', '=', 'desig.desigcode')
            ->select(
                'ap.auditteamid',
                'dp.deptuserid',
                DB::raw("STRING_AGG(dp.username || ' - ' || desig.desigelname, ', ') FILTER (WHERE aptm.teamhead = 'Y' and aptm.statusflag = 'Y') AS teamhead"),
                DB::raw("STRING_AGG(dp.username || ' - ' || desig.desigelname, ', ') FILTER (WHERE aptm.teamhead = 'N' and aptm.statusflag = 'Y') AS members")
            )
            ->where('ap.auditplanid', $auditteamid)
            ->groupBy('ap.auditteamid', 'dp.deptuserid')
            ->get();
        // return $query;

        // // // Start the base query
        // $query = DB::table("$auditplanTable as ap")
        //     ->join('audit.auditplanteam as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
        //     ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ap.instid')
        //     ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ins.regioncode')
        //     ->join('audit.mst_district as dist', 'dist.distcode', '=', 'ins.distcode')
        //     ->join('audit.mst_dept as d', 'd.deptcode', '=', 'ins.deptcode')
        //     ->join('audit.auditplanteammember as aptm', 'aptm.auditplanteamid', '=', 'ap.auditteamid')
        //     ->join('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'aptm.userid')

        //     ->whereNotExists(function ($subQuery) {
        //         $subQuery->select(DB::raw(1))
        //             ->from('audit.inst_auditschedule as ias')
        //             ->whereColumn('ias.auditplanid', 'ap.auditplanid');
        //     });

        // // Apply the filters if provided (DeptCode, RegionCode, DistCode, AuditTeamID, InstID)
        // if (!empty($deptcode)) {
        //     $query->where('ins.deptcode', $deptcode);
        // }
        // if (!empty($regioncode)) {
        //     $query->where('ins.regioncode', $regioncode);
        // }
        // if (!empty($distcode)) {
        //     $query->where('ins.distcode', $distcode);
        // }
        // if (!empty($auditteamid)) {
        //     $query->where('ap.auditteamid', $auditteamid);
        // }
        // // if (!empty($instid)) {
        // //     $query->where('ins.auditplanid', $auditteamid);
        // // }

        // // If team head names are provided, filter them
        // if (!empty($teamHeadNames)) {
        //     $query->whereIn('dp.username', $teamHeadNames)
        //         ->where('aptm.teamhead', 'Y'); // Only team heads
        // }

        // // If team member names are provided, filter them
        // if (!empty($teamMemberNames)) {
        //     $query->whereIn('dp.username', $teamMemberNames)
        //         ->where('aptm.teamhead', 'N'); // Only team members
        // }

        // // Return the query with selected fields
        // $scheduledauditors =  $query
        //     ->select(
        //         'ap.auditteamid',
        //         'reg.regionename',
        //         'dist.distename',
        //         'd.deptelname',
        //         'dp.deptuserid',
        //         'ins.instename',
        //         DB::raw("STRING_AGG(dp.username, ', ') FILTER (WHERE aptm.teamhead = 'Y') AS teamhead"),
        //         DB::raw("STRING_AGG(dp.username, ', ') FILTER (WHERE aptm.teamhead = 'N') AS members")
        //     )
        //     ->groupBy(
        //         'ap.auditteamid',
        //         'reg.regionename',
        //         'dist.distename',
        //         'd.deptelname',
        //         'ins.instename',
        //         'dp.deptuserid',
        //     )
        //       ->get();
        // $auditplanid = 2117;

        // $scheduledauditors = DB::select("
        //     SELECT
        //         ap.auditteamid,
        //         STRING_AGG(dp.username, ', ') FILTER (WHERE aptm.teamhead = 'Y') AS teamhead,
        //         STRING_AGG(dp.username || ' - ' || des.desigelname, ', ') FILTER (WHERE aptm.teamhead = 'N') AS members,
        //         dp.deptuserid
        //     FROM audit.auditplan ap
        //     INNER JOIN audit.auditplanteam apt ON apt.auditplanteamid = ap.auditteamid
        //     INNER JOIN audit.auditplanteammember aptm ON aptm.auditplanteamid = ap.auditteamid
        //     INNER JOIN audit.deptuserdetails dp ON dp.deptuserid = aptm.userid
        //     INNER JOIN audit.mst_designation des ON des.desigcode = dp.desigcode
        //     WHERE ap.auditplanid = ?
        //     GROUP BY ap.auditteamid
        // ", [$auditteamid]);

        // print_r($scheduledauditors);

        // // Manually replace the `?` with the bound value
        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $sql),
        //     array_map('addslashes', $bindings)
        // );

        // echo $finalQuery;
        // exit;

        $membercount = DB::table('audit.map_instdesig as mid')
            ->join('audit.auditplan as ap', 'ap.instid', '=', 'mid.instid')
            ->where('ap.auditplanid', $auditteamid)
            ->where('mid.teamhead', 'N')
            ->count();

        //     $querySql = $membercount->toSql();
        // $bindings = $membercount->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );

        // print_r($finalQuery);
        // exit;

        $data['scheduledauditors'] = $scheduledauditors;
        $data['membercount'] = $membercount;

        return $data;
    }

    public static function updateauditplanuser(array $data, $auditteamid = null)
    {
        // print_r($data);
        // exit;

        DB::beginTransaction();

        try {
            $session = session('user');
            $session_userId = $session->userid;

            $auditplanid = $data['auditplanid'];
            $finaliseflag = $data['statusflag'];

            $udpatedauditteamid = DB::table('audit.auditplan')
                ->where('auditplanid', $auditplanid)
                ->value('auditteamid');

            $query = DB::table('audit.audit_teams_draft')->where('auditplanid', $auditplanid);

            if ($auditteamid) {
                $query->where('auditteamsdraftid', '<>', $auditteamid);
            }

            if ($query->exists()) {
                throw new \Exception('Institution already exists for this audit plan.');
            }

            $procceed_forward = false;

            if ($finaliseflag === 'S') {
                DB::table('audit.auditplan')
                    ->where('auditplanid', $auditplanid)
                    ->update(['statusflag' => 'S']);
            }

            if ($auditteamid) {
                $updated = DB::table('audit.audit_teams_draft')
                    ->where('auditteamsdraftid', $auditteamid)
                    ->update($data);

                if ($updated === 0) {
                    throw new \Exception('No records updated.');
                }
                $procceed_forward = true;
            } else {
                $auditteamid = DB::table('audit.audit_teams_draft')
                    ->insertGetId($data, 'auditteamsdraftid');

                if (! $auditteamid) {
                    throw new \Exception('Insert failed for audit team draft.');
                }
                $procceed_forward = true;
            }

            if ($procceed_forward && $finaliseflag === 'F') {
                DB::table('audit.auditplanteammember')
                    ->where('auditplanteamid', $udpatedauditteamid)
                    ->update(['statusflag' => 'N']);

                // Insert team head
                if (! empty($data['newteamhead'])) {
                    DB::table('audit.auditplanteammember')->insert([
                        'auditplanteamid' => $udpatedauditteamid,
                        'userid' => $data['newteamhead'],
                        'teamhead' => 'Y',
                        'statusflag' => 'Y',
                        'createdon' => View::shared('get_nowtime'),
                        'createdby' => $session_userId,
                        'updatedby' => $session_userId,
                        'updatedon' => View::shared('get_nowtime'),
                    ]);
                }

                // Insert team members
                $members = is_array($data['newteammembers'])
                    ? $data['newteammembers']
                    : json_decode($data['newteammembers'], true);

                if (! empty($members) && is_array($members)) {
                    $rows = [];
                    foreach ($members as $userId) {
                        $rows[] = [
                            'auditplanteamid' => $udpatedauditteamid,
                            'userid' => (int) $userId,
                            'teamhead' => 'N',
                            'statusflag' => 'Y',
                            'createdon' => View::shared('get_nowtime'),
                            'createdby' => $session_userId,
                            'updatedby' => $session_userId,
                            'updatedon' => View::shared('get_nowtime'),
                        ];
                    }
                    DB::table('audit.auditplanteammember')->insert($rows);
                }

                DB::table('audit.auditplan')
                    ->where('auditplanid', $auditplanid)
                    ->update(['statusflag' => 'F', 'modifiedplan' => 'Y']);

                $schdExists = DB::table(self::$instauditschedule_table)
                    ->where('auditplanid', $auditplanid)
                    ->whereIn('statusflag', ['Y', 'F'])
                    ->first();

                if ($schdExists) {
                    $auditscheduleid = $schdExists->auditscheduleid;
                    $fromdate = $schdExists->fromdate;
                    $todate = $schdExists->todate;

                    DB::table(self::$instauditschedulemem_table)
                        ->where('auditscheduleid', $auditscheduleid)
                        ->update(['statusflag' => 'N']);

                    // Insert team head
                    if (! empty($data['newteamhead'])) {
                        DB::table(self::$instauditschedulemem_table)->insert([
                            'auditscheduleid' => $auditscheduleid,
                            'userid' => $data['newteamhead'],
                            'auditfromdate' => $fromdate,
                            'audittodate' => $todate,
                            'auditteamhead' => 'Y',
                            'statusflag' => 'Y',
                            'createdon' => View::shared('get_nowtime'),
                            'createdby' => $session_userId,
                            'updatedby' => $session_userId,
                            'updatedon' => View::shared('get_nowtime'),
                        ]);
                    }

                    // Insert team members
                    $members = is_array($data['newteammembers'])
                        ? $data['newteammembers']
                        : json_decode($data['newteammembers'], true);

                    if (! empty($members) && is_array($members)) {
                        $rows = [];
                        foreach ($members as $userId) {
                            $rows[] = [
                                'auditscheduleid' => $auditscheduleid,
                                'userid' => (int) $userId,
                                'auditfromdate' => $fromdate,
                                'audittodate' => $todate,
                                'auditteamhead' => 'N',
                                'statusflag' => 'Y',
                                'createdon' => View::shared('get_nowtime'),
                                'createdby' => $session_userId,
                                'updatedby' => $session_userId,
                                'updatedon' => View::shared('get_nowtime'),
                            ];
                        }
                        DB::table(self::$instauditschedulemem_table)->insert($rows);
                    }

                    DB::table(self::$instauditschedule_table)
                        ->where('auditplanid', $auditplanid)
                        ->update(['statusflag' => 'Y']);
                }

                DB::commit();  // ? COMMIT HERE

                return [
                    'status' => true,
                    'type' => 'finalised',
                    'message' => 'Audit Team finalised successfully.',
                ];
            } elseif ($procceed_forward && $finaliseflag === 'S') {
                DB::commit();  // ? COMMIT HERE

                return [
                    'status' => true,
                    'type' => 'saved',
                    'message' => 'Audit Team draft saved successfully.',
                ];
            } else {
                DB::rollBack();  // ? ROLLBACK if nothing done

                return [
                    'status' => false,
                    'type' => 'error',
                    'message' => 'No operation was completed.',
                ];
            }
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return [
                'status' => false,
                'type' => 'error',
                'message' => 'Update failed: '.$e->getMessage(),
            ];
        }
    }

    public static function getauditors_updateplanuser($deptcode, $regioncode, $distcode, $auditteamid)
    {
        if ($auditteamid) {
            // echo $auditteamid;

            // Step 1: First, retrieve the teamhead_userid and teammember_userid
            // $excludedUserIds = AuditTeamModel::query()
            //     ->join('audit.auditplanteammember as t', 't.auditplanteamid', '=', 'auditplanteam.auditplanteamid')  // Join with the teammember table
            //     ->where('auditplanteam.auditplanteamid', '=', $auditteamid)  // Filter by the specified teamcode
            //     ->where('t.statusflag', '=', 'Y') // Filter by teamcode
            //     ->select('t.userid')  // Select the user IDs
            //     ->get()
            //     ->pluck('userid')  // Get all teamhead_userid values
            //     ->merge(
            //         AuditTeamModel::query()
            //             ->join('audit.auditplanteammember as t', 't.auditplanteamid', '=', 'auditplanteam.auditplanteamid')  // Join again with the teammember table
            //             ->where('auditplanteam.auditplanteamid', '=', $auditteamid)  // Filter by the specified teamcode
            //             ->where('t.statusflag', '=', 'Y') // Filter by teamcode
            //             ->select('t.userid')  // Select the user IDs
            //             ->get()
            //             ->pluck('userid')  // Get all teammember_userid values
            //     );
            // // Step 2: Then, use the excluded user IDs in your original query to filter out them
            // $auditors = UserChargeDetailsModel::query()
            //     ->join('audit.deptuserdetails as du', 'userchargedetails.userid', '=', 'du.deptuserid')
            //     ->join('audit.chargedetails as cd', 'userchargedetails.chargeid', '=', 'cd.chargeid')
            //     ->join('audit.mst_district as md', 'md.distcode', '=', 'cd.distcode')
            //     ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
            //     ->select(
            //         'du.deptuserid',
            //         'du.username',
            //         'cd.chargedescription',
            //         'md.distename',
            //         'md.disttname',
            //         'userchargedetails.userid',
            //         'de.desigelname',
            //         'de.desigtlname'

            //     )
            //     ->where('userchargedetails.statusflag', '=', 'Y')
            //     ->when($distcode !== 'A', function ($query) use ($distcode) {
            //         return $query->where('md.distcode', '=', $distcode);
            //     })
            //     ->whereNotIn('userchargedetails.userid', $excludedUserIds)  // Exclude the userids
            //     ->get();
            // use Illuminate\Support\Facades\DB;

            $excludedIds = DB::table('audit.audit_teams_draft')
                ->where('auditteamsdraftid', $auditteamid)
                ->value('newteammembers');  // This returns a JSON string like '["1261","1434"]'

            $excludedArray = json_decode($excludedIds, true) ?? [];  // Ensure it's an array even if null

            $newTeamHead = DB::table('audit.audit_teams_draft')
                ->where('auditteamsdraftid', $auditteamid)
                ->value('newteamhead');  // Integer

            // Add newteamhead to the array if it's not null
            if (! is_null($newTeamHead)) {
                $excludedArray[] = (string) $newTeamHead;  // Ensure it's a string for consistency
            }

            // Optional: remove duplicates just in case
            $excludedArray = array_unique($excludedArray);

            // Debug
            // print_r($excludedArray);

            $data = DB::table('audit.deptuserdetails as dp')
                ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'dp.deptuserid')
                ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
                ->join('audit.rolemapping as rol', 'rol.rolemappingid', '=', 'cd.rolemappingid')
                ->join('audit.mst_district as md', 'md.distcode', '=', 'dp.distcode')
                ->join('audit.mst_designation as de', 'de.desigcode', '=', 'dp.desigcode')
                ->where('uc.statusflag', '=', 'Y')
                ->where('dp.statusflag', '=', 'Y')
                ->where('dp.deptcode', $deptcode)
                ->where('cd.regioncode', $regioncode)
                ->where('dp.distcode', $distcode)
                ->where('rol.roleactioncode', '04')
                ->when(! empty($excludedArray), function ($query) use ($excludedArray) {
                    $query->whereNotIn('dp.deptuserid', $excludedArray);
                })
                ->select('dp.deptuserid',
                    'dp.username',
                    'cd.chargedescription',
                    'md.distename',
                    'md.disttname',
                    'uc.userid',
                    'de.desigelname',
                    'de.desigtlname')
                ->get();

            return $data;

            // print_r($data);

            // exit;
        } else {
            $data = DB::table('audit.deptuserdetails as dp')
                ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'dp.deptuserid')
                ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
                ->join('audit.rolemapping as rol', 'rol.rolemappingid', '=', 'cd.rolemappingid')
                ->join('audit.mst_designation as de', 'de.desigcode', '=', 'dp.desigcode')
                ->join('audit.mst_district as md', 'md.distcode', '=', 'dp.distcode')
                ->where('uc.statusflag', '=', 'Y')
                ->where('dp.statusflag', '=', 'Y')
                ->where('dp.deptcode', $deptcode)
                ->where('cd.regioncode', $regioncode)
                ->where('dp.distcode', $distcode)
                ->where('rol.roleactioncode', '04')
                ->select(
                    'dp.deptuserid',
                    'dp.username',
                    'cd.chargedescription',
                    'md.distename',
                    'md.disttname',
                    'uc.userid',
                    'de.desigelname',
                    'de.desigtlname'
                )
                ->get();

            return $data;
        }
    }

    public function fetchUpdateuserplanData($auditteamid = null)
    {
        $data = DB::table('audit.audit_teams_draft as atd')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'atd.auditplanid')
            ->join('audit.auditplanteam as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->join('audit.mst_institution as inst', 'ap.instid', '=', 'inst.instid')
            ->join('audit.mst_dept as dept', 'inst.deptcode', '=', 'dept.deptcode')
            ->join('audit.mst_region as region', 'inst.regioncode', '=', 'region.regioncode')
            ->join('audit.mst_district as dist', 'inst.distcode', '=', 'dist.distcode')
            ->join('audit.deptuserdetails as oldhead', 'oldhead.deptuserid', '=', 'atd.oldteamhead')
            ->join('audit.mst_designation as olddes', 'olddes.desigcode', '=', 'oldhead.desigcode')
            ->join('audit.deptuserdetails as newhead', 'newhead.deptuserid', '=', 'atd.newteamhead')
            ->join('audit.mst_designation as newdes', 'newdes.desigcode', '=', 'newhead.desigcode')
            ->leftJoin('audit.fileuploaddetail as fu', 'fu.fileuploadid', '=', 'atd.fileuploadid')
            ->leftJoin(DB::raw("(SELECT mid.instid, COUNT(*) AS membercount
            FROM audit.map_instdesig AS mid
            JOIN audit.auditplan AS ap ON ap.instid = mid.instid
            WHERE mid.teamhead = 'N'
            GROUP BY mid.instid) AS member_counts"), 'member_counts.instid', '=', 'ap.instid')
            ->where('inst.statusflag', 'Y')
            ->where('dept.statusflag', 'Y')
            ->where('region.statusflag', 'Y')
            ->where('dist.statusflag', 'Y');

        if (! empty($auditteamid)) {
            $data->where('atd.auditteamsdraftid', $auditteamid);
        }

        $data = $data
            ->select(
                DB::raw("CASE
            WHEN atd.fileuploadid != 0 THEN CONCAT(fu.filename, ' - ', fu.filepath, ' - ', fu.filesize, ' - ', fu.fileuploadid)
            ELSE ' - '
        END AS filedetails"),
                'atd.*',
                'inst.deptcode',
                'inst.instename',
                'dept.deptelname',
                'region.regionename',
                'inst.regioncode',
                'inst.distcode',
                'ap.auditplanid',
                'auditteamsdraftid',
                'dist.distename',
                DB::raw("oldhead.username || ' - ' || olddes.desigelname || ' - ' || oldhead.deptuserid AS oldteamheadname"),
                DB::raw("newhead.username || ' - ' || newdes.desigelname || ' - ' || newhead.deptuserid AS newteamheadname"),
                'member_counts.membercount',
                // Safely handle the JSONB fields using a CASE WHEN for valid JSONB arrays
                DB::raw("(SELECT string_agg(dud.username || ' - ' || des.desigelname || ' - ' || dud.deptuserid, ', ')
                  FROM jsonb_array_elements_text(CASE
                      WHEN jsonb_typeof(atd.oldteammembers) = 'array' THEN atd.oldteammembers
                      ELSE '[]'::jsonb
                  END) AS memberid
                  JOIN audit.deptuserdetails AS dud ON dud.deptuserid = memberid::int
                  JOIN audit.mst_designation AS des ON des.desigcode = dud.desigcode) AS oldteammembernames"),
                DB::raw("(SELECT string_agg(dud.username || ' - ' || des.desigelname || ' - ' || dud.deptuserid, ', ')
                  FROM jsonb_array_elements_text(CASE
                      WHEN jsonb_typeof(atd.newteammembers) = 'array' THEN atd.newteammembers
                      ELSE '[]'::jsonb
                  END) AS memberid
                  JOIN audit.deptuserdetails AS dud ON dud.deptuserid = memberid::int
                  JOIN audit.mst_designation AS des ON des.desigcode = dud.desigcode) AS newteammembernames")
            )
            ->orderBy('dept.deptelname', 'asc')
            ->get();

        return $data;
    }

    // public static function getAuditorsfromplan($deptcode, $distcode, $quartercode)
    // {
    //     // return $quartercode;
    //     try {
    //         $column = 'inst.' . $quartercode;

    //         if (empty($deptcode)) {
    //             throw new Exception('Deptcode is not available');
    //         }
    //         if (empty($distcode)) {
    //             throw new Exception('distcode is not available');
    //         }
    //         $query = DB::table(self::$auditplan_table . ' as ap')
    //             ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'ap.instid')
    //             ->join(self::$auditplanteam_table . ' as at', 'at.auditplanteamid', '=', 'ap.auditteamid')
    //             ->join(self::$auditplanteammem_table . ' as mem', 'mem.auditplanteamid', '=', 'ap.auditteamid')
    //             ->join(self::$userdetail_table . ' as du', 'mem.userid', '=', 'du.deptuserid')
    //             ->join(self::$designation_table . ' as dd', 'dd.desigcode', '=', 'du.desigcode')
    //             ->select(
    //                 'ap.fromdate',
    //                 'ap.todate',
    //                 'ap.instid',
    //                 'inst.instename',
    //                 'inst.insttname',
    //                 'ap.auditmode',
    //                 'ap.mandays',
    //                 'ap.auditplanid',
    //                 'ap.auditquartercode',
    //                 'inst.spillover',
    //                 'ap.teamsize',
    //                 'inst.remainingmandays',
    //                 'ap.spilloverflag',
    //                 DB::raw("(
    //         SELECT head.usertamilname || ' - ' || desig.desigtlname
    //         FROM audit.auditplanteammember AS head_tm
    //         JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
    //         JOIN audit.mst_designation AS desig ON desig.desigcode = head.desigcode
    //         WHERE head_tm.auditplanteamid = ap.auditteamid AND head_tm.teamhead = 'Y' AND head_tm.statusflag='Y'
    //         LIMIT 1
    //     ) AS team_head_ta"),
    //                 DB::raw("(
    //         SELECT COALESCE(STRING_AGG(member.usertamilname || ' - ' || desig2.desigtlname, ', '), '')
    //         FROM audit.auditplanteammember AS member_tm
    //         JOIN audit.deptuserdetails AS member ON member.deptuserid = member_tm.userid
    //         JOIN audit.mst_designation AS desig2 ON desig2.desigcode = member.desigcode
    //         WHERE member_tm.auditplanteamid = ap.auditteamid AND member_tm.teamhead != 'Y' AND member_tm.statusflag='Y'
    //     ) AS team_members_ta"),
    //                 DB::raw("(
    //         SELECT head.username || ' - ' || desig.desigelname
    //         FROM audit.auditplanteammember AS head_tm
    //         JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
    //         JOIN audit.mst_designation AS desig ON desig.desigcode = head.desigcode
    //         WHERE head_tm.auditplanteamid = ap.auditteamid AND head_tm.teamhead = 'Y' AND head_tm.statusflag='Y'
    //         LIMIT 1
    //     ) AS team_head_en"),
    //                 DB::raw("(
    //         SELECT COALESCE(STRING_AGG(member.username || ' - ' || desig2.desigelname, ', '), '')
    //         FROM audit.auditplanteammember AS member_tm
    //         JOIN audit.deptuserdetails AS member ON member.deptuserid = member_tm.userid
    //         JOIN audit.mst_designation AS desig2 ON desig2.desigcode = member.desigcode
    //         WHERE member_tm.auditplanteamid = ap.auditteamid AND member_tm.teamhead != 'Y' AND member_tm.statusflag='Y'
    //     ) AS team_members_en")
    //             )
    //             ->where('inst.deptcode', $deptcode)
    //             ->where('inst.distcode', $distcode)
    //             ->where('ap.auditquartercode', $quartercode)
    //             ->where('ap.prioritycode', '02')
    //             ->where('mem.statusflag', 'Y')
    //             ->where('ap.statusflag', 'F')
    //             // ->orderBy('ap.auditplanid', 'asc')
    //             // ->orderBy('ap.auditteamid', 'asc')
    //             // ->orderBy('ap.fromdate', 'asc')
    //             ->orderByRaw("
    //                 CASE
    //                     WHEN ap.auditmode = 'P' THEN 0
    //                     ELSE 1
    //                 END
    //             ")
    //             // ->orderBy('ap.auditteamid', 'asc')
    //             ->orderBy('ap.fromdate', 'asc')
    //             ->get();

    //         return $query;
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

    //         \Log::error('SQL Error: ' . $customMessage);
    //         throw new \Exception($e->getMessage(), 500);
    //     } catch (\Exception $e) {
    //         throw new \Exception($e->getMessage(), 409);
    //     }
    // }

    // public static function checkexitmeetstatus($deptcode, $distcode)
    // {
    //     try {
    //         if (empty($deptcode)) {
    //             throw new Exception('Deptcode is not available');
    //         }
    //         if (empty($distcode)) {
    //             throw new Exception('distcode is not available');
    //         }

    //         $quarterdet = MastersModel::getoldandnewquarter($deptcode);

    //         $currentquarter = $quarterdet[0]->currentquarter;

    //         $query = DB::table(self::$institution_table . ' as inst')
    //             ->join(self::$auditplan_table . ' as plan', 'inst.instid', '=', 'plan.instid')
    //             ->join(self::$instauditschedule_table . ' as schd', 'plan.auditplanid', '=', 'schd.auditplanid')
    //             ->where('inst.deptcode', $deptcode)
    //             ->where('inst.distcode', $distcode)
    //             ->where('plan.auditquartercode', $currentquarter)
    //             ->whereNull('schd.exitmeetdate')
    //             ->whereNotNull('schd.entrymeetdate')
    //             ->exists();
    //         // ->where('inst.' . $currentquarter, 'Y')
    //         // ->join(self::$auditplan_table . ' as plan', 'inst.instid', '=', 'plan.instid')
    //         //   ->get();
    //         return $query;
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         $customMessage = 'A database error occurred while finalising. Please contact the administrator.';

    //         \Log::error('SQL Error: ' . $e->getMessage());
    //         throw new \Exception($customMessage, 500);
    //     } catch (\Exception $e) {
    //         // Optionally, you can log the error or handle it accordingly
    //         Log::error('Error in executing finaliseplan_function: ' . $e->getMessage());
    //         throw new \Exception($e->getMessage(), 409);
    //     }
    // }

    public static function getAuditorsfromplan($deptcode, $distcode, $quartercode, $prioritycode)
    {
        try {

            if (empty($deptcode)) {
                throw new Exception('Deptcode is not available');
            }
            if (empty($distcode)) {
                throw new Exception('distcode is not available');
            }
            $query = DB::table(self::$auditplan_table.' as ap')
                ->join(self::$institution_table.' as inst', 'inst.instid', '=', 'ap.instid')
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('audit.auditplanteammember as mem')
                        ->whereColumn('mem.auditplanteamid', 'ap.auditteamid')
                        ->where('mem.statusflag', 'Y');
                })
                ->select(
                    'ap.fromdate',
                    'ap.todate',
                    'ap.instid',
                    'inst.instename',
                    'inst.insttname',
                    'ap.auditmode',
                    'ap.mandays',
                    'ap.auditplanid',
                    'ap.auditquartercode',
                    'inst.spillover',
                    'ap.teamsize',
                    'inst.remainingmandays',
                    'ap.spilloverflag',
                    DB::raw("(
                        SELECT head.usertamilname || ' - ' || desig.desigtlname
                        FROM audit.auditplanteammember AS head_tm
                        JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
                        JOIN audit.mst_designation AS desig ON desig.desigcode = head.desigcode
                        WHERE head_tm.auditplanteamid = ap.auditteamid AND head_tm.teamhead = 'Y' AND head_tm.statusflag='Y'
                        LIMIT 1
                        ) AS team_head_ta"),
                    DB::raw("(
                        SELECT COALESCE(STRING_AGG(member.usertamilname || ' - ' || desig2.desigtlname, ', '), '')
                        FROM audit.auditplanteammember AS member_tm
                        JOIN audit.deptuserdetails AS member ON member.deptuserid = member_tm.userid
                        JOIN audit.mst_designation AS desig2 ON desig2.desigcode = member.desigcode
                        WHERE member_tm.auditplanteamid = ap.auditteamid AND member_tm.teamhead != 'Y' AND member_tm.statusflag='Y'
                        ) AS team_members_ta"),
                    DB::raw("(
                        SELECT head.username || ' - ' || desig.desigelname
                        FROM audit.auditplanteammember AS head_tm
                        JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
                        JOIN audit.mst_designation AS desig ON desig.desigcode = head.desigcode
                        WHERE head_tm.auditplanteamid = ap.auditteamid AND head_tm.teamhead = 'Y' AND head_tm.statusflag='Y'
                        LIMIT 1
                        ) AS team_head_en"),
                    DB::raw("(
                        SELECT COALESCE(STRING_AGG(member.username || ' - ' || desig2.desigelname, ', '), '')
                        FROM audit.auditplanteammember AS member_tm
                        JOIN audit.deptuserdetails AS member ON member.deptuserid = member_tm.userid
                        JOIN audit.mst_designation AS desig2 ON desig2.desigcode = member.desigcode
                        WHERE member_tm.auditplanteamid = ap.auditteamid AND member_tm.teamhead != 'Y' AND member_tm.statusflag='Y'
                        ) AS team_members_en")
                )
                ->where('inst.deptcode', $deptcode)
                ->where('inst.distcode', $distcode)
                ->where('ap.auditquartercode', $quartercode)
                ->where('ap.prioritycode', $prioritycode)

                ->where('ap.statusflag', 'F')

                ->orderByRaw("
                    CASE
                    WHEN ap.auditmode = 'P' THEN 0
                    ELSE 1
                    END
                    ")

                ->orderBy('ap.fromdate', 'asc')
                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching finalized plan details. Please contact the administrator.';
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function checkexitmeetstatus($deptcode, $distcode, $plan_config_details)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception('Deptcode is not available');
            }
            if (empty($distcode)) {
                throw new Exception('distcode is not available');
            }

            $prev_quatdetails = collect(
                CommonModel::getplandetailsWithPrev($deptcode)
            )
                ->where('quarter_type', 'ForQuarter')
                ->values()
                ->toArray();
            $planmappingid = $prev_quatdetails[0]->planmappingid;

            $query = DB::table(self::$institution_table.' as inst')
                ->join(self::$auditplan_table.' as plan', 'inst.instid', '=', 'plan.instid')
                ->join(self::$instauditschedule_table.' as schd', 'plan.auditplanid', '=', 'schd.auditplanid')
                ->where('inst.deptcode', $deptcode)
                ->where('inst.distcode', $distcode)
                ->where('plan.planmappingid', $planmappingid)
                ->whereNull('schd.exitmeetdate')
                ->whereNotNull('schd.entrymeetdate')
                ->exists();

            // ->where('inst.' . $currentquarter, 'Y')
            // ->join(self::$auditplan_table . ' as plan', 'inst.instid', '=', 'plan.instid')
            // ->get();
            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while finalising. Please contact the administrator.';

            // \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            // Optionally, you can log the error or handle it accordingly
            // Log::error('Error in executing finaliseplan_function: ' . $e->getMessage());
            throw new \Exception($e->getMessage(), 409);
        }
    }

    // public static function checkTemplateAudit($deptcode, $distcode)
    // {
    //     try {
    //         if (empty($deptcode)) {
    //             throw new Exception('Deptcode is not available');
    //         }

    //         if (empty($distcode)) {
    //             throw new Exception('Distcode is not available');
    //         }

    //         $quarterdet = MastersModel::getoldandnewquarter($deptcode);
    //         if (empty($quarterdet) || empty($quarterdet[0]->currentquarter)) {
    //             throw new Exception('Unable to determine current quarter');
    //         }

    //         $currentquarter = $quarterdet[0]->currentquarter;

    //         $isFinalized = DB::table('audit.templateauditplan as tp')
    //             ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'tp.instid')
    //             // ->join(self::$auditplan_table . ' as plan', 'inst.instid', '=', 'plan.instid')
    //             // ->join(self::$instauditschedule_table . ' as schd', 'plan.auditplanid', '=', 'schd.auditplanid')
    //             ->where('inst.deptcode', $deptcode)
    //             ->where('inst.distcode', $distcode)
    //             ->where('tp.auditquartercode', $currentquarter)
    //             ->where('tp.statusflag', 'F')
    //             ->whereNotNull('tp.startdate')  // Start date exists
    //             ->whereNull('tp.enddate')  // End date not entered
    //             ->exists();

    //         return $isFinalized;
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         Log::error('SQL Error in checkTemplateAudit: ' . $e->getMessage());
    //         throw new \Exception(
    //             'A database error occurred while checking template audit status.',
    //             500
    //         );
    //     } catch (\Exception $e) {
    //         Log::error('Error in checkTemplateAudit: ' . $e->getMessage());
    //         throw new \Exception($e->getMessage(), 409);
    //     }
    // }

    public static function checkTemplateAudit($deptcode, $distcode)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception('Deptcode is not available');
            }

            if (empty($distcode)) {
                throw new Exception('Distcode is not available');
            }

            $quarterdet = TemplateAudit::getquarterdetails($deptcode);

            // $currentquarter = $quarterdet[0]->currentquarter;

            $isFinalized = DB::table('audit.templateauditplan as tp')
                ->join(self::$institution_table.' as inst', 'inst.instid', '=', 'tp.instid')
                ->join(self::$auditplan_table.' as plan', 'inst.instid', '=', 'plan.instid')
                ->join(self::$instauditschedule_table.' as schd', 'plan.auditplanid', '=', 'schd.auditplanid')
                ->where('inst.deptcode', $deptcode)
                ->where('inst.distcode', $distcode)
                // ->where('tp.auditquartercode', $currentquarter)
                ->where('tp.statusflag', 'F')
                ->whereNotNull('tp.startdate')
                ->whereNull('tp.enddate')
                ->exists();

            return $isFinalized;
        } catch (\Illuminate\Database\QueryException $e) {
            throw new \Exception(
                'A database error occurred while checking template audit status.',
                500
            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function checkperformanceAudit($deptcode, $distcode)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception('Deptcode is not available');
            }
            if (empty($distcode)) {
                throw new Exception('distcode is not available');
            }

            $prev_quatdetails = collect(
                CommonModel::getplandetailsWithPrev($deptcode)
            )
                ->where('quarter_type', 'ForQuarter')
                ->values()
                ->toArray();
            $planmappingid = $prev_quatdetails[0]->planmappingid;

            $isFinalized = $query = DB::table(self::$institution_table.' as mi')
                ->join(self::$auditplan_table.' as ap', 'ap.instid', '=', 'mi.instid')
                ->join(self::$instauditschedule_table.' as schd', 'ap.auditplanid', '=', 'schd.auditplanid')
                ->join(self::$deptartment_table.' as d', 'd.deptcode', '=', 'mi.deptcode')
                ->join(self::$instauditschedulemem_table.' as stm', 'stm.auditscheduleid', '=', 'schd.auditscheduleid')
                ->where('mi.statusflag', 'Y')
                ->where('ap.auditmode', 'P')
                ->where('schd.statusflag', 'F')
                ->where('stm.statusflag', 'Y')
                ->where('mi.deptcode', $deptcode)
                ->where('mi.distcode', $distcode)
                ->where('ap.planmappingid', $planmappingid)
                ->whereNotNull('schd.entrymeetdate')
                ->where(function ($q) {
                    $q
                        ->whereExists(function ($sub) {
                            $sub
                                ->select(DB::raw(1))
                                ->from('audit.praudit_transpara as p')
                                ->whereColumn('p.auditscheduleid', 'stm.auditscheduleid')
                                ->whereColumn('p.schteammemberid', 'stm.userid')
                                ->where('p.statusflag', 'E');
                        })
                        ->orWhereNotExists(function ($sub) {
                            $sub
                                ->select(DB::raw(1))
                                ->from('audit.praudit_transpara as p')
                                ->whereColumn('p.auditscheduleid', 'stm.auditscheduleid')
                                ->whereColumn('p.schteammemberid', 'stm.userid');
                        });
                })
                ->orderBy('d.deptesname')
                ->orderBy('mi.instename')
                ->exists();

            return $isFinalized;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while finalising. Please contact the administrator.';

            // \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            // Optionally, you can log the error or handle it accordingly
            // Log::error('Error in executing finaliseplan_function: ' . $e->getMessage());
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function backupOldYearMapping($auditplanid)
    {
        $oldData = DB::table('audit.yearcode_mapping')
            ->where('auditplanid', $auditplanid)
            ->where('statusflag', 'Y')
            ->get();

        if ($oldData->isEmpty()) {
            return;
        }

        $yearselectedCombined = [];

        foreach ($oldData as $item) {
            $years = array_map('strval', explode(',', trim($item->yearselected, '{}')));
            $yearselectedCombined = array_merge($yearselectedCombined, $years);
        }

        $yearselectedCombined = array_values(array_unique($yearselectedCombined));

        $first = $oldData->first();

        DB::table('audit.yearcode_mappinghistory')->insert([
            'auditplanid' => $auditplanid,
            'createdby' => $first->createdby,
            'createdon' => $first->createdon,
            'updatedon' => $first->updatedon,
            'yearselected' => json_encode($yearselectedCombined),
            'statusflag' => $first->statusflag,
            'financestatus' => $first->financestatus,
        ]);
    }

    public static function changerequests_insertupdate(array $data, $auditplanid, $table)
    {
        try {
            self::backupOldYearMapping($auditplanid);

            $yeararr = array_unique($data['yearselected'] ?? []);

            $existingMappingarr = YearcodeMapping::fetchYearmapById($auditplanid, 'N');
            $yearSelectedArr = $existingMappingarr->pluck('yearselected')->toArray();

            $findNewArr = array_diff($yeararr, $yearSelectedArr);
            $removeExistArr = array_diff($yearSelectedArr, $yeararr);

            $annyeararr = array_unique($data['financestatus'] ?? []);

            $existingMappingannarr = YearcodeMapping::fetchYearmapById($auditplanid, 'Y');

            $yearSelectedannArr = $existingMappingannarr->pluck('yearselected')->toArray();
            $findNewannArr = array_diff($annyeararr, $yearSelectedannArr);
            $removeExistannArr = array_diff($yearSelectedannArr, $annyeararr);

            if ($data['updatefield'] === '02') {
                $record = DB::table('audit.inst_auditschedule')
                    ->where('auditplanid', $auditplanid)
                    ->select('workallocationflag')
                    ->first();

                if ($record && $record->workallocationflag === 'Y') {
                    throw new \Exception('Workflagvalidation');
                }

                DB::table('audit.inst_auditschedule')
                    ->where('auditplanid', $auditplanid)
                    ->update([
                        'fromdate' => null,
                        'todate' => null,
                        'statusflag' => 'Y',
                        'updatedon' => View::shared('get_nowtime'),
                        'updatedby' => $data['updatedby'],
                    ]);

                DB::table('audit.yearcode_mapping')
                    ->where('auditplanid', $auditplanid)
                    ->update([
                        'yearselected' => null,
                    ]);

                $auditschedule = DB::table('audit.inst_auditschedule')
                    ->where('auditplanid', $auditplanid)
                    ->select('auditscheduleid')
                    ->first();

                if ($auditschedule) {
                    DB::table('audit.selected_cfr')
                        ->where('auditscheduleid', $auditschedule->auditscheduleid)
                        ->update([
                            'statusflag' => 'N',
                            'updatedon' => View::shared('get_nowtime'),
                            'updatedby' => $data['updatedby'],
                        ]);
                }
            }

            $message = null;
            $hasUpdated = false;

            if (count($findNewArr) > 0) {
                self::updateyearcodemappings($findNewArr, $auditplanid, '', 'N');
                $hasUpdated = true;
            }

            if (count($findNewannArr) > 0) {
                self::updateyearcodemappings($findNewannArr, $auditplanid, '', 'Y');
                $hasUpdated = true;

            }

            if (count($removeExistannArr) > 0) {
                self::updateyearcodemappings($removeExistannArr, $auditplanid, 'Updatestatusflag', 'Y');
                $hasUpdated = true;
            }

            if (count($removeExistArr) > 0) {
                self::updateyearcodemappings($removeExistArr, $auditplanid, 'Updatestatusflag', 'N');
                $hasUpdated = true;
            }
            if (! $message && $data['updatefield'] === '02') {
                $message = 'Schedule Period has been reset to null. Assign new dates.';
            } elseif (! $message && $data['updatefield'] === '01') {
                $message = 'Audit year has been updated successfully.';
            }

            return $message;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function updateyearcodemappings(array $data, $currentUserId, $statusflagupdate = '', $financestatus = '')
    {
        //  return $statusflagupdate;

        if ($statusflagupdate == 'Updatestatusflag') {
            //   return 'entered';
            foreach ($data as $YearVal) {
                $yearmapping = YearcodeMapping::where('auditplanid', $currentUserId)
                    ->where('yearselected', $YearVal)
                    ->where('statusflag', 'Y')
                    ->first();
                if ($yearmapping) {
                    YearcodeMapping::where('auditplanid', $currentUserId)
                        ->where('yearselected', $YearVal)
                        ->where('statusflag', 'Y')
                        ->update(['statusflag' => 'N', 'financestatus' => $financestatus]);

                    DB::table('audit.inst_auditschedule')
                        ->where('auditplanid', $currentUserId)
                        ->update(['updatedon' => now()]);
                }
            }
        } else {
            //  return 'add';
            foreach ($data as $YearVal) {
                // return 'asd';
                $yearmapping = YearcodeMapping::where('auditplanid', $currentUserId)
                    ->where('yearselected', $YearVal)
                    ->where('statusflag', 'Y')
                    ->first();
                if ($yearmapping) {
                    $yearmapping->update(['yearselected' => $YearVal, 'financestatus' => $financestatus]);
                } else {
                    YearcodeMapping::create([
                        'auditplanid' => $currentUserId,
                        'yearselected' => $YearVal,
                        'createdby' => $currentUserId,
                        'statusflag' => 'Y',
                        'financestatus' => $financestatus,
                    ]);
                }
                DB::table('audit.inst_auditschedule')
                    ->where('auditplanid', $currentUserId)
                    ->update(['updatedon' => now()]);
            }
        }
    }

    public static function changerequestfetchData($auditplanid = null, $table = null)
    {
        $table = 'audit.auditplan';

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

        $query = DB::table($table.' as plan')
            ->join('audit.inst_auditschedule as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
            ->join(self::$institution_table.' as ins', 'plan.instid', '=', 'ins.instid')
            ->join(self::$deptartment_table.' as dept', 'ins.deptcode', '=', 'dept.deptcode')
            // ->join('audit.mst_auditquarter' . ' as qua', 'ins.audit_quarter', '=', 'qua.auditquartercode')
            ->join(self::$regionTable.' as reg', 'ins.regioncode', '=', 'reg.regioncode')
            ->join(self::$dist_table.' as dis', 'ins.distcode', '=', 'dis.distcode')
            ->join('audit.mst_auditquarter as qua', function ($join) {
                $join
                    ->on('qua.auditquartercode', '=', 'ins.audit_quarter')
                    ->on('qua.deptcode', '=', 'ins.deptcode');
            })
            ->leftJoinSub($auditPeriodSubquery, 'aps', function ($join) {
                $join->on('aps.auditplanid', '=', 'plan.auditplanid');
            })
            ->select(
                'ins.instid',
                'ins.instename',
                'ins.insttname',
                'reg.regioncode',
                'reg.regionename',
                'reg.regiontname',
                'dis.distename',
                'dis.disttname',
                'dis.distcode',
                'dept.deptcode',
                'dept.deptesname',
                'dept.deptelname',
                'sch.fromdate',
                'sch.todate',
                'dept.depttsname',
                'dept.depttlname',
                'qua.auditquartercode',
                'qua.auditquarter',
                'plan.auditplanid',
                'aps.audit_period',
                'aps.yearkeys'
            )
            ->where('plan.statusflag', 'F')
            ->where('sch.statusflag', 'F')
            ->where(function ($query) {
                $query
                    ->whereNull('sch.auditeeresponse')
                    ->orWhere('sch.auditeeresponse', '');
            })
            ->when($auditplanid, function ($query) use ($auditplanid) {
                $query->where('sch.auditplanid', $auditplanid);
            })
            ->orderBy('sch.updatedon', 'desc');

        $mainResults = $query->get();

        return $mainResults;
    }

    public static function commonquarterfetch($instmappingcode, $financialyear)
    {
        return DB::table('audit.auditplan as plan')
            ->join('audit.mst_institution as inst', 'plan.instid', '=', 'inst.instid')
            ->join('audit.auditplanmapping as planmap', 'planmap.planmappingid', '=', 'plan.planmappingid')
            ->join('audit.mst_financialyear as f', 'f.financialyearcode', '=', 'planmap.financialyearcode')

            ->select(
                'planmap.planname',
                'planmap.planmappingid',

                'inst.annadhanam_only',
                //  'inst.annadhanam_only'
            )
            ->where('planmap.financialyearcode', $financialyear)
            ->where('plan.instid', $instmappingcode)

            ->get();
    }

    public static function getFinancialYears($deptcode, $instmappingcode)
    {
        return DB::table('audit.auditplan as plan')
            ->join('audit.mst_institution as inst', 'plan.instid', '=', 'inst.instid')
            ->join('audit.auditplanmapping as planmap', 'planmap.planmappingid', '=', 'plan.planmappingid')
            ->join('audit.mst_financialyear as f', 'f.financialyearcode', '=', 'planmap.financialyearcode')
            ->select(
                'f.financialyearcode',
                'f.financialyear',
                'inst.annadhanam_only'
            )
            ->where('plan.instid', $instmappingcode)
            ->groupBy(
                'f.financialyearcode',
                'f.financialyear',
                'inst.annadhanam_only'
            )
            ->orderBy('f.financialyearcode', 'desc') // optional but useful
            ->get();
    }

    public static function commondeptfetch()
    {
        return DB::table(self::$deptartment_table.' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname')  // Select required columns
            ->where('dept.statusflag', '=', 'Y')  // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }

    public static function Auditperiodcompactfetch($deptcode)
    {
        return DB::table('audit.mst_auditperiod')
            ->select('auditperiodid', DB::raw("CONCAT(fromyear, ' - ', toyear) AS audit_period"))
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->where('financestatus', 'N')
            ->whereIn('lagacyyear', ['N', 'B'])
            ->orderBy('fromyear', 'desc')
            ->get();
    }

    public static function Auditperiodfetch($instmappingcode, $auditquartercode)
    {
        $table = 'audit.auditplan';
        $auditPeriodSubquery = DB::table('audit.yearcode_mapping as ycm')
            ->join('audit.mst_auditperiod as ap', 'ap.auditperiodid', '=', 'ycm.yearselected')
            ->select(
                'ycm.auditplanid',
                'ycm.yearselected',
                DB::raw("CONCAT(fromyear, ' - ', toyear) as audit_period")
            )
            ->where('ap.financestatus', 'N')
            ->where('ycm.statusflag', 'Y')
            ->where('ap.statusflag', 'Y')
            ->whereIn('ap.lagacyyear', ['N', 'B']);

        $query = DB::table($table.' as plan')
            ->join('audit.inst_auditschedule as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
            ->join('audit.auditplanmapping as planmap', 'planmap.planmappingid', '=', 'plan.planmappingid')
            ->leftJoinSub($auditPeriodSubquery, 'aps', function ($join) {
                $join->on('aps.auditplanid', '=', 'plan.auditplanid');
            })
            ->select(
                'plan.auditplanid',
                'aps.audit_period',
                'aps.yearselected',
                'sch.fromdate',
                'sch.todate'
            )
            ->where('plan.instid', $instmappingcode)
            ->where('plan.statusflag', 'F')
            ->where('sch.statusflag', 'F')
            ->where('planmap.planmappingid', $auditquartercode);

        $mainResults = $query->get();

        return $mainResults;
    }

    public static function getinstitutionBydistrictchange($district, $regioncode, $deptcode, $updatefield = null)
    {
        $table = self::$institution_table;

        return DB::table($table.' as ins')
            ->join('audit.auditplan'.' as plan', 'ins.instid', '=', 'plan.instid')
            ->join('audit.inst_auditschedule'.' as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
            ->select('ins.instename', 'ins.instid', 'ins.insttname')
            ->distinct()
            ->where('ins.distcode', $district)
            ->where('ins.deptcode', $deptcode)
            ->where('ins.regioncode', $regioncode)
            ->when($updatefield === '01' || $updatefield === '02', function ($query) {
                return $query->where('sch.auditeeresponse', 'A');
            }, function ($query) {
                return $query->where(function ($q) {
                    $q
                        ->whereNull('sch.auditeeresponse')
                        ->orWhere('sch.auditeeresponse', '');
                });
            })
            ->where('plan.statusflag', 'F')
            ->get();
    }

    public static function getRegionsByDept($deptcode)
    {
        $table = self::$institution_table;

        return DB::table($table.' as ins')
            ->join(self::$regionTable.' as reg', 'ins.regioncode', '=', 'reg.regioncode')
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
        $regionCodes = is_array($regioncode)
            ? $regioncode
            : preg_split('/\s*,\s*/', (string) ($regioncode ?? ''), -1, PREG_SPLIT_NO_EMPTY);
        $regionCodes = array_values(array_unique(array_filter(array_map(static function ($item) {
            return trim((string) $item);
        }, $regionCodes), static function ($item) {
            return $item !== '';
        })));

        $query = DB::table($table.' as ins')
            ->join(self::$dist_table.' as dis', 'ins.distcode', '=', 'dis.distcode')
            ->select('dis.distename', 'dis.distcode', 'dis.disttname')
            ->distinct()
            ->where('ins.deptcode', $deptcode)
            ->where('ins.statusflag', 'Y');

        if (count($regionCodes) > 1) {
            $query->whereIn('ins.regioncode', $regionCodes);
        } elseif (count($regionCodes) === 1) {
            $query->where('ins.regioncode', $regionCodes[0]);
        }

        return $query
            ->get();
    }

    // ----------------------------data verification start-----------------------------

    public static function instchangeDepartmentOptions()
    {
        return self::commondeptfetch();
    }

    public static function instchangeRegionOptions($deptcode)
    {
        return self::getRegionsByDept($deptcode);
    }

    public static function instchangeDistrictOptions($deptcode, $regioncode)
    {
        return self::getdistrictByregion($regioncode, $deptcode);
    }

    public static function dataVerificationUserDetails($deptcode, $distcode)
    {
        return DB::table(self::$userdetail_table.' as du')
            ->leftJoin(self::$deptartment_table.' as dept', 'dept.deptcode', '=', 'du.deptcode')
            ->leftJoin(self::$designation_table.' as desig', 'desig.desigcode', '=', 'du.desigcode')
            ->leftJoin(self::$dist_table.' as dist', 'dist.distcode', '=', 'du.distcode')
            ->leftJoin('audit.mst_disabilitytype as disabilitytype', 'disabilitytype.disability_typeid', '=', 'du.disability_typeid')
            ->select(
                'du.deptuserid',
                'du.username',
                'desig.desigelname',
                'du.disability',
                'du.disabilityrate',
                'disabilitytype.disability_ename',
                DB::raw("CASE
                    WHEN du.gendercode = 'M' THEN 'Male'
                    WHEN du.gendercode = 'F' THEN 'Female'
                    WHEN du.gendercode = 'T' THEN 'Transgender'
                    ELSE '-'
                END as gender_label"),
                DB::raw("CASE
                    WHEN du.reservelist = 'Y' THEN 'Yes'
                    WHEN du.reservelist = 'N' THEN 'No'
                    ELSE '-'
                END as reservelist_label")
            )
            ->where('du.statusflag', 'Y')
            ->where('du.deptcode', $deptcode)
            ->where('du.distcode', $distcode)
            ->orderBy('du.username', 'asc')
            ->get();
    }

    public static function dataVerificationSelectedOptions($deptcode, $regioncode, $distcode)
    {
        return [
            'department' => DB::table(self::$deptartment_table)
                ->select('deptcode', 'deptelname', 'depttlname')
                ->where('deptcode', $deptcode)
                ->first(),
            'region' => DB::table(self::$regionTable)
                ->select('regioncode', 'regionename', 'regiontname')
                ->where('regioncode', $regioncode)
                ->first(),
            'district' => DB::table(self::$dist_table)
                ->select('distcode', 'distename', 'disttname')
                ->where('distcode', $distcode)
                ->first(),
        ];
    }

    public static function dataVerificationInstitutionDetails($deptcode, $regioncode, $distcode, $quartercode = null, $prioritycode = null)
    {
        $quartercode = strtoupper(trim((string) ($quartercode ?? '')));
        $prioritycode = trim((string) ($prioritycode ?? ''));
        $prioritycode = ($prioritycode === '' || in_array(strtolower($prioritycode), ['null', 'undefined', "''"], true))
            ? null
            : $prioritycode;
        $quarterColumns = ['Q1', 'Q2', 'Q3', 'Q4'];
        $displayQuarter = in_array($quartercode, $quarterColumns, true) ? $quartercode : '-';

        $query = DB::table(self::$institution_table.' as inst')
            ->leftJoin(self::$mstauditeeinscategory_table.' as cat', 'cat.catcode', '=', 'inst.catcode')
            ->leftJoin(self::$subcategory_table.' as sub', 'sub.auditeeins_subcategoryid', '=', 'inst.subcatid')
            ->leftJoin(BaseModel::AUDITMODE_TABLE.' as auditmode', 'auditmode.auditmodecode', '=', 'inst.auditmode')
            ->select(
                'inst.instid',
                'inst.instename',
                'cat.catename',
                'sub.subcatename',
                'inst.teamsize',
                DB::raw("CASE
                    WHEN inst.spillover = 'Y' THEN inst.remainingmandays
                    ELSE inst.mandays
                END as mandays"),
                DB::raw("COALESCE(inst.spillover, 'N') as spillover"),
                'inst.remainingmandays',
                DB::raw('inst.inst_kms as distance'),
                DB::raw("'".$displayQuarter."' as quartercode"),
                DB::raw("COALESCE(auditmode.auditmodeename, inst.auditmode, '-') as auditmode_label")
            )
            ->where('inst.statusflag', 'Y')
            ->where('inst.deptcode', $deptcode)
            ->where('inst.regioncode', $regioncode)
            ->where('inst.distcode', $distcode)
            ->when(in_array($quartercode, $quarterColumns, true), function ($query) use ($quartercode) {
                $query->where('inst.'.$quartercode, 'Y');
            })
            ->when($prioritycode !== null, function ($query) use ($prioritycode) {
                $query->where('inst.inst_priority_kms', $prioritycode);
            })
            ->orderBy('inst.instename', 'asc');

        if ($prioritycode !== null) {
            $query->addSelect(DB::raw("CASE
                WHEN inst.inst_priority_kms = '01' THEN 'P1'
                WHEN inst.inst_priority_kms = '02' THEN 'P2'
                ELSE COALESCE(inst.inst_priority_kms, '-')
            END as priority_label"));
        }

        return $query->get();
    }

    public static function dataVerificationIsFinalized($deptcode, $regioncode, $distcode, $planmappingid, $quartercode, $prioritycode = null): bool
    {
        $planmappingid = (int) $planmappingid;
        $quartercode = strtoupper(trim((string) ($quartercode ?? '')));
        $prioritycode = trim((string) ($prioritycode ?? ''));
        $prioritycode = ($prioritycode === '' || in_array(strtolower($prioritycode), ['null', 'undefined', "''"], true))
            ? null
            : $prioritycode;

        $usersFinalized = DB::table('audit.log_deptuserdetails')
            ->where('deptcode', $deptcode)
            ->where('regioncode', $regioncode)
            ->where('distcode', $distcode)
            ->where('planmappingid', $planmappingid)
            ->where('verifiedquarter', $quartercode)
            ->where('statusflag', 'F')
            ->exists();

        $usersPendingDraft = DB::table('audit.log_deptuserdetails')
            ->where('deptcode', $deptcode)
            ->where('regioncode', $regioncode)
            ->where('distcode', $distcode)
            ->where('planmappingid', $planmappingid)
            ->where('verifiedquarter', $quartercode)
            ->where('statusflag', 'Y')
            ->exists();

        $institutionsFinalized = DB::table('audit.log_institution')
            ->where('deptcode', $deptcode)
            ->where('regioncode', $regioncode)
            ->where('distcode', $distcode)
            ->where('planmappingid', $planmappingid)
            ->where('verifiedquarter', $quartercode)
            ->where('statusflag', 'F')
            ->when($prioritycode !== null, function ($query) use ($prioritycode) {
                $query->where('inst_priority_kms', $prioritycode);
            })
            ->exists();

        $institutionsPendingDraft = DB::table('audit.log_institution')
            ->where('deptcode', $deptcode)
            ->where('regioncode', $regioncode)
            ->where('distcode', $distcode)
            ->where('planmappingid', $planmappingid)
            ->where('verifiedquarter', $quartercode)
            ->where('statusflag', 'Y')
            ->when($prioritycode !== null, function ($query) use ($prioritycode) {
                $query->where('inst_priority_kms', $prioritycode);
            })
            ->exists();

        return $usersFinalized && $institutionsFinalized && ! $usersPendingDraft && ! $institutionsPendingDraft;
    }

    public static function dataVerificationLogUserDetails($deptcode, $regioncode, $distcode, $planmappingid, $quartercode)
    {
        $planmappingid = (int) $planmappingid;
        $quartercode = strtoupper(trim((string) ($quartercode ?? '')));

        return DB::table('audit.log_deptuserdetails as du')
            ->leftJoin(self::$designation_table.' as desig', 'desig.desigcode', '=', 'du.desigcode')
            ->leftJoin('audit.mst_disabilitytype as disabilitytype', 'disabilitytype.disability_typeid', '=', 'du.disability_typeid')
            ->select(
                'du.deptuserid',
                'du.username',
                'desig.desigelname',
                'du.disability',
                'du.disabilityrate',
                'disabilitytype.disability_ename',
                DB::raw("CASE
                    WHEN du.gendercode = 'M' THEN 'Male'
                    WHEN du.gendercode = 'F' THEN 'Female'
                    WHEN du.gendercode = 'T' THEN 'Transgender'
                    ELSE '-'
                END as gender_label"),
                DB::raw("CASE
                    WHEN du.reservelist = 'Y' THEN 'Yes'
                    WHEN du.reservelist = 'N' THEN 'No'
                    ELSE '-'
                END as reservelist_label")
            )
            ->where('du.statusflag', 'F')
            ->where('du.deptcode', $deptcode)
            ->where('du.regioncode', $regioncode)
            ->where('du.distcode', $distcode)
            ->where('du.planmappingid', $planmappingid)
            ->where('du.verifiedquarter', $quartercode)
            ->orderBy('du.username', 'asc')
            ->get();
    }

    public static function dataVerificationLogInstitutionDetails($deptcode, $regioncode, $distcode, $planmappingid, $quartercode = null, $prioritycode = null)
    {
        $planmappingid = (int) $planmappingid;
        $quartercode = strtoupper(trim((string) ($quartercode ?? '')));
        $prioritycode = trim((string) ($prioritycode ?? ''));
        $prioritycode = ($prioritycode === '' || in_array(strtolower($prioritycode), ['null', 'undefined', "''"], true))
            ? null
            : $prioritycode;
        $quarterColumns = ['Q1', 'Q2', 'Q3', 'Q4'];
        $displayQuarter = in_array($quartercode, $quarterColumns, true) ? $quartercode : '-';

        $query = DB::table('audit.log_institution as inst')
            ->leftJoin(self::$institution_table.' as master_inst', 'master_inst.instid', '=', 'inst.instid')
            ->leftJoin(self::$mstauditeeinscategory_table.' as cat', 'cat.catcode', '=', 'inst.catcode')
            ->leftJoin(self::$subcategory_table.' as sub', 'sub.auditeeins_subcategoryid', '=', 'inst.subcatid')
            ->leftJoin(BaseModel::AUDITMODE_TABLE.' as auditmode', 'auditmode.auditmodecode', '=', 'inst.auditmode')
            ->select(
                'inst.instid',
                'inst.instename',
                'cat.catename',
                'sub.subcatename',
                'inst.teamsize',
                DB::raw("CASE
                    WHEN master_inst.spillover = 'Y' THEN master_inst.remainingmandays
                    ELSE inst.mandays
                END as mandays"),
                DB::raw("COALESCE(master_inst.spillover, 'N') as spillover"),
                'master_inst.remainingmandays',
                DB::raw('inst.inst_kms as distance'),
                DB::raw("'".$displayQuarter."' as quartercode"),
                DB::raw("COALESCE(auditmode.auditmodeename, inst.auditmode, '-') as auditmode_label")
            )
            ->where('inst.statusflag', 'F')
            ->where('inst.deptcode', $deptcode)
            ->where('inst.regioncode', $regioncode)
            ->where('inst.distcode', $distcode)
            ->where('inst.planmappingid', $planmappingid)
            ->where('inst.verifiedquarter', $quartercode)
            ->when($prioritycode !== null, function ($query) use ($prioritycode) {
                $query->where('inst.inst_priority_kms', $prioritycode);
            })
            ->orderBy('inst.instename', 'asc');

        if ($prioritycode !== null) {
            $query->addSelect(DB::raw("CASE
                WHEN inst.inst_priority_kms = '01' THEN 'P1'
                WHEN inst.inst_priority_kms = '02' THEN 'P2'
                ELSE COALESCE(inst.inst_priority_kms, '-')
            END as priority_label"));
        }

        return $query->get();
    }

    public static function dataVerificationMeta($deptcode, $regioncode, $distcode, $planmappingid, $quartercode, $prioritycode = null): array
    {
        $planmappingid = (int) $planmappingid;
        $quartercode = strtoupper(trim((string) ($quartercode ?? '')));
        $prioritycode = trim((string) ($prioritycode ?? ''));
        $prioritycode = ($prioritycode === '' || in_array(strtolower($prioritycode), ['null', 'undefined', "''"], true))
            ? null
            : $prioritycode;

        $userMeta = DB::table('audit.log_deptuserdetails as log')
            ->leftJoin(self::$userdetail_table.' as verifier', 'verifier.deptuserid', '=', 'log.verifiedby')
            ->select(
                'log.verifiedby',
                'verifier.username as verifiedby_name',
                DB::raw("to_char(log.verifiedon, 'DD-MM-YYYY HH12:MI AM') as verifiedon")
            )
            ->where('log.deptcode', $deptcode)
            ->where('log.regioncode', $regioncode)
            ->where('log.distcode', $distcode)
            ->where('log.planmappingid', $planmappingid)
            ->where('log.verifiedquarter', $quartercode)
            ->where('log.statusflag', 'F')
            ->whereNotNull('log.verifiedon')
            ->orderBy('log.verifiedon', 'desc')
            ->first();

        if ($userMeta) {
            return [
                'verifiedby' => $userMeta->verifiedby,
                'verifiedby_name' => $userMeta->verifiedby_name,
                'verifiedon' => $userMeta->verifiedon,
            ];
        }

        $institutionMeta = DB::table('audit.log_institution as log')
            ->leftJoin(self::$userdetail_table.' as verifier', 'verifier.deptuserid', '=', 'log.verifiedby')
            ->select(
                'log.verifiedby',
                'verifier.username as verifiedby_name',
                DB::raw("to_char(log.verifiedon, 'DD-MM-YYYY HH12:MI AM') as verifiedon")
            )
            ->where('log.deptcode', $deptcode)
            ->where('log.regioncode', $regioncode)
            ->where('log.distcode', $distcode)
            ->where('log.planmappingid', $planmappingid)
            ->where('log.verifiedquarter', $quartercode)
            ->where('log.statusflag', 'F')
            ->whereNotNull('log.verifiedon')
            ->when($prioritycode !== null, function ($query) use ($prioritycode) {
                $query->where('log.inst_priority_kms', $prioritycode);
            })
            ->orderBy('log.verifiedon', 'desc')
            ->first();

        return [
            'verifiedby' => $institutionMeta->verifiedby ?? null,
            'verifiedby_name' => $institutionMeta->verifiedby_name ?? null,
            'verifiedon' => $institutionMeta->verifiedon ?? null,
        ];
    }

    public static function saveDataVerificationUserDrafts($deptcode, $regioncode, $distcode, $planmappingid, $quartercode): int
    {
        $planmappingid = (int) $planmappingid;

        $users = DB::table(self::$userdetail_table.' as du')
            ->select(
                'du.deptuserid',
                'du.deptcode',
                'du.distcode',
                'du.username',
                'du.desigcode',
                'du.gendercode',
                'du.statusflag',
                'du.auditorflag',
                'du.reservelist',
                'du.disability',
                'du.disabilityrate',
                'du.disability_typeid'
            )
            ->where('du.statusflag', 'Y')
            ->where('du.deptcode', $deptcode)
            ->where('du.distcode', $distcode)
            ->whereNotNull('du.deptuserid')
            ->whereNotNull('du.username')
            ->whereNotNull('du.desigcode')
            ->whereNotNull('du.gendercode')
            ->get();

        return DB::transaction(function () use ($users, $deptcode, $regioncode, $distcode, $planmappingid, $quartercode) {
            $savedCount = 0;

            DB::table('audit.log_deptuserdetails')
                ->where('deptcode', $deptcode)
                ->where('regioncode', $regioncode)
                ->where('distcode', $distcode)
                ->where('planmappingid', $planmappingid)
                ->where('verifiedquarter', $quartercode)
                ->where('statusflag', 'Y')
                ->delete();

            DB::table('audit.temp_deptuserdetails')
                ->where('deptcode', $deptcode)
                ->where('regioncode', $regioncode)
                ->where('distcode', $distcode)
                ->where('planmappingid', $planmappingid)
                ->delete();

            foreach ($users as $user) {
                DB::table('audit.log_deptuserdetails')->updateOrInsert(
                    [
                        'deptuserid' => $user->deptuserid,
                        'deptcode' => $user->deptcode,
                        'regioncode' => $regioncode,
                        'distcode' => $user->distcode,
                        'desigcode' => $user->desigcode,
                        'planmappingid' => $planmappingid,
                    ],
                    [
                        'username' => $user->username,
                        'gendercode' => $user->gendercode,
                        'statusflag' => $user->statusflag,
                        'auditorflag' => $user->auditorflag,
                        'reservelist' => $user->reservelist,
                        'disability' => $user->disability,
                        'disabilityrate' => $user->disabilityrate,
                        'disability_typeid' => $user->disability_typeid,
                        'verifiedquarter' => $quartercode,
                        'verifiedby' => null,
                        'verifiedon' => null,
                    ]
                );

                DB::table('audit.temp_deptuserdetails')->insert([
                    'deptuserid' => $user->deptuserid,
                    'deptcode' => $user->deptcode,
                    'regioncode' => $regioncode,
                    'distcode' => $user->distcode,
                    'username' => $user->username,
                    'desigcode' => $user->desigcode,
                    'planmappingid' => $planmappingid,
                    'statusflag' => $user->statusflag,
                ]);

                $savedCount++;
            }

            return $savedCount;
        });
    }

    public static function saveDataVerificationInstitutionDrafts($deptcode, $regioncode, $distcode, $planmappingid, $quartercode, $prioritycode): int
    {
        $quartercode = strtoupper(trim((string) ($quartercode ?? '')));
        $prioritycode = trim((string) ($prioritycode ?? ''));
        $prioritycode = ($prioritycode === '' || in_array(strtolower($prioritycode), ['null', 'undefined', "''"], true))
            ? null
            : $prioritycode;
        $quarterColumns = ['Q1', 'Q2', 'Q3', 'Q4'];
        $planmappingid = (int) $planmappingid;

        $institutions = DB::table(self::$institution_table.' as inst')
            ->select(
                'inst.instid',
                'inst.deptcode',
                'inst.regioncode',
                'inst.distcode',
                DB::raw('COALESCE(inst.revenuedistcode, inst.distcode) as revenuedistcode'),
                'inst.instename',
                DB::raw("COALESCE(inst.catcode, '0') as catcode"),
                DB::raw('COALESCE(inst.subcatid, 0) as subcatid'),
                DB::raw("CASE
                    WHEN inst.spillover = 'Y' THEN inst.remainingmandays
                    ELSE inst.mandays
                END as mandays"),
                'inst.erpno',
                'inst.statusflag',
                'inst.teamsize',
                'inst.auditmode',
                'inst.parentinstid',
                'inst.insttype',
                'inst.hubdesigcode',
                'inst.hubtype',
                'inst.inst_kms',
                'inst.inst_priority_kms',
                'inst.distancecategory',
                'inst.zonecode'
            )
            ->where('inst.statusflag', 'Y')
            ->where('inst.deptcode', $deptcode)
            ->where('inst.regioncode', $regioncode)
            ->where('inst.distcode', $distcode)
            ->when(in_array($quartercode, $quarterColumns, true), function ($query) use ($quartercode) {
                $query->where('inst.'.$quartercode, 'Y');
            })
            ->when($prioritycode !== null, function ($query) use ($prioritycode) {
                $query->where('inst.inst_priority_kms', $prioritycode);
            })
            ->get();

        return DB::transaction(function () use ($institutions, $deptcode, $regioncode, $distcode, $planmappingid, $quartercode, $prioritycode) {
            $savedCount = 0;

            DB::table('audit.log_institution')
                ->where('deptcode', $deptcode)
                ->where('regioncode', $regioncode)
                ->where('distcode', $distcode)
                ->where('planmappingid', $planmappingid)
                ->when($prioritycode !== null, function ($query) use ($prioritycode) {
                    $query->where('inst_priority_kms', $prioritycode);
                })
                ->delete();

            DB::table('audit.temp_institution')
                ->where('deptcode', $deptcode)
                ->where('regioncode', $regioncode)
                ->where('distcode', $distcode)
                ->delete();

            foreach ($institutions as $institution) {
                DB::table('audit.log_institution')->insert(
                    [
                        'instid' => $institution->instid,
                        'deptcode' => $institution->deptcode,
                        'distcode' => $institution->distcode,
                        'regioncode' => $institution->regioncode,
                        'catcode' => $institution->catcode,
                        'subcatid' => $institution->subcatid,
                        'planmappingid' => $planmappingid,
                        'revenuedistcode' => $institution->revenuedistcode,
                        'instename' => $institution->instename,
                        'mandays' => $institution->mandays,
                        'erpno' => $institution->erpno,
                        'statusflag' => $institution->statusflag,
                        'teamsize' => $institution->teamsize,
                        'auditmode' => $institution->auditmode,
                        'parentinstid' => $institution->parentinstid,
                        'insttype' => $institution->insttype,
                        'hubdesigcode' => $institution->hubdesigcode,
                        'hubtype' => $institution->hubtype,
                        'inst_kms' => $institution->inst_kms,
                        'inst_priority_kms' => $institution->inst_priority_kms,
                        'distancecategory' => $institution->distancecategory,
                        'zonecode' => $institution->zonecode,
                        'verifiedquarter' => $quartercode,
                        'verifiedby' => null,
                        'verifiedon' => null,
                    ]
                );

                DB::table('audit.temp_institution')->insert([
                    'instid' => $institution->instid,
                    'deptcode' => $institution->deptcode,
                    'regioncode' => $institution->regioncode,
                    'distcode' => $institution->distcode,
                    'statusflag' => $institution->statusflag,
                ]);

                $savedCount++;
            }

            return $savedCount;
        });
    }

    public static function dataVerificationTempStatus($deptcode, $regioncode, $distcode, $planmappingid, $quartercode, $prioritycode = null): array
    {
        $planmappingid = (int) $planmappingid;
        $quartercode = strtoupper(trim((string) ($quartercode ?? '')));
        $prioritycode = trim((string) ($prioritycode ?? ''));
        $prioritycode = ($prioritycode === '' || in_array(strtolower($prioritycode), ['null', 'undefined', "''"], true))
            ? null
            : $prioritycode;
        $quarterColumns = ['Q1', 'Q2', 'Q3', 'Q4'];

        $userMasterCount = DB::table(self::$userdetail_table.' as du')
            ->where('du.statusflag', 'Y')
            ->where('du.deptcode', $deptcode)
            ->where('du.distcode', $distcode)
            ->whereNotNull('du.deptuserid')
            ->whereNotNull('du.username')
            ->whereNotNull('du.desigcode')
            ->whereNotNull('du.gendercode')
            ->count();

        $userTempCount = DB::table('audit.temp_deptuserdetails')
            ->where('deptcode', $deptcode)
            ->where('regioncode', $regioncode)
            ->where('distcode', $distcode)
            ->where('planmappingid', $planmappingid)
            ->where('statusflag', 'Y')
            ->count();

        $institutionMasterCount = DB::table(self::$institution_table.' as inst')
            ->where('inst.statusflag', 'Y')
            ->where('inst.deptcode', $deptcode)
            ->where('inst.regioncode', $regioncode)
            ->where('inst.distcode', $distcode)
            ->whereNotNull('inst.instid')
            ->when(in_array($quartercode, $quarterColumns, true), function ($query) use ($quartercode) {
                $query->where('inst.'.$quartercode, 'Y');
            })
            ->when($prioritycode !== null, function ($query) use ($prioritycode) {
                $query->where('inst.inst_priority_kms', $prioritycode);
            })
            ->count();

        $institutionTempCount = DB::table('audit.temp_institution')
            ->where('deptcode', $deptcode)
            ->where('regioncode', $regioncode)
            ->where('distcode', $distcode)
            ->where('statusflag', 'Y')
            ->count();

        $usersMatched = $userMasterCount === $userTempCount;
        $institutionsMatched = $institutionMasterCount === $institutionTempCount;

        return [
            'users_matched' => $usersMatched,
            'institutions_matched' => $institutionsMatched,
            'ready_to_finalize' => $usersMatched && $institutionsMatched,
            'user_master_count' => $userMasterCount,
            'user_temp_count' => $userTempCount,
            'institution_master_count' => $institutionMasterCount,
            'institution_temp_count' => $institutionTempCount,
        ];
    }

    public static function dataVerificationDraftStatus($deptcode, $regioncode, $distcode, $planmappingid, $quartercode, $prioritycode = null): array
    {
        $planmappingid = (int) $planmappingid;
        $quartercode = strtoupper(trim((string) ($quartercode ?? '')));
        $prioritycode = trim((string) ($prioritycode ?? ''));
        $prioritycode = ($prioritycode === '' || in_array(strtolower($prioritycode), ['null', 'undefined', "''"], true))
            ? null
            : $prioritycode;
        $quarterColumns = ['Q1', 'Q2', 'Q3', 'Q4'];

        $userExpectedCount = DB::table(self::$userdetail_table.' as du')
            ->where('du.statusflag', 'Y')
            ->where('du.deptcode', $deptcode)
            ->where('du.distcode', $distcode)
            ->whereNotNull('du.deptuserid')
            ->whereNotNull('du.username')
            ->whereNotNull('du.desigcode')
            ->whereNotNull('du.gendercode')
            ->count();

        $userSavedCount = DB::table('audit.log_deptuserdetails')
            ->where('deptcode', $deptcode)
            ->where('regioncode', $regioncode)
            ->where('distcode', $distcode)
            ->where('planmappingid', $planmappingid)
            ->where('verifiedquarter', $quartercode)
            ->where('statusflag', 'Y')
            ->count();

        $institutionExpectedCount = DB::table(self::$institution_table.' as inst')
            ->where('inst.statusflag', 'Y')
            ->where('inst.deptcode', $deptcode)
            ->where('inst.regioncode', $regioncode)
            ->where('inst.distcode', $distcode)
            ->whereNotNull('inst.instid')
            ->when(in_array($quartercode, $quarterColumns, true), function ($query) use ($quartercode) {
                $query->where('inst.'.$quartercode, 'Y');
            })
            ->when($prioritycode !== null, function ($query) use ($prioritycode) {
                $query->where('inst.inst_priority_kms', $prioritycode);
            })
            ->count();

        $institutionSavedCount = DB::table('audit.log_institution')
            ->where('deptcode', $deptcode)
            ->where('regioncode', $regioncode)
            ->where('distcode', $distcode)
            ->where('planmappingid', $planmappingid)
            ->where('verifiedquarter', $quartercode)
            ->where('statusflag', 'Y')
            ->when($prioritycode !== null, function ($query) use ($prioritycode) {
                $query->where('inst_priority_kms', $prioritycode);
            })
            ->count();

        $usersSaved = $userExpectedCount > 0 && $userSavedCount >= $userExpectedCount;
        $institutionsSaved = $institutionExpectedCount > 0 && $institutionSavedCount >= $institutionExpectedCount;

        return [
            'users' => $usersSaved,
            'institutions' => $institutionsSaved,
            'ready_to_finalize' => $usersSaved && $institutionsSaved,
            'user_expected_count' => $userExpectedCount,
            'user_saved_count' => $userSavedCount,
            'institution_expected_count' => $institutionExpectedCount,
            'institution_saved_count' => $institutionSavedCount,
        ];
    }

    public static function finalizeDataVerificationDrafts($deptcode, $regioncode, $distcode, $planmappingid, $quartercode, $prioritycode = null, $verifiedby = null): array
    {
        $planmappingid = (int) $planmappingid;
        $quartercode = strtoupper(trim((string) ($quartercode ?? '')));
        $prioritycode = trim((string) ($prioritycode ?? ''));
        $prioritycode = ($prioritycode === '' || in_array(strtolower($prioritycode), ['null', 'undefined', "''"], true))
            ? null
            : $prioritycode;
        $verifiedOn = View::shared('get_nowtime');

        return DB::transaction(function () use ($deptcode, $regioncode, $distcode, $planmappingid, $quartercode, $prioritycode, $verifiedby, $verifiedOn) {
            $userUpdatedCount = DB::table('audit.log_deptuserdetails')
                ->where('deptcode', $deptcode)
                ->where('regioncode', $regioncode)
                ->where('distcode', $distcode)
                ->where('planmappingid', $planmappingid)
                ->where('verifiedquarter', $quartercode)
                ->where('statusflag', 'Y')
                ->update([
                    'statusflag' => 'F',
                    'verifiedby' => $verifiedby,
                    'verifiedon' => $verifiedOn,
                ]);

            $institutionUpdatedCount = DB::table('audit.log_institution')
                ->where('deptcode', $deptcode)
                ->where('regioncode', $regioncode)
                ->where('distcode', $distcode)
                ->where('planmappingid', $planmappingid)
                ->where('verifiedquarter', $quartercode)
                ->where('statusflag', 'Y')
                ->when($prioritycode !== null, function ($query) use ($prioritycode) {
                    $query->where('inst_priority_kms', $prioritycode);
                })
                ->update([
                    'statusflag' => 'F',
                    'verifiedby' => $verifiedby,
                    'verifiedon' => $verifiedOn,
                ]);

            $auditorInstMappingUpdatedCount = DB::table('audit.auditor_instmapping')
                ->where('deptcode', $deptcode)
                ->where('regioncode', $regioncode)
                ->where('distcode', $distcode)
                ->update([
                    'verifiedplandetails' => 'A',
                ]);

            DB::table('audit.temp_deptuserdetails')
                ->where('deptcode', $deptcode)
                ->where('regioncode', $regioncode)
                ->where('distcode', $distcode)
                ->where('planmappingid', $planmappingid)
                ->delete();

            DB::table('audit.temp_institution')
                ->where('deptcode', $deptcode)
                ->where('regioncode', $regioncode)
                ->where('distcode', $distcode)
                ->delete();

            return [
                'users' => $userUpdatedCount,
                'institutions' => $institutionUpdatedCount,
                'auditor_instmapping' => $auditorInstMappingUpdatedCount,
            ];
        });
    }

    public static function dataVerificationPendingTransactions($deptcode, $distcode)
    {
        return DB::table('audit.othertransactions as ot')
            ->leftJoin('audit.transactiondetail as utd', 'utd.othertransid', '=', 'ot.othertransid')
            ->leftJoin('audit.userchargedetails as ucd', 'ucd.userchargeid', '=', 'utd.forwardedtouserchargeid')
            ->leftJoin('audit.deptuserdetails as updby', 'updby.deptuserid', '=', 'ucd.userid')
            ->leftJoin('audit.mst_designation as updby_des', 'updby_des.desigcode', '=', 'updby.desigcode')
            ->leftJoin('audit.userchargedetails as ucds', 'ucds.userchargeid', '=', 'ot.updatedbyuserchargeid')
            ->leftJoin('audit.deptuserdetails as updbys', 'updbys.deptuserid', '=', 'ucds.userid')
            ->leftJoin('audit.mst_designation as updby_dess', 'updby_dess.desigcode', '=', 'updbys.desigcode')
            ->join('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'ot.userid')
            ->join('audit.mst_designation as des', 'des.desigcode', '=', 'ot.fromdesigcode')
            ->join('audit.auditor_instmapping as aimsf', 'aimsf.instmappingcode', '=', 'ot.frominstmappingcode')
            ->join('audit.mst_dept as fdp', 'fdp.deptcode', '=', 'aimsf.deptcode')
            ->join('audit.mst_district as fd', 'fd.distcode', '=', 'aimsf.distcode')
            ->join('audit.auditor_instmapping as aimst', 'aimst.instmappingcode', '=', 'ot.toinstmappingcode')
            ->join('audit.mst_dept as tdp', 'tdp.deptcode', '=', 'aimst.deptcode')
            ->join('audit.mst_district as td', 'td.distcode', '=', 'aimst.distcode')
            ->join('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'ot.transactiontypecode')
            ->select(
                'fd.distcode',
                'fdp.deptcode',
                'td.distcode as tdistcode',
                'tdp.deptcode as tdeptcode',
                'dp.username',
                'des.desigesname as designation',
                'dp.ifhrmsno',
                'fdp.deptesname as fromdepartment',
                'tdp.deptesname as todepartment',
                'fd.distename as fromdistrict',
                'td.distename as todistrict',
                'tt.transactiontypelname as transactiontype',
                DB::raw("to_char(ot.orderdate, 'DD-MM-YYYY') as orderdate"),
                'ot.orderno',
                DB::raw("CONCAT(
                    COALESCE(updby.username, updbys.username, '-'),
                    ' (',
                    COALESCE(updby_des.desigesname, updby_dess.desigesname, '-'),
                    ')'
                ) as pending_at"),
                DB::raw("to_char(COALESCE(utd.updatedon, ot.updatedon), 'DD-MM-YYYY HH12:MI AM') as last_updatedon"),
                DB::raw("CASE
                    WHEN ot.processcode = 'S' THEN 'Pending At Entry Level'
                    WHEN ot.processcode = 'F' AND ot.inoutstatus = 'O' THEN 'Pending At AD(Out)'
                    WHEN ot.processcode = 'F' AND ot.inoutstatus = 'I' THEN 'Pending At AD(In)'
                    ELSE '-'
                END as status"),
                DB::raw("CASE
                    WHEN ot.processcode = 'S'
                        OR (ot.processcode = 'F' AND ot.inoutstatus = 'O')
                    THEN 'No'
                    ELSE 'Yes'
                END as startedprocess")
            )
            ->where('ot.processcode', '<>', 'P')
            ->where('ot.statusflag', 'Y')
            ->whereDate('ot.createdon', '>=', '2026-05-05')
            ->whereIn('ot.transactiontypecode', ['06', '07'])
            ->where(function ($query) use ($deptcode, $distcode) {
                $query
                    ->where(function ($subQuery) use ($deptcode, $distcode) {
                        $subQuery
                            ->where('ot.processcode', 'F')
                            ->where('ot.inoutstatus', 'O')
                            ->where('dp.reservelist', 'Y')
                            ->where('dp.allocatedstatusflag', 'Y')
                            ->where('fd.distcode', $distcode)
                            ->where('fdp.deptcode', $deptcode);
                    })
                    ->orWhere(function ($subQuery) use ($deptcode, $distcode) {
                        $subQuery
                            ->where('ot.processcode', 'F')
                            ->where('ot.inoutstatus', 'I')
                            ->where('td.distcode', $distcode)
                            ->where('tdp.deptcode', $deptcode);
                    });
            })
            ->orderBy('fdp.orderid')
            ->get();
    }

    public static function dataVerificationTeamAssignmentStatus($deptcode, $distcode, $planmappingid): array
    {
        $planmappingid = (int) $planmappingid;

        $status = DB::selectOne(
            "
            WITH params AS (
                SELECT
                    ?::varchar AS deptcode,
                    ?::varchar AS distcode,
                    ?::integer AS planmappingid
            ),
            normal_users AS (
                SELECT DISTINCT u.userid::integer AS userid
                FROM audit.team_assignments ta
                CROSS JOIN LATERAL unnest(array_prepend(ta.team_head, COALESCE(ta.team_users, ARRAY[]::integer[]))) AS u(userid)
                JOIN params p ON true
                WHERE ta.distcode = p.distcode
                  AND ta.deptcode = p.deptcode
                  AND ta.planmappingid = p.planmappingid
                  AND u.userid IS NOT NULL
            ),
            template_users AS (
                SELECT DISTINCT ta.userid::integer AS userid
                FROM audit.templateaudit_teamassignments ta
                JOIN params p ON true
                WHERE ta.distcode = p.distcode
                  AND ta.deptcode = p.deptcode
                  AND ta.planmappingid = p.planmappingid
                  AND ta.userid IS NOT NULL
                  AND NOT EXISTS (
                      SELECT 1 FROM normal_users n WHERE n.userid = ta.userid
                  )
            ),
            allocated_users AS (
                SELECT userid FROM normal_users
                UNION
                SELECT userid FROM template_users
            ),
            normal_institutions AS (
                SELECT DISTINCT ta.instid::integer AS instid
                FROM audit.team_assignments ta
                JOIN params p ON true
                WHERE ta.distcode = p.distcode
                  AND ta.deptcode = p.deptcode
                  AND ta.planmappingid = p.planmappingid
                  AND ta.instid IS NOT NULL
            ),
            template_institutions AS (
                SELECT DISTINCT ti.instid::integer AS instid
                FROM audit.templateaudit_teamassignments ta
                CROSS JOIN LATERAL unnest(ta.instid) AS ti(instid)
                JOIN params p ON true
                WHERE ta.distcode = p.distcode
                  AND ta.deptcode = p.deptcode
                  AND ta.planmappingid = p.planmappingid
                  AND ti.instid IS NOT NULL
                  AND NOT EXISTS (
                      SELECT 1 FROM normal_institutions n WHERE n.instid = ti.instid
                  )
            ),
            allocated_institutions AS (
                SELECT instid FROM normal_institutions
                UNION
                SELECT instid FROM template_institutions
            )
            SELECT
                EXISTS (
                    SELECT 1
                    FROM audit.team_assignments ta
                    JOIN params p ON true
                    WHERE ta.distcode = p.distcode
                      AND ta.deptcode = p.deptcode
                      AND ta.planmappingid = p.planmappingid
                ) AS regular_assignment_exists,
                EXISTS (
                    SELECT 1
                    FROM audit.templateaudit_teamassignments ta
                    JOIN params p ON true
                    WHERE ta.distcode = p.distcode
                      AND ta.deptcode = p.deptcode
                      AND ta.planmappingid = p.planmappingid
                ) AS template_assignment_exists,
                EXISTS (
                    SELECT 1
                    FROM allocated_users au
                    LEFT JOIN audit.deptuserdetails dp
                        ON dp.deptuserid = au.userid
                    JOIN params p ON true
                    WHERE dp.deptuserid IS NULL
                       OR dp.distcode <> p.distcode
                       OR dp.deptcode <> p.deptcode
                       OR COALESCE(dp.reservelist, 'N') <> 'Y'
                ) AS invalid_user_exists,
                EXISTS (
                    SELECT 1
                    FROM allocated_institutions ai
                    JOIN audit.mst_institution inst
                        ON inst.instid = ai.instid
                    JOIN params p ON true
                    WHERE inst.distcode <> p.distcode
                       OR inst.deptcode <> p.deptcode
                ) AS invalid_institution_exists
            ",
            [$deptcode, $distcode, $planmappingid]
        );

        $regularAssignmentExists = (bool) ($status->regular_assignment_exists ?? false);
        $templateAssignmentExists = (bool) ($status->template_assignment_exists ?? false);
        $invalidUserExists = (bool) ($status->invalid_user_exists ?? false);
        $invalidInstitutionExists = (bool) ($status->invalid_institution_exists ?? false);
        $invalidReferenceExists = $invalidUserExists || $invalidInstitutionExists;

        return [
            'ready_to_finalize' => ($regularAssignmentExists || $templateAssignmentExists)
                && ! $invalidReferenceExists,
            'regular_assignment_exists' => $regularAssignmentExists,
            'template_assignment_exists' => $templateAssignmentExists,
            'invalid_reference_exists' => $invalidReferenceExists,
            'invalid_user_exists' => $invalidUserExists,
            'invalid_institution_exists' => $invalidInstitutionExists,
        ];
    }

    public static function dataVerificationAssignmentMismatchStatus($deptcode, $regioncode, $distcode, $planmappingid, $quartercode, $prioritycode = null): array
    {
        $planmappingid = (int) $planmappingid;
        $quartercode = strtoupper(trim((string) ($quartercode ?? '')));
        $prioritycode = trim((string) ($prioritycode ?? ''));
        $prioritycode = ($prioritycode === '' || in_array(strtolower($prioritycode), ['null', 'undefined', "''"], true))
            ? null
            : $prioritycode;

        $verifiedUsers = function ($query) use ($deptcode, $regioncode, $distcode, $planmappingid, $quartercode) {
            $query->from('audit.log_deptuserdetails as ldu')
                ->select('ldu.deptuserid')
                ->where('ldu.deptcode', $deptcode)
                ->where('ldu.regioncode', $regioncode)
                ->where('ldu.distcode', $distcode)
                ->where('ldu.planmappingid', $planmappingid)
                ->where('ldu.verifiedquarter', $quartercode)
                ->where('ldu.statusflag', 'Y');
        };

        $verifiedInstitutions = function ($query) use ($deptcode, $regioncode, $distcode, $planmappingid, $quartercode, $prioritycode) {
            $query->from('audit.log_institution as li')
                ->select('li.instid', 'li.teamsize', 'li.mandays')
                ->where('li.deptcode', $deptcode)
                ->where('li.regioncode', $regioncode)
                ->where('li.distcode', $distcode)
                ->where('li.planmappingid', $planmappingid)
                ->where('li.verifiedquarter', $quartercode)
                ->where('li.statusflag', 'Y')
                ->when($prioritycode !== null, function ($subQuery) use ($prioritycode) {
                    $subQuery->where('li.inst_priority_kms', $prioritycode);
                });
        };

        $regularAssignmentQuery = DB::table('audit.team_assignments as ta')
            ->where('ta.deptcode', $deptcode)
            ->where('ta.distcode', $distcode)
            ->where('ta.planmappingid', $planmappingid);

        $templateAssignmentQuery = DB::table('audit.templateaudit_teamassignments as tta')
            ->where('tta.deptcode', $deptcode)
            ->where('tta.distcode', $distcode)
            ->where('tta.planmappingid', $planmappingid);

        $regularAssignedUsers = DB::query()
            ->fromSub(function ($query) use ($regularAssignmentQuery) {
                $headSql = (clone $regularAssignmentQuery)
                    ->selectRaw('ta.team_head::integer as userid')
                    ->whereNotNull('ta.team_head');
                $memberSql = (clone $regularAssignmentQuery)
                    ->selectRaw('unnest(ta.team_users)::integer as userid')
                    ->whereNotNull('ta.team_users');

                $query->fromSub($headSql->union($memberSql), 'regular_users')
                    ->select('userid')
                    ->distinct();
            }, 'assigned_users');

        $allAssignedUsers = function ($query) use ($regularAssignmentQuery, $templateAssignmentQuery) {
            $headSql = (clone $regularAssignmentQuery)
                ->selectRaw('ta.team_head::integer as userid')
                ->whereNotNull('ta.team_head');
            $memberSql = (clone $regularAssignmentQuery)
                ->selectRaw('unnest(ta.team_users)::integer as userid')
                ->whereNotNull('ta.team_users');
            $templateSql = (clone $templateAssignmentQuery)
                ->selectRaw('tta.userid::integer as userid')
                ->whereNotNull('tta.userid');

            $query->fromSub($headSql->union($memberSql)->union($templateSql), 'all_assigned_users')
                ->select('userid')
                ->distinct();
        };

        $regularUserMismatchCount = DB::query()
            ->fromSub($regularAssignedUsers, 'au')
            ->leftJoinSub($verifiedUsers, 'vu', 'vu.deptuserid', '=', 'au.userid')
            ->whereNull('vu.deptuserid')
            ->count();

        $templateUserMismatchCount = DB::query()
            ->fromSub((clone $templateAssignmentQuery)->selectRaw('distinct tta.userid::integer as userid'), 'tu')
            ->leftJoinSub($verifiedUsers, 'vu', 'vu.deptuserid', '=', 'tu.userid')
            ->whereNull('vu.deptuserid')
            ->count();

        $verifiedUserMismatchCount = DB::query()
            ->fromSub($verifiedUsers, 'vu')
            ->leftJoinSub($allAssignedUsers, 'au', 'au.userid', '=', 'vu.deptuserid')
            ->whereNull('au.userid')
            ->count();

        $regularInstitutionMismatchCount = DB::query()
            ->fromSub((clone $regularAssignmentQuery)->selectRaw('ta.instid::integer as instid, ta.team_size::integer as teamsize, ta.mandays::integer as mandays'), 'ra')
            ->leftJoinSub($verifiedInstitutions, 'vi', 'vi.instid', '=', 'ra.instid')
            ->where(function ($query) {
                $query->whereNull('vi.instid')
                    ->orWhereRaw('COALESCE(ra.teamsize, -1) <> COALESCE(vi.teamsize, -1)')
                    ->orWhereRaw('COALESCE(ra.mandays, -1) <> COALESCE(vi.mandays, -1)');
            })
            ->count();

        $templateInstitutionMismatchCount = DB::query()
            ->fromSub((clone $templateAssignmentQuery)->selectRaw('unnest(tta.instid)::integer as instid, tta.mandays::integer as mandays'), 'ta_inst')
            ->leftJoinSub($verifiedInstitutions, 'vi', 'vi.instid', '=', 'ta_inst.instid')
            ->where(function ($query) {
                $query->whereNull('vi.instid')
                    ->orWhereRaw('COALESCE(ta_inst.mandays, -1) <> COALESCE(vi.mandays, -1)');
            })
            ->count();

        $allAssignedInstitutions = function ($query) use ($regularAssignmentQuery, $templateAssignmentQuery) {
            $regularSql = (clone $regularAssignmentQuery)
                ->selectRaw('ta.instid::integer as instid')
                ->whereNotNull('ta.instid');
            $templateSql = (clone $templateAssignmentQuery)
                ->selectRaw('unnest(tta.instid)::integer as instid')
                ->whereNotNull('tta.instid');

            $query->fromSub($regularSql->union($templateSql), 'all_assigned_institutions')
                ->select('instid')
                ->distinct();
        };

        $verifiedInstitutionMismatchCount = DB::query()
            ->fromSub($verifiedInstitutions, 'vi')
            ->leftJoinSub($allAssignedInstitutions, 'ai', 'ai.instid', '=', 'vi.instid')
            ->whereNull('ai.instid')
            ->count();

        $totalMismatchCount = $regularUserMismatchCount
            + $templateUserMismatchCount
            + $verifiedUserMismatchCount
            + $regularInstitutionMismatchCount
            + $templateInstitutionMismatchCount
            + $verifiedInstitutionMismatchCount;

        return [
            'matched' => $totalMismatchCount === 0,
            'mismatch_count' => $totalMismatchCount,
            'regular_user_mismatch_count' => $regularUserMismatchCount,
            'template_user_mismatch_count' => $templateUserMismatchCount,
            'verified_user_not_in_assignment_count' => $verifiedUserMismatchCount,
            'regular_institution_mismatch_count' => $regularInstitutionMismatchCount,
            'template_institution_mismatch_count' => $templateInstitutionMismatchCount,
            'verified_institution_not_in_assignment_count' => $verifiedInstitutionMismatchCount,
        ];
    }

    // ----------------------------data verification End-----------------------------

    // -------------------------------------------Manual Plan Start----------------------------

    public static function fetchUpdatepManualplan($auditteamid = null)
    {
        $query = DB::table(self::$auditplan_table.' as plan')
            ->join(self::$auditplanteam_table.' as team', 'team.auditplanteamid', '=', 'plan.auditteamid')
            ->join(self::$auditplanteammem_table.' as mem', 'team.auditplanteamid', '=', 'mem.auditplanteamid')
            ->join(self::$institution_table.' as inst', 'inst.instid', '=', 'plan.instid')
            ->join(self::$deptartment_table.' as dept', 'inst.deptcode', '=', 'dept.deptcode')
            ->join(self::$regionTable.' as region', 'inst.regioncode', '=', 'region.regioncode')
            ->join(self::$dist_table.' as dist', 'inst.distcode', '=', 'dist.distcode')
            ->join(self::$userdetail_table.' as du', 'du.deptuserid', '=', 'mem.userid')
            ->join(self::$designation_table.' as desig', 'desig.desigcode', '=', 'du.desigcode')
            ->where('inst.statusflag', 'Y')
            ->where('dept.statusflag', 'Y')
            ->where('region.statusflag', 'Y')
            ->where('dist.statusflag', 'Y')
            ->where('plan.manualplan', 'Y')
            ->whereColumn('plan.auditquartercode', 'dept.currentquarter')
            ->select(
                'inst.deptcode',
                'inst.instid',
                'inst.instename',
                'inst.mandays',
                DB::raw('COALESCE(plan.totalmandays, inst.remainingmandays) as remainingmandays'),
                DB::raw("COALESCE(plan.spilloverflag, inst.spillover, 'N') as spillover"),
                'inst.teamsize',
                'dept.deptelname',
                'region.regionename',
                'inst.regioncode',
                'inst.distcode',
                'plan.auditplanid',
                'plan.fromdate',
                'plan.todate',
                'team.auditplanteamid',
                'dist.distename',
                'plan.statusflag',
                DB::raw("(
                    SELECT head.username || ' - ' || head_desig.desigelname|| ' - ' || head.deptuserid  || ' - ' || head_desig.desigcode
                    FROM audit.auditplanteammember AS head_tm
                    JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
                    JOIN audit.mst_designation AS head_desig ON head_desig.desigcode = head.desigcode
                    WHERE head_tm.auditplanteamid = team.auditplanteamid
                      AND head_tm.teamhead = 'Y'
                      AND head_tm.statusflag = 'Y'
                    LIMIT 1
                ) AS newteamheadname"),
                DB::raw("(
                    SELECT json_agg(DISTINCT tm.userid)
                    FROM audit.auditplanteammember AS tm
                    WHERE tm.auditplanteamid = team.auditplanteamid
                      AND tm.statusflag = 'Y'
                      AND tm.teamhead = 'N'
                ) AS newteammembers"),
                DB::raw("(
                    SELECT json_agg(DISTINCT tm.userid)
                    FROM audit.auditplanteammember AS tm
                    WHERE tm.auditplanteamid = team.auditplanteamid
                      AND tm.statusflag = 'Y'
                      AND tm.teamhead = 'Y'
                ) AS newteamhead"),
                DB::raw("(
                    SELECT STRING_AGG(DISTINCT member.username || ' - ' || member_desig.desigelname || ' - ' || member.deptuserid  || ' - ' ||member_desig.desigcode, ', ')
                    FROM audit.auditplanteammember AS tm
                    JOIN audit.deptuserdetails AS member ON member.deptuserid = tm.userid
                    JOIN audit.mst_designation AS member_desig ON member_desig.desigcode = member.desigcode
                    WHERE tm.auditplanteamid = team.auditplanteamid
                      AND tm.statusflag = 'Y'
                      AND tm.teamhead = 'N'
                ) AS newteammembernames")
            );

        if (! empty($auditteamid)) {
            $query->where('team.auditplanteamid', $auditteamid);
        }

        $query
            ->groupBy(
                'inst.deptcode',
                'inst.instename',
                'dept.deptelname',
                'region.regionename',
                'inst.regioncode',
                'inst.distcode',
                'plan.auditplanid',
                'team.auditplanteamid',
                'dist.distename',
                'team.statusflag',
                'inst.instid',
                'inst.remainingmandays',
                'inst.spillover',
                'plan.totalmandays',
                'plan.spilloverflag',
            )
            ->orderBy('plan.updatedon', 'desc')
            ->orderBy('dept.deptelname', 'asc');

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
    }

    // public static function fetchExcessinstitution(?string $deptcode = null, ?string $regioncode = null, ?string $distcode = null, $instid)
    // {
    //     try {
    //         $quarterdetails = self::getquarterdetails($deptcode);
    //         //    return $quarterdetails;
    //         $currentquarter = $quarterdetails[0]->auditquartercode;

    //         $query = DB::table(self::$institution_table . ' as inst')
    //             ->leftJoin(self::$auditplan_table . ' as plan', 'plan.instid', '=', 'inst.instid')
    //             ->join(self::$deptartment_table . ' as dept', 'dept.deptcode', '=', 'inst.deptcode')
    //             ->where(function ($q) use ($currentquarter, $instid) {
    //                 $q->whereNotIn('inst.instid', function ($subquery) use ($currentquarter) {
    //                     $subquery
    //                         ->select('instid')
    //                         ->from('audit.auditplan')
    //                         ->where('auditquartercode', $currentquarter);
    //                 });

    //                 $q->orWhere(function ($sub) {
    //                     $sub->where('plan.statusflag', '=', 'C');
    //                 });
    //                 if ($instid) {
    //                     $q->orWhere('inst.instid', $instid);
    //                 }
    //             })
    //             // ->whereNotIn('inst.instid', function ($query)  use ($currentquarter) {
    //             //     $query->select('instid')
    //             //         ->from('audit.auditplan')
    //             //         ->where('auditquartercode', $currentquarter);
    //             // })
    //             // ->where(function ($q) use ($instid) {
    //             //     $q->orWhere(function ($or) use ($instid) {
    //             //         $or->where('plan.instid', '=', $instid);
    //             //         //  ->whereNotIn('plan.statusflag', ['S', 'F']);
    //             //     });
    //             // })
    //             ->where("inst.$currentquarter", '=', 'Y')
    //             //  ->join(self::$mapinst_table . ' as map', 'map.instid', '=', 'inst.instid')
    //             ->select('inst.instename', 'inst.insttname', 'inst.instid', 'inst.mandays', 'inst.teamsize', 'plan.auditplanid')
    //             //  ->where('inst.quartercode', view::shared('current_quarter'))
    //             ->when($deptcode, function ($query) use ($deptcode) {
    //                 $query->where('inst.deptcode', $deptcode);
    //             })
    //             ->when($regioncode, function ($query) use ($regioncode) {
    //                 $query->where('inst.regioncode', $regioncode);
    //             })
    //             ->when($distcode, function ($query) use ($distcode) {
    //                 $query->where('inst.distcode', $distcode);
    //             })
    //             ->distinct();
    //         // $querySql = $query->toSql();
    //         // $bindings = $query->getBindings();

    //         // $finalQuery = vsprintf(
    //         //     str_replace('?', "'%s'", $querySql),
    //         //     array_map('addslashes', $bindings)
    //         // );

    //         // print_r($finalQuery);
    //         // exit;

    //         return $query->get();
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

    //         \Log::error('SQL Error: ' . $e->getMessage());
    //         throw new \Exception($customMessage, 500);
    //     } catch (\Exception $e) {
    //         throw new \Exception($e->getMessage(), 409);
    //     }
    // }

    public static function fetchExcessinstitution(?string $deptcode, ?string $regioncode, ?string $distcode, $instid)
    {
        try {
            $planDetails = collect(CommonModel::getplandetailsWithPrev($deptcode));
            // dd($planDetails);
            $previousPlan = $planDetails->first(fn ($plan) => ($plan->statusflag ?? null) === 'P');
            $currentPlan = $planDetails->first(fn ($plan) => ($plan->statusflag ?? null) === 'Y');
            $previousPlanMappingId = $previousPlan->planmappingid ?? null;
            $currentPlanMappingId = $currentPlan->planmappingid
                ?? DB::table(self::$deptartment_table)->where('deptcode', $deptcode)->value('planmappingid');
            $spilloverExcludedDeptcodes = ['04'];
            $skipSpilloverInstitutionFetch = in_array($deptcode, $spilloverExcludedDeptcodes, true);
            // dd($previousPlanMappingId, $currentPlanMappingId);

            $results = DB::table('audit.mst_institution as inst')
                ->distinct()
                ->select(
                    'inst.instename',
                    'inst.insttname',
                    'inst.instid',
                    'inst.mandays',
                    'inst.remainingmandays',
                    'inst.teamsize',
                    DB::raw("'N' as spillover")
                )
                ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'inst.deptcode')
                ->whereNotExists(function ($query) use ($currentPlanMappingId) {
                    $query->select(DB::raw(1))
                        ->from('audit.auditplan as planned')
                        ->whereColumn('planned.instid', 'inst.instid')
                        ->when($currentPlanMappingId, function ($subquery) use ($currentPlanMappingId) {
                            $subquery->where('planned.planmappingid', $currentPlanMappingId);
                        }, function ($subquery) {
                            $subquery->whereColumn('planned.planmappingid', 'dept.planmappingid');
                        });
                })
                ->where(function ($query) {
                    $query
                        ->where(function ($q) {
                            $q
                                ->where('dept.currentquarter', 'Q1')
                                ->whereRaw('inst."Q1" = ?', ['Y']);
                        })
                        ->orWhere(function ($q) {
                            $q
                                ->where('dept.currentquarter', 'Q2')
                                ->whereRaw('inst."Q2" = ?', ['Y']);
                        })
                        ->orWhere(function ($q) {
                            $q
                                ->where('dept.currentquarter', 'Q3')
                                ->whereRaw('inst."Q3" = ?', ['Y']);
                        })
                        ->orWhere(function ($q) {
                            $q
                                ->where('dept.currentquarter', 'Q4')
                                ->whereRaw('inst."Q4" = ?', ['Y']);
                        });
                })
                ->whereRaw('inst.inst_priority_kms = dept.inst_priority')
                ->whereNotExists(function ($subquery) {
                    $subquery->select(DB::raw(1))
                        ->from('audit.mapping_apiurl as ma')
                        ->where('ma.apifor', '=', 'E')
                        ->whereColumn('ma.catcode', 'inst.catcode')
                        ->whereRaw('ma.subcatid IS NOT DISTINCT FROM inst.subcatid');
                })
                ->where('inst.deptcode', $deptcode)
                ->where('inst.regioncode', $regioncode)
                ->where('inst.distcode', $distcode)
                ->get();

            $spilloverResults = collect();
            if ($previousPlanMappingId && $currentPlanMappingId && ! $skipSpilloverInstitutionFetch) {
                $spilloverResults = DB::table('audit.mst_institution as inst')
                    ->join('audit.auditplan as prev_plan', 'prev_plan.instid', '=', 'inst.instid')
                    ->join('audit.inst_auditschedule as schedule', 'schedule.auditplanid', '=', 'prev_plan.auditplanid')
                    ->where('inst.deptcode', $deptcode)
                    ->where('inst.regioncode', $regioncode)
                    ->where('inst.distcode', $distcode)
                    ->where('prev_plan.planmappingid', $previousPlanMappingId)
                    // ->where('prev_plan.spilloverflag', 'Y')
                    ->where(function ($query) {
                        $query->where('schedule.spillovercompleted', 'N');
                        // ->orWhereNull('schedule.spillovercompleted');
                    })
                    ->whereNotExists(function ($query) use ($currentPlanMappingId) {
                        $query->select(DB::raw(1))
                            ->from('audit.auditplan as current_plan')
                            ->whereColumn('current_plan.instid', 'inst.instid')
                            ->where('current_plan.planmappingid', $currentPlanMappingId);
                    })
                    ->select(
                        'inst.instename',
                        'inst.insttname',
                        'inst.instid',
                        'inst.mandays',
                        'inst.remainingmandays',
                        'inst.teamsize',
                        DB::raw("'Y' as spillover")
                    )
                    ->distinct()
                    ->get();
            }

            $selectedInstitution = collect();
            $selectedInstitutionSpilloverSql = "CASE WHEN COALESCE(selected_plan.spilloverflag, 'N') = 'Y'
                        OR EXISTS (
                            SELECT 1
                            FROM audit.auditplan prev_plan
                            JOIN audit.inst_auditschedule schedule
                                ON schedule.auditplanid = prev_plan.auditplanid
                            WHERE prev_plan.instid = inst.instid
	                              AND prev_plan.planmappingid = ".(int) $previousPlanMappingId."
                              AND prev_plan.spilloverflag = 'Y'
                              AND schedule.spillovercompleted = 'N'
                        ) THEN 'Y' ELSE 'N' END as spillover";
            if ($instid) {
                $selectedInstitution = DB::table('audit.mst_institution as inst')
                    ->leftJoin('audit.auditplan as selected_plan', function ($join) use ($currentPlanMappingId) {
                        $join->on('selected_plan.instid', '=', 'inst.instid');

                        if ($currentPlanMappingId) {
                            $join->where('selected_plan.planmappingid', $currentPlanMappingId);
                        }
                    })
                    ->where('inst.instid', $instid)
                    ->where('inst.deptcode', $deptcode)
                    ->where('inst.regioncode', $regioncode)
                    ->where('inst.distcode', $distcode)
                    ->select(
                        'inst.instename',
                        'inst.insttname',
                        'inst.instid',
                        'inst.mandays',
                        DB::raw('COALESCE(selected_plan.totalmandays, inst.remainingmandays) as remainingmandays'),
                        'inst.teamsize',
                        DB::raw($selectedInstitutionSpilloverSql)
                    )
                    ->orderByDesc('selected_plan.auditplanid')
                    ->limit(1)
                    ->get();
            }

            return $selectedInstitution
                ->merge($spilloverResults)
                ->merge($results)
                ->unique('instid')
                ->values();
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: '.$e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getManualSchemeTeamMembers($deptcode, $instid, $headUserid)
    {
        try {
            $schemePlan = DB::table(self::$deptartment_table.' as dept')
                ->join(self::$auditplanmapping_table.' as apm', 'apm.planmappingid', '=', 'dept.planmappingid')
                ->where('dept.deptcode', $deptcode)
                ->where('apm.manual_schemeplan', 'Y')
                ->whereNotNull('apm.ms_fromdate')
                ->whereNotNull('apm.ms_todate')
                ->select('dept.planmappingid', 'apm.ms_fromdate', 'apm.ms_todate')
                ->first();

            if (! $schemePlan) {
                return [
                    'scheme_active' => false,
                    'members' => collect(),
                ];
            }

            $team = DB::table(self::$auditplan_table.' as ap')
                ->join(self::$auditplanteam_table.' as team', 'team.auditplanteamid', '=', 'ap.auditteamid')
                ->join(self::$auditplanteammem_table.' as head', function ($join) use ($headUserid) {
                    $join->on('head.auditplanteamid', '=', 'team.auditplanteamid')
                        ->where('head.teamhead', 'Y')
                        ->where('head.statusflag', 'Y')
                        ->where('head.userid', $headUserid);
                })
                ->where('ap.instid', $instid)
                ->where('ap.planmappingid', $schemePlan->planmappingid)
                ->whereIn('ap.statusflag', ['S', 'F', 'Y'])
                ->where('team.statusflag', 'Y')
                ->select('team.auditplanteamid')
                ->orderByDesc('ap.auditplanid')
                ->first();

            if (! $team) {
                return [
                    'scheme_active' => true,
                    'members' => collect(),
                ];
            }

            $members = DB::table(self::$auditplanteammem_table.' as member')
                ->join(self::$userdetail_table.' as user', 'user.deptuserid', '=', 'member.userid')
                ->join(self::$designation_table.' as desig', 'desig.desigcode', '=', 'user.desigcode')
                ->where('member.auditplanteamid', $team->auditplanteamid)
                ->where('member.teamhead', 'N')
                ->where('member.statusflag', 'Y')
                ->select(
                    'user.deptuserid',
                    'user.username',
                    'desig.desigcode',
                    'desig.desigelname'
                )
                ->orderBy('user.username')
                ->get();

            return [
                'scheme_active' => true,
                'members' => $members,
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('SQL Error: '.$e->getMessage());
            throw new \Exception('A database error occurred while fetching scheme team members. Please contact the administrator.', 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getAuditors_manualplan($deptcode, $regioncode, $distcode, $auditteamid, $isreservedauditors)
    {
        // dd($auditteamid);
        try {
            $planmappingid = DB::table(self::$deptartment_table)
                ->where('deptcode', $deptcode)
                ->value('planmappingid');

            if ($auditteamid) {
                $excludedIds = DB::table('audit.auditplanteammember')
                    ->where('auditplanteamid', $auditteamid)
                    ->pluck('userid')
                    ->map(fn ($id) => (string) $id)
                    ->toArray();

                $newTeamHead = DB::table('audit.auditplanteammember')
                    ->where('auditplanteamid', $auditteamid)
                    ->where('teamhead', 'Y')
                    ->value('userid');

                if (! is_null($newTeamHead)) {
                    $excludedIds[] = (string) $newTeamHead;
                }

                $excludedIds = array_unique($excludedIds);  // deduplicate

                $hasOtherPlanForMapping = DB::table(self::$auditplan_table.' as ap')
                    ->join(self::$institution_table.' as inst', 'inst.instid', '=', 'ap.instid')
                    ->where('inst.deptcode', $deptcode)
                    ->where('inst.distcode', $distcode)
                    ->where('ap.planmappingid', $planmappingid)
                    ->where('ap.auditteamid', '<>', $auditteamid)
                    ->exists();

                if (! $hasOtherPlanForMapping) {
                    $dataquery = DB::table('audit.deptuserdetails as dept')
                        ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'dept.desigcode')
                        ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'dept.deptuserid')
                        ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
                        ->join('audit.rolemapping as rol', 'rol.rolemappingid', '=', 'ch.rolemappingid')
                        ->where('dept.deptcode', $deptcode)
                        ->where('dept.distcode', $distcode)
                        ->where('dept.reservelist', 'Y')
                        ->where('dept.statusflag', 'Y')
                        ->where('desig.belowaddesig', 'Y')
                        ->where('rol.roleactioncode', '04')
                        ->when(! empty($excludedIds), function ($query) use ($excludedIds) {
                            $query->whereNotIn('dept.deptuserid', $excludedIds);
                        })
                        ->selectRaw('
                            DISTINCT on (dept.deptuserid)
                            dept.deptuserid as userid,
                            dept.deptuserid,
                            dept.username,
                            desig.desigcode,
                            desig.desigelname
                        ')
                        ->orderBy('dept.deptuserid')
                        ->orderBy('dept.username', 'ASC')
                        ->get();

                    if ($isreservedauditors == 'Y') {
                        $reserveData = DB::table('audit.deptuserdetails as dp')
                            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'dp.deptuserid')
                            ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
                            ->join('audit.rolemapping as rol', 'rol.rolemappingid', '=', 'cd.rolemappingid')
                            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'cd.desigcode')
                            ->join('audit.mst_district as md', 'md.distcode', '=', 'dp.distcode')
                            ->where('uc.statusflag', '=', 'Y')
                            ->where('dp.statusflag', '=', 'Y')
                            ->where('dp.deptcode', $deptcode)
                            ->where('de.deptcode', $deptcode)
                            ->where('cd.regioncode', $regioncode)
                            ->where('dp.distcode', $distcode)
                            ->where('dp.reservelist', 'N')
                            ->where('rol.roleactioncode', '04')
                            ->when(! empty($excludedIds), function ($query) use ($excludedIds) {
                                $query->whereNotIn('dp.deptuserid', $excludedIds);
                            })
                            ->select(
                                'dp.deptuserid',
                                'dp.username',
                                'cd.chargedescription',
                                'md.distename',
                                'md.disttname',
                                'uc.userid',
                                'de.desigelname',
                                'de.desigcode',
                                'de.desigtlname'
                            )
                            ->orderBy('de.orderid')
                            ->orderBy('dp.username')
                            ->get();

                        return $dataquery->merge($reserveData)->values();
                    }

                    return $dataquery;
                }

                $data = DB::table('audit.auditplan as ap')
                    ->join('audit.auditplanteammember as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
                    ->join('audit.deptuserdetails as dept', 'dept.deptuserid', '=', 'apt.userid')
                    ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'dept.desigcode')
                    ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'dept.deptuserid')
                    ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
                    ->join('audit.rolemapping as rol', 'rol.rolemappingid', '=', 'ch.rolemappingid')
                    ->join('audit.mst_dept as de', 'de.deptcode', '=', 'dept.deptcode')
                    ->Join(DB::raw("
                    LATERAL (
                        SELECT
                            COUNT(*) FILTER (WHERE ap2.statusflag = 'F' and (inst.auditscheduleid is null or (inst.statusflag ='F' or inst.statusflag ='Y') )) AS total_plans,
                            COUNT(*) FILTER (WHERE ap2.statusflag = 'F' AND inst.exitmeetdate IS NOT NULL) AS scheduled_count,
                            MAX(inst.exitmeetdate) AS last_exitmeetdate,
                            MAX(ap2.todate) AS max_todate
                        FROM audit.auditplanteammember apt2
                        JOIN audit.auditplan ap2
                            ON ap2.auditteamid = apt2.auditplanteamid
	                        LEFT JOIN audit.inst_auditschedule inst
	                            ON inst.auditplanid = ap2.auditplanid
	                        WHERE apt2.userid = apt.userid
	                        AND ap2.auditquartercode = de.currentquarter
	                        AND ap2.planmappingid = de.planmappingid
	                    ) stats
	                "), DB::raw('TRUE'), DB::raw('TRUE'))
                    ->whereColumn('ap.auditquartercode', 'de.currentquarter')
                    ->whereColumn('ap.planmappingid', 'de.planmappingid')
                    ->where('dept.deptcode', $deptcode)
                    ->where('dept.distcode', $distcode)
                    ->where('dept.reservelist', 'Y')
                    ->where('dept.statusflag', 'Y')
                    ->where('desig.belowaddesig', 'Y')
                    ->when(! empty($excludedIds), function ($query) use ($excludedIds) {
                        $query->whereNotIn('apt.userid', $excludedIds);
                    })
                    ->where('rol.roleactioncode', '04')
                    ->groupBy(
                        'apt.userid',
                        'dept.deptuserid',
                        'dept.username',
                        'desig.desigelname',
                        'desig.desigcode',
                        'de.currentquartertodatewithcoolingperiod'
                    )
                    ->where(function ($q) {
                        $q
                            ->whereRaw('stats.max_todate < de.currentquartertodatewithcoolingperiod')
                            ->orWhere(function ($q2) {
                                $q2
                                    ->whereRaw('stats.total_plans > 0')
                                    ->whereRaw('stats.total_plans = stats.scheduled_count')
                                    ->whereRaw('stats.last_exitmeetdate::date <= current_date');
                            });
                    })
                    ->selectRaw('
                   DISTINCT on (apt.userid) apt.userid,
                    dept.username,
                    desig.desigcode,
                     dept.deptuserid,
                    desig.desigelname

                        ')
                    ->orderBy('apt.userid')
                    ->orderBy('dept.deptuserid')
                    ->orderBy('dept.username', 'ASC')
                    ->get();

                return $data;

                // print_r($data);

                // exit;
            } else {
                $hasPlanForMapping = DB::table(self::$auditplan_table.' as ap')
                    ->join(self::$institution_table.' as inst', 'inst.instid', '=', 'ap.instid')
                    ->where('inst.deptcode', $deptcode)
                    ->where('inst.distcode', $distcode)
                    ->where('ap.planmappingid', $planmappingid)
                    ->exists();

                if (! $hasPlanForMapping) {
                    $dataquery = DB::table('audit.deptuserdetails as dept')
                        ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'dept.desigcode')
                        ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'dept.deptuserid')
                        ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
                        ->join('audit.rolemapping as rol', 'rol.rolemappingid', '=', 'ch.rolemappingid')
                        ->where('dept.deptcode', $deptcode)
                        ->where('dept.distcode', $distcode)
                        ->where('dept.reservelist', 'Y')
                        ->where('dept.statusflag', 'Y')
                        ->where('desig.belowaddesig', 'Y')
                        ->where('rol.roleactioncode', '04')
                        ->selectRaw('
                                DISTINCT on (dept.deptuserid)
                                dept.deptuserid as userid,
                                dept.deptuserid,
                                dept.username,
                                desig.desigcode,
                                desig.desigelname
                            ')
                        ->orderBy('dept.deptuserid')
                        ->orderBy('dept.username', 'ASC')
                        ->get();

                    if ($isreservedauditors == 'Y') {
                        $reserveData = DB::table('audit.deptuserdetails as dp')
                            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'dp.deptuserid')
                            ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
                            ->join('audit.rolemapping as rol', 'rol.rolemappingid', '=', 'cd.rolemappingid')
                            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'cd.desigcode')
                            ->join('audit.mst_district as md', 'md.distcode', '=', 'dp.distcode')
                            ->where('uc.statusflag', '=', 'Y')
                            ->where('dp.statusflag', '=', 'Y')
                            ->where('dp.deptcode', $deptcode)
                            ->where('de.deptcode', $deptcode)
                            ->where('cd.regioncode', $regioncode)
                            ->where('dp.distcode', $distcode)
                            ->where('dp.reservelist', 'N')
                            ->where('rol.roleactioncode', '04')
                            ->select(
                                'dp.deptuserid',
                                'dp.username',
                                'cd.chargedescription',
                                'md.distename',
                                'md.disttname',
                                'uc.userid',
                                'de.desigelname',
                                'de.desigcode',
                                'de.desigtlname'
                            )
                            ->orderBy('de.orderid')
                            ->orderBy('dp.username')
                            ->get();

                        return $dataquery->merge($reserveData)->values();
                    }

                    return $dataquery;
                }

                $data = DB::table('audit.auditplan as ap')
                    ->join('audit.auditplanteammember as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
                    ->join('audit.deptuserdetails as dept', 'dept.deptuserid', '=', 'apt.userid')
                    ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'dept.desigcode')
                    ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'dept.deptuserid')
                    ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
                    ->join('audit.rolemapping as rol', 'rol.rolemappingid', '=', 'ch.rolemappingid')
                    ->join('audit.mst_dept as de', 'de.deptcode', '=', 'dept.deptcode')
                    ->Join(DB::raw("
                    LATERAL (
                        SELECT
                             COUNT(*) FILTER (WHERE ap2.statusflag = 'F' and (inst.auditscheduleid is null or (inst.statusflag ='F' or inst.statusflag ='Y') )) AS total_plans,
                            COUNT(*) FILTER (WHERE ap2.statusflag = 'F' AND inst.exitmeetdate IS NOT NULL) AS scheduled_count,
                            MAX(inst.exitmeetdate) AS last_exitmeetdate,
                            MAX(ap2.todate) AS max_todate
                        FROM audit.auditplanteammember apt2
                        JOIN audit.auditplan ap2
                            ON ap2.auditteamid = apt2.auditplanteamid
	                        LEFT JOIN audit.inst_auditschedule inst
	                            ON inst.auditplanid = ap2.auditplanid
	                        WHERE apt2.userid = apt.userid
	                        AND ap2.planmappingid = de.planmappingid

	                    ) stats
	                "), DB::raw('TRUE'), DB::raw('TRUE'))
                    // ->whereColumn('ap.auditquartercode', 'de.currentquarter')
                    ->whereColumn('ap.planmappingid', 'de.planmappingid')
                    ->where('dept.deptcode', $deptcode)
                    ->where('dept.distcode', $distcode)
                    ->where('dept.reservelist', 'Y')
                    ->where('dept.statusflag', 'Y')
                    ->where('desig.belowaddesig', 'Y')
                    ->where('rol.roleactioncode', '04')
                    ->groupBy(
                        'apt.userid',
                        'dept.username',
                        'desig.desigelname',
                        'desig.desigcode',
                        'dept.deptuserid',
                        'de.currentquartertodatewithcoolingperiod'
                    )
                    ->where(function ($q) {
                        $q
                            ->whereRaw('stats.max_todate < de.currentquartertodatewithcoolingperiod')
                            ->orWhere(function ($q2) {
                                $q2
                                    ->whereRaw('stats.total_plans > 0')
                                    ->whereRaw('stats.total_plans = stats.scheduled_count')
                                    ->whereRaw('stats.last_exitmeetdate::date <= current_date');
                            });
                    })
                    ->selectRaw('
                     DISTINCT on (apt.userid)
                     apt.userid,
                     dept.deptuserid,
                    dept.username,
                     desig.desigcode,
                    desig.desigelname

                        ');

                // $querySql = $data->toSql();
                // $bindings = $data->getBindings();

                // $finalQuery = vsprintf(
                //     str_replace('?', "'%s'", $querySql),
                //     array_map('addslashes', $bindings)
                // );

                // print_r($finalQuery);
                // exit;

                $dataquery = $data->get();

                if ($isreservedauditors == 'Y') {
                    $reserveData = DB::table('audit.deptuserdetails as dp')
                        ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'dp.deptuserid')
                        ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
                        ->join('audit.rolemapping as rol', 'rol.rolemappingid', '=', 'cd.rolemappingid')
                        ->join('audit.mst_designation as de', 'de.desigcode', '=', 'cd.desigcode')
                        ->join('audit.mst_district as md', 'md.distcode', '=', 'dp.distcode')
                        ->where('uc.statusflag', '=', 'Y')
                        ->where('dp.statusflag', '=', 'Y')
                        ->where('dp.deptcode', $deptcode)
                        ->where('de.deptcode', $deptcode)
                        ->where('cd.regioncode', $regioncode)
                        ->where('dp.distcode', $distcode)
                        ->where('dp.reservelist', 'N')
                        ->where('rol.roleactioncode', '04')
                        ->select(
                            'dp.deptuserid',
                            'dp.username',
                            'cd.chargedescription',
                            'md.distename',
                            'md.disttname',
                            'uc.userid',
                            'de.desigelname',
                            'de.desigcode',
                            'de.desigtlname'
                        )
                        ->orderBy('de.orderid')
                        ->orderBy('dp.username')
                        ->orderBy('dp.username')
                        ->get();
                    $merged = $dataquery->merge($reserveData);

                    return $merged->values();
                }

                return $dataquery;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetchings. Please contact the administrator.';

            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public function fetchupdatemanualplan($auditteamid = null)
    {
        $data = DB::table('audit.audit_teams_draft as atd')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'atd.auditplanid')
            ->join('audit.auditplanteam as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->join('audit.mst_institution as inst', 'ap.instid', '=', 'inst.instid')
            ->join('audit.mst_dept as dept', 'inst.deptcode', '=', 'dept.deptcode')
            ->join('audit.mst_region as region', 'inst.regioncode', '=', 'region.regioncode')
            ->join('audit.mst_district as dist', 'inst.distcode', '=', 'dist.distcode')
            ->join('audit.deptuserdetails as oldhead', 'oldhead.deptuserid', '=', 'atd.oldteamhead')
            ->join('audit.mst_designation as olddes', 'olddes.desigcode', '=', 'oldhead.desigcode')
            ->join('audit.deptuserdetails as newhead', 'newhead.deptuserid', '=', 'atd.newteamhead')
            ->join('audit.mst_designation as newdes', 'newdes.desigcode', '=', 'newhead.desigcode')
            ->leftJoin('audit.fileuploaddetail as fu', 'fu.fileuploadid', '=', 'atd.fileuploadid')
            ->leftJoin(DB::raw("(SELECT mid.instid, COUNT(*) AS membercount
            FROM audit.map_instdesig AS mid
            JOIN audit.auditplan AS ap ON ap.instid = mid.instid
            WHERE mid.teamhead = 'N'
            GROUP BY mid.instid) AS member_counts"), 'member_counts.instid', '=', 'ap.instid')
            ->where('inst.statusflag', 'Y')
            ->where('dept.statusflag', 'Y')
            ->where('region.statusflag', 'Y')
            ->where('dist.statusflag', 'Y');

        if (! empty($auditteamid)) {
            $data->where('atd.auditteamsdraftid', $auditteamid);
        }

        $data = $data
            ->select(
                DB::raw("CASE
	            WHEN atd.fileuploadid != 0 THEN CONCAT(fu.filename, ' - ', fu.filepath, ' - ', fu.filesize, ' - ', fu.fileuploadid)
	            ELSE ' - '
	        END AS filedetails"),
                'atd.*',
                'inst.deptcode',
                'inst.instename',
                DB::raw("CASE WHEN COALESCE(ap.spilloverflag, 'N') = 'Y'
	                    OR EXISTS (
	                        SELECT 1
	                        FROM audit.auditplan prev_plan
	                        JOIN audit.auditplanmapping prev_map
	                            ON prev_map.planmappingid = prev_plan.planmappingid
	                        JOIN audit.inst_auditschedule schedule
	                            ON schedule.auditplanid = prev_plan.auditplanid
	                        WHERE prev_plan.instid = inst.instid
	                          AND prev_map.deptcode = inst.deptcode
	                          AND prev_map.statusflag = 'P'
	                          AND prev_plan.spilloverflag = 'Y'
	                          AND schedule.spillovercompleted = 'N'
	                    )
	                    THEN 'Y' ELSE 'N' END as spillover"),
                'dept.deptelname',
                'region.regionename',
                'inst.regioncode',
                'inst.distcode',
                'ap.auditplanid',
                'auditteamsdraftid',
                'dist.distename',
                DB::raw("oldhead.username || ' - ' || olddes.desigelname || ' - ' || oldhead.deptuserid AS oldteamheadname"),
                DB::raw("newhead.username || ' - ' || newdes.desigelname || ' - ' || newhead.deptuserid AS newteamheadname"),
                'member_counts.membercount',
                // Safely handle the JSONB fields using a CASE WHEN for valid JSONB arrays
                DB::raw("(SELECT string_agg(dud.username || ' - ' || des.desigelname || ' - ' || dud.deptuserid || ' - '|| des.desigcode  , ', ')
                  FROM jsonb_array_elements_text(CASE
                      WHEN jsonb_typeof(atd.oldteammembers) = 'array' THEN atd.oldteammembers
                      ELSE '[]'::jsonb
                  END) AS memberid
                  JOIN audit.deptuserdetails AS dud ON dud.deptuserid = memberid::int
                  JOIN audit.mst_designation AS des ON des.desigcode = dud.desigcode) AS oldteammembernames"),
                DB::raw("(SELECT string_agg(dud.username || ' - ' || des.desigelname || ' - ' || dud.deptuserid || ' - '|| des.desigcode , ', ')
                  FROM jsonb_array_elements_text(CASE
                      WHEN jsonb_typeof(atd.newteammembers) = 'array' THEN atd.newteammembers
                      ELSE '[]'::jsonb
                  END) AS memberid
                  JOIN audit.deptuserdetails AS dud ON dud.deptuserid = memberid::int
                  JOIN audit.mst_designation AS des ON des.desigcode = dud.desigcode) AS newteammembernames")
            )
            ->orderBy('dept.deptelname', 'asc')
            ->get();

        return $data;
    }

    public static function updatemanualplan(array $data, $auditteamId, $auditplanid)
    {
        try {
            $session = session('user');
            $userid = $session->userid;

            //  $auditplanid = $data['auditplanid'];
            $finaliseflag = $data['statusflag'];

            //  return $data;
            //  exit;
            // $quoted = array_map(fn($v) => '"' . $v . '"', $data['newteammembers']);
            // $datatypes = '{' . implode(',', $quoted) . '}';
            // return $data['newteammembers'];
            $manualplanstatus = self::runManualPlanWithMandays($data, $auditplanid, $userid);

            // dd($manualplanstatus);
            return $manualplanstatus;
        } catch (\Exception $e) {
            DB::rollBack();  // ? ROLLBACK on exception

            return [
                'status' => false,
                'type' => 'error',
                'message' => 'Update failed: '.$e->getMessage(),
            ];
        }
    }

    private static function runManualPlanWithMandays(array $data, $auditplanid, $userid)
    {
        if (($data['is_spillover'] ?? 'N') !== 'Y') {
            return self::executeManualPlanFunction($data, $auditplanid, $userid);
        }

        return DB::transaction(function () use ($data, $auditplanid, $userid) {
            $institution = DB::table(self::$institution_table)
                ->where('instid', $data['instid'])
                ->lockForUpdate()
                ->select('mandays', 'remainingmandays', 'teamsize')
                ->first();

            $remainingMandays = $institution->remainingmandays ?? null;
            $teamSize = $institution->teamsize ?? null;

            if ($institution && $remainingMandays !== null && $remainingMandays !== '') {
                DB::table(self::$institution_table)
                    ->where('instid', $data['instid'])
                    ->update(['mandays' => $remainingMandays]);
            }

            $manualPlanStatus = self::executeManualPlanFunction($data, $auditplanid, $userid);
	            $manualPlanStatus = self::applySpilloverWorkingDayDate(
	                $manualPlanStatus,
	                $data,
	                $auditplanid,
	                $remainingMandays,
	                $teamSize
	            );

	            if ($institution) {
	                DB::table(self::$institution_table)
	                    ->where('instid', $data['instid'])
	                    ->update(['mandays' => $institution->mandays]);
            }

            return $manualPlanStatus;
        });
    }

    private static function applySpilloverWorkingDayDate($manualPlanStatus, array $data, $auditplanid, $remainingMandays, $teamSize)
    {
        if (! $manualPlanStatus || ! isset($manualPlanStatus[0]->manualplan) || ! $remainingMandays || ! $teamSize) {
            return $manualPlanStatus;
        }

        $resultData = json_decode($manualPlanStatus[0]->manualplan, true);

        if (! is_array($resultData) || empty($resultData['fromdate'])) {
            return $manualPlanStatus;
        }

        $fromDate = Carbon::parse($resultData['fromdate'])->toDateString();
        $toDate = self::calculateToDateWithoutHolidays($fromDate, $remainingMandays, $teamSize);
        $autoplanDate = self::getCurrentPlanAutoplanDate($data['deptcode'] ?? null);
        $isAutoplanCapped = false;
        $carryForwardMandays = 0;

        if (! $toDate) {
            return $manualPlanStatus;
        }

        if ($autoplanDate && Carbon::parse($toDate)->gt(Carbon::parse($autoplanDate))) {
            $isAutoplanCapped = true;
            $toDate = Carbon::parse($autoplanDate)->toDateString();
            $workingDaysBeforeAutoplan = self::countWorkingDaysWithoutHolidays($fromDate, $toDate);
            $completedMandays = $workingDaysBeforeAutoplan * max(1, (int) $teamSize);
            $carryForwardMandays = max(0, (int) $remainingMandays - $completedMandays);
        }

        $resultData['todate'] = $toDate;
        $resultData['spillover_autoplandate_capped'] = $isAutoplanCapped ? 'Y' : 'N';
        $resultData['spillover_autoplandate'] = $autoplanDate;
        $resultData['spillover_carryforward_mandays'] = $carryForwardMandays;
        $currentQuarterMandays = self::countWorkingDaysWithoutHolidays($fromDate, $toDate) * max(1, (int) $teamSize);
        $resultData['spillover_currentquarter_mandays'] = $currentQuarterMandays;
        $resultData['spillover_totalmandays'] = (int) $remainingMandays;
        $manualPlanStatus[0]->manualplan = json_encode($resultData);

        if (($data['formparam'] ?? '') !== 'check') {
            self::updateManualPlanToDate(
                $data,
                $auditplanid,
                $fromDate,
                $toDate,
                $isAutoplanCapped,
                $currentQuarterMandays,
                (int) $remainingMandays
            );
        }

        return $manualPlanStatus;
    }

    private static function getCurrentPlanAutoplanDate($deptcode)
    {
        if (! $deptcode) {
            return null;
        }

        $autoplanDate = DB::table(self::$deptartment_table.' as dept')
            ->join(self::$auditplanmapping_table.' as apm', 'apm.planmappingid', '=', 'dept.planmappingid')
            ->where('dept.deptcode', $deptcode)
            ->value('apm.autoplandate');

        return $autoplanDate ? Carbon::parse($autoplanDate)->toDateString() : null;
    }

    private static function calculateToDateWithoutHolidays($fromDate, $mandays, $teamSize)
    {
        $workingDaysNeeded = (int) ceil(((float) $mandays) / max(1, (int) $teamSize));
        $workingDaysNeeded = max(1, $workingDaysNeeded);

        $result = DB::selectOne(
            "
	            WITH working_days AS (
	                SELECT
	                    d::date AS working_date,
	                    ROW_NUMBER() OVER (ORDER BY d::date) AS rn
	                FROM generate_series(CAST(? AS date), CAST(? AS date) + interval '366 days', interval '1 day') AS d
	                WHERE EXTRACT(DOW FROM d) NOT IN (0, 6)
	                AND NOT EXISTS (
	                    SELECT 1
	                    FROM audit.mst_holiday h
	                    WHERE h.holiday_date = d::date
	                    AND COALESCE(h.statusflag, 'Y') = 'Y'
	                )
	            )
	            SELECT working_date
	            FROM working_days
	            WHERE rn = ?
	            ",
            [$fromDate, $fromDate, $workingDaysNeeded]
        );

        return $result->working_date ?? null;
    }

    private static function countWorkingDaysWithoutHolidays($fromDate, $toDate)
    {
        $result = DB::selectOne(
            "
	            SELECT COUNT(*) AS working_days
	            FROM generate_series(CAST(? AS date), CAST(? AS date), interval '1 day') AS d
	            WHERE EXTRACT(DOW FROM d) NOT IN (0, 6)
	            AND NOT EXISTS (
	                SELECT 1
	                FROM audit.mst_holiday h
	                WHERE h.holiday_date = d::date
	                AND COALESCE(h.statusflag, 'Y') = 'Y'
	            )
	            ",
            [$fromDate, $toDate]
        );

        return (int) ($result->working_days ?? 0);
    }

    private static function updateManualPlanToDate(
        array $data,
        $auditplanid,
        $fromDate,
        $toDate,
        $isAutoplanCapped = false,
        $currentQuarterMandays = null,
        $totalMandays = null
    ) {
        $planId = $auditplanid ?: null;

        if (! $planId) {
            $currentPlanMappingId = DB::table(self::$deptartment_table)
                ->where('deptcode', $data['deptcode'])
                ->value('planmappingid');

            $plan = DB::table(self::$auditplan_table)
                ->where('instid', $data['instid'])
                ->when($currentPlanMappingId, function ($query) use ($currentPlanMappingId) {
                    $query->where('planmappingid', $currentPlanMappingId);
                })
                ->whereDate('fromdate', $fromDate)
                ->orderByDesc('auditplanid')
                ->select('auditplanid')
                ->first();

            $planId = $plan->auditplanid ?? null;
        }

        if ($planId) {
            $updateData = ['todate' => $toDate];

            if ($currentQuarterMandays !== null) {
                $updateData['mandays'] = $currentQuarterMandays;
            }

            if ($totalMandays !== null) {
                $updateData['totalmandays'] = $totalMandays;
            }

	            if ($isAutoplanCapped) {
	                $updateData['carryforwardflag'] = 'Y';
	                $updateData['spilloverflag'] = 'Y';
	            }

	            DB::table(self::$auditplan_table)
	                ->where('auditplanid', $planId)
	                ->update($updateData);
	        }
	    }

	    private static function executeManualPlanFunction(array $data, $auditplanid, $userid)
	    {
		        return DB::select(
	            'SELECT * FROM audit.manualplan'.'(:deptcode,:regioncode, :distcode, :instid,:newteamhead,:newteammembers,:planid,:userid,:formparam,:isspillover)',
	            [
	                'deptcode' => $data['deptcode'],
	                'regioncode' => $data['regioncode'],
	                'distcode' => $data['distcode'],
	                'instid' => $data['instid'],
                'newteamhead' => $data['newteamhead'],
                'newteammembers' => $data['newteammembers'],
	                'planid' => $auditplanid,
	                'userid' => $userid,
	                'formparam' => $data['formparam'],
	                'isspillover' => $data['is_spillover'] ?? 'N',
	            ]
	        );
    }

    // -------------------------------------------Manual Plan End----------------------------

    // -------------------------------------------Quarter Transaction Start----------------------------

    private static function getInstChangePriorityLabel(?string $priorityCode): string
    {
        $priorityCode = $priorityCode !== null ? trim((string) $priorityCode) : null;

        return match ($priorityCode) {
            '01' => 'P1',
            '02' => 'P2',
            default => '',
        };
    }

    public static function getInstChangePlanContext($deptcode = null): array
    {
        $deptcode = $deptcode ?? (session('charge')->deptcode ?? null);

        $defaultContext = [
            'previous' => null,
            'current' => null,
            'next' => null,
            'moveToOptions' => [],
            'byPlanMappingId' => [],
            'byOptionValue' => [],
            'byQuarterCode' => [],
            'currentquarter' => null,
            'previousquarter' => null,
            'nextquarter' => null,
            'phaseLabel' => '',
            'nextPriorityLabel' => '',
            'currentfincode' => null,
            'currentFinancialYear' => null,
            'currentFinancialYearLabel' => '',
            'tofincode' => null,
            'toFinancialYear' => null,
            'toFinancialYearLabel' => '',
        ];

        if (empty($deptcode)) {
            return $defaultContext;
        }

        try {
            $result = collect(CommonModel::getplandetailsWithPrev($deptcode))->values();
            // dd($result);
            $normalizeDetail = function ($detail, string $quarterType): array {
                $priorityCode = isset($detail->prioritycode) && trim((string) $detail->prioritycode) !== ''
                    ? trim((string) $detail->prioritycode)
                    : null;

                $normalizedDetail = [
                    'quarter_type' => $quarterType,
                    'source_quarter_type' => $detail->quarter_type ?? null,
                    'auditquartercode' => $detail->auditquartercode ?? null,
                    'financialyearcode' => $detail->financialyearcode ?? null,
                    'financialyear' => $detail->financialyear ?? null,
                    'prioritycode' => $priorityCode,
                    'prioritylabel' => self::getInstChangePriorityLabel($priorityCode),
                    'planmappingid' => $detail->planmappingid ?? null,
                    'planname' => $detail->planname ?? null,
                    'fromdate' => $detail->fromdate ?? null,
                    'todate' => $detail->todate ?? null,
                    'statusflag' => $detail->statusflag ?? null,
                ];

                $normalizedDetail['optionvalue'] = ! empty($normalizedDetail['planmappingid'])
                    ? (string) $normalizedDetail['planmappingid']
                    : (string) ($normalizedDetail['auditquartercode'] ?? '');

                $normalizedDetail['optionlabel'] = trim(
                    $normalizedDetail['planname'] ?: ($normalizedDetail['auditquartercode'] ?? '')
                );

                return $normalizedDetail;
            };

            $context = $defaultContext;
            $currentRow = $result->get(0);
            $nextRows = $result->slice(1)->values();

            if ($currentRow) {
                $context['current'] = $normalizeDetail($currentRow, 'CURRENT');
            }

            $moveToOptions = $nextRows
                ->map(fn ($detail) => $normalizeDetail($detail, 'NEXT'))
                ->filter(fn ($detail) => ! empty($detail['auditquartercode']))
                ->values()
                ->all();

            $context['moveToOptions'] = $moveToOptions;
            $context['next'] = $moveToOptions[0] ?? null;

            if (! empty($context['current']['auditquartercode'])) {
                $context['byQuarterCode'][$context['current']['auditquartercode']] = $context['current'];
            }
            if (! empty($context['current']['planmappingid'])) {
                $context['byPlanMappingId'][$context['current']['planmappingid']] = $context['current'];
            }

            foreach ($moveToOptions as $detail) {
                if (! empty($detail['planmappingid'])) {
                    $context['byPlanMappingId'][$detail['planmappingid']] = $detail;
                }
                if (! empty($detail['optionvalue'])) {
                    $context['byOptionValue'][$detail['optionvalue']] = $detail;
                }
                if (empty($detail['auditquartercode'])) {
                    continue;
                }
                $context['byQuarterCode'][$detail['auditquartercode']] = $detail;
            }

            $current = $context['current'];
            $next = $context['next'];

            $context['currentquarter'] = $current['auditquartercode'] ?? null;
            $context['nextquarter'] = $next['auditquartercode'] ?? null;
            $context['phaseLabel'] = $current['prioritylabel'] ?? '';
            $context['nextPriorityLabel'] = $next['prioritylabel'] ?? '';
            $context['currentfincode'] = $current['financialyearcode'] ?? null;
            $context['currentFinancialYear'] = $current['financialyear'] ?? null;
            $context['currentFinancialYearLabel'] = $current['financialyear'] ?? '';
            $context['tofincode'] = $next['financialyearcode'] ?? null;
            $context['toFinancialYear'] = $next['financialyear'] ?? null;
            $context['toFinancialYearLabel'] = $next['financialyear'] ?? '';

            if ($context['currentquarter'] || $context['nextquarter']) {
                return $context;
            }
        } catch (\Throwable $e) {
            \Log::warning('Failed to fetch instchange plan details via fn_getplandetails', [
                'deptcode' => $deptcode,
                'message' => $e->getMessage(),
            ]);
        }

        return $defaultContext;
    }

    public static function getInstChangePlanDetailByType(string $quarterType, $deptcode = null): ?array
    {
        $context = self::getInstChangePlanContext($deptcode);

        return match (strtoupper(trim($quarterType))) {
            'CURRENT' => $context['current'] ?? null,
            'NEXT' => $context['next'] ?? null,
            default => null,
        };
    }

    public static function getInstChangeQuarterCodeByType(string $quarterType, $deptcode = null): ?string
    {
        $detail = self::getInstChangePlanDetailByType($quarterType, $deptcode);

        return $detail['auditquartercode'] ?? null;
    }

    public static function fetchSpilloverWithCount($deptcode = null, $distcode = null)
    {

        try {
            if (empty($deptcode) || empty($distcode)) {
                $session = session('charge');
                $deptcode = $deptcode ?? ($session->deptcode ?? null);
                $distcode = $distcode ?? ($session->distcode ?? null);
            }

            if (empty($deptcode) || empty($distcode)) {
                throw new \Exception('Department code or district code not found.');
            }

            // $pendingStatus = DB::table('audit.auditor_instmapping')
            //     ->where('deptcode', $deptcode)
            //     ->where('distcode', $distcode)
            //     ->value('pendinginststatus');

            $statuses = DB::table('audit.auditor_instmapping')
                ->where('deptcode', $deptcode)
                ->where('distcode', $distcode)
                ->select('pendinginststatus')
                ->first();

            if ($statuses->pendinginststatus === 'N') {
                $result = DB::selectOne(
                    'SELECT audit.get_spillover_institutions(:deptcode, :distcode) AS json_data',
                    [
                        'deptcode' => $deptcode,
                        'distcode' => $distcode,
                    ]
                );
                $jsonData = $result && $result->json_data ? json_decode($result->json_data, true) : [];

                $spilloverData = $jsonData['data'] ?? [];
                $overallInstCount = $jsonData['count'] ?? 0;

                return [
                    'spilloverData' => array_values($spilloverData),
                    'overallInstCount' => $overallInstCount,
                ];
            }

            return [
                'spilloverData' => [],
                'overallInstCount' => 0,
            ];
        } catch (\Throwable $e) {
            \Log::error('Error fetching spillover data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'deptcode' => $deptcode,
                'distcode' => $distcode,
            ]);

            return [
                'spilloverData' => [],
                'overallInstCount' => 0,
            ];
        }
    }

    public static function fetchspilloverQuarterDetails($spillous, $deptcode = null)
    {
        $sessionchargedel = session('charge');
        $deptcode = $deptcode ?: ($sessionchargedel->deptcode ?? null);

        $instids = collect($spillous['spilloverData'] ?? [])
            ->pluck('instid')
            ->filter()
            ->unique()
            ->toArray();

        if (empty($instids)) {
            return [
                'selected_quarters' => collect(),
                'quarter_starts_from' => collect(),
            ];
        }

        $planContext = self::getInstChangePlanContext($deptcode);
        $currentquarter = $planContext['currentquarter'] ?? null;

        $orderMapping = DB::raw("
        CASE ap.auditquartercode
            WHEN 'Q1' THEN 1
            WHEN 'Q2' THEN 2
            WHEN 'Q3' THEN 3
            WHEN 'Q4' THEN 4
        END
    ");

        $quarters = DB::table('audit.auditplan as ap')
            ->join('audit.inst_auditschedule as ia', 'ia.auditplanid', '=', 'ap.auditplanid')
            ->select(
                'ap.instid',
                'ap.auditquartercode',
                'ap.carryforwardflag',
                'ia.exitmeetdate',
                'ia.proposedexitmeetdate'
            )
            ->whereIn('ap.instid', $instids)
            ->orderBy('ap.instid')
            ->orderBy($orderMapping)
            ->get()
            ->groupBy('instid')
            ->map(function ($rows) {
                $instid = $rows->first()->instid;

                // All carry forward
                if ($rows->every(fn ($r) => $r->carryforwardflag === 'Y')) {
                    $r = $rows->first();

                    return [
                        'instid' => $instid,
                        'auditquartercode' => $r->auditquartercode,
                        'carryforwardflag' => 'Y',
                        'exitmeetdate' => $r->exitmeetdate,
                        'proposedexitmeetdate' => $r->proposedexitmeetdate,
                        'all_carryforward' => true,
                    ];
                }

                // Q1 priority
                $q1 = $rows->firstWhere('auditquartercode', 'Q1');
                if ($q1 && $q1->carryforwardflag === 'Y') {
                    return [
                        'instid' => $instid,
                        'auditquartercode' => 'Q1',
                        'carryforwardflag' => 'Y',
                        'exitmeetdate' => $q1->exitmeetdate,
                        'proposedexitmeetdate' => $q1->proposedexitmeetdate,
                        'value' => 1,
                    ];
                }

                // First carryforward quarter
                $carry = $rows->firstWhere('carryforwardflag', 'Y');
                if ($carry) {
                    return [
                        'instid' => $instid,
                        'auditquartercode' => $carry->auditquartercode,
                        'carryforwardflag' => 'Y',
                        'exitmeetdate' => $carry->exitmeetdate,
                        'proposedexitmeetdate' => $carry->proposedexitmeetdate,
                    ];
                }

                // Fallback → earliest quarter
                $first = $rows->first();

                return [
                    'instid' => $instid,
                    'auditquartercode' => $first->auditquartercode,
                    'carryforwardflag' => $first->carryforwardflag,
                    'exitmeetdate' => $first->exitmeetdate,
                    'proposedexitmeetdate' => $first->proposedexitmeetdate,
                ];
            })
            ->values();

        $quarterstartsfrom = DB::table('audit.auditplan as ap')
            ->join('audit.inst_auditschedule as ia', 'ia.auditplanid', '=', 'ap.auditplanid')
            ->select(
                'ap.instid',
                'ap.auditquartercode',
                'ap.carryforwardflag',
                'ia.exitmeetdate',
                'ia.proposedexitmeetdate'
            )
            ->whereIn('ap.instid', $instids)
            ->where('ap.auditquartercode', $currentquarter)
            ->orderBy('ap.instid')
            ->get();

        return [
            'selected_quarters' => $quarterstartsfrom,
            'quarter_starts_from' => $quarters,
        ];
    }

    // public static function getnotscheduleinstData()
    // {
    //     $sessionCharge = session('charge');

    //     $deptcode = $sessionCharge->deptcode ?? null;
    //     $regioncode = $sessionCharge->regioncode ?? null;
    //     $distcode = $sessionCharge->distcode ?? null;

    //     if (!$deptcode || !$regioncode || !$distcode) {
    //         return collect();
    //     }

    //     $planContext = self::getInstChangePlanContext($deptcode);
    //     $currentPlan = $planContext['current'] ?? null;
    //     $quarter = $currentPlan['auditquartercode'] ?? null;
    //     $currentPlanMappingId = $currentPlan['planmappingid'] ?? null;
    //     $priority = $currentPlan['prioritycode'] ?? null;
    //     $checkPriority = $priority !== null;

    //     if (!$quarter || !$currentPlanMappingId) {
    //         return collect();
    //     }

    //     $query = DB::table('audit.auditplan as ap')
    //         ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
    //         ->leftJoin('audit.inst_auditschedule as asch', 'asch.auditplanid', '=', 'ap.auditplanid')
    //         ->join('audit.mst_dept as de', 'de.deptcode', '=', 'inst.deptcode')
    //         ->select(
    //             'ap.*',
    //             'de.currentquarter',
    //             'de.nextquarter',
    //             'inst.instename',
    //             'inst.mandays'
    //         );

    //     $query
    //         ->where('ap.planmappingid', $currentPlanMappingId)
    //         ->where(function ($q) use ($deptcode, $regioncode, $distcode, $quarter, $checkPriority, $priority, $currentPlanMappingId) {
    //             $q
    //                 ->whereNotIn('ap.auditplanid', function ($sub) use ($deptcode, $regioncode, $distcode, $currentPlanMappingId) {
    //                     $sub
    //                         ->select('ap.auditplanid')
    //                         ->from('audit.inst_auditschedule as asch')
    //                         ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'asch.auditplanid')
    //                         ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
    //                         ->where('ap.planmappingid', $currentPlanMappingId)
    //                         ->where('inst.deptcode', $deptcode)
    //                         ->where('inst.regioncode', $regioncode)
    //                         ->where('inst.distcode', $distcode)
    //                         ->where('inst.statusflag', 'Y');
    //                 })
    //                 ->where('inst.deptcode', $deptcode)
    //                 ->where('inst.regioncode', $regioncode)
    //                 ->where('inst.distcode', $distcode)
    //                 ->where('inst.statusflag', 'Y')
    //                 ->where('ap.statusflag', 'F')
    //                 ->where('ap.auditquartercode', $quarter)
    //                 ->where('ap.planmappingid', $currentPlanMappingId)
    //                 ->where("inst.$quarter", 'Y')
    //                 ->when($checkPriority, function ($qq) use ($priority) {
    //                     $qq->where('inst.inst_priority_kms', $priority);
    //                 });
    //         })
    //         ->orWhere(function ($q) use ($deptcode, $regioncode, $distcode, $quarter, $checkPriority, $priority, $currentPlanMappingId) {
    //             $q
    //                 ->where(function ($inner) {
    //                     $inner
    //                         ->where('asch.statusflag', 'Y')
    //                         ->orWhere(function ($inner2) {
    //                             $inner2
    //                                 ->where('asch.statusflag', 'F')
    //                                 ->where('asch.workallocationflag', 'N')
    //                                 ->where(function ($sub) {
    //                                     $sub
    //                                         ->whereNull('asch.auditeeresponse')
    //                                         ->orWhere('asch.auditeeresponse', '<>', 'A');
    //                                 })
    //                                 ->whereNull('asch.entrymeetdate');
    //                         })
    //                         ->orWhere(function ($inner2) {
    //                             $inner2
    //                                 ->where('asch.statusflag', 'F')
    //                                 ->where('asch.workallocationflag', 'N')
    //                                 ->where(function ($sub) {
    //                                     $sub
    //                                         ->whereNull('asch.auditeeresponse')
    //                                         ->orWhere('asch.auditeeresponse', 'A');
    //                                 })
    //                                 ->whereNull('asch.entrymeetdate')
    //                                 ->whereNull('asch.exitmeetdate');
    //                         });
    //                 })
    //                 ->where('inst.deptcode', $deptcode)
    //                 ->where('inst.regioncode', $regioncode)
    //                 ->where('inst.distcode', $distcode)
    //                 ->where('inst.statusflag', 'Y')
    //                 ->where('ap.statusflag', 'F')
    //                 ->where('ap.auditquartercode', $quarter)
    //                 ->where('ap.planmappingid', $currentPlanMappingId)
    //                 ->where("inst.$quarter", 'Y')
    //                 ->when($checkPriority, function ($qq) use ($priority) {
    //                     $qq->where('inst.inst_priority_kms', $priority);
    //                 });
    //         })
    //         ->orWhere(function ($q) use ($deptcode, $regioncode, $distcode, $quarter, $checkPriority, $priority, $currentPlanMappingId) {
    //             $q
    //                 ->whereNotIn('inst.instid', function ($sub) use ($quarter, $currentPlanMappingId) {
    //                     $sub
    //                         ->select('ap.instid')
    //                         ->from('audit.auditplan as ap')
    //                         ->where('ap.auditquartercode', $quarter)
    //                         ->where('ap.planmappingid', $currentPlanMappingId)
    //                         ->where('ap.statusflag', 'F');
    //                 })
    //                 ->where('inst.deptcode', $deptcode)
    //                 ->where('inst.regioncode', $regioncode)
    //                 ->where('inst.distcode', $distcode)
    //                 ->where('inst.statusflag', 'Y')
    //                 ->where('ap.statusflag', 'F')
    //                 // ->where('inst.statusflag', 'Y')
    //                 ->where("inst.$quarter", 'Y')
    //                 ->where('ap.auditquartercode', $quarter)
    //                 ->where('ap.planmappingid', $currentPlanMappingId)
    //                 ->when($checkPriority, function ($qq) use ($priority) {
    //                     $qq->where('inst.inst_priority_kms', $priority);
    //                 });
    //         });
    //     //  $querySql = $query->toSql();
    //     //         $bindings = $query->getBindings();

    //     //         $finalQuery = vsprintf(
    //     //             str_replace('?', "'%s'", $querySql),
    //     //             array_map('addslashes', $bindings)
    //     //         );

    //     //         print_r($finalQuery);
    //     //         exit;
    //     return $query->get();
    // }
    public static function getnotscheduleinstData($deptcode = null, $regioncode = null, $distcode = null)
    {
        $sessionCharge = session('charge');

        $deptcode = $deptcode ?: ($sessionCharge->deptcode ?? null);
        $regioncode = $regioncode ?: ($sessionCharge->regioncode ?? null);
        $distcode = $distcode ?: ($sessionCharge->distcode ?? null);

        if (! $deptcode || ! $regioncode || ! $distcode) {
            return collect();
        }

        $planContext = self::getInstChangePlanContext($deptcode);
        $currentPlan = $planContext['current'] ?? null;
        $quarter = $currentPlan['auditquartercode'] ?? null;
        $currentPlanMappingId = $currentPlan['planmappingid'] ?? null;
        $priority = $currentPlan['prioritycode'] ?? null;
        $checkPriority = $priority !== null;

        if (! $quarter || ! $currentPlanMappingId) {
            return collect();
        }

        $excludeMappedInstitution = function ($sub) {
            $sub
                ->select(DB::raw(1))
                ->from('audit.mapping_apiurl as map')
                ->where('map.statusflag', 'Y')
                ->where('map.apifor', 'E')
                ->whereColumn('map.catcode', 'inst.catcode')
                ->where(function ($match) {
                    $match
                        ->whereColumn('map.subcatid', 'inst.subcatid')
                        ->orWhere(function ($nulls) {
                            $nulls
                                ->whereNull('map.subcatid')
                                ->whereNull('inst.subcatid');
                        });
                });
        };

        $query = DB::table('audit.auditplan as ap')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->leftJoin('audit.inst_auditschedule as asch', 'asch.auditplanid', '=', 'ap.auditplanid')
            ->join('audit.mst_dept as de', 'de.deptcode', '=', 'inst.deptcode')
            ->select(
                'ap.*',
                'de.currentquarter',
                'de.nextquarter',
                'inst.instename',
                'inst.mandays'
            );

        $query
            ->where('ap.planmappingid', $currentPlanMappingId)
            ->where(function ($q) use ($deptcode, $regioncode, $distcode, $quarter, $checkPriority, $priority, $currentPlanMappingId, $excludeMappedInstitution) {
                $q
                    ->whereNotIn('ap.auditplanid', function ($sub) use ($deptcode, $regioncode, $distcode, $currentPlanMappingId) {
                        $sub
                            ->select('ap.auditplanid')
                            ->from('audit.inst_auditschedule as asch')
                            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'asch.auditplanid')
                            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
                            ->where('ap.planmappingid', $currentPlanMappingId)
                            ->where('inst.deptcode', $deptcode)
                            ->where('inst.regioncode', $regioncode)
                            ->where('inst.distcode', $distcode)
                            ->where('inst.statusflag', 'Y');
                    })
                    ->where('inst.deptcode', $deptcode)
                    ->where('inst.regioncode', $regioncode)
                    ->where('inst.distcode', $distcode)
                    ->where('inst.statusflag', 'Y')
                    ->where('ap.statusflag', 'F')
                    ->where('ap.auditquartercode', $quarter)
                    ->where('ap.planmappingid', $currentPlanMappingId)
                    ->whereNotExists($excludeMappedInstitution)
                    ->where("inst.$quarter", 'Y')
                    ->when($checkPriority, function ($qq) use ($priority) {
                        $qq->where('inst.inst_priority_kms', $priority);
                    });
            })
            ->orWhere(function ($q) use ($deptcode, $regioncode, $distcode, $quarter, $checkPriority, $priority, $currentPlanMappingId, $excludeMappedInstitution) {
                $q
                    ->where(function ($inner) {
                        $inner
                            ->where('asch.statusflag', 'Y')
                            ->orWhere(function ($inner2) {
                                $inner2
                                    ->where('asch.statusflag', 'F')
                                    ->where('asch.workallocationflag', 'N')
                                    ->where(function ($sub) {
                                        $sub
                                            ->whereNull('asch.auditeeresponse')
                                            ->orWhere('asch.auditeeresponse', '<>', 'A');
                                    })
                                    ->whereNull('asch.entrymeetdate');
                            })
                            ->orWhere(function ($inner2) {
                                $inner2
                                    ->where('asch.statusflag', 'F')
                                    ->where('asch.workallocationflag', 'N')
                                    ->where(function ($sub) {
                                        $sub
                                            ->whereNull('asch.auditeeresponse')
                                            ->orWhere('asch.auditeeresponse', 'A');
                                    })
                                    ->whereNull('asch.entrymeetdate')
                                    ->whereNull('asch.exitmeetdate');
                            });
                    })
                    ->where('inst.deptcode', $deptcode)
                    ->where('inst.regioncode', $regioncode)
                    ->where('inst.distcode', $distcode)
                    ->where('inst.statusflag', 'Y')
                    ->where('ap.statusflag', 'F')
                    ->where('ap.auditquartercode', $quarter)
                    ->where('ap.planmappingid', $currentPlanMappingId)
                    ->whereNotExists($excludeMappedInstitution)
                    ->where("inst.$quarter", 'Y')
                    ->when($checkPriority, function ($qq) use ($priority) {
                        $qq->where('inst.inst_priority_kms', $priority);
                    });
            })
            ->orWhere(function ($q) use ($deptcode, $regioncode, $distcode, $quarter, $checkPriority, $priority, $currentPlanMappingId, $excludeMappedInstitution) {
                $q
                    ->whereNotIn('inst.instid', function ($sub) use ($quarter, $currentPlanMappingId) {
                        $sub
                            ->select('ap.instid')
                            ->from('audit.auditplan as ap')
                            ->where('ap.auditquartercode', $quarter)
                            ->where('ap.planmappingid', $currentPlanMappingId)
                            ->where('ap.statusflag', 'F');
                    })
                    ->where('inst.deptcode', $deptcode)
                    ->where('inst.regioncode', $regioncode)
                    ->where('inst.distcode', $distcode)
                    ->where('inst.statusflag', 'Y')
                    ->where('ap.statusflag', 'F')
                    // ->where('inst.statusflag', 'Y')
                    ->whereNotExists($excludeMappedInstitution)
                    ->where("inst.$quarter", 'Y')
                    ->where('ap.auditquartercode', $quarter)
                    ->where('ap.planmappingid', $currentPlanMappingId)
                    ->when($checkPriority, function ($qq) use ($priority) {
                        $qq->where('inst.inst_priority_kms', $priority);
                    });
            });
        //  $querySql = $query->toSql();
        //         $bindings = $query->getBindings();

        //         $finalQuery = vsprintf(
        //             str_replace('?', "'%s'", $querySql),
        //             array_map('addslashes', $bindings)
        //         );

        //         print_r($finalQuery);
        //         exit;
        return $query->get();
    }

    // public static function getnotscheduleinstCount()
    // {
    //     $sessionCharge = session('charge');

    //     $deptcode = $sessionCharge->deptcode ?? null;
    //     $regioncode = $sessionCharge->regioncode ?? null;
    //     $distcode = $sessionCharge->distcode ?? null;

    //     if (!$deptcode || !$regioncode || !$distcode) {
    //         return collect();
    //     }

    //     $planContext = self::getInstChangePlanContext($deptcode);
    //     $currentPlan = $planContext['current'] ?? null;
    //     $quarter = $currentPlan['auditquartercode'] ?? null;
    //     $currentPlanMappingId = $currentPlan['planmappingid'] ?? null;
    //     $priority = $currentPlan['prioritycode'] ?? null;
    //     $checkPriority = $priority !== null;

    //     if (!$quarter || !$currentPlanMappingId) {
    //         return collect();
    //     }

    //     $query = DB::table('audit.auditplan as ap')
    //         ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
    //         ->leftJoin('audit.inst_auditschedule as asch', 'asch.auditplanid', '=', 'ap.auditplanid')
    //         ->join('audit.mst_dept as de', 'de.deptcode', '=', 'inst.deptcode')
    //         ->select(
    //             'ap.*',
    //             'de.currentquarter',
    //             'de.nextquarter',
    //             'inst.instename',
    //             'inst.mandays'
    //         );

    //     $query
    //         ->where('ap.planmappingid', $currentPlanMappingId)
    //         ->where(function ($q) use ($deptcode, $regioncode, $distcode, $quarter, $checkPriority, $priority, $currentPlanMappingId) {
    //             $q
    //                 ->whereNotIn('ap.auditplanid', function ($sub) use ($deptcode, $regioncode, $distcode, $currentPlanMappingId) {
    //                     $sub
    //                         ->select('ap.auditplanid')
    //                         ->from('audit.inst_auditschedule as asch')
    //                         ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'asch.auditplanid')
    //                         ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
    //                         ->where('ap.planmappingid', $currentPlanMappingId)
    //                         ->where('inst.deptcode', $deptcode)
    //                         ->where('inst.regioncode', $regioncode)
    //                         ->where('inst.distcode', $distcode)
    //                         ->where('inst.statusflag', 'Y');
    //                 })
    //                 ->where('inst.deptcode', $deptcode)
    //                 ->where('inst.regioncode', $regioncode)
    //                 ->where('inst.distcode', $distcode)
    //                 ->where('inst.statusflag', 'Y')
    //                 ->where('ap.statusflag', 'F')
    //                 ->where('ap.auditquartercode', $quarter)
    //                 ->where('ap.planmappingid', $currentPlanMappingId)
    //                 ->where("inst.$quarter", 'Y')
    //                 ->when($checkPriority, function ($qq) use ($priority) {
    //                     $qq->where('inst.inst_priority_kms', $priority);
    //                 });
    //         })
    //         ->orWhere(function ($q) use ($deptcode, $regioncode, $distcode, $quarter, $checkPriority, $priority, $currentPlanMappingId) {
    //             $q
    //                 ->where(function ($inner) {
    //                     $inner
    //                         ->where('asch.statusflag', 'Y')
    //                         ->orWhere(function ($inner2) {
    //                             $inner2
    //                                 ->where('asch.statusflag', 'F')
    //                                 ->where('asch.workallocationflag', 'N')
    //                                 ->where(function ($sub) {
    //                                     $sub
    //                                         ->whereNull('asch.auditeeresponse')
    //                                         ->orWhere('asch.auditeeresponse', '<>', 'A');
    //                                 })
    //                                 ->whereNull('asch.entrymeetdate');
    //                         })
    //                         ->orWhere(function ($inner2) {
    //                             $inner2
    //                                 ->where('asch.statusflag', 'F')
    //                                 ->where('asch.workallocationflag', 'N')
    //                                 ->where(function ($sub) {
    //                                     $sub
    //                                         ->whereNull('asch.auditeeresponse')
    //                                         ->orWhere('asch.auditeeresponse', 'A');
    //                                 })
    //                                 ->whereNull('asch.entrymeetdate')
    //                                 ->whereNull('asch.exitmeetdate');
    //                         });
    //                 })
    //                 ->where('inst.deptcode', $deptcode)
    //                 ->where('inst.regioncode', $regioncode)
    //                 ->where('inst.distcode', $distcode)
    //                 ->where('inst.statusflag', 'Y')
    //                 ->where('ap.statusflag', 'F')
    //                 ->where('ap.auditquartercode', $quarter)
    //                 ->where('ap.planmappingid', $currentPlanMappingId)
    //                 ->where("inst.$quarter", 'Y')
    //                 ->when($checkPriority, function ($qq) use ($priority) {
    //                     $qq->where('inst.inst_priority_kms', $priority);
    //                 });
    //         })
    //         ->orWhere(function ($q) use ($deptcode, $regioncode, $distcode, $quarter, $checkPriority, $priority, $currentPlanMappingId) {
    //             $q
    //                 ->whereNotIn('inst.instid', function ($sub) use ($quarter, $currentPlanMappingId) {
    //                     $sub
    //                         ->select('ap.instid')
    //                         ->from('audit.auditplan as ap')
    //                         ->where('ap.auditquartercode', $quarter)
    //                         ->where('ap.planmappingid', $currentPlanMappingId)
    //                         ->where('ap.statusflag', 'F');
    //                 })
    //                 ->where('inst.deptcode', $deptcode)
    //                 ->where('inst.regioncode', $regioncode)
    //                 ->where('inst.distcode', $distcode)
    //                 ->where('inst.statusflag', 'Y')
    //                 ->where('ap.statusflag', 'F')
    //                 // ->where('inst.statusflag', 'Y')
    //                 ->where("inst.$quarter", 'Y')
    //                 ->where('ap.auditquartercode', $quarter)
    //                 ->where('ap.planmappingid', $currentPlanMappingId)
    //                 ->when($checkPriority, function ($qq) use ($priority) {
    //                     $qq->where('inst.inst_priority_kms', $priority);
    //                 });
    //         });

    //     return $query->distinct('inst.instid')->count('inst.instid');
    // }

    public static function getnotscheduleinstCount($deptcode = null, $regioncode = null, $distcode = null)
    {
        $sessionCharge = session('charge');

        $deptcode = $deptcode ?: ($sessionCharge->deptcode ?? null);
        $regioncode = $regioncode ?: ($sessionCharge->regioncode ?? null);
        $distcode = $distcode ?: ($sessionCharge->distcode ?? null);

        if (! $deptcode || ! $regioncode || ! $distcode) {
            return collect();
        }

        $planContext = self::getInstChangePlanContext($deptcode);
        $currentPlan = $planContext['current'] ?? null;
        $quarter = $currentPlan['auditquartercode'] ?? null;
        $currentPlanMappingId = $currentPlan['planmappingid'] ?? null;
        $priority = $currentPlan['prioritycode'] ?? null;
        $checkPriority = $priority !== null;

        if (! $quarter || ! $currentPlanMappingId) {
            return collect();
        }

        $excludeMappedInstitution = function ($sub) {
            $sub
                ->select(DB::raw(1))
                ->from('audit.mapping_apiurl as map')
                ->where('map.statusflag', 'Y')
                ->where('map.apifor', 'E')
                ->whereColumn('map.catcode', 'inst.catcode')
                ->where(function ($match) {
                    $match
                        ->whereColumn('map.subcatid', 'inst.subcatid')
                        ->orWhere(function ($nulls) {
                            $nulls
                                ->whereNull('map.subcatid')
                                ->whereNull('inst.subcatid');
                        });
                });
        };

        $query = DB::table('audit.auditplan as ap')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->leftJoin('audit.inst_auditschedule as asch', 'asch.auditplanid', '=', 'ap.auditplanid')
            ->join('audit.mst_dept as de', 'de.deptcode', '=', 'inst.deptcode')
            ->select(
                'ap.*',
                'de.currentquarter',
                'de.nextquarter',
                'inst.instename',
                'inst.mandays'
            );

        $query
            ->where('ap.planmappingid', $currentPlanMappingId)
            ->where(function ($q) use ($deptcode, $regioncode, $distcode, $quarter, $checkPriority, $priority, $currentPlanMappingId, $excludeMappedInstitution) {
                $q
                    ->whereNotIn('ap.auditplanid', function ($sub) use ($deptcode, $regioncode, $distcode, $currentPlanMappingId) {
                        $sub
                            ->select('ap.auditplanid')
                            ->from('audit.inst_auditschedule as asch')
                            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'asch.auditplanid')
                            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
                            ->where('ap.planmappingid', $currentPlanMappingId)
                            ->where('inst.deptcode', $deptcode)
                            ->where('inst.regioncode', $regioncode)
                            ->where('inst.distcode', $distcode)
                            ->where('inst.statusflag', 'Y');
                    })
                    ->where('inst.deptcode', $deptcode)
                    ->where('inst.regioncode', $regioncode)
                    ->where('inst.distcode', $distcode)
                    ->where('inst.statusflag', 'Y')
                    ->where('ap.statusflag', 'F')
                    ->where('ap.auditquartercode', $quarter)
                    ->where('ap.planmappingid', $currentPlanMappingId)
                    ->whereNotExists($excludeMappedInstitution)
                    ->where("inst.$quarter", 'Y')
                    ->when($checkPriority, function ($qq) use ($priority) {
                        $qq->where('inst.inst_priority_kms', $priority);
                    });
            })
            ->orWhere(function ($q) use ($deptcode, $regioncode, $distcode, $quarter, $checkPriority, $priority, $currentPlanMappingId, $excludeMappedInstitution) {
                $q
                    ->where(function ($inner) {
                        $inner
                            ->where('asch.statusflag', 'Y')
                            ->orWhere(function ($inner2) {
                                $inner2
                                    ->where('asch.statusflag', 'F')
                                    ->where('asch.workallocationflag', 'N')
                                    ->where(function ($sub) {
                                        $sub
                                            ->whereNull('asch.auditeeresponse')
                                            ->orWhere('asch.auditeeresponse', '<>', 'A');
                                    })
                                    ->whereNull('asch.entrymeetdate');
                            })
                            ->orWhere(function ($inner2) {
                                $inner2
                                    ->where('asch.statusflag', 'F')
                                    ->where('asch.workallocationflag', 'N')
                                    ->where(function ($sub) {
                                        $sub
                                            ->whereNull('asch.auditeeresponse')
                                            ->orWhere('asch.auditeeresponse', 'A');
                                    })
                                    ->whereNull('asch.entrymeetdate')
                                    ->whereNull('asch.exitmeetdate');
                            });
                    })
                    ->where('inst.deptcode', $deptcode)
                    ->where('inst.regioncode', $regioncode)
                    ->where('inst.distcode', $distcode)
                    ->where('inst.statusflag', 'Y')
                    ->where('ap.statusflag', 'F')
                    ->where('ap.auditquartercode', $quarter)
                    ->where('ap.planmappingid', $currentPlanMappingId)
                    ->whereNotExists($excludeMappedInstitution)
                    ->where("inst.$quarter", 'Y')
                    ->when($checkPriority, function ($qq) use ($priority) {
                        $qq->where('inst.inst_priority_kms', $priority);
                    });
            })
            ->orWhere(function ($q) use ($deptcode, $regioncode, $distcode, $quarter, $checkPriority, $priority, $currentPlanMappingId, $excludeMappedInstitution) {
                $q
                    ->whereNotIn('inst.instid', function ($sub) use ($quarter, $currentPlanMappingId) {
                        $sub
                            ->select('ap.instid')
                            ->from('audit.auditplan as ap')
                            ->where('ap.auditquartercode', $quarter)
                            ->where('ap.planmappingid', $currentPlanMappingId)
                            ->where('ap.statusflag', 'F');
                    })
                    ->where('inst.deptcode', $deptcode)
                    ->where('inst.regioncode', $regioncode)
                    ->where('inst.distcode', $distcode)
                    ->where('inst.statusflag', 'Y')
                    ->where('ap.statusflag', 'F')
                    // ->where('inst.statusflag', 'Y')
                    ->whereNotExists($excludeMappedInstitution)
                    ->where("inst.$quarter", 'Y')
                    ->where('ap.auditquartercode', $quarter)
                    ->where('ap.planmappingid', $currentPlanMappingId)
                    ->when($checkPriority, function ($qq) use ($priority) {
                        $qq->where('inst.inst_priority_kms', $priority);
                    });
            });

        return $query->distinct('inst.instid')->count('inst.instid');
    }

    public static function penidninst_fetchData($Pendinginstitutions, $table = null, $deptcode = null, $regioncode = null, $distcode = null)
    {
        if (! $table) {
            throw new Exception('Table name is required');
        }

        $sessionchargedel = session('charge');
        $deptcode = $deptcode ?: ($sessionchargedel->deptcode ?? null);
        $regioncode = $regioncode ?: ($sessionchargedel->regioncode ?? null);
        $distcode = $distcode ?: ($sessionchargedel->distcode ?? null);

        $planContext = self::getInstChangePlanContext($deptcode);
        //   dd($planContext);
        $currentQuarter = $planContext['currentquarter'] ?? null;

        if (! $currentQuarter) {
            return [
                'count' => 0,
                'data' => collect(),
            ];
        }

        $pendingInstIds = $Pendinginstitutions->pluck('instid')->toArray();

        if (empty($pendingInstIds)) {
            return [
                'count' => 0,
                'data' => collect(),
            ];
        }
        $baseQuery = DB::table($table.' as t')
            ->join('audit.mst_institution as ins', 'ins.instid', '=', 't.instid')
            ->where('ins.deptcode', $deptcode)
            ->where('ins.regioncode', $regioncode)
            ->where('ins.distcode', $distcode)
            ->where('ins.statusflag', 'Y')
            ->where('t.quartercode', $currentQuarter)
            ->whereIn('t.pendingflag', ['D'])
            ->whereIn('t.instid', $pendingInstIds);

        $data = (clone $baseQuery)
            ->select(
                't.*',
                'ins.instid',
                'ins.instename',
                'ins.insttname'
            )
            ->orderBy('t.tempid', 'desc')
            ->get();

        $count = (clone $baseQuery)->count('t.instid');

        return [
            'count' => $count,
            'data' => $data,
        ];
    }

    public static function pendinginstcheck()
    {
        $sessionchargedel = session('charge');
        $deptcode = $sessionchargedel->deptcode ?? null;
        $distcode = $sessionchargedel->distcode ?? null;

        $result = DB::table('audit.auditor_instmapping')
            ->where('deptcode', $deptcode)
            ->where('distcode', $distcode)
            ->get();

        return [
            'institutions' => $result,
            'quarterinfo' => (object) self::getInstChangePlanContext($deptcode),
        ];
    }

    public static function pendinginststatus($deptcode, $distcode): ?string
    {
        if (empty($deptcode) || empty($distcode)) {
            return null;
        }

        return DB::table('audit.auditor_instmapping')
            ->where('deptcode', $deptcode)
            ->where('distcode', $distcode)
            ->value('pendinginststatus');
    }

    public static function indexSpillousByInstid($spillous)
    {
        $indexed = [];

        if (is_array($spillous)) {
            foreach ($spillous as $item) {
                if (isset($item['instid'])) {
                    $indexed[(string) $item['instid']] = $item;
                }
            }
        }

        return $indexed;
    }

    public static function indexSpillousByInstidforFinalize($spillous)
    {
        $indexed = [];

        if (is_array($spillous)) {
            foreach ($spillous as $item) {
                if (isset($item['instid']) && isset($item['confirmflag']) && $item['confirmflag'] === 'Y') {
                    $indexed[(string) $item['instid']] = $item;
                }
            }
        }

        return $indexed;
    }

    public static function getQuarterValue($deptcode, $column = 'previousquarter')
    {
        return match ($column) {
            'previousquarter' => null,
            'currentquarter' => self::getInstChangeQuarterCodeByType('CURRENT', $deptcode),
            'nextquarter' => self::getInstChangeQuarterCodeByType('NEXT', $deptcode),
            default => null,
        };
    }

    public static function updateInstitutionExistingQuarter()
    {
        $sessionchargedel = session('charge');
        $deptcode = $sessionchargedel->deptcode ?? null;
        $distcode = $sessionchargedel->distcode ?? null;
        DB::table('audit.mst_institution')
            ->where('deptcode', $deptcode)
            ->where('distcode', $distcode)
            ->update([
                'priority' => null,
                'spillover' => null,
                'remainingmandays' => null,
            ]);
    }

    public static function updateSpilloverInstitutions($indexed, $newQuarter, $userid, $now)
    {
        if (empty($indexed)) {
            return;
        }

        DB::beginTransaction();

        try {
            foreach ($indexed as $instid => $item) {
                if (isset($item['confirmflag']) && $item['confirmflag'] === 'Y') {
                    $update = [
                        'priority' => 'Y',
                        'remainingmandays' => $item['remainingmandays'] ?? null,
                        'spillover' => 'Y',
                        'updatedon' => $now,
                        'updatedby' => $userid,
                    ];

                    if (in_array($newQuarter, ['Q1', 'Q2', 'Q3', 'Q4'])) {
                        $update[$newQuarter] = 'Y';
                    }

                    DB::table('audit.mst_institution')
                        ->where('instid', $instid)
                        ->update($update);
                }

                // 🔹 2️⃣ Update ONLY exact spillover row
                $updatetempdata = [
                    'pendingflag' => 'F',
                    'remainingmandays' => $item['remainingmandays'] ?? null,
                    'spilloverflag' => 'Y',
                    'spillovercnfrmflag' => $item['confirmflag'] ?? null,
                    'remarks' => $item['remarks'] ?? null,
                    'updatedon' => $now,
                    'updatedby' => $userid,
                ];

                DB::table('audit.temp_inst_q1_pending')
                    ->where('instid', $instid)
                    ->where('quartercode', 'Q4')
                    ->where('newquartercode', $newQuarter)
                    ->update($updatetempdata);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function UpdateNotscheduledData($data, $tempid, $table)
    {
        try {
            if (! empty($data['instid'])) {
                $instDet = DB::table('audit.mst_institution as mi')
                    ->where('mi.instid', $data['instid'])
                    ->select('mi.deptcode')
                    ->first();

                if ($instDet && ! empty($instDet->deptcode)) {
                    $planContext = self::getInstChangePlanContext($instDet->deptcode);
                    $quarterMap = $planContext['byQuarterCode'] ?? [];
                    $fromPlan = $quarterMap[$data['quartercode'] ?? ''] ?? ($planContext['current'] ?? null);
                    $toPlan = $quarterMap[$data['newquartercode'] ?? ''] ?? $fromPlan;

                    if (! isset($data['fromfinyearcode'])) {
                        $data['fromfinyearcode'] = $fromPlan['financialyearcode'] ?? null;
                    }
                    if (! isset($data['tofinyearcode'])) {
                        $data['tofinyearcode'] = $toPlan['financialyearcode'] ?? null;
                    }
                    if (! isset($data['fromprioritycode'])) {
                        $data['fromprioritycode'] = $fromPlan['prioritycode'] ?? null;
                    }
                    if (! isset($data['toprioritycode'])) {
                        $data['toprioritycode'] = (($toPlan['auditquartercode'] ?? null) === ($data['newquartercode'] ?? null))
                            ? ($toPlan['prioritycode'] ?? null)
                            : ($fromPlan['prioritycode'] ?? null);
                    }
                    if (! isset($data['fromplanmappingid'])) {
                        $data['fromplanmappingid'] = $fromPlan['planmappingid'] ?? null;
                    }
                    if (! isset($data['toplanmappingid'])) {
                        $data['toplanmappingid'] = $toPlan['planmappingid'] ?? null;
                    }
                }
            }

            if ($tempid) {
                DB::table($table)
                    ->where('instid', $data['instid'])
                    ->where('quartercode', $data['quartercode'])
                    ->update($data);
            } else {
                DB::table($table)->updateOrInsert([
                    'instid' => $data['instid'],
                    'quartercode' => $data['quartercode'],
                ], $data);
            }

            return ['status' => true];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public static function updateInstitutionQuarter(
        array $instids,
        string $new_quarter,
        string $current_quarter,
        $fromPlanMappingId,
        $toPlanMappingId,
        int $userid,
        $now,
        string $actionType
    ) {
        DB::beginTransaction();
        try {
            $approveProcessCode = View::shared('QT_finalizeProcesscode');

            $deptcode = DB::table('audit.mst_institution')
                ->whereIn('instid', $instids)
                ->value('deptcode');

            $planContext = $deptcode ? self::getInstChangePlanContext($deptcode) : [];

            $nextQuarter = null;

            if (! empty($planContext)) {
                $nextQuarter = $planContext['nextquarter'] ?? null;
            }

            $quarterQuery = DB::table('audit.mst_institution')
                ->whereIn('instid', $instids);

            $quarterQuery->update([
                $new_quarter => 'Y',
                $current_quarter => 'N',
                'updatedon' => $now,
                'updatedby' => $userid,
            ]);

            if ($actionType === 'template') {
                $planByMappingId = $planContext['byPlanMappingId'] ?? [];
                $fromPlan = $fromPlanMappingId ? ($planByMappingId[$fromPlanMappingId] ?? null) : null;
                $toPlan = $toPlanMappingId ? ($planByMappingId[$toPlanMappingId] ?? null) : null;
                $shouldMarkPriority = ! empty($fromPlan) &&
                    ! empty($toPlan) &&
                    (($fromPlan['auditquartercode'] ?? null) === ($toPlan['auditquartercode'] ?? null)) &&
                    (($fromPlan['prioritycode'] ?? null) !== ($toPlan['prioritycode'] ?? null));

                DB::table('audit.mst_institution')
                    ->whereIn('instid', $instids)
                    ->update(['priority' => $shouldMarkPriority ? 'Y' : null]);

                DB::table('audit.templateauditplan')
                    ->whereIn('instid', $instids)
                    ->when($fromPlanMappingId, function ($query) use ($fromPlanMappingId) {
                        $query->where('planmappingid', $fromPlanMappingId);
                    })
                    ->update([
                        'statusflag' => $approveProcessCode,
                        'updatedon' => $now,
                        'updatedby' => $userid,
                    ]);
            } elseif ($actionType === 'pending') {
                DB::table('audit.mst_institution')
                    ->whereIn('instid', $instids)
                    ->update(['priority' => 'B']);

                $auditplanQuery = DB::table('audit.auditplan')
                    ->whereIn('instid', $instids)
                    ->where('auditquartercode', $current_quarter)
                    ->when($fromPlanMappingId, function ($query) use ($fromPlanMappingId) {
                        $query->where('planmappingid', $fromPlanMappingId);
                    });

                $auditplanIds = $auditplanQuery->pluck('auditplanid')->toArray();

                if (! empty($auditplanIds)) {
                    DB::table('audit.auditplan')
                        ->whereIn('auditplanid', $auditplanIds)
                        ->update([
                            'statusflag' => $approveProcessCode,
                            'updatedon' => $now,
                            'updatedby' => $userid,
                        ]);

                    // $auditscheduleIds = DB::table('audit.inst_auditschedule')
                    //     ->whereIn('auditplanid', $auditplanIds)
                    //     ->pluck('auditscheduleid')
                    //     ->toArray();

                    // if(!empty($auditscheduleIds)) {

                    //     DB::table('audit.inst_auditschedule')
                    //         ->whereIn('auditscheduleid', $auditscheduleIds)
                    //         ->update(['statusflag' => 'N', 'updatedon' => $now, 'updatedby' => $userid,]);

                    //     DB::table('audit.inst_schteammember')
                    //         ->whereIn('auditscheduleid', $auditscheduleIds)
                    //         ->update(['statusflag' => 'N', 'updatedon' => $now, 'updatedby' => $userid,]);
                    // }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public static function deactivateAuditDataByInstid($instid, $userid, $now)
    {
        $scheduleIds = DB::table('audit.inst_auditschedule as asch')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'asch.auditplanid')
            ->where('ap.instid', $instid)
            ->pluck('asch.auditscheduleid');

        if ($scheduleIds->isNotEmpty()) {
            foreach (['selected_cfr', 'trans_accountdetails', 'auditee_office_users', 'trans_callforrecords'] as $table) {
                DB::table("audit.$table")
                    ->whereIn('auditscheduleid', $scheduleIds)
                    ->update([
                        'statusflag' => 'N',
                        'updatedon' => $now,
                        'updatedby' => $userid,
                    ]);
            }
        }

        $planIds = DB::table('audit.auditplan as p')
            ->join('audit.inst_auditschedule as s', 'p.auditplanid', '=', 's.auditplanid')
            ->where('p.instid', $instid)
            ->pluck('p.auditplanid');

        if ($planIds->isNotEmpty()) {
            DB::table('audit.yearcode_mapping')
                ->whereIn('auditplanid', $planIds)
                ->update([
                    'statusflag' => 'N',
                    'updatedon' => $now,
                ]);
        }
    }

    public static function pendingupdation($deptcode, $distcode, $userid, $now)
    {
        $query = DB::table(self::$auditor_instmapping_table)
            ->where('deptcode', $deptcode)
            ->where('distcode', $distcode);

        $query->update([
            'pendinginststatus' => 'Y',
            'updatedon' => $now,
            'updatedby' => $userid,
        ]);
    }

    public static function spillover_fetchData($spillous, $table = null)
    {
        if (! $table) {
            throw new Exception('Table name is required');
        }

        $sessionchargedel = session('charge');
        $deptcode = $sessionchargedel->deptcode ?? null;
        $regioncode = $sessionchargedel->regioncode ?? null;
        $distcode = $sessionchargedel->distcode ?? null;

        $currentQuarter = self::getInstChangeQuarterCodeByType('CURRENT', $deptcode);

        if (! $currentQuarter) {
            return [
                'count' => 0,
                'data' => collect(),
            ];
        }

        $spilloverInstIds = array_column($spillous['spilloverData'] ?? [], 'instid');
        if (empty($spilloverInstIds)) {
            return [
                'count' => 0,
                'data' => collect(),
            ];
        }

        $baseQuery = DB::table($table.' as t')
            ->join('audit.mst_institution as ins', 'ins.instid', '=', 't.instid')
            ->where('ins.deptcode', $deptcode)
            ->where('ins.regioncode', $regioncode)
            ->where('ins.distcode', $distcode);
        // ->where('t.quartercode', 'Q4')
        // ->whereIn('t.pendingflag', ['F', 'D'])

        $data = (clone $baseQuery)
            ->select(
                't.*',
                'ins.instid',
                'ins.instename',
                'ins.insttname'
            )
            ->orderBy('ins.instid', 'desc')
            ->get();

        $count = (clone $baseQuery)->count('t.instid');

        return [
            'count' => $count,
            'data' => $data,
        ];
    }

    public static function getTemplateInstitutionList($deptcode = null, $regioncode = null, $distcode = null)
    {
        $sessionchargedel = session('charge');

        $deptcode = $deptcode ?: ($sessionchargedel->deptcode ?? null);
        $regioncode = $regioncode ?: ($sessionchargedel->regioncode ?? null);
        $distcode = $distcode ?: ($sessionchargedel->distcode ?? null);

        $planContext = self::getInstChangePlanContext($deptcode);
        $currentPlan = $planContext['current'] ?? null;
        $quartercurrent = $currentPlan['auditquartercode'] ?? null;
        $currentPlanMappingId = $currentPlan['planmappingid'] ?? null;
        $priority = $currentPlan['prioritycode'] ?? null;
        $checkPriority = $priority !== null;

        if (! $quartercurrent || ! $currentPlanMappingId) {
            return collect();
        }

        $baseQuery = DB::table('audit.templateauditplan as tp')
            ->join('audit.mst_institution as mi', 'mi.instid', '=', 'tp.instid')
            ->join('audit.mst_dept as d', 'd.deptcode', '=', 'mi.deptcode')
            ->whereNull('tp.startdate')
            ->whereNull('tp.enddate')
            ->where('mi.deptcode', $deptcode)
            ->where('mi.regioncode', $regioncode)
            ->where('mi.distcode', $distcode)
            ->where("mi.$quartercurrent", 'Y')
            ->where('tp.auditquartercode', $quartercurrent)
            ->where('tp.planmappingid', $currentPlanMappingId)
            ->where('tp.statusflag', 'F')
            ->when($checkPriority, function ($q) use ($priority) {
                $q->where('mi.inst_priority_kms', $priority);
            })
            ->when($checkPriority, function ($q) use ($priority) {
                $q->where('tp.prioritycode', $priority);
            });

        $count = (clone $baseQuery)->count();

        $data = $baseQuery
            ->select(
                'd.deptesname',
                'mi.instid',
                'mi.instename',
                'tp.startdate',
                'tp.enddate'
            )
            ->orderBy('d.deptesname')
            ->orderBy('mi.instename')
            ->get();

        return [
            'count' => $count,
            'data' => $data,
        ];
    }

    public static function penidndtemplate_fetchData($templateinstitutionlist, $table = null, $deptcode = null, $regioncode = null, $distcode = null)
    {
        if (! $table) {
            throw new Exception('Table name is required');
        }

        $sessionchargedel = session('charge');
        $deptcode = $deptcode ?: ($sessionchargedel->deptcode ?? null);
        $regioncode = $regioncode ?: ($sessionchargedel->regioncode ?? null);
        $distcode = $distcode ?: ($sessionchargedel->distcode ?? null);

        $planContext = self::getInstChangePlanContext($deptcode);
        $currentPlan = $planContext['current'] ?? null;
        $quartercurrent = $currentPlan['auditquartercode'] ?? null;
        $priority = $currentPlan['prioritycode'] ?? null;
        $checkPriority = $priority !== null;

        if (! $quartercurrent) {
            return collect();
        }

        $pendingInstIds = $templateinstitutionlist->pluck('instid')->toArray();

        if (empty($pendingInstIds)) {
            return [
                'count' => 0,
                'data' => collect(),
            ];
        }

        $baseQuery = DB::table($table.' as t')
            ->join('audit.mst_institution as ins', 'ins.instid', '=', 't.instid')
            ->where('ins.deptcode', $deptcode)
            ->where('ins.regioncode', $regioncode)
            ->where('ins.distcode', $distcode)
            ->where('t.quartercode', $quartercurrent)
            ->whereIn('t.pendingflag', ['D'])
            ->whereIn('t.instid', $pendingInstIds)
            ->when($checkPriority, function ($q) use ($priority) {
                $q->where('ins.inst_priority_kms', $priority);
            });

        $data = (clone $baseQuery)
            ->select(
                't.*',
                'ins.instid',
                'ins.instename',
                'ins.insttname'
            )
            ->orderBy('t.tempid', 'desc')
            ->get();

        $count = (clone $baseQuery)->count('t.instid');

        return [
            'count' => $count,
            'data' => $data,
        ];
    }

    // -------------------------------------------Quarter Transaction End----------------------------

    // ----------------------------------------------------------------------------SpillOver Revoke--------------------------------------

    protected static $roletypemappingTable = BaseModel::ROLETYPEMAPPING_TABLE;

    protected static $region_table = BaseModel::REGION_TABLE;

    protected static $auditeinstmap = BaseModel::AUDITOR_INSTMAPPING_TABLE;

    public static function deptfetch()
    {
        return DB::table(self::$deptartment_table.' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname')
            ->where('dept.statusflag', '=', 'Y')
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }

    public static function regionfetch($deptcode)
    {
        return DB::table(self::$roletypemappingTable.' as rtm')
            ->join(self::$region_table.' as rt', 'rt.deptcode', '=', 'rtm.parentcode')
            ->join(self::$deptartment_table.' as md', 'md.deptcode', '=', 'rtm.parentcode')
            ->join(self::$auditeinstmap.' as map', 'map.regioncode', '=', 'rt.regioncode')
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
        return DB::table(self::$auditeinstmap.' as d')
            ->join(self::$dist_table.' as di', 'di.distcode', '=', 'd.distcode')
            ->select('di.distename', 'di.distcode', 'di.disttname')
            ->where('d.regioncode', $regioncode)  // Select required columns
            ->where('d.statusflag', '=', 'Y')  // Use the correct table alias for `statusflag`
            ->distinct()
            ->orderBy('di.distcode', 'asc')
            ->get();
    }

    public static function fetch_institutions($deptcode, $regioncode, $distcode)
    {
        $planContext = self::getInstChangePlanContext($deptcode);
        $currentPlan = $planContext['current'] ?? null;
        $quarter = $currentPlan['auditquartercode'] ?? null;
        $quartertodate = $currentPlan['todate'] ?? null;
        $currentPlanMappingId = $currentPlan['planmappingid'] ?? null;

        if (empty($quarter) || empty($currentPlanMappingId)) {
            return collect();
        }

        $query = DB::table(self::$institution_table.' as i')
            ->join(self::$auditplan_table.' as ap', 'ap.instid', '=', 'i.instid')
            ->join(self::$instauditschedule_table.' as ins', 'ins.auditplanid', '=', 'ap.auditplanid')
            ->select(
                'i.instid',
                'i.instename',
                'i.distcode',
                'ins.auditscheduleid',
                'ins.exitmeetdate',
                'ins.proposedexitmeetdate',
                'ins.spill_revokeflag',
                'ins.spillovercompleted',
                'ap.mandays',
                DB::raw('COALESCE(i.mandays,0) - COALESCE(i.remainingmandays,0) as completed_mandays')
            )
            ->where('i.deptcode', $deptcode)
            ->where('i.regioncode', $regioncode)
            ->where('i.distcode', $distcode)
            ->where('i.statusflag', 'Y')
            ->where('ap.auditquartercode', $quarter)
            ->where('ap.planmappingid', $currentPlanMappingId)
            ->where(function ($query) use ($quartertodate) {
                $query
                    ->where('ap.carryforwardflag', 'Y')
                    ->orWhere(function ($sub) {
                        $sub
                            ->where('ap.carryforwardflag', 'N')
                            ->where('ap.spilloverflag', 'Y');
                    });

                if (! empty($quartertodate)) {
                    $query->orWhere('ins.proposedexitmeetdate', '>', $quartertodate);
                }
            })
            ->whereNotNull('ins.spillovercompleted')
            ->distinct()
            ->orderBy('i.distcode', 'asc');

        return $query->get();
    }

    public static function getSpilloverRevokeDetails($instid)
    {
        $instDetails = DB::table(self::$institution_table)
            ->select('instid', 'deptcode')
            ->where('instid', $instid)
            ->first();

        $planContext = $instDetails ? self::getInstChangePlanContext($instDetails->deptcode) : [];
        $currentPlan = $planContext['current'] ?? null;
        $currentPlanMappingId = $currentPlan['planmappingid'] ?? null;

        return DB::table(self::$instauditschedule_table.' as ins')
            ->join(self::$auditplan_table.' as ap', 'ap.auditplanid', '=', 'ins.auditplanid')
            ->join(self::$institution_table.' as i', 'i.instid', '=', 'ap.instid')
            ->select(
                'i.instid',
                'i.instename',
                'ins.auditscheduleid',
                'ins.spillovercompleted',
                'ins.entrymeetdate',
                'ins.exitmeetdate',
                'i.mandays',
                'i.remainingmandays',
                'ap.teamsize',
                DB::raw('
                    CASE
                        WHEN ins.entrymeetdate IS NOT NULL AND ins.exitmeetdate IS NOT NULL
                        THEN audit.get_working_days_between(ins.entrymeetdate, ins.exitmeetdate)
                        ELSE 0
                    END as working_days
                '),
                DB::raw('
                    (
                        COALESCE(ap.mandays, 0) -
                        (
                            CASE
                                WHEN ins.entrymeetdate IS NOT NULL AND ins.exitmeetdate IS NOT NULL
                                THEN audit.get_working_days_between(ins.entrymeetdate, ins.exitmeetdate)
                                ELSE 0
                            END
                            * COALESCE(ap.teamsize, 0)
                        )
                    ) as recalculated_remaining_mandays
                '),
                DB::raw('COALESCE(i.mandays, 0) - COALESCE(i.remainingmandays, 0) as completed_mandays')
            )
            ->where('i.instid', $instid)
            ->when($currentPlanMappingId, function ($query) use ($currentPlanMappingId) {
                $query->where('ap.planmappingid', $currentPlanMappingId);
            })
            ->orderByDesc('ins.auditscheduleid')
            ->first();
    }

    public static function updateSpilloverRevokeFlagByInstid($instid, $revokeFlag, $userId, $remarks)
    {
        $column = self::resolveSpilloverRevokeColumn();

        if (! $column) {
            throw new Exception('Revoke column not found in audit.inst_auditschedule.');
        }

        return DB::transaction(function () use ($instid, $revokeFlag, $userId, $column, $remarks) {
            $instDetails = DB::table(self::$institution_table)
                ->select('instid', 'deptcode')
                ->where('instid', $instid)
                ->first();

            $planContext = $instDetails ? self::getInstChangePlanContext($instDetails->deptcode) : [];
            $currentPlan = $planContext['current'] ?? null;
            $currentPlanMappingId = $currentPlan['planmappingid'] ?? null;

            $schedule = DB::table(self::$instauditschedule_table.' as ins')
                ->join(self::$auditplan_table.' as ap', 'ap.auditplanid', '=', 'ins.auditplanid')
                ->join(self::$institution_table.' as i', 'i.instid', '=', 'ap.instid')
                ->where('ap.instid', $instid)
                ->when($currentPlanMappingId, function ($query) use ($currentPlanMappingId) {
                    $query->where('ap.planmappingid', $currentPlanMappingId);
                })
                ->orderByDesc('ins.auditscheduleid')
                ->select(
                    'ins.auditscheduleid',
                    'ins.spillovercompleted',
                    'ins.entrymeetdate',
                    'ins.exitmeetdate',
                    'i.instid',
                    'i.remainingmandays',
                    'ap.auditquartercode',
                    'ap.mandays',
                    'ap.teamsize',
                    DB::raw('
                        CASE
                            WHEN ins.entrymeetdate IS NOT NULL AND ins.exitmeetdate IS NOT NULL
                            THEN audit.get_working_days_between(ins.entrymeetdate, ins.exitmeetdate)
                            ELSE 0
                        END as working_days
                    ')
                )
                ->first();

            if (! $schedule) {
                throw new Exception('Institution schedule not found.');
            }

            $now = View::shared('get_nowtime') ?? now();
            $newSpilloverCompleted = (($schedule->spillovercompleted ?? 'N') === 'Y') ? 'N' : 'Y';
            $workingDays = (int) ($schedule->working_days ?? 0);
            // dd($workingDays);
            $teamSize = (int) ($schedule->teamsize ?? 0);
            $mandays = (int) ($schedule->mandays ?? 0);
            $totalWorkingMandays = $workingDays * $teamSize;
            $newRemainingMandays = $mandays - $totalWorkingMandays;
            $currentRemainingMandays = (int) ($schedule->remainingmandays ?? 0);

            DB::table(self::$instauditschedule_table)
                ->where('auditscheduleid', $schedule->auditscheduleid)
                ->update([
                    $column => $revokeFlag,
                    'spillovercompleted' => $newSpilloverCompleted,
                    'updatedon' => $now,
                    'updatedby' => $userId,
                ]);

            // Completed -> Carry Forward: recalculate and persist remaining mandays.
            if ($newSpilloverCompleted === 'N') {
                DB::table(self::$institution_table)
                    ->where('instid', $schedule->instid)
                    ->update([
                        'remainingmandays' => $newRemainingMandays,
                        'updatedon' => $now,
                        'updatedby' => $userId,
                    ]);
            } else {
                // Carry Forward -> Completed: clear remaining mandays in institution master.
                DB::table(self::$institution_table)
                    ->where('instid', $schedule->instid)
                    ->update([
                        'remainingmandays' => null,
                        'updatedon' => $now,
                        'updatedby' => $userId,
                    ]);
            }

            // Carry Forward -> Completed: capture executed remaining mandays in revoke history.
            $executedRemainingMandays = ($newSpilloverCompleted === 'Y') ? $currentRemainingMandays : null;
            $loggedRemainingMandays = ($newSpilloverCompleted === 'N') ? $newRemainingMandays : null;
            DB::table('audit.spilloverrevoke')->insert([
                'auditscheduleid' => $schedule->auditscheduleid,
                'instid' => $schedule->instid,
                'auditquarter' => $schedule->auditquartercode,
                'fromspillovercompleted' => $schedule->spillovercompleted ?? 'N',
                'tospillovercompleted' => $newSpilloverCompleted,
                'exe_remainingmandays' => $executedRemainingMandays,
                'remainingmandays' => $loggedRemainingMandays,
                'remarks' => $remarks,
                'statusflag' => 'Y',
                'revokedby' => $userId,
                'revokedon' => $now,
            ]);

            return [
                'auditscheduleid' => $schedule->auditscheduleid,
                'spillovercompleted' => $newSpilloverCompleted,
                'working_days' => $workingDays,
                'teamsize' => $teamSize,
                'total_working_mandays' => $totalWorkingMandays,
                'mandays' => $mandays,
                'remainingmandays' => $newRemainingMandays,
                'remarks' => $remarks,
            ];
        });
    }

    public static function resolveSpilloverRevokeColumn()
    {
        $candidates = ['spill_revokeflag', 'revokeflag', 'revoke', 'spilloverrevokeflag'];

        $columns = DB::table('information_schema.columns')
            ->where('table_schema', 'audit')
            ->where('table_name', 'inst_auditschedule')
            ->whereIn('column_name', $candidates)
            ->pluck('column_name')
            ->toArray();

        foreach ($candidates as $candidate) {
            if (in_array($candidate, $columns, true)) {
                return $candidate;
            }
        }

        return null;
    }

    // ----------------------------------------------------------------------------SpillOver Revoke--------------------------------------

    public static function checkisteamassigned($deptcode, $distcode)
    {
        try {

            $query = DB::table(self::$teamassignments_table.' as ta')
                ->join(self::$institution_table.' as inst', 'inst.instid', '=', 'ta.instid')
                ->where('inst.distcode', $distcode)
                ->where('inst.deptcode', $deptcode)
                ->exists();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching assigned team details. Please contact the administrator.';

            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function assignteams($deptcode, $distcode, $quartercode, $loginid)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception('Deptcode is not available');
            }
            if (empty($distcode)) {
                throw new Exception('Distcode is not available');
            }
            if (empty($quartercode)) {
                throw new Exception('Quartercode is not available');
            }
            DB::beginTransaction();

            // Execute the query

            $result = DB::select('SELECT * FROM audit.fn_auditplan_final(?, ?, ?, ?)', [$deptcode, $distcode, $quartercode, $loginid]);

            // Commit the transaction if everything goes fine
            DB::commit();

            return $result;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            // \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();

            // Optionally, you can log the error or handle it accordingly
            // Log::error('Error in executing loop_until_finished: ' . $e->getMessage());

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getchecklistteamdet($deptcode, $distcode, $quartercode)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception('Deptcode is not available');
            }
            if (empty($distcode)) {
                throw new Exception('Distcode is not available');
            }
            if (empty($quartercode)) {
                throw new Exception('Quartercode is not available');
            }

            $teamdetails = DB::table('audit.team_assignments as ta')
                ->selectRaw("
                    DISTINCT ON (ta.team_name)
                    ta.team_name,
                    th.username || ' (' || deth.desigesname || ')' AS teamhead,
                    ta.team_size,
                    members_list.members
                ")
                ->join('audit.deptuserdetails as th', 'th.deptuserid', '=', 'ta.team_head')
                ->join('audit.mst_designation as deth', 'deth.desigcode', '=', 'th.desigcode')
                ->leftJoin(DB::raw("LATERAL (
                    SELECT string_agg(member, ', ' ORDER BY member) AS members
                    FROM (
                        SELECT DISTINCT u.username || ' (' || de.desigesname || ')' AS member
                        FROM unnest(ta.team_users) AS uid(deptuserid)
                        JOIN audit.deptuserdetails u ON u.deptuserid = uid.deptuserid
                        JOIN audit.mst_designation de ON de.desigcode = u.desigcode
                    ) AS sub
                ) members_list"), DB::raw('true'), '=', DB::raw('true'))
                ->where('th.deptcode', $deptcode)
                ->where('th.distcode', $distcode)
                ->orderBy('ta.team_name')
                ->orderBy('th.username')
                ->orderBy('deth.desigesname')
                ->get();

            $teamdet = DB::table(self::$teamassignments_table.' as ta')
                ->join(DB::raw('unnest(ta.team_users) AS uid(deptuserid)'), DB::raw('true'), DB::raw('true'), DB::raw('true'))
                ->join(self::$userdetail_table.' as u', 'u.deptuserid', '=', 'uid.deptuserid')
                ->join(self::$designation_table.' as de', 'de.desigcode', '=', 'u.desigcode')
                ->join(self::$userdetail_table.' as th', 'th.deptuserid', '=', 'ta.team_head')
                ->join(self::$designation_table.' as deth', 'deth.desigcode', '=', 'th.desigcode')
                ->join(self::$institution_table.' as ins', 'ins.instid', '=', 'ta.instid')
                ->where('ins.distcode', $distcode)
                ->where('ins.deptcode', $deptcode)
                ->selectRaw("
                    ins.instename,
                     ins.insttname,
                    ta.team_name,
                    th.username || ' (' || deth.desigesname || ')' AS teamhead,
                    string_agg(u.username || ' (' || de.desigesname || ')', ', ' ORDER BY u.username) AS members,
                    ta.from_date,
                    ta.to_date,
                    team_size,
                    ta.mandays
                ")
                ->groupByRaw('ins.instename, ins.insttname,ta.from_date, ta.to_date, ta.mandays, ta.assign_id, th.username, deth.desigesname')
                ->orderBy('ta.assign_id')
                ->get();

            // 👥 Step 1: Calculate total audit days (excluding weekends and holidays)
            $totalAuditDays = DB::table(DB::raw("generate_series('2025-04-15'::date, '2025-06-23'::date, interval '1 day') as d"))
                ->whereRaw('EXTRACT(DOW FROM d) NOT IN (0,6)')  // Exclude weekends (Sunday = 0, Saturday = 6)
                ->whereNotIn('d', function ($q) {
                    $q->select('holiday_date')->from('audit.mst_holiday');  // Exclude holidays
                })
                ->count();

            // 👥 Step 2: Get all assigned users (team_head and team_users)
            $assignments = DB::table('audit.team_assignments')
                ->select('team_head as person_id', 'from_date', 'to_date')
                ->unionAll(
                    DB::table('audit.team_assignments')
                        ->select(DB::raw('unnest(team_users) as person_id'), 'from_date', 'to_date')
                );

            // 📊 Step 3: Person-wise period and allotted audit days (merge with assignments)
            $personPeriods = DB::table(DB::raw("({$assignments->toSql()}) as a"))
                ->mergeBindings($assignments)
                ->select(
                    'person_id',
                    DB::raw('MIN(from_date) as period_start'),
                    DB::raw('MAX(to_date) as period_end'),
                    DB::raw("SUM(
            (
                SELECT COUNT(*)
                FROM generate_series(from_date, to_date, interval '1 day') AS d
                WHERE EXTRACT(DOW FROM d) NOT IN (0,6)
                AND d::date NOT IN (SELECT holiday_date FROM audit.mst_holiday)
            )
        ) as allotted_days")
                )
                ->groupBy('person_id');

            // 👤 Step 4: Filter users from specific dist and dept
            $allUsers = DB::table('audit.deptuserdetails as u')
                ->leftJoin(DB::raw('(
        SELECT team_head AS person_id FROM audit.team_assignments
        UNION
        SELECT unnest(team_users) AS person_id FROM audit.team_assignments
    ) as a'), 'a.person_id', '=', 'u.deptuserid')
                ->join('audit.mst_designation as de', 'de.desigcode', '=', 'u.desigcode')
                ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'u.deptuserid')
                ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
                ->join('audit.rolemapping as ro', 'ro.rolemappingid', '=', 'ch.rolemappingid')
                ->where([
                    ['u.distcode', '=', $distcode],
                    ['u.deptcode', '=', $deptcode],
                    ['ro.roleactioncode', '=', '04'],
                    ['uc.statusflag', '=', 'Y'],
                    ['u.statusflag', '=', 'Y'],
                    ['u.reservelist', '=', 'Y'],
                ])
                ->whereIn('u.desigcode', function ($query) use ($deptcode) {
                    $query
                        ->select('desigcode')
                        ->from('audit.mst_designation')
                        ->where('deptcode', $deptcode)
                        ->where('belowaddesig', 'Y');
                })
                ->select(
                    'u.deptuserid as person_id',
                    DB::raw("u.username || ' (' || de.desigesname || ')' as username_with_designation"),
                    'de.desigesname'
                );

            // 🧾 Step 5: Join users with periods and calculate status
            $finalList = DB::table(DB::raw("({$allUsers->toSql()}) as u"))
                ->mergeBindings($allUsers)
                ->leftJoinSub($personPeriods, 'p', 'p.person_id', '=', 'u.person_id')
                ->select(
                    'u.username_with_designation as username',
                    DB::raw("COALESCE(TO_CHAR(p.period_start, 'DD/MM/YYYY') || ' - ' || TO_CHAR(p.period_end, 'DD/MM/YYYY'), 'NIL') as engagement_period"),
                    DB::raw("
            CASE
                WHEN p.allotted_days = {$totalAuditDays} THEN 'Fully Engaged'
                WHEN p.allotted_days IS NULL THEN 'Idle'
                ELSE 'Partially engaged'
            END as status
        "),
                    DB::raw("{$totalAuditDays} as total_audit_days"),
                    DB::raw('COALESCE(p.allotted_days, 0) as allotted_days'),
                    'u.desigesname'
                );

            // 🧑‍💼 Step 6: Custom ordering of statuses (Fully Engaged -> Partially Engaged -> Idle)
            $result = $finalList
                ->orderByRaw("
        CASE
            WHEN
                (CASE
                    WHEN p.allotted_days = {$totalAuditDays} THEN 'Fully Engaged'
                    WHEN p.allotted_days IS NULL THEN 'Idle'
                    ELSE 'Partially engaged'
                END) = 'Fully Engaged' THEN 1
            WHEN
                (CASE
                    WHEN p.allotted_days = {$totalAuditDays} THEN 'Fully Engaged'
                    WHEN p.allotted_days IS NULL THEN 'Idle'
                    ELSE 'Partially engaged'
                END) = 'Partially engaged' THEN 2
            WHEN
                (CASE
                    WHEN p.allotted_days = {$totalAuditDays} THEN 'Fully Engaged'
                    WHEN p.allotted_days IS NULL THEN 'Idle'
                    ELSE 'Partially engaged'
                END) = 'Idle' THEN 3
            ELSE 4
        END
    ")
                ->get();

            $idleinst = DB::table(self::$institution_table.' as inst')
                ->join(self::$mapinst_table.' as map', 'inst.instid', '=', 'map.instid')
                //   ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'map.desigcode')
                ->selectRaw('inst.instid,inst.instename,inst.insttname,inst.mandays,inst.carryforward,

                     count( map.desigcode ORDER BY map.desigcode) AS desigcodes, inst.rankorder')
                ->where('inst.deptcode', $deptcode)
                ->where('inst.distcode', $distcode)
                ->where('inst.allocatedflag', 'N')
                ->groupBy(
                    'inst.instid',
                    'inst.mandays',
                    'inst.rankorder',
                    // 'desig.desigelname',
                    // 'desig.desigtlname',
                )
                ->get();

            $totalinstcount = DB::table(self::$institution_table.' as inst')
                ->where('inst.deptcode', $deptcode)
                ->where('inst.distcode', $distcode)
                ->where('inst.audit_quarter', $quartercode)
                ->count();

            $totalauditorscount = DB::table(self::$userdetail_table.' as du')
                ->join(self::$designation_table.' as desig', 'desig.desigcode', '=', 'du.desigcode')
                ->join(self::$userchargedetail_table.' as uc', 'uc.userid', '=', 'du.deptuserid')
                ->join(self::$chargedetail_table.' as c', 'uc.chargeid', '=', 'c.chargeid')
                ->join(self::$rolemapping_table.' as ro', 'ro.rolemappingid', '=', 'c.rolemappingid')
                ->where('du.deptcode', $deptcode)
                ->where('du.distcode', $distcode)
                ->where('ro.roleactioncode', '04')
                ->where('uc.statusflag', 'Y')
                ->where('du.reservelist', 'Y')
                ->where('du.statusflag', 'Y')
                ->count();

            $designationDetails = DB::table(self::$userdetail_table.' as du')
                ->join(self::$designation_table.' as desig', 'desig.desigcode', '=', 'du.desigcode')
                ->join(self::$userchargedetail_table.' as uc', 'uc.userid', '=', 'du.deptuserid')
                ->join(self::$chargedetail_table.' as c', 'uc.chargeid', '=', 'c.chargeid')
                ->selectRaw('desig.desigelname,desig.desigtlname,count(du.desigcode)')
                ->where('du.deptcode', $deptcode)
                ->where('du.distcode', $distcode)
                ->where('uc.statusflag', 'Y')
                ->where('du.reservelist', 'Y')
                ->where('du.statusflag', 'Y')
                ->groupBy(
                    'du.desigcode', 'desig.desigelname', 'desig.desigtlname'
                )
                ->get();

            $sessiondept = DB::table(self::$deptartment_table.' as dept')
                ->select('dept.deptelname', 'dept.depttlname')
                ->where('dept.deptcode', $deptcode)
                ->get();

            $sessiondist = DB::table(self::$dist_table.' as dist')
                ->select('dist.distename', 'dist.disttname')
                ->where('dist.distcode', $distcode)
                ->get();

            return [
                'totalteamdetails' => $teamdetails,
                'idelusers' => $result,
                'teamdetails' => $teamdet,
                'idleinst' => $idleinst,
                'totalinstcount' => $totalinstcount,
                'totalauditorscount' => $totalauditorscount,
                'designationDetails' => $designationDetails,
                'deptname' => $sessiondept,
                'distname' => $sessiondist,
            ];

            // return $results;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: '.$e->getMessage());
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    // public static function getAuditorsInstdet($deptcode, $distcode, $quartercode)
    // {
    //     try {
    //         if (empty($deptcode)) {
    //             throw new Exception('Deptcode is not available');
    //         }
    //         if (empty($distcode)) {
    //             throw new Exception('Distcode is not available');
    //         }
    //         if (empty($quartercode)) {
    //             throw new Exception('Quartercode is not available');
    //         }

    //         $prioritydetails = DB::table(self::$deptartment_table . ' as dept')
    //             ->where('dept.deptcode', $deptcode)
    //             ->select('dept.supplementaryplan', 'dept.inst_priority', 'autoplandate', 'nextinst_priority')
    //             ->get();
    //         $supplementaryflag = $prioritydetails[0]->supplementaryplan;
    //         // $institution_priority = $prioritydetails[0]->inst_priority;
    //         $autoplandate = $prioritydetails[0]->autoplandate;

    //         $currentDate = Carbon::today();

    //         // Check if autoplandate is available and valid
    //         if (is_null($autoplandate)) {
    //             throw new \Exception('Autoplandate is null for department ' . $deptcode);
    //         }

    //         if ($currentDate->gte($autoplandate)) {
    //             $institution_priority = $prioritydetails[0]->nextinst_priority;
    //         } else {
    //             $institution_priority = $prioritydetails[0]->inst_priority;
    //         }

    //         $users = DB::table(self::$userdetail_table . ' as du')
    //             ->join(self::$userchargedetail_table . ' as uc', 'uc.userid', '=', 'du.deptuserid')
    //             ->join(self::$chargedetail_table . ' as cd', 'cd.chargeid', '=', 'uc.chargeid')
    //             ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'cd.rolemappingid')
    //             ->join(self::$designation_table . ' as dd', 'dd.desigcode', '=', 'du.desigcode')
    //             ->join(self::$deptartment_table . ' as dept', 'dept.deptcode', '=', 'du.deptcode')
    //             ->where('du.deptcode', $deptcode)
    //             ->where('du.distcode', $distcode)
    //             ->where('du.statusflag', 'Y')
    //             ->where('uc.statusflag', 'Y')
    //             ->where('du.reservelist', 'Y')
    //             ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))
    //             // ->where('du.chargeassigned', 'Y')
    //             // ->where('du.auditorflag', 'Y')
    //             ->orderBy('dd.desigcode', 'asc')
    //             ->groupBy('dd.desigcode', 'dd.desigelname', 'dd.desigtlname', 'du.deptuserid', 'du.username', 'du.usertamilname', 'dept.deptesname', 'dept.depttsname')
    //             ->select('du.deptuserid', 'du.username', 'du.usertamilname', 'dd.desigelname', 'dd.desigtlname', 'dept.deptesname', 'dept.depttsname')
    //             ->get();

    //         $column = 'inst.' . $quartercode;

    //         $inst_query = DB::table(self::$institution_table . ' as inst')
    //             ->join(self::$deptartment_table . ' as dept', 'dept.deptcode', '=', 'inst.deptcode')
    //             ->join(self::$mstauditeeinscategory_table . ' as cat', 'cat.catcode', '=', 'inst.catcode')
    //             // ->join(self::$mapinst_table . ' as map', 'map.instid', '=', 'inst.instid')
    //             ->where('inst.deptcode', $deptcode)
    //             ->where('inst.distcode', $distcode)
    //             ->where('inst.statusflag', 'Y')
    //             ->where($column, 'Y')
    //             ->where('inst.auditmode', '<>', 'T')
    //             ->select(
    //                 'inst.mandays',
    //                 'inst.carryforward',
    //                 'inst.checkcarryforward',
    //                 'inst.instid',
    //                 'inst.instename',
    //                 'inst.insttname',
    //                 'inst.spillover',
    //                 'inst.remainingmandays',
    //                 'cat.catename',
    //                 'cat.cattname',
    //                 'inst.teamsize'
    //             );
    //         if ($supplementaryflag == 'Y') {
    //             $inst_query->where('inst.inst_priority_kms', $institution_priority);
    //         }
    //         $inst_query
    //             ->orderByRaw("inst.spillover = 'Y' ASC")
    //             ->orderBy('cat.catename', 'asc')
    //             ->orderBy('inst.mandays', 'asc')
    //             ->orderBy('inst.instename', 'asc');
    //         // ->groupBy(
    //         //     'inst.instid',
    //         //     'inst.mandays',
    //         //     'inst.carryforward',
    //         //     'inst.instename',
    //         //     'inst.insttname',
    //         //     'cat.catename',
    //         //     'cat.cattname',)
    //         $inst_det = $inst_query->get();
    //         $performanceinst_det = DB::table('mst_prauditinstmapping as per')
    //             ->join(self::$institution_table . ' as inst', 'per.instid', '=', 'inst.instid')
    //             ->join(self::$deptartment_table . ' as dept', 'dept.deptcode', '=', 'inst.deptcode')
    //             // ->join(self::$mapinst_table . ' as map', 'map.instid', '=', 'inst.instid')
    //             ->where('inst.deptcode', $deptcode)
    //             ->where('per.statusflag', 'Y')
    //             ->where('inst.distcode', $distcode)
    //             ->where('inst.statusflag', 'Y')
    //             ->where('per.quartercode', $quartercode)
    //             // ->where('inst.auditmode', '<>', 'T')
    //             ->where('per.prioritycode', '=', '02')
    //             ->select(
    //                 'dept.mandays',
    //                 'inst.instid',
    //                 'inst.instename',
    //                 'inst.insttname',
    //                 'dept.teamsize'
    //             )
    //             ->orderBy('inst.mandays', 'asc')
    //             ->orderBy('inst.instename', 'asc');

    //         $performanceinst_det = $performanceinst_det->get();

    //         return [
    //             'users' => $users,
    //             'inst_det' => $inst_det,
    //             'performanceinst_det' => $performanceinst_det
    //         ];
    //         // return $results;
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         $customMessage = 'A database error occurred while fetching Auditors and Institution Details. Please contact the administrator.';

    //         \Log::error('SQL Error: ' . $e->getMessage());
    //         throw new \Exception($e->getMessage(), 500);
    //     } catch (\Exception $e) {
    //         throw new \Exception($e->getMessage(), 409);
    //     }
    // }

    public static function dist_details($deptcode, $userchargeid, $param, $regioncode = null)
    {
        try {
            $reg_query = DB::table(self::$userchargedetail_table.' as uc')
                ->select('uc.regioncodes')
                ->where('uc.userchargeid', $userchargeid)
                ->where('uc.statusflag', 'Y')
                ->get();

            $regioncodes = $reg_query[0]->regioncodes;

            $reg_arr = explode(',', trim($regioncodes, '{}'));
            $query = DB::table(self::$institution_table.' as inst')
                ->join(self::$deptartment_table.' as dept', 'dept.deptcode', '=', 'inst.deptcode')
                ->join(self::$region_table.' as reg', 'reg.regioncode', '=', 'inst.regioncode')
                ->select(
                    'dept.deptcode',
                    'dept.deptesname',
                    'dept.depttsname',
                    'reg.regioncode',
                    'reg.regionename',
                    'reg.regiontname'
                )
                ->whereIn('reg.regioncode', $reg_arr)
                ->where('dept.deptcode', $deptcode)
                ->where('inst.statusflag', 'Y')
                ->groupBy(
                    'dept.deptcode',
                    'dept.deptesname',
                    'dept.depttsname',
                    'reg.regioncode',
                    'reg.regionename',
                    'reg.regiontname'
                );

            $query->when($param === 'district', function ($q) use ($regioncode) {
                $q
                    ->join(self::$dist_table.' as dist', 'dist.distcode', '=', 'inst.distcode')
                    ->addSelect(
                        'dist.distcode',
                        'dist.distename',
                        'dist.disttname'
                    )
                    ->where('reg.regioncode', $regioncode)
                    ->groupBy(
                        'dist.distcode',
                        'dist.distename',
                        'dist.disttname'
                    )
                    ->orderBy('dist.distename');
            });

            return $query->get();
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching details. Please contact the administrator.';
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getAuditorsInstdet($deptcode, $distcode, $quartercode, $plan_config_details)
    {
        try {

            $institution_priority = $plan_config_details->prioritycode;

            $users = DB::table(self::$userdetail_table.' as du')
                ->join(self::$userchargedetail_table.' as uc', 'uc.userid', '=', 'du.deptuserid')
                ->join(self::$chargedetail_table.' as cd', 'cd.chargeid', '=', 'uc.chargeid')
                ->join(self::$rolemapping_table.' as rm', 'rm.rolemappingid', '=', 'cd.rolemappingid')
                ->join(self::$designation_table.' as dd', 'dd.desigcode', '=', 'du.desigcode')
                ->join(self::$deptartment_table.' as dept', 'dept.deptcode', '=', 'du.deptcode')
                ->where('du.deptcode', $deptcode)
                ->where('du.distcode', $distcode)
                ->where('du.statusflag', 'Y')
                ->where('uc.statusflag', 'Y')
                ->where('du.reservelist', 'Y')
                ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))
                // ->where('du.chargeassigned', 'Y')
                // ->where('du.auditorflag', 'Y')
                ->orderBy('dd.desigcode', 'asc')
                ->groupBy(
                    'dd.desigcode',
                    'dd.desigelname',
                    'dd.desigtlname',
                    'du.deptuserid',
                    'du.username',
                    'du.usertamilname',
                    'dept.deptesname',
                    'dept.depttsname'
                )
                ->select(
                    'du.deptuserid',
                    'du.username',
                    'du.usertamilname',
                    'dd.desigelname',
                    'dd.desigtlname',
                    'dept.deptesname',
                    'dept.depttsname'
                )
                ->get();

            $column = 'inst.'.$quartercode;

            $inst_query = DB::table(self::$institution_table.' as inst')
                ->join(self::$deptartment_table.' as dept', 'dept.deptcode', '=', 'inst.deptcode')
                ->join(self::$mstauditeeinscategory_table.' as cat', 'cat.catcode', '=', 'inst.catcode')
                // ->join(self::$mapinst_table . ' as map', 'map.instid', '=', 'inst.instid')
                ->where('inst.deptcode', $deptcode)
                ->where('inst.distcode', $distcode)
                ->where('inst.statusflag', 'Y')
                ->where($column, 'Y')
                // ->where('inst.auditmode', '<>', 'T')
                ->select(
                    'inst.mandays',
                    'inst.carryforward',
                    'inst.checkcarryforward',
                    'inst.instid',
                    'inst.instename',
                    'inst.insttname',
                    'inst.spillover',
                    'inst.inst_kms',
                    'inst.remainingmandays',
                    'cat.catename',
                    'cat.cattname',
                    'inst.teamsize'
                );

            if ($institution_priority != null) {
                $inst_query->where('inst.inst_priority_kms', $institution_priority);
            }

            $inst_query
                ->orderByRaw("inst.spillover = 'Y' ASC")
                ->orderBy('inst.teamsize', 'asc')
                ->orderBy('inst.inst_priority_kms', 'asc')
                ->orderBy('inst.mandays', 'asc');

            $plannedinst_query = clone $inst_query;
            $idleinst_query = clone $inst_query;
            $inst_det_query = clone $inst_query;

            $plannedinst_det = $plannedinst_query
                ->where('inst.allocatedflag', 'Y')
                ->get();

            $idleinst_det = $idleinst_query
                ->where(function ($q) {
                    $q->where('inst.allocatedflag', 'N')
                        ->orWhereNull('inst.allocatedflag');
                })
                ->get();

            $inst_det = $inst_det_query->get();

            $performanceinst_det = DB::table('audit.mst_prauditinstmapping as per')
                ->join(self::$institution_table.' as inst', 'per.instid', '=', 'inst.instid')
                ->join(self::$deptartment_table.' as dept', 'dept.deptcode', '=', 'inst.deptcode')
                // ->join(self::$mapinst_table . ' as map', 'map.instid', '=', 'inst.instid')
                ->where('inst.deptcode', $deptcode)
                ->where('per.statusflag', 'Y')
                ->where('inst.distcode', $distcode)
                ->where('inst.statusflag', 'Y')
                ->where('per.quartercode', $quartercode)
                // ->where('inst.auditmode', '<>', 'T')
                ->select(
                    'dept.mandays',
                    'inst.instid',
                    'inst.instename',
                    'inst.insttname',
                    'dept.teamsize'
                )
                ->orderBy('inst.mandays', 'asc')
                ->orderBy('inst.instename', 'asc');

            if ($institution_priority === null) {
                $performanceinst_det->whereNull('per.prioritycode');
            } else {
                $performanceinst_det->where('per.prioritycode', $institution_priority);
            }

            $performanceinst_det = $performanceinst_det->get();

            return [
                'users' => $users,
                'inst_det' => $inst_det,
                'plannedinst_det' => $plannedinst_det,
                'idleinst_det' => $idleinst_det,
                'performanceinst_det' => $performanceinst_det,
            ];
            // return $results;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching Auditors and Institution Details. Please contact the
        administrator.';

            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getmandaysDetais($deptcode, $distcode, $quartercode)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception('Deptcode is not available');
            }
            if (empty($distcode)) {
                throw new Exception('Distcode is not available');
            }
            if (empty($quartercode)) {
                throw new Exception('Quartercode is not available');
            }

            $totalworkingdays = DB::select("WITH all_days AS (
    SELECT generate_series('2025-04-15', '2025-06-30', interval '1 day') AS day
  ),
  working_days AS (
    SELECT day
    FROM all_days
    WHERE EXTRACT(DOW FROM day) NOT IN (0, 6)  -- Exclude Sunday(0) and Saturday(6)
      AND day NOT IN (SELECT holiday_date FROM audit.mst_holiday)
  ),
  cutoff AS (
    SELECT day AS cutoff_date
    FROM working_days
    WHERE day < '2025-06-30'
    ORDER BY day DESC
    OFFSET 4 LIMIT 1
  ),
  final_days AS (
    SELECT day
    FROM working_days, cutoff
    WHERE day >= '2025-04-01' AND day <= cutoff.cutoff_date
  ),
  final_result AS (
    SELECT
      ARRAY_AGG(day ORDER BY day) AS assigned_users,
      MAX(cutoff.cutoff_date) AS last_date,
      COUNT(*) AS total_days
    FROM final_days, cutoff
  )
  SELECT
    fr.total_days
  FROM final_result fr;
 ");

            $totalusers = DB::table(self::$userdetail_table.' as du')
                ->join(self::$designation_table.' as de', 'de.desigcode', '=', 'du.desigcode')
                ->join(self::$userchargedetail_table.' as uc', 'uc.userid', '=', 'du.deptuserid')
                ->join(self::$chargedetail_table.' as c', 'uc.chargeid', '=', 'c.chargeid')
                ->join(self::$rolemapping_table.' as ro', 'ro.rolemappingid', '=', 'c.rolemappingid')
                ->where('ro.roleactioncode', '04')
                ->where('uc.statusflag', 'Y')
                ->where('du.statusflag', 'Y')
                ->where('reservelist', 'Y')
                ->where('du.distcode', $distcode)
                ->where('du.deptcode', $deptcode)
                ->where('uc.statusflag', 'Y')
                ->count();

            $sumMandays = DB::select("  select sum(mandays) from audit.mst_institution where distcode = '029' AND deptcode = '03'
    ");

            return [
                'totalworkingdays' => $totalworkingdays,
                'sumMandays' => $sumMandays,
                'totalusers' => $totalusers,
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching Auditors and Institution Details. Please contact the administrator.';

            \Log::error('SQL Error: '.$e->getMessage());
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    // public static function getalldetails($deptcode, $distcode, $quartercode)
    // {
    //     try {
    //         if (empty($deptcode)) {
    //             throw new Exception('Deptcode is not available');
    //         }
    //         if (empty($distcode)) {
    //             throw new Exception('Distcode is not available');
    //         }
    //         if (empty($quartercode)) {
    //             throw new Exception('Quartercode is not available');
    //         }
    //         $result = DB::select('SELECT * FROM audit.checklistdel(?, ?)', [$deptcode, $distcode]);

    //         return $result;
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

    //         \Log::error('SQL Error: ' . $e->getMessage());
    //         throw new \Exception($e->getMessage(), 500);
    //     } catch (\Exception $e) {
    //         throw new \Exception($e->getMessage(), 409);
    //     }
    // }

    public static function getalldetails($deptcode, $distcode, $loginid)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception('Deptcode is not available');
            }
            if (empty($distcode)) {
                throw new Exception('Distcode is not available');
            }
            if (empty($loginid)) {
                throw new Exception('Login Detail is not available');
            }
            $result = DB::select('SELECT * FROM audit.checklistdel(?, ?,?)', [$deptcode, $distcode, $loginid]);

            return $result;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: '.$e->getMessage());
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function checkisPlanfinalized($deptcode, $distcode)
    {
        try {

            $query = DB::table(self::$auditor_instmapping_table.' as map')
                ->select('map.autoplanstatus', 'map.pendinginststatus')
                ->where('map.distcode', $distcode)
                ->where('map.deptcode', $deptcode)
                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching details. Please contact the administrator.';
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    // public static function finaliseplan($deptcode, $distcode)
    // {
    //     try {
    //         if (empty($deptcode)) {
    //             throw new Exception('Deptcode is not available');
    //         }
    //         if (empty($distcode)) {
    //             throw new Exception('distcode is not available');
    //         }

    //         $session = session('charge');
    //         $userchargeid = $session->userchargeid;

    //         DB::beginTransaction();

    //         // $isexitdone = self::checkexitmeetstatus($deptcode, $distcode);

    //         // Execute the query
    //         $query = DB::select(
    //             'SELECT * FROM audit.distributeauditteamplan' . '(:distcode, :deptcode, :userchargeid)',
    //             [
    //                 'distcode' => $distcode,
    //                 'deptcode' => $deptcode,
    //                 'userchargeid' => $userchargeid,
    //             ]
    //         );

    //         // Commit the transaction if everything goes fine
    //         DB::commit();
    //         return $query;
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         $customMessage = 'A database error occurred while finalising. Please contact the administrator.';

    //         \Log::error('SQL Error: ' . $e->getMessage());
    //         throw new \Exception($e->getMessage(), 500);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         // Optionally, you can log the error or handle it accordingly
    //         Log::error('Error in executing finaliseplan_function: ' . $e->getMessage());
    //         throw new \Exception($e->getMessage(), 409);
    //     }
    // }

    public static function finaliseplan($deptcode, $distcode, $callfor)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception('Deptcode is not available');
            }
            if (empty($distcode)) {
                throw new Exception('distcode is not available');
            }

            $session = session('charge');
            $sessionuser = session('user');
            $loginid = $sessionuser->loginid;
            $userchargeid = $session->userchargeid;

            DB::beginTransaction();

            // Execute the query
            $query = DB::select(
                'SELECT * FROM audit.distributeauditteamplan'.'(:distcode, :deptcode, :userchargeid, :callfor, :loginid)',
                [
                    'distcode' => $distcode,
                    'deptcode' => $deptcode,
                    'userchargeid' => $userchargeid,
                    'callfor' => $callfor,
                    'loginid' => $loginid,
                ]
            );

            // Commit the transaction if everything goes fine
            DB::commit();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while finalising. Please contact the administrator.';

            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();

            // Optionally, you can log the error or handle it accordingly
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function markChecklistPlanDetailsVerified($deptcode, $regioncode, $distcode): array
    {
        $updated = DB::table('audit.auditor_instmapping')
            ->where('deptcode', $deptcode)
            ->where('regioncode', $regioncode)
            ->where('distcode', $distcode)
            ->update([
                'verifiedplandetails' => 'F',
            ]);

        return [
            'updated' => $updated,
        ];
    }

    // don't forget to get the use///
    public static function getauditquarter($deptcode)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception('Deptcode is not available');
            }
            $deptData = DB::table(self::$deptartment_table)
                ->select('nextquarter', 'nextquarterfromdate', 'nextquartertodate', 'currentquarter',
                    'currentquarterfromdate', 'currentquartertodate', 'autoplandate')
                ->where('deptcode', $deptcode)
                ->get();
            // return $deptData;
            $autoplandate = $deptData[0]->autoplandate;

            $currentDate = Carbon::today();

            // Check if autoplandate is available and valid
            if (is_null($autoplandate)) {
                throw new \Exception('Autoplandate is null for department '.$deptcode);
            }

            // Compare current date with autoplandate
            if ($currentDate->gte($autoplandate)) {
                // If current date is greater than or equal to autoplandate, use next quarter
                $planQuarter = $deptData[0]->nextquarter;
                $planQuarterFromDate = $deptData[0]->nextquarterfromdate;
                $planQuarterToDate = $deptData[0]->nextquartertodate;
            } else {
                // If current date is less than autoplandate, use current quarter
                $planQuarter = $deptData[0]->currentquarter;
                $planQuarterFromDate = $deptData[0]->currentquarterfromdate;
                $planQuarterToDate = $deptData[0]->currentquartertodate;
            }

            return $planQuarter;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public function creatauditschedule_dropdownvalues(Request $request)
    {
        // $auditplanid = $request->query('auditplanid'); // Default to '1' if no value is provided.
        if ($request->auditplanid) {
            $auditplanid = Crypt::decryptString($request->auditplanid);
            $userid = $request->userid;
        } else {
            // print_r($auditplanid);
            $session = $request->session();
            if ($session->has('user')) {
                $user = $session->get('user');
                $userid = $user->userid ?? null;
            } else {
                return 'No user found in session.';
            }
        }

        // echo $auditplanid;

        // echo $userid;
        // Fetch the data based on the provided auditplanid
        $inst = AuditManagementModel::auditplandet($auditplanid, $userid);

        $catcode = $inst->first()->catcode;
        $deptcode = $inst->first()->deptcode;
        $subcatid = $inst->first()->subcatid;
        $planquartercode = $inst->first()->auditquartercode;  // fetch from plandet

        $fetchcurrquarter = AuditManagementModel::getCurrentQuarter($deptcode, $planquartercode);
        //  $str_Quarter = "Q2";
        $str_Quarter = $fetchcurrquarter->quarterfrom;
        $str_Quarter = date('Y-m-01', strtotime($str_Quarter));

        $end_Quarter = $fetchcurrquarter->quarterto;
        $end_Quarter = date('Y-m-t', strtotime($end_Quarter));

        $Quarter = ['fromquarter' => $str_Quarter, 'toquarter' => $end_Quarter];

        $Accountparticulars = self::audit_particulars($catcode, $deptcode, $subcatid);
        $quartercode = $inst->first()->auditquartercode;
        $schdel = DB::table('audit.inst_auditschedule')
            ->where('auditplanid', '=', $inst->first()->auditplanid)
            ->get();

        if (count($schdel) > 0) {
            $rcno = $schdel->first()->rcno;
        } else {
            $deptdel = DB::table('audit.mst_dept')
                // ->where('auditplanid', '=', $inst->first()->auditplanid)
                ->where('deptcode', '=', $deptcode)
                ->get();

            if ($deptdel->isNotEmpty()) {
                // Ensure there's a valid first item before accessing its properties
                $firstItem = $deptdel->first();

                if ($firstItem) {
                    // Now safely access properties on the first item
                    $rcnocount = $firstItem->rcno;
                    $deptsname = $firstItem->deptesname;
                    $deptfirstcharacter = substr($deptsname, 0, 1);  // Corrected the typo

                    // Increment the count, and ensure it's padded with leading zeros
                    $incrementcount = $rcnocount ? $rcnocount + 1 : 1;

                    // Pad the increment count with leading zeros to make it 4 digits
                    $incrementcount = str_pad($incrementcount, 4, '0', STR_PAD_LEFT);

                    // Concatenate the values
                    $rcno = $deptfirstcharacter.'25'.$quartercode.$incrementcount;
                }
            }
        }

        $auditperiod = AuditPeriodModel::select('auditperiodid', DB::raw("CONCAT(fromyear, ' - ', toyear) AS audit_period"))
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->where('financestatus', 'N')
            ->orderBy('fromyear', 'desc')
            ->get();

        $annadhanamperiod = AuditPeriodModel::select('auditperiodid', DB::raw("CONCAT(fromyear, ' - ', toyear) AS audit_period"))
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->where('financestatus', 'Y')
            ->orderBy('fromyear', 'desc')
            ->get();

        $DraftStatus['auditschid'] = '';
        $DraftStatus['exists'] = 'N';

        $hasexists = DB::table('audit.inst_auditschedule')
            ->where('auditplanid', $auditplanid)
            ->where('statusflag', 'Y')
            ->exists();

        if ($hasexists) {
            $schedules = DB::table('audit.inst_auditschedule')
                ->select('auditscheduleid')
                ->where('auditplanid', $auditplanid)
                ->where('statusflag', 'Y')
                ->first();

            $DraftStatus['auditschid'] = $schedules->auditscheduleid;
            $DraftStatus['exists'] = 'Y';
        }

        // Redirect to the view and pass the data using compact
        return view('audit.auditdatefixing', compact('inst', 'Accountparticulars', 'rcno', 'auditperiod', 'annadhanamperiod', 'Quarter', 'DraftStatus'));
    }

    public static function getquarterdetails($deptcode)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception('Deptcode is not available');
            }

            $deptData = DB::table(self::$deptartment_table.' as dept')
                ->select(
                    'dept.nextquarter',
                    'dept.nextquarterfromdate',
                    'dept.nextquartertodate',
                    'dept.currentquarter',
                    'dept.currentquarterfromdate',
                    'dept.currentquartertodate',
                    'dept.autoplandate',
                )
                ->where('dept.deptcode', $deptcode)
                ->get();

            $autoplandate = $deptData[0]->autoplandate;

            $currentDate = Carbon::today();
            //  $currentDate = '2025-07-22';

            // Check if autoplandate is available and valid
            if (is_null($autoplandate)) {
                throw new \Exception('Date is null for department '.$deptcode);
            }

            $quarterDets = [];

            if (
                $currentDate > $deptData[0]->autoplandate &&
                $currentDate < $deptData[0]->nextquarterfromdate
            ) {
                $quarterDets = DB::table(self::$auditquarter_table.' as aq')
                    ->select('aq.auditquarter', 'aq.auditquartercode')
                    ->distinct()
                    ->where('deptcode', $deptcode)
                    ->whereIN('auditquartercode', [$deptData[0]->nextquarter, $deptData[0]->currentquarter])
                    ->orderBy('auditquartercode', 'desc')
                    ->get();
            } elseif (
                $currentDate > $deptData[0]->autoplandate &&
                $currentDate >= $deptData[0]->currentquarterfromdate
            ) {
                $quarterDets = DB::table(self::$auditquarter_table.' as aq')
                    ->select('aq.auditquarter', 'aq.auditquartercode')
                    ->distinct()
                    ->where('deptcode', $deptcode)
                    ->whereIN('auditquartercode', [$deptData[0]->currentquarter])
                    ->get();
            } elseif ($currentDate < $deptData[0]->autoplandate) {
                $quarterDets = DB::table(self::$auditquarter_table.' as aq')
                    ->select('aq.auditquarter', 'aq.auditquartercode')
                    ->distinct()
                    ->where('deptcode', $deptcode)
                    ->whereIN('auditquartercode', [$deptData[0]->currentquarter])
                    ->get();
            }

            return $quarterDets;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching . Please contact the administrator.';

            \Log::error('SQL Error: '.$e->getMessage());
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {
            // Optionally, you can log the error or handle it accordingly

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function fetch_auditplandetails($userid, $planmappingid)
    {
        $query = self::query()
            ->join(self::$institution_table.' as ai', 'ai.instid', '=', 'auditplan.instid')
            ->join(self::$auditplanmapping_table.' as mpl', 'mpl.planmappingid', '=', 'auditplan.planmappingid')
            ->join(self::$mst_financialyearcode_table.' as fin', 'mpl.financialyearcode', '=', 'fin.financialyearcode')
            ->join(self::$auditplanteam_table.' as at', 'at.auditplanteamid', '=', 'auditplan.auditteamid')
            ->join(self::$auditplanteammem_table.' as atm', 'atm.auditplanteamid', '=', 'auditplan.auditteamid')
            ->join(self::$typeofaudit_table.' as mst', 'mst.typeofauditcode', '=', 'auditplan.typeofauditcode')
            ->join(self::$userdetail_table.' as du', 'du.deptuserid', '=', 'atm.userid')
            ->join(self::$designation_table.' as desig', 'desig.desigcode', '=', 'du.desigcode')
            ->join(self::$deptartment_table.' as msd', 'msd.deptcode', '=', 'ai.deptcode')
            ->join(self::$mstauditeeinscategory_table.' as mac', 'mac.catcode', '=', 'ai.catcode')
            ->LeftJoin(self::$subcategory_table.' as sub', 'sub.auditeeins_subcategoryid', '=', 'ai.subcatid')
            ->leftJoin('audit.inst_auditschedule as ins', function ($join) {
                $join
                    ->on('ins.auditplanid', '=', 'auditplan.auditplanid')
                    ->whereIn('ins.statusflag', ['Y', 'F']);
            })
            ->select(
                DB::raw("(
                        SELECT m.planmappingid
                        FROM audit.auditplanmapping m
                        WHERE m.statusflag = 'Y'
                          AND m.deptcode = ai.deptcode
                        LIMIT 1
                    ) AS activeplan"),
                'auditplan.fromdate',
                'auditplan.todate',
                'auditplan.planmappingid',
                'auditplan.auditquartercode',
                'msd.freezeschedule',
                'mpl.autoplandate',
                'auditplan.datafromapi',
                DB::raw("mpl.planname || ' - ( ' || fin.financialyear || ' )' as planname"),
                'ai.instename',
                'ai.insttname',
                'ai.deptcode',
                'ai.instid',
                'ai.mandays',
                'ai.remainingmandays',
                'ai.spillover',
                'auditplan.auditteamid',
                'auditplan.auditplanid',
                'auditplan.datafromapi',
                'at.auditplanteamid',
                'at.teamname',
                'auditplan.auditmode',
                'mst.typeofauditename',
                'mst.typeofaudittname',
                'msd.deptesname',
                'msd.depttsname',
                'mac.catename',
                'mac.cattname',
                'du.deptuserid',
                'mpl.scheduleenddate',
                'auditplan.statusflag',
                'sub.subcatename',
                'sub.subcattname',
                'ins.statusflag as schedule_status',
                'ins.exitmeetdate',
                'ins.auditscheduleid',
                'auditplan.spilloverflag',
                // Count of team members who are NOT team heads
                DB::raw("(
                SELECT COUNT(*)
                FROM audit.auditplanteammember AS sub_atm
                WHERE sub_atm.auditplanteamid = auditplan.auditteamid
                   AND sub_atm.statusflag = 'Y'
                AND sub_atm.teamhead = 'N'
            ) AS team_member_count"),
                DB::raw("
                    CASE
                        WHEN
                            EXISTS (
                                SELECT 1
                                FROM audit.futureplanheadtransfer fpt
                                WHERE fpt.auditplanid = auditplan.auditplanid
                                  AND fpt.statusflag = 'Y'
                                  AND fpt.touserid = ?
                                  AND auditplan.planmappingid = ?
                            )
                            OR
                            EXISTS (
                                SELECT 1
                                FROM audit.logothertrans_scheduledel lsd
                                WHERE lsd.auditscheduleid = ins.auditscheduleid
                                  AND lsd.statusflag = 'Y'
                                  AND lsd.touserid = ?

                            )
                        THEN 'Y'
                        ELSE 'N'
                    END AS parallelteamhead
                "),
                // Aggregating all team members' names and designations
                DB::raw("(
                SELECT STRING_AGG(du2.username || ' - ' || desig2.desigelname, ', ')
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du2 ON du2.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig2 ON desig2.desigcode = du2.desigcode
                WHERE sub_atm.auditplanteamid = auditplan.auditteamid
                AND sub_atm.statusflag='Y'
                AND sub_atm.teamhead = 'N'
            ) AS team_members_en"),
                // Getting team head s name and designation separately
                DB::raw("(
                SELECT du3.username || ' - ' || desig3.desigelname
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du3 ON du3.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig3 ON desig3.desigcode = du3.desigcode
                WHERE sub_atm.auditplanteamid = auditplan.auditteamid
                AND sub_atm.teamhead = 'Y'
                AND sub_atm.statusflag='Y'
                LIMIT 1
            ) AS team_head_en"),
                DB::raw("(
                SELECT STRING_AGG(du2.usertamilname || ' - ' || desig2.desigtlname, ', ')
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du2 ON du2.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig2 ON desig2.desigcode = du2.desigcode
                WHERE sub_atm.auditplanteamid = auditplan.auditteamid
                 AND sub_atm.statusflag = 'Y'
                AND sub_atm.teamhead = 'N'
            ) AS team_members_ta"),
                // Getting team head s name and designation separately
                DB::raw("(
                SELECT du3.usertamilname || ' - ' || desig3.desigtlname
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du3 ON du3.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig3 ON desig3.desigcode = du3.desigcode
                WHERE sub_atm.auditplanteamid = auditplan.auditteamid
                    AND sub_atm.statusflag = 'Y'
                AND sub_atm.teamhead = 'Y'
                LIMIT 1
            ) AS team_head_ta"),
            )
            ->addBinding([
                $userid,
                $planmappingid,
                $userid,
            ], 'select')
            ->where('atm.userid', '=', $userid)
            ->where('auditplan.planmappingid', $planmappingid)
            // ->where('mpl.statusflag', 'Y')
            // ->whereColumn('mpl.deptcode', 'ai.deptcode')
            // ->where('auditplan.auditquartercode',  $quartercode)
            ->where('atm.statusflag', '=', 'Y')
            ->where('atm.teamhead', '=', 'Y')
            ->where('auditplan.statusflag', '=', 'F')
            // ->where('auditplan.prioritycode', '02')
            ->orderBy('auditplan.fromdate', 'asc')
            ->groupBy(
                'mpl.planname',
                'fin.financialyear',
                'msd.freezeschedule',
                'auditplan.auditquartercode',
                'mpl.autoplandate',
                'mpl.scheduleenddate',
                // 'msd.currentquarter',
                // 'msd.nextquarter',
                'ai.instename',
                'ai.insttname',
                'ai.deptcode',
                'ai.instid',
                'auditplan.mandays',
                'auditplan.planmappingid',
                'du.deptuserid',
                'auditplan.auditteamid',
                'auditplan.auditplanid',
                'at.auditplanteamid',
                'at.teamname',
                'mst.typeofauditename',
                'mst.typeofaudittname',
                'msd.deptesname',
                'msd.depttsname',
                'mac.catename',
                'mac.cattname',
                'auditplan.statusflag',
                'sub.subcatename',
                'sub.subcattname',
                'ins.statusflag',
                'ins.exitmeetdate',
                'ins.auditscheduleid',
                'auditplan.fromdate',
                'auditplan.todate',
            );

        return $query->get();

        // return $query;
    }

    public static function fetch_particularscheduleDetails($auditscheduleid)
    {
        try {
            if (empty($auditscheduleid)) {
                throw new \Exception('No Schedule details');
            }

            $query = DB::table(self::$instauditschedule_table.' as schd')
                ->join(self::$auditplan_table.' as plan', 'plan.auditplanid', '=', 'schd.auditplanid')
                ->join(self::$institution_table.' as inst', 'plan.instid', '=', 'inst.instid')
                ->join(self::$deptartment_table.' as dept', 'dept.deptcode', '=', 'inst.deptcode')
                ->select('inst.spillover', 'schd.entrymeetdate', 'schd.workallocationflag', 'plan.auditquartercode', 'dept.financialyear', 'plan.spilloverflag')
                ->where('schd.auditscheduleid', $auditscheduleid)
                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching . Please contact the administrator.';
            \Log::error('SQL Error: '.$e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getspilloverplandetails($instid, $planid)
    {
        try {
            $query = DB::table(self::$institution_table.' as inst')
                ->join(self::$auditplan_table.' as plan', 'plan.instid', '=', 'inst.instid')
                ->join(self::$auditplanteammem_table.' as team', 'plan.auditteamid', '=', 'team.auditplanteamid')
                ->join(self::$userdetail_table.' as uc', 'uc.deptuserid', '=', 'team.userid')
                ->join(self::$designation_table.' as desig', 'desig.desigcode', '=', 'uc.desigcode')
                ->join(self::$auditquarter_table.' as q', 'q.auditquartercode', '=', 'plan.auditquartercode')
                ->select(
                    DB::raw("CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM audit.inst_auditschedule schd
\t\t\tWHERE schd.auditplanid = plan.auditplanid AND schd.statusflag='F'
                    ) THEN 'Y' ELSE 'N'
                 END AS scheduledflag"),
                    'plan.todate',
                    'plan.auditquartercode',
                    'plan.fromdate',
                    'plan.todate',
                    'inst.instename',
                    'inst.insttname',
                    'inst.instid',
                    'plan.auditplanid',
                    'q.auditquarter',
                    DB::raw("TO_CHAR(plan.fromdate, 'DD-MM-YYYY') AS fromdate"),
                    DB::raw("TO_CHAR(plan.todate, 'DD-MM-YYYY') AS todate"),
                    DB::raw("(
                SELECT COUNT(*)
                FROM audit.auditplanteammember AS sub_atm
                WHERE sub_atm.auditplanteamid = plan.auditteamid
                   AND sub_atm.statusflag = 'Y'
                  ) AS team_member_count"),
                    // Aggregating all team members' names and designations
                    DB::raw("(
                SELECT STRING_AGG(du2.username || ' - ' || desig2.desigelname, ', ')
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du2 ON du2.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig2 ON desig2.desigcode = du2.desigcode
                WHERE sub_atm.auditplanteamid = plan.auditteamid
                AND sub_atm.statusflag='Y'
                AND sub_atm.teamhead = 'N'
            ) AS team_members_en"),
                    // Getting team head s name and designation separately
                    DB::raw("(
                SELECT du3.username || ' - ' || desig3.desigelname
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du3 ON du3.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig3 ON desig3.desigcode = du3.desigcode
                WHERE sub_atm.auditplanteamid = plan.auditteamid
                AND sub_atm.teamhead = 'Y'
                AND sub_atm.statusflag='Y'
                LIMIT 1
            ) AS team_head_en"),
                    DB::raw("(
                SELECT STRING_AGG(du2.usertamilname || ' - ' || desig2.desigtlname, ', ')
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du2 ON du2.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig2 ON desig2.desigcode = du2.desigcode
                WHERE sub_atm.auditplanteamid = plan.auditteamid
                 AND sub_atm.statusflag = 'Y'
                AND sub_atm.teamhead = 'N'
            ) AS team_members_ta"),
                    // Getting team head s name and designation separately
                    DB::raw("(
                SELECT du3.usertamilname || ' - ' || desig3.desigtlname
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du3 ON du3.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig3 ON desig3.desigcode = du3.desigcode
                WHERE sub_atm.auditplanteamid = plan.auditteamid
                    AND sub_atm.statusflag = 'Y'
                AND sub_atm.teamhead = 'Y'
                LIMIT 1
            ) AS team_head_ta"),
                )
                ->where('inst.instid', $instid)
                ->where('plan.auditplanid', $planid)
                ->where('plan.statusflag', 'F')
                ->groupBy(
                    'plan.mandays',
                    'plan.auditquartercode',
                    'plan.fromdate',
                    'plan.todate',
                    'inst.instename',
                    'inst.insttname',
                    'inst.instid',
                    'plan.auditplanid',
                    'q.auditquarter',
                    'team.auditplanteamid',
                    'plan.auditteamid',
                    'desig.desigelname',
                    'desig.desigtlname'
                )
                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching . Please contact the administrator.';

            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function chargetakingover($instid, $userid)
    {
        DB::beginTransaction();

        try {
            if (empty($instid)) {
                throw new \Exception('Institution Details not found');
            }
            if (empty($userid)) {
                throw new \Exception('Session Details not found');
            }

            $result = DB::select('SELECT audit.chargetakingover(?, ?) AS response', [$instid, $userid]);

            DB::commit();

            return $result;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $customMessage = 'A database error occurred while fetching. Please contact the administrator.';
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            DB::rollBack();

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function ForInstituionlDeptfetch()
    {
        return DB::table(self::$deptartment_table.' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname')  // Select required columns
            ->where('dept.statusflag', '=', 'Y')  // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }

    public static function getinstitutionBydistrictalocation($district, $regioncode, $deptcode)
    {
        $table = 'audit.auditplan';

        return DB::table($table.' as plan')
            ->join('audit.mst_institution'.' as ins', 'ins.instid', '=', 'plan.instid')
            ->join('audit.mst_dept as dept', 'ins.deptcode', '=', 'dept.deptcode')
            ->select('ins.instename', 'ins.instid', 'ins.insttname', 'dept.currentquarter')
            ->distinct()
            ->where('ins.distcode', $district)
            ->where('ins.deptcode', $deptcode)
            ->where('ins.regioncode', $regioncode)
            ->whereNotIn('auditplanid', function ($query) {
                $query
                    ->select('auditplanid')
                    ->from('audit.inst_auditschedule');
            })
            ->whereColumn('plan.auditquartercode', 'dept.currentquarter')
            ->where('plan.statusflag', 'F')
            ->get();
    }

    public static function getteamBasedOninst($instmappingcode)
    {
        $table = 'audit.auditplan';

        return DB::table($table.' as plan')
            ->join('audit.mst_institution'.' as ins', 'ins.instid', '=', 'plan.instid')
            ->join('audit.auditplanteammember as aup', 'aup.auditplanteamid', '=', 'plan.auditteamid')
            ->join('audit.deptuserdetails as user', 'aup.userid', '=', 'user.deptuserid')
            ->join('audit.mst_dept as dept', 'ins.deptcode', '=', 'dept.deptcode')
            ->join('audit.mst_designation as desig', 'user.desigcode', '=', 'desig.desigcode')
            ->select('user.username', 'user.usertamilname', 'aup.teamhead', 'desig.desigesname', 'user.deptuserid', 'plan.auditplanid', 'dept.currentquarter')
            ->distinct()
            ->where('ins.instid', $instmappingcode)
            ->whereColumn('plan.auditquartercode', 'dept.currentquarter')
            ->where('aup.statusflag', 'Y')
            ->where('ins.statusflag', 'Y')
            ->where('plan.statusflag', 'F')
            ->where('desig.statusflag', 'Y')
            ->get();
    }

    public static function institutionallocation_fetchData($table = null)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;
        $table = 'audit.auditplan';

        $query = DB::table($table.' as plan')
            ->join('audit.mst_institution as ins', 'ins.instid', '=', 'plan.instid')
            ->join('audit.auditplanteammember as aup', 'aup.auditplanteamid', '=', 'plan.auditteamid')
            ->join('audit.deptuserdetails as deptuser', 'aup.userid', '=', 'deptuser.deptuserid')
            ->join('audit.mst_dept as dept', 'ins.deptcode', '=', 'dept.deptcode')
            ->join('audit.mst_region as reg', 'ins.regioncode', '=', 'reg.regioncode')
            ->join('audit.mst_district as dist', 'ins.distcode', '=', 'dist.distcode')
            ->join('audit.mst_designation as desig', 'deptuser.desigcode', '=', 'desig.desigcode')
            ->select(
                'plan.auditquartercode',
                'ins.instename',
                'ins.insttname',
                'dist.distename',
                'dept.deptesname',
                'reg.regionename',
                'reg.regiontname',
                'ins.updatedon',
                'dept.currentquarter',
                DB::raw("
            string_agg(
                CASE
                    WHEN aup.teamhead = 'Y'
                        THEN deptuser.username || ' (' || desig.desigesname || ')'
                END,
                E'
'
            ) as team_heads
        "),
                DB::raw("
            string_agg(
                CASE
                    WHEN aup.teamhead = 'N'
                        THEN deptuser.username || ' (' || desig.desigesname || ')'
                END,
                E'
'
            ) as team_members
        ")
            )
            ->whereColumn('plan.auditquartercode', 'dept.currentquarter')
            ->where('aup.statusflag', '=', 'Y')
            ->where('ins.statusflag', '=', 'Y')
            ->where('plan.statusflag', '=', 'F')
            ->where('desig.statusflag', '=', 'Y')
            ->groupBy(
                'plan.auditquartercode',
                'ins.instename',
                'ins.insttname',
                'dist.distename',
                'dept.deptesname',
                'reg.regionename',
                'reg.regiontname',
                'ins.updatedon',
                'dept.currentquarter'
            )
            ->orderBy('dept.deptesname', 'asc');

        // dd($query->toSql());
        return $query->get();
    }

    public static function institutionallocation_insertupdate($data)
    {
        try {
            $auditPlanId = $data['auditplanid'];
            $auditQuarter = $data['currentquarter'];

            $futurePlanStatus = DB::table('audit.auditplanteammember as aptm')
                ->join('audit.auditplan as ap', 'ap.auditteamid', '=', 'aptm.auditplanteamid')
                ->select(
                    'aptm.userid',
                    DB::raw("
                    CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM audit.auditplan plan
                            JOIN audit.auditplanteammember aptm_inner
                                ON plan.auditteamid = aptm_inner.auditplanteamid
                            LEFT JOIN audit.inst_auditschedule sch
                                ON plan.auditplanid = sch.auditplanid
                            WHERE plan.statusflag = 'F'
                              AND sch.auditscheduleid IS NULL
                              AND aptm_inner.userid = aptm.userid
                              AND plan.auditplanid <> $auditPlanId
                              AND plan.auditquartercode = '$auditQuarter'
                        ) THEN 'Y'
                        ELSE 'N'
                    END as future_plan_status
                "),
                    DB::raw("
                    CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM audit.inst_auditschedule inst
                            JOIN audit.inst_schteammember inschm
                                ON inschm.auditscheduleid = inst.auditscheduleid
                            WHERE inst.entrymeetdate IS NOT NULL
                              AND inst.exitmeetdate IS NULL
                              AND inschm.userid = aptm.userid
                        ) THEN 'Y'
                        ELSE 'N'
                    END as current_schedule_status
                ")
                )
                ->where('ap.auditplanid', $auditPlanId)
                ->groupBy('aptm.userid')
                ->get();

            if ($futurePlanStatus->contains(function ($row) {
                return $row->future_plan_status === 'Y' || $row->current_schedule_status === 'Y';
            })) {
                throw new \Exception('futureplanexistts');
            }

            $affectedRows = DB::table('audit.auditplan')
                ->where('instid', $data['instmappingcode'])
                ->where('auditquartercode', $data['currentquarter'])
                ->update([
                    'statusflag' => $data['statusflag'],
                    'createdon' => $data['createdon'],
                    'createdby' => $data['createdby'],
                    'updatedon' => $data['updatedon'],
                    'updatedby' => $data['updatedby'],
                ]);

            // if ($affectedRows === 0) {
            //     throw new \Exception('No records were updated.');
            // }

            if (($data['listresponse'] ?? null) === 'Y' && ! empty($futurePlanStatus)) {
                $userIds = $futurePlanStatus->pluck('userid')->toArray();
                if (! empty($userIds)) {
                    $affectedRows = DB::table('audit.deptuserdetails')
                        ->whereIn('deptuserid', $userIds)
                        ->update([
                            'reservelist' => 'N',
                            'createdon' => $data['createdon'],
                            'createdby' => $data['createdby'],
                            'updatedon' => $data['updatedon'],
                            'updatedby' => $data['updatedby'],
                        ]);
                }
            }

            return $affectedRows;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function fetchexitmeetdate($spillous, $deptcode = null)
    {
        $instids = collect($spillous['spilloverData'] ?? [])
            ->pluck('instid')
            ->filter()
            ->unique()
            ->toArray();

        if (empty($instids)) {
            return collect();
        }

        $sessionchargedel = session('charge');

        $deptcode = $deptcode ?: ($sessionchargedel->deptcode ?? null);
        $planContext = self::getInstChangePlanContext($deptcode);
        $currentPlan = $planContext['current'] ?? null;
        $currentPlanMappingId = $currentPlan['planmappingid'] ?? null;

        $result = DB::table('audit.inst_auditschedule as ia')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ia.auditplanid')
            ->select(
                'ap.instid',
                'ap.auditquartercode',
                'ia.exitmeetdate',
                'ia.proposedexitmeetdate'
            )
            ->whereIn('ap.instid', $instids)
            ->when($currentPlanMappingId, function ($query) use ($currentPlanMappingId) {
                $query->where('ap.planmappingid', $currentPlanMappingId);
            })
            ->get();

        return $result;
    }
    // public static function fetchexitmeetdate($spillous)
    // {
    //     $instids = collect($spillous['spilloverData'] ?? [])
    //         ->pluck('instid')
    //         ->filter()
    //         ->unique()
    //         ->toArray();

    //     if (empty($instids)) {
    //         return collect();
    //     }

    //     $sessionchargedel = session('charge');

    //     $deptcode = $sessionchargedel->deptcode ?? null;

    //     $currecntQuarterdel = DB::table('audit.mst_dept')
    //         ->where('deptcode', $deptcode)
    //         ->select('currentquarter', 'nextquarter')
    //         ->get();
    //     $currecntQuarterFromDept = $currecntQuarterdel[0]->currentquarter;

    //     $result = DB::table('audit.inst_auditschedule as ia')
    //         ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ia.auditplanid')
    //         ->select(
    //             'ap.instid',
    //             'ap.auditquartercode',
    //             'ia.exitmeetdate',
    //             'ia.proposedexitmeetdate'
    //         )
    //         ->whereIn('ap.instid', $instids)
    //         ->where('ap.auditquartercode', $currecntQuarterFromDept)
    //         ->get();

    //     return $result;
    // }

    // public static function getallocdet_temp($deptcode, $distcode, $quarter)
    // {
    //     $column = 'inst.' . $quarter;

    //     $prioritydetails = DB::table(self::$deptartment_table . ' as dept')
    //         ->where('dept.deptcode', $deptcode)
    //         ->select('dept.supplementaryplan', 'dept.inst_priority', 'autoplandate', 'nextinst_priority')
    //         ->get();

    //     $autoplandate = $prioritydetails[0]->autoplandate;

    //     $currentDate = Carbon::today();

    //     // Check if autoplandate is available and valid
    //     if (is_null($autoplandate)) {
    //         throw new \Exception('Autoplandate is null for department ' . $deptcode);
    //     }

    //     if ($currentDate->gte($autoplandate)) {
    //         $institution_priority = $prioritydetails[0]->nextinst_priority;
    //     } else {
    //         $institution_priority = $prioritydetails[0]->inst_priority;
    //     }

    //     $supplementaryflag = $prioritydetails[0]->supplementaryplan;
    //     // $query = DB::table('audit.templateaudit_teamassignments as ta')
    //     //     ->join('mst_institution as inst', function ($join) {
    //     //         $join->whereRaw('inst.instid = ANY(ta.instid)');
    //     //     })
    //     //     ->join(self::$mstauditeeinscategory_table . ' as mac', 'mac.catcode', '=', 'inst.catcode')
    //     //     ->where('inst.deptcode', $deptcode)
    //     //     ->where('inst.distcode', $distcode)
    //     //     ->select('ta.userid', 'inst.instename', 'inst.insttname', 'mac.catename', 'mac.cattname')
    //     //     ->get();
    //     $query = DB::table('mst_institution as inst')
    //         ->select(
    //             'inst.instename',
    //             'inst.insttname',
    //             'mac.catename',
    //             'mac.cattname'
    //         )
    //         // ->leftJoin('mst_nonaudit_hub as ho', function ($join) {
    //         //     $join
    //         //         ->on('ho.deptcode', '=', 'inst.deptcode')
    //         //         ->on('ho.distcode', '=', 'inst.distcode')
    //         //         ->on('ho.desigcode', '=', 'inst.hubdesigcode');
    //         // })
    //         ->join(self::$mstauditeeinscategory_table . ' as mac', 'mac.catcode', '=', 'inst.catcode')
    //         ->where('inst.distcode', $distcode)
    //         ->where('inst.deptcode', $deptcode)
    //         ->where('inst.auditmode', 'T')
    //         ->where($column, 'Y');
    //     if ($supplementaryflag == 'Y') {
    //         $query->where('inst.inst_priority_kms', $institution_priority);
    //     }
    //     // ->where('Q4', 'Y')  // make sure Q3 is really a column name
    //     //         ->orderByRaw("
    //     //     CASE
    //     //         WHEN inst.hubtype = 'A' THEN inst.parentid
    //     //         WHEN inst.hubtype = 'I' AND inst.deptcode = '01' THEN ho.circleid
    //     //         ELSE ho.desigcode::int
    //     //     END
    //     // ")
    //     // ->orderBy('inst.instename')
    //     // ->get();
    //     $query->orderBy('inst.instename');
    //     return $query->get();
    // }

    public static function getallocdet_temp($deptcode, $distcode, $quarter, $plan_config_details)
    {
        $column = 'inst.'.$quarter;

        $institution_priority = $plan_config_details->prioritycode;

        $query = DB::table('audit.mst_institution as inst')
            ->select(
                'inst.instename',
                'inst.insttname',
                'mac.catename',
                'mac.cattname'
            )
            ->join(self::$mstauditeeinscategory_table.' as mac', 'mac.catcode', '=', 'inst.catcode')
            ->where('inst.distcode', $distcode)
            ->where('inst.deptcode', $deptcode)
            ->where('inst.auditmode', 'T')
            ->where($column, 'Y');
        if ($institution_priority === null) {
            $query->where(function ($q) {
                $q->whereNull('inst.inst_priority_kms')
                    ->orWhere('inst.inst_priority_kms', '03');
            });
        } else {
            $query->where('inst.inst_priority_kms', $institution_priority);
        }

        $query->orderBy('inst.instename');

        return $query->get();
    }

    public static function get_userbasedtemp($deptcode, $distcode, $userid)
    {
        $query = DB::table('audit.templateaudit_teamassignments  '.' as ta')
            ->join('audit.mst_institution as inst', function ($join) {
                $join->whereRaw('inst.instid = ANY(ta.instid)');
            })
            ->join(self::$mstauditeeinscategory_table.' as mac', 'mac.catcode', '=', 'inst.catcode')
            ->where('inst.deptcode', $deptcode)
            ->where('inst.distcode', $distcode)
            ->where('ta.userid', $userid)
            ->select('inst.instename', 'inst.insttname', 'mac.catename', 'mac.cattname')
            ->get();

        return $query;
    }

    // public static function getTemplateplan($deptcode, $distcode, $quartercode)
    // {
    //     try {
    //         if (empty($deptcode)) {
    //             throw new Exception('Deptcode is not available');
    //         }
    //         if (empty($distcode)) {
    //             throw new Exception('distcode is not available');
    //         }
    //         $query = DB::table('audit.templateauditplan' . ' as tp')
    //             ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'tp.instid')
    //             ->join(self::$mstauditeeinscategory_table . ' as cat', 'inst.catcode', '=', 'cat.catcode')
    //             ->join(self::$deptartment_table . ' as dept', 'dept.deptcode', '=', 'inst.deptcode')
    //             ->join(self::$dist_table . ' as dist', 'dist.distcode', '=', 'inst.distcode')
    //             ->join(self::$userdetail_table . ' as du', 'tp.deptuserid', '=', 'du.deptuserid')
    //             ->join(self::$designation_table . ' as dd', 'dd.desigcode', '=', 'du.desigcode')
    //             ->select('inst.instename', 'inst.insttname', 'cat.catename', 'cat.cattname', 'du.username', 'du.usertamilname', 'dd.desigelname', 'dd.desigtlname')
    //             ->where('inst.deptcode', $deptcode)
    //             ->where('inst.distcode', $distcode)
    //             ->where('tp.prioritycode', '02')
    //             ->where('tp.auditquartercode', $quartercode)
    //             ->get();

    //         return $query;
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

    //         throw new \Exception($e->getMessage(), 500);
    //     } catch (\Exception $e) {
    //         throw new \Exception($e->getMessage(), 409);
    //     }
    // }

    public static function getTemplateplan($deptcode, $distcode, $quartercode, $prioritycode)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception('Deptcode is not available');
            }
            if (empty($distcode)) {
                throw new Exception('distcode is not available');
            }
            $query = DB::table('audit.templateauditplan'.' as tp')
                ->join(self::$institution_table.' as inst', 'inst.instid', '=', 'tp.instid')
                ->join(self::$mstauditeeinscategory_table.' as cat', 'inst.catcode', '=', 'cat.catcode')
                ->join(self::$deptartment_table.' as dept', 'dept.deptcode', '=', 'inst.deptcode')
                ->join(self::$dist_table.' as dist', 'dist.distcode', '=', 'inst.distcode')
                ->join(self::$userdetail_table.' as du', 'tp.deptuserid', '=', 'du.deptuserid')
                ->join(self::$designation_table.' as dd', 'dd.desigcode', '=', 'du.desigcode')
                ->select(
                    'inst.instename',
                    'inst.insttname',
                    'cat.catename',
                    'cat.cattname',
                    'du.username',
                    'du.usertamilname',
                    'dd.desigelname',
                    'dd.desigtlname'
                )
                ->where('inst.deptcode', $deptcode)
                ->where('inst.distcode', $distcode)
                ->where('tp.prioritycode', $prioritycode)
                ->where('tp.auditquartercode', $quartercode)
                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function Auditannathanamfetch($deptcode, $instmappingcode, $auditquartercode)
    {
        $auditPlanSub = DB::table('audit.auditplan')
            ->select('auditplanid')
            ->where('instid', $instmappingcode)
            ->where('planmappingid', $auditquartercode)
            ->where('statusflag', 'F');

        return DB::table('audit.mst_auditperiod as ap')
            ->leftJoin('audit.yearcode_mapping as ycm', function ($join) use ($auditPlanSub) {
                $join->on('ycm.yearselected', '=', 'ap.auditperiodid')
                    ->whereIn('ycm.auditplanid', $auditPlanSub)
                    ->where('ycm.statusflag', 'Y')
                    ->where('ycm.financestatus', 'Y');
            })
            ->select(
                'ycm.yearselected',
                'ap.auditperiodid',
                DB::raw("CONCAT(ap.fromyear, ' - ', ap.toyear) AS audit_period"),
                DB::raw('CASE WHEN ycm.yearselected IS NULL THEN 0 ELSE 1 END AS is_selected')
            )
            ->where('ap.deptcode', $deptcode)
            ->where('ap.financestatus', 'Y')
            ->where('ap.statusflag', 'Y')
            ->whereIn('ap.lagacyyear', ['N', 'B'])
            ->orderBy('ap.fromyear', 'DESC')
            ->groupBy('ap.auditperiodid', 'ap.fromyear', 'ap.toyear', 'ycm.yearselected')
            ->get();
    }

    public static function getreadyforauditregions($deptcode)
    {
        try {
            return DB::table('audit.temp_readyforaudit as tra')
                ->join('audit.mst_institution as inst', 'inst.instid', '=', 'tra.instid')
                ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'inst.regioncode')
                ->where('inst.deptcode', $deptcode)
                ->where('inst.statusflag', 'Y')
                ->where('tra.statusflag', 'S')
                ->select(
                    'reg.regioncode',
                    'reg.regionename',
                    'reg.regiontname'
                )
                ->distinct()
                ->orderBy('reg.regionename')
                ->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function getreadyforauditdistricts($deptcode, $regioncode = null)
    {
        try {
            return DB::table('audit.temp_readyforaudit as tra')
                ->join('audit.mst_institution as inst', 'inst.instid', '=', 'tra.instid')
                ->join('audit.mst_district as dist', 'dist.distcode', '=', 'inst.distcode')
                ->where('inst.deptcode', $deptcode)
                ->when($regioncode, function ($query) use ($regioncode) {
                    $query->where('inst.regioncode', $regioncode);
                })
                ->where('inst.statusflag', 'Y')
                ->where('tra.statusflag', 'S')
                ->select(
                    'dist.distcode',
                    'dist.distename',
                    'dist.disttname'
                )
                ->distinct()
                ->orderBy('dist.distename')
                ->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function getreadyforauditinstitutions($deptcode, $regioncode = null, $distcode = null)
    {
        try {
            if (empty($deptcode)) {
                return collect();
            }

            if (empty($regioncode) || empty($distcode)) {
                return collect();
            }

            $institutions = DB::table('audit.temp_readyforaudit as a')
                ->join('audit.mst_institution as i', function ($join) {
                    $join->on('i.instid', '=', 'a.instid')
                        ->where('i.statusflag', 'Y');
                })
                ->join('audit.mst_financialyear as fy', 'fy.financialyearcode', '=', 'a.financialyearcode')
                ->where('a.statusflag', 'S')
                ->where('i.deptcode', $deptcode)
                ->where('i.regioncode', $regioncode)
                ->where('i.distcode', $distcode)
                ->where(function ($query) {
                    $query
                        ->where('a.finaliseflag', 'Y')
                        ->orWhereNull('a.finaliseflag')
                        ->orWhere('a.finaliseflag', 'N');
                })
                ->select(
                    'a.instid',
                    'a.readyforauditid',
                    'i.instename',
                    'a.financialyearcode',
                    'a.audityear as financialyear',
                    'a.finaliseflag',
                    'i.teamsize',
                    'i.mandays'
                )
                ->orderBy('a.readyforauditid')
                ->get();

            return $institutions;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function checkreadyforauditfinalised($deptcode, $distcode)
    {
        try {
            $finalise = DB::table('audit.auditor_instmapping')
                ->where('deptcode', $deptcode)
                ->where('distcode', $distcode)
                ->where('readyforauditfinalise', 'F')
                ->exists();

            return $finalise;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function automateschedule($planid, $instid, $userid)
    {
        try {

            DB::beginTransaction();

            $result = DB::selectOne(
                'SELECT audit.automateschedule(?, ?, ?) AS response',
                [$instid, $planid, $userid]
            );
            $response = json_decode($result->response, true);

            if ($response['status'] !== 'success') {
                DB::rollBack();

                return $response;
            }

            DB::commit();

            return json_decode($result->response, true);
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    public static function updatereadyforaudit($instid, $deptcode, $distcode, $regioncode = null)
    {
        try {
            DB::beginTransaction();

            if (empty($distcode)) {
                throw new \Exception('District is required.');
            }

            // Check if already finalised
            $finalise = self::checkreadyforauditfinalised($deptcode, $distcode);

            if ($finalise) {
                DB::rollBack();

                return [
                    'status' => false,
                    'message' => 'Already finalised. Update not allowed.',
                ];
            }

            $scopedReadyForAuditIds = DB::table('audit.temp_readyforaudit as tra')
                ->join('audit.mst_institution as inst', 'inst.instid', '=', 'tra.instid')
                ->where('inst.deptcode', $deptcode)
                ->where('inst.distcode', $distcode)
                ->when($regioncode, function ($query) use ($regioncode) {
                    $query->where('inst.regioncode', $regioncode);
                })
                ->where('inst.statusflag', 'Y')
                ->where('tra.statusflag', 'S')
                ->pluck('tra.readyforauditid')
                ->toArray();

            $resetCount = 0;

            if (! empty($scopedReadyForAuditIds)) {
                $resetCount = DB::table('audit.temp_readyforaudit')
                    ->whereIn('readyforauditid', $scopedReadyForAuditIds)
                    ->where('statusflag', 'S')
                    ->update([
                        'finaliseflag' => 'N',
                    ]);
            }

            $selectedIds = array_values(array_intersect($instid, $scopedReadyForAuditIds));
            $updateCount = 0;

            if (! empty($selectedIds)) {
                $updateCount = DB::table('audit.temp_readyforaudit')
                    ->whereIn('readyforauditid', $selectedIds)
                    ->where('statusflag', 'S')
                    ->update([
                        'finaliseflag' => 'Y',
                    ]);
            }

            DB::commit();

            return [
                'status' => true,
                'updated' => ($resetCount > 0 || $updateCount > 0),
                'data' => 'success',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
