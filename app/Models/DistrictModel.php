<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// class UserModel extends Model
// {
//     use HasFactory;
// }

class DistrictModel extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon'; // Custom column name for `updated_at` (if you have it)

    // Specify that the primary key is `userid` instead of `id`
    // protected $primaryKey = 'deptcode';

    // Specify the table name
    protected $table = 'audit.mst_district';

    // Set the primary key type if it's not an auto-incrementing integer
    // protected $keyType = 'string';  // If `userid` is an integer

    // If your primary key is not auto-incrementing, set `incrementing` to false
    // public $incrementing = true; // Set to `false` if `userid` is not auto-incrementing

    // Define the fillable fields
    // protected $fillable = [
    //     'email', 'mobilenumber', 'ifhrmsno', 'createdon', 'updatedon'
    // ];


    /**
     * Create a new user if it doesn't already exist based on email, phone, name, and address.
     * Otherwise, update the user if it already exists, based on email, phone, and name (excluding current id).
     *
     * @param array $data
     * @param int|null $currentUserId (optional: pass the current user's id for updates)
     * @return User|false
     */
}
