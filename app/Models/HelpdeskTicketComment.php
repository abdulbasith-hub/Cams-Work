<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpdeskTicketComment extends Model
{
    use HasFactory;

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
    ];

    public function ticket()
    {
        return $this->belongsTo(HelpdeskTicket::class, 'ticket_id');
    }
}
