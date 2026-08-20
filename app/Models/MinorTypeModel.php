<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class MinorTypeModel extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon'; // Custom column name for `updated_at` (if you have it)

    // Specify that the primary key is `userid` instead of `id`
    protected $primaryKey = 'diaryid';

    // Specify the table name
    protected $table = 'audit.mst_subworkallocationtype';

    // Set the primary key type if it's not an auto-incrementing integer
    protected $keyType = 'int';  // If `userid` is an integer


}

