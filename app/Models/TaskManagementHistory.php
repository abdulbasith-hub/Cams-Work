<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskManagementHistory extends Model
{
    use HasFactory;

    protected $table = 'audit.task_management_histories';

    protected $fillable = [
        'task_id',
        'action_key',
        'stage',
        'status',
        'comment',
        'performed_by_userid',
        'performed_by_name',
        'performed_by_role',
        'performed_at',
        'metadata',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(TaskManagementTask::class, 'task_id');
    }
}
