<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use Illuminate\Support\Facades\View;
use \DateTime;

class AuditDiaryHistoryModel extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon'; 
    const UPDATED_AT = 'updatedon'; 

    // Specify that the primary key is `userid` instead of `id`
    protected $primaryKey = 'audithistoryid';

    // Specify the table name
    protected $table = 'audit.auditdiary_history';

    // Set the primary key type if it's not an auto-incrementing integer
    protected $keyType = 'int';  // If `userid` is an integer

    // If your primary key is not auto-incrementing, set `incrementing` to false
    public $incrementing = true; // Set to `false` if `userid` is not auto-incrementing

    // Define the fillable fields
    protected $fillable = [
        'diaryid','workallocationid',  'percentofcompletion', 'fromdate',  'remarks','schteammemberid','statusflag','createdby','updatedby'
    ];
}

class AuditDiaryModel extends Model
{

  
	protected static $regionTable = BaseModel::REGION_TABLE;
	protected static $dist_table = BaseModel::DIST_Table;

    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon'; 
    const UPDATED_AT = 'updatedon'; 

    // Specify that the primary key is `userid` instead of `id`
    protected $primaryKey = 'diaryid';
    public $timestamps = false; // <--- IMPORTANT

    // Specify the table name
    protected $table = 'audit.auditdiary';

    // Set the primary key type if it's not an auto-incrementing integer
    protected $keyType = 'int';  // If `userid` is an integer

    // If your primary key is not auto-incrementing, set `incrementing` to false
    public $incrementing = true; // Set to `false` if `userid` is not auto-incrementing

    // Define the fillable fields
    protected $fillable = [
      'workallocationid',  'percentofcompletion', 'fromdate','remarks','schteammemberid','statusflag','createdon','updatedon','createdby','updatedby'
    ];

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


    // public static function createIfNotExistsOrUpdate(array $data,$auditdiaryid='',$actiontype,$userid=null)
    // {

    //     $data['statusflag'] = $data['statusflag'];

    //     if ($data['fromdate']) {
    //         $date = DateTime::createFromFormat('d/m/Y', $data['fromdate']);
    //         if ($date) {
    //             $data['fromdate'] = $date->format('Y/m/d');
    //         } else {
    //             $data['fromdate'] = null;
    //         }
           
    //     }
    //     try
    //     {
    //         if ($actiontype === 'Update' && $auditdiaryid) {
    //             $UpdateDiary = self::find($auditdiaryid);
    
    //             if (!$UpdateDiary) return null;
    
    //             $oldPercent = (int) $UpdateDiary->percentofcompletion;
    //             $newPercent = (int) ($data['percentofcompletion'] ?? 0);

    
    //             if ($oldPercent !== $newPercent) {
    //                     if ($oldPercent !== 0) {
    //                     $UpdateDiary['diaryid'] = $auditdiaryid;
    //                     AuditDiaryHistoryModel::create($UpdateDiary->toArray());
    //                 }

    //                 $UpdateDiary->update($data);
            
    //                 // ✅ If the new percent is 100, also save updated value to history
    //                 if ($newPercent === 100) {
    //                     $fresh = self::find($auditdiaryid);
    //                     $fresh['diaryid'] = $auditdiaryid;
    //                     AuditDiaryHistoryModel::create($fresh->toArray());
    //                 }
            
    //                 return $UpdateDiary;
    //             }

    //             $UpdateDiary->update($data);
    //             return $UpdateDiary;
    
    //         }    

    //         else
    //         {

    //             $CreateAuditDiaryid=self::create($data);
    //             $GetAuditDiaryId=$CreateAuditDiaryid->diaryid;
    //             return $GetAuditDiaryId;
    //         }
    //     } catch (QueryException $e) {
    //         // Handle any database-specific exceptions (e.g., duplicate entry)
    //         Log::error("Database error: " . $e->getMessage());
    //         throw new Exception("Database error occurred. Please try again later.");
    //     } catch (Exception $e) {
    //         // Handle any other general exceptions
    //         Log::error("General error: " . $e->getMessage());
    //         throw new Exception("Something went wrong: " . $e->getMessage());
    //     }
    // }



    public static function createIfNotExistsOrUpdate(array $data, $auditdiaryid = '', $actiontype, $userid = null)
{
    $data['statusflag'] = $data['statusflag'];

    // Format fromdate to Y-m-d for comparison
    if (!empty($data['fromdate'])) {
        $date = DateTime::createFromFormat('d/m/Y', $data['fromdate']);
        if ($date) {
            $data['fromdate'] = $date->format('Y-m-d');
        } else {
            $data['fromdate'] = null;
        }
    }

    try {
        if ($actiontype === 'Update' && $auditdiaryid) {
            $UpdateDiary = self::find($auditdiaryid);

	            if (!$UpdateDiary) return null;
		if (!empty($data['percentofcompletion']) && empty($data['remarks'])) {
                $data['remarks'] = $UpdateDiary->remarks;
            }
            // Detect if anything changed
            $hasChanged = false;
            $fieldsToCheck = ['percentofcompletion', 'remarks', 'fromdate'];

            foreach ($fieldsToCheck as $field) {
                // Note: Ensure null comparison is safe
                if ((string) $UpdateDiary->$field !== (string) $data[$field]) {
                    $hasChanged = true;
                    break;
                }
            }

            if ($hasChanged) {
                // Set updatedon only if there's a change
                $data['updatedon'] = View::shared('get_nowtime');

                $oldPercent = (int) $UpdateDiary->percentofcompletion;
                $newPercent = (int) $data['percentofcompletion'];

                if ($oldPercent !== $newPercent && $oldPercent !== 0) {
                    $UpdateDiary['diaryid'] = $auditdiaryid;
                    AuditDiaryHistoryModel::create($UpdateDiary->toArray());
                }

                $UpdateDiary->update($data);

                if ($newPercent === 100) {
                    $fresh = self::find($auditdiaryid);
                    $fresh['diaryid'] = $auditdiaryid;
                    AuditDiaryHistoryModel::create($fresh->toArray());
                }

                return $UpdateDiary;
            }

            // No changes, return existing record
            return $UpdateDiary;
        } else {
            // Creation flow
            $data['createdon'] = View::shared('get_nowtime');
            $data['updatedon'] = View::shared('get_nowtime');

            $CreateAuditDiaryid = self::create($data);
 	if ((int)$data['percentofcompletion'] === 100) {
                $data['diaryid'] = $CreateAuditDiaryid->diaryid;
                AuditDiaryHistoryModel::create($data);
            }
        }
	$existing = self::where('workallocationid', $data['workallocationid'])
                ->where('schteammemberid', $data['schteammemberid'])
                ->first();

            if ($existing) {
                $existing->update($data);
                return $existing;
            }
    } catch (QueryException $e) {
        Log::error("Database error: " . $e->getMessage());
        throw new Exception("Database error occurred. Please try again later.");
    } catch (Exception $e) {
        Log::error("General error: " . $e->getMessage());
        throw new Exception("Something went wrong: " . $e->getMessage());
    }
}


    


    public static function auditdiary_finalize($scheduleid,$memberid)
    {
        $table = 'audit.inst_schteammember';
    
        return DB::table($table)
            ->where('auditscheduleid', $scheduleid)
            ->where('schteammemberid', $memberid)
            ->update(['diarystatus' => 'F']);
    }
    



    public static function Fetch_Cat_subCat($auditscheduleid, $schteammemberid)
    {
        $connection = 'pgsql'; 
        $table = 'audit.trans_workallocation'; 

          //  print_r($auditscheduleid);echo '<br>';print_r($schteammemberid);
            $AllData  = DB::table($table)
                        ->join(self::$instauditschedulemem_table . ' as inm', 'inm.schteammemberid', '=',  $table . '.schteammemberid')
                        ->join(self::$instauditschedule_table . ' as asch', 'asch.auditscheduleid', '=', $table . '.auditscheduleid')
                        ->join(self::$auditplan_table . ' as plan', 'plan.auditplanid', '=', 'asch.auditplanid')
                        ->join(self::$institution_table . ' as inst', 'inst.instid', '=',  'plan.instid')
                        ->join(self::$mapallocationobjection_table . ' as map', 'map.mapallocationobjectionid', '=', $table . '.workallocationtypeid')
                        ->join(self::$majorworkallocation_table . ' as major', 'major.majorworkallocationtypeid', '=', 'map.majorworkallocationtypeid')
                        ->join(self::$group_table . ' as grp', 'grp.groupid', '=', 'map.groupid')
                        ->join(self::$userdetail_table . ' as dept', 'dept.deptuserid', '=', 'inm.userid')
                        ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'dept.desigcode')
                       // ->where($table . '.statusflag', '=', 'Y')
                        ->where($table . '.auditscheduleid', $auditscheduleid)
                        ->where($table . '.schteammemberid', $schteammemberid)
                        ->select(
                            'grp.groupid',
                            'grp.groupename',
                            'grp.grouptname',
                            'major.majorworkallocationtypeid',
                            'major.majorworkallocationtypeename',
                             $table . '.workallocationid'
                             )
                        ->groupBy(
                            'grp.groupid',
                            'grp.groupename',
                            'grp.grouptname',
                            'major.majorworkallocationtypeid',
                            'major.majorworkallocationtypeename',
                            $table . '.workallocationid'

                        )
                        ->orderBy('major.majorworkallocationtypeename', 'asc')
                        ->get();

                        
        return $AllData;

    }

    public static function DiaryFetchData($auditscheduleid, $schteammemberid)
    {
        $connection = 'pgsql';
        $table = 'audit.trans_workallocation'; // Full table name (including schema)

       

            $AuditDiaryDataFetch = DB::connection($connection)
                                        ->table($table)
                                        ->join('audit.auditdiary as audiary', 'audit.trans_workallocation.workallocationid', '=', 'audiary.workallocationid')
                                        //->where('audit.trans_workallocation.statusflag', '=', 'F')
                                        ->where('audiary.statusflag', '=', 'Y')
                                        ->where('audit.trans_workallocation.auditscheduleid', '=', $auditscheduleid)
                                        ->where('audit.trans_workallocation.schteammemberid', '=', $schteammemberid)
                                        ->first();


            if($AuditDiaryDataFetch)
            {
                $AuditDiaryDataFetch = DB::connection($connection)
                                        ->table($table)
                                        //->join('audit.mst_subworkallocationtype as b', 'audit.trans_workallocation.subtypecode', '=', 'b.subworkallocationtypeid')
                                        //->join('audit.mst_majorworkallocationtype as c', 'b.majorworkallocationtypeid', '=', 'c.majorworkallocationtypeid')
                                        ->join('audit.auditdiary as audiary', 'audit.trans_workallocation.workallocationid', '=', 'audiary.workallocationid')
                                        //->where('audit.trans_workallocation.statusflag', '=', 'F')
                                        ->where('audiary.statusflag', '=', 'Y')
                                        //->where('b.statusflag', '=', 'Y')
                                       // ->where('c.statusflag', '=', 'Y')
                                        ->where('audit.trans_workallocation.auditscheduleid', '=', $auditscheduleid)
                                        ->where('audit.trans_workallocation.schteammemberid', '=', $schteammemberid)
                                        ->get();

                \Log::info('Fetched Audit Records:', $AuditDiaryDataFetch->toArray()); // Logs as array for better readability

            }else
            {
                $AuditDiaryDataFetch='nodata';
            }
            // Log the result for debugging (better than using print_r)

            return $AuditDiaryDataFetch;

        

    }

    

    public static function ShowHistory($diaryid)
    {
        $table = 'audit.auditdiary_history';
    
       return  DB::table($table)
        ->where('diaryid', $diaryid)
        ->select(
            'remarks',
            'diaryid',
            'fromdate',
            'percentofcompletion',
        )

      ->get();

    }

    public static function Showworkandgroup($diaryid)
    {
        $table = 'audit.auditdiary';

        return  DB::table(self::$transworkallc_table . ' as trans')
            ->join(self::$mapallocationobjection_table . ' as map', 'map.mapallocationobjectionid', '=', 'trans.workallocationtypeid')
            ->join($table . ' as diary', 'diary.workallocationid', '=', 'trans.workallocationid') 
            ->join(self::$majorworkallocation_table . ' as major', 'major.majorworkallocationtypeid', '=', 'map.majorworkallocationtypeid')
            ->join(self::$group_table . ' as grp', 'grp.groupid', '=', 'map.groupid')
            ->where('diary.diaryid', $diaryid)
            ->select(
                'major.majorworkallocationtypeid',
                'major.majorworkallocationtypeename',
                'grp.groupename',
                'grp.grouptname',
    
            )
 
            ->first();

    }


    public static function fetchAllusers($auditscheduleid,$schteammemberid)
    {
      
            $table = self::$transworkallc_table;

            $AuditDiaryDataFetch = DB::table($table)
                                    ->join(self::$instauditschedulemem_table . ' as inm', 'inm.schteammemberid', '=',  $table . '.schteammemberid')
                                    ->join(self::$instauditschedule_table . ' as asch', 'asch.auditscheduleid', '=', $table . '.auditscheduleid')
                                    ->join(self::$auditplan_table . ' as plan', 'plan.auditplanid', '=', 'asch.auditplanid')
                                    ->join(self::$institution_table . ' as inst', 'inst.instid', '=',  'plan.instid')
                                    ->join(self::$mapallocationobjection_table . ' as map', 'map.mapallocationobjectionid', '=', $table . '.workallocationtypeid')
                                    ->join('audit.auditdiary as audiary', 'audit.trans_workallocation.workallocationid', '=', 'audiary.workallocationid')
                                    ->join(self::$majorworkallocation_table . ' as major', 'major.majorworkallocationtypeid', '=', 'map.majorworkallocationtypeid')
                                    ->join(self::$group_table . ' as grp', 'grp.groupid', '=', 'map.groupid')
                                    ->join(self::$userdetail_table . ' as dept', 'dept.deptuserid', '=', 'inm.userid')
                                    ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'dept.desigcode')
                                    ->where($table . '.auditscheduleid', $auditscheduleid)
                                    ->where($table . '.schteammemberid', $schteammemberid)
                                    ->select(
                                        'grp.groupename',
                                        'grp.grouptname',
                                        'audiary.fromdate',
                                        'audiary.remarks',
                                        'audiary.percentofcompletion',
                                        'major.majorworkallocationtypetname',
                                        'major.majorworkallocationtypeename',
                                        'audiary.updatedon',
                                        'audiary.diaryid'
                                    )
                                    ->groupBy(
                                        'grp.groupename',
                                        'grp.grouptname',
                                        'audiary.fromdate',
                                        'audiary.remarks',
                                        'audiary.percentofcompletion',
                                        'major.majorworkallocationtypetname',
                                        'major.majorworkallocationtypeename',
                                        'audiary.updatedon',
                                        'audiary.diaryid'
                                    )
                                    ->orderBy('audiary.updatedon', 'desc')
                                    ->get();
            return $AuditDiaryDataFetch;
    }

public static function fetchAllusersdiary($auditscheduleid)
    {

            $table = self::$transworkallc_table;

            $AuditDiaryDataFetch = DB::table($table)
                                    ->join(self::$instauditschedulemem_table . ' as inm', 'inm.schteammemberid', '=',  $table . '.schteammemberid')
                                    ->join(self::$instauditschedule_table . ' as asch', 'asch.auditscheduleid', '=', $table . '.auditscheduleid')
                                    ->join(self::$auditplan_table . ' as plan', 'plan.auditplanid', '=', 'asch.auditplanid')
                                    ->join(self::$institution_table . ' as inst', 'inst.instid', '=',  'plan.instid')
                                    ->join(self::$mapallocationobjection_table . ' as map', 'map.mapallocationobjectionid', '=', $table . '.workallocationtypeid')
                                    ->join('audit.auditdiary as audiary', 'audit.trans_workallocation.workallocationid', '=', 'audiary.workallocationid')
                                    ->join(self::$majorworkallocation_table . ' as major', 'major.majorworkallocationtypeid', '=', 'map.majorworkallocationtypeid')
                                    ->join(self::$group_table . ' as grp', 'grp.groupid', '=', 'map.groupid')
                                    ->join(self::$userdetail_table . ' as dept', 'dept.deptuserid', '=', 'inm.userid')
                                    ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'dept.desigcode')
                                    ->where($table . '.auditscheduleid', $auditscheduleid)
                                    ->select(
                                        'grp.groupename',
                                        'grp.grouptname',
                                        'audiary.fromdate',
                                        'audiary.remarks',
                                        'audiary.percentofcompletion',
                                        'major.majorworkallocationtypetname',
                                        'major.majorworkallocationtypeename',
                                        'audiary.updatedon',
                                        'audiary.diaryid',
                                        'dept.username',
                                        'dept.usertamilname',
                                        'desig.desigelname',
                                        'desig.desigtlname',
                                        'inm.auditteamhead'
                                    )
                                    ->groupBy(
                                        'grp.groupename',
                                        'grp.grouptname',
                                        'audiary.fromdate',
                                        'audiary.remarks',
                                        'audiary.percentofcompletion',
                                        'major.majorworkallocationtypetname',
                                        'major.majorworkallocationtypeename',
                                        'audiary.updatedon',
                                        'audiary.diaryid',
                                        'dept.username',
                                        'dept.usertamilname',
                                        'desig.desigelname',
                                        'desig.desigtlname',
                                        'inm.auditteamhead'
                                    )
                                    ->orderBy('audiary.updatedon', 'desc')
                                    ->get();
            return $AuditDiaryDataFetch;
    }

    public static function userdetails_fetch()
    {
            $chargeData = session('charge');
            $session_deptcode = $chargeData->deptcode; // Accessing the department code from the session
            $session_usertypecode = $chargeData->usertypecode;
            $userData = session('user');
            $session_userid = $userData->userid;



            // Perform a database quer
            $inst_details = DB::table('audit.inst_schteammember as sm')
                                ->join('audit.inst_auditschedule as is', 'is.auditscheduleid', '=', 'sm.auditscheduleid')
                                ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'is.auditplanid')
                                ->join('audit.mst_institution as in', 'in.instid', '=', 'ap.instid')
                                //->join('audit.mst_auditeeins_category as incat', 'incat.catcode', '=', 'in.catcode')
                                //->join('audit.mst_typeofaudit as ta', 'ta.typeofauditcode', '=', 'ap.typeofauditcode')
                                //->join('audit.mst_auditperiod as d', 'd.auditperiodid', '=', 'ap.auditperiodid')
                                ->where('sm.userid', $session_userid)
                                ->select('is.fromdate', 'is.todate', 'is.auditscheduleid','sm.auditscheduleid','sm.auditteamhead','is.auditplanid','is.fromdate','ap.instid','in.instename','in.mandays','sm.auditteamhead','sm.schteammemberid')
                                ->orderBy('is.fromdate', 'desc') // Sort by `fromdate` in descending order
                                ->first();

            if($inst_details)
            {
                return $inst_details;

            }else{
                return false;
            }

    }

    public static function GetInstituteDetails($session_userid,$auditscheduleid)
    {       
        $inst_details = DB::table('audit.inst_schteammember as sm')
                            ->join('audit.inst_auditschedule as is', 'is.auditscheduleid', '=', 'sm.auditscheduleid')
                            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'is.auditplanid')
                            ->join('audit.mst_institution as in', 'in.instid', '=', 'ap.instid')
                            ->join('audit.mst_auditeeins_category as incat', 'incat.catcode', '=', 'in.catcode')
                            ->join('audit.mst_typeofaudit as ta', 'ta.typeofauditcode', '=', 'ap.typeofauditcode')
                        //  ->join('audit.mst_auditperiod as d', 'd.auditperiodid', '=', 'ap.auditperiodid')
                            ->join('audit.yearcode_mapping as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
                            ->join(
                            'audit.mst_auditperiod as d',
                            DB::raw('CAST(yrmap.yearselected AS INTEGER)'),
                            '=',
                            'd.auditperiodid'
                        )
                            ->where('userid', $session_userid)
                            ->where('is.auditscheduleid', $auditscheduleid)
                            // Apply STRING_AGG to aggregate years

                            ->select( 'is.auditscheduleid','sm.auditscheduleid','sm.auditteamhead','is.auditplanid','is.fromdate','is.todate','ap.instid','in.instename','incat.catename','in.mandays','sm.auditteamhead','ta.typeofauditename','sm.schteammemberid'
                            ,DB::raw('STRING_AGG(DISTINCT d.fromyear || \'-\' || d.toyear, \', \') as yearname') )
                            ->groupby('is.auditscheduleid','sm.auditscheduleid','sm.auditteamhead','is.auditplanid','is.fromdate','is.todate','ap.instid','in.instename','incat.catename','in.mandays','sm.auditteamhead','ta.typeofauditename','sm.schteammemberid')

                            ->first();

        return $inst_details;

        
    }

   
    public static function auditdiarytablehomeforfetch1($sessionuserid,$auditscheduleid,$schteammemberid){

       // dd($sessionuserid);
       
        return DB::table(self::$instauditschedulemem_table . ' as scm')
        ->join(self::$instauditschedule_table . ' as sc', 'sc.auditscheduleid', '=', 'scm.auditscheduleid')
        ->join(self::$auditplan_table . ' as ap', 'ap.auditplanid', '=', 'sc.auditplanid')
        ->join('audit.trans_workallocation as twa', function ($join) use ($auditscheduleid, $schteammemberid) {
            $join->on('twa.auditscheduleid', '=', 'sc.auditscheduleid')
                 ->on('twa.schteammemberid', '=', 'scm.schteammemberid');
        })
        ->join(self::$institution_table . ' as mi', 'mi.instid', '=', 'ap.instid')
        ->join('audit.mst_auditquarter' . ' as aq', 'ap.auditquartercode', '=', 'aq.auditquartercode')
        ->join('audit.mst_dept as dept','mi.deptcode', '=', 'dept.deptcode')
        ->join('audit.mst_district as dist','mi.distcode', '=', 'dist.distcode')
        ->join('audit.mst_region as reg','reg.regioncode', '=', 'mi.regioncode')
        ->where('sc.auditeeresponse', 'A')
        ->where('sc.workallocationflag', 'Y')
        ->where('scm.userid', '=', $sessionuserid)
        ->where('twa.auditscheduleid', '=', $auditscheduleid)
        ->where('twa.schteammemberid', '=', $schteammemberid)
        ->where('sc.statusflag', 'F')
        ->where('scm.statusflag', 'Y')
        ->whereColumn('ap.auditquartercode', 'dept.currentquarter')
        ->groupBy(
            'sc.auditscheduleid',
            'ap.auditplanid',
            'ap.instid',
            'mi.instename',
            'mi.mandays',
            'aq.auditquarter',
            'sc.fromdate',
            'sc.todate',
            'scm.schteammemberid',
            'scm.diarystatus',
            'sc.entrymeetdate',
            'sc.exitmeetdate',
            'dist.distename',
            'reg.regionename',
            'dept.deptcode',
            'dept.deptelname',
        )
        ->select(
            'sc.auditscheduleid',
            'sc.fromdate',
            'sc.todate',
            'ap.auditplanid',
            'ap.instid',
            'mi.instename',
            'aq.auditquarter',
            'mi.mandays',
            'scm.diarystatus',
            'scm.schteammemberid',
            'sc.entrymeetdate',
            'sc.exitmeetdate',
            'dist.distename',
            'reg.regionename',
            'dept.deptcode',
            'dept.deptelname',
            DB::raw("(
                SELECT head.username || ' - ' || desig.desigelname
                FROM audit.auditplanteammember AS head_tm
                JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
                JOIN audit.mst_designation AS desig ON desig.desigcode = head.desigcode
                WHERE head_tm.auditplanteamid = ap.auditteamid AND head_tm.teamhead = 'Y' AND head_tm.statusflag ='Y'
                LIMIT 1
            ) AS team_head_en"),    
            DB::raw("(
                        SELECT COALESCE(STRING_AGG(member.username || ' - ' || desig2.desigelname, ', '), '')
                        FROM audit.auditplanteammember AS member_tm
                        JOIN audit.deptuserdetails AS member ON member.deptuserid = member_tm.userid
                        JOIN audit.mst_designation AS desig2 ON desig2.desigcode = member.desigcode
                        WHERE member_tm.auditplanteamid = ap.auditteamid AND member_tm.teamhead != 'Y' AND member_tm.statusflag ='Y'
                    ) AS team_members_en"),
                )
        ->get();


    }



    public static function auditdiarytablehomeforfetch($sessionuserid){

        // dd($sessionuserid);      
         return  DB::table(self::$instauditschedulemem_table . ' as scm')
         ->join(self::$instauditschedule_table . ' as sc', 'sc.auditscheduleid', '=', 'scm.auditscheduleid')
         ->join(self::$auditplan_table . ' as ap', 'ap.auditplanid', '=', 'sc.auditplanid')
         ->join(self::$deptartment_table . ' as dept', 'ap.auditquartercode', '=', 'dept.currentquarter')
         ->join(self::$institution_table . ' as mi', 'mi.instid', '=', 'ap.instid')
         ->join('audit.mst_district as dist','mi.distcode', '=', 'dist.distcode')
         ->join('audit.mst_region as reg','reg.regioncode', '=', 'mi.regioncode')
         ->where('sc.auditeeresponse', 'A')
         ->where('sc.workallocationflag', 'Y')
         ->whereNotNull('sc.entrymeetdate')
         ->where('scm.userid', '=', $sessionuserid)
	 ->where('sc.statusflag', 'F')
  	 ->whereColumn('ap.auditquartercode', 'dept.currentquarter')
         ->groupBy(
             'sc.auditscheduleid',
             'ap.auditplanid',
             'ap.instid',
             'mi.instename',
             'mi.mandays',
             'sc.fromdate',
             'sc.todate',

             'scm.diarystatus',

             'scm.schteammemberid',
             'sc.entrymeetdate',
             'sc.exitmeetdate',
             'dist.distename',
             'reg.regionename',
            
         )
         ->select(
             'sc.auditscheduleid',
             'sc.fromdate',
             'sc.todate',
             'ap.auditplanid',
             'ap.instid',
             'mi.instename',
             'mi.mandays',

             'scm.diarystatus',

             'scm.schteammemberid',
             'sc.entrymeetdate',
             'sc.exitmeetdate',
             'dist.distename',
             'reg.regionename',
            
             DB::raw("(
                 SELECT head.username || ' - ' || desig.desigelname
                 FROM audit.auditplanteammember AS head_tm
                 JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
                 JOIN audit.mst_designation AS desig ON desig.desigcode = head.desigcode
                 WHERE head_tm.auditplanteamid = ap.auditteamid AND head_tm.teamhead = 'Y' AND head_tm.statusflag ='Y'
                 LIMIT 1
             ) AS team_head_en"),    
             DB::raw("(
                         SELECT COALESCE(STRING_AGG(member.username || ' - ' || desig2.desigelname, ', '), '')
                         FROM audit.auditplanteammember AS member_tm
                         JOIN audit.deptuserdetails AS member ON member.deptuserid = member_tm.userid
                         JOIN audit.mst_designation AS desig2 ON desig2.desigcode = member.desigcode
                         WHERE member_tm.auditplanteamid = ap.auditteamid AND member_tm.teamhead != 'Y' AND member_tm.statusflag ='Y'
                     ) AS team_members_en"),
         )
         ->get();

    //     $querySql = $query->toSql();
    //     dd($query->toSql());

    //    $querySql = $query->toSql();
    //    $bindings = $query->getBindings();


    //    print_r($finalQuery);


         
 
 
     }


  
 public static function commondeptfetch()
     {
         return DB::table(self::$deptartment_table . ' as dept')
             ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname') // Select required columns
             ->where('dept.statusflag', '=', 'Y') // Use the correct table alias for `statusflag`
             ->orderBy('dept.deptcode', 'asc')
             ->get();
     }
     

     public static function getinstitutionBydistrictchange($district, $regioncode, $deptcode)
{
    $table = self::$institution_table;

    return DB::table($table . ' as ins')
        ->join('audit.auditplan' . ' as plan', 'ins.instid', '=', 'plan.instid')
        ->join('audit.mst_dept as dept', 'dept.currentquarter', '=', 'plan.auditquartercode')
        ->join('audit.inst_auditschedule' . ' as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
        ->select('ins.instename', 'ins.instid','ins.insttname')
        ->distinct()
        ->where('ins.distcode', $district)
        ->where('ins.deptcode', $deptcode)
        ->where('ins.regioncode', $regioncode)
         ->where('plan.statusflag', 'F')
	->whereNull('sch.exitmeetdate')
         ->where('sch.workallocationflag', 'Y')
         ->where('sch.statusflag', 'F')
        ->get();
}



public static function getRegionsByDept($deptcode)
{
    $table = self::$institution_table;

    return DB::table($table . ' as ins')
        ->join(self::$regionTable . ' as reg', 'ins.regioncode', '=', 'reg.regioncode')
        ->select('reg.regioncode', 'reg.regionename','reg.regiontname')
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
        ->select('dis.distename', 'dis.distcode','dis.disttname')
        ->distinct()
        ->where('ins.deptcode', $deptcode)
        ->where('ins.regioncode', $regioncode)
        ->where('ins.statusflag', 'Y')
        ->get();
}


public static function getUsernameBasedOnInstitution($instMappingCode)
{

    $table = self::$institution_table;

    return DB::table($table . ' as ins')
        ->join('audit.auditplan as plan', 'ins.instid', '=', 'plan.instid')
        ->join('audit.mst_dept as dept', 'dept.currentquarter', '=', 'plan.auditquartercode')
        ->join('audit.inst_auditschedule as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
        ->join('audit.inst_schteammember as schmem', 'schmem.auditscheduleid', '=', 'sch.auditscheduleid')
        ->join('audit.deptuserdetails as user', 'user.deptuserid', '=', 'schmem.userid')
        ->select('user.username','schmem.schteammemberid', 'schmem.auditteamhead','user.deptuserid')
        ->where('user.statusflag', 'Y')
        ->where('schmem.statusflag', 'Y')
        ->groupBy('user.username','schmem.schteammemberid', 'schmem.auditteamhead','user.deptuserid')
        ->where('ins.instid', $instMappingCode)
        ->get();

}



public static function auditdiaryfetchData($table = null)
{
    $table = 'audit.auditplan';

    $query = DB::table($table . ' as plan')  
        ->join(self::$institution_table . ' as ins', 'plan.instid', '=', 'ins.instid')
        ->join('audit.mst_dept as dept_current', 'dept_current.currentquarter', '=', 'plan.auditquartercode')
        ->join(self::$deptartment_table . ' as dept_actual', 'ins.deptcode', '=', 'dept_actual.deptcode')
        ->join('audit.inst_auditschedule as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
        ->join('audit.inst_schteammember as schm', 'schm.auditscheduleid', '=', 'sch.auditscheduleid')
        ->join('audit.deptuserdetails as user', 'user.deptuserid', '=', 'schm.userid')
        ->join(self::$regionTable . ' as reg', 'ins.regioncode', '=', 'reg.regioncode')
        ->join(self::$dist_table . ' as dis', 'ins.distcode', '=', 'dis.distcode')

        ->select(
            'ins.instid',
            'ins.instename',
            'ins.insttname',
            'reg.regioncode',
            'reg.regionename',
            'user.username',
            'reg.regiontname',
            'dis.distename',
            'dis.disttname',
            'dis.distcode',
            'dept_actual.deptcode',
            'plan.auditquartercode',
            'dept_actual.deptesname',
            'dept_actual.deptelname',
            'dept_actual.depttsname',
            'dept_actual.depttlname',
            'plan.auditplanid',
            'schm.diarystatus',
            'schm.schteammemberid',
            'schm.auditteamhead'
        )
        ->groupBy(
            'ins.instid',
            'ins.instename',
            'ins.insttname',
            'reg.regioncode',
            'reg.regionename',
            'user.username',
            'reg.regiontname',
            'dis.distename',
            'dis.disttname',
            'dis.distcode',
            'dept_actual.deptcode',
            'plan.auditquartercode',
            'dept_actual.deptesname',
            'dept_actual.deptelname',
            'dept_actual.depttsname',
            'dept_actual.depttlname',
            'plan.auditplanid',
            'schm.diarystatus',
            'schm.schteammemberid',
            'schm.auditteamhead'
        )
        
        ->where('plan.statusflag', 'F')
        ->where('sch.statusflag', 'F')
        ->where('sch.workallocationflag', 'Y')
	->whereNull('sch.exitmeetdate')
        ->orderBy('schm.updatedon', 'desc');

    return $query->get();
}



public static function auditdiary_insertupdate(array $data, $table = null, $userchargeid)
{
    $table = 'audit.inst_schteammember';
    $historyTable = 'audit.inst_schteammember_history';

    try {
        $userid = $data['usernamefield'] ?? null;
        $schteammemberid = $data['schteammemberid'] ?? null;

        if (!$userid) {
            throw new \Exception('User ID is missing.');
        }
        if (!$schteammemberid) {
            throw new \Exception('schteammemberid is missing.');
        }

        $now = View::shared('get_nowtime');

        $existingRecord = DB::table($table)
            ->where('userid', $userid)
            ->where('schteammemberid', $schteammemberid)
            ->first();

        if (!$existingRecord) {
            throw new \Exception('Record not found for logging.');
        }

        $historyData = (array) $existingRecord;

        $historyData['createdon'] = $now;
        $historyData['createdby'] = $userchargeid;
        $historyData['updatedon'] = $now;
        $historyData['updatedby'] = $userchargeid;

        DB::table($historyTable)->insert($historyData);

        $updateData = [
            'diarystatus' => 'N',
            'createdon'   => $now,
            'createdby'   => $userchargeid,
            'updatedon'   => $now,
            'updatedby'   => $userchargeid,
        ];

        $updated = DB::table($table)
            ->where('userid', $userid)
            ->where('schteammemberid', $schteammemberid)
            ->update($updateData);

        return $updated;

    } catch (\Exception $e) {
        throw new \Exception($e->getMessage());
    }
}

}
