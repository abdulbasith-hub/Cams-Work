<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// class UserModel extends Model
// {
//     use HasFactory;
// }

class InstSchteamMemberModel  extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL


    protected $table = 'audit.inst_schteammember';
    public $incrementing = false; // No auto-increment
    protected $primaryKey = null; // No primary key
    public $timestamps = true;
    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon';
    // Define the fillable fields
    protected $fillable = [

        'auditscheduleid',
        'auditfromdate',
        'audittodate',
        'userid',
        'auditteamhead',
        'statusflag',
        'createdon',
        'updatedon',
        'teamcode'
    ];


    /**
     * Create a new user if it doesn't already exist based on email, phone, name, and address.
     * Otherwise, update the user if it already exists, based on email, phone, and name (excluding current id).
     *
     * @param array $data
     * @param int|null $currentUserId (optional: pass the current user's id for updates)
     * @return User|false
     */



    public static function createIfNotExistsOrUpdate(array $data, $currentUserId = null)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            // Throwing a custom exception with the message from the model
            throw new \Exception($e->getMessage());
        }
    }


    public static function fetchUserById($userId)
    {
        return self::find($userId);
    }
    public static function fetchteamMembers($auditscheduleid)
    {
        $teammember = self::where('inst_schteammember.auditscheduleid', '=', $auditscheduleid)
            ->get(); // Use get() to return all matching records

        // Return the team members
        return $teammember;
    }

    public static function update_teamstatus($statusflag, $auditscheduleid)
    {
        return   self::query()
            ->where('auditscheduleid', $auditscheduleid)
            ->whereNot('statusflag', 'N')
            ->update([
                'statusflag' => $statusflag,
                'updatedon' => now(),
            ]);
    }
}
