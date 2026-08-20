<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpdeskV2Comment extends Model
{
    protected $table = 'audit.helpdesk_ticket_comments';

    protected $fillable = [
        'ticket_id',
        'cams_userid',
        'user_name',
        'user_role',
        'comment',
        'is_internal',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(HelpdeskV2Ticket::class, 'ticket_id');
    }

    public function getVisibilityAttribute(): string
    {
        return $this->is_internal ? 'internal' : 'public';
    }

    public function getCreatedByUseridAttribute(): ?string
    {
        return $this->cams_userid;
    }

    public function getCreatedByNameAttribute(): ?string
    {
        return $this->user_name;
    }

    public function getCreatedByRoleAttribute(): ?string
    {
        return $this->user_role;
    }
}
