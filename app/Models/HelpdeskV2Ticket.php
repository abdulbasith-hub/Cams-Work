<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HelpdeskV2Ticket extends Model
{
    use HasFactory;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_PENDING_STATE_ADMIN = 'pending_state_admin';
    public const STATUS_FORWARDED_NIC_ADMIN = 'forwarded_nic_admin';
    public const STATUS_ASSIGNED_LAYER_LEAD = 'assigned_layer_lead';
    public const STATUS_ASSIGNED_DEVELOPER = 'assigned_developer';
    public const STATUS_DEVELOPER_IN_PROGRESS = 'dev_in_progress';
    public const STATUS_RETURNED_BY_DEVELOPER = 'returned_by_dev';
    public const STATUS_ASSIGNED_TESTER = 'assigned_tester';
    public const STATUS_TESTING_IN_PROGRESS = 'testing_in_progress';
    public const STATUS_RETURNED_BY_TESTER = 'returned_by_tester';
    public const STATUS_RETURNED_TO_DEVELOPER = 'returned_to_dev';
    public const STATUS_RETURNED_TO_TESTER = 'returned_to_tester';
    public const STATUS_COMPLETED_LAYER_LEAD = 'completed_layer_lead';
    public const STATUS_PENDING_NIC_ADMIN_REVIEW = 'pending_nic_review';
    public const STATUS_PENDING_STATE_ADMIN_REVIEW = 'pending_state_review';
    public const STATUS_RETURNED_TO_NIC_ADMIN = 'returned_to_nic_admin';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_REOPENED = 'reopened';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    public const PRIORITIES = ['Low', 'Medium', 'High', 'Critical'];

    public const CATEGORY_OPTIONS = [
        'APMS',
        'Audit Para',
        'Audit Report',
        'Audit Schedule',
        'Data Correction',
        'Login / Access',
        'Report Download',
        'Support',
        'Workflow',
        'Other',
    ];

    public const STATUS_LABELS = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'need_clarification' => 'Need Clarification',
        self::STATUS_SUBMITTED => 'Submitted',
        self::STATUS_PENDING_STATE_ADMIN => 'Pending with State Admin',
        self::STATUS_FORWARDED_NIC_ADMIN => 'Forwarded to NIC Admin',
        self::STATUS_ASSIGNED_LAYER_LEAD => 'Assigned to Senior Developer',
        self::STATUS_ASSIGNED_DEVELOPER => 'Assigned to Developer',
        self::STATUS_DEVELOPER_IN_PROGRESS => 'Developer In Progress',
        self::STATUS_RETURNED_BY_DEVELOPER => 'Returned by Developer',
        self::STATUS_ASSIGNED_TESTER => 'Assigned to Tester',
        self::STATUS_TESTING_IN_PROGRESS => 'Testing In Progress',
        self::STATUS_RETURNED_BY_TESTER => 'Returned by Tester',
        self::STATUS_RETURNED_TO_DEVELOPER => 'Returned to Developer',
        self::STATUS_RETURNED_TO_TESTER => 'Returned to Tester',
        self::STATUS_COMPLETED_LAYER_LEAD => 'Completed by Senior Developer',
        self::STATUS_PENDING_NIC_ADMIN_REVIEW => 'Pending NIC Admin Review',
        self::STATUS_PENDING_STATE_ADMIN_REVIEW => 'Pending State Admin Review',
        self::STATUS_RETURNED_TO_NIC_ADMIN => 'Returned to NIC Admin',
        'developer_in_progress' => 'Developer In Progress',
        'returned_by_developer' => 'Returned by Developer',
        'returned_to_developer' => 'Returned to Developer',
        'pending_nic_admin_review' => 'Pending NIC Admin Review',
        'pending_state_admin_review' => 'Pending State Admin Review',
        self::STATUS_CLOSED => 'Closed',
        self::STATUS_REOPENED => 'Reopened',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_CANCELLED => 'Cancelled',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['status_label'];

    protected static function booted(): void
    {
        static::creating(function (self $ticket): void {
            if (!$ticket->ticket_number) {
                $ticket->ticket_number = self::generateTicketNumber($ticket);
            }
        });
    }

    public static function labelFor(?string $status): string
    {
        return self::STATUS_LABELS[$status] ?? str_replace('_', ' ', ucfirst((string) $status));
    }

    public static function mainStatusKeyFor(?string $status): string
    {
        return match ($status) {
            self::STATUS_ASSIGNED_LAYER_LEAD,
            self::STATUS_ASSIGNED_DEVELOPER,
            self::STATUS_DEVELOPER_IN_PROGRESS,
            'developer_in_progress',
            self::STATUS_RETURNED_BY_DEVELOPER,
            'returned_by_developer',
            self::STATUS_ASSIGNED_TESTER,
            self::STATUS_TESTING_IN_PROGRESS,
            self::STATUS_RETURNED_BY_TESTER,
            self::STATUS_RETURNED_TO_DEVELOPER,
            'returned_to_developer',
            self::STATUS_RETURNED_TO_TESTER,
            self::STATUS_COMPLETED_LAYER_LEAD,
            self::STATUS_PENDING_NIC_ADMIN_REVIEW,
            'pending_nic_admin_review',
            self::STATUS_PENDING_STATE_ADMIN_REVIEW,
            'pending_state_admin_review',
            self::STATUS_RETURNED_TO_NIC_ADMIN => 'in_progress',
            default => (string) ($status ?: 'open'),
        };
    }

    public static function mainStatusLabelFor(?string $status): string
    {
        return self::labelFor(self::mainStatusKeyFor($status));
    }

    public static function generateTicketNumber(self $ticket): string
    {
        $year = now('Asia/Kolkata')->format('Y');
        $departmentLetter = self::resolveDepartmentLetter($ticket);
        $quarterCode = self::resolveQuarterCode($ticket->planmappingid);
        $prefix = sprintf('TKT%s%s%s', $year, $departmentLetter, $quarterCode);

        $lastTicketNumber = self::query()
            ->where('ticket_number', 'like', $prefix.'%')
            ->orderByDesc('ticket_number')
            ->value('ticket_number');

        $next = $lastTicketNumber ? ((int) substr($lastTicketNumber, -5)) + 1 : 1;

        return $prefix.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    private static function resolveDepartmentLetter(self $ticket): string
    {
        $departmentName = $ticket->department_name;

        if (! $departmentName && $ticket->deptcode) {
            $departmentName = DB::table('audit.mst_dept')
                ->where('deptcode', $ticket->deptcode)
                ->value('deptesname');
        }

        if (! $departmentName) {
            return 'X';
        }

        preg_match('/[A-Z]/', Str::upper((string) $departmentName), $matches);

        return $matches[0] ?? 'X';
    }

    private static function resolveQuarterCode($planMappingId): string
    {
        if (! $planMappingId) {
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

    public function attachments()
    {
        return collect($this->attachments ?? []);
    }

    public function comments()
    {
        return $this->hasMany(HelpdeskV2Comment::class, 'ticket_id')->latest();
    }

    public function histories()
    {
        return $this->hasMany(HelpdeskV2Comment::class, 'ticket_id')->oldest('created_at')->oldest('id');
    }

    public function latestHistories()
    {
        return $this->hasMany(HelpdeskV2Comment::class, 'ticket_id')->latest();
    }

    public function devComments()
    {
        return $this->hasMany(HelpdeskTicketDevComment::class, 'ticket_id')->latest();
    }

    public function assignments()
    {
        return $this->hasMany(HelpdeskTicketAssignment::class, 'ticket_id')->latest('assigned_at');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::labelFor($this->status);
    }

    public function mainStatusKey(): string
    {
        return self::mainStatusKeyFor($this->status);
    }

    public function mainStatusLabel(): string
    {
        return self::mainStatusLabelFor($this->status);
    }

    public function getFinancialYearLabelAttribute(): string
    {
        return $this->planDetailValue('financialyear') ?: (string) ($this->financialyearcode ?: '-');
    }

    public function getAuditQuarterLabelAttribute(): string
    {
        return $this->planDetailValue('planname')
            ?: $this->planDetailValue('auditquarter')
            ?: $this->planDetailValue('auditquartercode')
            ?: (string) ($this->planmappingid ?: '-');
    }

    public function getRequestTypeLabelAttribute(): string
    {
        if (! $this->request_type) {
            return '-';
        }

        return HelpdeskTicket::REQUEST_TYPE_OPTIONS[$this->request_type]
            ?? Str::of((string) $this->request_type)->replace('_', ' ')->title()->toString();
    }

    public function getPendingRoleAttribute(): ?string
    {
        return $this->forwarded_to_role;
    }

    public function getPendingUseridAttribute(): ?string
    {
        return $this->assigned_to_userid;
    }

    public function getPendingNameAttribute(): ?string
    {
        return $this->assigned_to_name;
    }

    public function getCreatedByUseridAttribute(): ?string
    {
        return $this->cams_userid;
    }

    public function getCreatedByNameAttribute(): ?string
    {
        return $this->user_name;
    }

    public function getCreatedByEmailAttribute(): ?string
    {
        return $this->user_email;
    }

    public function getCreatedByRoleAttribute(): string
    {
        return 'user';
    }

    public function getLayerLeadUseridAttribute(): ?string
    {
        return $this->assignmentUserId('layer_lead');
    }

    public function getLayerLeadNameAttribute(): ?string
    {
        return $this->assignmentName('layer_lead');
    }

    public function getDeveloperUseridAttribute(): ?string
    {
        return $this->assignmentUserId('developer') ?: $this->directDeveloperValue('assigned_to_userid');
    }

    public function getDeveloperNameAttribute(): ?string
    {
        return $this->assignmentName('developer') ?: $this->directDeveloperValue('assigned_to_name');
    }

    public function getTesterUseridAttribute(): ?string
    {
        return $this->assignmentUserId('tester');
    }

    public function getTesterNameAttribute(): ?string
    {
        return $this->assignmentName('tester');
    }

    public function getExpectedCompletionDateAttribute(): mixed
    {
        return null;
    }

    public function getResolutionAttribute(): ?string
    {
        return $this->forward_notes;
    }

    public function getClosedAtAttribute(): mixed
    {
        return $this->resolved_at;
    }

    public function getReopenCountAttribute(): int
    {
        if ($this->relationLoaded('comments')) {
            return $this->comments
                ->filter(fn ($comment) => Str::contains(Str::upper((string) $comment->comment), ['[REOPEN]', 'REOPENED']))
                ->count();
        }

        return (int) DB::table('audit.helpdesk_ticket_comments')
            ->where('ticket_id', $this->id)
            ->where(function ($query) {
                $query->whereRaw('UPPER(comment) LIKE ?', ['%[REOPEN]%'])
                    ->orWhereRaw('UPPER(comment) LIKE ?', ['%REOPENED%']);
            })
            ->count();
    }

    public function pendingWithLabel(): string
    {
        if ($this->pending_name) {
            return $this->pending_name.' ('.$this->pendingRoleLabel().')';
        }

        return $this->pendingRoleLabel();
    }

    public function currentOnLabelForRole(string $viewerRole): string
    {
        if ($this->isFinalStatus()) {
            return $this->status_label;
        }

        if (in_array($viewerRole, ['user', 'state_admin'], true) && $this->isWithTechnicalTeam()) {
            return 'NIC Admin';
        }

        if (in_array($viewerRole, ['nic_admin', 'developer', 'layer_lead', 'watchlist'], true) && $this->isWithTechnicalTeam()) {
            $technical = $this->currentTechnicalOwner();

            if ($technical['name']) {
                return $technical['name'].' ('.$technical['label'].')';
            }
        }

        return $this->pendingWithLabel();
    }

    public function currentOnMetaForRole(string $viewerRole): ?string
    {
        if (! in_array($viewerRole, ['nic_admin', 'developer', 'layer_lead', 'watchlist'], true) || ! $this->isWithTechnicalTeam()) {
            return null;
        }

        $technical = $this->currentTechnicalOwner();

        if (! $technical['assigned_at']) {
            return null;
        }

        return 'Assigned on '.$this->formatDateTime($technical['assigned_at']);
    }

    public function assignedByLabel(): string
    {
        if ($this->isFinalStatus()) {
            return '-';
        }

        $assignment = $this->initialTechnicalAssignment() ?: $this->latestAssignment();

        return $assignment?->assigned_by_name ?: '-';
    }

    public function hasLayerLeadFlow(): bool
    {
        if (! $this->layer_lead_userid) {
            return false;
        }

        if ($this->pending_role === 'layer_lead'
            || in_array($this->status, [
                self::STATUS_ASSIGNED_LAYER_LEAD,
                self::STATUS_RETURNED_BY_DEVELOPER,
                'returned_by_developer',
                self::STATUS_RETURNED_BY_TESTER,
                self::STATUS_COMPLETED_LAYER_LEAD,
            ], true)) {
            return true;
        }

        $developerAssignment = $this->assignmentForRole('developer');

        return $developerAssignment
            && (string) $developerAssignment->assigned_by_userid === (string) $this->layer_lead_userid;
    }

    public function pendingRoleLabel(): string
    {
        return match ($this->pending_role) {
            'stateadmin', 'superadmin' => 'State Admin',
            'nicadmin' => 'NIC Admin',
            'state_admin' => 'State Admin',
            'nic_admin' => 'NIC Admin',
            'layer_lead' => 'Senior Developer',
            'developer' => 'Developer',
            'tester' => 'Tester',
            default => '-',
        };
    }

    public function ageInDays(): int
    {
        return (int) $this->created_at?->diffInDays(now('Asia/Kolkata'));
    }

    public function techTeamStatusLabel(): string
    {
        return $this->status_label;
    }

    public function techTeamStatusMeta(): ?string
    {
        return null;
    }

    private function assignmentUserId(string $role): ?string
    {
        $assignment = $this->assignmentForRole($role);

        return $assignment?->developer_userid;
    }

    private function directDeveloperValue(string $column): ?string
    {
        if (! in_array($this->pending_role, ['developer'], true)
            && ! in_array($this->status, [
                self::STATUS_ASSIGNED_DEVELOPER,
                self::STATUS_DEVELOPER_IN_PROGRESS,
                'developer_in_progress',
                self::STATUS_RETURNED_TO_DEVELOPER,
                'returned_to_developer',
                'in_progress',
            ], true)) {
            return null;
        }

        return $this->{$column};
    }

    private function planDetailValue(string $key): ?string
    {
        if (! $this->deptcode || ! $this->financialyearcode) {
            return null;
        }

        try {
            $detail = collect(CommonModel::getplandetailsforreport($this->deptcode))
                ->first(function ($row) {
                    $matchesYear = (string) ($row->financialyearcode ?? '') === (string) $this->financialyearcode;

                    if (! $this->planmappingid) {
                        return $matchesYear;
                    }

                    return $matchesYear && (string) ($row->planmappingid ?? '') === (string) $this->planmappingid;
                });
        } catch (\Throwable) {
            return null;
        }

        return $detail && isset($detail->{$key}) ? (string) $detail->{$key} : null;
    }

    private function assignmentName(string $role): ?string
    {
        $assignment = $this->assignmentForRole($role);

        return $assignment?->developer_name;
    }

    private function assignmentForRole(string $role): ?object
    {
        $statuses = match ($role) {
            'developer' => ['developer', 'assigned', 'returned', 'reassigned'],
            'layer_lead' => ['layer_lead', 'additional_layer'],
            'tester' => ['tester'],
            default => [$role],
        };

        if ($this->relationLoaded('assignments')) {
            return $this->assignments->first(fn ($assignment) => in_array($assignment->status, $statuses, true));
        }

        return DB::table('audit.helpdesk_ticket_assignments')
            ->where('ticket_id', $this->id)
            ->whereIn('status', $statuses)
            ->latest('assigned_at')
            ->latest('id')
            ->first();
    }

    private function latestAssignment(): ?object
    {
        if ($this->relationLoaded('assignments')) {
            return $this->assignments->first();
        }

        return DB::table('audit.helpdesk_ticket_assignments')
            ->where('ticket_id', $this->id)
            ->latest('assigned_at')
            ->latest('id')
            ->first();
    }

    private function initialTechnicalAssignment(): ?object
    {
        $statuses = ['layer_lead', 'additional_layer', 'assigned', 'developer', 'reassigned', 'returned'];

        if ($this->relationLoaded('assignments')) {
            return $this->assignments
                ->filter(fn ($assignment) => in_array($assignment->status, $statuses, true))
                ->sortBy(fn ($assignment) => optional($assignment->assigned_at)->timestamp ?: 0)
                ->first();
        }

        return DB::table('audit.helpdesk_ticket_assignments')
            ->where('ticket_id', $this->id)
            ->whereIn('status', $statuses)
            ->oldest('assigned_at')
            ->oldest('id')
            ->first();
    }

    private function currentTechnicalOwner(): array
    {
        $role = match (true) {
            in_array($this->pending_role, ['layer_lead', 'developer', 'tester'], true) => $this->pending_role,
            in_array($this->status, [
                self::STATUS_ASSIGNED_LAYER_LEAD,
                self::STATUS_RETURNED_BY_DEVELOPER,
                'returned_by_developer',
                self::STATUS_RETURNED_BY_TESTER,
                self::STATUS_COMPLETED_LAYER_LEAD,
            ], true) => 'layer_lead',
            in_array($this->status, [
                self::STATUS_ASSIGNED_TESTER,
                self::STATUS_TESTING_IN_PROGRESS,
                self::STATUS_RETURNED_TO_TESTER,
            ], true) => 'tester',
            default => 'developer',
        };

        $assignment = $this->assignmentForRole($role);

        return [
            'role' => $role,
            'label' => match ($role) {
                'layer_lead' => 'Senior Developer',
                'tester' => 'Tester',
                default => 'Developer',
            },
            'name' => $assignment?->developer_name ?: ($this->pending_role === $role ? $this->pending_name : null),
            'assigned_at' => $assignment?->assigned_at ?: ($this->pending_role === $role ? $this->forwarded_at : null),
        ];
    }

    private function isWithTechnicalTeam(): bool
    {
        if ($this->isFinalStatus()) {
            return false;
        }

        return in_array($this->pending_role, ['developer', 'layer_lead', 'tester'], true)
            || in_array($this->status, [
                'in_progress',
                self::STATUS_ASSIGNED_LAYER_LEAD,
                self::STATUS_ASSIGNED_DEVELOPER,
                self::STATUS_DEVELOPER_IN_PROGRESS,
                'developer_in_progress',
                self::STATUS_RETURNED_BY_DEVELOPER,
                'returned_by_developer',
                self::STATUS_ASSIGNED_TESTER,
                self::STATUS_TESTING_IN_PROGRESS,
                self::STATUS_RETURNED_BY_TESTER,
                self::STATUS_RETURNED_TO_DEVELOPER,
                'returned_to_developer',
                self::STATUS_RETURNED_TO_TESTER,
                self::STATUS_COMPLETED_LAYER_LEAD,
            ], true);
    }

    public function isFinalStatus(): bool
    {
        return in_array($this->status, [self::STATUS_CLOSED, self::STATUS_REJECTED, self::STATUS_CANCELLED], true)
            || ($this->status === 'resolved' && ! $this->pending_role);
    }

    private function formatDateTime(mixed $value): string
    {
        return self::displayDateTime($value);
    }

    public static function displayDateTime(mixed $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('d/m/Y h:i A');
        }

        return Carbon::parse($value)->format('d/m/Y h:i A');
    }
}
