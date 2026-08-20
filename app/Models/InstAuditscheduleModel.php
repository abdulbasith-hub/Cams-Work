<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

// class UserModel extends Model
// {
//     use HasFactory;
// }

class InstAuditscheduleModel extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL


    protected $table = 'audit.inst_auditschedule';
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


    /**
     * Create a new user if it doesn't already exist based on email, phone, name, and address.
     * Otherwise, update the user if it already exists, based on email, phone, and name (excluding current id).
     *
     * @param array $data
     * @param int|null $currentUserId (optional: pass the current user's id for updates)
     * @return User|false
     */


    public static function createIfNotExistsOrUpdate(array $data, $currentScheduleid = null)
    {
        try {
            // Check for duplicates before proceeding
            // if ($currentScheduleid) {
            //     // Check if email, mobile number or IFHRMS number exist with a different user
            //     $emailExists = self::where('email', $data['email'])
            //         ->where('deptuserid', '!=', $currentScheduleid)->exists();

            //     $mobileExists = self::where('mobilenumber', $data['mobilenumber'])
            //         ->where('deptuserid', '!=', $currentScheduleid)->exists();

            //     $ifhrmsnoExists = self::where('ifhrmsno', $data['ifhrmsno'])
            //         ->where('deptuserid', '!=', $currentScheduleid)->exists();

            //     // Check if the combination of all three fields (email, mobile, ifhrmsno) exists with a different user
            //     $existingUser = self::where('email', $data['email'])
            //         ->where('mobilenumber', $data['mobilenumber'])
            //         ->where('ifhrmsno', $data['ifhrmsno'])
            //         ->where('deptuserid', '!=', $currentScheduleid) // Ensure it's a different user
            //         ->first();
            // } else {
            //     // For new user, just check for individual field duplicates
            //     $emailExists = self::where('email', $data['email'])->exists();
            //     $mobileExists = self::where('mobilenumber', $data['mobilenumber'])->exists();
            //     $ifhrmsnoExists = self::where('ifhrmsno', $data['ifhrmsno'])->exists();

            //     // Check for the combination of all three fields for existing records
            //     $existingUser = self::where('email', $data['email'])
            //         ->where('mobilenumber', $data['mobilenumber'])
            //         ->where('ifhrmsno', $data['ifhrmsno'])
            //         ->first();
            // }

            // // Return specific error messages if duplicates are found
            // if ($emailExists) {
            //     throw new \Exception('The email address is already associated with a different user.');
            // }

            // if ($mobileExists) {
            //     throw new \Exception('The mobile number is already associated with a different user.');
            // }

            // if ($ifhrmsnoExists) {
            //     throw new \Exception('The IFHRMS number is already associated with a different user.');
            // }

            // if ($existingUser) {
            //     throw new \Exception('The combination of email, mobile number, and IFHRMS number is already associated with a different user.');
            // }

            // If no conflicts, proceed with create or update
            if (!empty($currentScheduleid)) {
                $existingUser = self::find($currentScheduleid);
                if ($existingUser) {
                    $existingUser->update($data);
                    return $existingUser;
                } else {
                    throw new \Exception("Record not found with ID: $currentScheduleid");
                }
            } else {
                return self::create($data);
            }
        } catch (\Exception $e) {
            // Throwing a custom exception with the message from the model
            throw new \Exception($e->getMessage());
        }
    }


    public static function fetchUserById($userId)
    {
        return self::find($userId);
    }

    // public static function  fetch_auditscheduledetails($userid)
    // {
    //     return self::query()
    //     ->join('audit.inst_schteammember as inm', 'inm.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
    //     ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'inst_auditschedule.auditplanid')
    //     ->join('audit.auditplanteam as at', 'ap.auditteamid', '=', 'at.auditplanteamid')
    //     ->join('audit.mst_institution as ai', 'ai.instid', '=', 'ap.instid')
    //     ->join('audit.audtieeuserdetails as auser', 'auser.instid', '=', 'ap.instid')
    //     ->join('audit.mst_typeofaudit as mst', 'mst.typeofauditcode', '=', 'ap.typeofauditcode')
    //     ->join('audit.mst_dept as msd', 'msd.deptcode', '=', 'ai.deptcode')
    //     ->join('audit.mst_auditeeins_category as mac', 'mac.catcode', '=', 'ai.catcode')
    //     ->join('audit.mst_auditquarter as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
    //     ->join('audit.userchargedetails as uc', 'uc.userchargeid', '=', 'inm.userid')
    //     ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
    //     ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
    //     ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
    //     ->select(
    //         'inst_auditschedule.auditscheduleid',
    //         'inst_auditschedule.fromdate',
    //         'inst_auditschedule.todate',
    //         'inst_auditschedule.auditeeresponse',
    //         'inm.userid',
    //         'du.username',
    //         'ai.instename',
    //         'ai.deptcode',
    //         'ai.instid',
    //         'ap.auditteamid',
    //         'ap.auditplanid',
    //         'at.teamname',
    //         'mst.typeofauditename',
    //         'msd.deptesname',
    //         'mac.catename',
    //         'maq.auditquarter',
    //         'ap.statusflag',
    //         'cd.chargedescription',
    //         'de.desigelname',
    //         'inm.auditteamhead'

    //     )
    //     // ->where('inst_auditschedule.auditscheduleid', function ($query) {
    //     //     $query->select('auditscheduleid')
    //     //         ->from('audit.inst_auditschedule')
    //     //         ->whereColumn('auditscheduleid', 'inst_auditschedule.auditscheduleid')
    //     //         ->where('statusflag', 'F');
    //     // })
    //     ->where('auser.auditeeuserid', '=', $userid)
    //     ->whereColumn('auser.instid', '=', 'ap.instid')
    //      ->where('inm.statusflag', '=', 'Y')
    //     //->where('inm.auditteamhead', '=', 'N') // Exclude team head

    //     ->get();
    // }


    public static function  fetch_auditscheduledetails($userid)
    {
        return self::query()
            ->join('audit.inst_schteammember as inm', 'inm.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'inst_auditschedule.auditplanid')
            ->join('audit.auditplanteam as at', 'ap.auditteamid', '=', 'at.auditplanteamid')
            ->join('audit.mst_institution as ai', 'ai.instid', '=', 'ap.instid')
            ->join('audit.audtieeuserdetails as auser', 'auser.instid', '=', 'ap.instid')
            ->join('audit.mst_typeofaudit as mst', 'mst.typeofauditcode', '=', 'ap.typeofauditcode')
            ->join('audit.mst_dept as msd', 'msd.deptcode', '=', 'ai.deptcode')
            ->join('audit.mst_auditeeins_category as mac', 'mac.catcode', '=', 'ai.catcode')
            ->join('audit.mst_auditquarter as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'inm.userid')
            ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
            // ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
            // ->join('audit.mst_designation as nd', 'nd.desigcode', '=', 'ai.nodalperson_desigcode')
            ->join('audit.yearcode_mapping as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
            ->join(
                'audit.mst_auditperiod as period',
                DB::raw('CAST(yrmap.yearselected AS INTEGER)'),
                '=',
                'period.auditperiodid'
            )
            ->select(
                'inst_auditschedule.auditscheduleid',
                'inst_auditschedule.fromdate',
                'inst_auditschedule.todate',
                'inst_auditschedule.auditeeresponse',
                'inm.userid',
                'du.username',
                'ai.instename',
                'ai.nodalperson_ename',
                'ai.nodalperson_tname',
                'ai.email',
                'ai.mobile',
                'ai.nodalperson_desigcode',
                'ai.deptcode',
                'ai.catcode',
                'ai.instid',
                'ap.auditteamid',
                'ap.auditplanid',
                'at.teamname',
                'mst.typeofauditename',
                'mst.typeofaudittname',
                'msd.deptesname',
                'mac.catename',
                'maq.auditquarter',
                'ap.statusflag',
                // 'cd.chargedescription',
                'de.desigelname',
                'de.desigtlname',
                'ai.nodalperson_desigcode',
                // DB::raw('nd.desigelname as nodal_desigename'),
                'inm.auditteamhead',

                DB::raw('STRING_AGG(DISTINCT period.fromyear || \'-\' || period.toyear, \', \') as yearname'),

            )
            ->groupBy(
                'inst_auditschedule.auditscheduleid',
                'inst_auditschedule.fromdate',
                'inst_auditschedule.todate',
                'inst_auditschedule.auditeeresponse',
                'inm.userid',
                'du.username',
                'ai.instename',
                'ai.deptcode',
                'ai.catcode',
                'ai.instid',
                'ap.auditteamid',
                'ap.auditplanid',
                'at.teamname',
                'mst.typeofauditename',
                'mst.typeofaudittname',
                'msd.deptesname',
                'mac.catename',
                'maq.auditquarter',
                'ap.statusflag',
                // 'cd.chargedescription',
                'de.desigelname',
                'de.desigtlname',
                'ai.nodalperson_desigcode',
                // 'nd.desigelname',
                'inm.auditteamhead',
                'du.deptuserid'
            )
            // ->where('inst_auditschedule.auditscheduleid', function ($query) {
            //     $query->select('auditscheduleid')
            //         ->from('audit.inst_auditschedule')
            //         ->whereColumn('auditscheduleid', 'inst_auditschedule.auditscheduleid')
            //         ->where('statusflag', 'F');
            // })

            ->where('auser.auditeeuserid', '=', $userid)
            ->whereColumn('auser.instid', '=', 'ap.instid')
            ->where('inm.statusflag', '=', 'Y')
            //->where('inm.auditteamhead', '=', 'N') // Exclude team head
            ->orderBy('du.desigcode', 'asc')
            ->orderBy('du.deptuserid', 'asc')
            ->get();
            dd($get_majorobjection->toSql());
    }
    public static function update_partialchange($data, $auditescheduleid, $status)
    {
        if ($status == 'P') {
            return   self::query()
                ->where('auditscheduleid', $auditescheduleid)
                ->update([
                    'auditeeremarks' => $data['auditeeremarks'],
                    'auditeeresponsedt' => now(),
                    'auditeeresponse' => $data['auditeeresponse'],
                    'entrymeetdate' => $data['entrymeetdate'],
                    'auditeeproposeddate' => $data['auditeeproposeddate'],
                    'updatedon' => now(),
                ]);
        } else {
            return   self::query()
                ->where('auditscheduleid', $auditescheduleid)
                ->update([
                    'auditeeresponsedt' => now(),
                    'auditeeresponse' => 'A',
                    'entrymeetdate' => now(),
                    'updatedon' => now(),
                    'auditeeremarks' =>  $data['auditeeremarks'],
                    'nodalname'            => $data['nodalname'],
                    'nodalmobile'          => $data['nodalmobile'],
                    'nodalemail'           => $data['nodalemail'],
                    'nodaldesignation'     => $data['nodaldesignation'],

                ]);
        }
    }

    // public static function  fetch_auditplandetails($userid)
    // {

    //     return self::query()
    //         ->join('audit.auditplan as ap', 'inst_auditschedule.auditplanid', '=', 'ap.auditplanid')
    //         ->join('audit.mst_institution as mi', 'mi.instid', '=', 'ap.instid')
    //         ->join('audit.inst_schteammember as at', function ($join) {
    //             $join->on('at.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
    //                 ->where('at.auditteamhead', '=', 'Y');
    //         })
    //         ->join('audit.auditplanteam as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
    //         ->join('audit.userchargedetails as uc', 'uc.userchargeid', '=', 'at.userid')
    //         ->join('audit.deptuserdetails as du', 'at.userid', '=', 'du.deptuserid')
    //         ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')

    //         ->select(
    //             'inst_auditschedule.auditscheduleid',
    //             'inst_auditschedule.fromdate',
    //             'inst_auditschedule.todate',
    //             'inst_auditschedule.rcno',
    //             'inst_auditschedule.statusflag',
    //             'inst_auditschedule.auditeeresponse',
    //             'inst_auditschedule.auditeeresponsedt',
    //             'inst_auditschedule.auditeeproposeddate',
    //             'inst_auditschedule.auditeeremarks',
    //             'inst_auditschedule.nodalname',
    //             'inst_auditschedule.nodalmobile',
    //             'inst_auditschedule.nodalemail',
    //             'inst_auditschedule.nodaldesignation',
    //             'apt.teamname',
    //             'mi.instename',
    //             'mi.mandays',
    //             'at.auditscheduleid',
    //             'at.userid',
    //             'du.username',
    //             'cd.chargedescription',
    //             DB::raw('(
    //     SELECT COUNT(*)
    //     FROM audit.inst_schteammember as sub_atm
    //     WHERE sub_atm.auditscheduleid = inst_auditschedule.auditscheduleid
    //     AND (sub_atm.statusflag =  \'Y\' )
    //     AND sub_atm.auditteamhead = \'N\'
    // ) AS team_member_count')
    //         )
    //         ->where(function ($query) {

    //             $query->where('inst_auditschedule.statusflag', '=', 'F')
    //                 ->whereNotNull('inst_auditschedule.auditeeresponse') // Check auditeeresponse is not null
    //                 ->where('inst_auditschedule.auditeeresponse', '!=', '');
    //         })
    //         ->get();
    // }

    public static function  fetch_auditplandetails($userid)
    {

        return self::query()
            ->join('audit.auditplan as ap', 'inst_auditschedule.auditplanid', '=', 'ap.auditplanid')
            ->join('audit.mst_institution as mi', 'mi.instid', '=', 'ap.instid')
            ->join('audit.inst_schteammember as at', function ($join) {
                $join->on('at.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
                    ->where('at.auditteamhead', '=', 'Y');
            })
            ->join('audit.auditplanteam as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'at.userid')
            ->join('audit.deptuserdetails as du', 'at.userid', '=', 'du.deptuserid')
            // ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join('audit.yearcode_mapping as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
            ->join(
                'audit.mst_auditperiod as period',
                DB::raw('CAST(yrmap.yearselected AS INTEGER)'),
                '=',
                'period.auditperiodid'
            )
            ->select(
                'inst_auditschedule.auditscheduleid',
                'inst_auditschedule.fromdate',
                'inst_auditschedule.todate',
                'inst_auditschedule.rcno',
                'inst_auditschedule.statusflag',
                'inst_auditschedule.auditeeresponse',
                'inst_auditschedule.auditeeresponsedt',
                'inst_auditschedule.auditeeproposeddate',
                'inst_auditschedule.auditeeremarks',
                'inst_auditschedule.nodalname',
                'inst_auditschedule.nodalmobile',
                'inst_auditschedule.nodalemail',
                'inst_auditschedule.nodaldesignation',
                'inst_auditschedule.workallocationflag',
                'apt.teamname',
                'mi.instename',
                'mi.mandays',
                'at.auditscheduleid',
                'at.userid',
                'du.username',
                // 'cd.chargedescription',
                DB::raw('STRING_AGG(DISTINCT period.fromyear || \'-\' || period.toyear, \', \') as yearname'),
                DB::raw('(
    SELECT COUNT(*)
    FROM audit.inst_schteammember as sub_atm
    WHERE sub_atm.auditscheduleid = inst_auditschedule.auditscheduleid
    AND (sub_atm.statusflag =  \'Y\' )
    AND sub_atm.auditteamhead = \'N\'
) AS team_member_count')
            )
            ->groupBy(
                'inst_auditschedule.auditscheduleid',
                'inst_auditschedule.fromdate',
                'inst_auditschedule.todate',
                'inst_auditschedule.rcno',
                'inst_auditschedule.statusflag',
                'inst_auditschedule.auditeeresponse',
                'inst_auditschedule.auditeeresponsedt',
                'inst_auditschedule.auditeeproposeddate',
                'inst_auditschedule.auditeeremarks',
                'inst_auditschedule.nodalname',
                'inst_auditschedule.nodalmobile',
                'inst_auditschedule.nodalemail',
                'inst_auditschedule.nodaldesignation',
                'inst_auditschedule.workallocationflag',
                'apt.teamname',
                'mi.instename',
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
            ->whereNotNull('inst_auditschedule.auditeeresponse')
            ->where('inst_auditschedule.auditeeresponse', '!=', '')
            ->get();
    }

    public static function fetch_Accountaccepteddetails($auditscheduleid)
    {
        return self::query()


            ->join('audit.trans_accountdetails as ad', 'ad.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
            ->join('audit.mst_accountparticulars as map', 'map.accountparticularsid', '=', 'ad.accountcode')
            ->join('audit.auditplan as ap', 'inst_auditschedule.auditplanid', '=', 'ap.auditplanid')
            ->join('audit.auditplanteam as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->join('audit.mst_institution as mi', 'mi.instid', '=', 'ap.instid')
            ->leftjoin('audit.fileuploaddetail as fu', 'fu.fileuploadid', '=', 'ad.fileuploadid')

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
                'inst_auditschedule.auditscheduleid',
                'inst_auditschedule.nodalname',
                'inst_auditschedule.nodalmobile',
                'inst_auditschedule.nodaldesignation',
                'inst_auditschedule.nodalemail',
                'inst_auditschedule.auditeeremarks',
                'ad.accountcode',
                'ad.fileuploadid',
                'ad.remarks',
                'map.accountparticularsename',
                'map.accountparticularstname',
                'map.accountparticularsid'

            )
            ->groupBy(
                'inst_auditschedule.auditscheduleid',
                'inst_auditschedule.nodalname',
                'inst_auditschedule.nodalmobile',
                'inst_auditschedule.nodaldesignation',
                'inst_auditschedule.nodalemail',
                'inst_auditschedule.auditeeremarks',
                'ad.accountcode',
                'ad.fileuploadid',
                'ad.remarks',
                'map.accountparticularsename',
                'map.accountparticularstname',
                'map.accountparticularsid'
            )
            ->where('inst_auditschedule.statusflag', '=', 'F')
            ->where('inst_auditschedule.auditscheduleid', '=', $auditscheduleid)
            ->whereNotNull('inst_auditschedule.auditeeresponse')
            ->orderBy('map.accountparticularsename', 'desc')
            ->get();
    }

    public static function fetch_cfraccepteddetails($auditscheduleid)
    {
        return self::query()

            ->join('audit.trans_callforrecords as cfr', 'cfr.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
            //->join('audit.mst_subworkallocationtype as msw', 'msw.subworkallocationtypeid', '=', 'cfr.subtypecode')
            // ->join('audit.mst_majorworkallocationtype as mmw', 'mmw.majorworkallocationtypeid', '=', 'msw.majorworkallocationtypeid')
            ->join('audit.callforrecords_auditee as cfra', 'cfra.callforrecordsid', '=', 'cfr.subtypecode')

            ->join('audit.auditplan as ap', 'inst_auditschedule.auditplanid', '=', 'ap.auditplanid')
            ->join('audit.auditplanteam as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->join('audit.mst_institution as mi', 'mi.instid', '=', 'ap.instid')

            ->select(
                'inst_auditschedule.auditscheduleid',
                'inst_auditschedule.nodalname',
                'inst_auditschedule.nodalmobile',
                'inst_auditschedule.nodaldesignation',
                'inst_auditschedule.nodalemail',
                'inst_auditschedule.auditeeremarks',
                'cfr.subtypecode',
                'cfr.remarks as cfr_remarks',
                'cfr.replystatus',
                'cfra.callforrecordsename',
                'cfra.callforrecordstname',
                'cfra.callforrecordsid',


            )
            ->where('inst_auditschedule.statusflag', '=', 'F')
            ->where('cfr.auditscheduleid', '=', $auditscheduleid)
            ->whereNotNull('inst_auditschedule.auditeeresponse')
            ->orderBy('cfra.callforrecordsename', 'desc')
            ->get();
    }

    public static function fetchAuditScheduleDetailsDeptUsers($deptuserid)
    {
        return self::query()
            // Join statements
            ->join('audit.inst_schteammember as inm', 'inm.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'inst_auditschedule.auditplanid')
            ->join('audit.auditplanteam as at', 'ap.auditteamid', '=', 'at.auditplanteamid')
            ->join('audit.mst_institution as ai', 'ai.instid', '=', 'ap.instid')
            ->join('audit.mst_typeofaudit as mst', 'mst.typeofauditcode', '=', 'ap.typeofauditcode')
            ->join('audit.mst_dept as msd', 'msd.deptcode', '=', 'ai.deptcode')
            ->join('audit.mst_auditeeins_category as mac', 'mac.catcode', '=', 'ai.catcode')
            ->join('audit.mst_auditquarter as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'inm.userid')
            ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
            ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
            ->join('audit.yearcode_mapping as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
            ->join('audit.mst_auditperiod as period', DB::raw('CAST(yrmap.yearselected AS INTEGER)'), '=', 'period.auditperiodid')

            // Where conditions
            ->where('du.deptuserid', '=', $deptuserid) // Filter by deptuserid
            ->where('inm.statusflag', '=', 'Y') // Filter by statusflag = 'Y'

            // Select columns
            ->select(
                'inst_auditschedule.auditscheduleid',
                'inst_auditschedule.fromdate',
                'inst_auditschedule.todate',
                'inst_auditschedule.auditeeresponse',
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
                'cd.chargedescription',
                'de.desigelname',
                'inm.auditteamhead',
                DB::raw('STRING_AGG(period.fromyear || \'-\' || period.toyear, \', \') as yearname')
            )

            // Group by clause
            ->groupBy(
                'inst_auditschedule.auditscheduleid',
                'inst_auditschedule.fromdate',
                'inst_auditschedule.todate',
                'inst_auditschedule.auditeeresponse',
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
                'cd.chargedescription',
                'de.desigelname',
                'inm.auditteamhead'
            )
            ->get(); // Execute the query
    }


    public static function GetSchedultedEventDetails($scheduleid)
    {
        return self::query()
        // Join statements
        ->join('audit.inst_schteammember as inm', 'inm.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
        ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'inst_auditschedule.auditplanid')
        ->join('audit.auditplanteam as at', 'ap.auditteamid', '=', 'at.auditplanteamid')
        ->join('audit.mst_institution as ai', 'ai.instid', '=', 'ap.instid')
        ->join('audit.mst_typeofaudit as mst', 'mst.typeofauditcode', '=', 'ap.typeofauditcode')
        ->join('audit.mst_dept as msd', 'msd.deptcode', '=', 'ai.deptcode')
        ->join('audit.mst_district as msi', 'msi.distcode', '=', 'ai.distcode')
        ->join('audit.mst_auditeeins_category as mac', 'mac.catcode', '=', 'ai.catcode')
        ->join('audit.mst_auditquarter as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
        ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'inm.userid')
        ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
        ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
        ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
        ->join('audit.yearcode_mapping as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
        ->join('audit.mst_auditperiod as period', DB::raw('CAST(yrmap.yearselected AS INTEGER)'), '=', 'period.auditperiodid')

        // Where conditions
        ->where('inst_auditschedule.auditscheduleid', '=', $scheduleid) // Filter by deptuserid
        ->where('inm.statusflag', '=', 'Y') // Filter by statusflag = 'Y'

        // Select columns
        ->select(
            'inst_auditschedule.auditscheduleid',
            'inst_auditschedule.fromdate',
            'inst_auditschedule.todate',
            'inst_auditschedule.auditeeresponse',
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
            'inst_auditschedule.auditscheduleid',
            'inst_auditschedule.fromdate',
            'inst_auditschedule.todate',
            'inst_auditschedule.auditeeresponse',
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


    // public static function Automateworkallocation($scheduleid)
    // {
    //     call the postgres functionv Work allocation automation check with exception and error
    // }

    public static function Automateworkallocation($scheduleid)
    {
        try {
            Log::info('Calling PostgreSQL function: audit.workallocationAutomation with auditscheduleid: ' . $scheduleid);

            // Call the PostgreSQL function
            $result = DB::select('SELECT audit.workallocationAutomation(:scheduleid) AS response', [
                'scheduleid' => $scheduleid
            ]);

            // Extract the JSON response from the database result
            $response = $result[0]->response ?? null;

            Log::info('PostgreSQL function response: ' . json_encode($response));

            return $response;
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database Query Error: ' . $e->getMessage());
            throw new Exception('Database error occurred while automating work allocation.');
        } catch (\Exception $e) {
            Log::error('General Error: ' . $e->getMessage());
            throw new Exception('Unexpected error occurred while automating work allocation.');
        }
    }

    public static function update_exitmeetdate($data, $auditscheduleid)
    {

        try {
       
            $result = self::where('auditscheduleid', $auditscheduleid)
                ->update($data);
            if ($result) {
                // Fetch and return the updated record
                $updatedRecord = self::where('auditscheduleid', $auditscheduleid)->first();
                return $updatedRecord;
            } else {
                // Return null if no rows were updated
                return null;
            } // $data should be an associative array of the columns to update
        } catch (\Exception $e) {
            // Handle exceptions (e.g., log errors or throw a custom exception)
            \Log::error('Failed to update exit meet date: ' . $e->getMessage());
            return false;
        }
    }

public static function update_entrymeetdate($data, $auditscheduleid, $spilloverflag, $auditmode, $is_epaccs)  {

  try {
 if ($spilloverflag == 'Y' || $auditmode == 'P' || $is_epaccs) {
  $result = self::where('auditscheduleid', $auditscheduleid)
  ->update($data);
  }
  $updatedRecord = self::where('auditscheduleid', $auditscheduleid)->first();
  return $updatedRecord;
  } catch (\Exception $e) {
  // Handle exceptions (e.g., log errors or throw a custom exception)
  \Log::error('Failed to update entry meet date: ' . $e->getMessage());
  return false;
  }
  }

}
