<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\View;


class CalendarModel extends Model
{
    protected $connection   = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL

    protected $table        =  BaseModel::INSTSCHEDULE_TABLE;

    protected static $Instschedule_Table            =  BaseModel::INSTSCHEDULE_TABLE;
    protected static $InstscheduleMem_Table         =  BaseModel::INSTSCHEDULEMEM_TABLE;
    protected static $AuditPlan_Table               =  BaseModel::AUDITPLAN_TABLE;
    protected static $AuditPlanTeam_Table           =  BaseModel::AUDITPLANTEAM_TABLE;
    protected static $Institution_Table             =  BaseModel::INSTITUTION_TABLE;
    protected static $AuditeeUserDetail_Table       =  BaseModel::AUDITEEUSERDETAIL_TABLE;
    protected static $TypeofAudit_Table             =  BaseModel::TYPEOFAUDIT_TABLE;
    protected static $Dept_Table                    =  BaseModel::DEPT_TABLE;
    protected static $MstAuditeeInsCategory_Table   =  BaseModel::MSTAUDITEEINSCATEGORY_TABLE;
    protected static $AuditQuarter_Table            =  BaseModel::AUDITQUARTER_TABLE;
    protected static $UserChargeDetails_Table       =  BaseModel::USERCHARGEDETAIL_TABLE;
    protected static $UserDetails_Table             =  BaseModel::USERDETAIL_TABLE;
    protected static $Designation_Table             =  BaseModel::DESIGNATION_Table;
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



    // No auto-increment
    protected $primaryKey = 'auditscheduleid'; // No primary key
    // public $timestamps = true;
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon';

    protected $fillable = [
        'auditplanid',
        'fromdate',
        'todate',
        'rcno',
        'statusflag'

    ];


    public static function GetSchedultedEventDetails($scheduleid)
    {
        $table = self::$Instschedule_Table;

        return self::query()
        // Join statements
        ->join(self::$InstscheduleMem_Table . ' as inm', 'inm.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
        ->join(self::$AuditPlan_Table .'  as ap', 'ap.auditplanid', '=', 'inst_auditschedule.auditplanid')
        ->join(self::$AuditPlanTeam_Table . ' as at', 'ap.auditteamid', '=', 'at.auditplanteamid')
        ->join(self::$Institution_Table . ' as ai', 'ai.instid', '=', 'ap.instid')
        ->join(self::$TypeofAudit_Table . ' as mst', 'mst.typeofauditcode', '=', 'ap.typeofauditcode')
        ->join(self::$Dept_Table . ' as msd', 'msd.deptcode', '=', 'ai.deptcode')
        ->join(self::$District_Table .' as msi', 'msi.distcode', '=', 'ai.distcode')
        ->join(self::$MstAuditeeInsCategory_Table . ' as mac', 'mac.catcode', '=', 'ai.catcode')
        ->join(self::$AuditQuarter_Table . ' as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
        ->join(self::$UserChargeDetails_Table . ' as uc', 'uc.userid', '=', 'inm.userid')
        ->join(self::$UserDetails_Table . ' as du', 'uc.userid', '=', 'du.deptuserid')
        ->join(self::$ChargeDetails_Table .' as cd', 'uc.chargeid', '=', 'cd.chargeid')
        ->join(self::$Designation_Table . ' as de', 'de.desigcode', '=', 'du.desigcode')
        ->join(self::$MapYearcode_Table . ' as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
        ->join(self::$AuditPeriod_Table .' as period', DB::raw('CAST(yrmap.yearselected AS INTEGER)'), '=', 'period.auditperiodid')

        // Where conditions
        ->where($table .'.auditscheduleid', '=', $scheduleid) // Filter by deptuserid
        ->where('inm.statusflag', '=', 'Y') // Filter by statusflag = 'Y'

        // Select columns
        ->select(
             $table .'.auditscheduleid',
             $table .'.fromdate',
             $table .'.todate',
             $table .'.auditeeresponse',
            'inm.userid',
            'du.username',
            'ai.instename',
            'ai.insttname',
            'ai.mandays',
            'ai.deptcode',
            'ai.instid',
            'ap.auditteamid',
            'ap.auditplanid',
            'at.teamname',
            'mst.typeofauditename',
            'msd.deptesname',
            'msd.deptelname',
            'msi.distename',
            'mac.catename',
            'maq.auditquarter',
            'ap.statusflag',
            'cd.chargedescription',
            'de.desigelname',
            'inm.auditteamhead',
            DB::raw('STRING_AGG(distinct period.fromyear || \'-\' || period.toyear, \', \') as yearname')
        )


        // Group by clause
        ->groupBy(
             $table .'.auditscheduleid',
             $table .'.fromdate',
             $table .'.todate',
             $table .'.auditeeresponse',
            'inm.userid',
            'du.username',
            'ai.instename',
            'ai.mandays',
            'ai.deptcode',
            'ai.instid',
            'ap.auditteamid',
            'ap.auditplanid',
            'at.teamname',
            'mst.typeofauditename',
            'msd.deptesname',
            'msd.deptelname',
            'msi.distename',
            'mac.catename',
            'maq.auditquarter',
            'ap.statusflag',
            'cd.chargedescription',
            'de.desigelname',
            'inm.auditteamhead'
        )
        ->first(); // Execute the query

    }


    public static function fetchAuditScheduleDetailsDeptUsers($deptuserid)
    {
        $table = self::$Instschedule_Table;

        return self::query()
            // Join statements
            ->join(self::$InstscheduleMem_Table . ' as inm', 'inm.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
            ->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', 'inst_auditschedule.auditplanid')
            ->join(self::$AuditPlanTeam_Table . ' as at', 'ap.auditteamid', '=', 'at.auditplanteamid')
            ->join(self::$Institution_Table . ' as ai', 'ai.instid', '=', 'ap.instid')
            ->join(self::$TypeofAudit_Table . ' as mst', 'mst.typeofauditcode', '=', 'ap.typeofauditcode')
            ->join(self::$Dept_Table . ' as msd', 'msd.deptcode', '=', 'ai.deptcode')
            ->join(self::$MstAuditeeInsCategory_Table . ' as mac', 'mac.catcode', '=', 'ai.catcode')
            ->join(self::$AuditQuarter_Table . ' as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
            //->join(self::$UserChargeDetails_Table . ' as uc', 'uc.userid', '=', 'inm.userid')
            ->join(self::$UserDetails_Table . ' as du', 'inm.userid', '=', 'du.deptuserid')
            //->join(self::$ChargeDetails_Table .' as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join(self::$Designation_Table . ' as de', 'de.desigcode', '=', 'du.desigcode')
            ->join(self::$MapYearcode_Table . ' as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$AuditPeriod_Table .' as period', DB::raw('CAST(yrmap.yearselected AS INTEGER)'), '=', 'period.auditperiodid')

            // Where conditions
            ->where('du.deptuserid', '=', $deptuserid) // Filter by deptuserid
            ->where('inm.statusflag', '=', 'Y') // Filter by statusflag = 'Y'

            // Select columns
            ->select(
                $table .'.auditscheduleid',
                $table .'.fromdate',
                $table .'.todate',
                $table .'.auditeeresponse',
                'inm.userid',
                'du.username',
                'ai.instename',
                'ai.deptcode',
                'ai.instid',
                'ap.auditteamid',
                'ap.auditplanid',
                'at.teamname',
                'mst.typeofauditename',
                'msd.deptesname',
                'mac.catename',
                'maq.auditquarter',
                'ap.statusflag',
                //'cd.chargedescription',
                'de.desigelname',
                'inm.auditteamhead',
                DB::raw('STRING_AGG(period.fromyear || \'-\' || period.toyear, \', \') as yearname')
            )

            // Group by clause
            ->groupBy(
                 $table .'.auditscheduleid',
                 $table .'.fromdate',
                 $table .'.todate',
                 $table .'.auditeeresponse',
                'inm.userid',
                'du.username',
                'ai.instename',
                'ai.deptcode',
                'ai.instid',
                'ap.auditteamid',
                'ap.auditplanid',
                'at.teamname',
                'mst.typeofauditename',
                'msd.deptesname',
                'mac.catename',
                'maq.auditquarter',
                'ap.statusflag',
                //'cd.chargedescription',
                'de.desigelname',
                'inm.auditteamhead'
            )
            ->get(); // Execute the query
    }


    public static function createHoliday(array $data)
    {
        try {
            // Create and return the holiday record
            $holiday = HolidayModel::create([
                'holiday_title' => $data['holiday_title'],
                'holiday_date' => $data['holiday_date'],
                'statusflag'=>'Y',
                'createdon' => now(), // Automatically set the current timestamp for 'createdon'
                'updatedon' => now(), // Set 'updatedon' as well
            ]);

            // Return success response with the created holiday
            return $holiday->holiday_id;
        } catch (QueryException $e) {
            // Handle database exceptions (e.g., unique constraint violations)
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred: ' . $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            // Handle other exceptions
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public static function RemoveHoliday($id)
    {
        try {
            // Find the holiday by ID
            $holiday = HolidayModel::find($id);

            // Check if the holiday exists
            if ($holiday) {
                // Update the statusflag to 'N' (inactive or deleted)
                $holiday->statusflag = 'N';
                $holiday->save();  // Save the updated record

                return ['success' => true, 'message' => 'Holiday deleted successfully.'];
            } else {
                return ['success' => false, 'message' => 'Holiday not found.'];
            }
        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return [
                'success' => false,
                'message' => 'Error deleting the holiday event: ' . $e->getMessage(),
            ];
        }
    }

    public static function GetHoliday()
    {

        $holidays = HolidayModel::where('statusflag', 'Y')->get();

        // Return the events in a format that FullCalendar expects
        $events = $holidays->map(function ($holiday) {
            return [
                'id' => $holiday->holiday_id, // ID of the holiday
                'holiday_title' => $holiday->holiday_title,   // Holiday title
                'holiday_date' => $holiday->holiday_date,   // Holiday start date
                'extendedProps' => ['calendar' => 'danger' ],
            ];
        });

        return response()->json($events);

    }

    public static function FetchHoliday()
    {
        $holidays = HolidayModel::where('statusflag', 'Y') // Only active holidays
        ->get(['holiday_date', 'holiday_title']) // Fetch both date and name
        ->map(function ($holiday) {
            return [
                'date' => \Carbon\Carbon::parse($holiday->holiday_date)->format('d/m/Y'), // Format date
                'name' => $holiday->holiday_title, // Include holiday name
            ];
        });

        return response()->json($holidays);
    }


}


class HolidayModel extends Model
{
    // Set the connection if it's not the default (e.g., PostgreSQL)
    protected $connection = 'pgsql'; // Use 'pgsql' for PostgreSQL or 'mysql' for MySQL

    // Set custom column names for created_at and updated_at
    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon'; // Custom column name for `updated_at`

    // Define the table associated with this model
    protected $table = BaseModel::HOLIDAY_TABLE;  // The name of your holiday table

    // Set primary key if it differs from the default 'id'
    protected $primaryKey = 'holiday_id';  // Assuming `holiday_id` is the primary key

    // Set the primary key type if it's not an auto-incrementing integer
    protected $keyType = 'int';  // If `holiday_id` is an integer

    // Enable auto-incrementing
    public $incrementing = true;  // If true, it will be treated as an auto-incrementing column

    // Set the fillable fields
    protected $fillable = ['holiday_title', 'holiday_date','statusflag', 'createdon', 'updatedon'];


}

?>
