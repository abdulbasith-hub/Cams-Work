<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
// class UserModel extends Model
// {
//     use HasFactory;
// }

class AuditTeamModel extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL


    protected $table = 'audit.auditplanteam';
    // Specify that the primary key is `userid` instead of `id`
    protected $primaryKey = 'auditplanteamid';

    // Specify the table name


    // Set the primary key type if it's not an auto-incrementing integer
    // protected $keyType = 'string';  // If `userid` is an integer

    // If your primary key is not auto-incrementing, set `incrementing` to false
    public $incrementing = true;
    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon';
    // Define the fillable fields
    protected $fillable = [
        'distcode',
        'configcode',
        'deptcode',
        'teamname',
        'statusflag',
        'createdon',
        'updatedon',
        'auditordiststatus'
    ];
    public static function createIfNotExistsOrUpdate(array $data, $auditteamid = null)
    {
        // return self::create($data);

        try {
            // If no conflicts, proceed with create or update
            if ($auditteamid)
            {

                $existingUser = self::where('auditplanteamid', $auditteamid)->first();


                // Check if the user exists before attempting to update
                if ($existingUser) {

                    // If the user exists, update with the provided data
                    $existingUser->update($data);


                    return $existingUser->auditplanteamid;
                } else {
                    // If the user doesn't exist, handle the error appropriately
                    return response()->json(['error' => 'User not found'], 404);
                }
            } else {
               // print_r($data);
               $team = self::create($data);
               return $team->auditplanteamid;  // Return the `teamid` of the newly created team
            }
        } catch (\Exception $e) {
            // Throwing a custom exception with the message from the model
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Create a new user if it doesn't already exist based on email, phone, name, and address.
     * Otherwise, update the user if it already exists, based on email, phone, and name (excluding current id).
     *
     * @param array $data
     * @param int|null $currentUserId (optional: pass the current user's id for updates)
     * @return User|false
     */
    // public static function createIfNotExistsOrUpdate(array $data, $currentUserId = null)
    // {
    //     try {
    //         // If currentUserId is provided, we are doing an update operation
    //         if ($currentUserId)
    //         {
    //             $emailExists = self::where('email', $data['email'])
    //                                 ->where('deptuserid', '!=', $currentUserId)->exists();
    //             $mobileExists = self::where('mobilenumber', $data['mobilenumber'])
    //                                 ->where('deptuserid', '!=', $currentUserId)->exists();
    //             $ifhrmsnoExists = self::where('ifhrmsno', $data['ifhrmsno'])
    //                                 ->where('deptuserid', '!=', $currentUserId)->exists();

    //             // Check if the same fields exist, but with a different user ID
    //             $existingUser = self::where('email', $data['email'])
    //                                 ->where('mobilenumber', $data['mobilenumber'])
    //                                 ->where('ifhrmsno', $data['ifhrmsno'])
    //                                 ->where('deptuserid', '!=', $currentUserId) // Ensure it's a different user
    //                                 ->first();
    //         }
    //         else
    //         {
    //             $emailExists = self::where('email', $data['email'])->exists();
    //             $mobileExists = self::where('mobilenumber', $data['mobilenumber'])->exists();
    //             $ifhrmsnoExists = self::where('ifhrmsno', $data['ifhrmsno'])->exists();

    //             // 2. Check if all three fields match together
    //             $existingUser = self::where('email', $data['email'])
    //                 ->where('mobilenumber', $data['mobilenumber'])
    //                 ->where('ifhrmsno', $data['ifhrmsno'])
    //                 ->first();
    //         }


    //         if ($emailExists) {
    //             throw new Exception('The email address is already associated with a different user.');
    //         }

    //         if ($mobileExists) {
    //             throw new Exception('The mobile number is already associated with a different user.');
    //         }

    //         if ($ifhrmsnoExists) {
    //             throw new Exception('The IFHRMS number is already associated with a different user.');
    //         }

    //         if ($existingUser) {
    //             throw new Exception('The combination of email, mobile number, and IFHRMS number is already associated with a different user.');
    //         }


    //         if ($currentUserId)
    //         {
    //             $existingUser = self::find($currentUserId);
    //             $existingUser->update($data);
    //             return $existingUser;
    //         }
    //         else
    //         {
    //             // If no currentUserId (new user), just create a new record
    //             return self::create($data);
    //         }
    //     } catch (QueryException $e) {
    //         // Handle any database-specific exceptions (e.g., duplicate entry)
    //         Log::error("Database error: " . $e->getMessage());
    //         throw new Exception("Database error occurred. Please try again later.");
    //     } catch (Exception $e) {
    //         // Handle any other general exceptions
    //         Log::error("General error: " . $e->getMessage());
    //         throw new Exception("Something went wrong: " . $e->getMessage());
    //     }
    // }





    public static function fetchUserById($userId)
    {
        return self::find($userId);
    }

    public static function fetch_teamdetail()
    {
        return self::query()
            ->select(
                'auditplanteam.auditplanteamid',
                'auditplanteam.teamname',
                // Subquery for total_team_count
                DB::raw('(SELECT COUNT( auditplanteamid) FROM audit.auditplanteam WHERE statusflag = \'F\') AS total_team_count'),
                // Subquery for team_member_count
                DB::raw('(SELECT COUNT(DISTINCT at.planteammemberid) FROM audit.auditplanteammember as at WHERE at.statusflag = \'Y\' AND at.auditplanteamid = auditplanteam.auditplanteamid) AS team_member_count')
            )
            ->where('auditplanteam.statusflag', 'F')
            ->get();
    }

}
