<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
// class UserModel extends Model
// {ṇ
//     use HasFactory;
// }

class MastersModel extends Model
{
    protected static $mapallocationobjection_new_table = BaseModel::MAPALLOCATIONOBJECTION_NEW_TABLE;
    protected static $userchargedetail_table = BaseModel::USERCHARGEDETAIL_TABLE;
    protected static $chargedetail_table = BaseModel::CHARGEDETAIL_TABLE;
    protected static $auditmode_table = BaseModel::AUDITMODE_TABLE;
    protected static $alteredinst_table = BaseModel::ALTEREDINST_TABLE;
    protected static $regionTable = BaseModel::REGION_TABLE;
    protected static $deptTable = BaseModel::DEPT_TABLE;
    protected static $distTable = BaseModel::DIST_Table;
    protected static $roletype = BaseModel::ROLETYPE_TABLE;
    protected static $stateTable = BaseModel::STATE_TABLE;
    protected static $designation = BaseModel::DESIGNATION_Table;
    protected static $auditquarter_table = BaseModel::AUDITQUARTER_TABLE;
    protected static $subcategory = BaseModel::SUBCATEGORY_TABLE;
    protected static $mainobjection = BaseModel::MAINOBJECTION_TABLE;
    protected static $category = BaseModel::MSTAUDITEEINSCATEGORY_TABLE;
    protected static $roletypemapping_table = BaseModel::ROLETYPEMAPPING_TABLE;
    protected static $department_table = BaseModel::DEPARTMENT_TABLE;
    protected static $region_table = BaseModel::REGION_TABLE;
    protected static $inst_table = BaseModel::INSTITUTION_TABLE;
    protected static $group_table = BaseModel::GROUP_TABLE;
    protected static $subobjection = BaseModel::SUBOBJ_TABLE;
    protected static $callforrecords = BaseModel::CALLFORRECORDS_AUDITEE_TABLE;
    protected static $mapinstdesig_table = BaseModel::MAPINST_TABLE;
    protected static $mapallocationobjection_table = BaseModel::MAPALLOCATIONOBJECTION_TABLE;
    protected static $audittype_table = BaseModel::TYPEOFAUDIT_TABLE;

    // ------------------------------------------Group------------------------------------------------------------------------

    protected static $group = BaseModel::GROUP_TABLE;

    // -------------------------------------------------auditee institution mapping------------------------------------------------------

    protected static $auditeinstmap = BaseModel::AUDITOR_INSTMAPPING_TABLE;

    // ---------------------MAP CALL FOR RECORDS-----------------------------------------------------------------------

    protected static $callforrecords_auditee = BaseModel::CALLFORRECORDS_AUDITEE_TABLE;
    protected static $mapcallforrecords = BaseModel::MAPCALLFORRECORDS_TABLE;
    protected static $auditeecategory = BaseModel::MSTAUDITEEINSCATEGORY_TABLE;
    // -----------------------WORK ALLOCATION -------------------------------------------------------------------------
    protected static $workallocation = BaseModel::MAJORWORKALLOCATION_TABLE;

    // ------------------------SUB WORK ALLOCATION---------------------------------------------------------------------

    protected static $subworkallocation = BaseModel::SUBWORKALLOCATION_TABLE;

    // -----------------------AUDITEE UDER DETAILS-------------------------------------------------------------------------

    protected static $institution = BaseModel::INSTITUTION_TABLE;
    protected static $auditeeuserdetail = BaseModel::AUDITEEUSERDETAIL_TABLE;
    protected static $auditeedept_table = BaseModel::AUDITEEDEPT_TABLE;
    protected static $auditeedeptreport_table = BaseModel::AUDITEEDEPTREPORT_TABLE;
    protected static $supercheck = BaseModel::supercheck_TABLE;

    protected $connection = 'pgsql';  // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon';  // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon';  // Custom column name for `updated_at` (if you have it)

    protected $table = 'audit.mst_designation';

    protected static $auditeescheme = BaseModel::AUDITEESCHEME_TABLE;

    protected static $auditeedepartment = BaseModel::AUDITEEDEPARTMENT_TABLE;

    protected static $irregularities = BaseModel::IRREGULARITIES_TABLE;

    protected static $irregularitiescategory = BaseModel::IRREGULARITIESCATEGORY_TABLE;

    protected static $irregularitiessubcategory = BaseModel::IRREGULARITIESSUBCATEGORY_TABLE;

    protected static $auditinspection_table = BaseModel::AUDITINSPECTION_TABLE;

    protected static $rolemapping_table = BaseModel::ROLEMAPPING_TABLE;

    // -------------------------------------Role type mapping-----------------------------------------------------------

    protected static $roletypemappingTable = BaseModel::ROLETYPEMAPPING_TABLE;

    // -----------------------------------------Audit Period-------------------------------------------------------

    protected static $auditperiod = BaseModel::AUDITPERIOD_TABLE;

    // --------------------------------------------------------------AUDIT dISTRICT-----------------------------------------------

    protected static $auditdistrict = BaseModel::AUDITDISTRICT_TABLE;

    protected static $revenuedistrict_table = BaseModel::REVENUEDISTRICT_TABLE;

    protected static $userdetail_table = BaseModel::USERDETAIL_TABLE;

    // ==========================================================================Common Department Fetch-==========================================================

    public static function commondeptfetch()
    {
        return DB::table(self::$deptTable . ' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname')  // Select required columns
            ->where('dept.statusflag', '=', 'Y')  // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }

    // --------------------------------------------------iregularities subcategory-------------------------------------------------------------------------

    public static function getcategoryByIrr($irregularitiescode)
    {
        return DB::table(self::$irregularitiescategory)
            // ->select()
            ->where('irregularitiescode', $irregularitiescode)
            ->where('statusflag', 'Y')
            ->get(['irregularitiescatcode', 'irregularitiescatelname', 'irregularitiescattlname']);
    }

    public static function Forirregularitiessubcat_insertupdate(array $data, $irregularitiessubcatid = null)
    {
        $table = self::$irregularitiessubcategory;

        try {
            $commonConditions = DB::table($table)
                ->where('irregularitiescatcode', $data['irregularitiescatcode'])
                ->where('statusflag', $data['statusflag']);

            $irregularitiessubcatelname = trim($data['irregularitiessubcatelname']);
            $irregularitiessubcattlname = trim($data['irregularitiessubcattlname']);

            $irregularsubcatename = strtolower(str_replace(' ', '', $irregularitiessubcatelname));
            $irregularsubcattlname = strtolower(str_replace(' ', '', $irregularitiessubcattlname));

            $irregularsubcatelnameExists = (clone $commonConditions)
                ->whereRaw("LOWER(REPLACE(CAST(irregularitiessubcatelname AS TEXT), ' ', '')) = ?", [$irregularsubcatename])
                ->when($irregularitiessubcatid, fn($query) => $query->where('irregularitiessubcatid', '<>', $irregularitiessubcatid))
                ->exists();

            $irregularsubcattlnameExists = (clone $commonConditions)
                ->whereRaw("LOWER(REPLACE(CAST(irregularitiessubcattlname AS TEXT), ' ', '')) = ?", [$irregularsubcattlname])
                ->when($irregularitiessubcatid, fn($query) => $query->where('irregularitiessubcatid', '<>', $irregularitiessubcatid))
                ->exists();

            if ($irregularsubcatelnameExists && $irregularsubcattlnameExists) {
                throw new \Exception('AllirredularsubcatExist');
            } elseif ($irregularsubcatelnameExists) {
                throw new \Exception('irregularsubcatelnameExists');
            } elseif ($irregularsubcattlnameExists) {
                throw new \Exception('irregularsubcattlnameExists');
            }

            if ($irregularitiessubcatid) {
                $affectedRows = DB::table($table)
                    ->where('irregularitiessubcatid', $irregularitiessubcatid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $irregularitiessubcatid;
            } else {
                $latestCode = DB::table($table)
                    ->whereNotNull('irregularitiessubcatcode')  // Ensure we're considering only non-null values
                    ->max(DB::raw('CAST(irregularitiessubcatcode AS INTEGER)'));

                $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

                $data['irregularitiessubcatcode'] = $newCode;

                $newRecordId = DB::table($table)->insertGetId($data, 'irregularitiessubcatid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function irregularitiessubcat_fetch($irregularitiessubcatid = null, $table = null)
    {
        $table = self::$irregularitiessubcategory;

        $query = DB::table($table . ' as irrsubcat')
            ->Join(self::$irregularitiescategory . ' as irrcat', 'irrcat.irregularitiescatcode', '=', 'irrsubcat.irregularitiescatcode')  // Ensure proper join condition
            ->join(self::$irregularities . '  as irr', 'irrcat.irregularitiescode', '=', 'irr.irregularitiescode')
            ->select(
                'irr.irregularitiescode',
                'irr.irregularitieselname',
                'irr.irregularitiestlname',
                'irrcat.irregularitiescatesname',
                'irrcat.irregularitiescatcode',
                'irrcat.irregularitiescatelname',
                'irrcat.irregularitiescattsname',
                'irrcat.irregularitiescattlname',
                'irrsubcat.irregularitiessubcatid',
                'irrsubcat.irregularitiessubcatelname',
                'irrsubcat.irregularitiessubcatesname',
                'irrsubcat.irregularitiessubcattsname',
                'irrsubcat.irregularitiessubcattlname',
                'irrsubcat.statusflag'
            )
            ->when($irregularitiessubcatid, function ($query) use ($irregularitiessubcatid) {
                $query->where('irrsubcat.irregularitiessubcatid', $irregularitiessubcatid);
            })
            ->orderBy('irrsubcat.updatedon', 'desc');

        return $query->get();  // Retrieve all results
    }

    // --------------------------------------------Irregularities Category---------------------------------------------------------------------------------

    public static function irregularitiesfetch()
    {
        return DB::table(self::$irregularities . ' as irr')
            ->select('irr.irregularitieselname', 'irr.irregularitiestlname', 'irr.irregularitiescode')
            ->where('irr.statusflag', '=', 'Y')
            ->orderBy('irr.irregularitieselname', 'asc')
            ->get();
    }

    public static function Forirregularitiescat_insertupdate(array $data, $irregularitiescatid = null)
    {
        $table = self::$irregularitiescategory;

        try {
            $commonConditions = DB::table($table)
                ->where('irregularitiescode', $data['irregularitiescode'])
                ->where('statusflag', $data['statusflag']);

            $irregularitiescatelname = trim($data['irregularitiescatelname']);
            $irregularitiescattlname = trim($data['irregularitiescattlname']);

            $irregularcatename = strtolower(str_replace(' ', '', $irregularitiescatelname));
            $irregularcattlname = strtolower(str_replace(' ', '', $irregularitiescattlname));

            $irregularcatelnameExists = (clone $commonConditions)
                ->whereRaw("LOWER(REPLACE(CAST(irregularitiescatelname AS TEXT), ' ', '')) = ?", [$irregularcatename])
                ->when($irregularitiescatid, fn($query) => $query->where('irregularitiescatid', '<>', $irregularitiescatid))
                ->exists();

            $irregularcattlnameExists = (clone $commonConditions)
                ->whereRaw("LOWER(REPLACE(CAST(irregularitiescattlname AS TEXT), ' ', '')) = ?", [$irregularcattlname])
                ->when($irregularitiescatid, fn($query) => $query->where('irregularitiescatid', '<>', $irregularitiescatid))
                ->exists();

            // Handle duplicate cases
            if ($irregularcatelnameExists && $irregularcattlnameExists) {
                throw new \Exception('AllirredularcatExist');
            } elseif ($irregularcatelnameExists) {
                throw new \Exception('irregularcatelnameExists');
            } elseif ($irregularcattlnameExists) {
                throw new \Exception('irregularcattlnameExists');
            }

            if ($irregularitiescatid) {
                $affectedRows = DB::table($table)
                    ->where('irregularitiescatid', $irregularitiescatid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $irregularitiescatid;
            } else {
                $latestCode = DB::table($table)
                    ->whereNotNull('irregularitiescatcode')  // Ensure we're considering only non-null values
                    ->max(DB::raw('CAST(irregularitiescatcode AS INTEGER)'));

                $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

                $data['irregularitiescatcode'] = $newCode;

                $newRecordId = DB::table($table)->insertGetId($data, 'irregularitiescatid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function irregularitiescat_fetch($irregularitiescatid = null, $table = null)
    {
        $table = self::$irregularitiescategory;

        $query = DB::table($table . ' as irrcat')
            ->Join(self::$irregularities . ' as irr', 'irr.irregularitiescode', '=', 'irrcat.irregularitiescode')  // Ensure proper join condition
            ->select(
                'irr.irregularitiescode',
                'irr.irregularitiesesname',
                'irr.irregularitieselname',
                'irr.irregularitiestsname',
                'irr.irregularitiestlname',
                'irrcat.irregularitiescatid',
                'irrcat.irregularitiescatesname',
                'irrcat.irregularitiescatelname',
                'irrcat.irregularitiescattsname',
                'irrcat.irregularitiescattlname',
                'irrcat.statusflag'
            )
            ->when($irregularitiescatid, function ($query) use ($irregularitiescatid) {
                $query->where('irrcat.irregularitiescatid', $irregularitiescatid);
            })
            ->orderBy('irrcat.updatedon', 'desc');

        return $query->get();  // Retrieve all results
    }

    // -----------------------------------------------------------Irregularities------------------------------------------------------------

    public static function Forirregularities_insertupdate(array $data, $irregularitiesid = null)
    {
        $table = self::$irregularities;

        try {
            $commonConditions = DB::table($table)
                // ->where('deptcode', $data['deptcode'])
                ->where('statusflag', $data['statusflag']);

            $irregularitieselname = trim($data['irregularitieselname']);
            $irregularitiestlname = trim($data['irregularitiestlname']);

            $irregularename = strtolower(str_replace(' ', '', $irregularitieselname));
            $irregulartlname = strtolower(str_replace(' ', '', $irregularitiestlname));

            $irregularelnameExists = (clone $commonConditions)
                ->whereRaw("LOWER(REPLACE(CAST(irregularitieselname AS TEXT), ' ', '')) = ?", [$irregularename])
                ->when($irregularitiesid, fn($query) => $query->where('irregularitiesid', '<>', $irregularitiesid))
                ->exists();

            $irregulartlnameExists = (clone $commonConditions)
                ->whereRaw("LOWER(REPLACE(CAST(irregularitiestlname AS TEXT), ' ', '')) = ?", [$irregulartlname])
                ->when($irregularitiesid, fn($query) => $query->where('irregularitiesid', '<>', $irregularitiesid))
                ->exists();

            // Handle duplicate cases
            if ($irregularelnameExists && $irregulartlnameExists) {
                throw new \Exception('AllirredularExist');
            } elseif ($irregularelnameExists) {
                throw new \Exception('irregularelnameExists');
            } elseif ($irregulartlnameExists) {
                throw new \Exception('irregulartlnameExists');
            }

            if ($irregularitiesid) {
                $affectedRows = DB::table($table)
                    ->where('irregularitiesid', $irregularitiesid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $irregularitiesid;
            } else {
                $latestCode = DB::table($table)
                    ->whereNotNull('irregularitiescode')  // Ensure we're considering only non-null values
                    ->max(DB::raw('CAST(irregularitiescode AS INTEGER)'));

                $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

                $data['irregularitiescode'] = $newCode;

                $newRecordId = DB::table($table)->insertGetId($data, 'irregularitiesid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function irregularities_fetch($irregularitiesid = null, $table = null)
    {
        $table = self::$irregularities;

        $query = DB::table($table . ' as aud')
            // ->Join(self::$deptTable . ' as dept', 'dept.deptcode', '=', 'aud.deptcode') // Ensure proper join condition
            ->select(
                // 'dept.deptcode',
                // 'dept.deptesname',
                // 'dept.deptelname',
                // 'dept.depttsname',
                // 'dept.depttlname',
                'aud.irregularitiesid',
                'aud.irregularitiesesname',
                'aud.irregularitieselname',
                'aud.irregularitiestsname',
                'aud.irregularitiestlname',
                'aud.statusflag'
            )
            ->when($irregularitiesid, function ($query) use ($irregularitiesid) {
                $query->where('aud.irregularitiesid', $irregularitiesid);
            })
            ->orderBy('aud.updatedon', 'desc');

        return $query->get();  // Retrieve all results
    }

    // -----------------------------------------------------------------Auditee department-------------------------------------------------------------

    public static function Forauditeedept_insertupdate(array $data, $auditeedeptid = null)
    {
        $table = self::$auditeedepartment;

        try {
            $commonConditions = DB::table($table)
                ->where('deptcode', $data['deptcode'])
                ->where('statusflag', $data['statusflag']);

            $auditeedeptename = trim($data['auditeedeptename']);
            $auditeedepttname = trim($data['auditeedepttname']);

            $auditeeesname = strtolower(str_replace(' ', '', $auditeedeptename));
            $auditeetlname = strtolower(str_replace(' ', '', $auditeedepttname));

            $auditeeengnameExists = (clone $commonConditions)
                ->whereRaw("LOWER(REPLACE(CAST(auditeedeptename AS TEXT), ' ', '')) = ?", [$auditeeesname])
                ->when($auditeedeptid, fn($query) => $query->where('auditeedeptid', '<>', $auditeedeptid))
                ->exists();

            $auditeetamnameExists = (clone $commonConditions)
                ->whereRaw("LOWER(REPLACE(CAST(auditeedepttname AS TEXT), ' ', '')) = ?", [$auditeetlname])
                ->when($auditeedeptid, fn($query) => $query->where('auditeedeptid', '<>', $auditeedeptid))
                ->exists();

            // Handle duplicate cases
            if ($auditeeengnameExists && $auditeetamnameExists) {
                throw new \Exception('AllNamesExist');
            } elseif ($auditeeengnameExists) {
                throw new \Exception('auditeeengnameExists');
            } elseif ($auditeetamnameExists) {
                throw new \Exception('auditeetamnameExists');
            }

            if ($auditeedeptid) {
                $affectedRows = DB::table($table)
                    ->where('auditeedeptid', $auditeedeptid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $auditeedeptid;
            } else {
                $latestCode = DB::table($table)
                    ->whereNotNull('auditeedeptcode')  // Ensure we're considering only non-null values
                    ->max(DB::raw('CAST(auditeedeptcode AS INTEGER)'));

                $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

                $data['auditeedeptcode'] = $newCode;

                $newRecordId = DB::table($table)->insertGetId($data, 'auditeedeptid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function auditeedepartment_fetch($auditeedeptid = null, $table = null)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;
        $table = self::$auditeedepartment;

        $query = DB::table($table . ' as aud')  // Table passed as $table, alias as 'des'
            ->Join(self::$deptTable . ' as dept', 'dept.deptcode', '=', 'aud.deptcode')  // Ensure proper join condition
            ->select(
                'dept.deptcode',
                'dept.deptesname',
                'dept.deptelname',
                'dept.depttsname',
                'dept.depttlname',
                'aud.*',
		DB::raw("
                CASE

                    WHEN EXISTS (
                        SELECT 1
                        FROM audit.auditee_dept ad
                        WHERE ad.auditeedepartment = aud.auditeedeptcode
                        AND ad.statusflag = 'Y'
                    )

                    THEN 'Y'

                    ELSE 'N'

                END as isexists
            ")

            )
            ->when($auditeedeptid, function ($query) use ($auditeedeptid) {
                $query->where('aud.auditeedeptid', $auditeedeptid);
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
                $query->where('dept.deptcode', '=', $sessiondeptcode);
            });
        $query->orderBy('aud.updatedon', 'desc');

        return $query->get();  // Retrieve all results
    }

    // -----------------------------------------------------------------------------------------------scheme-------------------------------------------------------

    public static function Forscheme_insertupdate(array $data, $auditeeschemeid = null)
    {
        $table = self::$auditeescheme;

        try {
            $commonConditions = DB::table($table)
                ->where('deptcode', $data['deptcode'])
                ->where('catcode', $data['catcode'])
                ->where('auditeeins_subcategoryid', $data['auditeeins_subcategoryid'])
                ->where('statusflag', $data['statusflag']);

            $auditeeschemeelname = trim($data['auditeeschemeelname']);
            $auditeeschemetlname = trim($data['auditeeschemetlname']);

            $schemesname = strtolower(str_replace(' ', '', $auditeeschemeelname));
            $schemetlname = strtolower(str_replace(' ', '', $auditeeschemetlname));

            $schemeengnameExists = (clone $commonConditions)
                ->whereRaw("LOWER(REPLACE(CAST(auditeeschemeelname AS TEXT), ' ', '')) = ?", [$schemesname])
                ->when($auditeeschemeid, fn($query) => $query->where('auditeeschemeid', '<>', $auditeeschemeid))
                ->exists();

            $schemetamnameExists = (clone $commonConditions)
                ->whereRaw("LOWER(REPLACE(CAST(auditeeschemetlname AS TEXT), ' ', '')) = ?", [$schemetlname])
                ->when($auditeeschemeid, fn($query) => $query->where('auditeeschemeid', '<>', $auditeeschemeid))
                ->exists();

            // Handle duplicate cases
            if ($schemeengnameExists && $schemetamnameExists) {
                throw new \Exception('AllNamesExist');
            } elseif ($schemeengnameExists) {
                throw new \Exception('schemeengnameExists');
            } elseif ($schemetamnameExists) {
                throw new \Exception('schemetamnameExists');
            }

            if ($auditeeschemeid) {
                $affectedRows = DB::table($table)
                    ->where('auditeeschemeid', $auditeeschemeid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $auditeeschemeid;
            } else {
                $latestCode = DB::table($table)
                    ->whereNotNull('auditeeschemecode')  // Ensure we're considering only non-null values
                    ->max(DB::raw('CAST(auditeeschemecode AS INTEGER)'));

                $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

                $data['auditeeschemecode'] = $newCode;

                $newRecordId = DB::table($table)->insertGetId($data, 'auditeeschemeid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function scheme_fetch($auditeeschemeid = null)
{
    $sessiondet = session('charge');
    $sessiondeptcode = $sessiondet->deptcode;

    $table = self::$auditeescheme;

    $query = DB::table($table . ' as aud')

        ->join(
            self::$deptTable . ' as dept',
            'aud.deptcode',
            '=',
            'dept.deptcode'
        )

        ->join(
            self::$auditeecategory . ' as cat',
            'aud.catcode',
            '=',
            'cat.catcode'
        )

        ->leftJoin(
            self::$subcategory . ' as sub',
            'aud.auditeeins_subcategoryid',
            '=',
            'sub.auditeeins_subcategoryid'
        )

        ->leftJoin(
            DB::raw("
                (
                    SELECT DISTINCT auditeeschemecode
                    FROM audit.trans_auditslip
                    WHERE statusflag = 'Y'
                ) ai
            "),
            'ai.auditeeschemecode',
            '=',
            'aud.auditeeschemecode'
        )

        ->select(
            'aud.auditeeschemeid',
            'aud.auditeeschemeesname',
            'aud.auditeeschemeelname',
            'aud.auditeeschemetsname',
            'aud.auditeeschemetlname',
            'aud.statusflag',
            'aud.auditeeins_subcategoryid',

            'cat.catcode',
            'cat.catename as catengname',
            'cat.cattname as cattamname',
            'cat.if_subcategory as subcategory',

            'sub.subcatename',
            'sub.subcattname',
            'sub.auditeeins_subcategoryid',

            'dept.deptcode',
            'dept.deptesname as deptengsname',
            'dept.deptelname',
            'dept.depttsname as depttamsname',
            'dept.depttlname',

            DB::raw("
                CASE
                    WHEN cat.if_subcategory = 'N'
                      OR cat.if_subcategory IS NULL
                    THEN cat.catename
                    ELSE sub.subcatename
                END AS subcategory_ename
            "),

            DB::raw("
                CASE
                    WHEN cat.if_subcategory = 'N'
                      OR cat.if_subcategory IS NULL
                    THEN cat.cattname
                    ELSE sub.subcattname
                END AS subcategory_tname
            "),

            DB::raw("
                CASE
                    WHEN ai.auditeeschemecode IS NOT NULL
                    THEN 'Y'
                    ELSE 'N'
                END as isexists
            ")
        )

        ->when($auditeeschemeid, function ($query) use ($auditeeschemeid) {
            $query->where('aud.auditeeschemeid', $auditeeschemeid);
        })

        ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
            $query->where('dept.deptcode', $sessiondeptcode);
        })

        ->orderBy('aud.auditeeschemeid', 'desc');

    return $query->get();
}
    // ----------------------------------------------------------Super Check--------------------------------------------------------------------------

    public static function supercheck_multiinsert(array $data, $supercheckid = null)
    {
        $table = self::$supercheck;

        try {
            $subcats = !empty($data['subcatcode']) ? $data['subcatcode'] : [null];

            $heading_en = trim($data['heading_en']);
            $heading_ta = trim($data['heading_ta']);
            $checkHEsname = strtolower(str_replace(' ', '', trim($data['heading_en'])));
            $checkHTname = strtolower(str_replace(' ', '', trim($data['heading_ta'])));

            foreach ($subcats as $subcat) {
                for ($i = 0; $i < count($data['question_type']); $i++) {
                    $checkpoint_en = trim($data['checkpoint_en'][$i]);
                    $checkpoint_ta = trim($data['checkpoint_ta'][$i]);

                    $checkCEname = strtolower(str_replace(' ', '', $checkpoint_en));
                    $checkCTname = strtolower(str_replace(' ', '', $checkpoint_ta));

                    $exists = DB::table($table)
                        ->where('deptcode', $data['deptcode'])
                        ->where('catcode', $data['catcode'])
                        ->where('subcatcode', $subcat)
                        ->where('part_no', $data['part_no'])
                        ->where('question_type', $data['question_type'][$i])
                        ->where('statusflag', $data['statusflag'])
                        ->when($supercheckid, fn($query) => $query->where('supercheckid', '<>', $supercheckid))
                        ->whereRaw("LOWER(REPLACE(CAST(heading_en AS TEXT), ' ', '')) = ?", [$checkHEsname])
                        ->whereRaw("LOWER(REPLACE(CAST(heading_ta AS TEXT), ' ', '')) = ?", [$checkHTname])
                        ->whereRaw("LOWER(REPLACE(CAST(checkpoint_en AS TEXT), ' ', '')) = ?", [$checkCEname])
                        ->whereRaw("LOWER(REPLACE(CAST(checkpoint_ta AS TEXT), ' ', '')) = ?", [$checkCTname])
                        ->exists();

                    if ($exists) {
                        throw new \Exception('Heading and Checkpoint already exist');
                    }
                }
            }

            foreach ($subcats as $subcat) {
                for ($i = 0; $i < count($data['question_type']); $i++) {
                    DB::table($table)->insert([
                        'sl_no' => $data['sl_no'][$i],
                        'deptcode' => $data['deptcode'],
                        'catcode' => $data['catcode'],
                        'subcatcode' => $subcat,
                        'heading_en' => $data['heading_en'],
                        'heading_ta' => $data['heading_ta'],
                        'part_no' => $data['part_no'],
                        'checkpoint_en' => $data['checkpoint_en'][$i],
                        'checkpoint_ta' => $data['checkpoint_ta'][$i],
                        'question_type' => $data['question_type'][$i],
                        'statusflag' => $data['statusflag'],
                    ]);
                }
            }

            return;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function Forsupercheck_insertupdate(array $data, $supercheckid = null)
    {
        $table = self::$supercheck;

        try {
            $heading_en = trim($data['heading_en']);
            $heading_ta = trim($data['heading_ta']);
            $checkpoint_en = trim($data['checkpoint_en']);
            $checkpoint_ta = trim($data['checkpoint_ta']);

            // Normalize for space-insensitive and case-insensitive comparison
            $checkHEsname = strtolower(str_replace(' ', '', $heading_en));
            $checkHTname = strtolower(str_replace(' ', '', $heading_ta));
            $checkCEname = strtolower(str_replace(' ', '', $checkpoint_en));
            $checkCTname = strtolower(str_replace(' ', '', $checkpoint_ta));

            // Build the full condition
            $exists = DB::table($table)
                ->where('deptcode', $data['deptcode'])
                ->where('catcode', $data['catcode'])
                ->where('subcatcode', $data['subcatcode'])
                ->where('part_no', $data['part_no'])
                // ->where('question_type', $data['question_type'])
                // ->where('statusflag', $data['statusflag'])
                ->whereRaw("LOWER(REPLACE(CAST(heading_en AS TEXT), ' ', '')) = ?", [$checkHEsname])
                ->whereRaw("LOWER(REPLACE(CAST(heading_ta AS TEXT), ' ', '')) = ?", [$checkHTname])
                ->whereRaw("LOWER(REPLACE(CAST(checkpoint_en AS TEXT), ' ', '')) = ?", [$checkCEname])
                ->whereRaw("LOWER(REPLACE(CAST(checkpoint_ta AS TEXT), ' ', '')) = ?", [$checkCTname])
                ->when($supercheckid, fn($query) => $query->where('supercheckid', '<>', $supercheckid))
                ->exists();

            if ($exists) {
                throw new \Exception('CEnameExists');
            }
            if ($supercheckid) {
                $affectedRows = DB::table($table)
                    ->where('supercheckid', $supercheckid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $supercheckid;
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'supercheckid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getcategoryByDeptsupercheck($deptcode)
    {
        return DB::table(self::$auditeecategory)
            // ->select()
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->get(['catcode', 'catename', 'cattname', 'if_subcategory']);
    }

    public static function getSubcategoryByCategoryforsupercheck($category)
    {
        $table = self::$auditeecategory;

        return DB::table($table . ' as aud')
            ->join(self::$subcategory . ' as sub', 'aud.catcode', '=', 'sub.catcode')
            ->select('sub.subcatename', 'sub.subcattname', 'sub.auditeeins_subcategoryid', 'aud.if_subcategory', 'aud.catcode', 'aud.catename', 'aud.if_subcategory', 'aud.cattname')
            ->where('sub.catcode', $category)
            ->where('aud.if_subcategory', 'Y')
            ->orderBy('sub.subcatename', 'Asc')
            //  dd($date);
            ->get();
    }

    public static function supercheck_fetch($supercheckid = null, $table = null)
    {
        $table = self::$supercheck;

        $query = DB::table($table . ' as aud')
            ->Join(self::$deptTable . ' as dept', 'aud.deptcode', '=', 'dept.deptcode')
            ->join(self::$auditeecategory . ' as cat', 'aud.catcode', '=', 'cat.catcode')
            ->leftjoin(self::$subcategory . ' as sub', 'aud.subcatcode', '=', 'sub.auditeeins_subcategoryid')
            ->select(
                'aud.heading_en',
                'aud.heading_ta',
                'aud.part_no',
                'aud.sl_no',
                'aud.checkpoint_en',
                'aud.checkpoint_ta',
                'aud.question_type',
                'aud.statusflag',
                'aud.supercheckid',
                'aud.subcatcode',
                'cat.catcode',
                'cat.catename as catengname',
                'cat.cattname as cattamname',
                'cat.if_subcategory as subcategory',
                'sub.subcatename',
                'sub.subcattname',
                'sub.auditeeins_subcategoryid',
                'dept.deptcode',
                'dept.deptesname as deptengsname',
                'dept.deptelname',
                'dept.depttsname as depttamsname',
                'dept.depttlname',
                DB::raw("CASE WHEN cat.if_subcategory = 'N' OR   cat.if_subcategory IS NULL THEN cat.catename ELSE sub.subcatename END AS subcategory_ename"),
                DB::raw("CASE WHEN cat.if_subcategory = 'N' OR cat.if_subcategory IS NULL THEN cat.cattname ELSE sub.subcattname END AS subcategory_tname")
            )
            ->when($supercheckid, function ($query) use ($supercheckid) {
                $query->where('aud.supercheckid', $supercheckid);
            })
            ->orderBy('aud.supercheckid', 'desc');

        //  dd($query->toSql(), $query->getBindings());

        return $query->get();  // Retrieve all results
    }

    // -----------------------------------------------------------Role Action--------------------------------------------------------------------------------

    protected static $roleaction = BaseModel::ROLEACTION_TABLE;

    public static function roleaction_insertupdate(array $data, $roleactionid = null, $table = null)
    {
        $table = self::$roleaction;

        try {
            // $data['auditquarter'] = strtolower(str_replace(' ', '', trim($data['auditquarter'])));

            // $query = DB::table($table)
            //     ->where('deptcode', $data['deptcode']);

            // $auditquarterExists = $query->clone()
            //     ->whereRaw("LOWER(REPLACE(auditquarter, ' ', '')) = ?", [$data['auditquarter']])
            //     ->when($roleactionid, function ($q) use ($roleactionid) {
            //         return $q->where('roleactionid', '<>', $roleactionid);
            //     })
            //     ->exists();

            // // Throw errors based on duplication checks
            // if ($auditquarterExists ) {
            //     throw new \Exception('auditquarterExists');
            // }

            if ($roleactionid) {
                $affectedRows = DB::table($table)
                    ->where('roleactionid', $roleactionid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $roleactionid;
            } else {
                $latestCode = DB::table($table)
                    ->whereNotNull('roleactioncode')  // Ensure we're considering only non-null values
                    ->max(DB::raw('CAST(roleactioncode AS INTEGER)'));

                $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

                $data['roleactioncode'] = $newCode;

                $newRecordId = DB::table($table)->insertGetId($data, 'roleactionid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function roleaction_fetch($roleactionid = null, $table = null)
    {
        $table = self::$roleaction;

        $query = DB::table($table . ' as aud')
            ->select(
                'aud.*',
            )
            ->when($roleactionid, function ($query) use ($roleactionid) {
                $query->where('aud.roleactionid', $roleactionid);
            })
            ->orderBy('aud.roleactionid', 'desc');

        return $query->get();  // Retrieve all results
    }

    // ------------------------------------------------------------Audit Quarter-----------------------------------------------------------------------------

    protected static $auditquarter = BaseModel::AUDITQUARTER_TABLE;

    public static function auditquarter_insertupdate(array $data, $auditquarterid = null, $table = null)
    {
        $table = self::$auditquarter;

        try {
            $data['auditquarter'] = strtolower(str_replace(' ', '', trim($data['auditquarter'])));

            $query = DB::table($table)
                ->where('deptcode', $data['deptcode']);

            $auditquarterExists = $query
                ->clone()
                ->whereRaw("LOWER(REPLACE(auditquarter, ' ', '')) = ?", [$data['auditquarter']])
                ->when($auditquarterid, function ($q) use ($auditquarterid) {
                    return $q->where('auditquarterid', '<>', $auditquarterid);
                })
                ->exists();

            // Throw errors based on duplication checks
            if ($auditquarterExists) {
                throw new \Exception('auditquarterExists');
            }

            if ($auditquarterid) {
                $affectedRows = DB::table($table)
                    ->where('auditquarterid', $auditquarterid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $auditquarterid;
            } else {
                $latestCode = DB::table($table)
                    ->whereNotNull('auditquartercode')  // Ensure we're considering only non-null values
                    ->max(DB::raw('CAST(auditquartercode AS INTEGER)'));

                $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

                $data['auditquartercode'] = $newCode;

                $newRecordId = DB::table($table)->insertGetId($data, 'auditquarterid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function audit_fetch($auditquarterid = null, $table = null)
    {
        $query = DB::table($table . ' as aud')  // Table passed as $table, alias as 'des'
            ->Join(self::$deptTable . ' as dept', 'dept.deptcode', '=', 'aud.deptcode')  // Ensure proper join condition
            ->select(
                'dept.deptcode',
                'dept.deptesname',
                'dept.deptelname',
                'dept.depttsname',
                'dept.depttlname',
                'aud.*',
            )
            ->when($auditquarterid, function ($query) use ($auditquarterid) {
                $query->where('aud.auditquarterid', $auditquarterid);
            })
            ->orderBy('aud.auditquarterid', 'desc');

        return $query->get();  // Retrieve all results
    }

    // -----------------------------------------------------Account PArticulars-----------------------------------------------------------
    protected static $accountparticulars = BaseModel::ACCOUNTPARTICULARS_TABLE;

    public static function accountparticulardatafetch($accountparticularsid = null, $table = null)
    {
        $table = self::$accountparticulars;

        $query = DB::table($table . ' as aud')  // Table passed as $table, alias as 'des'
            ->Join(self::$deptTable . ' as dept', 'dept.deptcode', '=', 'aud.deptcode')  // Ensure proper join condition
            ->join(self::$category . ' as cat', 'cat.catcode', '=', 'aud.catcode')
            ->leftjoin(self::$subcategory . ' as sub', 'aud.auditeeins_subcategoryid', '=', 'sub.auditeeins_subcategoryid')
            ->select(
                'dept.deptcode',
                'dept.deptesname',
                'dept.deptelname',
                'dept.depttsname',
                'dept.depttlname',
                'cat.catcode',
                'cat.catename',
                'cat.cattname',
                'aud.catcode',
                'sub.auditeeins_subcategoryid',
                'sub.subcatename',
                'sub.subcattname',
                //    'aud.accountparticularsid',
                'aud.accountparticularsename',
                'aud.accountparticularstname',
                'aud.*',
            )
            ->when($accountparticularsid, function ($query) use ($accountparticularsid) {
                $query->where('aud.accountparticularsid', $accountparticularsid);
            })
            ->orderBy('aud.accountparticularsid', 'desc');
        return $query->get();  // Retrieve all results
    }

    public static function Modaldeptfetch()
    {
        return DB::table(self::$deptTable . ' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttsname', 'dept.depttlname')  // Select required columns
            ->where('dept.statusflag', '=', 'Y')  // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            // dd($query->toSql());
            ->get();
    }

    public static function accountparticulars_insertupdate(array $data, $accountparticularsid = null, $table = null)
    {
        $table = self::$accountparticulars;

        try {
            $data['accountparticularsename'] = strtolower(str_replace(' ', '', trim($data['accountparticularsename'])));
            $data['accountparticularstname'] = strtolower(str_replace(' ', '', trim($data['accountparticularstname'])));

            $query = DB::table($table)
                ->where('deptcode', $data['deptcode'])
                ->where('catcode', $data['catcode'])
                ->where('auditeeins_subcategoryid', $data['auditeeins_subcategoryid']);

            $enameExists = $query
                ->clone()
                ->whereRaw("LOWER(REPLACE(accountparticularsename, ' ', '')) = ?", [$data['accountparticularsename']])
                ->when($accountparticularsid, function ($q) use ($accountparticularsid) {
                    return $q->where('accountparticularsid', '<>', $accountparticularsid);
                })
                ->exists();

            $tnameExists = $query
                ->clone()
                ->whereRaw("LOWER(REPLACE(accountparticularstname, ' ', '')) = ?", [$data['accountparticularstname']])
                ->when($accountparticularsid, function ($q) use ($accountparticularsid) {
                    return $q->where('accountparticularsid', '<>', $accountparticularsid);
                })
                ->exists();

            // Throw errors based on duplication checks
            if ($enameExists && $tnameExists) {
                throw new \Exception('AccountParticularsETnameExist');
            } elseif ($enameExists) {
                throw new \Exception('AccountParticularsEnameExist');
            } elseif ($tnameExists) {
                throw new \Exception('AccountParticularsTnameExist');
            }

            if ($accountparticularsid) {
                $affectedRows = DB::table($table)
                    ->where('accountparticularsid', $accountparticularsid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $accountparticularsid;
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'accountparticularsid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getsubCategoriesBasedOncategory($catcode)
    {
        return DB::table(self::$subcategory . ' as sub')
            ->join(self::$category . ' as cat', 'cat.catcode', '=', 'sub.catcode')
            ->select('sub.auditeeins_subcategoryid', 'sub.subcattname', 'sub.subcatename')
            ->where('sub.statusflag', 'Y')
            ->where('sub.catcode', $catcode)
            ->orderBy('sub.auditeeins_subcategoryid', 'desc')
            ->get();
    }

    // --------------------------------------------------Role Type Mapping-------------------------------------------------------------------

    public static function Roletypefetchdata()
    {
        return DB::table(self::$roletype . ' as role')
            ->select('role.roletypeelname', 'role.roletypetlname', 'role.roletypecode')
            ->where('role.statusflag', '=', 'Y')
            ->orderBy('role.roletypeelname', 'asc')
            ->get();
    }

    public static function rolemapping_insertupdate(array $data, $roletypemappingid = null, $table = null)
    {
        $table = self::$roletypemappingTable;

        try {
            if ($roletypemappingid) {
                $affectedRows = DB::table($table)
                    ->where('roletypemappingid', $roletypemappingid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $roletypemappingid;
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'roletypemappingid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getAllRoletypemappingmapping($roletypemappingid = null, $table = null)
    {
        $query = DB::table($table . ' as aud')  // Table passed as $table, alias as 'des'
            ->Join(self::$roletype . ' as role', 'aud.roletypecode', '=', 'role.roletypecode')
            ->Join(self::$deptTable . ' as dept', 'aud.deptcode', '=', 'dept.deptcode')  // Ensure proper join condition
            ->select(
                'role.roletypecode',
                'role.roletypetsname',
                'role.roletypetlname',
                'role.roletypeelname',
                'dept.deptcode',
                'dept.deptesname',
                'dept.deptelname',
                'dept.depttsname',
                'dept.depttlname',
                'aud.*',
                // catename from mst_auditeeins_category
            )
            ->when($roletypemappingid, function ($query) use ($roletypemappingid) {
                $query->where('aud.roletypemappingid', $roletypemappingid);
            })
            ->orderBy('aud.roletypemappingid', 'desc');

        return $query->get();  // Retrieve all results
    }

    // -----------------------------------------------------District--------------------------------------------------------------------------
    public static function fetchdistrictData($distid = null, $table = null)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;

        $table = self::$distTable;

        $query = DB::table($table . ' as des')
            ->select(
                'des.*',
            )
            ->when($distid, function ($query) use ($distid) {
                $query->where('des.distid', $distid);
            });

        $query->orderBy('des.distid', 'desc');

        return $query->get();
    }

    public static function statefetch()
    {
        return DB::table(self::$stateTable . ' as dis')
            ->select('dis.statecode', 'dis.stateename')
            ->where('dis.statusflag', '=', 'Y')
            ->orderBy('dis.stateename', 'asc')
            ->get();
    }

    public static function district_insertupdate(array $data, $distid = null, $table = null)
    {
        $table = self::$distTable;

        try {
            $distename = trim($data['distename']);
            $distcode = trim($data['distcode']);

            $stateCode = 33;  // Assuming 33 represents TN (Tamil Nadu)

            // Prepare for duplicate check (normalize by removing spaces and converting to lowercase)
            $checkDistename = strtolower(str_replace(' ', '', $distename));
            $checkDistcode = strtolower(str_replace(' ', '', $distcode));

            $enameExists = DB::table($table)
                ->whereRaw("LOWER(REPLACE(distename, ' ', '')) = ?", [$checkDistename])
                ->where('statecode', $stateCode)
                ->when($distid, function ($query) use ($distid) {
                    return $query->where('distid', '<>', $distid);
                })
                ->exists();

            $distcodeExists = DB::table($table)
                ->where('distcode', $data['distcode'])
                ->where('statecode', $stateCode)  // Ensure same state
                ->when($distid, function ($query) use ($distid) {
                    return $query->where('distid', '<>', $distid);
                })
                ->exists();

            // Handle duplicate cases
            if ($enameExists && $distcodeExists) {
                throw new \Exception('DistEDnameExist');
            } elseif ($enameExists) {
                throw new \Exception('DistEnameExist');
            } elseif ($distcodeExists) {
                throw new \Exception('DistcodeExists');
            }

            if ($distid) {
                $affectedRows = DB::table($table)
                    ->where('distid', $distid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $distid;
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'distid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // /----------------------------------------------------Department----------------------------------------------------------------------
    // public static function deptcodeinsert()
    // {
    //     DB::beginTransaction();

    //     try {
    //         $latestCode = DB::table(self::$auditdistrict)
    //             ->whereNotNull('deptcode') // Ensure we're considering only non-null values
    //             ->max(DB::raw('CAST(deptcode AS INTEGER)'));

    //         $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

    //         DB::commit();

    //         return $newCode;
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw $e;
    //     }
    // }

    public static function department_insertupdate(array $data, $deptid = null, $table = null)
    {
        $table = self::$deptTable;

        try {
            if ($deptid) {
                $affectedRows = DB::table($table)
                    ->where('deptid', $deptid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $deptid;
            } else {
                $latestCode = DB::table($table)
                    ->whereNotNull('deptcode')  // Ensure we're considering only non-null values
                    ->max(DB::raw('CAST(deptcode AS INTEGER)'));

                $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

                $data['deptcode'] = $newCode;

                $newRecordId = DB::table($table)->insertGetId($data, 'deptid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function fetchdepartmentData($deptid = null, $table = null)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;

        $table = self::$deptTable;

        $query = DB::table($table . ' as des')
            ->select(
                'des.*',
            )
            ->when($deptid, function ($query) use ($deptid) {
                $query->where('des.deptid', $deptid);
            });

        $query->orderBy('des.deptid', 'desc');

        return $query->get();
    }

    // ----------------------------------------------------Audit District-------------------------------------------------------------------------

    public static function auditdistcodeinsert()
    {
        DB::beginTransaction();

        try {
            $latestCode = DB::table(self::$auditdistrict)
                ->whereNotNull('auditdistcode')  // Ensure we're considering only non-null values
                ->max(DB::raw('CAST(auditdistcode AS INTEGER)'));

            $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

            DB::commit();

            return $newCode;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function fetchauditdistrictData($auditdistid = null, $table = null)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;

        $table = self::$auditdistrict;

        $query = DB::table($table . ' as des')
            ->join(self::$deptTable . ' as d', 'd.deptcode', '=', 'des.auditdeptcode')
            ->select(
                'des.*',
                'd.deptcode',
                'd.deptelname',
                'd.depttlname',
                'd.deptesname',
                'd.depttsname',
                'des.auditdeptcode'
            )
            ->when($auditdistid, function ($query) use ($auditdistid) {
                $query->where('des.auditdistid', $auditdistid);
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
                $query->where('d.deptcode', '=', $sessiondeptcode);
            });
        $query->orderBy('des.updatedon', 'desc');

        return $query->get();
    }

    public static function auditdistrict_insertupdate(array $data, $auditdistid = null, $table = null)
    {
        $table = self::$auditdistrict;

        try {
            $data['auditdistename'] = strtolower(str_replace(' ', '', trim($data['auditdistename'])));
            $data['auditdisttname'] = strtolower(str_replace(' ', '', trim($data['auditdisttname'])));

            $enameExists = DB::table($table)
                ->where('auditdeptcode', $data['auditdeptcode'])
                ->whereRaw("LOWER(REPLACE(auditdistename, ' ', '')) = ?", [$data['auditdistename']])
                ->when($auditdistid, function ($query) use ($auditdistid) {
                    return $query->where('auditdistid', '<>', $auditdistid);
                })
                ->exists();

            $tnameExists = DB::table($table)
                ->where('auditdeptcode', $data['auditdeptcode'])
                ->whereRaw("LOWER(REPLACE(auditdisttname, ' ', '')) = ?", [$data['auditdisttname']])
                ->when($auditdistid, function ($query) use ($auditdistid) {
                    return $query->where('auditdistid', '<>', $auditdistid);
                })
                ->exists();

            if ($enameExists && $tnameExists) {
                throw new \Exception('AuditdistETnameExist');
            } elseif ($enameExists) {
                throw new \Exception('AuditdistEnameExist');
            } elseif ($tnameExists) {
                throw new \Exception('AuditdistTnameExist');
            }

            if ($auditdistid) {
                $affectedRows = DB::table($table)
                    ->where('auditdistid', $auditdistid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $auditdistid;
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'auditdistid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // -------------------------------------------------------Audit Period-----------------------------------------

    public static function createauditperiod_insertupdate(array $data, $auditperiodid = null, $table = null)
    {
        $table = self::$auditperiod;

        try {
            $query = DB::table($table);
            if ($auditperiodid) {
                $query->where('auditperiodid', '<>', $auditperiodid);
            }
            if ($auditperiodid) {
                $affectedRows = DB::table($table)->where('auditperiodid', $auditperiodid)->update($data);
                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'auditperiodid');
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getAllauditperiodData($auditperiodid = null, $table = null)
    {
        $table = self::$auditperiod;
        // $table1 = self::$workallocation;

        $query = DB::table($table . ' as des')
            ->select('des.*')
            ->when($auditperiodid, function ($query) use ($auditperiodid) {
                $query->where('des.auditperiodid', $auditperiodid);  // Filter by subworkallocationtypeid if passed
            })
            ->orderBy('des.auditperiodid', 'desc');  // Order by subworkallocationtypeid in descending order
        // dd($query->tosql());
        return $query->get();  // Retrieve all results
    }

    // --------------------------------------------------------group form-------------------------------------------

    public static function model_groupdeptfetch()
    {
        return DB::table(self::$deptTable . ' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname')  // Select required columns
            ->where('dept.statusflag', '=', 'Y')  // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }

    public static function checkExistingGroup($ename, $statusflag, $fname, $deptcode, $groupid = null)
    {
        $query = DB::table(self::$group)->where('deptcode', $deptcode);

        $englishExists = $query
            ->whereRaw("LOWER(REPLACE(groupename, ' ', '')) = ?", [$ename])
            ->where('statusflag', $statusflag)
            ->when($groupid, function ($query) use ($groupid) {
                return $query->where('groupid', '<>', $groupid);
            })
            ->exists();

        $tamilExists = DB::table(self::$group)
            ->where('statusflag', $statusflag)
            ->where('deptcode', $deptcode)
            ->whereRaw("LOWER(REPLACE(grouptname, ' ', '')) = ?", [$fname])
            ->when($groupid, function ($query) use ($groupid) {
                return $query->where('groupid', '<>', $groupid);
            })
            ->exists();

        return [
            'englishExists' => $englishExists,
            'tamilExists' => $tamilExists
        ];
    }

    public static function group_insertupdate(array $data, $groupid = null, $table = null)
    {
        $table = self::$group;

        try {
            $query = DB::table($table);
            if ($groupid) {
                $query->where('groupid', '<>', $groupid);
            }
            if ($groupid) {
                $affectedRows = DB::table($table)->where('groupid', $groupid)->update($data);
                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'groupid');
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function fetchgroupData($groupid = null, $table = null)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;

        $table = self::$group;

        $query = DB::table($table . ' as des')
            ->join(self::$deptTable . ' as d', 'd.deptcode', '=', 'des.deptcode')
            ->select(
                'des.*',
                'd.deptcode',
                'd.deptelname',
                'd.depttlname',
                'd.deptesname',
                'd.depttsname',
                'des.deptcode',
		DB::raw("
                CASE

                    WHEN EXISTS (
                        SELECT 1
                        FROM audit.map_allocation_objection mao
                        WHERE mao.groupid = des.groupid
                        AND mao.statusflag = 'Y'
                    )

                    THEN 'Y'

                    ELSE 'N'

                END as isexists
            ")

            )
            ->when($groupid, function ($query) use ($groupid) {
                $query->where('des.groupid', $groupid);
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
                $query->where('d.deptcode', '=', $sessiondeptcode);
            });
        $query->orderBy('des.updatedon', 'desc');

        return $query->get();
    }

    // ------------------------------------------------Mater category form--------------------------------------------

    public static function model_categorydeptfetch()
    {
        return DB::table(self::$deptTable . ' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname')  // Select required columns
            ->where('dept.statusflag', '=', 'Y')  // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }

    public static function checkExistingCategory($ename, $fname, $deptcode, $auditeeins_categoryid = null)
    {
        $table = self::$auditeecategory;
        $query = DB::table($table)->where('deptcode', $deptcode);

        $englishExists = $query
            ->whereRaw("LOWER(REPLACE(catename, ' ', '')) = ?", [$ename])
            ->when($auditeeins_categoryid, function ($query) use ($auditeeins_categoryid) {
                return $query->where('auditeeins_categoryid', '<>', $auditeeins_categoryid);
            })
            ->exists();

        $tamilExists = DB::table($table)
            ->where('deptcode', $deptcode)
            ->whereRaw("LOWER(REPLACE(cattname, ' ', '')) = ?", [$fname])
            ->when($auditeeins_categoryid, function ($query) use ($auditeeins_categoryid) {
                return $query->where('auditeeins_categoryid', '<>', $auditeeins_categoryid);
            })
            ->exists();

        return [
            'englishExists' => $englishExists,
            'tamilExists' => $tamilExists
        ];
    }

    public static function createcategory_insertupdate(array $data, $auditeeins_categoryid = null, $table = null)
    {
        $table = self::$auditeecategory;

        try {
            // Check for duplicate records based on `orderid`
            // $duplicateCheck = DB::table($table)
            //     ->where('orderid', $data['orderid'])
            //     ->when($subworkallocationtypeid, function ($query) use ($subworkallocationtypeid) {
            //         return $query->where('subworkallocationtypeid', '<>', $subworkallocationtypeid);
            //     })
            //     ->exists();

            // if ($duplicateCheck) {
            //     throw new \Exception('Order already exists. Please use a different one.');
            // }

            if ($auditeeins_categoryid) {
                $affectedRows = DB::table($table)
                    ->where('auditeeins_categoryid', $auditeeins_categoryid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $auditeeins_categoryid;
            } else {
                $latestCode = DB::table($table)
                    ->whereNotNull('catcode')  // Ensure we're considering only non-null values
                    ->max(DB::raw('CAST(catcode AS INTEGER)'));

                $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

                $data['catcode'] = $newCode;

                $newRecordId = DB::table($table)->insertGetId($data, 'auditeeins_categoryid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // public static function catcodeinsert()
    // {
    //     DB::beginTransaction();

    //     try {
    //         $latestCode = DB::table(self::$auditeecategory)
    //             ->max(DB::raw('CAST(catcode AS INTEGER)'));

    //         $newCode = $latestCode ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

    //         DB::commit();

    //         return $newCode;
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw $e;
    //     }
    // }

    // public static function catcodeinsert()
    // {
    //     DB::beginTransaction();

    //     try {
    //         $latestCode = DB::table(self::$auditeecategory)
    //             ->whereNotNull('catcode') // Ensure we're considering only non-null values
    //             ->max(DB::raw('CAST(catcode AS INTEGER)'));

    //         $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

    //         DB::commit();

    //         return $newCode;
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw $e;
    //     }
    // }

 public static function getCategoryFetch($auditeeins_categoryid = null, $table = null)
{
    $sessiondet = session('charge');
    $sessiondeptcode = $sessiondet->deptcode;

    $table = self::$auditeecategory;

    $query = DB::table($table . ' as aud')
        ->join(self::$deptTable . ' as dept', 'aud.deptcode', '=', 'dept.deptcode')
        ->select(
            'dept.deptelname',
            'dept.depttlname',
            'dept.deptesname',
            'dept.depttsname',
            'dept.deptcode',
            'aud.*',

            DB::raw("
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM audit.mst_auditeeins_subcategory sc
                        WHERE sc.catcode = aud.catcode
                        AND sc.statusflag = 'Y'
                    )

                    OR EXISTS (
                        SELECT 1
                        FROM audit.map_allocation_objection mao
                        WHERE mao.catcode = aud.catcode
                        AND mao.statusflag = 'Y'
                    )

                    OR EXISTS (
                        SELECT 1
                        FROM audit.mst_institution mi
                        WHERE mi.catcode = aud.catcode
                    )

                    THEN 'Y'
                    ELSE 'N'
                END as isexists
            ")
        )
        ->when($auditeeins_categoryid, function ($query) use ($auditeeins_categoryid) {
            $query->where('aud.auditeeins_categoryid', $auditeeins_categoryid);
        })
        ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
            $query->where('dept.deptcode', '=', $sessiondeptcode);
        });

    $query->orderBy('aud.updatedon', 'desc');

    return $query->get();
}

    // ------------------------------------------------Map0-allc_obj--------------------------------------------

    //  <<<------------------- Master Callfor Records Start ------------------->>>
    public static function roletypeinsert(array $data, $roletypeinsert)
    {
        // Insert data into the specified table
        $inserted = DB::table($roletypeinsert)->insert($data);

        if (!$inserted) {
            throw new \Exception('Failed to insert the new record.');
        }

        return true;  // Return true instead of an ID
    }

    public static function checkExistingCallforrecords($ename, $fname, $deptcode, $callforrecordsid = null)
    {
        $table = self::$callforrecords;
        $query = DB::table($table)->where('deptcode', $deptcode);

        $englishExists = $query
            ->whereRaw("LOWER(REPLACE(callforrecordsename, ' ', '')) = ?", [$ename])
            ->when($callforrecordsid, function ($query) use ($callforrecordsid) {
                return $query->where('callforrecordsid', '<>', $callforrecordsid);
            })
            ->exists();

        $tamilExists = DB::table($table)
            ->where('deptcode', $deptcode)
            ->whereRaw("LOWER(REPLACE(callforrecordstname, ' ', '')) = ?", [$fname])
            ->when($callforrecordsid, function ($query) use ($callforrecordsid) {
                return $query->where('callforrecordsid', '<>', $callforrecordsid);
            })
            ->exists();

        return [
            'englishExists' => $englishExists,
            'tamilExists' => $tamilExists
        ];
    }

    public static function callforrecords_insertupdate(array $data, $callforrecordsid, $callforrecords_auditee)
    {
        try {
            // Check if we need to update or insert
            if ($callforrecordsid) {
                // Update existing record

                $affectedRows = DB::table($callforrecords_auditee)
                    ->where('callforrecordsid', $callforrecordsid)
                    ->update($data);

                // if ($affectedRows === 0) {
                //     throw new \Exception('Failed to update the record.');
                // }
                // return $callforrecordsid;
            } else {
                // Insert new record
                $newRecordId = DB::table($callforrecords_auditee)->insertGetId($data, 'callforrecordsid');
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    //  // Fetch Call For Records Data (with possible joins)
    // public static function fetchcallforrecordsData($chargeid = null, $callforrecords_auditee)
    // {
    //     $query = DB::table($callforrecords_auditee . ' as cfr')
    //         ->join('audit.mst_dept as d', 'd.deptcode', '=', 'cfr.deptcode')  // Join with mst_dept table
    //         // ->join('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', 'cfr.catcode')  // Join with mst_dept table
    //         ->join('audit.callforrecords_auditee as c', 'c.callforrecordsid', '=', 'cfr.callforrecordsid')  // Join with callforrecords_auditee table
    //         ->select(
    //             'd.deptcode',
    //             'd.deptesname',
    //             'd.depttsname',
    //             'd.depttlname',
    //             'd.deptelname',
    //             'cfr.*'
    //         )
    //         ->when($chargeid, function ($query) use ($chargeid) {
    //             $query->where('cfr.callforrecordsid', $chargeid);  // Filter based on the callforrecordsid
    //         })
    //         ->orderBy('cfr.updatedon', 'desc');  // Order by deptcode in descending order

    //     return $query->get();
    // }

    public static function fetchcallforrecordsData($chargeid = null, $callforrecords_auditee)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;
        $query = DB::table($callforrecords_auditee . ' as cfr')
            ->join('audit.mst_dept as d', 'd.deptcode', '=', 'cfr.deptcode')  // Join with mst_dept table
            // ->join('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', 'cfr.catcode')  // Join with mst_dept table
            ->join('audit.callforrecords_auditee as c', 'c.callforrecordsid', '=', 'cfr.callforrecordsid')  // Join with callforrecords_auditee table
            ->select(
                'd.deptcode',
                'd.deptesname',
                'd.depttsname',
                'd.depttlname',
                'd.deptelname',
                'cfr.*',
 DB::raw("
                CASE

                    WHEN EXISTS (
                        SELECT 1
                        FROM audit.map_allocation_objection mao
                        WHERE mao.mapcallforrecordsid = cfr.callforrecordsid
                        AND mao.statusflag = 'Y'
                    )

                    THEN 'Y'

                    ELSE 'N'

                END as isexists
            ")
            )
            ->when($chargeid, function ($query) use ($chargeid) {
                $query->where('cfr.callforrecordsid', $chargeid);  // Filter based on the callforrecordsid
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
                $query->where('d.deptcode', '=', $sessiondeptcode);
            });
        $query->orderBy('cfr.updatedon', 'desc');  // Order by deptcode in descending order

        return $query->get();
    }

    //  <<<------------------- Master Callfor Records End ------------------->>>

    // --------------------------------------------------mapping allocation objection [Niji]---------------------------------------------------------------------------

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

    public static function getobjectionByDept($deptcode)
    {
        $table = self::$deptTable;

        return DB::table($table . ' as dept')
            ->join(self::$mainobjection . ' as main', 'dept.deptcode', '=', 'main.deptcode')
            ->select('main.objectionename', 'main.mainobjectionid', 'main.objectiontname')
            ->distinct()
            ->where('main.deptcode', $deptcode)
            ->where('main.statusflag', 'Y')
            ->orderBy('main.objectionename', 'Asc')
            ->get();
    }

    // --------------------------------------------Auditor Inst Mapping---------------------------------------------------

    // -------------------------------------------------------------Auditor Institution Mapping-------------------------------------------------------------------------

    public static function getAllAuditorInstmapping($instmappingid = null)
    {
        $table = self::$auditeinstmap;

        $query = DB::table($table . ' as aud')  // Table passed as $table, alias as 'des'
            ->leftJoin(self::$roletype . ' as role', 'aud.roletypecode', '=', 'role.roletypecode')
            ->Join(self::$deptTable . ' as dept', 'aud.deptcode', '=', 'dept.deptcode')  // Ensure proper join condition
            ->leftjoin(self::$designation . ' as des', 'aud.nodalperson_desigcode', '=', 'des.desigcode')
            ->leftJoin(self::$regionTable . ' as reg', 'aud.regioncode', '=', 'reg.regioncode')
            ->leftJoin(self::$distTable . ' as dis', 'aud.distcode', '=', 'dis.distcode')
            ->select(
                'role.roletypecode',
                'role.roletypetsname',
                'role.roletypetlname',
                'role.roletypeelname',
                'reg.regioncode',
                'reg.regionename',
                'reg.regiontname',
                'dis.distename',
                'dis.disttname',
                'des.desigcode',
                'des.desigtsname',
                'des.desigelname',
                'des.desigesname',
                'dis.distcode',
                'dept.deptcode',
                // 'dept.deptesname',
                'aud.*',
                'dept.deptcode',
                'dept.deptesname',
                'dept.deptelname',
                'dept.depttsname',
                'dept.depttlname',
                // catename from mst_auditeeins_category
            )
            ->when($instmappingid, function ($query) use ($instmappingid) {
                $query->where('aud.instmappingid', $instmappingid);
            })
            ->orderBy('aud.instmappingid', 'desc');

        return $query->get();  // Retrieve all results
    }

    // public static function Instmappingcode()
    // {
    //     $count = DB::table(self::$auditeinstmap)->count();

    //     $instmappingcode = str_pad($count + 1, 2, '0', STR_PAD_LEFT);

    //     while (DB::table(self::$auditeinstmap)->where('instmappingcode', $instmappingcode)->exists()) {
    //         $count++;
    //         $instmappingcode = str_pad($count + 1, 2, '0', STR_PAD_LEFT);
    //     }

    //     return $instmappingcode;
    // }

    // public static function Instmappingcode()
    // {
    //     DB::beginTransaction();

    //     try {
    //         // Get the max value of instmappingcode, cast to integer
    //         $latestCode = DB::table(self::$auditeinstmap)
    //             ->max(DB::raw('CAST(instmappingcode AS INTEGER)'));

    //         // Calculate new code
    //         $newCode = $latestCode ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

    //         // Commit transaction
    //         DB::commit();

    //         return $newCode;
    //     } catch (\Exception $e) {
    //         // Rollback transaction if any error occurs
    //         DB::rollBack();
    //         throw $e;
    //     }
    // }

    public static function ForAuditauditorinstmapp_insertupdate(array $data, $instmappingid = null)
    {
        $table = self::$auditeinstmap;

        try {
            if ($instmappingid) {
                $affectedRows = DB::table($table)
                    ->where('instmappingid', $instmappingid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $instmappingid;
            } else {
                $latestCode = DB::table($table)
                    ->whereNotNull('instmappingcode')  // Ensure we're considering only non-null values
                    ->max(DB::raw('CAST(instmappingcode AS INTEGER)'));

                $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

                $data['instmappingcode'] = $newCode;

                $newRecordId = DB::table($table)->insertGetId($data, 'instmappingid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function distfetch()
    {
        return DB::table(self::$distTable . ' as dis')
            ->select('dis.distcode', 'dis.distename', 'dis.disttname')
            ->where('dis.statusflag', '=', 'Y')
            ->orderBy('dis.distename', 'asc')
            ->get();
    }

    public static function Roletypefetch()
    {
        return DB::table(self::$roletype . ' as role')
            ->select('role.roletypecode', 'role.roletypeelname', 'role.roletypetlname')
            ->where('role.statusflag', '=', 'Y')
            ->where('role.auditor_instituionflag', '=', 'Y')
            ->orderBy('role.roletypecode', 'desc')
            ->get();
    }

    public static function ForAuditgetRegionsByDept($deptcode)
    {
        $table = self::$regionTable;

        return DB::table($table . ' as reg')
            ->join(self::$roletypemappingTable . ' as rtm', function ($join) {
                $join->on('reg.deptcode', '=', 'rtm.parentcode');
                // ->on('rtm.roletypecode', '=', 'reg.roletypecode');
            })
            ->select('reg.regioncode', 'reg.regionename', 'reg.regiontname')
            ->distinct()
            ->where('rtm.deptcode', $deptcode)
            ->where('reg.statusflag', 'Y')
            ->orderBy('reg.regionename', 'Asc')
            ->get();
    }

    public static function ForAuditgetaudittypeByDept($deptcode)
    {
        $table = self::$audittype_table;

        return DB::table($table . ' as type')
            ->select('type.typeofauditename', 'type.typeofaudittname', 'type.typeofauditcode')
            ->where('type.deptcode', $deptcode)
            ->where('type.statusflag', 'Y')
            ->where('type.officeflag', 'Y')
            ->orderBy('type.typeofauditename', 'Asc')
            ->get();
    }

    // //----------------------------working-------------------------------------------
    public static function ForAuditgetdesignationByDept($deptcode)
    {
        $table = self::$designation;

        return DB::table($table . ' as des')
            ->select('des.desigcode', 'des.desigelname', 'des.desigtlname', 'des.orderid')
            ->distinct()
            ->where('des.deptcode', $deptcode)
            ->where('des.statusflag', 'Y')
            ->orderBy('des.orderid', 'Asc')
            ->get();
    }

    public static function ForAuditmodalDeptfetch()
    {
        return DB::table(self::$deptTable . ' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname')  // Select required columns
            ->where('dept.statusflag', '=', 'Y')  // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }

    // ---------------------------------------------sub work allocation----------------------------------------------------

    // public static function getSubWorkAllocationTypeWithName()
    // {
    //     return DB::table('audit.mst_majorworkallocationtype AS major')
    //         ->join('audit.mst_subworkallocationtype AS sub', 'major.majorworkallocationtypeid', '=', 'sub.majorworkallocationtypeid')
    //         ->where('sub.statusflag', '=', 'Y')  // Filters rows where statusflag = 'Y' in the sub table
    //         ->select('major.majorworkallocationtypeename','major.majorworkallocationtypeid')  // Selects the majorworkallocationtypename from the major t
    //        ->orderBy('major.majorworkallocationtypeid', 'asc')  // Orders by majorworkallocationtypeid in ascending order
    //         ->get();  // Retrieve the results
    // }

    public static function subwork_deptfetch()
    {
        return DB::table(self::$deptTable . ' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname')  // Select required columns
            ->where('dept.statusflag', '=', 'Y')  // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }

    public static function checkDuplicateForSubwork($majorworkallocationtypeid, $statusflag, $ename, $tname, $subworkallocationtypeid = null)
    {
        $table = self::$subworkallocation;

        $query = DB::table($table)
            ->where('statusflag', $statusflag)
            ->where('majorworkallocationtypeid', $majorworkallocationtypeid)
            ->where(function ($q) use ($ename, $tname) {
                $q
                    ->whereRaw("LOWER(REPLACE(subworkallocationtypeename, ' ', '')) = ?", [$ename])
                    ->orWhereRaw("LOWER(REPLACE(subworkallocationtypetname, ' ', '')) = ?", [$tname]);
            });

        if ($subworkallocationtypeid) {
            $query->where('subworkallocationtypeid', '<>', $subworkallocationtypeid);
        }

        $duplicates = $query->get();

        $isEnameDuplicate = false;
        $isTnameDuplicate = false;

        foreach ($duplicates as $duplicate) {
            if (strtolower(str_replace(' ', '', $duplicate->subworkallocationtypeename)) === $ename) {
                $isEnameDuplicate = true;
            }
            if (strtolower(str_replace(' ', '', $duplicate->subworkallocationtypetname)) === $tname) {
                $isTnameDuplicate = true;
            }
        }

        return ['ename' => $isEnameDuplicate, 'tname' => $isTnameDuplicate];
    }

    public static function createsubworkallocation_insertupdate(array $data, $subworkallocationtypeid = null)
    {
        $table = self::$subworkallocation;

        try {
            // Check for duplicate records based on `orderid`
            // $duplicateCheck = DB::table($table)
            //     ->where('orderid', $data['orderid'])
            //     ->when($subworkallocationtypeid, function ($query) use ($subworkallocationtypeid) {
            //         return $query->where('subworkallocationtypeid', '<>', $subworkallocationtypeid);
            //     })
            //     ->exists();

            // if ($duplicateCheck) {
            //     throw new \Exception('Order already exists. Please use a different one.');
            // }

            if ($subworkallocationtypeid) {
                $affectedRows = DB::table($table)
                    ->where('subworkallocationtypeid', $subworkallocationtypeid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $subworkallocationtypeid;
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'subworkallocationtypeid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    public static function getAllSubWorkAllocationData($subworkallocationtypeid = null)
{
    $sessiondet = session('charge');
    $sessiondeptcode = $sessiondet->deptcode;
    $table = self::$subworkallocation;

    $query = DB::table($table . ' as des')
        ->join(self::$deptTable . ' as d', 'd.deptcode', '=', 'des.deptcode')
        ->leftJoin(self::$workallocation . ' as w', 'w.majorworkallocationtypeid', '=', 'des.majorworkallocationtypeid')
        ->select(
            'des.*',
            'd.deptelname',
            'd.depttlname',
            'w.majorworkallocationtypeename',
            'w.majorworkallocationtypetname',
            'd.depttsname',
            'd.deptesname',
            'd.deptcode',

            DB::raw("
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM audit.map_allocation_objection mao
                        WHERE mao.subworkallocationtypeid = des.subworkallocationtypeid
                        AND mao.statusflag = 'Y'
                    )
                    THEN 'Y'
                    ELSE 'N'
                END as isexists
            ")
        )
        ->when($subworkallocationtypeid, function ($query) use ($subworkallocationtypeid) {
            $query->where('des.subworkallocationtypeid', $subworkallocationtypeid);
        })
        ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
            $query->where('d.deptcode', '=', $sessiondeptcode);
        });

    $query->orderBy('des.updatedon', 'desc');

    return $query->get();
}

    public static function getworkallocationByDept($deptcode)
    {
        // return $deptcode;
        $table = self::$workallocation;

        return DB::table($table . ' as dept')
            ->select('dept.majorworkallocationtypeid', 'dept.majorworkallocationtypeename', 'dept.majorworkallocationtypetname')
            ->distinct()
            ->where('dept.deptcode', '=', $deptcode)
            ->where('dept.statusflag', 'Y')
            ->orderBy('dept.majorworkallocationtypeename', 'Asc')
            ->get();
    }

    // ------------------------------------------------work allocation--------------------------------------------------->>
    public static function model_workallocationdeptfetch()
    {
        return DB::table(self::$deptTable . ' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname')  // Select required columns
            ->where('dept.statusflag', '=', 'Y')  // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }

   public static function fetchworkallocationData($majorworkallocationtypeid = null)
{
    $table = self::$workallocation;

    $sessiondet = session('charge');
    $sessiondeptcode = $sessiondet->deptcode;

    $query = DB::table($table . ' as des')

        ->join(self::$deptTable . ' as d', 'd.deptcode', '=', 'des.deptcode')

        ->leftJoin(
            DB::raw("
                (
                    SELECT DISTINCT majorworkallocationtypeid
                    FROM audit.mst_subworkallocationtype
                    WHERE statusflag = 'Y'
                ) sw
            "),
            'sw.majorworkallocationtypeid',
            '=',
            'des.majorworkallocationtypeid'
        )

        ->leftJoin(
            DB::raw("
                (
                    SELECT DISTINCT majorworkallocationtypeid
                    FROM audit.map_allocation_objection
                    WHERE statusflag = 'Y'
                ) mao
            "),
            'mao.majorworkallocationtypeid',
            '=',
            'des.majorworkallocationtypeid'
        )

        ->leftJoin(
            DB::raw("
                (
                    SELECT DISTINCT workallocationtypeid
                    FROM audit.trans_workallocation
                ) tw
            "),
            'tw.workallocationtypeid',
            '=',
            'des.majorworkallocationtypeid'
        )

        ->select(
            'des.*',
            'd.deptelname',
            'd.depttlname',
            'd.deptesname',
            'd.depttsname',

            DB::raw("
                CASE
                    WHEN sw.majorworkallocationtypeid IS NOT NULL
                      OR mao.majorworkallocationtypeid IS NOT NULL
                      OR tw.workallocationtypeid IS NOT NULL
                    THEN 'Y'
                    ELSE 'N'
                END as isexists
            ")
        )

        ->when($majorworkallocationtypeid, function ($query) use ($majorworkallocationtypeid) {
            $query->where('des.majorworkallocationtypeid', $majorworkallocationtypeid);
        })

        ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
            $query->where('d.deptcode', $sessiondeptcode);
        })

        ->orderBy('des.updatedon', 'desc');

    return $query->get();
}

    public static function checkExistingWorkAllocation($ename, $status, $fname, $deptcode, $majorworkallocationtypeid = null)
    {
        $query = DB::table(self::$workallocation)->where('deptcode', $deptcode);

        $englishExists = $query
            ->whereRaw("LOWER(REPLACE(majorworkallocationtypeename, ' ', '')) = ?", [$ename])
            ->where('statusflag', $status)
            ->when($majorworkallocationtypeid, function ($query) use ($majorworkallocationtypeid) {
                return $query->where('majorworkallocationtypeid', '<>', $majorworkallocationtypeid);
            })
            ->exists();

        $tamilExists = DB::table(self::$workallocation)
            ->where('statusflag', $status)
            ->where('deptcode', $deptcode)
            ->whereRaw("LOWER(REPLACE(majorworkallocationtypetname, ' ', '')) = ?", [$fname])
            ->when($majorworkallocationtypeid, function ($query) use ($majorworkallocationtypeid) {
                return $query->where('majorworkallocationtypeid', '<>', $majorworkallocationtypeid);
            })
            ->exists();

        return [
            'englishExists' => $englishExists,
            'tamilExists' => $tamilExists
        ];
    }

    public static function createworkallocation_insertupdate(array $data, $majorworkallocationtypeid = null)
    {
        $table = self::$workallocation;

        try {
            $query = DB::table($table);
            if ($majorworkallocationtypeid) {
                $query->where('majorworkallocationtypeid', '<>', $majorworkallocationtypeid);
            }
            if ($majorworkallocationtypeid) {
                $affectedRows = DB::table($table)->where('majorworkallocationtypeid', $majorworkallocationtypeid)->update($data);
                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'majorworkallocationtypeid');
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // ------------------------Auditee User Details---------------------------------------------------------------------

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

    public static function getinstitutionBydistrict($district, $regioncode, $deptcode)
    {
        $table = self::$institution;

        return DB::table($table . ' as ins')
            ->select('ins.instename', 'ins.instid')
            ->distinct()
            ->where('ins.distcode', $district)
            ->where('ins.deptcode', $deptcode)
            ->where('ins.regioncode', $regioncode)
            ->where('ins.statusflag', 'Y')
            ->get();
    }

    public static function getAllAudtieeUserDetails($auditeeuserid = null)
    {
        $table = self::$auditeeuserdetail;

        $query = DB::table($table . ' as des')  // Table passed as $table, alias as 'des'
            ->join(self::$institution . ' as ins', 'des.instid', '=', 'ins.instid')
            ->join(self::$deptTable . ' as dept', 'ins.deptcode', '=', 'dept.deptcode')  // Ensure proper join condition
            ->join(self::$regionTable . ' as reg', 'ins.regioncode', '=', 'reg.regioncode')
            ->join(self::$distTable . ' as dis', 'ins.distcode', '=', 'dis.distcode')
            ->select(
                'ins.instid',
                'ins.instename',
                'reg.regioncode',
                'reg.regionename',
                'dis.distename',
                'dis.distcode',
                'ins.deptcode',
                // 'dept.deptesname',
                'des.*',
                'dept.*'  // All fields from mst_majorworkallocationtype (aliased as 'des')
                // catename from mst_auditeeins_category
            )
            ->when($auditeeuserid, function ($query) use ($auditeeuserid) {
                $query->where('des.auditeeuserid', $auditeeuserid);
            })
            ->orderBy('des.auditeeuserid', 'desc');

        return $query->get();  // Retrieve all results
    }

    public static function audit_deptfetch()
    {
        return DB::table(self::$institution . ' as ins')
            ->join(self::$deptTable . ' as dept', 'ins.deptcode', '=', 'dept.deptcode')  // Ensure proper join condition
            ->select('dept.deptelname', 'dept.deptcode')  // Select required columns
            ->distinct()
            ->where('ins.statusflag', '=', 'Y')  // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            // dd($query->toSql());
            ->get();
    }

    public static function createauditeeuserdetails_insertupdate(array $data, $auditeeuserid = null)
    {
        $table = self::$auditeeuserdetail;
        try {
            $query = DB::table($table);
            if ($auditeeuserid) {
                $query->where('auditeeuserid', '<>', $auditeeuserid);
            }
            if ($auditeeuserid) {
                $affectedRows = DB::table($table)->where('auditeeuserid', $auditeeuserid)->update($data);
                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'auditeeuserid');
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // public static function auditee_institutionfetch()
    // {
    //         return DB::table(self::$institution . ' as ins')
    //         ->select('ins.instid','ins.instename')
    //         ->where('statusflag', 'Y')
    //         ->orderby('ins.instename', 'Asc')
    //         ->get();
    // }

    // -------------------------map call for records-----------------------------------------------------------------------

    // public static function map_callforrecordsfetch()
    // {

    //     return DB::table('audit.map_callforrecord as map')
    //         ->join('audit.callforrecords_auditee as records', 'map.callforecordsid', '=', 'records.callforrecordsid')
    //         // Fixed condition syntax
    //         ->select('records.callforrecordsename','records.callforrecordsid')
    //         ->distinct() // Ensures distinct results
    //         ->where('map.statusflag', 'Y')
    //         ->orderBy('records.callforrecordsename', 'ASC') // Fixed orderBy syntax
    //         ->get();
    // }
    public static function getcategoryByDept($deptcode)
    {
        return DB::table(self::$auditeecategory)
            // ->select()
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->get(['catcode', 'catename', 'cattname']);
    }

    public static function getcallforrecordsByDept($deptcode)
    {
        return DB::table(self::$callforrecords_auditee)
            // ->select()
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->get(['callforrecordsename', 'callforrecordsid']);
    }

    public static function map_callforrecordsfetch()
    {
        return DB::table(self::$mapcallforrecords . ' as map')
            ->join(self::$callforrecords_auditee . ' as records', 'map.callforecordsid', '=', 'records.callforrecordsid')
            ->select('records.callforrecordsename', 'records.callforrecordsid')
            ->distinct()  // Ensures distinct results
            ->where('map.statusflag', 'Y')
            ->orderBy('records.callforrecordsename', 'ASC')  // Fixed orderBy syntax
            ->get();
    }

    public static function ModaldeptForCallforrecords()
    {
        return DB::table(self::$auditeecategory . ' as audi_cat')
            ->join(self::$deptTable . ' as dept', 'audi_cat.deptcode', '=', 'dept.deptcode')
            ->select('dept.deptelname', 'dept.deptcode')  // Select required columns
            ->distinct()
            ->where('audi_cat.statusflag', '=', 'Y')  // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            // dd($query->toSql());
            ->get();
    }

    public static function createmapcallforrecords_insertupdate(array $data, $mapcallforrecordid = null)
    {
        $table = self::$mapcallforrecords;
        try {
            $query = DB::table($table);
            if ($mapcallforrecordid) {
                $query->where('mapcallforrecordid', '<>', $mapcallforrecordid);
            }
            if ($mapcallforrecordid) {
                $affectedRows = DB::table($table)->where('mapcallforrecordid', $mapcallforrecordid)->update($data);
                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'mapcallforrecordid');
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getAllmapcallforrecords($mapcallforrecordid = null)
    {
        $table = self::$mapcallforrecords;

        $query = DB::table($table . ' as des')  // Table passed as $table, alias as 'des'
            ->join(self::$auditeecategory . ' as aud_cat', 'des.catcode', '=', 'aud_cat.catcode')
            ->join(self::$deptTable . '  as d', 'aud_cat.deptcode', '=', 'd.deptcode')
            ->join(self::$callforrecords_auditee . ' as c', 'des.callforecordsid', '=', 'c.callforrecordsid')
            ->select(
                'des.*',  // All fields from mst_majorworkallocationtype (aliased as 'des')
                'd.deptelname',
                'aud_cat.deptcode',  // deptelname from mst_dept (aliased as 'd')
                'aud_cat.catename',
                'c.callforrecordsename',
                'c.callforrecordsid'  // catename from mst_auditeeins_category
            )
            ->when($mapcallforrecordid, function ($query) use ($mapcallforrecordid) {
                $query->where('des.mapcallforrecordid', $mapcallforrecordid);
            })
            ->orderBy('des.mapcallforrecordid', 'desc');

        return $query->get();  // Retrieve all results
    }

    public static function createdept_insertupdate(array $data, $currentDeptId = null, $table)
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

    public static function createdesignation_insertupdate(array $data, $desigid = null, $table = null)
    {
        try {
            $table = self::$designation;

            $fields = [
                'desigesname' => trim($data['desigesname']),
                'desigelname' => trim($data['desigelname']),
                'desigtsname' => trim($data['desigtsname']),
                'desigtlname' => trim($data['desigtlname']),
            ];

            // Prepare normalized (space-removed, lowercase) versions for duplicate checking
            $normalized = [];
            foreach ($fields as $key => $value) {
                $normalized[$key] = strtolower(str_replace(' ', '', $value));
            }

            $duplicateFlags = [];
            foreach ($normalized as $column => $value) {
                $exists = DB::table($table)
                    ->where('deptcode', $data['deptcode'])
                    ->where('isauditeeedepartment', $data['isauditeeedepartment'])
                    ->whereRaw("LOWER(REPLACE($column, ' ', '')) = ?", [$value])
                    ->when($desigid, fn($query) => $query->where('desigid', '<>', $desigid))
                    ->exists();

                if ($exists) {
                    $duplicateFlags[] = $column;
                }
            }

            // Handle duplicates with specific exception messages
            if (count($duplicateFlags) === count($fields)) {
                throw new \Exception('DesigAllNamesExist');
            }

            $exceptionMap = [
                'desigesname' => 'DesigEsnameExist',
                'desigelname' => 'DesigElnameExist',
                'desigtsname' => 'DesigTsnameExist',
                'desigtlname' => 'DesigTlnameExist',
            ];

            foreach ($duplicateFlags as $flag) {
                throw new \Exception($exceptionMap[$flag]);
            }

            $query = DB::table($table);
            if ($desigid) {
                $query->where('desigid', '<>', $desigid);
            }
            if ($desigid) {
                $affectedRows = DB::table($table)->where('desigid', $desigid)->update($data);
                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
            } else {
                $latestCode = DB::table($table)
                    ->whereNotNull('desigcode')  // Ensure we're considering only non-null values
                    ->max(DB::raw('CAST(desigcode AS INTEGER)'));

                $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

                $data['desigcode'] = $newCode;

                $newRecordId = DB::table($table)->insertGetId($data, 'desigid');
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // public static function fetchdesignationData($chargeid = null, $table)
    // {
    //     $query = DB::table($table . ' as des')
    //         ->join('audit.mst_dept as d', 'd.deptcode', '=', 'des.deptcode')
    //         ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'des.desigcode')
    //         ->select(
    //             'd.deptcode',
    //             'd.depttsname',
    //             'd.deptelname',
    //             'd.depttlname',
    //             'd.deptesname',

    //             'des.*'
    //         )
    //         ->when($chargeid, function ($query) use ($chargeid) {
    //             $query->where('des.desigid', $chargeid);
    //         })
    //         ->orderBy('des.updatedon', 'desc');
    //     return $query->get();
    // }

    public static function fetchdesignationData($chargeid = null, $table)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;
        $query = DB::table($table . ' as des')
            ->join('audit.mst_dept as d', 'd.deptcode', '=', 'des.deptcode')
            ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'des.desigcode')
            ->select(
                'd.deptcode',
                'd.depttsname',
                'd.deptelname',
                'd.depttlname',
                'd.deptesname',
                'des.*',
		DB::raw("
                CASE

                    WHEN EXISTS (
                        SELECT 1
                        FROM audit.deptuserdetails ai
                        WHERE ai.desigcode = des.desigcode
                        AND des.statusflag = 'Y'
                    )

                    THEN 'Y'

                    ELSE 'N'

                END as isexists
            ")
            )
            ->when($chargeid, function ($query) use ($chargeid) {
                $query->where('des.desigid', $chargeid);
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
                $query->where('d.deptcode', '=', $sessiondeptcode);
            });
        $query->orderBy('des.updatedon', 'desc');
        return $query->get();
    }

    //  <<<------------------- Master Designation End ------------------->>>
    //  <<<------------------- Master Region Start ------------------->>>
    public static function createregion_insertupdate(array $data, $regionid = null, $table = null)
    {
        $table = self::$regionTable;

        try {
            $regionename = trim($data['regionename']);
            $regiontname = trim($data['regiontname']);

            // Prepare for duplicate check (remove spaces and change to lowercase)
            $checkEname = strtolower(str_replace(' ', '', $regionename));
            $checkTname = strtolower(str_replace(' ', '', $regiontname));

            $enameExists = DB::table($table)
                ->where('deptcode', $data['deptcode'])
                ->whereRaw("LOWER(REPLACE(regionename, ' ', '')) = ?", [$checkEname])
                ->when($regionid, function ($query) use ($regionid) {
                    return $query->where('regionid', '<>', $regionid);
                })
                ->exists();

            $tnameExists = DB::table($table)
                ->where('deptcode', $data['deptcode'])
                ->whereRaw("LOWER(REPLACE(regiontname, ' ', '')) = ?", [$checkTname])
                ->when($regionid, function ($query) use ($regionid) {
                    return $query->where('regionid', '<>', $regionid);
                })
                ->exists();

            if ($enameExists && $tnameExists) {
                throw new \Exception('RegionETnameExist');
            } elseif ($enameExists) {
                throw new \Exception('RegionEnameExist');
            } elseif ($tnameExists) {
                throw new \Exception('RegionTnameExist');
            }

            $query = DB::table($table);
            if ($regionid) {
                $query->where('regionid', '<>', $regionid);
            }
            if ($regionid) {
                $affectedRows = DB::table($table)->where('regionid', $regionid)->update($data);
                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
            } else {
                $latestCode = DB::table($table)
                    ->whereNotNull('regioncode')  // Ensure we're considering only non-null values
                    ->max(DB::raw('CAST(regioncode AS INTEGER)'));

                $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

                $data['regioncode'] = $newCode;

                $newRecordId = DB::table($table)->insertGetId($data, 'regionid');
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // public static function fetchregionData($regionid = null, $table)
    // {
    //     $query = DB::table($table . ' as des')
    //         ->join('audit.mst_dept as d', 'd.deptcode', '=', 'des.deptcode')
    //         ->select(
    //             'd.deptcode',
    //             'd.deptesname',
    //             'd.depttsname',
    //             'des.*'
    //         )
    //         ->when($regionid, function ($query) use ($regionid) {
    //             $query->where('des.regionid', $regionid);
    //         })
    //         ->orderBy('des.updatedon', 'desc');
    //     return $query->get();
    // }

    public static function fetchregionData($regionid = null, $table)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;
        $query = DB::table($table . ' as des')
            ->join('audit.mst_dept as d', 'd.deptcode', '=', 'des.deptcode')
            ->select(
                'd.deptcode',
                'd.deptesname',
                'd.depttsname',
                'des.*',
		DB::raw("
                CASE

                    WHEN EXISTS (
                        SELECT 1
                        FROM audit.auditor_instmapping ai
                        WHERE ai.regioncode = des.regioncode
                        AND ai.statusflag = 'Y'
                    )

                    THEN 'Y'

                    ELSE 'N'

                END as isexists
            ")
            )
            ->when($regionid, function ($query) use ($regionid) {
                $query->where('des.regionid', $regionid);
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
                $query->where('d.deptcode', '=', $sessiondeptcode);
            });

        $query->orderBy('des.updatedon', 'desc');
        return $query->get();
    }

    //  <<<------------------- Master Region End ------------------->>>
    //  <<<------------------- Master MainObjection Start ------------------->>>

    public static function checkExistingMainObjection($ename, $statusflag, $fname, $deptcode, $mainobjectionid = null)
    {
        $table = self::$mainobjection;
        $query = DB::table($table)->where('deptcode', $deptcode);

        $englishExists = $query
            ->whereRaw("LOWER(REPLACE(objectionename, ' ', '')) = ?", [$ename])
            ->where('statusflag', $statusflag)
            ->when($mainobjectionid, function ($query) use ($mainobjectionid) {
                return $query->where('mainobjectionid', '<>', $mainobjectionid);
            })
            ->exists();

        $tamilExists = DB::table($table)
            ->where('statusflag', $statusflag)
            ->where('deptcode', $deptcode)
            ->whereRaw("LOWER(REPLACE(objectiontname, ' ', '')) = ?", [$fname])
            ->when($mainobjectionid, function ($query) use ($mainobjectionid) {
                return $query->where('mainobjectionid', '<>', $mainobjectionid);
            })
            ->exists();

        return [
            'englishExists' => $englishExists,
            'tamilExists' => $tamilExists
        ];
    }

    public static function createmainobjection_insertupdate(array $data, $mainobjectionid = null, $table)
    {
        try {
            $query = DB::table($table);
            if ($mainobjectionid) {
                $query->where('mainobjectionid', '<>', $mainobjectionid);
            }
            if ($mainobjectionid) {
                $affectedRows = DB::table($table)->where('mainobjectionid', $mainobjectionid)->update($data);
                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'mainobjectionid');
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function fetchmainobjectionData($mainobjectionid = null, $table)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;

        $query = DB::table($table . ' as des')
            ->join('audit.mst_dept as d', 'd.deptcode', '=', 'des.deptcode')
            ->select(
                'd.deptcode',
                'd.deptesname',
                'd.depttsname',
                'd.deptelname',
                'd.depttlname',
                'des.*',
 DB::raw("
                CASE

                    WHEN EXISTS (
                        SELECT 1
                        FROM audit.mst_subobjection so
                        WHERE so.mainobjectionid = des.mainobjectionid
                        AND so.statusflag = 'Y'
                    )

                    OR EXISTS (
                        SELECT 1
                        FROM audit.map_allocation_objection mao
                        WHERE mao.mainobjectionid = des.mainobjectionid
                        AND mao.statusflag = 'Y'
                    )

                    THEN 'Y'

                    ELSE 'N'

                END as isexists
            ")
            )
            ->when($mainobjectionid, function ($query) use ($mainobjectionid) {
                $query->where('des.mainobjectionid', $mainobjectionid);
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
                $query->where('d.deptcode', '=', $sessiondeptcode);
            });
        $query->orderBy('des.updatedon', 'desc');

        return $query->get();
    }

    public static function mainobjectiondetails($tablename)
    {
        return DB::table("$tablename as main")
            ->select('main.objectionename', 'main.objectiontname', 'main.mainobjectionid')
            ->where('statusflag', '=', 'Y')
            ->orderBy('objectionename', 'asc')
            ->get();
    }

    //  <<<------------------- Master MainObjection End ------------------->>>

    //  <<<------------------- Master SubObjection Start ------------------->>>

    // public static function getSubcategoryByCategory($cat_code)
    // {
    //     $table = self::$auditeecategory;

    //     return DB::table($table . ' as aud')
    //         ->join(self::$subcategory . ' as sub', 'aud.catcode', '=', 'sub.catcode')
    //         ->select('sub.subcatename', 'sub.subcattname', 'sub.auditeeins_subcategoryid', 'aud.if_subcategory')
    //         ->distinct()
    //         ->where('sub.catcode', $cat_code)
    //         ->where('aud.if_subcategory', 'Y')
    //         ->orderBy('sub.subcatename', 'Asc')
    //         //  dd($date);
    //         ->get();
    // }

    // public static function getobjectionByDept($deptcode)
    // {
    //     $table = self::$deptTable;

    //     return DB::table($table . ' as dept')
    //         ->join(self::$mainobjection . ' as main', 'dept.deptcode', '=', 'main.deptcode')
    //         ->select('main.objectionename', 'main.mainobjectionid', 'main.objectiontname')
    //         ->distinct()
    //         ->where('main.deptcode', $deptcode)
    //         ->where('main.statusflag', 'Y')
    //         ->orderBy('main.objectionename', 'Asc')
    //         ->get();
    // }

    public static function checkDuplicateForSubobj($mainobjectionid, $statusflag, $ename, $tname, $subobjectionid = null)
    {
        $table = self::$subobjection;

        $query = DB::table($table)
            ->where('statusflag', $statusflag)
            ->where('mainobjectionid', $mainobjectionid)
            ->where(function ($q) use ($ename, $tname) {
                $q
                    ->whereRaw("LOWER(REPLACE(subobjectionename, ' ', '')) = ?", [$ename])
                    ->orWhereRaw("LOWER(REPLACE(subobjectiontname, ' ', '')) = ?", [$tname]);
            });

        if ($subobjectionid) {
            $query->where('subobjectionid', '<>', $subobjectionid);
        }

        $duplicates = $query->get();

        $isEnameDuplicate = false;
        $isTnameDuplicate = false;

        foreach ($duplicates as $duplicate) {
            if (strtolower(str_replace(' ', '', $duplicate->subobjectionename)) === $ename) {
                $isEnameDuplicate = true;
            }
            if (strtolower(str_replace(' ', '', $duplicate->subobjectiontname)) === $tname) {
                $isTnameDuplicate = true;
            }
        }

        return ['ename' => $isEnameDuplicate, 'tname' => $isTnameDuplicate];
    }

    public static function creatsubobjection_insertupdate(array $data, $subobjectionid = null, $table)
    {
        try {
            $query = DB::table($table);
            if ($subobjectionid) {
                $query->where('subobjectionid', '<>', $subobjectionid);
            }
            if ($subobjectionid) {
                $affectedRows = DB::table($table)->where('subobjectionid', $subobjectionid)->update($data);
                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'subobjectionid');
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // public static function fetchsubobjectionData($subobjectionid = null, $table)
    // {
    //     if($subobjectionid===''){
    //         $query = DB::table($table . ' as des')
    //          ->join('audit.mst_mainobjection as d', 'des.mainobjectionid', '=', 'd.mainobjectionid')
    //          ->join('audit.mst_dept as dept', 'des.deptcode', '=', 'dept.deptcode')
    //         ->select(
    //             'dept.deptcode',
    //             'dept.deptelname as dept_name',
    //             'd.mainobjectionid',
    //             'd.objectionename',

    //              'des.*'
    //         )
    //         ->when($subobjectionid, function ($query) use ($subobjectionid) {
    //             $query->where('des.subobjectionid', $subobjectionid);
    //         })
    //         ->orderBy('des.subobjectionid', 'desc');
    //     return $query->get();
    //     }
    //     else{
    //         $query = DB::table($table . ' as des')
    //         // ->join('audit.mst_mainobjection as d', 'd.mainobjectionid', '=', 'des.mainobjectionid')
    //         ->select(
    //             // 'd.mainobjectionid',
    //             // 'd.objectionename',
    //              'des.*'
    //         )
    //         ->when($subobjectionid, function ($query) use ($subobjectionid) {
    //             $query->where('des.subobjectionid', $subobjectionid);
    //         })
    //         ->orderBy('des.subobjectionid', 'desc');
    //     return $query->get();
    //     }

    // }
    public static function fetchsubobjectionData($subobjectionid = null, $table)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;

        $query = DB::table($table . ' as des')
            ->leftJoin('audit.mst_mainobjection as d', 'des.mainobjectionid', '=', 'd.mainobjectionid')
            ->leftJoin('audit.mst_dept as dept', 'des.deptcode', '=', 'dept.deptcode')  // Use LEFT JOIN
            ->select(
                'dept.deptcode',
                'dept.deptelname',
                'dept.depttsname',
                'dept.deptesname',
                'dept.depttlname',
                'd.mainobjectionid',
                'd.objectionename',
                'd.objectiontname',
                'des.*',
 		DB::raw("
                CASE

                    WHEN EXISTS (
                        SELECT 1
                        FROM audit.map_allocation_objection mao
                        WHERE mao.subobjectionid = des.subobjectionid
                        AND mao.statusflag = 'Y'
                    )

                    THEN 'Y'

                    ELSE 'N'

                END as isexists
            ")
            )
            ->when($subobjectionid, function ($query) use ($subobjectionid) {
                $query->where('des.subobjectionid', $subobjectionid);
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
                $query->where('dept.deptcode', '=', $sessiondeptcode);
            });
        $query->orderBy('des.updatedon', 'desc');

        // Debugging - Uncomment to test
        // dd($query->toSql(), $query->getBindings());

        return $query->get();
    }

    //  <<<------------------- Master MainObjection End ------------------->>>
    //  <<<------------------- Master Menu Form  Start ------------------->>>
    public static function getTopLevelMenus($table)
    {
        $model = new self();
        $model->setTable($table);
        return $model->where('parentid', '0')->get();
    }

    public static function createmenu_insertupdate(array $data, $menuid = null, $table)
    {
        try {
            $query = DB::table($table);
            if ($menuid) {
                $query->where('menuid', '<>', $menuid);
            }
            if ($menuid) {
                $affectedRows = DB::table($table)->where('menuid', $menuid)->update($data);
                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'menuid');
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function fetchmenuData($menuid = null, $table)
    {
        $query = DB::select(
            DB::raw("
                WITH RECURSIVE MenuHierarchy AS (
                    -- Base case: Select parent menus
                    SELECT
                        t1.menuid,
                        t1.menuename,
                        t1.menuurl,
                        t1.parentid,
                        t1.orderid,
                        t1.parentorderid,
                        t3.menuename AS parent_menuename,
                        0 AS levelid,
                        t1.menuid::text AS full_id
                    FROM
                        audit.mst_menu AS t1
                    LEFT JOIN
                        audit.mst_menu AS t3 ON t1.parentid = t3.menuid
                    WHERE
                        t1.parentid = 0

                    UNION ALL

                    -- Recursive case: Select submenus
                    SELECT
                        t1.menuid,
                        t1.menuename,
                        t1.menuurl,
                        t1.parentid,
                        t1.orderid,
                        t1.parentorderid,
                        t3.menuename AS parent_menuename,
                        mh.levelid + 1 AS levelid,
                        mh.full_id || '.' || t1.menuid::text AS full_id
                    FROM
                        audit.mst_menu AS t1
                    LEFT JOIN
                        audit.mst_menu AS t3 ON t1.parentid = t3.menuid
                    JOIN
                        MenuHierarchy AS mh ON t1.parentid = mh.menuid
                )
                SELECT
                    mh.full_id AS menu_order,  -- Concatenates the parent and submenu id to create hierarchical structure
                    mh.menuid,
                    mh.menuename,
                    mh.menuurl,
                    mh.parentid,
                    mh.orderid,
                    mh.parentorderid,
                    mh.parent_menuename,
                    mh.levelid
                FROM
                    MenuHierarchy AS mh
                " . ($menuid ? 'WHERE mh.menuid = :menuid' : '') . '  -- Apply the menuid filter if provided
                ORDER BY
                    mh.full_id ASC,
                    mh.orderid ASC;
            '),
            $menuid ? ['menuid' => $menuid] : []
        );

        $collection = collect($query);

        if ($collection->isEmpty()) {
            return [];
        }
        return $collection;
    }

    public static function fetchmenuData_record($menuid, $table)
    {
        $query = DB::table($table . ' as t1')
            ->leftJoin($table . ' as t3', 't1.parentid', '=', 't3.menuid')
            ->select(
                't1.*',
                't1.parentid as parentid',
                't1.levelid as levelid'
            )
            ->when($menuid, function ($query) use ($menuid) {
                $query->where('t1.menuid', $menuid);
            })
            ->orderBy('t1.menuid', 'asc');
        return $query->get();
    }

    // public static function insertmulti_mapWorkObj($data, $mapallocationobjectionid = null, $table, $userid)
    // {
    //     $query = DB::table($table);

    //     try {
    //         foreach ($data as $tabledatas) {

    //             foreach ($tabledatas['work_allocations'] as $work) {
    //                 $insertData = [
    //                     'catcode' => $tabledatas['category'],
    //                     'auditeeins_subcategoryid' =>  !empty($tabledatas['subcategory']) ? $tabledatas['subcategory'] : null,
    //                     'groupid' => $tabledatas['group'],
    //                     'mapcallforrecordsid' => $tabledatas['call_for_records'],
    //                     'majorworkallocationtypeid' => $work['main_work'],
    //                     'subworkallocationtypeid' => !empty($work['sub_work']) ? $work['sub_work'] : null, // ✅ Convert empty to NULL
    //                     'mainobjectionid' => $work['main_obj'],
    //                     'subobjectionid' => $work['sub_obj'], // ✅ Convert empty to NULL
    //                     'statusflag' => 'Y',
    //                     // 'allocatetowhom' => 'Y',
    //                     'allocatetowhom' =>  $work['allocate_towhom'],
    //                     'created_by' => $userid,
    //                     'created_on' => View::shared('get_nowtime'),
    //                     'updated_on' => View::shared('get_nowtime'),
    //                 ];

    //                 // Insert into the table
    //                 DB::table($table)->insert($insertData);
    //             }
    //         }

    //         return;
    //     } catch (\Exception $e) {
    //         // ✅ Error Handling: Log and return error message
    //         \Log::error('Error inserting data: ' . $e->getMessage());
    //         return response()->json(['error' => 'Something went wrong.'], 500);
    //     }
    // }

    public static function insertmulti_mapWorkObj($data, $mapallocationobjectionid = null, $table, $userid)
    {
        try {
            foreach ($data as $tabledatas) {
                if (is_array($tabledatas['category'])) {
                    $categories = $tabledatas['category'];
                } else {
                    $categories = [$tabledatas['category']];
                }

                if (!empty($tabledatas['subcategory'])) {
                    if (is_array($tabledatas['subcategory'])) {
                        $subcategories = array_filter($tabledatas['subcategory'], function ($val) {
                            return !empty($val);
                        });

                        if (empty($subcategories)) {
                            $subcategories = [null];
                        }
                    } else {
                        $subcategories = [$tabledatas['subcategory']];
                    }
                } else {
                    $subcategories = [null];
                }

                foreach ($categories as $category) {
                    foreach ($subcategories as $subcategory) {
                        foreach ($tabledatas['work_allocations'] as $work) {
                            $insertData = [
                                'catcode' => $category,
                                'auditeeins_subcategoryid' => $subcategory,
                                'groupid' => $tabledatas['group'],
                                'mapcallforrecordsid' => $tabledatas['call_for_records'],
                                'majorworkallocationtypeid' => $work['main_work'],
                                'subworkallocationtypeid' => !empty($work['sub_work']) ? $work['sub_work'] : null,
                                'mainobjectionid' => $work['main_obj'],
                                'subobjectionid' => $work['sub_obj'],
                                'statusflag' => 'Y',
                                'allocatetowhom' => $work['allocate_towhom'],
                                'created_by' => $userid,
                                'created_on' => View::shared('get_nowtime'),
                                'updated_on' => View::shared('get_nowtime'),
                            ];

                            DB::table($table)->insert($insertData);
                        }
                    }
                }
            }

            return;
        } catch (\Exception $e) {
            \Log::error('Error inserting data: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    public static function mapallocationobj_insertupdate($data, $mapallocationobjectionid = null, $table, $userid)
    {
        // return $data;
        $query = DB::table($table);

        if ($mapallocationobjectionid) {
            $query->where('mapallocationobjectionid', '!=', $mapallocationobjectionid);
        }

        if ($data['auditeeins_subcategoryid'] == '' || $data['auditeeins_subcategoryid'] == null) {
            $data['auditeeins_subcategoryid'] == null;
        }
        $existingMap = (clone $query)
            ->where('auditeeins_subcategoryid', $data['auditeeins_subcategoryid'])
            ->where('majorworkallocationtypeid', $data['majorworkallocationtypeid'])
            ->where('subworkallocationtypeid', $data['subworkallocationtypeid'])
            ->where('catcode', $data['catcode'])
            ->where('mainobjectionid', $data['mainobjectionid'])
            ->where('subobjectionid', $data['subobjectionid'])
            ->where('mapcallforrecordsid', $data['mapcallforrecordsid'])
            ->where('allocatetowhom', $data['allocatetowhom'])
            ->where('groupid', $data['groupid'])
            // ->where('deptcode', $data['deptcode'])
            ->exists();

        // return $existingMap;
        // Duplicate validation
        if ($existingMap) {
            // return 'true';
            throw new \Exception('The Mapping   already exists.');
        }
        // return '';
        if ($mapallocationobjectionid) {
            $data['updated_by'] = $userid;
            $data['updated_on'] = View::shared('get_nowtime');
            DB::table($table)->where('mapallocationobjectionid', $mapallocationobjectionid)->update($data);
            return DB::table($table)->where('mapallocationobjectionid', $mapallocationobjectionid)->first();
        } else {
            // foreach ($sub_workArray as $index => $subworkid) {
            //     $data = [
            //         'subworkallocationtypeid' => $subworkid,
            //         'created_by' => $userid,
            //         'created_on' => View::shared('get_nowtime'),
            //     ];
            // return $data;
            // Insert and get the ID
        }
        $data['created_by'] = $userid;
        $data['created_on'] = View::shared('get_nowtime');
        $newMapAllocationObjId = DB::table($table)->insertGetId($data, 'mapallocationobjectionid');
        // $newMapAllocationObjId = DB::table($table)->insertGetId($data, 'mapallocationobjectionid');
        return DB::table($table)->where('mapallocationobjectionid', $newMapAllocationObjId)->first();

        // Optional: Debugging
        // dd($newMapAllocationObjId, $data);
        // }
        // $data['created_by'] = $userid;
        // $data['created_on'] = View::shared('get_nowtime');
        // $newMapAllocationObjId = DB::table($table)->insertGetId($data, 'mapallocationobjectionid');
        // return DB::table($table)->where('mapallocationobjectionid', $newMapAllocationObjId)->first();
        // }
    }

    public static function fetchall_mapallocationObj($table, $id = null, $deptcode_filt, $towhom)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;

        $query = DB::table($table . ' as map')
            // ->join('audit.map_callforrecord as mcr', 'mcr.mapcallforrecordid', '=', 'map.mapcallforrecordsid')
            // ->join('audit.')
            ->join('audit.callforrecords_auditee as cr', 'cr.callforrecordsid', '=', 'map.mapcallforrecordsid')
            ->join('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', 'map.catcode')
            ->join(self::$group_table . ' as group', 'group.groupid', '=', 'map.groupid')
            ->leftJoin(self::$subcategory . ' as sub', 'map.auditeeins_subcategoryid', '=', 'sub.auditeeins_subcategoryid')
            // ->join('audit.map_workallocation as mapwork', 'mapwork.majorworkallocationtypeid', '=', 'map.majorworkallocationtypeid')
            ->join('audit.mst_majorworkallocationtype as majwork', 'majwork.majorworkallocationtypeid', '=', 'map.majorworkallocationtypeid')
            // ->leftJoin('audit.map_workallocation as mapwork', 'mapwork.minorworkallocationtypeid', '=', 'map.subworkallocationtypeid')
            ->leftJoin('audit.mst_subworkallocationtype as subwork', 'subwork.subworkallocationtypeid', '=', 'map.subworkallocationtypeid')
            ->Join('audit.mst_mainobjection as mainobj', 'mainobj.mainobjectionid', '=', 'map.mainobjectionid')
            ->Join('audit.mst_subobjection as subobj', 'subobj.subobjectionid', '=', 'map.subobjectionid')
            ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'cat.deptcode')
            ->select(
                'map.mapallocationobjectionid',
                'map.majorworkallocationtypeid',
                'map.subworkallocationtypeid',
                'map.mapcallforrecordsid',
                'map.mainobjectionid',
                'map.subobjectionid',
                'map.allocatetowhom',
                'group.groupename',
                'group.grouptname',
                'group.groupid',
                'dept.deptcode',
                'dept.deptesname',
                'dept.depttsname',
                'cat.catename',
                'cat.cattname',
                'cat.if_subcategory',
                'map.catcode',
                'sub.auditeeins_subcategoryid',
                // 'mcr.mapcallforrecordsid',
                'cr.callforrecordsename',
                'cr.callforrecordstname',
                'cr.callforrecordsid',
                // 'map.mapcallforrecordsid',
                'majwork.majorworkallocationtypeename',
                'majwork.majorworkallocationtypetname',
                'subwork.subworkallocationtypeename',
                'subwork.subworkallocationtypetname',
                'mainobj.objectionename',
                'mainobj.objectiontname',
                'subobj.subobjectionename',
                'subobj.subobjectiontname',
                DB::raw("CASE WHEN cat.if_subcategory = 'N' OR   cat.if_subcategory IS NULL THEN cat.catename ELSE sub.subcatename END AS subcategory_ename"),
                // Handling subcategory_title based on if_subcategory flag
                DB::raw("CASE WHEN cat.if_subcategory = 'N' OR cat.if_subcategory IS NULL THEN cat.cattname ELSE sub.subcattname END AS subcategory_tname"),
		DB::raw("
                    CASE
                            WHEN EXISTS (
                                SELECT 1
                                FROM audit.trans_followup fp
                                WHERE fp.mainobjectionid = map.mainobjectionid
                            )

                            OR EXISTS (
                                SELECT 1
                                FROM audit.trans_followup fp
                                WHERE fp.subobjectionid = map.subobjectionid
                            )

                            OR EXISTS (
                                SELECT 1
                                FROM audit.trans_auditslip ts
                                WHERE ts.mainobjectionid = map.mainobjectionid
                            )

                            OR EXISTS (
                                SELECT 1
                                FROM audit.trans_auditslip ts
                                WHERE ts.subobjectionid = map.subobjectionid
                            )

                            THEN 'Y'
                            ELSE 'N'
                            END as isexists

                    ")

                // 'map.*',
                // 'mcr.*',
                // 'cr.*',
                // 'cat.*',
                // 'majwork.*',
                // 'mapwork.*',

            )
            ->where('map.statusflag', 'Y')
            ->orderby('updated_on', 'desc');
        $query->when($id, function ($query) use ($id) {
            $query->where('map.mapallocationobjectionid', '=', $id);
        });
        $query->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
            $query->where('dept.deptcode', '=', $sessiondeptcode);
        });
        $query->when($deptcode_filt, function ($query) use ($deptcode_filt) {
            $query->where('dept.deptcode', '=', $deptcode_filt);
        });
        $query->when($towhom, function ($query) use ($towhom) {
            $query->where('map.allocatetowhom', '=', $towhom);
        });

        $query->orderBy('map.updated_on', 'desc');

        // dd($query->tosql());
        return $query->cursor();
    }

    //  <<<------------------- Master Menu Form  End ------------------->>>

    // public static function getSubWorkAllocationTypeWithName()
    // {
    //     return DB::table('audit.mst_majorworkallocationtype AS major')
    //         ->join('audit.mst_subworkallocationtype AS sub', 'major.majorworkallocationtypeid', '=', 'sub.majorworkallocationtypeid')
    //         ->where('sub.statusflag', '=', 'Y')  // Filters rows where statusflag = 'Y' in the sub table
    //         ->select('major.majorworkallocationtypeename','major.majorworkallocationtypeid')  // Selects the majorworkallocationtypename from the major t
    //        ->orderBy('major.majorworkallocationtypeid', 'asc')  // Orders by majorworkallocationtypeid in ascending order
    //         ->get();  // Retrieve the results
    // }

    // <<------------------------work allocation----------------------->>

    // public static function fetchworkallocationData($majorworkallocationtypeid = null, $table)

    // {
    //     $query = DB::table($table . ' as des')
    //         ->join('audit.mst_dept as d', 'd.deptcode', '=', 'des.deptcode')
    //         ->join('audit.mst_auditeeins_category', 'des.catcode', '=', 'mst_auditeeins_category.catcode')
    //         ->select(
    //             'des.*',                               // All fields from mst_majorworkallocationtype (aliased as 'des')
    //             'd.deptelname',                        // deptelname from mst_dept (aliased as 'd')
    //             'mst_auditeeins_category.catename'     // catename from mst_auditeeins_category
    //         )
    //         ->when($majorworkallocationtypeid, function ($query) use ($majorworkallocationtypeid) {
    //             $query->where('des.majorworkallocationtypeid', $majorworkallocationtypeid);
    //         })
    //         ->orderBy('des.majorworkallocationtypeid', 'desc');

    //     return $query->get();
    // }

    public static function getgroupDet($table, $deptcode)
    {
        return DB::table($table)
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->distinct()
            ->get();
    }

    public static function fetchCategoryData($table, $deptcode)
    {
        return DB::table($table)
            // ->join('audit.mst_auditeeins_category as mac', 'md.deptcode', '=', 'mac.deptcode')
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->select('deptcode', 'catcode', 'cattname', 'catename', 'auditeeins_categoryid', 'if_subcategory')
            ->distinct()
            ->orderBy('catename', 'Asc')
            ->get();
    }

    public static function fetchMajorWorkallocationData($table, $deptcode)
    {
        return DB::table($table)
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->select('deptcode', 'majorworkallocationtypeid', 'majorworkallocationtypeename', 'majorworkallocationtypetname')
            ->distinct()
            ->orderBy('majorworkallocationtypeename', 'Asc')
            ->get();
    }

    public static function fetchcallforrecordDatabyDept($table, $deptcode)
    {
        return DB::table($table . ' as cr')
            ->Join('audit.mst_dept as dept', 'cr.deptcode', '=', 'dept.deptcode')
            //  ->Join('audit.map_allocation_objection as map', 'cr.callforrecordsid', '=', 'map.mapcallforrecordsid')
            ->select('cr.callforrecordsid', 'cr.callforrecordsename', 'cr.callforrecordstname')
            ->where('dept.deptcode', $deptcode)
            ->where('cr.statusflag', 'Y')
            ->orderBy('cr.callforrecordsename', 'Asc')
            ->distinct()
            ->get();
    }

    public static function mainobjectionData($table, $deptcode)
    {
        return DB::table($table)
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->select('deptcode', 'mainobjectionid', 'objectionename', 'objectiontname')
            ->distinct()
            ->orderBy('objectionename', 'Asc')
            ->get();
    }

    // public static function getsubobjection($table, $deptcode)
    // {
    //     return   DB::table($table)
    //         // ->leftjoin('audit.mst_mainobjection as main', 'main.mainobjectionid', '=', 'sub.mainobjectionid')
    //         ->where('statusflag', 'Y')
    //         ->where('deptcode', $deptcode)
    //         ->select('subobjectionid', 'subobjectionename', 'subobjectiontname',)
    //         ->distinct()
    //         ->get();
    // }
    public static function getsubobjection($table, $mainobjectionid)
    {
        return DB::table($table . ' as sub')
            ->leftjoin('audit.mst_mainobjection as main', 'main.mainobjectionid', '=', 'sub.mainobjectionid')
            ->where('sub.statusflag', 'Y')
            ->where('sub.mainobjectionid', $mainobjectionid)
            ->select(
                'sub.subobjectionid',
                'sub.subobjectionename',
                'sub.subobjectiontname',
            )
            ->distinct()
            ->get();
    }

    public static function fetch_mapInstDet($table, $id = null, $deptcode, $quarter, $auditmode, $formtype)
    {
        try {
            $session = session('charge');

            // Session variables with null coalescing for safety
            $sessiondeptcode = $session->deptcode ?? null;
            $sessiondistcode = $session->distcode ?? null;
            $sessionregioncode = $session->regioncode ?? null;
            $sessionroletypecode = $session->roletypecode ?? null;

            // Start with base query
            $coreQuery = DB::table('audit.mst_institution as inst')
                ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'inst.deptcode')
                ->join('audit.mst_region as region', 'region.regioncode', '=', 'inst.regioncode')
                ->join('audit.mst_district as dist', 'dist.distcode', '=', 'inst.distcode')
                ->join('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', 'inst.catcode')
                ->leftJoin('audit.mst_auditeeins_subcategory as subcat', 'subcat.auditeeins_subcategoryid', '=', 'inst.subcatid')
                ->leftJoin('audit.mst_hubdesig as hub', 'hub.desigcode', '=', 'inst.hubdesigcode')
                ->leftJoin('audit.auditor_instmapping as instmap', 'instmap.instmappingcode', '=', 'inst.auditoffice')
                ->leftJoin('audit.deptuserdetails as userdet', 'userdet.deptuserid', '=', 'inst.auditaduserid')
                ->leftJoin('audit.mst_hriazone as z', 'z.zonecode', '=', 'inst.zonecode')
                ->join('audit.mst_revenuedistrict as reven', 'reven.revenuedistcode', '=', 'inst.revenuedistcode')
                ->join('audit.audtieeuserdetails as users', 'users.instid', '=', 'inst.instid')
                ->leftJoin('audit.mst_auditeedept as audept', 'audept.auditeedeptcode', '=', 'inst.auditeedeptcode')
                ->leftJoin('audit.mst_typeofaudit as atype', 'atype.typeofauditcode', '=', 'inst.typeofauditcode');

            // Select fields based on formtype
            if ($formtype == 'fetch') {
                // Fetch select fields
                $fetchFields = [
                    'subcat.regex',
                    'inst.*',
                    'dept.deptesname',
                    'dept.depttsname',
                    'cat.catename',
                    'cat.cattname',
                    'cat.if_subcategory',
                    'region.regionename',
                    'region.regiontname',
                    'dist.distename',
                    'dist.disttname',
                    'subcat.auditeeins_subcategoryid',
                    'subcat.subcattname',
                    'subcat.subcatename',
                    'inst.mandays',
                    'audept.auditeedeptename',
                    'audept.auditeedepttname',
                    'dept.deptelname',
                    'dept.depttlname',
                    'hub.desigename',
                    'z.zoneename',
                    'z.zonetname',
                    'z.zonecode',
                    'hub.desigtname',
                    'instmap.instename as auditinst_ename',
                    'instmap.insttname as auditinst_tname',
                    'userdet.username',
                    'userdet.usertamilname',
                    'users.auditeeuserid',
                    'atype.typeofauditename',
                    'atype.typeofaudittname',
                    'reven.revenuedistename',
                    'reven.revenuedisttname',
                    'dist.distcode',
                    'inst.q1spillover',
                    DB::raw("
                    TRIM(BOTH ',' FROM
                        CONCAT_WS(',',
                            CASE WHEN \"Q1\" = 'Y' THEN 'Q1' END,
                            CASE WHEN \"Q2\" = 'Y' THEN 'Q2' END,
                            CASE WHEN \"Q3\" = 'Y' THEN 'Q3' END,
                            CASE WHEN \"Q4\" = 'Y' THEN 'Q4' END
                        )
                    ) AS auditquarter
                "),
                ];
                $coreQuery->select($fetchFields);
            } else {
                // Default select fields
                $commonFields = [
                    'inst.*',
                ];

                $joinedFields = [
                    'audept.auditeedeptename',
                    'audept.auditeedepttname',
                    'dept.deptelname',
                    'dept.depttlname',
                    'dept.deptesname',
                    'dept.depttsname',
                    'hub.desigename',
                    'hub.desigtname',
                    'instmap.instename as auditinst_ename',
                    'instmap.insttname as auditinst_tname',
                    'userdet.username',
                    'userdet.usertamilname',
                    'users.auditeeuserid',
                    'cat.catename',
                    'cat.cattname',
                    'cat.if_subcategory',
                    'region.regionename',
                    'region.regiontname',
                    'atype.typeofauditename',
                    'atype.typeofaudittname',
                    'reven.revenuedistename',
                    'reven.revenuedisttname',
                    'dist.distcode',
                    'dist.distename',
                    'dist.disttname',
                    'z.zoneename',
                    'z.zonetname',
                    'z.zonecode',
                    'subcat.auditeeins_subcategoryid',
                    'subcat.subcattname',
                    'subcat.subcatename',
                ];

                // Combine all fields
                $allFields = array_merge($commonFields, $joinedFields);

                // Add computed fields
                $allFields[] = DB::raw("
                TRIM(BOTH ',' FROM
                    CONCAT_WS(',',
                        CASE WHEN \"Q1\" = 'Y' THEN 'Q1' END,
                        CASE WHEN \"Q2\" = 'Y' THEN 'Q2' END,
                        CASE WHEN \"Q3\" = 'Y' THEN 'Q3' END,
                        CASE WHEN \"Q4\" = 'Y' THEN 'Q4' END
                    )
                ) AS auditquarter
            ");

                $allFields[] = DB::raw("
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM audit.temp_inst_q1_pending temp
                        WHERE temp.instid = inst.instid
                    ) THEN 'Y' ELSE 'N'
                END AS pendingflag
            ");
                $allFields[] = DB::raw("CASE
                            WHEN EXISTS (
                                SELECT 1
                                FROM audit.team_assignments te
                                inner join audit.mst_institution inst2 on inst2.instid =te.instid
                                where inst2.deptcode = inst.deptcode and inst.distcode = inst2.distcode

                            ) THEN 'Y' ELSE 'N'
                         END AS checklist_dist");
               
$allFields[] = DB::raw("CASE
                            WHEN EXISTS (
                                SELECT 1
                                FROM audit.trans_followup fp
                                inner join audit.mst_institution inst3 on inst3.instid =fp.instid
                                where inst3.statusflag = 'Y' and fp.statusflag = 'F'

                            ) THEN 'Y' ELSE 'N'
                         END AS apms_inst");
 $coreQuery->select($allFields);
            }
		

            // Apply common conditions

            // Filter by session department and quarter
            if ($sessiondeptcode) {
                $coreQuery
                    ->where('inst.deptcode', $sessiondeptcode)
                    ->where("inst.$quarter", 'Y');
            }

            // Filter by audit mode
            if ($auditmode) {
                $coreQuery->where('inst.auditmode', $auditmode);
            }

            // Filter for role type '04'
            if ($sessionroletypecode === '04') {
                $coreQuery
                    ->where('inst.deptcode', $deptcode)
                    ->where("inst.$quarter", 'Y');
            }

            // Filter by region
            if ($sessionregioncode) {
                $coreQuery->where('inst.regioncode', $sessionregioncode);
            }

            // Filter by district
            if ($sessiondistcode) {
                $coreQuery->where('inst.distcode', $sessiondistcode);
            }

            // Filter by ID
            if ($id) {
                $coreQuery->where('inst.instid', $id);
            }

            // Return based on formtype
            if ($formtype == 'fetch') {
                return $coreQuery->cursor();
            } else {
                return $coreQuery->get();
            }
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('SQL Error in fetch_mapInstDet: ' . $e->getMessage());
            throw new \Exception('A database error occurred while fetching the Institution Details. Please contact the administrator.', 500);
        }
    }

    public static function fetch_auditreportDet($id = null, $deptcode, $quarter, $auditmode)
    {
        $session = session('charge');
        $sessiondeptcode = $session->deptcode;
        $sessionroletypecode = $session->roletypecode;

        $quarterdetails = self::getoldandnewquarter();
        $quatdetails = $quarterdetails[0];

        $currentquarter = $quatdetails->currentquarter;
        $nextquarter = $quatdetails->nextquarter;

        $query = DB::table(self::$auditeedeptreport_table . ' as rep')
            ->Join(self::$inst_table . ' as inst', 'rep.instid', '=', 'inst.instid')
            ->leftJoin(self::$auditeedept_table . ' as auddept', 'auddept.auditeedeptcode', '=', 'rep.auditeedeptcode')
            ->orderby('auditeedesigid', 'asc')
            ->where('rep.statusflag', 'Y')
            ->select('rep.email', 'rep.mobilenumber', 'rep.auditeedesigid', 'rep.auditeedeptcode', 'rep.designation')
            // ->where('inst.deptcode', $deptcode)
            ->where('inst.statusflag', 'Y')
            ->when($sessionroletypecode == '04', function ($q) use ($quarter, $deptcode) {
                return $q
                    ->where('rep.deptcode', $deptcode)
                    ->where("inst.$quarter", 'Y');
            })
            ->when($auditmode, function ($query) use ($auditmode) {
                $query->where('inst.auditmode', '=', $auditmode);
            })
            ->when($id, function ($query) use ($id) {
                $query->where('rep.instid', '=', $id);
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode, $quarter) {
                $query
                    ->where('rep.deptcode', '=', $sessiondeptcode)
                    ->where('inst.' . $quarter, 'Y');
            })
            ->get();  // get() should be the last method

        return $query;
    }

    public static function institute_insertupdate($auditee_reportdata, $data, $instid, $table, $userid, $auditeeuserid)
    {
        DB::beginTransaction();  // Begin the transaction

        try {
            $query = DB::table($table);

            if ($instid) {
                $query->where('instid', '!=', $instid);
            }

            $ename = strtolower(str_replace(' ', '', $data['instename']));
            $tname = strtolower(str_replace(' ', '', $data['insttname']));

            // $rankExists = (clone $query)
            //     ->where('rankorder', $data['rankorder'])
            //     ->where('statusflag', 'Y')
            //     ->where('deptcode', $data['deptcode'])
            //     ->where('distcode', $data['distcode'])
            //     ->where('Q2', 'Y')
            //     ->exists();

            if ($data['auditmode'] == 'T') {
                $emailExists = 0;
            } else {
                $emailExists = (clone $query)->where('email', $data['email'])->where('statusflag', 'Y')->exists();
            }
            $enameExists = (clone $query)->whereRaw("LOWER(REPLACE(instename, ' ', '')) = ?", [$ename])->where('statusflag', 'Y')->exists();
            $tnameExists = (clone $query)->whereRaw("LOWER(REPLACE(insttname, ' ', '')) = ?", [$tname])->where('statusflag', 'Y')->exists();

            // if ($rankExists) {
            //     throw new \Exception('Rank Order Already Exists');
            // }

            if ($emailExists) {
                throw new \Exception('Email Already Exists');
            }

            if ($enameExists) {
                throw new \Exception('Institution English Name Already Exists');
            }
            if ($tnameExists) {
                throw new \Exception('Institution Tamil Name Already Exists');
            }

            if ($instid) {
                $data['updatedby'] = $userid;
                $data['updatedon'] = View::shared('get_nowtime');

                DB::table($table)->where('instid', $instid)->update($data);

                // Fetch existing records
                $existingreportRecords = DB::table(self::$auditeedeptreport_table)
                    ->where('deptcode', $auditee_reportdata['deptcode'])
                    ->where('instid', $instid)
                    ->where('statusflag', 'Y')
                    ->get();

                // Fetch existing designations from DB
                $existingauditreportdesigIDs = DB::table(self::$auditeedeptreport_table)
                    ->where('instid', $instid)
                    ->orderBy('auditeedesigid')
                    ->pluck('auditeedesigid')
                    ->toArray();

                $existingreportCount = count($existingreportRecords);
                $newreportCount = count($auditee_reportdata['nodaldesignation']);
                $commonreportCount = min($existingreportCount, $newreportCount);

                // Step 2: Update existing members (excluding first, which is already updated)
                for ($i = 1; $i <= $commonreportCount; $i++) {
                    DB::table(self::$auditeedeptreport_table)
                        ->where('instid', $instid)
                        ->where('deptcode', $auditee_reportdata['deptcode'])
                        ->where('auditeedesigid', $existingreportRecords[$i - 1]->auditeedesigid)
                        ->update([
                            'auditeedeptcode' => $auditee_reportdata['auditeedeptcode'],
                            'deptcode' => $auditee_reportdata['deptcode'],
                            'instid' => $instid,
                            'designation' => $auditee_reportdata['nodaldesignation'][$i],
                            'mobilenumber' => $auditee_reportdata['nodalmobile'][$i],
                            'email' => $auditee_reportdata['nodalemail'][$i],
                            'statusflag' => 'Y',
                            'updatedby' => $userid,
                            'updatedon' => View::shared('get_nowtime'),
                        ]);
                }

                // Step 3: Insert new records if new count is greater
                if ($newreportCount > $existingreportCount) {
                    for ($i = $commonreportCount; $i < $newreportCount; $i++) {
                        DB::table(self::$auditeedeptreport_table)->insert([
                            'auditeedeptcode' => $auditee_reportdata['auditeedeptcode'],
                            'deptcode' => $auditee_reportdata['deptcode'],
                            'instid' => $instid,
                            'designation' => $auditee_reportdata['nodaldesignation'][$i + 1],
                            'mobilenumber' => $auditee_reportdata['nodalmobile'][$i + 1],
                            'email' => $auditee_reportdata['nodalemail'][$i + 1],
                            'statusflag' => 'Y',
                            'createdby' => $userid,
                            'createdon' => View::shared('get_nowtime'),
                            'updatedby' => $userid,
                            'updatedon' => View::shared('get_nowtime'),
                        ]);
                    }
                }

                // Step 4: Delete extra records if new count is smaller
                if ($newreportCount < $existingreportCount) {
                    $idsToDelete = array_slice($existingauditreportdesigIDs, $newreportCount);
                    if (!empty($idsToDelete)) {
                        DB::table(self::$auditeedeptreport_table)
                            ->where('instid', $instid)
                            ->whereIn('auditeedesigid', $idsToDelete)
                            ->update([
                                'statusflag' => 'N',
                                'updatedby' => $userid,
                                'updatedon' => View::shared('get_nowtime'),
                            ]);
                    }
                }

                // Update auditee user details
                if ($auditeeuserid) {
                    $auditeeuserdetails = [
                        'instid' => $instid,
                        'username' => $data['nodalperson_ename'],
                        'email' => $data['email'],
                        'mobilenumber' => $data['mobile'],
                        'chargeid' => View::shared('Auditeechargeid'),
                        'statusflag' => $data['statusflag'],
                        'updatedby' => $userid,
                        'updatedon' => View::shared('get_nowtime'),
                    ];

                    DB::table(self::$auditeeuserdetail)->where('auditeeuserid', $auditeeuserid)->update($auditeeuserdetails);
                    DB::commit();  // Commit transaction
                    return DB::table(self::$auditeeuserdetail)->where('auditeeuserid', $auditeeuserid)->first();
                }
            } else {
                $data['createdby'] = $userid;
                $data['createdon'] = View::shared('get_nowtime');
                $instId = DB::table($table)->insertGetId($data, 'instid');

                if (empty($instId)) {
                    throw new \Exception('Failed to Insert Institution Details');
                }

                try {
                    if ($instId) {
                        $lastInstId = DB::table($table)->where('instid', $instId)->first(['instid']);
                    }

                    $auditeeuserdetails = [];
                    $hashpass = Hash::make(View::shared('auditeedefaultPass'));
                    $auditeeuserdetails['instid'] = $lastInstId->instid;
                    $auditeeuserdetails['username'] = $data['nodalperson_ename'];
                    $auditeeuserdetails['email'] = $data['email'];
                    $auditeeuserdetails['mobilenumber'] = $data['mobile'];
                    $auditeeuserdetails['pwd'] = $hashpass;
                    $auditeeuserdetails['chargeid'] = View::shared('Auditeechargeid');
                    $auditeeuserdetails['profile_update'] = 'Y';
                    $auditeeuserdetails['statusflag'] = $data['statusflag'];
                    $auditeeuserdetails['createdby'] = $userid;
                    $auditeeuserdetails['createdon'] = View::shared('get_nowtime');
                    $instuserdet = DB::table('audit.audtieeuserdetails')->insertGetId($auditeeuserdetails, 'auditeeuserid');

                    foreach ($auditee_reportdata['auditee_report'] as $key => $val) {
                        DB::table(self::$auditeedeptreport_table)->insert([
                            'auditeedeptcode' => $auditee_reportdata['auditeedeptcode'],
                            'deptcode' => $auditee_reportdata['deptcode'],
                            'instid' => $lastInstId->instid,
                            'designation' => $auditee_reportdata['nodaldesignation'][$key],
                            'mobilenumber' => $auditee_reportdata['nodalmobile'][$key],
                            'email' => $auditee_reportdata['nodalemail'][$key],
                            'statusflag' => 'Y',
                            'createdby' => $userid,
                            'createdon' => View::shared('get_nowtime'),
                            'updatedby' => $userid,
                            'updatedon' => View::shared('get_nowtime'),
                        ]);
                    }

                    DB::commit();  // Commit transaction
                } catch (\Exception $e) {
                    DB::rollBack();  // Rollback transaction on error
                    throw $e;
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();  // Rollback transaction on error
            throw new \Exception($e->getMessage(), 409);
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching the Institution Details. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        }
    }

    public static function catDet($table, $deptcode)
    {
        return DB::table($table . ' as md')
            ->join('audit.mst_auditeeins_category as mac', 'md.deptcode', '=', 'mac.deptcode')
            ->where('md.deptcode', $deptcode)
            ->select('md.deptcode', 'mac.catcode', 'mac.cattname', 'mac.catename', 'mac.auditeeins_categoryid', 'md.membercount', 'mac.if_subcategory')
            ->distinct()
            ->orderBy('mac.catename', 'asc')
            ->get();
    }

    public static function regionDet($table, $deptcode)
    {
        return DB::table($table . ' as rtm')
            ->join(self::$region_table . ' as rt', 'rt.deptcode', '=', 'rtm.parentcode')
            ->join(self::$department_table . ' as md', 'md.deptcode', '=', 'rtm.parentcode')
            ->join(self::$auditeinstmap . ' as map', 'map.regioncode', '=', 'rt.regioncode')
            ->where('map.statusflag', 'Y')
            ->where('rtm.deptcode', $deptcode)
            // ->where('rtm.roletypecode', $request->roletypecode)
            ->select('md.deptcode', 'rt.regionename', 'rt.regiontname', 'rt.regioncode')
            ->distinct()
            ->orderBy('rt.regionename', 'asc')
            ->get();
    }

    public static function designationDet($table, $deptcode)
    {
        return DB::table($table . ' as desig')
            ->where('desig.deptcode', $deptcode)
            ->where('desig.statusflag', 'Y')
            // ->where('rtm.roletypecode', $request->roletypecode)
            ->select('desig.desigid', 'desig.desigcode', 'desig.desigelname', 'desig.desigtlname', 'desig.desigesname', 'desigtsname')
            ->distinct()
            ->get();
    }

    public static function getaudieedept($deptcode)
    {
        return DB::table(self::$auditeedept_table . ' as dept')
            ->where('dept.deptcode', $deptcode)
            ->where('dept.statusflag', 'Y')
            ->select('dept.auditeedeptcode', 'dept.auditeedeptename', 'dept.auditeedepttname')
            ->distinct()
            ->get();
    }

    public static function gettypeofaudit($deptcode)
    {
        return DB::table(self::$audittype_table . ' as type')
            ->where('type.deptcode', $deptcode)
            ->where('type.statusflag', 'Y')
            ->select('type.typeofauditcode', 'type.typeofaudittname', 'type.typeofauditename')
            ->distinct()
            ->get();
    }

    public static function subCateDet($table, $catcode)
    {
        return DB::table($table . ' as subcat')
            ->join('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', 'subcat.catcode')
            // ->join(self::$department_table . ' as md', 'md.deptcode', '=', 'rtm.parentcode')
            ->where('cat.catcode', $catcode)
            ->where('subcat.statusflag', 'Y')
            // ->where('rtm.roletypecode', $request->roletypecode)
            ->select('subcat.auditeeins_subcategoryid', 'subcat.subcatename', 'subcat.subcattname', 'subcat.totalins')
            ->distinct()
            ->get();
    }

    public static function districtDet($table, $regioncode, $deptcode)
    {
        return DB::table($table . ' as dist')
            ->join(self::$auditeinstmap . '  as map', 'map.distcode', '=', 'dist.distcode')
            ->where('map.regioncode', $regioncode)
            ->where('map.deptcode', $deptcode)
            ->where('map.statusflag', 'Y')
            ->where('dist.statusflag', 'Y')
            ->select('dist.distename', 'dist.disttname', 'dist.distcode')
            ->distinct()
            ->get();
        //  $querySql = $result->toSql();

        // $querySql = $result->toSql();
        // $bindings = $result->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );
        // print_r($finalQuery);
    }

    public static function getOfficeDet($cond)
    {
        return DB::table(self::$auditeinstmap . ' as auditinst')
            ->where('deptcode', $cond['deptcode'])
            ->where('regioncode', $cond['regioncode'])
            ->where('distcode', $cond['distcode'])
            ->where('statusflag', 'Y')
            ->select('instename', 'insttname', 'instmappingcode')
            ->get();
    }

    public static function getauditordetbasedon_inst($cond)
    {
        return DB::table(self::$auditeinstmap . ' as auditinst')
            ->join(self::$designation . '  as desig', 'auditinst.nodalperson_desigcode', '=', 'desig.desigcode')
            ->where('auditinst.deptcode', $cond['deptcode'])
            ->where('auditinst.regioncode', $cond['regioncode'])
            ->where('auditinst.distcode', $cond['distcode'])
            ->where('auditinst.instmappingcode', $cond['instmappingcode'])
            ->where('auditinst.statusflag', 'Y')
            ->select(
                'auditinst.nodalperson_desigcode',
                'auditinst.instmappingcode',
                'desig.desigcode',
                'desig.desigelname',
                'desig.desigtlname',
            )
            ->get();
    }

    public static function auditofficerDet($cond)
    {
        return DB::table(self::$userdetail_table . ' as users')
            ->join(self::$userchargedetail_table . '  as uc', 'uc.userid', '=', 'users.deptuserid')
            ->join(self::$chargedetail_table . '  as c', 'c.chargeid', '=', 'uc.chargeid')
            ->join(self::$auditeinstmap . '  as auditinst', 'users.desigcode', '=', 'auditinst.nodalperson_desigcode')
            ->where('c.instmappingcode', $cond['instmappingcode'])
            ->where('c.desigcode', $cond['desigcode'])
            ->where('c.deptcode', $cond['deptcode'])
            ->where('c.distcode', $cond['distcode'])
            ->where('c.regioncode', $cond['regioncode'])
            // ->where('users.chargeassigned', 'Y')
            // ->where('users.auditorflag', 'Y')
            ->where('users.statusflag', 'Y')
            ->where('uc.statusflag', 'Y')
            ->select('users.username', 'users.usertamilname', 'users.deptuserid')
            ->distinct()
            ->get();
        // $querySql = $result->toSql();

        // $querySql = $result->toSql();
        // $bindings = $result->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );
        // print_r($finalQuery);
    }

    // protected static $usertypetable = BaseModel::USERTYPE_TABLE;

    // protected static $roletypetable = BaseModel::ROLETYPE_TABLE;

    public static function FetchDepartment()
    {
        return DB::table(self::$auditeecategory . ' as audi_cat')
            ->join(self::$deptTable . ' as dept', 'audi_cat.deptcode', '=', 'dept.deptcode')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname')
            ->distinct()
            ->where('audi_cat.statusflag', '=', 'Y')
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }

    public static function checkDuplicateForsubcat($categorycode, $ename, $tname, $subcategoryid = null)
    {
        $table = self::$subcategory;

        $query = DB::table($table)
            ->where('catcode', $categorycode)
            ->where(function ($q) use ($ename, $tname) {
                $q
                    ->whereRaw("LOWER(REPLACE(subcatename, ' ', '')) = ?", [$ename])
                    ->orWhereRaw("LOWER(REPLACE(subcattname, ' ', '')) = ?", [$tname]);
            });

        if ($subcategoryid) {
            $query->where('auditeeins_subcategoryid', '<>', $subcategoryid);
        }

        $duplicates = $query->get();

        $isEnameDuplicate = false;
        $isTnameDuplicate = false;

        foreach ($duplicates as $duplicate) {
            if (strtolower(str_replace(' ', '', $duplicate->subcatename)) === $ename) {
                $isEnameDuplicate = true;
            }
            if (strtolower(str_replace(' ', '', $duplicate->subcattname)) === $tname) {
                $isTnameDuplicate = true;
            }
        }

        return ['ename' => $isEnameDuplicate, 'tname' => $isTnameDuplicate];
    }

    public static function subcategory_insertupdate(array $data, $subcategoryid = null)
    {
        $table = self::$subcategory;
        try {
            $query = DB::table($table);
            if ($subcategoryid) {
                $query->where('auditeeins_subcategoryid', '<>', $subcategoryid);
            }
            if ($subcategoryid) {
                $affectedRows = DB::table($table)->where('auditeeins_subcategoryid', $subcategoryid)->update($data);
                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'auditeeins_subcategoryid');
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getSubcategoryforrecords($subcategorydid = null)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;

        $table = self::$subcategory;

        $query = DB::table($table . ' as des')
            ->leftJoin(self::$auditeecategory . ' as aud_cat', 'des.catcode', '=', 'aud_cat.catcode')
            ->join(self::$deptTable . '  as d', 'aud_cat.deptcode', '=', 'd.deptcode')
            ->select(
                'des.*',
                'd.deptelname',
                'd.deptesname',
                'd.depttsname',
                'aud_cat.deptcode',
                'aud_cat.catename',
                'aud_cat.cattname',
DB::raw("
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM audit.map_allocation_objection mao
                        WHERE mao.auditeeins_subcategoryid = des.auditeeins_subcategoryid
                        AND mao.statusflag = 'Y'
                    )

                    OR EXISTS (
                        SELECT 1
                        FROM audit.mst_institution mi
                        WHERE mi.subcatid = des.auditeeins_subcategoryid
                    )

                    THEN 'Y'
                    ELSE 'N'
                END as isexists
                ")
            )
            ->when($subcategorydid, function ($query) use ($subcategorydid) {
                $query->where('des.auditeeins_subcategoryid', $subcategorydid);
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
                $query->where('d.deptcode', '=', $sessiondeptcode);
            });
        $query->orderBy('des.updatedon', 'desc');

        return $query->get();
    }

    // public static function check_mapAllcObj($data)
    // {
    //     // return $data;
    //     try {
    //         $existingIndexes = [];
    //         foreach ($data as $dataIndex => $tabledatas) {
    //             foreach ($tabledatas['work_allocations'] as $workIndex => $work) {
    //                 // Check for existence in the table
    //                 $exists = DB::table(self::$mapallocationobjection_table)
    //                     ->where('catcode', $tabledatas['category'])
    //                     ->where(function ($query) use ($tabledatas) {
    //                         // Handle optional subcategory
    //                         if (!empty($tabledatas['subcategory'])) {
    //                             $query->where('auditeeins_subcategoryid', $tabledatas['subcategory']);
    //                         } else {
    //                             $query->whereNull('auditeeins_subcategoryid');
    //                         }
    //                     })
    //                     // ->where('auditeeins_subcategoryid', $tabledatas['subcategory'])
    //                     ->where('groupid', $tabledatas['group'])
    //                     ->where('mapcallforrecordsid', $tabledatas['call_for_records'])
    //                     ->where('majorworkallocationtypeid', $work['main_work'])
    //                     ->where(function ($query) use ($work) {
    //                         // Handle optional subworkallocationtypeid
    //                         if (!empty($work['sub_work'])) {
    //                             $query->where('subworkallocationtypeid', $work['sub_work']);
    //                         } else {
    //                             $query->whereNull('subworkallocationtypeid');
    //                         }
    //                     })
    //                     ->where('mainobjectionid', $work['main_obj'])
    //                     ->where('subobjectionid', $work['sub_obj'])
    //                     // ->where(function ($query) use ($work) {
    //                     //     // Handle optional subobjectionid
    //                     //     if (!empty($work['sub_obj'])) {
    //                     //         $query->where('subobjectionid', $work['sub_obj']);
    //                     //     } else {
    //                     //         $query->whereNull('subobjectionid');
    //                     //     }
    //                     // })
    //                     ->where('allocatetowhom', $work['allocate_towhom'])
    //                     ->exists();

    //                 if ($exists) {
    //                     // echo 'exits';
    //                     $existingIndexes[] = "dataIndex: $dataIndex, workIndex: $workIndex";
    //                     // print_r($existingIndexes);
    //                 } else {
    //                     // echo 'notexits';
    //                     $existingIndexes = [];
    //                 }
    //             }
    //         }
    //         return $existingIndexes;

    //         // return;
    //     } catch (\Exception $e) {
    //         // ✅ Error Handling: Log and return error message
    //         \Log::error('Error inserting data: ' . $e->getMessage());
    //         return response()->json(['error' => 'Something went wrong.'], 500);
    //     }
    // }

    public static function check_mapAllcObj($data)
    {
        try {
            $existingIndexes = [];

            foreach ($data as $dataIndex => $tabledatas) {
                if (is_array($tabledatas['category'])) {
                    $categories = $tabledatas['category'];
                } else {
                    $categories = [$tabledatas['category']];
                }

                if (!empty($tabledatas['subcategory'])) {
                    if (is_array($tabledatas['subcategory'])) {
                        $subcategories = array_filter($tabledatas['subcategory'], function ($val) {
                            return !empty($val);
                        });

                        if (empty($subcategories)) {
                            $subcategories = [null];
                        }
                    } else {
                        $subcategories = [$tabledatas['subcategory']];
                    }
                } else {
                    $subcategories = [null];
                }

                foreach ($categories as $category) {
                    foreach ($subcategories as $subcategory) {
                        foreach ($tabledatas['work_allocations'] as $workIndex => $work) {
                            $exists = DB::table(self::$mapallocationobjection_new_table)
                                ->where('catcode', $category)
                                ->where(function ($query) use ($subcategory) {
                                    if (!empty($subcategory)) {
                                        $query->where('auditeeins_subcategoryid', $subcategory);
                                    } else {
                                        $query->whereNull('auditeeins_subcategoryid');
                                    }
                                })
                                ->where('groupid', $tabledatas['group'])
                                ->where('mapcallforrecordsid', $tabledatas['call_for_records'])
                                ->where('majorworkallocationtypeid', $work['main_work'])
                                ->where(function ($query) use ($work) {
                                    if (!empty($work['sub_work'])) {
                                        $query->where('subworkallocationtypeid', $work['sub_work']);
                                    } else {
                                        $query->whereNull('subworkallocationtypeid');
                                    }
                                })
                                ->where('mainobjectionid', $work['main_obj'])
                                ->where('subobjectionid', $work['sub_obj'])
                                ->where('allocatetowhom', $work['allocate_towhom'])
                                ->exists();

                            if ($exists) {
                                $existingIndexes[] = "dataIndex: $dataIndex, workIndex: $workIndex";
                            }
                        }
                    }
                }
            }

            return $existingIndexes;
        } catch (\Exception $e) {
            \Log::error('Error inserting data: ' . $e->getMessage());
            return [];
        }
    }

    public static function getsubworkallocationbasedondept($deptcode)
    {
        // return $deptcode;
        $table = self::$subworkallocation;

        return DB::table($table . ' as w')
            ->select('w.subworkallocationtypeid', 'w.subworkallocationtypeename', 'w.subworkallocationtypetname')
            ->distinct()
            ->where('w.deptcode', '=', $deptcode)
            ->where('w.statusflag', 'Y')
            ->orderBy('w.subworkallocationtypeename', 'Asc')
            ->get();
    }

    public static function getsubobjectionbasedondept($deptcode)
    {
        // return $deptcode;
        $table = self::$subobjection;

        return DB::table($table . ' as s')
            ->select('s.subobjectionid', 's.subobjectionename', 's.subobjectiontname')
            ->distinct()
            ->where('s.deptcode', '=', $deptcode)
            ->where('s.statusflag', 'Y')
            ->orderBy('s.subobjectionename', 'Asc')
            ->get();
    }

    public static function updatesubworkallocation($subworkallocationid, $workallocationid)
    {
        try {
            // Table name for subworkallocation
            $table = self::$subworkallocation;

            // Check if work allocation ID is valid
            if (!$workallocationid) {
                throw new \Exception('Failed to get work allocationid');
            }

            // Check if there are subworkallocationids to process
            if (empty($subworkallocationid)) {
                throw new \Exception('No subworkallocation IDs provided.');
            }

            // Validate that all subworkallocationids are valid and exist
            $validSubworkAllocations = DB::table($table)
                ->whereIn('subworkallocationtypeid', $subworkallocationid)
                ->get();

            // Check if any subworkallocation is found
            if ($validSubworkAllocations->isEmpty()) {
                throw new \Exception('No valid subworkallocation records found.');
            }

            // Prepare the data for batch update
            $data = [];
            foreach ($subworkallocationid as $value) {
                $data[] = [
                    'subworkallocationtypeid' => $value,
                    'majorworkallocationtypeid' => $workallocationid
                ];
            }

            // Perform the batch update in one go
            $affectedRows = DB::table($table)
                ->whereIn('subworkallocationtypeid', $subworkallocationid)
                ->update(['majorworkallocationtypeid' => $workallocationid]);

            // Check if rows were affected
            if ($affectedRows === 0) {
                throw new \Exception('No rows were updated.');
            }

            // Return success response
            return response()->json(['message' => 'Subwork allocation updated successfully.']);
        } catch (\Exception $e) {
            // Catch and rethrow exception with message
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public static function updateobjection($subobjectionid, $objectionid)
    {
        try {
            // Table name for subworkallocation
            $table = self::$subobjection;

            // Check if work allocation ID is valid
            if (!$objectionid) {
                throw new \Exception('Failed to get work allocationid');
            }

            // Check if there are subworkallocationids to process
            if (empty($subobjectionid)) {
                throw new \Exception('No subworkallocation IDs provided.');
            }

            // Validate that all subworkallocationids are valid and exist
            $validSubworkAllocations = DB::table($table)
                ->whereIn('subobjectionid', $subobjectionid)
                ->get();

            // Check if any subworkallocation is found
            if ($validSubworkAllocations->isEmpty()) {
                throw new \Exception('No valid subworkallocation records found.');
            }

            // Prepare the data for batch update
            $data = [];
            foreach ($subobjectionid as $value) {
                $data[] = [
                    'subobjectionid' => $value,
                    'mainobjectionid' => $objectionid
                ];
            }

            // Perform the batch update in one go
            $affectedRows = DB::table($table)
                ->whereIn('subobjectionid', $subobjectionid)
                ->update(['mainobjectionid' => $objectionid]);

            // Check if rows were affected
            if ($affectedRows === 0) {
                throw new \Exception('No rows were updated.');
            }

            // Return success response
            return response()->json(['message' => 'Sub Objection allocation updated successfully.']);
        } catch (\Exception $e) {
            // Catch and rethrow exception with message
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // --------------------------------------Audit Inspection----------------------------------------------------------------------------

    public static function getCategoryforinspection($deptcode)
    {
        $table = self::$auditeecategory;
        return DB::table($table . ' as aud')
            // ->join(self::$subcategory . ' as sub', 'aud.catcode', '=', 'sub.catcode')
            ->select('aud.catcode', 'aud.if_subcategory')
            ->where('aud.deptcode', $deptcode)
            ->where('aud.statusflag', 'Y')
            ->orderBy('aud.catename', 'Asc')
            // dd($date);
            ->get();
    }

    public static function getSubcategoryByCategoryforinspection($category)
    {
        $table = self::$auditeecategory;

        return DB::table($table . ' as aud')
            ->join(self::$subcategory . ' as sub', 'aud.catcode', '=', 'sub.catcode')
            ->select('sub.subcatename', 'sub.subcattname', 'sub.auditeeins_subcategoryid', 'aud.if_subcategory', 'aud.catcode', 'aud.catename', 'aud.if_subcategory', 'aud.cattname')
            ->where('sub.catcode', $category)
            ->where('aud.if_subcategory', 'Y')
            ->orderBy('sub.subcatename', 'Asc')
            // dd($date);
            ->get();
    }

    public static function checkinspectionForDuplicate(array $data, $aifid = null)
    {
        $table = self::$auditinspection_table;
        $heading_en = trim($data['heading_en']);
        $heading_ta = trim($data['heading_ta']);

        $checkpoint_en = trim($data['checkpoint_en']);
        $checkpoint_ta = trim($data['checkpoint_ta']);
        $headingengname = strtolower(str_replace(' ', '', $heading_en));
        $headingtamname = strtolower(str_replace(' ', '', $heading_ta));

        $checkpointen = strtolower(str_replace(' ', '', $checkpoint_en));
        $checkpointa = strtolower(str_replace(' ', '', $checkpoint_ta));

        $commonConditions = DB::table($table)
            ->where('deptcode', $data['deptcode'])
            ->where('desigcode', $data['desigcode'])
            ->where('catcode', $data['catcode'])
            ->where('subcatid', $data['subcatid'])
            ->where('partno', $data['partno'])
            ->where('statusflag', $data['statusflag'])
            ->whereRaw("LOWER(REPLACE(CAST(heading_en AS TEXT), ' ', '')) = ?", [$headingengname])
            ->whereRaw("LOWER(REPLACE(CAST(heading_ta AS TEXT), ' ', '')) = ?", [$headingtamname])
            ->whereRaw("LOWER(REPLACE(CAST(checkpoint_en AS TEXT), ' ', '')) = ?", [$checkpointen])
            ->whereRaw("LOWER(REPLACE(CAST(checkpoint_ta AS TEXT), ' ', '')) = ?", [$checkpointa])
            ->when($aifid, fn($query) => $query->where('aifid', '<>', $aifid))
            ->exists();

        if ($commonConditions) {
            throw new \Exception('AllNamesExist');
        }
    }

    public static function auditinspect_insertupdate(array $data, $aifid = null)
    {
        $table = self::$auditinspection_table;

        try {
            if ($aifid) {
                $affectedRows = DB::table($table)
                    ->where('aifid', $aifid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $aifid;
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'aifid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function auditinspectform_fetchData($supercheckid = null, $deptcode = null, $catcode = null, $subcatecode = null)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;
        $table = self::$auditinspection_table;

        $query = DB::table($table . ' as aud')
            ->Join(self::$deptTable . ' as dept', 'aud.deptcode', '=', 'dept.deptcode')
            ->leftjoin(self::$designation . ' as desig', 'aud.desigcode', '=', 'desig.desigcode')
            ->join(self::$auditeecategory . ' as cat', 'aud.catcode', '=', 'cat.catcode')
            ->leftjoin(self::$subcategory . ' as sub', 'aud.subcatid', '=', 'sub.auditeeins_subcategoryid')
            ->select(
                'desig.desigcode',
                'desig.desigelname',
                'desig.desigtlname',
                'aud.heading_en',
                'aud.heading_ta',
                'aud.partno',
                'aud.checkpoint_en',
                'aud.checkpoint_ta',
                'aud.objectiontype',
                'aud.statusflag',
                'aud.aifid',
                'aud.subcatid',
                'cat.catcode',
                'cat.catename as catengname',
                'cat.cattname as cattamname',
                'cat.if_subcategory as subcategory',
                'sub.subcatename',
                'sub.subcattname',
                'sub.auditeeins_subcategoryid',
                'dept.deptcode',
                'dept.deptesname as deptengsname',
                'dept.deptelname',
                'dept.depttsname as depttamsname',
                'dept.depttlname',
                DB::raw("CASE WHEN cat.if_subcategory = 'N' OR cat.if_subcategory IS NULL THEN cat.catename ELSE sub.subcatename END AS subcategory_ename"),
                DB::raw("CASE WHEN cat.if_subcategory = 'N' OR cat.if_subcategory IS NULL THEN cat.cattname ELSE sub.subcattname END AS subcategory_tname")
            )
            ->when($supercheckid, function ($query) use ($supercheckid) {
                $query->where('aud.aifid', $supercheckid);
            })
            ->when($deptcode, function ($query) use ($deptcode) {
                $query->where('aud.deptcode', '=', $deptcode);
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
                $query->where('dept.deptcode', '=', $sessiondeptcode);
            });

        if ($catcode && $catcode !== 'A') {
            $query->where('aud.catcode', $catcode);
        }

        if ($subcatecode && $subcatecode !== 'A') {
            $query->where('aud.subcatid', $subcatecode);
        }

        $query->orderBy('aud.updatedon', 'desc');

        return $query->get();
    }

    public static function getcategoryByDeptauditinspect($deptcode)
    {
        return DB::table(self::$auditeecategory)
            // ->select()
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->get(['catcode', 'catename', 'cattname', 'if_subcategory']);
    }

    public static function getdesignationByDeptauditinspect($deptcode)
    {
        return DB::table(self::$designation)
            // ->select()
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->where('inspection', 'Y')
            ->get(['desigcode', 'desigelname', 'desigtlname']);
    }

    public static function getParentinst($cond)
    {
        $instid = $cond['instid'];
        $query = DB::table(self::$inst_table);

        if ($instid) {
            $query->where('instid', '!=', $instid);
        }

        $result = $query
            ->where('deptcode', $cond['deptcode'])
            ->where('regioncode', $cond['regioncode'])
            ->where('distcode', $cond['distcode'])
            ->where('statusflag', 'Y')
            ->select('instename', 'insttname', 'instid')
            ->get();  // Get the result of the query

        return $result;
    }

    public static function getauditmodeDetails()
    {
        return DB::table(self::$auditmode_table)
            ->where('statusflag', 'Y')
            ->get();
    }

    public static function getoldandnewquarter()
    {
        $session = session('charge');
        $deptcode = $session->deptcode;
        $query = DB::table(self::$department_table . ' as dept')
            ->select('currentquarter', 'nextquarter')
            ->when($deptcode, fn($q) => $q->where('dept.deptcode', $deptcode))
            ->get();

        return $query;
    }

    public static function commoncategoryfetch($deptcode)
    {
        return DB::table(self::$auditeecategory)
            // ->select()
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->orderby('catename')
            ->get(['catcode', 'catename', 'cattname', 'if_subcategory']);
    }

    public static function commmonSubcategoryByCategory($category)
    {
        $table = self::$auditeecategory;

        return DB::table($table . ' as aud')
            ->join(self::$subcategory . ' as sub', 'aud.catcode', '=', 'sub.catcode')
            ->select('sub.subcatename', 'sub.subcattname', 'sub.auditeeins_subcategoryid', 'aud.if_subcategory', 'aud.catcode', 'aud.catename', 'aud.if_subcategory', 'aud.cattname')
            ->where('sub.catcode', $category)
            ->where('aud.if_subcategory', 'Y')
            ->orderBy('sub.subcatename', 'Asc')
            ->get();
    }

    public static function Fortemplateslip_insertupdate(array $data, $templateslipid = null)
    {
        $table = 'audit.audit_templateslip';

        try {
            if ($templateslipid) {
                $affectedRows = DB::table($table)
                    ->where('tempslipid', $templateslipid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $templateslipid;
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'tempslipid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function templateslip_fetch($tempslipid = null, $table = null)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;
        $table = 'audit.audit_templateslip';

        $query = DB::table($table . ' as aud')
            ->join(self::$deptTable . ' as dept', 'aud.deptcode', '=', 'dept.deptcode')
            ->join(self::$auditeecategory . ' as cat', 'aud.catcode', '=', 'cat.catcode')
            ->leftJoin(self::$subcategory . ' as sub', 'aud.auditeeins_subcategoryid', '=', 'sub.auditeeins_subcategoryid')
            ->select(
                'aud.tempslipid',
                'aud.deptcode',
                'aud.catcode',
                'aud.templateparaename',
                'aud.templateparatname',
                'aud.statusflag',
                'aud.auditeeins_subcategoryid',
                'cat.catcode',
                'cat.catename as catengname',
                'cat.cattname as cattamname',
                'cat.if_subcategory as subcategory',
                'sub.subcatename',
                'sub.subcattname',
                'sub.auditeeins_subcategoryid',
                'dept.deptcode',
                'dept.deptesname as deptengsname',
                'dept.deptelname',
                'dept.depttsname as depttamsname',
                'dept.depttlname',
                DB::raw("CASE WHEN cat.if_subcategory = 'N' OR cat.if_subcategory IS NULL THEN cat.catename ELSE sub.subcatename END AS subcategory_ename"),
                DB::raw("CASE WHEN cat.if_subcategory = 'N' OR cat.if_subcategory IS NULL THEN cat.cattname ELSE sub.subcattname END AS subcategory_tname")
            )
            ->when($tempslipid, fn($q) => $q->where('aud.tempslipid', $tempslipid))
            ->when($sessiondeptcode, fn($q) => $q->where('dept.deptcode', $sessiondeptcode))
            ->orderBy('aud.updatedon', 'desc');

        $results = $query->get();

        $results->transform(function ($item) {
            $item->templateparaename = json_decode($item->templateparaename, true)['content'] ?? '';
            $item->templateparatname = json_decode($item->templateparatname, true)['content'] ?? '';
            return $item;
        });

        return $results;
    }

    public static function getquartersforinst()
    {
        $query = DB::table(self::$auditquarter_table)
            ->where('instflag', 'Y')
            ->where('statusflag', 'Y')
            ->distinct('auditquartercode')
            ->orderBy('auditquartercode', 'asc')
            ->get();

        return $query;
    }

    public static function getAuditdesignationByDept($cond)
    {
        $table = 'audit.mst_hubdesig';

        return DB::table($table . ' as des')
            ->join('audit.mst_nonaudit_hub as hub', 'hub.desigcode', '=', 'des.desigcode')
            ->join('audit.mst_district as dis', 'hub.distcode', '=', 'dis.distcode')
            ->join('audit.mst_dept as dept', 'hub.deptcode', '=', 'dept.deptcode')
            ->join('audit.mst_region as reg', 'hub.regioncode', '=', 'reg.regioncode')
            ->select(
                'dis.distcode',
                'dis.distename',
                'dis.disttname',
                'des.desigcode',
                'des.desigtname',
                DB::raw("CONCAT(des.desigename, ' - ', hub.address, ' (', dis.distename, ')') as desigename")
            )
            ->distinct()
            ->where('dept.deptcode', $cond['deptcode'])
            ->where('hub.regioncode', $cond['regioncode'])
            ->where('dis.distcode', $cond['distcode'])
            ->where('des.statusflag', 'Y')
            ->where('hub.statusflag', 'Y')
            ->where('dis.statusflag', 'Y')
            ->get();
    }

    public static function FornonAuditgetdesignationByDept($deptcode)
    {
        $table = 'audit.mst_hubdesig';

        return DB::table($table . ' as des')
            ->select('des.desigcode', 'des.desigename', 'des.desigtname')
            ->distinct()
            ->where('des.deptcode', $deptcode)
            ->where('des.statusflag', 'Y')
            ->orderBy('des.desigename', 'Asc')
            ->get();
    }

    public static function getcirclezonebasedonregiofornonaudit($commonzone)
    {
        $table = 'audit.mst_hriacircle';

        return DB::table($table . ' as circle')
            ->select('circle.circleename', 'circle.circletname', 'circle.circleid', 'circle.zonecode')
            ->distinct()
            ->where('circle.zonecode', $commonzone)
            ->where('circle.statusflag', 'Y')
            ->orderBy('circle.circleename', 'Asc')
            ->get();
    }

    public static function getcommonzonebasedonregiofornonaudit($distcode)
    {
        $table = 'audit.mst_hriazone';

        return DB::table($table . ' as zone')
            ->select('zone.zoneename', 'zone.zonetname', 'zone.zonecode')
            ->distinct()
            ->where('zone.distcode', $distcode)
            ->where('zone.statusflag', 'Y')
            ->orderBy('zone.zoneename', 'Asc')
            ->get();
    }

    public static function Nonauditablehub_insertupdate(array $data, $hubid = null, $table = null)
    {
        try {
            $table = 'audit.mst_nonaudit_hub';

            // Normalize email
            $email = strtolower(str_replace(' ', '', trim($data['email'] ?? '')));
            $mobilenumber = $data['mobilenumber'];

            $globalEmailExists = DB::table($table)
                ->whereRaw("LOWER(REPLACE(CAST(email AS TEXT), ' ', '')) = ?", [$email])
                ->when($hubid, fn($query) => $query->where('hubid', '<>', $hubid))
                ->exists();

            $mobile = DB::table($table)
                ->where('mobilenumber', $mobilenumber)
                ->when($hubid, fn($query) => $query->where('hubid', '<>', $hubid))
                ->exists();

            $scopedEmailExists = DB::table($table)
                ->where('deptcode', $data['deptcode'])
                ->where('desigcode', $data['desigcode'])
                ->where('mobilenumber', $data['mobilenumber'])
                ->whereRaw("LOWER(REPLACE(CAST(email AS TEXT), ' ', '')) = ?", [$email])
                ->when($hubid, fn($query) => $query->where('hubid', '<>', $hubid))
                ->exists();

            if ($globalEmailExists) {
                throw new \Exception('createUserEmailError');
            }
            if ($mobile) {
                throw new \Exception('createUserMobileExixt');
            }

            if ($scopedEmailExists) {
                throw new \Exception('EmailAlreadyExistsInDeptDesignation');
            }

            if ($hubid) {
                $affectedRows = DB::table($table)
                    ->where('hubid', $hubid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $hubid;
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'hubid');
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function fetchauditablehubData($hubid = null, $table = null)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;

        $query = DB::table($table . ' as hub')
            ->join('audit.mst_dept as d', 'd.deptcode', '=', 'hub.deptcode')
            ->join('audit.mst_hubdesig as desig', 'desig.desigcode', '=', 'hub.desigcode')
            ->join('audit.mst_district as dist', 'dist.distcode', '=', 'hub.distcode')
            ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'hub.regioncode')
            ->leftjoin('audit.mst_hriacircle as circle', 'circle.circleid', '=', 'hub.circleid')
            ->leftjoin('audit.mst_hriazone as zone', 'zone.zonecode', '=', 'hub.zonecode')
            ->select(
                'd.deptcode',
                'd.depttsname',
                'd.deptelname',
                'd.depttlname',
                'd.deptesname',
                'hub.hubid',
                'hub.deptcode',
                'hub.address',
                'hub.distcode',
                'hub.regioncode',
                'hub.desigcode',
                'hub.statusflag',
                'hub.email',
                'hub.zonecode',
                'hub.circleid',
                'hub.mobilenumber',
                'zone.zonecode',
                'zone.zoneename',
                'zone.zonetname',
                'circle.circleid',
                'circle.circleename',
                'circle.circletname',
                'reg.regionename',
                'reg.regiontname',
                'dist.distename',
                'dist.disttname',
                'desig.desigcode',
                'desig.desigename',
                'desig.desigtname',
            )
            ->when($hubid, function ($query) use ($hubid) {
                $query->where('hub.hubid', $hubid);
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
                $query->where('d.deptcode', '=', $sessiondeptcode);
            });
        $query->orderBy('hub.updatedon', 'desc');
        return $query->get();
    }

    public static function createhubdesignation_insertupdate(array $data, $hubdesigid = null, $table = null)
    {
        try {
            $table = 'audit.mst_hubdesig';

            $commonConditions = DB::table($table)
                ->where('deptcode', $data['deptcode'])
                ->where('statusflag', $data['statusflag']);

            $desigename = trim($data['desigename']);
            $desigtname = trim($data['desigtname']);

            $checkEname = strtolower(str_replace(' ', '', $desigename));
            $checkTname = strtolower(str_replace(' ', '', $desigtname));

            $desigename = (clone $commonConditions)
                ->whereRaw("LOWER(REPLACE(CAST(desigename AS TEXT), ' ', '')) = ?", [$checkEname])
                ->when($hubdesigid, fn($query) => $query->where('hubdesigid', '<>', $hubdesigid))
                ->exists();

            $desigtname = (clone $commonConditions)
                ->whereRaw("LOWER(REPLACE(CAST(desigtname AS TEXT), ' ', '')) = ?", [$checkTname])
                ->when($hubdesigid, fn($query) => $query->where('hubdesigid', '<>', $hubdesigid))
                ->exists();

            if ($desigename && $desigtname) {
                throw new \Exception('DesigAllNamesExist');
            } elseif ($desigename) {
                throw new \Exception('DesigEameExist');
            } elseif ($desigtname) {
                throw new \Exception('DesigTnameExist');
            }

            if ($hubdesigid && isset($data['statusflag']) && strtoupper(trim($data['statusflag'])) === 'N') {
                $parentDesigCode = DB::table('audit.mst_hubdesig')
                    ->where('hubdesigid', $hubdesigid)
                    ->value('desigcode');

                if ($parentDesigCode) {
                    $childExists = DB::table('audit.mst_nonaudit_hub')
                        ->where('desigcode', $parentDesigCode)
                        ->exists();

                    if ($childExists) {
                        throw new \Exception('Nonaudithub_error');
                    }
                }
            }

            if ($hubdesigid) {
                $affectedRows = DB::table($table)
                    ->where('hubdesigid', $hubdesigid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $hubdesigid;
            } else {
                $latestCode = DB::table($table)
                    ->whereNotNull('desigcode')
                    ->max(DB::raw('CAST(desigcode AS INTEGER)'));

                $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

                $data['desigcode'] = $newCode;

                $newRecordId = DB::table($table)->insertGetId($data, 'hubdesigid');
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function fetchhubdesignationData($hubdesigid = null, $table = null)
{

    $sessiondet = session('charge');
    $sessiondeptcode =  $sessiondet->deptcode;
    $query = DB::table($table . ' as des')
        ->join('audit.mst_dept as d', 'd.deptcode', '=', 'des.deptcode')
     //   ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'des.desigcode')
        ->select(
            'd.deptcode',
            'd.depttsname',
            'd.deptelname',
            'd.depttlname',
            'd.deptesname',
            'des.hubdesigid',
            'des.deptcode',
            'des.desigename',
            'des.desigtname',
            'des.statusflag',
            DB::raw("
                CASE

                    WHEN EXISTS (
                        SELECT 1
                        FROM audit.mst_nonaudit_hub nh
                        WHERE nh.desigcode = des.desigcode
                    )

                    THEN 'Y'

                    ELSE 'N'

                END as isexists
            ")


        )
        ->when($hubdesigid, function ($query) use ($hubdesigid) {
            $query->where('des.hubdesigid', $hubdesigid);
        })
        ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
            $query->where('d.deptcode', '=', $sessiondeptcode);
        });
    $query->orderBy('des.updatedon', 'desc');
    return $query->get();
}

    public static function commonRegionsByDept($deptcode)
    {
        $table = self::$institution;

        return DB::table($table . ' as ins')
            ->join(self::$regionTable . ' as reg', 'ins.regioncode', '=', 'reg.regioncode')
            ->select('reg.regioncode', 'reg.regionename', 'reg.regiontname')
            ->distinct()
            ->where('ins.deptcode', $deptcode)
            ->where('ins.statusflag', 'Y')
            ->orderBy('reg.regionename', 'Asc')
            ->get();
    }

    public static function commondistrictByregion($regioncode, $deptcode)
    {
        $table = self::$institution;

        return DB::table($table . ' as ins')
            ->join(self::$distTable . ' as dis', 'ins.distcode', '=', 'dis.distcode')
            ->select('dis.distename', 'dis.disttname', 'dis.distcode')
            ->distinct()
            ->where('ins.deptcode', $deptcode)
            ->where('ins.regioncode', $regioncode)
            ->where('ins.statusflag', 'Y')
            ->get();
    }

    public static function get_alterinstdet($deptcode, $quarter, $auditmode)
    {
        $query = DB::table(self::$institution . ' as inst')
            ->Join(self::$deptTable . ' as dept', 'inst.deptcode', '=', 'dept.deptcode')
            ->Join(self::$distTable . ' as dist', 'inst.distcode', '=', 'dist.distcode')
            ->Join(self::$region_table . ' as region', 'inst.regioncode', '=', 'region.regioncode')
            ->Join('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', 'inst.catcode')
            ->leftJoin('audit.mst_auditeeins_subcategory as subcat', 'subcat.auditeeins_subcategoryid', '=', 'inst.subcatid')
            ->join('audit.audtieeuserdetails as users', 'users.instid', '=', 'inst.instid')
            ->leftJoin(self::$alteredinst_table . ' as altins', 'inst.instid', '=', 'altins.instid')
            ->select(
                'altins.*',
                'inst.*',
                'cat.catename',
                'cat.cattname',
                'subcat.subcatename',
                'subcat.subcattname',
                'cat.cattname',
                'region.regionename',
                'region.regiontname',
                'dist.distename',
                'dist.disttname',
                'dept.deptesname',
                'dept.depttsname',
                'altins.statusflag as alterstatus',
                // 'users.nodalperson_ename',
                // 'users.nodalperson_tname',
                // 'users.email',
                // 'users.mobile',
                // 'users.nodalperson_desigcode',
            )
            ->when($deptcode, function ($query) use ($deptcode) {
                $query->where('inst.deptcode', '=', $deptcode);
            })
            ->when($quarter, function ($query) use ($quarter) {
                $query->where('inst.' . $quarter, 'Y');
            })
            ->when($auditmode, function ($query) use ($auditmode) {
                $query->where('inst.auditmode', '=', $auditmode);
            })
            ->whereNotIn('inst.instid', function ($sub) {
                $sub
                    ->select('instid')
                    ->from(self::$alteredinst_table)
                    ->where('statusflag', '=', 'F');
            })
            ->orderByRaw('altins.updatedon IS NULL, altins.updatedon DESC')
            ->orderBy('inst.instename', 'asc')
            ->get();

        return $query;
    }

    public static function get_singlealterinstdet($deptcode, $quarter, $instid, $regioncode, $distcode, $cond, $detailid)
    {
        $query = DB::table(self::$institution . ' as inst')
            ->Join(self::$deptTable . ' as dept', 'inst.deptcode', '=', 'dept.deptcode')
            ->Join(self::$distTable . ' as dist', 'inst.distcode', '=', 'dist.distcode')
            ->Join(self::$region_table . ' as region', 'inst.deptcode', '=', 'region.regioncode')
            ->leftJoin(self::$alteredinst_table . ' as altins', 'inst.instid', '=', 'altins.instid')
            ->Join('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', 'inst.catcode')
            ->leftJoin('audit.mst_auditeeins_subcategory as subcat', 'subcat.auditeeins_subcategoryid', '=', 'inst.subcatid')
            ->join('audit.audtieeuserdetails as users', 'users.instid', '=', 'inst.instid')
            // ->joinSub($instQuartersSql, 'inst_quarters', function ($join) {
            //     $join->on('inst.instid', '=', 'inst_quarters.instid');
            // })
            // ->join('audit.mst_auditquarter as q', function ($join) {
            //     $join->on('q.auditquartercode', '=', 'inst_quarters.auditquartercode')
            //         ->on('q.deptcode', '=', 'inst_quarters.deptcode');
            // })
            ->Leftjoin('audit.mst_typeofaudit as atype', 'atype.typeofauditcode', '=', 'inst.typeofauditcode')
            ->select(
                'altins.*',
                'inst.*',
                'cat.catename',
                'cat.cattname',
                'subcat.subcatename',
                'subcat.subcattname',
                'cat.cattname',
                'region.regionename',
                'region.regiontname',
                'dist.distename',
                'dist.disttname',
                'dept.deptesname',
                'dept.depttsname',
                // 'users.nodalperson_ename',
                // 'users.nodalperson_tname',
                // 'users.email',
                // 'users.mobile',
                // 'users.nodalperson_desigcode',
            )
            ->when($deptcode, function ($query) use ($deptcode) {
                $query->where('inst.deptcode', '=', $deptcode);
            })
            ->when($instid, function ($query) use ($instid) {
                $query->where('inst.instid', '=', $instid);
            })
            ->when($quarter, function ($query) use ($quarter) {
                $query->where('inst.' . $quarter, 'Y');
            })
            ->when($detailid, function ($query) use ($detailid) {
                $query->where('altins.detailid', '=', $detailid);
            })
            ->get();

        $parentinsdet = self::getParentinst($cond);
        $hubdesidetails = self::getAuditdesignationByDept($cond);
        //  return $query;
        $data = [
            'instdata' => $query,
            'parentinsdet' => $parentinsdet,
            'hubdesidetails' => $hubdesidetails
        ];

        return $data;
    }

    public static function insertorupdate_alterInst($data, $detailid, $instchangedata)
    {
        try {
            //   dd($instchangedata);
            //  DB::beginTransaction(); // ⬅ start transaction

            // $check = DB::table(self::$alteredinst_table)

            // if ($detailid) {
            //     $check->where('instid', '!=',  $data['instid']);
            // }

            // $exists = $check->exists();

            // if ($check)
            //     throw new \Exception('The Institution already altered');

            if ($detailid) {
                $affectedRows = DB::table(self::$alteredinst_table)
                    ->where('detailid', $detailid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
            } else {
                $newRecordId = DB::table(self::$alteredinst_table)
                    ->insertGetId($data, 'detailid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                $detailid = $newRecordId;  // Store inserted ID for reference
            }

            if (isset($data['statusflag']) && $data['statusflag'] == 'F') {
                $instdata = [
                    'nodalperson_ename' => $data['new_nodalperson_ename'],
                    'nodalperson_tname' => $data['new_nodalperson_tname'],
                    'nodalperson_desigcode' => $data['new_nodalperson_desigcode'],
                    'email' => $data['new_email'],
                    'mobile' => $data['new_mobile'],
                    'mandays' => $data['new_mandays'],
                    'teamsize' => $data['new_teamsize'],
                    'auditeeofficeaddress' => $data['new_auditeeofficeaddress'],
                    'auditmode' => $data['new_auditmode'],
                    'parentinstid' => $data['new_parentinstid'],
                    'insttype' => $data['new_insttype'],
                    'hubtype' => $data['new_hubtype'],
                    'hubdesigcode' => $data['new_hubdesigcode'],
                    'updatedon' => $data['updatedon'],
                ];
                $instdata = array_merge($instdata, $instchangedata);
                // $quarters = [];

                // foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $quarter) {
                //     if (!empty($instchangedata[$quarter]) && $instchangedata[$quarter] === 'Y') {
                //         $quarters[] = $quarter;
                //     }
                // }
                // dd($instdata);
                // return $instdata;
                $audittedata = [
                    'username' => $data['new_nodalperson_ename'],
                    'email' => $data['new_email'],
                    'mobilenumber' => $data['new_mobile'],
                    'updatedon' => $data['updatedon'],
                ];

                $affectedinstRows = DB::table(self::$inst_table)
                    ->where('instid', $data['instid'])
                    ->update($instdata);

                if ($affectedinstRows === 0) {
                    throw new \Exception('Failed to update the institution record.');
                }

                $affectedauditeeRows = DB::table(self::$auditeeuserdetail)
                    ->where('instid', $data['instid'])
                    ->update($audittedata);

                if ($affectedauditeeRows === 0) {
                    throw new \Exception('Failed to update the Auditee record.');
                }
            }

            //    DB::commit(); // ⬅ commit if all succeed

            return $detailid;  // optionally return inserted/updated detailid
        } catch (\Exception $e) {
            //  DB::rollBack(); // ⬅ rollback on error
            throw new \Exception($e->getMessage(), 409);
        } catch (\Illuminate\Database\QueryException $e) {
            //  DB::rollBack(); // ⬅ rollback on DB error
            $customMessage = 'A database error occurred while inserting altered institution details. Please contact the administrator.';
            throw new \Exception($customMessage, 500);
        }
    }

    public static function hologindetails_insertupdate(array $data, $auditeedeptid = null)
    {
        $table = 'audit.auditee_dept';

        try {
            $commonConditions = DB::table($table)
                ->where('deptcode', $data['deptcode'])
                ->where('statusflag', $data['statusflag']);

            $email = trim($data['email']);
            $mobilenumber = trim($data['mobilenumber']);

            $emailExists = (clone $commonConditions)
                ->whereRaw('LOWER(TRIM(email)) = ?', [strtolower($email)])
                ->when($auditeedeptid, fn($query) => $query->where('auditeedeptid', '<>', $auditeedeptid))
                ->exists();

            $mobileExists = (clone $commonConditions)
                ->where('mobilenumber', $mobilenumber)
                ->when($auditeedeptid, fn($query) => $query->where('auditeedeptid', '<>', $auditeedeptid))
                ->exists();

            if ($emailExists) {
                throw new \Exception('createUserEmailError');
            } elseif ($mobileExists) {
                throw new \Exception('createUserMobileExixt');
            }

            if ($auditeedeptid) {
                $affectedRows = DB::table($table)
                    ->where('auditeedeptid', $auditeedeptid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $auditeedeptid;
            } else {
                $latestCode = DB::table($table)
                    ->whereNotNull('auditeedeptcode')  // Ensure we're considering only non-null values
                    ->max(DB::raw('CAST(auditeedeptcode AS INTEGER)'));

                $newCode = $latestCode !== null ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT) : '01';

                $data['auditeedeptcode'] = $newCode;

                $newRecordId = DB::table($table)->insertGetId($data, 'auditeedeptid');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;  // Return the ID of the newly inserted record
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getdesignationByDeptauditee($deptcode)
    {
        $table = self::$designation;

        return DB::table($table . ' as des')
            ->select('des.desigcode', 'des.desigelname', 'des.desigtlname', 'des.orderid')
            ->distinct()
            ->where('des.deptcode', $deptcode)
            ->where('des.statusflag', 'Y')
            ->where('des.isauditeeedepartment', 'N')
            ->orderBy('des.orderid', 'Asc')
            ->get();
    }

    public static function getDeptauditeedeptbydeptcode($deptcode)
    {
        $table = 'audit.mst_auditeedept';

        return DB::table($table . ' as aud')
            ->select('aud.auditeedeptcode', 'aud.auditeedeptename', 'aud.auditeedepttname')
            ->distinct()
            ->where('aud.deptcode', $deptcode)
            ->where('aud.statusflag', 'Y')
            ->orderBy('aud.auditeedeptename', 'Asc')
            ->get();
    }

    public static function getinstbasedonsubcategoryauditee($deptcode, $catcode, $subcatcode)
    {
        $table = 'audit.auditplan';

        $query = DB::table($table . ' as plan')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'plan.instid')
            ->select('inst.instename', 'inst.insttname', 'inst.instid')
            ->distinct()
            ->where('inst.deptcode', $deptcode)
            // ->where('inst.deptcode', $catcode)
            // ->where('inst.deptcode', $subcatcode)
            ->where('inst.statusflag', 'Y');

        if ($catcode !== 'A') {
            $query->where('inst.catcode', $catcode);
        }

        if (!empty($subcatcode) && !in_array('A', $subcatcode)) {
            $query->whereIn('inst.subcatid', $subcatcode);
        }
        $query->orderBy('inst.instename', 'Asc');
        return $query->get();
    }

    public static function hologindetails_fetchData($auditeedeptid = null, $table = null)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode ?? null;
        $table = 'audit.auditee_dept';

        $query = DB::table($table . ' as aud')
            ->join(self::$deptTable . ' as dept', 'dept.deptcode', '=', 'aud.deptcode')
            ->leftJoin(DB::raw("
        LATERAL (
            SELECT jsonb_array_elements_text(aud.catcode->'1')::text AS catcode
        ) AS catid(catcode)
        "), DB::raw('true'), '=', DB::raw('true'))
            ->leftJoin('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', DB::raw('catid.catcode::text'))
            ->leftJoin(DB::raw("
            LATERAL (
                SELECT value AS subcategoryid
                FROM jsonb_array_elements_text(aud.auditeeins_subcategoryid->'1') AS value
                WHERE value ~ '^\d+\$'
            ) AS subid(subcategoryid)
        "), DB::raw('true'), '=', DB::raw('true'))
            ->leftJoin('audit.mst_auditeeins_subcategory as sub', function ($join) {
                $join->on(DB::raw('sub.auditeeins_subcategoryid'), '=', DB::raw('subid.subcategoryid::int'));
            })
            ->join(self::$designation . ' as desig', 'desig.desigcode', '=', 'aud.designation')
            ->join('audit.mst_auditeedept as mst_aud', 'aud.auditeedepartment', '=', 'mst_aud.auditeedeptcode')
            ->leftJoin(
                DB::raw("
                LATERAL (
                    SELECT value::int AS instid
                    FROM jsonb_array_elements_text(aud.instid->'1') value
                    WHERE value ~ '^[0-9]+\$'
                ) AS instids(instid)
            "),
                DB::raw('true'),
                '=',
                DB::raw('true')
            )
            ->leftJoin('audit.mst_institution as ins', 'ins.instid', '=', DB::raw('instids.instid'))
            ->select(
                'dept.deptcode',
                'dept.deptesname',
                'dept.deptesname',
                'mst_aud.auditeedeptcode',
                'mst_aud.auditeedeptename',
                'mst_aud.auditeedepttname',
                'desig.desigcode',
                'desig.desigelname',
                'aud.designation',
                'aud.instid',
                'ins.instename',
                'cat.if_subcategory',
                'aud.auditeeins_subcategoryid',
                'cat.catcode',
                'aud.catcode',
                'dept.depttsname',
                'dept.depttlname',
                'aud.auditeedeptid',
                'aud.designation',
                'desig.desigelname',
                'desig.desigtlname',
                'aud.email',
                'aud.auditeedepartment',
                // 'aud.auditeedepttname',
                'aud.mobilenumber',
                'aud.statusflag',
                DB::raw("
                CASE
            WHEN (
                SELECT COUNT(*)
                FROM jsonb_array_elements_text(aud.catcode->'1') val
            ) = 1
            AND (
                SELECT val
                FROM jsonb_array_elements_text(aud.catcode->'1') val
                LIMIT 1
            ) = 'A'
            THEN 'All'
            ELSE string_agg(DISTINCT cat.catename, ', ')
        END as category_names
                "),
                DB::raw("
           CASE
        WHEN (
            SELECT COUNT(*)
            FROM jsonb_array_elements_text(aud.catcode->'1') val
        ) = 1
        AND (
            SELECT val
            FROM jsonb_array_elements_text(aud.catcode->'1') val
            LIMIT 1
        ) = 'A'
        THEN 'அனைத்து'
        ELSE string_agg(DISTINCT cat.cattname, ', ')
    END as category_names_tamil
            "),
                DB::raw("
            CASE
                WHEN (
                    SELECT COUNT(*)
                    FROM jsonb_array_elements_text(aud.instid->'1') val
                ) = 1
                AND (
                    SELECT val
                    FROM jsonb_array_elements_text(aud.instid->'1') val
                    LIMIT 1
                ) = 'A'
                THEN 'All'
                ELSE string_agg(DISTINCT ins.instename, ', ')
            END as instname_eng
        "),
                DB::raw("
            CASE
                WHEN (
                    SELECT bool_or(val = 'A')
                    FROM jsonb_array_elements_text(aud.auditeeins_subcategoryid->'1') val
                ) THEN 'All'
                WHEN MAX(cat.if_subcategory) = 'N' OR MAX(cat.if_subcategory) IS NULL
                    THEN NULL
                ELSE string_agg(DISTINCT sub.subcatename, ', ')
            END AS subcategory_names
        "),
                DB::raw("
            CASE
                WHEN (
                    SELECT bool_or(val = 'A')
                    FROM jsonb_array_elements_text(aud.auditeeins_subcategoryid->'1') val
                ) THEN 'அனைத்து'
                WHEN MAX(cat.if_subcategory) = 'N' OR MAX(cat.if_subcategory) IS NULL
                    THEN NULL
                ELSE string_agg(DISTINCT sub.subcattname, ', ')
            END AS subcategory_names_tamil
        "),
            )
            ->when($auditeedeptid, function ($query) use ($auditeedeptid) {
                $query->where('aud.auditeedeptid', $auditeedeptid);
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
                $query->where('dept.deptcode', $sessiondeptcode);
            })
            ->groupBy(
                'dept.deptcode',
                'dept.deptesname',
                'dept.deptelname',
                'mst_aud.auditeedeptcode',
                'aud.auditeedeptcode',
                'mst_aud.auditeedeptename',
                'mst_aud.auditeedepttname',
                'desig.desigcode',
                'desig.desigelname',
                'aud.designation',
                'aud.instid',
                'ins.instename',
                'cat.if_subcategory',
                'aud.auditeeins_subcategoryid',
                'cat.catcode',
                'aud.catcode',
                'dept.depttsname',
                'dept.depttlname',
                'aud.auditeedeptid',
                'aud.designation',
                'desig.desigelname',
                'desig.desigtlname',
                'aud.email',
                'aud.mobilenumber',
                'aud.statusflag'
            )
            ->orderBy('aud.updatedon', 'desc');

        return $query->get();
    }

    public static function authorityofaudit_fetchData($authority_id = null, $table = null)
    {
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;
        $table = 'audit.map_authorityofaudit';

        $query = DB::table($table . ' as aud')
            ->join(self::$deptTable . ' as dept', 'aud.deptcode', '=', 'dept.deptcode')
            ->leftjoin(self::$auditeecategory . ' as cat', 'aud.catcode', '=', 'cat.catcode')
            ->leftjoin(
                self::$institution . ' as ins',
                DB::raw('ins.instid::text'),
                '=',
                'aud.instid'
            )
            ->leftJoin(
                self::$subcategory . ' as sub',
                DB::raw('sub.auditeeins_subcategoryid::text'),
                '=',
                'aud.subcatid'
            )
            ->select(
                'aud.authority_id',
                'aud.deptcode',
                'aud.catcode as actual_catcode',
                DB::raw("CASE WHEN aud.catcode = 'A' THEN 'All' ELSE cat.catename END AS catengname"),
                DB::raw("CASE WHEN aud.catcode = 'A' THEN 'அனைத்தும்' ELSE cat.cattname END AS cattamname"),
                DB::raw("CASE WHEN aud.instid = 'A' THEN 'All' ELSE ins.instename END AS instename"),
                DB::raw("CASE WHEN aud.instid = 'A' THEN 'அனைத்தும்' ELSE ins.insttname END AS insttname"),
                DB::raw("CASE WHEN aud.subcatid = 'A' THEN 'All' ELSE sub.subcatename END AS subcategory_ename"),
                DB::raw("CASE WHEN aud.subcatid = 'A' THEN 'அனைத்தும்' ELSE sub.subcattname END AS subcategory_tname"),
                'aud.auth_content_en',
                'aud.auth_content_ta',
                'aud.statusflag',
                'aud.subcatid as actual_subcatcode',
                'aud.instid as actual_instid',
                'cat.catcode',
                'ins.instid',
                'cat.if_subcategory as subcategory',
                'sub.subcatename',
                'sub.subcattname',
                'sub.auditeeins_subcategoryid',
                'dept.deptcode',
                'dept.deptesname as deptengsname',
                'dept.deptelname',
                'dept.depttsname as depttamsname',
                'dept.depttlname',
            )
            ->where('aud.statusflag', '=', 'Y')
            ->when($authority_id, fn($q) => $q->where('aud.authority_id', $authority_id))
            ->when($sessiondeptcode, fn($q) => $q->where('dept.deptcode', $sessiondeptcode))
            ->orderBy('aud.updatedon', 'desc');

        $results = $query->get();

        $results->transform(function ($item) {
            $item->auth_content_en = json_decode($item->auth_content_en, true)['content'] ?? '';
            $item->auth_content_ta = json_decode($item->auth_content_ta, true)['content'] ?? '';
            return $item;
        });

        return $results;
    }

    public static function getinstituionbasedonsubcategory($deptcode, $category, $subcategory)
    {
        $table = 'audit.mst_institution';

        return DB::table($table . ' as ins')
            ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'ins.deptcode')
            ->join('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', 'ins.catcode')
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
                $query->where('ins.subcatid', $subcategory);  // THIS IS THE ONLY CORRECT FILTER
            })
            ->orderBy('ins.instename', 'ASC')
            ->get();
    }

    public static function authorityofaudit_insertupdate(array $data, $authority_id = null)
    {
        $table = 'audit.map_authorityofaudit';

        try {
            $duplicateQuery = DB::table($table)
                ->where('deptcode', $data['deptcode'])
                ->where('catcode', $data['catcode'])
                ->where('subcatid', $data['subcatid'])
                ->where('instid', $data['instid'])
                ->where('statusflag', 'Y')
                ->when($authority_id, function ($q) use ($authority_id) {
                    return $q->where('authority_id', '<>', $authority_id);
                });

            if ($duplicateQuery->exists() && $data['statusflag'] === 'Y') {
                throw new \Exception('A matching record already exists.');
            }

            if ($authority_id) {
                $affectedRows = DB::table($table)
                    ->where('authority_id', $authority_id)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
                return $authority_id;
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'authority_id');

                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function fetchtemplatebankdetails($bankid = null)
    {
        $table = 'audit.mst_bank_master';
        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;
        $query = DB::table($table . ' as b')
            ->select(
                'b.*',
            )
            ->when($bankid, function ($query) use ($bankid) {
                $query->where('b.bankid', $bankid);
            });

        $query->orderBy('b.updatedon', 'desc');

        return $query->get();
    }

    public static function templatebankdetails_insertupdate(array $data, $bankid = null)
    {
        $table = 'audit.mst_bank_master';

        try {
            $normalize = fn($v) => strtolower(preg_replace('/\s+/', '', trim($v)));

            $checkBankName = $normalize($data['bank_name']);
            $checkIfsc = $normalize($data['ifsc_code']);

            $existing = DB::table($table)
                ->selectRaw("
                MAX(CASE
                    WHEN LOWER(REGEXP_REPLACE(bank_name, '\s+', '', 'g')) = ? THEN 1
                    ELSE 0
                END) AS bank_exists,

                MAX(CASE
                    WHEN LOWER(REGEXP_REPLACE(ifsc_code, '\s+', '', 'g')) = ? THEN 1
                    ELSE 0
                END) AS ifsc_exists
            ", [$checkBankName, $checkIfsc])
                ->when(!empty($bankid), function ($query) use ($bankid) {
                    return $query->where('bankid', '<>', $bankid);
                })
                ->first();

            $bankNameExists = (bool) $existing->bank_exists;
            $ifscExists = (bool) $existing->ifsc_exists;

            if ($bankNameExists && $ifscExists) {
                throw new \Exception('BankNameAndIfscExist');
            } elseif ($bankNameExists) {
                throw new \Exception('BankNameExist');
            } elseif ($ifscExists) {
                throw new \Exception('IfscCodeExist');
            }

            if ($bankid && $data['statusflag'] === 'N') {
                $isUsedInChild = DB::table('audit.hria_details')
                    ->where('bankid', $bankid)
                    ->exists();

                if ($isUsedInChild) {
                    throw new \Exception('BankInUseCannotDeactivate');
                }
            }

            $query = DB::table($table);
            if ($bankid) {
                $query->where('bankid', '<>', $bankid);
            }
            if ($bankid) {
                $affectedRows = DB::table($table)->where('bankid', $bankid)->update($data);
                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'bankid');
                if (!$newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }
                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function fetchzonebasedondistforinst($cond)
    {
        // return $deptcode;
        $table = 'audit.mst_hriazone';

        return DB::table($table . ' as z')
            ->select('z.zoneename', 'z.zoneename', 'z.zonecode')
            ->distinct()
            ->where('z.distcode', $cond['distcode'])
            ->where('z.statusflag', 'Y')
            ->orderBy('z.zonecode', 'Asc')
            ->get();
    }

    public static function fetchzonebasedondist($distcode)
    {
        // return $deptcode;
        $table = 'audit.mst_hriazone';

        return DB::table($table . ' as z')
            ->select('z.zoneename', 'z.zoneename', 'z.zonecode')
            ->distinct()
            ->where('z.distcode', '=', $distcode)
            ->where('z.statusflag', 'Y')
            ->orderBy('z.zonecode', 'Asc')
            ->get();
    }

    public static function circle_fetchData($circleid = null, $table)
    {
        $query = DB::table($table . ' as h')
            ->join('audit.mst_hriazone as z', 'h.zonecode', '=', 'z.zonecode')
            ->join('audit.mst_district as d', 'd.distcode', '=', 'z.distcode')
            ->select(
                'd.distename',
                'd.disttname',
                'd.distcode',
                'z.zoneename',
                'z.zonetname',
                'z.zonecode',
                'h.circleename',
                'h.circletname',
                'h.circleid',
                'h.statusflag',
            )
            ->when($circleid, function ($query) use ($circleid) {
                $query->where('h.circleid', $circleid);
            })
            ->where('d.statusflag', 'Y')
            ->where('z.statusflag', 'Y');

        $query->orderBy('h.updatedon', 'desc');
        return $query->get();
    }

    public static function circle_insertupdate(array $data, $circleid = null, $table = null)
    {
        $table = 'audit.mst_hriacircle';

        try {
            $circleename = trim($data['circleename']);
            $circletname = trim($data['circletname']);

            // Remove spaces + lowercase for duplicate check
            $checkEname = strtolower(str_replace(' ', '', $circleename));
            $checkTname = strtolower(str_replace(' ', '', $circletname));

            // Duplicate check inside same zonecode
            $enameExists = DB::table($table)
                ->where('zonecode', $data['zonecode'])
                ->whereRaw("LOWER(REPLACE(circleename, ' ', '')) = ?", [$checkEname])
                ->when($circleid, function ($query) use ($circleid) {
                    return $query->where('circleid', '<>', $circleid);
                })
                ->exists();

            $tnameExists = DB::table($table)
                ->where('zonecode', $data['zonecode'])
                ->whereRaw("LOWER(REPLACE(circletname, ' ', '')) = ?", [$checkTname])
                ->when($circleid, function ($query) use ($circleid) {
                    return $query->where('circleid', '<>', $circleid);
                })
                ->exists();

            if ($enameExists && $tnameExists) {
                throw new \Exception('CircleETnameExist');
            } elseif ($enameExists) {
                throw new \Exception('CircleEnameExist');
            } elseif ($tnameExists) {
                throw new \Exception('CircleTnameExist');
            }

            if ($circleid && isset($data['statusflag']) && $data['statusflag'] === 'N') {
                $isUsedInHub = DB::table('audit.mst_nonaudit_hub')
                    ->where('circleid', $circleid)
                    ->exists();

                if ($isUsedInHub) {
                    throw new \Exception('CircleAlreadyMappedInNonAuditHub');
                }
            }

            if ($circleid) {
                $affectedRows = DB::table($table)
                    ->where('circleid', $circleid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }

                return $circleid;
            } else {
                $newId = DB::table($table)->insertGetId($data, 'circleid');

                if (!$newId) {
                    throw new \Exception('Failed to insert the new record.');
                }

                return $newId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getdistrict()
    {
        return DB::table('audit.mst_district')
            ->select('distename', 'distcode', 'disttname')
            ->distinct()
            ->orderby('distcode', 'asc')
            ->where('statusflag', 'Y')
            ->get();
    }

    public static function zone_insertupdate(array $data, $zoneid = null, $table = null)
    {
        $table = 'audit.mst_hriazone';

        try {
            $zoneename = trim($data['zoneename']);
            $zonetname = trim($data['zonetname']);

            // Remove spaces + lowercase for duplicate check
            $checkEname = strtolower(str_replace(' ', '', $zoneename));
            $checkTname = strtolower(str_replace(' ', '', $zonetname));

            // Duplicate check inside same distcode
            $enameExists = DB::table($table)
                ->where('distcode', $data['distcode'])
                ->whereRaw("LOWER(REPLACE(zoneename, ' ', '')) = ?", [$checkEname])
                ->when($zoneid, function ($query) use ($zoneid) {
                    return $query->where('zoneid', '<>', $zoneid);
                })
                ->exists();

            $tnameExists = DB::table($table)
                ->where('distcode', $data['distcode'])
                ->whereRaw("LOWER(REPLACE(zonetname, ' ', '')) = ?", [$checkTname])
                ->when($zoneid, function ($query) use ($zoneid) {
                    return $query->where('zoneid', '<>', $zoneid);
                })
                ->exists();

            if ($enameExists && $tnameExists) {
                throw new \Exception('ZoneETnameExist');
            } elseif ($enameExists) {
                throw new \Exception('ZoneEnameExist');
            } elseif ($tnameExists) {
                throw new \Exception('ZoneTnameExist');
            }

            if ($zoneid && isset($data['statusflag']) && $data['statusflag'] === 'N') {
                // Get current zonecode
                $zonecode = DB::table($table)
                    ->where('zoneid', $zoneid)
                    ->value('zonecode');

                $isUsedInCircle = DB::table('audit.mst_hriacircle')
                    ->where('zonecode', $zonecode)
                    ->exists();

                if ($isUsedInCircle) {
                    throw new \Exception('ZoneAlreadyMappedInCircle');
                }
            }

            if ($zoneid) {
                $affectedRows = DB::table($table)
                    ->where('zoneid', $zoneid)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }

                return $zoneid;
            } else {
                // Generate next zonecode
                $latestCode = DB::table($table)
                    ->whereNotNull('zonecode')
                    ->max(DB::raw('CAST(zonecode AS INTEGER)'));

                $newCode = $latestCode !== null
                    ? str_pad($latestCode + 1, 2, '0', STR_PAD_LEFT)
                    : '01';

                $data['zonecode'] = $newCode;

                $newId = DB::table($table)->insertGetId($data, 'zoneid');

                if (!$newId) {
                    throw new \Exception('Failed to insert the new record.');
                }

                return $newId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function zone_fetchData($zoneid = null, $table)
    {
        $query = DB::table($table . ' as z')
            ->join('audit.mst_district as d', 'd.distcode', '=', 'z.distcode')
            ->select(
                'd.distename',
                'd.distcode',
                'z.*'
            )
            ->when($zoneid, function ($query) use ($zoneid) {
                $query->where('z.zoneid', $zoneid);
            })
            ->where('d.statusflag', 'Y');

        $query->orderBy('z.updatedon', 'desc');
        return $query->get();
    }

  public static function update_apmsdata($instid)
    {
        $formname = 'alter_inst';

        try {
            DB::beginTransaction();

            $result = DB::select(
                "SELECT audit.fn_update_followup_audityear_INST(?, ?) AS response",
                [$instid, $formname]
            );

            DB::commit();


            $response = $result[0]->response ?? null;


            if (is_string($response)) {
                $response = json_decode($response, true);
            }

            return $response;
        } catch (\Exception $e) {

            DB::rollBack();


            return [
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
}
