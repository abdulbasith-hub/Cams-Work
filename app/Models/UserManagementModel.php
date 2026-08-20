<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Hash;



use Exception;

class UserManagementModel extends Model
{
     protected static $auditeedept = BaseModel::AUDITEEDEPT_TABLE;

    protected static $hodcreatechargetable = BaseModel::HOD_CREATE_CHARGE;

    protected static $mstauditeeinscategory_table = BaseModel::MSTAUDITEEINSCATEGORY_TABLE;

    protected static $subcategory = BaseModel::SUBCATEGORY_TABLE;

    protected static $hodassigchargetable = BaseModel::HOD_ASSIGN_CHARGE;

    protected static $deptTable = BaseModel::DEPT_TABLE;
    protected static $distTable = BaseModel::DIST_Table;
    protected static $regionTable = BaseModel::REGION_TABLE;
    protected static $auditorinstmappingTable = BaseModel::AUDITORINSTMAPPING_TABLE;

protected static $transpara_table = BaseModel::TRANSPARA_TABLE;
 protected static $historytranspara_table = BaseModel::HISTORYTRANSPARA_TABLE;



    protected static $designationTable = BaseModel::DESIGNATION_TABLE;


    protected static $roletypeTable = BaseModel::ROLETYPE_TABLE;
    protected static $roletypemappingTable = BaseModel::ROLETYPEMAPPING_TABLE;
    protected static $roleactionTable = BaseModel::ROLEACTION_TABLE;
    protected static $rolemappingTable = BaseModel::ROLEMAPPING_TABLE;



    protected static $userdetailTable = BaseModel::USERDETAIL_TABLE;
    protected static $chargedetailTable = BaseModel::CHARGEDETAIL_TABLE;
    protected static $userchargedetailTable = BaseModel::USERCHARGEDETAIL_TABLE;


public static function getRoleactionBasedOnRoletype($deptcode, $roletypecode)
    {
        // Retrieve session data (only once)
        $session_desigcode = session('charge')->desigcode ?? null;

        // Build the base query for designations
        $baseQuery = DB::table(self::$rolemappingTable . ' as r')
            ->join(self::$roletypemappingTable . ' as rt', 'rt.roletypemappingcode', '=', 'r.roletypemappingcode')
            ->join(self::$roleactionTable . ' as ra', 'ra.roleactioncode', '=', 'r.roleactioncode')
            ->select('r.roleactioncode', 'ra.roleactionesname', 'ra.roleactionelname', 'ra.roleactiontlname', 'ra.roleactiontsname', 'ra.categoryflag', 'ra.subcategoryflag')
            ->where('ra.statusflag', 'Y');



        // Add deptcode condition if provided
        if ($deptcode) {
            $baseQuery->where('rt.deptcode', '=', $deptcode);
        }

        if ($roletypecode) {
            $baseQuery->where('rt.roletypecode', '=', $roletypecode);
        }


        // Default case for other pages: apply the orderid condition
        return $baseQuery
            ->distinct()
            ->orderBy('ra.roleactionelname', 'ASC')
            ->get();
    }



    /************************************************* Common Function ********************************************/


    public static function roleactiondetail()
    {
        return DB::table(self::$roleactionTable)
            ->where('statusflag', '=', 'Y')
            ->orderBy('roleactionid', 'asc')
            ->get();
    }

    public static function getRoleMappingId($deptcode, $roletypecode, $roleactioncode)
    {
        return DB::table(self::$rolemappingTable . ' as r')
            ->join(self::$roletypemappingTable . ' as rm', 'rm.roletypemappingcode', '=', 'r.roletypemappingcode')
            ->where('rm.roletypecode', $roletypecode)
            ->where('r.roleactioncode', $roleactioncode)
            ->where('rm.deptcode', $deptcode)
            ->where('rm.statusflag', 'Y')
            ->value('r.rolemappingid');
    }

    public static function deptdetail($viewname = Null)
    {
        if (($viewname == 'createcharge') || ($viewname == 'createuser')) {
            return DB::table(self::$deptTable . ' as d')
                ->where('statusflag', '=', 'Y')
                ->select('d.deptesname', 'd.deptelname', 'd.depttsname', 'd.depttlname', 'deptcode')
                ->orderBy('orderid', 'asc')
                ->get();
        } else {
            $chargeData = session('charge');
            $session_deptcode = $chargeData->deptcode;

            $query = DB::table(self::$deptTable . ' as d')
                ->distinct()
                ->select('d.deptcode', 'd.deptelname', 'd.depttlname')
                ->join(self::$chargedetailTable . ' as c', 'c.deptcode', '=', 'd.deptcode')
                ->where('c.statusflag', operator: 'Y')
                ->where('d.statusflag', 'Y');
            // ->whereNotIn('c.chargeid', function ($query) {
            //     $query->select('chargeid')->from('audit.userchargedetails')
            //     ->where('statusflag','Y');
            // });

            if ($session_deptcode) {
                $query->where('c.deptcode', $session_deptcode);
            }
            $results = $query->get();

            return $results;
        }
    }

     public static function departmenttdetail($tablename = Null)
    {
         return DeptModel::where('statusflag', '=', 'Y')
             ->orderBy('orderid', 'asc')
             ->get();
     }



    public static function distdetail($pagename = Null)
    {
        $sessiondetails =   session('charge');
        $sessionroletypecode    =   $sessiondetails->roletypecode;
        $sessiondeptcode    =   $sessiondetails->roletypecode;

        if (($pagename == 'createuser') &&   ($sessionroletypecode == View::shared('Re_roletypecode'))) {
            $sessionregioncode    =   $sessiondetails->regioncode;
            return DB::table(self::$auditorinstmappingTable . ' as instm')
                ->join(self::$roletypemappingTable . ' as rtm', function ($join) {
                    $join->on('instm.deptcode', '=', 'rtm.parentcode')
                        ->on('rtm.roletypecode', '=', 'instm.roletypecode');
                })
                ->where('instm.statusflag', 'Y')
                ->where('rtm.deptcode', $deptcode)
                ->where('instm.roletypecode', $sessionroletypecode)
                ->where("instm.regioncode", $sessionregioncode);


            (self::$distTable . ' as d')
                ->select('d.distcode', 'd.distename')
                ->get();
        } else {
            return DB::table(self::$distTable . ' as d')
                ->select('d.distcode', 'd.distename')
                ->get();
        }
    }


    public static function designationdetail()
    {
        $chargeData = session('charge');

        // Safely retrieve the session data
        $session_deptcode = $chargeData->deptcode ?? null;



        // Build the query
        $query = DB::table(self::$designationTable)
            ->where('statusflag', 'Y');

        if ($session_deptcode) {
            $query->where('deptcode', $session_deptcode);
        }

        // Retrieve results
        $designations = $query->orderBy('desigelname', 'asc')->get();

        return $designations;
    }

    public static function roletypebasedon_sessionroletype($deptcode, $roletypecode, $page)
    {
        // dd($deptcode, $roletypecode, $page);
        if ($page === 'createcharge') {
            $query = DB::table(self::$roletypemappingTable . ' as rm')
                ->join(self::$roletypeTable . ' as r', 'r.roletypecode', '=', 'rm.roletypecode')
                ->join(self::$deptTable . ' as d', 'd.deptcode', '=', 'rm.deptcode')
                ->select('rm.roletypecode', 'r.roletypeelname', 'r.roletypetlname')
                ->where('r.statusflag', 'Y')
 		->where('rm.statusflag', 'Y');

            if ($roletypecode) {
                $query->where('rm.roletypecode', '<=', $roletypecode);
            }

            if ($deptcode) {
                $query->where('rm.deptcode', '=', $deptcode);
            }

            return $query
                ->orderBy('rm.orderid', 'DESC')
                ->get();
        }

        if ($page === 'assigncharge') {
            $query =  DB::table('audit.chargedetails as c')
                ->distinct()
                ->select('r.roletypecode', 'r.roletypeelname', 'r.roletypetlname')
                ->join(self::$rolemappingTable . ' as ro', 'ro.rolemappingid', '=', 'c.rolemappingid')
                ->join(self::$roletypemappingTable . ' as rm', 'rm.roletypemappingcode', '=', 'ro.roletypemappingcode')
                ->join(self::$roletypeTable . ' as r', 'r.roletypecode', '=', 'rm.roletypecode')
                ->where('r.statusflag', 'Y')
                ->where('c.deptcode', '=', $deptcode);
            if ($roletypecode) {
                $query->where('rm.roletypecode', '<=', $roletypecode);
            }
            // ->whereNotIn('c.chargeid', function ($query) {
            //     $query->select('chargeid')
            //         ->from('audit.userchargedetails')
            //         ->where('statusflag', 'Y');
            // })
            return $query->get();
        }

        // Default return if no page matches
        return collect(); // Empty collection
    }





    public static function getDesignationBasedonDept($deptcode, $page)
    {
        // Retrieve session data (only once)
        $session_desigcode = session('charge')->desigcode ?? null;

        // Build the base query for designations
        $baseQuery = DB::table(self::$designationTable . ' as d')
            ->select('d.desigcode', 'd.desigelname', 'd.desigesname', 'd.desigtlname', 'd.desigtsname')
            ->where('d.statusflag', 'Y');

        // Add deptcode condition if provided
        if ($deptcode) {
            $baseQuery->where('d.deptcode', '=', $deptcode);
        }

        // Fetch orderid if desigcode is set and apply deptcode if provided
        $desigdelorderid = null;
        if ($session_desigcode) {
            $desigdelorderid = DB::table(self::$designationTable . ' as d')
                ->where('d.statusflag', 'Y')
                ->where('d.desigcode', '=', $session_desigcode)
                ->when($deptcode, function ($query) use ($deptcode) {
                    return $query->where('d.deptcode', '=', $deptcode);
                })
                ->value('orderid');
        }

        // Common order condition if desigdelorderid exists
        $orderCondition = function ($query) use ($desigdelorderid) {
            if ($desigdelorderid) {
                return $query->where('d.orderid', '>', $desigdelorderid);
            }
            return $query;
        };

        // If the page is 'assigncharge', create the specific query
        if ($page === 'assigncharge') {
            return DB::table('audit.chargedetails as c')
                ->select('d.desigcode', 'd.desigelname', 'd.desigesname', 'd.desigtlname', 'd.desigtsname')
                ->join(self::$designationTable . ' as d', 'd.desigcode', '=', 'c.desigcode')
                ->where('c.statusflag', 'Y')
                ->when($deptcode, function ($query) use ($deptcode) {
                    return $query->where('c.deptcode', '=', $deptcode);
                })
                ->when($desigdelorderid, function ($query) use ($desigdelorderid) {
                    return $query->where('d.orderid', '>', $desigdelorderid);
                })
                ->orderBy('d.orderid', 'ASC')
                ->distinct('d.desigcode', 'd.desigelname', 'd.desigesname', 'd.desigtlname', 'd.desigtsname', 'd.orderid')
                ->get();
        }

        // Default case for other pages: apply the orderid condition
        return $baseQuery
            ->when($desigdelorderid, function ($query) use ($desigdelorderid) {
                return $query->where('d.orderid', '>', $desigdelorderid);
            })
            ->orderBy('d.orderid', 'ASC')
            ->get();
    }











    public static function getRegionDistrictInstDelBasedOnDept(
        string $deptcode,
        ?string $regioncode = null,
        ?string $distcode = null,
        string $getval,
        string $roletypecode,
        string $page
    ) {
        //echo 'jo';
        // Validate required parameters
        if (empty($deptcode) || empty($getval)) {
            throw new InvalidArgumentException("Invalid arguments provided.");
        }

        // Initialize base query
        $query = DB::table(self::$auditorinstmappingTable . ' as instm')
            // ->join(self::$roletypemappingTable . ' as rtm', function ($join) {
            //     $join->on('instm.deptcode', '=', 'rtm.parentcode')
            //         ->on('rtm.roletypecode', '=', 'instm.roletypecode');
            // })
            ->where('instm.statusflag', 'Y')
            ->where('instm.deptcode', $deptcode);
        if ($page == 'createuser') {
            // echo $roletypecode;
            // if($roletypecode)  $query->where("instm.roletypecode", $roletypecode);
        } else {
            $query->where('instm.roletypecode', $roletypecode);
        }






        // Process based on the value of $getval
        switch ($getval) {
                // protected static $auditorinstmappingTable = BaseModel::AUDITORINSTMAPPING_TABLE;
            case 'region':
                $query->join(self::$regionTable . ' as re', 're.regioncode', '=', "instm.regioncode")
                    ->select("instm.regioncode", 're.regionename', 're.regiontname')
                    ->distinct();

                if ($page === 'assigncharge') {
                    $query->join('audit.chargedetails as c', 'c.regioncode', '=', 're.regioncode')
                        // ->whereNotIn('c.chargeid', function ($subQuery) {
                        //     $subQuery->select('chargeid')
                        //             ->from('audit.userchargedetails')
                        //             ->where('statusflag', 'Y');
                        // })
                    ;
                }

                $query->orderBy('re.regionename', 'ASC');
                break;




            case 'district':
                $query->join(self::$regionTable . ' as re', 're.regioncode', '=', "instm.regioncode")
                    ->join(self::$distTable . ' as d', 'd.distcode', '=', "instm.distcode")

                    ->select("instm.distcode", 'd.distename', 'd.disttname')
                    ->distinct();
                if ($page == 'createuser') {
                    if ($regioncode)  $query->where("instm.regioncode", $regioncode);
                } else    $query->where("instm.regioncode", $regioncode);
                if ($page === 'assigncharge') {
                    $query->join('audit.chargedetails as c', 'c.regioncode', '=', 're.regioncode')
                        // ->whereNotIn('c.chargeid', function ($subQuery) {
                        //     $subQuery->select('chargeid')
                        //             ->from('audit.userchargedetails')
                        //             ->where('statusflag', 'Y');
                        // })
                    ;
                }
                $query->orderBy("d.distename", 'ASC');
                break;

            case 'institution':
                $query->join(self::$regionTable . ' as re', 're.regioncode', '=', "instm.regioncode")
                    ->select("instm.instmappingid", "instm.instename", "instm.instmappingcode", "instm.insttname");
                // ->where("instm.roletypecode", $roletypecode);

                // Apply additional filters for institution
                // if ($regioncode) {
                $query->where("instm.regioncode", $regioncode);
                //}
                if ($roletypecode == '01') {
                    $query->join(self::$distTable . ' as d', 'd.distcode', '=', "instm.distcode")
                        ->where("instm.distcode", $distcode);
                }
                if ($page === 'assigncharge') {
                    $query->join('audit.chargedetails as c', 'c.instmappingcode', '=', "instm.instmappingcode")
                        ->distinct()
                        // ->whereNotIn('c.chargeid', function ($subQuery) {
                        //     $subQuery->select('chargeid')
                        //             ->from('audit.userchargedetails')
                        //             ->where('statusflag', 'Y');
                        // })
                    ;
                }
                $query->orderBy("instm.instename", 'ASC');
                //dd($query->tosql());
                break;

            default:
                throw new InvalidArgumentException("Invalid 'getval' provided. Allowed values are 'region', 'district', or 'institution'.");
        }

$querySql = $query->toSql();
        $bindings = $query->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );

        // print_r($finalQuery);
        // Order results and execute the query
        return

            $query->get();
    }

    public static function bindwherecondition_basedon_roletypecode($data)
    {
        $where = [];
        if (in_array($data['roletypecode'], [
            View::shared('Ho_roletypecode'),
            View::shared('Re_roletypecode'),
            View::shared('Dist_roletypecode')
        ])) {
            $where['deptcode'] = $data['deptcode'];

            if (in_array($data['roletypecode'], [View::shared('Re_roletypecode'), View::shared('Dist_roletypecode')])) {
                $where['instmappingid'] = $data['instid'];

                if ($data['roletypecode'] == View::shared('Re_roletypecode')) {
                    $where['regioncode'] = $data['regioncode'];
                }

                if ($data['roletypecode'] == View::shared('Dist_roletypecode')) {
                    $where['distcode'] = $data['distcode'];
                }
            }
        }
        return $where; // Ensure the condition is returned
    }


    /************************************************* Common Function ********************************************/



    /************************************************* Charge Form - Function ********************************************/


 public static function createcharge_insertupdate(array $data, $chargeid, $table)
    {
        try {
            // Check if the role mapping exists and get the ID
            $rolemappingid = DB::table('audit.rolemapping')
                ->join('audit.roletypemapping as rm', 'rm.roletypemappingcode', '=', 'audit.rolemapping.roletypemappingcode')
                ->where('rm.roletypecode', $data['roletypecode'])
                ->where('rolemapping.roleactioncode', $data['roleactioncode'])
                ->where('rm.deptcode', $data['deptcode'])
                ->value('rolemappingid');

            // dd($rolemappingid);
            // dd($request->all());

            if (! $rolemappingid) {
                throw new \Exception('Role mapping does not exist.');
            }

            // // Build where conditions dynamically
            $wherecondition = [
                'rolemappingid' => $rolemappingid,
                'desigcode' => $data['desigcode'],
            ];

	    $consolidationEntry = view()->shared('consolidation_entry');
            $consolidationApproval = view()->shared('consolidation_approval');

            if (in_array($data['roleactioncode'], [
                $consolidationEntry,
                $consolidationApproval
            ])) {
                $wherecondition['catcode'] = $data['catcode'];
            }

            $roletypecode = $data['roletypecode'];

            // // Start building the query
            $query = DB::table($table)->where($wherecondition);

            if (($roletypecode == '03') || ($roletypecode == '02') || ($roletypecode == '01')) {
                $query->where('deptcode', '=', $data['deptcode']);
                if (($roletypecode == '02') || ($roletypecode == '01')) {
                    $query->where('regioncode', '=', $data['regioncode']);
                    $query->where('instmappingcode', '=', $data['instmappingcode']);
                    if (($roletypecode == '01')) {
                        $query->where('distcode', '=', $data['distcode']);
                    }
                }
            }

            // Exclude the current chargeid if updating
            if ($chargeid) {
                $query->where('chargeid', '<>', $chargeid);
            }

            // Check if the record already exists
            if ($query->exists()) {
                throw new \Exception('ChargeExist');
            }

            // Remove unwanted fields and add rolemappingid
            unset($data['roleactioncode'], $data['roletypecode']);
            $data['rolemappingid'] = $rolemappingid;

            // Insert or update the record based on chargeid
            if ($chargeid) {
                $affectedRows = DB::table($table)->where('chargeid', $chargeid)->update($data);
                if ($affectedRows === 0) {
                    throw new \Exception('Failed to update the record.');
                }
            } else {
                $newRecordId = DB::table($table)->insertGetId($data, 'chargeid');

                if (! $newRecordId) {
                    throw new \Exception('Failed to insert the new record.');
                }

                return $newRecordId;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


  public static function fetchchargeData($chargeid = null, $table)
    {
        $sessiondetails =   session('charge');
        $sessionchargeid    =   $sessiondetails->chargeid;

        $sessionroletypecode    =   $sessiondetails->roletypecode;
        $sessiondeptcode   =   $sessiondetails->deptcode;
        $sessionregioncode    =   $sessiondetails->regioncode;
        $sessiondistcode    =   $sessiondetails->distcode;
        $sessiondesigcode   =   $sessiondetails->desigcode;


        $get_desigorderid    =   DB::table(self::$designationTable . ' as desig')

            // ->select( 'desig.orderid')
            ->where('desig.desigcode', '=', $sessiondesigcode)
            ->value('desig.orderid');



        // Build the query and apply the 'chargeid' condition if it's provided
        $query  =  DB::table($table)
            ->join("audit.rolemapping as rm", "rm.rolemappingid", '=', "$table.rolemappingid")
            ->join("audit.mst_roleaction as ra", "rm.roleactioncode", '=', "ra.roleactioncode")
            ->join("audit.roletypemapping as rtm", "rtm.roletypemappingcode", '=', "rm.roletypemappingcode")
            ->join("audit.mst_roletype as rt", "rt.roletypecode", '=', "rtm.roletypecode")
            ->join("audit.mst_dept as d", "d.deptcode", '=', "$table.deptcode")
            ->leftjoin("audit.mst_region as r", "r.regioncode", '=', "$table.regioncode")
            ->leftjoin("audit.mst_district as di", "di.distcode", '=', "$table.distcode")
            ->leftjoin("audit.auditor_instmapping as ins", "ins.instmappingcode", '=', "$table.instmappingcode")
            ->join("audit.mst_designation as des", "des.desigcode", '=', "$table.desigcode")
            ->leftjoin("audit.mst_auditeeins_category as cat", "cat.catcode", '=', "$table.catcode")
            ->leftjoin("audit.mst_auditeeins_subcategory as subcat", "subcat.auditeeins_subcategoryid", '=', "$table.subcatid")


            ->select(
                "rt.roletypecode",
                'rt.roletypeelname',
                'rt.roletypetlname',

                "ra.roleactioncode",
                'ra.roleactionelname',
                'ra.roleactiontlname',

                'r.regionename',
                'r.regiontname',
                "$table.regioncode",
                "$table.distcode",

                'di.distename',
                'di.disttname',

                "$table.instmappingcode",
                'ins.instename',
                'ins.insttname',

                'd.deptesname',
                'd.depttsname',

                "$table.deptcode",
                "$table.desigcode",
                'des.desigesname',
                'des.desigtsname',

                "$table.chargedescription",
                "$table.chargeid",
                "$table.catcode",
                "$table.subcatid",
                DB::raw("
                CASE
                    WHEN NOT EXISTS (
                        SELECT 1
                        FROM audit.userchargedetails u2
                        WHERE u2.chargeid = chargedetails.chargeid
                    )
                    OR (
                        SELECT COUNT(*)
                        FROM audit.userchargedetails u2
                        WHERE u2.chargeid = chargedetails.chargeid
                    ) = (
                        SELECT COUNT(*)
                        FROM audit.userchargedetails u2
                        WHERE u2.chargeid = chargedetails.chargeid
                          AND u2.statusflag = 'N'
                    )
                    THEN 'N'
                    ELSE 'Y'
                END AS assignedstatus
               "),

                "cat.catename",
                'cat.cattname',


                "subcat.subcatename",
                'subcat.subcattname'
            );


        if ($get_desigorderid) {
            $query->where('des.orderid', '>', $get_desigorderid);
        }

        if (($sessionroletypecode ==  View::shared('Ho_roletypecode')) || ($sessionroletypecode ==  View::shared('Re_roletypecode')) || ($sessionroletypecode ==  View::shared('Dist_roletypecode'))) {
            $query->where("$table.deptcode", $sessiondeptcode);
            if (($sessionroletypecode ==  View::shared('Re_roletypecode')) || ($sessionroletypecode ==  View::shared('Dist_roletypecode'))) {
                $query->where("$table.regioncode", $sessionregioncode);
                if (($sessionroletypecode ==  View::shared('Dist_roletypecode')))
                    $query->where("$table.distcode", $sessiondistcode);
            }
        }
        $query->when($sessionchargeid, function ($query) use ($sessionchargeid) {
            $query->where("chargeid", '<>', $sessionchargeid);
        });
        $query->when($chargeid, function ($query) use ($chargeid) {
            $query->where('chargeid', $chargeid);
        });
        $query->orderBy($table . '.updatedon', 'desc');

        // $querySql = $query->toSql();
        // $bindings = $query->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );

        // print_r($finalQuery);

        // Return the results directly
        return $query->get();
    }

    /************************************************* Charge Form - Function ********************************************/



    /************************************************* User Form - Function ********************************************/

    public static function createuser_insertupdate(array $data, $currentUserId = null, $table)
    {
        try {

            // $roletypemappingcode = DB::table('audit.roletypemapping as rm')
            // 	->where('rm.roletypecode', $data['roletypecode'])
            // 	->where('rm.deptcode', $data['deptcode'])
            // 	->value('roletypemappingcode');

            // if (!$roletypemappingcode) {
            // 	throw new \Exception('Role mapping does not exist.');
            // }

            // unset( $data['roletypecode']);
            // $data['roletypemappingcode'] = $roletypemappingcode;


            // Common duplicate checks
            $query = DB::table($table);

            if ($currentUserId) {
                $query->where('deptuserid', '!=', $currentUserId);
            }

            $emailExists = (clone $query)->where('email', $data['email'])->exists();
            $mobileExists = (clone $query)->where('mobilenumber', $data['mobilenumber'])->exists();
            $ifhrmsnoExists = (clone $query)->where('ifhrmsno', $data['ifhrmsno'])->exists();
            $existingUser = (clone $query)
                ->where('email', $data['email'])
                ->where('mobilenumber', $data['mobilenumber'])
                ->where('ifhrmsno', $data['ifhrmsno'])
                ->first();

            // Duplicate validation
            if ($emailExists) {
                throw new \Exception('createUserEmailError');
            }
            // if ($emailExists) {
            //     throw new \Exception('The email address is already associated with a different user.');
            // }
            if ($mobileExists) {
                throw new \Exception('createUserMobileExixt');
            }
            if ($ifhrmsnoExists) {
                throw new \Exception('createUserIFMSExixt');
            }
            if ($existingUser) {
                throw new \Exception('createUserCombError');
            }

            // Create or update
            if ($currentUserId) {
                DB::table($table)->where('deptuserid', $currentUserId)->update($data);
                return DB::table($table)->where('deptuserid', $currentUserId)->first();
            } else {
                $newUserId = DB::table($table)->insertGetId($data, 'deptuserid');
                return DB::table($table)->where('deptuserid', $newUserId)->first();
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function fetchuserData($userid = null, $table)
    {
        $sessiondetails_user = session('user');
        $sessiondetails = session('charge');
        $sessionroletypecode = $sessiondetails->roletypecode;
        $sessionuserid = $sessiondetails_user->userid;
        $sessiondeptcode = $sessiondetails->deptcode;
        $sessionregioncode = $sessiondetails->regioncode;
        $sessiondistcode = $sessiondetails->distcode;
        $sessiondesigcode = $sessiondetails->desigcode;



        $get_desigorderid    =   DB::table(self::$designationTable . ' as desig')

        // ->select( 'desig.orderid')
        ->where('desig.desigcode', '=', $sessiondesigcode)
        ->value('desig.orderid');


        // Build the query and apply conditions
        $query = DB::table(self::$userdetailTable . ' as deptuserdetails')
            ->join(self::$deptTable . ' as dept', 'deptuserdetails.deptcode', '=', 'dept.deptcode')
            ->leftjoin(self::$distTable . ' as dist', 'dist.distcode', '=', 'deptuserdetails.distcode')

            ->join(self::$designationTable . ' as desig', 'desig.desigcode', '=', 'deptuserdetails.desigcode')
            ->leftJoin(self::$auditorinstmappingTable . ' as rtm', 'rtm.distcode', '=', 'deptuserdetails.distcode')
            // ->leftJoin('audit.mst_roletype as rt', 'rt.roletypecode', '=', 'rtm.roletypecode')
            ->select(
                'desig.desigesname',
                'dist.distename',
                'dist.disttname',
                'desig.desigelname',
                'desig.desigtsname',
                'desig.desigtlname',
                'dept.deptesname',
                'dept.deptelname',
                'dept.depttsname',
                'dept.depttlname',
                'deptuserdetails.deptuserid',
                'deptuserdetails.deptcode',
                'deptuserdetails.username',
                'deptuserdetails.usertamilname',
                'deptuserdetails.ifhrmsno',
                'deptuserdetails.gendercode',
                'deptuserdetails.dob',
                'deptuserdetails.email',
                'deptuserdetails.doj',
		'deptuserdetails.dor',
	        'deptuserdetails.psaflag',
                'deptuserdetails.auditorflag',
                'deptuserdetails.mobilenumber',
                'deptuserdetails.desigcode',
                'deptuserdetails.distcode',
                'deptuserdetails.createdon',
 		'deptuserdetails.reservelist',
		DB::raw("CASE WHEN deptuserdetails.deptuserid NOT IN (
                         SELECT userid FROM audit.userchargedetails WHERE statusflag = 'Y'
                     ) THEN 'N'
                     ELSE 'Y'
                END AS assignedstatus")

            )

            ->distinct()
            ->where('deptuserdetails.statusflag', '=', 'Y');

        // if ($sessionroletypecode) {
        //     $query->where('rtm.roletypecode', '<=', $sessionroletypecode);
        // }

        if($get_desigorderid)
        {
            $query->where('desig.orderid', '>', $get_desigorderid);
        }

        if ($sessiondeptcode) {
            $query->where('deptuserdetails.deptcode', '=', $sessiondeptcode);
        }
        if ($sessionregioncode) {
            $query->where('rtm.regioncode', '=', $sessionregioncode);
        }
        if ($sessiondistcode) {
            $query->where('rtm.distcode', '=', $sessiondistcode);
            // $query->where('rtm.distcode', 'is not null');
        }




        $query->where('deptuserdetails.deptuserid', '!=', $sessionuserid);

        $query->when($userid, function ($query) use ($userid) {
            $query->where('deptuserdetails.deptuserid', '=', $userid);
        });

        $query->orderBy('deptuserdetails.createdon', 'desc');

        $querySql = $query->toSql();
        $bindings = $query->getBindings();

        $finalQuery = vsprintf(
            str_replace('?', "'%s'", $querySql),
            array_map('addslashes', $bindings)
        );
       //print_r($finalQuery);


        return $query->get(); // Return the results
    }

    /************************************************* User Form - Function ********************************************/


    /************************************************* Assign Charge Form - Function ********************************************/


    public static function getDesignationFromChargeDetails($table, $data)
    {
        $query = DB::table('audit.chargedetails as c')
            ->distinct()
            ->select('d.desigcode', 'd.desigelname', 'd.desigtlname')
            ->join("$table as d", 'd.desigcode', '=', 'c.desigcode')
            ->where('c.statusflag', 'Y');

        // Apply role type-based filtering
        $hoRoleTypeCode = View::shared('Ho_roletypecode');
        $reRoleTypeCode = View::shared('Re_roletypecode');
        $distRoleTypeCode = View::shared('Dist_roletypecode');

        if (in_array($data['roletypecode'], [$hoRoleTypeCode, $reRoleTypeCode, $distRoleTypeCode])) {
            $query->where('c.deptcode', $data['deptcode']);

            if (in_array($data['roletypecode'], [$reRoleTypeCode, $distRoleTypeCode])) {
                $query->where('c.regioncode', $data['regioncode']);

                if ($data['roletypecode'] === $distRoleTypeCode) {
                    $query->where('c.distcode', $data['distcode']);
                }
            }
        }

        // Exclude records that are already in userchargedetails
        // $query->whereNotIn('c.chargeid', function ($subQuery) {
        //     $subQuery->select('chargeid')
        //         ->from('audit.userchargedetails')
        //         ->where('statusflag', 'Y');
        // });

        return $query->get();
    }

    public static function getchargedescription($table, $data)
    {
        $query = DB::table('audit.chargedetails as c')
            ->select('c.chargeid', 'c.chargedescription')
            ->where('c.statusflag', 'Y');

        // Apply role type-based filtering
        $hoRoleTypeCode = View::shared('Ho_roletypecode');
        $reRoleTypeCode = View::shared('Re_roletypecode');
        $distRoleTypeCode = View::shared('Dist_roletypecode');

        $query->where('c.desigcode', $data['desigcode']);

        if (in_array($data['roletypecode'], [$hoRoleTypeCode, $reRoleTypeCode, $distRoleTypeCode])) {
            $query->where('c.deptcode', $data['deptcode']);

            if (in_array($data['roletypecode'], [$reRoleTypeCode, $distRoleTypeCode])) {
                $query->where('c.regioncode', $data['regioncode']);
  		$query->where('c.instmappingcode', $data['instmappingcode']);

                if ($data['roletypecode'] === $distRoleTypeCode) {
                    $query->where('c.distcode', $data['distcode']);
                }
            }
        }

        $querySql = $query->toSql();
        $bindings = $query->getBindings();

        $finalQuery = vsprintf(
            str_replace('?', "'%s'", $querySql),
            array_map('addslashes', $bindings)
        );

        // /print_r($finalQuery);

        // Exclude records that are already in userchargedetails
        // $query->whereNotIn('c.chargeid', function ($subQuery) {
        //     $subQuery->select('chargeid')
        //         ->from('audit.userchargedetails')
        //         ->where('statusflag', 'Y');
        // });


        return $query->get();
    }

    // public static function getuserbasedonroletype($data)
    // {
    //     $query  =    DB::table(self::$auditorinstmappingTable.' as ainm')
    //     ->join(self::$deptTable." as u" , 'u.distcode', '=', 'ainm.distcode')
    //     ->select('u.deptuserid', 'u.username')
    //     ->where('u.statusflag', 'Y')
    //     ->where('u.desigcode', $data['desigcode'])
    //     ->whereNotIn('u.deptuserid', function ($subQuery) {
    //         $subQuery->from('audit.userchargedetails')
    //             ->select('userid')
    //             ->where('statusflag', 'Y');
    //     });

    //     if(($sessionroletypecode ==  View::shared('Re_roletypecode')) || ($sessionroletypecode ==  View::shared('Dist_roletypecode')))
    //     {
    //         $query->where("ainm.roletypecode", $data['roletypecode']);
    //         if(($sessionroletypecode ==  View::shared('Re_roletypecode')))
    //         $query->where("c.regioncode", $data['regioncode']);
    //         if(($sessionroletypecode ==  View::shared('Dist_roletypecode')))
    //         $query->where("c.distcode", $data['distcode']);
    //     }
    //     $query->get();
    // }

    // public static function getUserBasedOnRoleType($data)
    // {
    //     $query = DB::table(self::$userdetailTable . ' as u')
    //         // ->join(self::$userdetailTable . ' as u', 'u.deptcode', '=', 'ainm.deptcode')
    //         ->join(self::$auditorinstmappingTable . ' as ainm', 'u.deptcode', '=', 'ainm.deptcode')
    //         ->distinct(['u.deptuserid', 'u.username']) // Select distinct rows based on specified columns
    //         ->where('u.statusflag', '=', 'Y')         // Filter where statusflag is 'Y'
    //         ->where('u.desigcode', $data['desigcode']);
    //     if ($data['page'] == 'assigncharge') {
    //         $query->where('u.chargeassigned', 'N');
    //         $multicharge = 'N';
    //     } else {

    //         $desig_query = DB::table(self::$designationTable . ' as des')
    //         ->where('des.desigcode', $data['desigcode'])
    //        ->get();

    //        $multicharge  =  $desig_query[0]->multicharge;

    //     //   echo  $multicharge ;

    //     //    print_r($desig_query);
    //     //    exit;



    //         $query->whereNotIn('u.deptuserid', function ($subQuery) use ($data) {
    //             $subQuery->from('audit.userchargedetails')
    //                 ->select('userid')
    //                 ->where('statusflag', 'Y')
    //                 ->where('chargeid', '=', $data['chargeid']);
    //         });

    //         $query->where('u.chargeassigned', 'Y');



    //         // print_r()

    //     }




    //     if (($data['roletypecode'] == View::shared('Re_roletypecode')) || $data['roletypecode'] == View::shared('Dist_roletypecode')) {
    //         $query->where("ainm.roletypecode", $data['roletypecode']);

    //         if ($data['roletypecode'] == View::shared('Re_roletypecode')) {
    //             $query->where("ainm.regioncode", $data['regioncode']);
    //         }
    //         elseif ( ($data['roletypecode'] == View::shared('Dist_roletypecode'))  && ($multicharge == 'Y') && ($data['page'] == 'additionalcharge') ){
    //             // $query->where("u.distcode", $data['distcode']);
    //         }
    //         elseif ($data['roletypecode'] == View::shared('Dist_roletypecode')&& (($multicharge == '') || ($multicharge == null)  || ($multicharge == 'N') ) && (($data['page'] == 'additionalcharge') || ($data['page'] == 'assigncharge') )) {
    //             $query->where("u.distcode", $data['distcode']);
    //         }
    //     }

    //     // dd($query->tosql());
    //     // Replace placeholders and display the query
    //     // $querySql = $query->toSql();
    //     // $bindings = $query->getBindings();

    //     // $finalQuery = vsprintf(
    //     //     str_replace('?', "'%s'", $querySql),
    //     //     array_map('addslashes', $bindings)
    //     // );

    //     // print_r($finalQuery);

    //     return $query->get();
    // }


       public static function getUserBasedOnRoleType($data)
    {
        // Get designation details
        $desig_query = DB::table(self::$designationTable . ' as des')
            ->where('des.desigcode', $data['desigcode'])
            ->first();

        $multicharge = $desig_query?->multicharge ?? 'N';

        $query = DB::table(self::$userdetailTable . ' as u')
            ->leftJoin(self::$auditorinstmappingTable . ' as ainm', 'u.distcode', '=', 'ainm.distcode')
            ->distinct()
            ->select('u.deptuserid', 'u.username')
            ->where('u.statusflag', 'Y');

        if (in_array($data['page'], ['additionalcharge', 'unassigncharge'])) {
            $query->whereNotIn('u.deptuserid', function ($subQuery) use ($data) {
                $subQuery->from('audit.userchargedetails')
                    ->select('userid')
                    ->where('statusflag', 'Y')
                    ->where('chargeid', $data['chargeid']);
            });
            // $query->where('u.chargeassigned', 'Y');
        }

        if ($data['include_otherdeptment'] == 'Y') {
            $query->whereIn('u.deptuserid', function ($sub) use ($data) {
                $sub->select('uc.userid')
                    ->from('audit.userchargedetails as uc')
                    ->join('audit.deptuserdetails as u', 'u.deptuserid', '=', 'uc.userid')
                    ->where('uc.statusflag', 'Y')
                    ->where('u.statusflag', 'Y')
                    ->where('u.deptcode', $data['otherdeptcode'])
                    ->where('u.desigcode', $data['otherdept_desigcode']);

                if ($data['otherdept_roletypecode'] == View::shared('Dist_roletypecode')) {
                    $sub->whereNotNull('u.distcode');
                } else {
                    $sub->whereNull('u.distcode');
                }
            });
        } else {
            $query->where('u.desigcode', $data['desigcode']);

            $roleType = $data['roletypecode'];
            $query->where('u.deptcode', $data['deptcode']);

            if (in_array($data['page'], ['additionalcharge', 'unassigncharge']))
            {
                $query->whereIn('u.deptuserid', function ($sub) use ($data,$multicharge) {
                    $sub->select('uc.userid')
                        ->from('audit.userchargedetails as uc')
                        ->join('audit.deptuserdetails as u', 'u.deptuserid', '=', 'uc.userid')
                        ->where('uc.statusflag', 'Y')
                        ->where('u.statusflag', 'Y');
                });

                if(($data['roletypecode'] == View::shared('Dist_roletypecode')))
                {
                    if(($multicharge == 'Y') && ($data['page'] == 'additionalcharge') ){
                        $query->whereNotNull("u.distcode");

                    }
                    else if ((!$multicharge || $multicharge == 'N') && ($data['page'] == 'additionalcharge' || $data['page'] == 'assigncharge'))
                    {
                        $query->where("u.distcode", '=', $data['distcode']);
                    }
                }
                else
                {
                    $query->whereNull("u.distcode");
                }
            }
            else
            {
                if ($data['roletypecode'] == View::shared('Dist_roletypecode')) {
                    $query->where("u.distcode", $data['distcode']);
               } else {
                   $query->whereNull('u.distcode');
               }
                $query->wherenotIn('u.deptuserid', function ($sub) use ($data,$multicharge) {
                    $sub->select('uc.userid')
                        ->from('audit.userchargedetails as uc')
                        ->join('audit.deptuserdetails as u', 'u.deptuserid', '=', 'uc.userid')
                        ->where('uc.statusflag', 'Y')
                        ->where('u.statusflag', 'Y');

                });

            }
        }
        return $query->get();
    }



    // public static function assigncharge_insertupdate(array $data, $userchargeid = null, $table)
    // {
    //     try {

    //         //Check if the role mapping exists and get the ID
    //         $useridexists = DB::table('audit.deptuserdetails as d')
    //             ->where('d.deptuserid', $data['userid'])
    //             ->value('deptuserid');

    //         $chargeidexists = DB::table('audit.chargedetails as c')
    //             ->where('c.chargeid', $data['chargeid'])
    //             ->value('chargeid');



    //         if (!$useridexists) {
    //             throw new \Exception('UserId does not exist.');
    //         }

    //         if (!$chargeidexists) {
    //             throw new \Exception('ChargeId does not exist.');
    //         }

    //         // Build where conditions dynamically
    //         $wherecondition = [
    //             'userid' => $data['userid'],
    //             'chargeid'     => $data['chargeid'],
    //         ];

    //         // Start building the query
    //         $query = DB::table($table)->where($wherecondition);

    //         // Exclude the current chargeid if updating
    //         if ($userchargeid) {
    //             $query->where('userchargeid', '<>', $userchargeid);
    //         }

    //         // Check if the record already exists
    //         if ($query->exists()) {
    //             throw new \Exception('ChargeExist');
    //         }




    //         // Insert or update the record based on chargeid
    //         if ($userchargeid) {
    //             $affectedRows = DB::table($table)->where('userchargeid', $userchargeid)->update($data);
    //             if ($affectedRows === 0) {
    //                 throw new \Exception('Failed to update the record.');
    //             }
    //         } else {
    //             $newRecordId = DB::table($table)->insertGetId($data, 'userchargeid');

    //             if (!$newRecordId) {
    //                 throw new \Exception('Failed to insert the new record.');
    //             }

    //             $password = '123456'; // Password to hash

    //             $hashedPassword = Hash::make($password);

    //             $affectedRows = DB::table('audit.deptuserdetails')->where('deptuserid', $data['userid'])->update(array('pwd' => $hashedPassword, 'chargeassigned' => 'Y'));
    //             if ($affectedRows === 0) {
    //                 throw new \Exception('Failed to update the record.');
    //             }

    //             //plogreturn $newRecordId;
    //         }
    //     } catch (\Exception $e) {
    //         throw new \Exception($e->getMessage());
    //     }
    // }

 public static function assigncharge_insertupdate(array $data, $userchargeid = null, $table,$page,$useriddel)
    {

        try {

            $getchargeauditorstatus = DB::table('audit.chargedetails as c')
            ->join(self::$rolemappingTable . " as rm", "rm.rolemappingid", '=', "c.rolemappingid")
            ->join(self::$roleactionTable . " as ra", "rm.roleactioncode", '=', "ra.roleactioncode")
            ->where('c.chargeid', $data['chargeid'])
		->select('auditorstatus', 'rm.roleactioncode')
                ->first();



            if (!$getchargeauditorstatus) {
                throw new \Exception('ChargeId does not exist.');
            }

            $allowedChargeCount = self::getAllowedChargeMemberCount();
            $chargeScope = self::getChargeAllocationScope($data['chargeid']);

  	    $psa_role = false;
            if (
                $getchargeauditorstatus && in_array($getchargeauditorstatus->roleactioncode, View::shared('psa_roleaction'))

            ) {
                $psa_role = true;
            }

            if($page == 'assigncharge')
            {
                foreach($useriddel as $userid)
                {
                    $useridexists = DB::table('audit.deptuserdetails as d')
                    ->where('d.deptuserid', $userid)
                    ->value('deptuserid');

                    if (!$useridexists) {
                        throw new \Exception('UserId does not exist.');
                    }


                        // Build where conditions dynamically
                    $wherecondition = [
                        'userid' => $userid,
                        'chargeid'     => $data['chargeid'],
                        'statusflag' => 'Y'
                    ];

                    // Start building the query
                    $query = DB::table($table)->where($wherecondition);


                    if ($query->exists()) {
                        throw new \Exception('ChargeExist');
                    }

                    if ($userchargeid) {
                        $affectedRows = DB::table($table)->where('userchargeid', $userchargeid)->update($data);
                        if ($affectedRows === 0) {
                            throw new \Exception('Failed to update the record.');
			}
			if ($psa_role) {
                            $updated = self::updatePSAFlag($data['userid'], 'Y');

                            if ($updated === false) {
                                throw new \Exception('Failed to update PSA flag.');
                            }
                        }
                    } else {
                        $data['userid'] =   $userid;
                        $newRecordId = DB::table($table)->insertGetId($data, 'userchargeid');

                        if (!$newRecordId) {
                            throw new \Exception('Failed to insert the new record.');
			}
			if ($psa_role) {
                            $updated = self::updatePSAFlag($data['userid'], 'Y');

                            if ($updated === false) {
                                throw new \Exception('Failed to update PSA flag.');
                            }
                        }
                        //$password = 'Cams@123'; // Password to hash
			            $password = 'Dgcams@2025';

                        $hashedPassword = Hash::make($password);

                        $udpatedeptuser =   array('pwd' => $hashedPassword, 'chargeassigned' => 'Y');

                        if($getchargeauditorstatus->auditorstatus == 'Y')  $udpatedeptuser['auditorflag']  =   'Y';


                        $affectedRows = DB::table('audit.deptuserdetails')->where('deptuserid', $data['userid'])->update($udpatedeptuser);
                        if ($affectedRows === 0) {
                            throw new \Exception('Failed to update the record.');
                        }
                    }



                }
            }
            else if($page == 'additionalcharge')
            {
                  //Check if the role mapping exists and get the ID
                $useridexists = DB::table('audit.deptuserdetails as d')
                ->where('d.deptuserid', $data['userid'])
                ->value('deptuserid');

                if (!$useridexists) {
                    throw new \Exception('UserId does not exist.');
                }


                // Build where conditions dynamically
                $wherecondition = [
                    'userid' => $data['userid'],
                    'chargeid'     => $data['chargeid'],
                    'statusflag' => 'Y'

                ];

                // Start building the query
                $query = DB::table($table)->where($wherecondition);

                // Exclude the current chargeid if updating
                if ($userchargeid) {
                    $query->where('userchargeid', '<>', $userchargeid);
                }

                // Check if the record already exists
                if ($query->exists()) {
                    throw new \Exception('ChargeExist');
                }

                $isHelpdeskRole = (string) ($getchargeauditorstatus->roleactioncode ?? '') === (string) View::shared('Helpdesk_roleactioncode');

                if ($isHelpdeskRole) {
                    $activeChargeUserCount = self::getActiveDeptChargeUserCount($chargeScope, $data['userid']);

                    if ($activeChargeUserCount >= $allowedChargeCount) {
                        throw new \Exception('chargecheck');
                    }
                }

                if ($userchargeid) {
                    $affectedRows = DB::table($table)->where('userchargeid', $userchargeid)->update($data);
                    if ($affectedRows === 0) {
                        throw new \Exception('Failed to update the record.');
		    }
		     if ($psa_role) {
                        $updated = self::updatePSAFlag($data['userid'], 'Y');

                        if ($updated === false) {
                            throw new \Exception('Failed to update PSA flag.');
                        }
                    }
                } else {
                    $newRecordId = DB::table($table)->insertGetId($data, 'userchargeid');

                    if (!$newRecordId) {
                        throw new \Exception('Failed to insert the new record.');
                    }

		    if ($psa_role) {

                        $updated = self::updatePSAFlag($data['userid'], 'Y');

                        if ($updated === false) {
                            throw new \Exception('Failed to update PSA flag.');
                        }
                    }
                     //$password = 'Cams@123'; // Password to hash
			$password = 'Dgcams@2025';

                    $hashedPassword = Hash::make($password);

                    $udpatedeptuser =   array( 'chargeassigned' => 'Y');

		    if ($getchargeauditorstatus->auditorstatus == 'Y')  $udpatedeptuser['auditorflag']  =   'Y';





                    $affectedRows = DB::table('audit.deptuserdetails')->where('deptuserid', $data['userid'])->update($udpatedeptuser);
                    if ($affectedRows === 0) {
                        throw new \Exception('Failed to update the record.');
                    }
                }

            }

            // Insert or update the record based on chargeid

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    public static function fetchuserchargeData($page)
    {
        $sessiondetails =   session('charge');
        $sessionroletypecode    =   $sessiondetails->roletypecode;
        $sessiondeptcode   =   $sessiondetails->deptcode;
        $sessionregioncode    =   $sessiondetails->regioncode;
        $sessiondistcode    =   $sessiondetails->distcode;



        // Build the query and apply the 'chargeid' condition if it's provided
        $query  =  DB::table(self::$userchargedetailTable . ' as uc')
            ->join(self::$chargedetailTable . " as c", "c.chargeid", '=', "uc.chargeid")
            ->join(self::$userdetailTable . " as u", "u.deptuserid", '=', "uc.userid")
            ->join(self::$rolemappingTable . " as rm", "rm.rolemappingid", '=', "c.rolemappingid")
            ->join(self::$roleactionTable . " as ra", "rm.roleactioncode", '=', "ra.roleactioncode")
            ->join(self::$roletypemappingTable . " as rtm", "rtm.roletypemappingcode", '=', "rm.roletypemappingcode")
            ->join(self::$roletypeTable . " as rt", "rt.roletypecode", '=', "rtm.roletypecode")
            ->join(self::$deptTable . " as d", "d.deptcode", '=', "c.deptcode")
            ->leftjoin(self::$regionTable . " as r", "r.regioncode", '=', "c.regioncode")
            ->leftjoin(self::$distTable . " as di", "di.distcode", '=', "c.distcode")
            ->leftjoin(self::$auditorinstmappingTable . " as ins", "ins.instmappingcode", '=', "c.instmappingcode")
            ->join(self::$designationTable . " as des", "des.desigcode", '=', "c.desigcode")

            ->select(
                "rt.roletypecode",
                'rt.roletypeelname',
                'rt.roletypetlname',
                "ra.roleactioncode",
                'ra.roleactionelname',
                'ra.roleactiontlname',
                'r.regionename',
                "c.regioncode",
                "r.regiontname",
                "c.distcode",
                'di.distename',
                "di.disttname",
                "c.instmappingcode",
                'ins.instename',
                'ins.insttname',
                'd.deptesname',
                "c.deptcode",
                "c.desigcode",
                'des.desigesname',
                'des.desigtsname',
                "c.chargedescription",
                "c.chargeid",
                "u.deptuserid",
                'u.username',
                'u.ifhrmsno',
                'u.email',
                 'u.reservelist'
            );

         // If the page is either 'assigncharge' or 'additionalcharge'
        if(($page == 'assigncharge') || ($page == 'additionalcharge')) {
            // Add condition to check if statusflag is 'Y'
            $query->where("uc.statusflag", '=', 'Y');

            // If it's 'assigncharge', set chargeflag to 'P'
            if($page == 'assigncharge') {
                $query->where("uc.chargeflag", '=', 'P');
            }

            // If it's 'additionalcharge', set chargeflag to 'A'
            if($page == 'additionalcharge') {
                $query->where("uc.chargeflag", '=', 'A');
            }
        }
        // If the page is 'unassigncharge', set statusflag to 'N'
        else if($page == 'unassigncharge') {
            $query->where("uc.statusflag", '=', 'N');
        }
        if (($sessionroletypecode ==  View::shared('Ho_roletypecode')) || ($sessionroletypecode ==  View::shared('Re_roletypecode')) || ($sessionroletypecode ==  View::shared('Dist_roletypecode'))) {
            $query->where("c.deptcode", $sessiondeptcode);
            if (($sessionroletypecode ==  View::shared('Re_roletypecode')) || ($sessionroletypecode ==  View::shared('Dist_roletypecode'))) {
                $query->where("c.regioncode", $sessionregioncode);
                if (($sessionroletypecode ==  View::shared('Dist_roletypecode')))
                    $query->where("c.distcode", $sessiondistcode);
            }
        }
        $query->orderBy('uc.updatedon', 'desc');

        // $query->when($chargeid, function ($query) use ($chargeid) {
        //     $query->where('chargeid', $chargeid);
        // });

        //dd($query->tosql());
        // Return the results directly
        return $query->get();
    }







    public static function getuserbasedonroletype_unassigncharge($data)
    {
        $query = DB::table(self::$userchargedetailTable . ' as uc')
            ->join(self::$userdetailTable . ' as u', 'u.deptuserid', '=', 'uc.userid')
            ->join(self::$auditorinstmappingTable . ' as ainm', 'u.deptcode', '=', 'ainm.deptcode')
 	    ->join(self::$chargedetailTable . ' as c', 'c.chargeid', '=', 'uc.chargeid')
            ->distinct(['u.deptuserid', 'u.username']) // Select distinct rows based on specified columns
            ->where('u.statusflag', '=', 'Y')
            ->where('uc.statusflag', '=', 'Y')         // Filter where statusflag is 'Y'
            ->where('c.desigcode', $data['desigcode'])
            ->where('uc.chargeid', $data['chargeid']);

        if (($data['roletypecode'] == View::shared('Re_roletypecode')) || $data['roletypecode'] == View::shared('Dist_roletypecode')) {
            $query->where("ainm.roletypecode", $data['roletypecode']);

            if ($data['roletypecode'] == View::shared('Re_roletypecode')) {
                $query->where("ainm.regioncode", $data['regioncode']);
            }
            elseif ( ($data['roletypecode'] == View::shared('Dist_roletypecode')) ){
                // $query->where("u.distcode", $data['distcode']);
            }
            elseif ($data['roletypecode'] == View::shared('Dist_roletypecode')) {
                $query->where("u.distcode", $data['distcode']);
            }
        }
        return $query->get();
    }


 public static function checkUserHasNoPending($userid, $chargeid)
    {
        $getquarter = DB::table('audit.chargedetails as ch')
            ->join('audit.mst_dept as dp', 'dp.deptcode', '=', 'ch.deptcode')
            ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'ch.rolemappingid')
            ->join('audit.mst_roleaction as ro', 'ro.roleactioncode', '=', 'rm.roleactioncode')
            ->where('ch.chargeid', $chargeid)
            ->select('dp.currentquarter', 'ro.roleactioncode')
            ->first();

        $currentquarter = $getquarter->currentquarter;
        $roleactioncode = $getquarter->roleactioncode;

        $auditPlans = collect();
        if ($roleactioncode == '04') {
            // Query the database for audit plans that match the given conditions
            // $auditPlans = DB::table('audit.auditplanteammember as apm')
            //     ->join('audit.auditplan as ap', 'ap.auditteamid', '=', 'apm.auditplanteamid')
            //     ->where('apm.userid', $userid) // Check if the user ID matches
            //     ->where('ap.auditquartercode', $currentquarter)
            //     ->where('apm.statusflag', 'Y') // Ensure status is 'Y'
            //     ->whereNotIn('ap.auditplanid', function ($query) use ($userid, $currentquarter) {
            //         // Subquery to exclude audit plans where the user has already participated
            //         $query->select('ap.auditplanid')
            //             ->from('audit.inst_schteammember as insc')
            //             ->join('audit.inst_auditschedule as ia', 'ia.auditscheduleid', '=', 'insc.auditscheduleid')
            //             ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ia.auditplanid')
            //             ->where('ap.auditquartercode', $currentquarter)
            //             ->where('insc.userid', $userid) // Check if the user ID matches in the subquery
            //             ->where('insc.statusflag', 'Y') // Ensure the status is 'Y' in the subquery
            //             ->whereNotNull('ia.exitmeetdate'); // Ensure exitmeetdate is not null
            //     })
            //     ->whereNotIn('ap.instid', function ($query) {
            //         $query->select('instid')
            //             ->from('audit.temp_inst_q1_pending')
            //             ->where('quartercode', 'Q2');
            //     })
            //     ->get(); // Get the results
            $auditPlans = DB::table('audit.inst_schteammember as insc')
    ->join('audit.inst_auditschedule as ia', 'ia.auditscheduleid', '=', 'insc.auditscheduleid')
    ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ia.auditplanid')
    ->where('ap.auditquartercode', $currentquarter)
    ->where('insc.userid', $userid)
    ->where('insc.statusflag', 'Y')
    ->where('ia.statusflag', 'F')
    ->whereNotNull('ia.entrymeetdate')
    ->whereNull('ia.exitmeetdate')
    ->select('ap.auditplanid')
    ->get();
            //      > $querySql = $auditPlans->toSql();
            // $bindings = $auditPlans->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );

            // print_r($finalQuery);
            // exit;



        }else if ($roleactioncode == View::shared('PUADroleactioncode')) {
            $auditPlans = DB::table(self::$historytranspara_table . ' as hist')
                ->join(self::$transpara_table . ' as para', 'hist.paraid', '=', 'para.paraid')
                ->where('para.forwardedtouserid', $userid)
	// ->whereNotIn('para.processcode', ['A', 'I'])
	        ->whereIn('para.processcode', ['K'])
                ->select('para.paraid')
                ->get();
        } else if ($roleactioncode == View::shared('PUroleactioncode')) {
            $auditPlans = DB::table(self::$historytranspara_table . ' as hist')
                ->join(self::$transpara_table . ' as para', 'hist.paraid', '=', 'para.paraid')
                ->where('para.forwardedtouserid', $userid)
		//->whereNotIn('para.processcode', ['A', 'I'])
		->whereIn('para.processcode', ['F'])
                ->select('para.paraid')
                ->get();
        }
        return $auditPlans;

        // Return the retrieved audit plans
    }




    public static function unassigncharge_insertupdate(array $data, array $wheredata)
    {
        try {
            // First query - Get the user's charge details
            $query = DB::table(self::$userchargedetailTable . ' as uc')
                ->where('uc.statusflag', '=', 'Y')
                ->where('uc.userid', $wheredata['userid'])
                ->get();

            $countOf_charges_userhave = count($query);

            // Second query - Get charge auditor status
            $getchargeauditorstatus = DB::table('audit.chargedetails as c')
                ->join(self::$rolemappingTable . " as rm", "rm.rolemappingid", '=', "c.rolemappingid")
                ->join(self::$roleactionTable . " as ra", "rm.roleactioncode", '=', "ra.roleactioncode")
                ->where('c.chargeid', $wheredata['chargeid'])
		->select('auditorstatus', 'rm.roleactioncode')
                ->first();

				  $psa_role = false;
            if (
                $getchargeauditorstatus && in_array($getchargeauditorstatus->roleactioncode, View::shared('psa_roleaction'))

            ) {
                $psa_role = true;
            }
            // Third query - Get user charge details
            $query = DB::table(self::$userchargedetailTable . ' as uc')
                ->where('uc.statusflag', '=', 'Y')
                ->where('uc.userid', $wheredata['userid'])
                ->where('uc.chargeid', $wheredata['chargeid'])
                ->get();

            if ($query->isEmpty()) {
                throw new \Exception('User charge detail not found.');
            }

            $userchargeid = $query[0]->userchargeid;
            $userid = $query[0]->userid;

            // Check if user charge exists
            if ($userchargeid) {
                // Update user charge details
                $affectedRows = DB::table(self::$userchargedetailTable)
                    ->where('userchargeid', $userchargeid)
                    ->update($data);

                // After update, prepare data for updating the user table
                $update_usertable = [];
                if ($countOf_charges_userhave == 1) {
                    $update_usertable['chargeassigned'] = 'N';
                }

                if ($getchargeauditorstatus->auditorstatus == 'Y') {
                    $update_usertable['auditorflag'] = 'N';
                }

                // Update user table if necessary
                if (!empty($update_usertable)) {
                    DB::table(self::$userdetailTable)
                        ->where('deptuserid', $userid)
                        ->update($update_usertable);
		}
		if ($psa_role) {
                    $updated = self::updatePSAFlag($userid, 'N');

                    if ($updated === false) {
                        throw new \Exception('Failed to update PSA flag.');
                    }
                }
            }

            return true; // Success response, you can return more data if necessary
        } catch (\Exception $e) {
            // Catch all exceptions and throw them for the controller to handle
            throw new \Exception($e->getMessage());
        }
    }

  public static function updatePSAFlag($userid, $flag)
    {
        try {
            $affectedRows = DB::table(self::$userdetailTable)
                ->where('deptuserid', $userid)
                ->update([
                    'psaflag' => $flag
                ]);

            return $affectedRows;
        } catch (\Exception $e) {
            return false;
        }
    }

 public static function additionalcharge_check($userid, $chargeid)
    {
        $check = DB::table('audit.chargedetails as ch')
            ->join('audit.userchargedetails as uc', 'uc.chargeid', '=', 'ch.chargeid')
	    ->where('uc.userid', $userid)
	    ->where('ch.chargeid', $chargeid)
            ->select('uc.chargeflag', 'uc.userid')
            ->first();
        if ($check->chargeflag != 'P') {
            return true;
        }

        if ($check->chargeflag == 'P') {
            $hasAdditional_charge = DB::table('audit.userchargedetails as uc')
                ->where('uc.userid', $check->userid)
                ->where('uc.statusflag', 'Y')
                ->where('uc.chargeflag', 'A')
                ->exists();

            $hasadditional = $hasAdditional_charge ? false : true;
            return $hasadditional;
        }
    }

     public static function deptfetch()
    {
        return DB::table(self::$deptTable.' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname') // Select required columns
            ->where('dept.statusflag', '=', 'Y') // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }

    public static function getauditeedept($deptcode)
    {
        return DB::table(self::$auditeedept)
            // ->select()
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->get(['auditeedeptename', 'auditeedepttname', 'auditeedeptcode']);
    }

    public static function fetchdesinationData($deptcode)
    {
        return DB::table(self::$designationTable)
            // ->select()
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->where('isauditeeedepartment', 'N')
            ->get(['desigelname', 'desigtlname', 'desigesname', 'desigtsname', 'desigcode']);
    }

    public static function getroletypecode_basedondesig($deptcode)
    {
        $rolemappingid = DB::table('audit.rolemapping as rm')
            ->join('audit.roletypemapping as rtm', 'rtm.roletypemappingcode', '=', 'rm.roletypemappingcode')
            ->where('rm.roleactioncode', View::shared('roleaction_for_hodcharge'))
            ->where('rtm.roletypecode', View::shared('roletype_for_hodcharge'))
            ->where('rtm.deptcode', $deptcode)
            ->where('rtm.statusflag', 'Y')
            ->value('rm.rolemappingid');

        return $rolemappingid;   // ✅ IMPORTANT
    }

    public static function insertUpdatehodcreatecharge(array $data, $chargeid = null)
    {

        try {
            $table = self::$hodcreatechargetable;

            $deptcode = $data['deptcode'] ?? null;
            $auditeedeptcode = $data['auditeedeptcode'] ?? null;
            $desigcode = $data['desigcode'] ?? null;

            // if (!$deptcode || !$auditeedeptcode || !$desigcode) {
            //     throw new Exception('Required fields missing.', 422);
            // }

            $recordId = $chargeid ?? null;

            //    dd($recordId);

            $existsQuery = DB::table($table)
                ->where('deptcode', $deptcode)
                ->where('auditeedeptcode', $auditeedeptcode)
                ->where('desigcode', $desigcode);

            // Exclude current record during update
            if (! empty($recordId)) {
                $existsQuery->where('chargeid', '<>', $recordId);
            }

            if ($existsQuery->exists()) {
                throw new Exception('charge_exists', 409);
            }

            if (! empty($recordId)) {

                $affectedRows = DB::table($table)
                    ->where('chargeid', $recordId)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new Exception('No changes were made or record not found.', 404);
                }

                return (object) ['chargeid' => $recordId];
            }



            $newId = DB::table($table)->insertGetId($data, 'chargeid');

            if (! $newId) {
                throw new Exception('Failed to insert record.', 500);
            }

            //  dd($newId);

            return (object) ['chargeid' => $newId];

        } catch (\Illuminate\Database\QueryException $e) {
            dd($e->errorInfo);
        }
    }

    public static function fetchhod_createcharge($chargeid, $table)
    {

        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;

        $query = DB::table($table.' as cd')
            ->join(self::$deptTable.' as d', 'd.deptcode', '=', 'cd.deptcode')
            ->join(self::$auditeedept.' as ad', 'ad.auditeedeptcode', '=', 'cd.auditeedeptcode')
            ->join(self::$designationTable.' as des', 'des.desigcode', '=', 'cd.desigcode')
            ->leftjoin(self::$hodassigchargetable.' as hs', 'hs.chargeid', '=', 'cd.chargeid')

            ->select(
                'cd.chargeid',
                'cd.chargedescription',
                'cd.statusflag as chargestatus',
                'd.deptcode',
                'd.deptesname',
                'd.depttsname',
                'd.deptelname',
                'd.depttlname',
                'ad.auditeedeptename',
                'ad.auditeedepttname',
                'ad.auditeedeptcode',
                'des.desigesname',
                'des.desigtsname',
                'des.desigelname',
                'des.desigtlname',
                'des.desigcode',
                'hs.statusflag'
            )
            ->when($chargeid, function ($query) use ($chargeid) {
                $query->where('cd.chargeid', $chargeid);
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
                $query->where('d.deptcode', '=', $sessiondeptcode);
            });
        $query->orderBy('cd.updatedon', 'desc');

        return $query->get();
    }
    // ------------------------------------------HOD Charge create end -----------------------------------------------------------

    // ----------------------------- HOD Assign charge start--------------------------------------------

    public static function getcategoryByDept($deptcode)
    {
        return DB::table(self::$mstauditeeinscategory_table)
            // ->select()
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->get(['catcode', 'catename', 'cattname']);
    }

    public static function getSubcategoryByCategory($cat_code)
    {
        $table = self::$mstauditeeinscategory_table;

        $query = DB::table($table.' as aud')
            ->join(self::$subcategory.' as sub', 'aud.catcode', '=', 'sub.catcode')
            ->select(
                'sub.subcatename',
                'sub.subcattname',
                'sub.auditeeins_subcategoryid',
                'aud.if_subcategory'
            )
            ->distinct()
            ->where('aud.if_subcategory', 'Y')
            ->orderBy('sub.subcatename', 'Asc');

        // ✅ Apply filter only if not empty
        if (! empty($cat_code)) {
            $query->whereIn('sub.catcode', $cat_code);
        }

        return $query->get();
    }

    public static function fetchchargeforhodassigncharge($deptcode, $auditeedeptcode, $desigcode)
    {
        return DB::table('audit.hodchargedetails')
            ->where('deptcode', $deptcode)
            ->where('auditeedeptcode', $auditeedeptcode)
            ->where('desigcode', $desigcode)
            ->where('statusflag', 'Y')
            ->get(['chargedescription', 'chargeid']);
    }

    public static function fetchuserforhodassigcharge(
        $deptcode,
        $auditeedeptcode,
        $desigcode,
        $catcode,
        $subcatid
    ) {

        $query = DB::table('audit.auditee_dept')
            ->where('deptcode', $deptcode)
            ->where('auditeedepartment', $auditeedeptcode)
            ->where('designation', $desigcode)
            ->where('statusflag', 'Y');

        if (! is_array($catcode)) {
            $catcode = [$catcode];
        }

        $catcode = array_filter($catcode);

        if (! empty($catcode)) {

            $values = "'".implode("','", $catcode)."'";

            $query->whereRaw("
            EXISTS (
                SELECT 1
                FROM jsonb_each(catcode) as j(key, value),
                     jsonb_array_elements_text(value) as elem
                WHERE elem IN ($values)
            )
        ");
        }

        if (! is_array($subcatid)) {
            $subcatid = [$subcatid];
        }

        $subcatid = array_filter($subcatid);

        if (! empty($subcatid)) {

            $values = "'".implode("','", $subcatid)."'";

            $query->whereRaw("
            EXISTS (
                SELECT 1
                FROM jsonb_each(auditeeins_subcategoryid) as j(key, value),
                     jsonb_array_elements_text(value) as elem
                WHERE elem IN ($values)
            )
        ");
        }

        return $query->get(['auditeedeptid', 'email', 'mobilenumber']);
    }

    public static function insertUpdatehodassigncharge(array $data, $userchargeid = null)
    {

        try {
            $table = self::$hodassigchargetable;

            $chargeid = $data['chargeid'] ?? null;
            $userid = $data['userid'] ?? null;

            // if (!$deptcode || !$auditeedeptcode || !$desigcode) {
            //     throw new Exception('Required fields missing.', 422);
            // }

            $recordId = $userchargeid ?? null;

            //    dd($recordId);

            $existsQuery = DB::table($table)
                ->where('chargeid', $chargeid)
                ->where('userid', $userid);
            // ->where('desigcode', $desigcode);

            // Exclude current record during update
            if (! empty($recordId)) {
                $existsQuery->where('userchargeid', '<>', $recordId);
            }

            if ($existsQuery->exists()) {
                throw new Exception('assignedalready', 409);
            }

            if (! empty($recordId)) {

                $affectedRows = DB::table($table)
                    ->where('userchargeid', $recordId)
                    ->update($data);

                if ($affectedRows === 0) {
                    throw new Exception('No changes were made or record not found.', 404);
                }

                return (object) ['userchargeid' => $recordId];
            }

            $newId = DB::table($table)->insertGetId($data, 'userchargeid');

            if (! $newId) {
                throw new Exception('Failed to insert record.', 500);
            }

            //  dd($newId);

            return (object) ['userchargeid' => $newId];

        } catch (\Illuminate\Database\QueryException $e) {
            dd($e->errorInfo);
        }
    }

    public static function fetchhodassigncharge($userchargeid, $table)
    {

        $sessiondet = session('charge');
        $sessiondeptcode = $sessiondet->deptcode;

        $query = DB::table($table.' as ucd')
            ->join('audit.hodchargedetails as cd', 'cd.chargeid', '=', 'ucd.chargeid')
            ->join('audit.auditee_dept as u', 'u.auditeedeptid', '=', 'ucd.userid')
            // ->join('audit.hodchargedetails as cd', 'cd.chargeid', '=', 'ucd.chargeid')$mstauditeeinscategory_table
            ->join(self::$deptTable.' as d', 'd.deptcode', '=', 'cd.deptcode')
            ->join(self::$auditeedept.' as ad', 'ad.auditeedeptcode', '=', 'cd.auditeedeptcode')
            ->join(self::$designationTable.' as des', 'des.desigcode', '=', 'cd.desigcode')
            ->leftJoin(DB::raw('
LATERAL (
    SELECT elem AS catcode_val
    FROM jsonb_each(u.catcode) j,
         jsonb_array_elements_text(j.value) elem
) catjson
'), DB::raw('true'), '=', DB::raw('true'))
            ->leftJoin(self::$mstauditeeinscategory_table.' as c',
                'c.catcode',
                '=',
                DB::raw('catjson.catcode_val')
            )
            ->leftJoin(DB::raw('
LATERAL (
    SELECT elem AS subcat_val
    FROM jsonb_each(u.auditeeins_subcategoryid) j,
         jsonb_array_elements_text(j.value) elem
) subjson
'), DB::raw('true'), '=', DB::raw('true'))
            ->leftJoin(self::$subcategory.' as sc',
                DB::raw('sc.auditeeins_subcategoryid'),
                '=',
                DB::raw("
        CASE
            WHEN subjson.subcat_val ~ '^[0-9]+$'
            THEN subjson.subcat_val::int
        END
    ")
            )

            ->select(
                'cd.chargeid',
                'cd.chargedescription',
                'd.deptcode',
                'd.deptesname',
                'd.depttsname',
                'd.deptelname',
                'd.depttlname',
                'ad.auditeedeptename',
                'ad.auditeedepttname',
                'ad.auditeedeptcode',
                'des.desigesname',
                'des.desigtsname',
                'des.desigelname',
                'des.desigtlname',
                'des.desigcode',
                'u.email',
                'u.mobilenumber',
                DB::raw("
                        CASE
                            WHEN catjson.catcode_val = 'A' THEN 'ALL'
                            ELSE c.catename
                        END as catename
                        "),

                DB::raw("
                        CASE
                            WHEN subjson.subcat_val = 'A' THEN 'ALL'
                            ELSE sc.subcatename
                        END as subcatename
                        "),

            )
            ->when($userchargeid, function ($query) use ($userchargeid) {
                $query->where('ucd.userchargeid', $userchargeid);
            })
            ->when($sessiondeptcode, function ($query) use ($sessiondeptcode) {
                $query->where('d.deptcode', '=', $sessiondeptcode);
            });
        $query->orderBy('cd.updatedon', 'desc');

        return $query->get();
    }


       private static function getAllowedChargeMemberCount(): int
    {
        $configuredCount = (int) View::shared('charge_membercount_limit');

        return $configuredCount > 0 ? $configuredCount : 1;
    }

    private static function getChargeAllocationScope($chargeid): ?object
    {
        return DB::table('audit.chargedetails')
            ->where('chargeid', $chargeid)
            ->select('deptcode', 'rolemappingid', 'desigcode')
            ->first();
    }

    private static function getActiveDeptChargeUserCount(?object $chargeScope, $excludeUserId = null): int
    {
        if (!$chargeScope || empty($chargeScope->deptcode) || empty($chargeScope->rolemappingid) || empty($chargeScope->desigcode)) {
            return 0;
        }

        $query = DB::table('audit.userchargedetails as uc')
            ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
            ->where('c.deptcode', $chargeScope->deptcode)
            ->where('c.rolemappingid', $chargeScope->rolemappingid)
            ->where('c.desigcode', $chargeScope->desigcode)
            ->where('uc.statusflag', 'Y');

        if ($excludeUserId !== null) {
            $query->where('uc.userid', '<>', $excludeUserId);
        }

        return (int) $query->distinct('uc.userid')->count('uc.userid');
    }


}

