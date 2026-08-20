<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Support\HelpdeskSession;

class HelpdeskTicket extends Model
{
    use HasFactory;

    public const REQUEST_TYPE_OPTIONS = [
        'support' => 'Support',
        'new_feature' => 'New Feature',
    ];

    public const CATEGORY_OPTIONS = [
        'Login',
        'Schedule',
        'Field audit',
        'Auditee Login',
        'Slip',
        'APMS',
        'Legacy',
        'Field audit - Attachment',
        'APMS - attachment',
        'Audit report',
        'Inspection',
        'Others',
    ];

    protected $table = 'audit.helpdesk_tickets';

    protected $fillable = [
        'ticket_number',
        'cams_userid',
        'user_name',
        'user_email',
        'deptcode',
        'department_name',
        'financialyearcode',
        'planmappingid',
        'institution',
        'subject',
        'description',
        'request_type',
        'category',
        'priority',
        'status',
        'assigned_to_userid',
        'assigned_to_name',
        'forwarded_to_chargeid',
        'forwarded_to_role',
        'forwarded_at',
        'forward_notes',
        'attachments',
        'resolved_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'forwarded_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $ticket): void {
            if (!$ticket->ticket_number) {
                $ticket->ticket_number = self::generateTicketNumber($ticket);
            }
        });
    }

    private static function generateTicketNumber(self $ticket): string
    {
        $year = now()->format('Y');
        $departmentLetter = self::resolveDepartmentLetter($ticket);
        $quarterCode = self::resolveQuarterCode($ticket->planmappingid);
        $prefix = sprintf('TKT%s%s%s', $year, $departmentLetter, $quarterCode);

        $lastTicketNumber = self::query()
            ->where('ticket_number', 'like', $prefix.'%')
            ->orderByDesc('ticket_number')
            ->value('ticket_number');

        $nextSequence = 1;
        if ($lastTicketNumber) {
            $nextSequence = ((int) substr($lastTicketNumber, -5)) + 1;
        }

        return $prefix.str_pad((string) $nextSequence, 5, '0', STR_PAD_LEFT);
    }

    private static function resolveDepartmentLetter(self $ticket): string
    {
        $departmentName = $ticket->department_name;

        if (!$departmentName && $ticket->deptcode) {
            $departmentName = DB::table('audit.mst_dept')
                ->where('deptcode', $ticket->deptcode)
                ->value('deptesname');
        }

        if (!$departmentName) {
            return 'X';
        }

        $departmentName = Str::upper((string) $departmentName);
        preg_match('/[A-Z]/', $departmentName, $matches);

        return $matches[0] ?? 'X';
    }

    private static function resolveQuarterCode($planMappingId): string
    {
        if (!$planMappingId) {
            return 'X';
        }

        $quarter = DB::table('audit.auditplanmapping')
            ->where('planmappingid', $planMappingId)
            ->value('auditquartercode');

        return match (Str::upper((string) $quarter)) {
            'Q1' => 'A',
            'Q2' => 'B',
            'Q3' => 'C',
            'Q4' => 'D',
            default => 'X',
        };
    }

    public function comments()
    {
        return $this->hasMany(HelpdeskTicketComment::class, 'ticket_id')->latest();
    }

    public function devComments()
    {
        return $this->hasMany(HelpdeskTicketDevComment::class, 'ticket_id')->latest();
    }

    public function assignments()
    {
        return $this->hasMany(HelpdeskTicketAssignment::class, 'ticket_id')->latest('assigned_at');
    }

    public function normalizedForwardedRole(): ?string
    {
        $role = $this->forwarded_to_role;

        if ($role === 'superadmin') {
            return 'stateadmin';
        }

        if ($role === 'developer' && empty($this->assigned_to_userid)) {
            return 'nicadmin';
        }

        return $role;
    }

    public function isDeveloperStage(): bool
    {
        return $this->forwarded_to_role === 'developer' && !empty($this->assigned_to_userid);
    }

    public function currentHolderLabel(): string
    {
        if (in_array($this->status, ['resolved', 'closed'], true)) {
            return HelpdeskSession::normalizeUserName($this->user_name) ?: 'Ticket Owner';
        }

        return match ($this->normalizedForwardedRole()) {
            'stateadmin' => 'StateAdmin',
            'nicadmin' => 'NIC Admin',
            'developer' => 'NIC Admin',
            'department_admin' => 'Department Admin',
            null, '' => HelpdeskSession::normalizeUserName($this->user_name) ?: 'Ticket Owner',
            default => Str::headline(str_replace('_', ' ', (string) $this->forwarded_to_role)),
        };
    }

    public function assignedDeveloperLabel(): string
    {
        return $this->isDeveloperStage()
            ? ($this->assigned_to_name ?: '-')
            : '-';
    }

    public function completedByLabel(): string
    {
        $completedAssignment = $this->assignments->first(function ($assignment) {
            return in_array($assignment->status, ['returned', 'reassigned', 'reopened'], true);
        });

        return $completedAssignment?->developer_name ?: '-';
    }

    public function isReturnedDeveloperReassignment(): bool
    {
        if (!$this->isDeveloperStage()) {
            return false;
        }

        return $this->assignments->contains(function ($assignment) {
            return in_array($assignment->status, ['returned', 'reassigned'], true);
        });
    }

    public function reopenedOn()
    {
        if (!$this->relationLoaded('comments')) {
            return $this->comments()
                ->whereRaw('LOWER(comment) LIKE ?', ['%reopened%'])
                ->latest()
                ->first()?->created_at;
        }

        $reopenedComment = $this->comments->first(function ($comment) {
            return Str::contains(Str::lower((string) $comment->comment), 'reopened');
        });

        return $reopenedComment?->created_at;
    }

    public function requestTypeLabel(): string
    {
        return Str::headline(str_replace('_', ' ', $this->request_type ?? '-'));
    }

}
