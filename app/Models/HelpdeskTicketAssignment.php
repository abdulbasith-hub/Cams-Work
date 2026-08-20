<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpdeskTicketAssignment extends Model
{
    use HasFactory;

    protected $table = 'audit.helpdesk_ticket_assignments';

    protected $fillable = [
        'ticket_id',
        'assigned_by_userid',
        'assigned_by_name',
        'developer_userid',
        'developer_name',
        'notes',
        'status',
        'assigned_at',
        'released_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(HelpdeskTicket::class, 'ticket_id');
    }
}
