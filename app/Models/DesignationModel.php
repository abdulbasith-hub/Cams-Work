<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// class UserModel extends Model
// {
//     use HasFactory;
// }

class DesignationModel extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon'; // Custom column name for `updated_at` (if you have it)

    // // Specify that the primary key is `userid` instead of `id`
    // protected $primaryKey = 'deptcode';

    // Specify the table name
    protected $table = 'audit.mst_designation';

    // // Set the primary key type if it's not an auto-incrementing integer
    // protected $keyType = 'string';  // If `userid` is an integer

    // // If your primary key is not auto-incrementing, set `incrementing` to false
    // public $incrementing = true; // Set to `false` if `userid` is not auto-incrementing



}
