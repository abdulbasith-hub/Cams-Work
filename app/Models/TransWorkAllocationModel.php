<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TransWorkAllocationModel extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL


    protected $table = 'audit.trans_workallocation';
    protected $primaryKey = 'workallocationid'; // No primary key
    // public $timestamps = true;
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon';
    protected $fillable = [
        'subtypecode',
        'auditscheduleid',
        'statusflag',
        'schteammemberid'


    ];
    public static function fetchexistingwork($data)
    {
        return self::query()
            ->join('audit.mst_subworkallocationtype as msw', 'msw.subworkallocationtypeid', '=', 'trans_workallocation.subtypecode')
            ->join('audit.mst_majorworkallocationtype as mmw', 'mmw.majorworkallocationtypeid', '=', 'msw.majorworkallocationtypeid')
            ->where('auditscheduleid', $data['auditscheduleid'])
            // ->where('mmw.majorworkallocationtypeid', $data['majorwa'])
            ->where('trans_workallocation.statusflag', 'Y')
            ->get();
    }
    public static  function updatework($data, $subTypecode, $auditscheduleid)
    {

        $updatework = self::query()
            // ->where('subtypecode', $subTypecode)
            // ->where('schteammemberid', $data['schteammemberid'])
            ->where('auditscheduleid', $auditscheduleid)
            ->where('schteammemberid', $data['schteammemberid'])
            ->where('subtypecode', $subTypecode)
            ->where('statusflag', 'Y')
            ->update([
                'auditscheduleid'    =>  $data['auditscheduleid'],
                'schteammemberid'    =>  $data['schteammemberid'],
                'statusflag'         =>  $data['statusflag'],
                'subtypecode'        =>  $subTypecode,
                'createdon'          =>  now(),
                'updatedon'          =>  now(),
            ]);
        return $updatework;
        // $existingWork = self::where('schteammemberid', $data['schteammemberid']
        //     ->where('statusflag', 'F'));
    }
    public static function checkforsubtype($data, $subtype, $auditscheduleid = null)
    {

        $workexists = self::where('auditscheduleid', $data['auditscheduleid'])
            // ->where('auditscheduleid', $auditscheduleid)
            ->where('subtypecode', $subtype)
            ->where('statusflag', 'F')->exists();
        if ($workexists) {
            return response()->json(['error' => 'The Work was already allocated for the user'], 400);
        }
    }


    public static function fetchworkdetail($auditscheduleid, $TeamHead = '', $userid = '')
    {
        $query = self::query()

            ->join('audit.inst_schteammember as inm', 'inm.schteammemberid', '=', 'trans_workallocation.schteammemberid')
            ->join('audit.inst_auditschedule as asch', 'asch.auditscheduleid', '=', 'trans_workallocation.auditscheduleid')
            //->join('audit.map_workallocation as mapw', 'mapw.workallocationtypeid', '=', 'trans_workallocation.workallocationtypeid')
            ->join('audit.map_allocation_objection as mao', 'mao.mapallocationobjectionid', '=', 'trans_workallocation.workallocationtypeid')
            ->join('audit.group as grp', 'grp.groupid', '=', 'mao.groupid')

            //  ->join('audit.mst_subworkallocationtype as msw', 'msw.subworkallocationtypeid', '=', 'mapw.minorworkallocationtypeid')
            ->join('audit.mst_majorworkallocationtype as mmw', 'mmw.majorworkallocationtypeid', '=', 'mao.majorworkallocationtypeid')
            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'inm.userid')
            ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
            ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
            ->select(
                'du.username',
                'trans_workallocation.schteammemberid',
                // DB::raw('STRING_AGG(msw.subworkallocationtypeename, \', \') as subtypecodes'), // Concatenate all subtypecodes
                'trans_workallocation.statusflag',
                'asch.fromdate',
                'asch.todate',
                'mmw.majorworkallocationtypeename',
                'mmw.majorworkallocationtypeid',
                'asch.entrymeetdate',
                'asch.exitmeetdate',
                'groupename',
                'grouptname'
            )
            ->where('trans_workallocation.auditscheduleid', '=', $auditscheduleid)
            ->where('trans_workallocation.schteammemberid', '=', $userid)
            ->orderby('mmw.majorworkallocationtypeename');


        /* ->where(function ($query) {
            $query->where('trans_workallocation.statusflag', '=', 'Y')
                ->orWhere('trans_workallocation.statusflag', '=', 'F');
        })*/
        // ->groupBy(
        //     'du.username',
        //     'trans_workallocation.schteammemberid',
        //     'trans_workallocation.statusflag',
        //     'asch.fromdate',
        //     'asch.todate',
        //     'mmw.majorworkallocationtypeename',
        //     'mmw.majorworkallocationtypeid'
        // );

        // Conditionally add where clause based on $TeamHead value
        // if ($TeamHead == 'N') {
        //     $query->where('trans_workallocation.schteammemberid', '=', $userid);
        //     $query->where('trans_workallocation.statusflag', '=', 'F');
        // } else {
        //     $query->whereIn('trans_workallocation.statusflag', ['Y', 'F']);
        // }

        $result = $query->get();

        return $result;
    }


    public static function fetchSingleworkdetail($schteammemberid, $auditscheduleid, $major_id)
    {
        return  self::query()

            ->join('audit.inst_schteammember as inm', 'inm.schteammemberid', '=', 'trans_workallocation.schteammemberid')
            ->join('audit.inst_auditschedule as asch', 'asch.auditscheduleid', '=', 'trans_workallocation.auditscheduleid')
            ->join('audit.mst_subworkallocationtype as msw', 'msw.subworkallocationtypeid', '=', 'trans_workallocation.subtypecode')
            ->join('audit.mst_majorworkallocationtype as mmw', 'mmw.majorworkallocationtypeid', '=', 'msw.majorworkallocationtypeid')
            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'inm.userid')
            ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
            ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')

            ->select(
                'du.username',
                'inm.userid',
                'de.desigelname',
                'inm.schteammemberid',
                'trans_workallocation.workallocationid',
                'trans_workallocation.statusflag',
                'trans_workallocation.subtypecode',
                'asch.fromdate',
                'asch.todate',
                'mmw.majorworkallocationtypeename',
                'mmw.majorworkallocationtypeid',
                'msw.subworkallocationtypeename',
                'msw.subworkallocationtypeid',
            )

            // ->where('trans_workallocation.workallocationid', '=', $workallocationid)
            ->where('trans_workallocation.schteammemberid', '=', $schteammemberid)
            ->where('trans_workallocation.statusflag', '=', 'Y')
            ->where('mmw.majorworkallocationtypeid', '=', $major_id)
            // ->where(function ($query) {
            //     $query->where('trans_workallocation.statusflag', '=', 'Y')
            //         ->orWhere('trans_workallocation.statusflag', '=', 'F');
            // })
            // ->whereColumn('auser.instid', '=', 'ap.instid')
            ->where('inm.statusflag', '=', 'Y')
            // Exclude team head

            ->get();
    }
    public static function fetch_allocatedwork($auditscheduleid)
    {
        return self::query()
            ->join('audit.inst_schteammember as inm', 'inm.schteammemberid', '=', 'trans_workallocation.schteammemberid')
            ->join('audit.inst_auditschedule as asch', 'asch.auditscheduleid', '=', 'trans_workallocation.auditscheduleid')
            ->join('audit.map_workallocation as map', 'map.workallocationtypeid', '=', 'trans_workallocation.workallocationtypeid')
            ->join('audit.mst_majorworkallocationtype as major', 'major.majorworkallocationtypeid', '=', 'map.majorworkallocationtypeid')
            ->join('audit.deptuserdetails as dept', 'dept.deptuserid', '=', 'inm.userid')
            ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'dept.desigcode')
            ->where('trans_workallocation.auditscheduleid', $auditscheduleid)
            ->select(
                'dept.deptuserid',
                'dept.username',
                'desig.desigelname',
                'desig.desigtlname',
                DB::raw('string_agg(DISTINCT major.majorworkallocationtypetname, \',\' ORDER BY major.majorworkallocationtypetname ASC) as worktypes_ta'),

                DB::raw('string_agg(DISTINCT major.majorworkallocationtypeename, \',\' ORDER BY major.majorworkallocationtypeename ASC) as worktypes_en')
            )
            ->groupBy(
                'dept.deptuserid',
                'dept.username',
                'desig.desigelname',
                'desig.desigtlname',
            )
            ->orderBy('dept.desigcode', 'asc')
            ->orderBy('dept.deptuserid', 'asc') // Order by deptuserid
            ->get();
    }

   
}
