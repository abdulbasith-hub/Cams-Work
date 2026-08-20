<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\View;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class CommonModel extends Model
{


    public static function getplandetails($deptcode)
    {
        try {

            $query = DB::select(
                "SELECT * FROM audit.fn_getplandetails(:deptcode)",
                [
                    'deptcode' => $deptcode
                ]
            );

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while fetching details';
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }


    public static function getplandetailsWithPrev($deptcode)
    {
        try {

            $query = DB::select(
                "SELECT * FROM audit.fn_getplandetailsWithPrev(:deptcode)",
                [
                    'deptcode' => $deptcode
                ]
            );

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while fetching details';
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }




     public static function getplandetailsforreport($deptcode)
    {
        try {

            $query = DB::select(
                "SELECT * FROM audit.fn_getplandetailsforreport(:deptcode)",
                [
                    'deptcode' => $deptcode
                ]
            );
            // dd($query);
            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A error occurred while fetching details';
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

}
