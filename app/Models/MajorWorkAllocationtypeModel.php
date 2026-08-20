<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MajorWorkAllocationtypeModel extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL


    protected $table = 'audit.mst_majorworkallocationtype';
    protected $primaryKey = 'majorworkallocationtypeid'; // No primary key
    // public $timestamps = true;
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon';

    public static function audit_particulars()
    {
        return self::query()
            ->join('audit.mst_subworkallocationtype as sub', 'mst_majorworkallocationtype.majorworkallocationtypeid', '=', 'sub.majorworkallocationtypeid')

            ->select(
                'mst_majorworkallocationtype.majorworkallocationtypeename',
                'mst_majorworkallocationtype.majorworkallocationtypeid',
                'sub.subworkallocationtypeid',
                'sub.subworkallocationtypeename'

            )
            ->orderBy('mst_majorworkallocationtype.majorworkallocationtypeename', 'desc')
            ->where('sub.statusflag', '=', 'Y')
            ->get();
    }

    public static function callforrecords($catcode, $deptcode, $subcatcode)
    {
        //print_R($catcode);exit;
        /*return DB::table('audit.map_callforrecord')
        ->join('audit.callforrecords_auditee as cfa', 'audit.map_callforrecord.callforecordsid', '=', 'cfa.callforrecordsid')
        ->join('audit.map_allocation_objection as mao', 'mao.mapcallforrecordsid', '=', 'map_callforrecord.mapcallforrecordid')
        ->orderBy('cfa.callforrecordsename', 'asc') // Order by callforrecordsid or any other column
        ->where('cfa.statusflag', '=', 'Y') // Filter by 'statusflag' in callforrecords_auditee table
        ->where('map_callforrecord.catcode', '=',$catcode) // Filter by 'statusflag' in callforrecords_auditee table
        ->where('cfa.deptcode', '=',$deptcode) // Filter by 'statusflag' in callforrecords_auditee table
        ->get();*/

        $ifSubcategory = DB::table('audit.mst_auditeeins_category')
            ->where('catcode', '=', $catcode)
            ->value('if_subcategory');

        $query = DB::table('audit.map_allocation_objection as mao')
            ->join('audit.callforrecords_auditee as cfa', 'mao.mapcallforrecordsid', '=', 'cfa.callforrecordsid')
            ->join('audit.mst_auditeeins_category as aic', 'mao.catcode', '=', 'aic.catcode')
            ->orderBy('cfa.callforrecordsename', 'asc')
            ->where('cfa.statusflag', '=', 'Y')
            ->where('mao.catcode', '=', $catcode)
            ->select('cfa.callforrecordsid', 'cfa.callforrecordsename', 'cfa.callforrecordstname')
            ->distinct();

        if ($ifSubcategory === 'Y') {
            $query->join('audit.mst_auditeeins_subcategory as sub', 'mao.auditeeins_subcategoryid', '=', 'sub.auditeeins_subcategoryid')
                ->where('sub.auditeeins_subcategoryid', '=', $subcatcode);
        }

        $results = $query->get();




        return $results;
    }
}
