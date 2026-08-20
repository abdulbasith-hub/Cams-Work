<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskManagementTask extends Model
{
    use HasFactory;

    protected $table = 'audit.task_management_details';

    protected $fillable = [
        'legacy_developer_task_id',
        'assigned_by_userid',
        'assigned_by_name',
        'developer_userid',
        'developer_name',
        'module_category_id',
        'process_assigned',
        'task_type',
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
        'statusflag',
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

    public function histories(): HasMany
    {
        return $this->hasMany(TaskManagementHistory::class, 'task_id');
    }

    public function statusKey(): string
    {
        if ($this->verified_on) {
            return 'completed';
        }

        if ($this->completed_on) {
            return 'completed';
        }

        if ($this->expected_date_to_complete && $this->expected_date_to_complete->isPast()) {
            return 'overdue';
        }

        if ($this->started_on) {
            return 'in_progress';
        }

        return 'pending';
    }

    public function statusLabel(): string
    {
        if ($this->verified_on) {
            return 'Sent to NIC Admin';
        }

        if ($this->completed_on) {
            return 'Testing Stage';
        }

        return match ($this->statusKey()) {
            'in_progress' => 'In Progress',
            'overdue' => 'Overdue',
            default => 'Pending',
        };
    }
}
