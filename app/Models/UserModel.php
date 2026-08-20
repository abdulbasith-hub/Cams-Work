<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// class UserModel extends Model
// {
//     use HasFactory;
// }

class UserModel extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon'; // Custom column name for `updated_at` (if you have it)

    // Specify that the primary key is `userid` instead of `id`
    protected $primaryKey = 'deptuserid';

    // Specify the table name
    protected $table = 'audit.deptuserdetails';

    // Set the primary key type if it's not an auto-incrementing integer
    protected $keyType = 'int';  // If `userid` is an integer

    // If your primary key is not auto-incrementing, set `incrementing` to false
    public $incrementing = true; // Set to `false` if `userid` is not auto-incrementing

    // Define the fillable fields
    protected $fillable = [
        'email', 'mobilenumber', 'ifhrmsno', 'createdon', 'updatedon','deptcode','username','gendercode','dob',
        'desigcode','doj','dor','auditorflag','statusflag',
    ];


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
}
