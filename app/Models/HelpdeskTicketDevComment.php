<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpdeskTicketDevComment extends Model
{
    use HasFactory;

    protected $table = 'audit.helpdesk_ticket_comments_dev';

    protected $fillable = [
        'ticket_id',
        'cams_userid',
        'user_name',
        'user_role',
        'comment',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(HelpdeskTicket::class, 'ticket_id');
    }
}
