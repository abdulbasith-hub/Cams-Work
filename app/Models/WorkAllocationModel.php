<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Models\BaseModel;

class MajorWorkAllocationModel extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon'; // Custom column name for `updated_at` (if you have it)

    // Specify that the primary key is `userid` instead of `id`
    protected $primaryKey = 'majorworkallocationtypeid';

    // Specify the table name
    protected $table = 'audit.mst_majorworkallocationtype';

    // Set the primary key type if it's not an auto-incrementing integer
    protected $keyType = 'int';  // If `userid` is an integer

    // If your primary key is not auto-incrementing, set `incrementing` to false
    public $incrementing = true; // Set to `false` if `userid` is not auto-incrementing

}

class WorkAllocationModel extends Model
{
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
    protected static $transaccountdetails_table = BaseModel::TRANSACCOUNTDETAILS_TABLE;

    protected static $mapallocationobjection_table = BaseModel::MAPALLOCATIONOBJECTION_TABLE;
    protected static $majorworkallocation_table = BaseModel::MAJORWORKALLOCATION_TABLE;
    protected static $transworkallc_table = BaseModel::TRANSWORKALLOCATION_TABLE;
    protected static $workallocation_function = BaseModel::WORKALLOCATION_FUNCTION;
    protected static $group_table = BaseModel::GROUP_TABLE;



    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon'; // Custom column name for `updated_at` (if you have it)

    // Specify that the primary key is `userid` instead of `id`
    protected $primaryKey = 'workallocationtypeid';

    // Specify the table name
    protected $table = 'audit.map_workallocation';

    // Set the primary key type if it's not an auto-incrementing integer
    protected $keyType = 'int';  // If `userid` is an integer

    // If your primary key is not auto-incrementing, set `incrementing` to false
    public $incrementing = true; // Set to `false` if `userid` is not auto-incrementing

    // Define the fillable fields
    protected $fillable = [
        'majorworkallocationtypeid',
        'minorworkallocationtypeid',
        'catcode',
        'teamhead',
        'statusflag'
    ];


    /**
     * Create a new user if it doesn't already exist based on email, phone, name, and address.
     * Otherwise, update the user if it already exists, based on email, phone, and name (excluding current id).
     *
     * @param array $data
     * @param int|null $currentUserId (optional: pass the current user's id for updates)
     * @return User|false
     */
    public static function createIfNotExistsOrUpdate(array $data, $workallocid)
    {
        if ($workallocid) {
            $existingUser = self::query()
                ->where('statusflag', 'Y')
                ->where('workallocationtypeid', '!=', $workallocid)
                ->where('majorworkallocationtypeid', $data['majorworkallocationtypeid'])
                ->where('minorworkallocationtypeid', $data['minorworkallocationtypeid'])
                ->where('catcode', $data['catcode'])
                ->first();

            if ($existingUser) {
                return false;
            }

            // If no such user exists, update the existing record with the provided data
            $existingUser = self::find($workallocid);
            $existingUser->update($data);
        } else {

            $existingUser = self::query()
                ->where('statusflag', 'Y')
                ->where('majorworkallocationtypeid', $data['majorworkallocationtypeid'])
                ->where('minorworkallocationtypeid', $data['minorworkallocationtypeid'])
                ->where('catcode', $data['catcode'])
                ->first();

            if ($existingUser) {
                return false;
            } else {
                $existingUser = self::create($data);
            }
        }

        return $existingUser;
    }

    public static function fetchinstdet($data)
    {
        return DB::table(self::$institution_table . ' as inst')
            ->join(self::$auditplan_table . ' as plan', 'inst.instid', '=', 'plan.instid')
            ->join(self::$deptartment_table . ' as dept', 'inst.deptcode', '=', 'dept.deptcode')
            ->join(self::$instauditschedule_table . ' as schd', 'schd.auditplanid', '=', 'plan.auditplanid')
            ->join(self::$instauditschedulemem_table . ' as schdmem', 'schdmem.auditscheduleid', '=', 'schd.auditscheduleid')
            ->where('schd.statusflag', 'F')
            ->where('schdmem.userid', $data['userid'])
            ->where('schdmem.statusflag', 'Y')
            ->where('inst.distcode', $data['distcode'])
            ->where('inst.deptcode', $data['deptcode'])
            ->whereColumn('plan.auditquartercode', 'dept.currentquarter')
            ->where('inst.statusflag', 'Y')
            ->select('inst.instid', 'inst.instename', 'inst.insttname', 'schdmem.auditscheduleid')
            ->orderBy('instename', 'asc')
            ->get();
    }

    public static function fetchworkdata($data)
    {
        $schedule = DB::table(self::$instauditschedule_table)
            ->where('auditscheduleid', $data['scheduleid'])
            ->first();

        if (!$schedule || $schedule->workallocationflag !== 'Y') {
            return ['error' => 'Work Allocation was not Randomized'];
        }

        $query = DB::table(self::$transworkallc_table . ' as trans')
            ->join('audit.inst_schteammember as inm', 'inm.schteammemberid', '=', 'trans.schteammemberid')
            ->join('audit.inst_auditschedule as asch', 'asch.auditscheduleid', '=', 'trans.auditscheduleid')
            //->join('audit.map_workallocation as mapw', 'mapw.workallocationtypeid', '=', 'trans_workallocation.workallocationtypeid')
            ->join('audit.map_allocation_objection as mao', 'mao.mapallocationobjectionid', '=', 'trans.workallocationtypeid')
            ->join('audit.group as grp', 'grp.groupid', '=', 'mao.groupid')

            //  ->join('audit.mst_subworkallocationtype as msw', 'msw.subworkallocationtypeid', '=', 'mapw.minorworkallocationtypeid')
            ->join('audit.mst_majorworkallocationtype as mmw', 'mmw.majorworkallocationtypeid', '=', 'mao.majorworkallocationtypeid')
            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'inm.userid')
            ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
            ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
            ->select(
		'du.usertamilname',
                'de.desigelname',
                'de.desigtlname',
                'du.username',
                'trans.schteammemberid',
                // DB::raw('STRING_AGG(msw.subworkallocationtypeename, \', \') as subtypecodes'), // Concatenate all subtypecodes
                'trans.statusflag',
                // 'asch.fromdate',
                // 'asch.todate',
                'mmw.majorworkallocationtypeename',
                'mmw.majorworkallocationtypetname',
                'mmw.majorworkallocationtypeid',
                // 'asch.entrymeetdate',
                // 'asch.exitmeetdate',
                'groupename',
                'grouptname'
            )
            ->where('trans.auditscheduleid', '=', $data['scheduleid'])
            ->where('inm.userid', '=', $data['userid'])
            ->where('asch.workallocationflag', 'Y')
            ->where('mao.statusflag', 'Y')
            //  ->distinct()
            ->orderby('mmw.majorworkallocationtypeename')
            ->get();


        if ($query->isEmpty()) {

            return response()->json([
                'error' => 'No work was allocated'
            ], 404);
        } else {
            // Return the found records.
            return $query;
        }
    }

    public static function Majorworktypes()
    {
        $MajorWorkAllocation = MajorWorkAllocationModel::where('statusflag', 'Y')
            ->get();
        return $MajorWorkAllocation;
    }

    public static function fetchAllusers()
    {
        $AllData = DB::table('audit.map_workallocation as wa')
            ->join('audit.mst_majorworkallocationtype as major', 'wa.majorworkallocationtypeid', '=', 'major.majorworkallocationtypeid')
            ->leftJoin('audit.mst_subworkallocationtype as minor', function ($join) {
                $join->on('wa.minorworkallocationtypeid', '=', 'minor.subworkallocationtypeid')
                    ->whereNotNull('wa.minorworkallocationtypeid'); // Only join if minorworkallocationtypeid has a value
            })
            ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'major.deptcode')
            //->join('audit.mst_auditeeins_category as cat', 'wa.catcode', '=', 'cat.catcode')
            ->select(
                'wa.workallocationtypeid',
                'wa.teamhead',
                'wa.statusflag',
                'dept.deptelname',
                //'cat.catename',
                'major.majorworkallocationtypeename',
                DB::raw("COALESCE(minor.subworkallocationtypeename, 'NA') as subworkallocationtypeename") // Corrected single quotes
            )
            ->where('wa.statusflag', 'Y')
            ->get();



        // Log the result for debugging (better than using print_r)
        \Log::info('Fetched Work Allocation Records:', $AllData->toArray()); // Logs as array for better readability

        // Return the data (can be used in a controller to return the response)
        return $AllData;  // Eloquent collection
    }


    public static function fetchCurrentUser($workid)
    {
        //echo 'fetchcuser';
        $WorkData = DB::table('audit.map_workallocation as wa')
            ->join('audit.mst_majorworkallocationtype as major', 'wa.majorworkallocationtypeid', '=', 'major.majorworkallocationtypeid')
            ->leftJoin('audit.mst_subworkallocationtype as minor', 'wa.minorworkallocationtypeid', '=', 'minor.subworkallocationtypeid')
            ->select(
                'wa.workallocationtypeid',
                'wa.teamhead',
                'wa.statusflag',
                'wa.majorworkallocationtypeid',
                DB::raw("COALESCE(CAST(wa.minorworkallocationtypeid AS TEXT), 'NA') as minorworkallocationtypeid"),
                'major.deptcode',
                'wa.catcode'
            )
            ->where('wa.workallocationtypeid', $workid)
            ->where('wa.statusflag', 'Y')
            ->first();
        /*$WorkData =   DB::table('audit.map_workallocation as wa')
                        ->select(
                            'wa.workallocationtypeid',
                            'wa.teamhead',
                            'wa.statusflag',
                            'wa.majorworkallocationtypeid',
                            'wa.minorworkallocationtypeid',
                        )
                        ->where('wa.workallocationtypeid',$workid)
                        ->where('wa.statusflag', 'Y')
                        ->first();*/



        // Return the data (can be used in a controller to return the response)
        return $WorkData;  // Eloquent collection

    }
    public static function Automateworkallocation($scheduleid)
    {
        try {
           
            $Session=session('user');
            $sessionuserid =$Session->userid;

            // $query = 'SELECT ' . self::$workallocation_function . '(:scheduleid,:user) AS response';
            // $bindings = [
            //     'scheduleid' => $scheduleid,
            //     'userid'     => $sessionuserid
            // ];
            
            // echo vsprintf(str_replace([':scheduleid', ':userid'], ['%s', '%s'], $query), $bindings);
            
 $result = DB::select("SELECT audit.workallocationAutomation(?, ?) AS response", [$scheduleid, $sessionuserid]);
 return $result;

//return response()->json($result);

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

    public static function fetch_allocatedwork($auditscheduleid)
    {
        $table = self::$transworkallc_table;
        return DB::table($table)
            ->join(self::$instauditschedulemem_table . ' as inm', 'inm.schteammemberid', '=',  $table . '.schteammemberid')
            ->join(self::$instauditschedule_table . ' as asch', 'asch.auditscheduleid', '=', $table . '.auditscheduleid')
            ->join(self::$auditplan_table . ' as plan', 'plan.auditplanid', '=', 'asch.auditplanid')
            ->join(self::$institution_table . ' as inst', 'inst.instid', '=',  'plan.instid')
            ->join(self::$mapallocationobjection_table . ' as map', 'map.mapallocationobjectionid', '=', $table . '.workallocationtypeid')
            ->join(self::$majorworkallocation_table . ' as major', 'major.majorworkallocationtypeid', '=', 'map.majorworkallocationtypeid')
            ->join(self::$group_table . ' as grp', 'grp.groupid', '=', 'map.groupid')

            ->join(self::$userdetail_table . ' as dept', 'dept.deptuserid', '=', 'inm.userid')
            ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'dept.desigcode')
            ->where($table . '.auditscheduleid', $auditscheduleid)
            ->select(
                'dept.deptuserid',
                'dept.username',
                'dept.usertamilname',
                'desig.desigelname',
                'desig.desigtlname',
                'inst.instename',
                'inst.insttname',
                'grp.groupename',
                'grp.grouptname',
                DB::raw('string_agg(DISTINCT major.majorworkallocationtypetname, \',\' ORDER BY major.majorworkallocationtypetname ASC) as worktypes_ta'),

                DB::raw('string_agg(DISTINCT major.majorworkallocationtypeename, \',\' ORDER BY major.majorworkallocationtypeename ASC) as worktypes_en')
            )
            ->groupBy(
                'dept.deptuserid',
                'dept.username',
                'dept.usertamilname',
                'desig.desigelname',
                'desig.desigtlname',
                'inst.instename',
                'inst.insttname',
                'grp.groupename',
                'grp.grouptname',
            )
            ->orderBy('dept.desigcode', 'asc')
            ->orderBy('dept.deptuserid', 'asc') // Order by deptuserid
            ->get();
    }

}
