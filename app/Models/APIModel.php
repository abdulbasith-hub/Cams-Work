<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;

class APIModel extends Model
{
    public static function getentrymeetingdel($auditscheduleid)
    {
        return DB::select('SELECT * FROM audit.api_entrymeetingdel(?)', [$auditscheduleid]);
    }

    public static function exitmeetingdel($auditscheduleid)
    {
        return DB::select('SELECT * FROM audit.api_exitmeetingdel(?)', [$auditscheduleid]);
    }
public static function auditscheduledel($apicode)
{
return DB::select('SELECT * FROM audit.api_auditscheduledel(?)', [$apicode]);
}


    public static function getsecretkey($apicode)
    {
        return DB::table('audit.mst_apiclient')
            ->where('apicode', $apicode)
            ->value('secretkey');
    }
public static function api_updateuserdel($leaveid, $othertransid)
    {
        return DB::select('SELECT * FROM audit.api_updateuserdel(?,?)', [$leaveid, $othertransid]);
    }
public static function apilog($data)
    {
        try {

            $inserted = DB::table('audit.apilog')->insert($data);

            return [
                'status' => $inserted,
                'message' => $inserted ? 'Log inserted successfully' : 'Insert failed'
            ];
        } catch (\Throwable $e) {

            \Log::error('APILOG INSERT FAILED', [
                'error' => $e->getMessage(),
                'data'  => $data
            ]);

            return [
                'status' => false,
                'message' => 'Exception while inserting log',
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ];
        }
    }

    public static function updateapisent($leaveid, $othertransid)
    {
        try {

            $query = DB::table('audit.logothertrans_scheduledel');

            if (!empty($leaveid)) {
                $query->where('leaveid', $leaveid);
            }

            if (!empty($othertransid)) {
                $query->where('othertransid', $othertransid);
            }

            return $query->update([
                'apisent' => 'Y'
            ]);
        } catch (\Throwable $e) {

            \Log::error('updateapisent failed', [
                'error' => $e->getMessage(),
                'leaveid' => $leaveid,
                'othertransid' => $othertransid
            ]);

            return false;
        }
    }
}
