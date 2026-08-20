<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use Illuminate\Support\Facades\View;
use App\Services\SmsService;
use App\Models\MastersModel;
use Carbon\Carbon; 
use Exception;

class PerformanceModel extends Model
{
    protected static $dept_table = BaseModel::DEPARTMENT_TABLE;
    protected static $instauditschedule_table = BaseModel::INSTSCHEDULE_TABLE;
    protected static $instauditschedulemem_table = BaseModel::INSTSCHEDULEMEM_TABLE;
        protected static $praudit_instmap_table = BaseModel::PRAUDIT_INSTMAP_TABLE;
    protected static $auditeecategory = BaseModel::MSTAUDITEEINSCATEGORY_TABLE;
    protected static $subcategory = BaseModel::SUBCATEGORY_TABLE;
    protected static $fileupload = BaseModel::FILEUPLOAD_TABLE;
    protected static $mst_praudit_title_table = BaseModel::MST_PRAUDIT_TITLE_TABLE;
    protected static $AuditPlan_Table               =  BaseModel::AUDITPLAN_TABLE;
    protected static $Dept_Table                    =  BaseModel::DEPT_TABLE;
    protected static $distTable = BaseModel::DIST_Table;
    protected static $regionTable = BaseModel::REGION_TABLE;
    protected static $institution = BaseModel::INSTITUTION_TABLE;
     public static function deptfetch()
     {
         return DB::table(self::$dept_table . ' as dept')
             ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname') // Select required columns
             ->where('dept.statusflag', '=', 'Y') // Use the correct table alias for `statusflag`
             ->orderBy('dept.deptcode', 'asc')
             ->get();
     }

      public static function getcategoryByDept($deptcode)
    {
        return DB::table(self::$auditeecategory)
            //->select()
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->get(['catcode', 'catename', 'cattname']);
    }


     public static function getSubcategoryByCategory($cat_code)
    {
        $table = self::$auditeecategory;

        return DB::table($table . ' as aud')
            ->join(self::$subcategory . ' as sub', 'aud.catcode', '=', 'sub.catcode')
            ->select('sub.subcatename', 'sub.subcattname', 'sub.auditeeins_subcategoryid', 'aud.if_subcategory')
            ->distinct()
            ->where('sub.catcode', $cat_code)
            ->where('aud.if_subcategory', 'Y')
            ->orderBy('sub.subcatename', 'Asc')
            //  dd($date);
            ->get();
    }



public static function insertupdate_praudittitle(array $data, $praudittitleid = null)
{
    try {

        $table = self::$mst_praudit_title_table;

        $recordId   = $praudittitleid ?? ($data['praudittitleid'] ?? null);
        $deptcode   = $data['deptcode'] ?? null;
        $catcode    = $data['catcode'] ?? ($data['category'] ?? null);
        $titleename = $data['titleename'] ?? null;
        $titletname = $data['titletname'] ?? null;

        $subcatColumn = array_key_exists('subcategoryid', $data) ? 'subcategoryid' : 'subcatid';
        $subcatValue  = $data[$subcatColumn] ?? null;


        $comboQuery = DB::table($table)
            ->where('deptcode', $deptcode)
            ->where('catcode', $catcode)
            ->where($subcatColumn, $subcatValue);

        if (!empty($recordId)) {
            $comboQuery->where('praudittitleid', '<>', $recordId);
        }

        // if ($comboQuery->exists()) {
        //     throw new Exception('Already Exixsts', 409);
        // }



        $checkEname = strtolower(str_replace(' ', '', $titleename));
        $checkTname = strtolower(str_replace(' ', '', $titletname));

        $enameExists = DB::table($table)
            ->where('deptcode', $deptcode)
            ->where('catcode', $catcode)
            ->whereRaw("LOWER(REPLACE(titleename, ' ', '')) = ?", [$checkEname])
            ->when($recordId, function ($query) use ($recordId) {
                return $query->where('praudittitleid', '<>', $recordId);
            })
            ->exists();

        $tnameExists = DB::table($table)
            ->where('deptcode', $deptcode)
            ->where('catcode', $catcode)
            ->whereRaw("LOWER(REPLACE(titletname, ' ', '')) = ?", [$checkTname])
            ->when($recordId, function ($query) use ($recordId) {
                return $query->where('praudittitleid', '<>', $recordId);
            })
            ->exists();

            if ($enameExists && $tnameExists) {
                            throw new \Exception('TitleETnameExist');
                        } elseif ($enameExists) {
                            throw new \Exception('TitleEnameExist');
                        } elseif ($tnameExists) {
                          throw new \Exception('TitleTnameExist');
                }


        $payload = $data;
        $payload['catcode'] = $catcode;

        if (!empty($recordId)) {

            $affected = DB::table($table)
                ->where('praudittitleid', $recordId)
                ->update($payload);

            if ($affected === 0) {
                throw new Exception('No changes were made or record not found.');
            }

            return (object) ['praudittitleid' => $recordId];
        }

        $newId = DB::table($table)
            ->insertGetId($payload, 'praudittitleid');

        if (!$newId) {
            throw new Exception('Failed to insert performance audit title.');
        }

        return (object) ['praudittitleid' => $newId];

    } catch (\Illuminate\Database\QueryException $e) {

        throw new Exception('Database error while saving performance audit title.', 500);

    } catch (Exception $e) {

        throw new Exception($e->getMessage(), $e->getCode() ?: 409);
    }
}


     public static function fetch_prauditmasterrecords($praudittitleid = null, $table)
    {

        $sessiondet = session('charge');
        $sessiondeptcode =  $sessiondet->deptcode;



        $query = DB::table($table . ' as prt')
                    ->join(self::$dept_table . ' as d', 'd.deptcode', '=', 'prt.deptcode')
                     ->join(self::$auditeecategory . ' as c', 'c.catcode', '=', 'prt.catcode')
                    ->leftJoin(self::$subcategory . ' as sub', 'sub.auditeeins_subcategoryid', '=', 'prt.subcatid')
                    ->leftJoin(self::$fileupload . ' as f', 'f.fileuploadid', '=', 'prt.fileuploadid')

            ->select(
                'prt.praudittitleid',
                'd.deptcode',
                'd.deptesname',
                'd.depttsname',
                'd.deptelname',
                'd.depttlname',
                'c.catcode',
                'c.catename',
                'c.cattname',
                'prt.subcatid',
                'sub.subcatename',
                'sub.subcattname',
                'prt.titleename',
                'prt.titletname',
                'prt.statusflag',
                'prt.fileuploadid',
                'f.filepath',
                'f.filename',
            )
            ->when($praudittitleid, function ($query) use ($praudittitleid) {
                $query->where('prt.praudittitleid', $praudittitleid);
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
                $query->where('d.deptcode', '=', $sessiondeptcode);
            });
        $query->orderBy('prt.updatedon', 'desc');

        return $query->get();
    }


  public static function Modaldeptfetch()
    {
        return DB::table(self::$Dept_Table . ' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttsname', 'dept.depttlname') // Select required columns
            ->where('dept.statusflag', '=', 'Y') // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            //dd($query->toSql());
            ->get();
    }




public static function getDFinancialyear(){
    $query = DB::table('audit.mst_financialyear as year')
        ->select('year.financialyearcode', 'year.financialyear','year.financialyearid')
        ->where('year.statusflag', 'Y')
        ->distinct()
        ->get();

    return $query;
}

public static function getQuarter(){
    $query = DB::table(self::$AuditPlan_Table . ' as plan')
        ->select('plan.auditquartercode')
        ->distinct()
        ->orderby('plan.auditquartercode')
        ->get();

    return $query;
}


public static function commoncategoryfetch($deptcode)
{
    return DB::table(self::$auditeecategory)
        //->select()
        ->where('deptcode', $deptcode)
        ->where('statusflag', 'Y')
        ->orderby('catename')
        ->get(['catcode', 'catename', 'cattname','if_subcategory']);
}



public static function commmonSubcategoryByCategory($category)
{
    $table = self::$auditeecategory;

    return DB::table($table . ' as aud')
        ->join(self::$subcategory . ' as sub', 'aud.catcode', '=', 'sub.catcode')
        ->select('sub.subcatename', 'sub.subcattname', 'sub.auditeeins_subcategoryid','aud.if_subcategory','aud.catcode','aud.catename', 'aud.if_subcategory', 'aud.cattname')
        ->where('sub.catcode', $category)
        ->where('aud.if_subcategory', 'Y')
        ->orderBy('sub.subcatename', 'Asc')
        ->get();
}



public static function getRegionsByDept($deptcode)
{
    $table = self::$institution;

    return DB::table($table . ' as ins')
        ->join(self::$regionTable . ' as reg', 'ins.regioncode', '=', 'reg.regioncode')
        ->select('reg.regioncode', 'reg.regionename')
        ->distinct()
        ->where('ins.deptcode', $deptcode)
        ->where('ins.statusflag', 'Y')
        ->orderBy('reg.regionename', 'Asc')
        ->get();
}


public static function getmandaysandteamsizeDept($deptcode)
{

    return DB::table(self::$Dept_Table . ' as dept')
        ->select('dept.mandays', 'dept.teamsize')
        ->distinct()
        ->where('dept.deptcode', $deptcode)
        ->where('dept.statusflag', 'Y')
        ->get();
}




public static function getdistrictByregion($regioncode, $deptcode)
{
    $table = self::$institution;

    return DB::table($table . ' as ins')
        ->join(self::$distTable . ' as dis', 'ins.distcode', '=', 'dis.distcode')
        // ->join('audit.mst_region as reg', 'ins.regioncode', '=' , 'reg.regioncode')
        ->select('dis.distename', 'dis.distcode')
        ->distinct()
        ->where('ins.deptcode', $deptcode)
        ->where('ins.regioncode', $regioncode)
        ->where('ins.statusflag', 'Y')
        ->get();
}


public static function getinstituionbasedondistcommon($deptcode, $category, $subcategory,$regioncode,$distcode)
{
    $table = 'audit.mst_institution';

    return DB::table($table . ' as ins')
        ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'ins.deptcode')
        ->join('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', 'ins.catcode')
        ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ins.regioncode')
        ->join('audit.mst_district as dis', 'dis.distcode', '=', 'ins.distcode')
        ->leftJoin('audit.mst_auditeeins_subcategory as sub', 'sub.auditeeins_subcategoryid', '=', 'ins.subcatid')
        ->select(
            'ins.instename',
            'ins.instid',
            'ins.insttname'
        )
        ->where('ins.statusflag', 'Y')
        ->when(!empty($deptcode), function ($query) use ($deptcode) {
            $query->where('ins.deptcode', $deptcode);
        })
        ->when(!empty($category), function ($query) use ($category) {
            $query->where('ins.catcode', $category);
        })
        ->when(!empty($subcategory), function ($query) use ($subcategory) {
            $query->where('ins.subcatid', $subcategory);   // THIS IS THE ONLY CORRECT FILTER
        })
        ->when(!empty($regioncode), function ($query) use ($regioncode) {
            $query->where('ins.regioncode', $regioncode);   // THIS IS THE ONLY CORRECT FILTER
        })
        ->when(!empty($distcode), function ($query) use ($distcode) {
            $query->where('ins.distcode', $distcode);   // THIS IS THE ONLY CORRECT FILTER
        })
        ->orderBy('ins.instename', 'ASC')
        ->get();
}

public static function gettitlesubcategory($deptcode, $category, $subcategory){

    $table = 'audit.mst_praudit_title';

    return DB::table($table . ' as title')
        ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'title.deptcode')
        ->join('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', 'title.catcode')
        ->leftJoin('audit.mst_auditeeins_subcategory as sub', 'sub.auditeeins_subcategoryid', '=', 'title.subcatid')
        ->select(
            'title.praudittitleid',
            'title.titleename',
            'title.titletname'
        )
        ->where('title.statusflag', 'Y')
        ->when(!empty($deptcode), function ($query) use ($deptcode) {
            $query->where('title.deptcode', $deptcode);
        })
        ->when(!empty($category), function ($query) use ($category) {
            $query->where('title.catcode', $category);
        })
        ->when(!empty($subcategory), function ($query) use ($subcategory) {
            $query->where('title.subcatid', $subcategory);   // THIS IS THE ONLY CORRECT FILTER
        })
        ->orderBy('title.titleename', 'ASC')
        ->get();

}


public static function prauditinstmapping_insertupdate(array $data, $prauditmapid = null)
{
    $table = 'audit.mst_prauditinstmapping';

    try {

        /*
        |------------------------------------------
        | Duplicate Check
        | Avoid same instid + quarter + finyear
        |------------------------------------------
        */

        $query = DB::table($table)
            ->where('instid', $data['instid'])
            ->where('quartercode', $data['quartercode'])
            ->where('finyearcode', $data['finyearcode']);

        if ($prauditmapid) {
            $query->where('prauditmapid', '<>', $prauditmapid);
        }

        $exists = $query->exists();

        if ($exists) {
            throw new Exception('The selected institution already Exists.');
        }

        /*
        |------------------------------------------
        | UPDATE
        |------------------------------------------
        */
        if ($prauditmapid) {

            $affectedRows = DB::table($table)
                ->where('prauditmapid', $prauditmapid)
                ->update($data);

            if ($affectedRows === 0) {
                throw new Exception('FailedToUpdate');
            }

            return $prauditmapid;
        }

        /*
        |------------------------------------------
        | INSERT
        |------------------------------------------
        */
        $newId = DB::table($table)
            ->insertGetId($data, 'prauditmapid');

        if (!$newId) {
            throw new Exception('FailedToInsert');
        }

        return $newId;

    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }
}



public static function performanceaudit_fetchdata($prauditmapid = null, $table = null)
{
    $sessiondet = session('charge');
    $sessiondeptcode = $sessiondet->deptcode;
    $sessionregion = $sessiondet->regioncode;
    $sessiondistcode = $sessiondet->distcode;


    $table = 'audit.mst_prauditinstmapping';

    $query = DB::table($table . ' as pr')
         ->join( self::$institution . ' as ins', 'ins.instid', '=','pr.instid')
        ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'ins.deptcode')
        ->join('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', 'ins.catcode')
        ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ins.regioncode')
        ->join('audit.mst_district as dis', 'dis.distcode', '=', 'ins.distcode')
        ->leftJoin('audit.mst_auditeeins_subcategory as sub', 'sub.auditeeins_subcategoryid', '=', 'ins.subcatid')
        ->join('audit.mst_financialyear as yr', 'yr.financialyearcode', '=', 'pr.finyearcode')
        ->join('audit.mst_praudit_title as title', 'title.praudittitleid', '=', 'pr.praudittitleid')

            ->select(
            'pr.prauditmapid',
            'pr.statusflag',
            'pr.prioritycode',
            'ins.instename',
            'ins.instid',
            'reg.regioncode',
            'reg.regionename',
            'reg.regiontname',
            'ins.insttname',
            'dis.distename',
            'dis.distcode',
            'cat.catcode',
            'dept.teamsize',
            'dept.mandays',
            'title.praudittitleid',
            'title.titleename',
            'title.titletname',
            'pr.quartercode',
            'yr.financialyearcode',
            'yr.financialyear',
            'cat.if_subcategory as subcategory',
            'cat.catename',
            'cat.cattname',
            'sub.subcatename',
            'sub.subcattname',
            'sub.auditeeins_subcategoryid',
            'dept.deptcode',
            'dept.deptesname',

        )

        ->where('ins.statusflag', '=', 'Y')

        ->when($prauditmapid, fn($q) => $q->where('pr.prauditmapid', $prauditmapid))
        ->when($sessiondeptcode, fn($q) => $q->where('dept.deptcode', $sessiondeptcode))
        ->when($sessionregion, fn($q) => $q->where('reg.regioncode', $sessionregion))
        ->when($sessiondistcode, fn($q) => $q->where('dis.distcode', $sessiondistcode))

        ->orderBy('pr.updatedon', 'desc');

    $results = $query->get();

    return $results;
}


public static function getRegion()
{
$query = DB::table(self::$institution . ' as inst')
->Join(self::$regionTable . ' as reg', 'reg.regioncode', '=', 'inst.regioncode')
->where('inst.statusflag', 'Y')
->select('reg.regioncode', 'reg.regionename', 'reg.regiontname')
->distinct()

->get();

return $query;
}


public static function getDistrict()
{
$query = DB::table(self::$institution . ' as inst')
->Join('audit.mst_district as dist', 'dist.distcode', '=', 'inst.distcode')
->where('inst.statusflag', 'Y')
->select('dist.distcode', 'dist.distename', 'dist.disttname')
->distinct()
->get();

return $query;
}


public static function getPriority($deptcode)
{
    $today = Carbon::today()->toDateString();

    return DB::table('audit.mst_dept as dept')
        ->where('dept.statusflag', 'Y')
        ->where('dept.deptcode', $deptcode)

        ->select(

            DB::raw("
                CASE
                    WHEN '$today' >=  dept.autoplandate
                    THEN dept.nextquarter
                    ELSE dept.currentquarter
                END as quartercode
            "),

            DB::raw("
                CASE
                    WHEN '$today'  < dept.autoplandate
                         AND dept.supplementaryplan = 'Y'
                    THEN dept.inst_priority

                    WHEN  '$today' >= dept.autoplandate
                         AND dept.nextsupplementaryplans = 'Y'
                    THEN dept.nextinst_priority

                    ELSE NULL
                END as inst_priority
            ")
        )
        ->get();
}

public static function pafinalized($deptcode = null, $distcode = null)
{


    if (is_null($deptcode) || is_null($distcode)) {
        $sessionchargedel = session('charge');

        if (!$sessionchargedel) {
            return null;
        }

        $deptcode = $deptcode ?? $sessionchargedel->deptcode;
        $distcode = $distcode ?? $sessionchargedel->distcode;
    }

    //dd($deptcode, $distcode);

    if (empty($deptcode) || empty($distcode)) {
        return null;
    }

    $record = DB::table('audit.auditor_instmapping')
        ->where('deptcode', $deptcode)
        ->where('distcode', $distcode)
        ->first();

    return $record ? $record->pa_finalised : null;
}

  public static function get_titledet($auditscheduleid)
    {

    $query = DB::table(self::$instauditschedule_table . ' as sch')
    ->Join(self::$AuditPlan_Table . ' as plan', 'plan.auditplanid', '=', 'sch.auditplanid')
    ->join(self::$praudit_instmap_table . ' as prfmap', function ($join) {
    $join->on('prfmap.instid', '=', 'plan.instid')
    ->on('prfmap.finyearcode', '=', 'plan.financialyearcode')
    ->on('prfmap.quartercode', '=', 'plan.auditquartercode');
    })
    ->Join(self::$mst_praudit_title_table . ' as prf', 'prfmap.praudittitleid', '=', 'prf.praudittitleid')
    ->leftJoin(self::$fileupload . ' as fu', 'fu.fileuploadid', '=', 'prf.fileuploadid')
    ->select(
    'prf.titleename',
    'prf.titletname',
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
    )
    ->where('sch.statusflag', 'F')
    ->whereNotNull('sch.entrymeetdate')
    ->where('sch.auditscheduleid', $auditscheduleid)
    ->groupBy(
    'prf.titleename',
    'prf.titletname',
    );


    return $query->get();
    }

    public static function createRecord($data)
    {
    $table = 'audit.praudit_transpara';
    $insertData = [
    'schteammemberid' => $data['schteammemberid'] ?? session('user')->userid,
    'auditscheduleid' => $data['auditscheduleid'],
    'auditplanid' => $data['auditplanid'],
    'remarks' => json_encode($data['remarks']),
    'fileuploadid' => $data['fileuploadid'],
    'statusflag' => $data['statusflag'] ?? 'E',
    'createdby' => $data['createdby'],
    'createdon' => now()
    ];

    return DB::table($table)
    ->insertGetId($insertData, 'praudittransid');
    }

    public static function updateRecord($id, $data)
    {
    $table = 'audit.praudit_transpara';
    $updateData = [
    'auditscheduleid' => $data['auditscheduleid'],
    'auditplanid' => $data['auditplanid'],
    'remarks' => json_encode($data['remarks']),
    'fileuploadid' => $data['fileuploadid'],
    'statusflag' => $data['statusflag'],
    'updatedby' => $data['updatedby'] ?? session('user')->userid,
    'updatedon' => now()
    ];

    return DB::table($table)
    ->where('praudittransid', $id)
    ->update($updateData);
    }

    public static function updateFileUploadId($id, $fileuploadid, $userid)
    {
    $table = 'audit.praudit_transpara';

    return DB::table($table)
    ->where('praudittransid', $id)
    ->update([
    'fileuploadid' => $fileuploadid,
    'updatedby' => $userid,
    'updatedon' => now()
    ]);
    }

    public static function getExistingData($auditscheduleid, $userid)
    {
    $table = 'audit.praudit_transpara';
    return DB::table($table)
    ->where('auditscheduleid', $auditscheduleid)
    ->where('schteammemberid', $userid)
    ->orderBy('createdon', 'desc')
    ->first();
    }

    public static function getFileDetails($fileuploadid)
    {
    $table = self::$fileupload;
    return DB::table($table)
    ->where('fileuploadid', $fileuploadid)
    ->select('filename', 'filepath')
    ->first();
    }

    public static function updateFileStatus($fileuploadid, $status = 'N')
    {
    $table = self::$fileupload;

    $fileDetails = self::getFileDetails($fileuploadid);

    $updated = DB::table($table)
    ->where('fileuploadid', $fileuploadid)
    ->update(['statusflag' => $status]);

    return [
    'success' => $updated,
    'fileDetails' => $fileDetails
    ];
    }

 public static function getinstitutionremarksforpraudit($institutionId)
    {
        $table = self::$praudit_transpara_table;

        return DB::table($table . ' as praudit')
            ->join(self::$AuditPlan_Table . ' as plan', 'plan.auditplanid', '=', 'praudit.auditplanid')
            ->join(self::$instauditschedule_table . ' as sch', 'sch.auditscheduleid', '=', 'praudit.auditscheduleid')
            ->join(self::$deptuserdetails . ' as userdet', 'userdet.deptuserid', '=', 'praudit.schteammemberid')
            ->join(self::$instauditschedulemem_table . ' as mem', function ($join) {
                $join->on('mem.auditscheduleid', '=', 'praudit.auditscheduleid')
                    ->on('mem.userid', '=', 'userdet.deptuserid')
                    ->where('mem.statusflag', 'Y');
            })
            ->leftJoin('audit.fileuploaddetail as filedet', 'filedet.fileuploadid', '=', 'praudit.fileuploadid')
            ->where('plan.instid', $institutionId)
            ->where('praudit.statusflag', 'F')
            ->where('plan.statusflag', 'F')
            ->where('plan.auditmode', 'P')
            ->where('sch.statusflag', 'F')
            ->where('mem.statusflag', 'Y')
            ->select(
                'filedet.fileuploadid',
                'filedet.filepath',
                'filedet.filename',
                'praudit.remarks',
                'plan.auditplanid',
                'sch.auditscheduleid',
                'mem.schteammemberid',
                'mem.auditteamhead',
                'praudit.schteammemberid',
                'userdet.username',
                'userdet.usertamilname',
                'praudit.prreportverifyflag',
                'praudit.fileinreportflag',
                'praudit.praudittransid',
                'praudit.prfileverifyflag',
                'praudit.prremarksverifyflag'
            )
            ->orderBy('mem.auditteamhead','desc')
            ->orderBy('praudit.praudittransid','asc')
            ->get();
    }




}
