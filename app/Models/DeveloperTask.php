<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperTask extends Model
{
    use HasFactory;

    protected $table = 'audit.developer_tasks';

    protected $fillable = [
        'assigned_by_userid',
        'assigned_by_name',
        'developer_userid',
        'developer_name',
        'process_assigned',
        'task_type',
        'module_category_id',
        'senior_userid',
        'senior_name',
        'is_testing_task',
        'testing_task_description',
        'assigned_on',
        'expected_date_to_complete',
        'started_on',
        'completed_on',
        'remarks_by_developer',
        'task_status_by_tester',
        'remarks_by_project_head',
        'verifier_feedback',
        'verified_by',
        'verified_on',
        'remarks_by_verifier',
        'approved_by',
        'approved_on',
        'hosted_in_staging',
        'deployed_in_live_server',
    ];

    protected $casts = [
        'assigned_on' => 'datetime',
        'expected_date_to_complete' => 'datetime',
        'started_on' => 'datetime',
        'completed_on' => 'datetime',
        'verified_on' => 'datetime',
        'approved_on' => 'datetime',
        'is_testing_task' => 'boolean',
        'hosted_in_staging' => 'boolean',
        'deployed_in_live_server' => 'boolean',
    ];
}
