<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;



class LeaveManagementModel extends Model
{
    protected $table;

    public function __construct($table)
    {
        $this->table = $table;
    }
}
