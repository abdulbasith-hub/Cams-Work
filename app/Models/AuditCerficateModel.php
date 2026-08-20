<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class AuditCertificateModel extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon'; // Custom column name for `updated_at` (if you have it)

    // Specify that the primary key is `userid` instead of `id`
    protected $primaryKey = 'auditcertificateid';

    // Specify the table name
    protected $table = 'audit.audit_certificate';

    // Set the primary key type if it's not an auto-incrementing integer
    protected $keyType = 'int';  // If `userid` is an integer

    // If your primary key is not auto-incrementing, set `incrementing` to false
    public $incrementing = true; // Set to `false` if `userid` is not auto-incrementing

    // Define the fillable fields
    protected $fillable = [
        'membership_sharedcapital',
        'deposits_borrowings',
        'reserves_surplus',
        'other_liability',
        'investments',
        'loans_advances',
        'trading_result',
        'net_result',
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
    public static function createIfNotExistsOrUpdate(array $data,$cerid)
    {
        if($cerid)
        {
            $existingUser = self::query()
                                ->where('statusflag', 'Y')
                                ->where('workallocationtypeid', '!=', $workallocid)
                                ->where('majorworkallocationtypeid', $data['majorworkallocationtypeid'])
                                ->where('minorworkallocationtypeid', $data['minorworkallocationtypeid'])
                                ->first();

            if($existingUser)
            {
               return false;
            }
        
            // If no such user exists, update the existing record with the provided data
            $existingUser = self::find($workallocid);
            $existingUser->update($data);

        }else
        {

            /*$existingUser = self::query()
                                ->where('statusflag', 'Y')
                                ->first();

            if($existingUser)
            {
               return false;
            }else
            {*/
                $existingUser=self::create($data);
                print_r($existingUser);

           /* }*/

        }

        return $existingUser;
       
    }



}
