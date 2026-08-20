<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

class FreshHelpdesk extends Model
{
    protected $table = 'audit.helpdesk_tickets';

    public const CATEGORY_TABLE = 'audit.helpdesk_module_categories';
    public const TASK_TABLE = 'audit.developer_tasks';
    public const TASK_HISTORY_TABLE = 'audit.task_management_histories';
    private const TASK_TABLE_NAME = 'developer_tasks';

    public const ROLE_USER = 'user';
    public const ROLE_STATE_ADMIN = 'state_admin';
    public const ROLE_NIC_ADMIN = 'nic_admin';
    public const ROLE_SENIOR_DEVELOPER = 'senior_developer';
    public const ROLE_DEVELOPER = 'developer';

    public const TICKET_PENDING_STATE = 'pending_state_admin';
    public const TICKET_PENDING_STATE_REVIEW = 'pending_state_review';
    public const TICKET_PENDING_STATE_ADMIN_REVIEW = 'pending_state_admin_review';
    public const TICKET_PENDING_NIC = 'pending_nic_admin';
    public const TICKET_PENDING_SENIOR = 'pending_senior_dev';
    public const TICKET_PENDING_DEVELOPER = 'pending_developer';
    public const TICKET_RETURNED_SENIOR = 'returned_senior_dev';
    public const TICKET_RETURNED_NIC = 'returned_nic_admin';
    public const TICKET_RETURNED_STATE = 'returned_state_admin';
    public const TICKET_RETURNED_USER = 'returned_user';
    public const TICKET_CLOSED = 'closed';
    public const TICKET_IN_PROGRESS = 'in_progress';
    public const TICKET_NEED_CLARIFICATION = 'need_clarification';
    public const TICKET_RESOLVED = 'resolved';
    public const TICKET_SCOPE_SPECIFIED = 'specified';
    public const TICKET_SCOPE_ALL = 'all';

	    public const TASK_NOT_STARTED = 'not_started';
	    public const TASK_IN_PROGRESS = 'in_progress';
	    public const TASK_NEED_CLARIFICATION = 'need_clarification';
	    public const TASK_WRONGLY_ASSIGNED = 'wrongly_assigned';
	    public const TASK_RESOLVED = 'resolved';
	    public const TASK_CLOSED = 'closed';

    public const DEV_STATUS_IN_PROGRESS = 'in_progress';
    public const DEV_STATUS_NEED_CLARIFICATION = 'need_clarification';
    public const DEV_STATUS_COMPLETED = 'completed';

    private static array $schemaEnsureCache = [];

    public static function role(): string
    {
        $charge = session('charge');
        $userId = self::userId();

        if (($charge->chargeid ?? null) !== null && (string) $charge->chargeid === '1') {
            return self::ROLE_STATE_ADMIN;
        }

        if (($charge->chargeid ?? null) !== null && (string) $charge->chargeid === '907') {
            return self::ROLE_NIC_ADMIN;
        }

        if (($charge->roleactioncode ?? null) !== null && (string) $charge->roleactioncode === '01') {
            return self::ROLE_NIC_ADMIN;
        }

        $developer = self::developerUser();
        if ($developer) {
            return strtoupper(trim((string) ($developer->senior_flag ?? ''))) === 'Y'
                ? self::ROLE_SENIOR_DEVELOPER
                : self::ROLE_DEVELOPER;
        }

        return self::ROLE_USER;
    }

    public static function canAccess(): bool
    {
        $roleActionCode = (string) (session('charge')->roleactioncode ?? '');

        return session('user') !== null
            && (
                $roleActionCode === '27'
                || in_array(self::role(), [
                    self::ROLE_STATE_ADMIN,
                    self::ROLE_NIC_ADMIN,
                    self::ROLE_SENIOR_DEVELOPER,
                    self::ROLE_DEVELOPER,
                ], true)
            );
    }

    public static function ticketUrlToken(int|string|null $ticketId): string
    {
        return Crypt::encryptString((string) $ticketId);
    }

    public static function ticketIdFromUrlToken(?string $token): ?int
    {
        $token = trim((string) $token);

        if ($token === '') {
            return null;
        }

        if (ctype_digit($token)) {
            return (int) $token;
        }

        try {
            $ticketId = trim(Crypt::decryptString($token));
        } catch (\Throwable $e) {
            return null;
        }

        return ctype_digit($ticketId) ? (int) $ticketId : null;
    }

    public static function taskUrlToken(int|string|null $taskId): string
    {
        return Crypt::encryptString((string) $taskId);
    }

    public static function taskIdFromUrlToken(?string $token): ?int
    {
        $token = trim((string) $token);

        if ($token === '') {
            return null;
        }

        if (ctype_digit($token)) {
            return (int) $token;
        }

        try {
            $taskId = trim(Crypt::decryptString($token));
        } catch (\Throwable $e) {
            return null;
        }

        return ctype_digit($taskId) ? (int) $taskId : null;
    }

    public static function roleLabel(?string $role = null): string
    {
        $roleValue = (string) ($role ?? self::role());
        $normalized = self::normalizedTicketStatus($roleValue);

        if (self::isLegacyStateForwardStatus($roleValue) || in_array($normalized, ['state admin', 'stateadmin', 'superadmin'], true)) {
            return 'State Admin';
        }

        return match ($role ?? self::role()) {
            self::ROLE_STATE_ADMIN, 'stateadmin' => 'State Admin',
            self::ROLE_NIC_ADMIN, 'nicadmin' => 'NIC Admin',
            self::ROLE_SENIOR_DEVELOPER => 'Senior Developer',
            self::ROLE_DEVELOPER => 'Developer',
            default => 'User',
        };
    }

    public static function userId(): ?string
    {
        $userId = session('user')->userid ?? session('user')->deptuserid ?? null;

        return $userId === null ? null : (string) $userId;
    }

    public static function userName(): string
    {
        return trim((string) (session('user')->username ?? session('user')->deptusername ?? 'CAMS User'));
    }

    public static function userEmail(): ?string
    {
        return session('user')->email ?? null;
    }

    public static function developerUser(): ?object
    {
        $userId = self::userId();
        $email = self::userEmail();

        if (!$userId && !$email) {
            return null;
        }

        return DB::table('audit.dev_userdetails')
            ->where('statusflag', 'Y')
            ->where(function ($query) use ($userId, $email) {
                if ($userId) {
                    $query->where('devuserid', $userId);
                }

                if ($email) {
                    $query->orWhereRaw('LOWER(email) = ?', [strtolower((string) $email)]);
                }
            })
            ->first(['devuserid', 'devename', 'email', 'senior_flag']);
    }

    public static function developerUserId(): ?string
    {
        $developer = self::developerUser();

        return $developer ? (string) $developer->devuserid : self::userId();
    }

    public static function taskAssignedToCurrentDeveloper(object $task, ?string $developerUserId = null): bool
    {
        $developerUserId = (string) ($developerUserId ?? self::developerUserId());
        $taskDeveloperUserId = trim((string) ($task->developer_userid ?? ''));

        if ($developerUserId !== '' && $taskDeveloperUserId !== '' && $taskDeveloperUserId === $developerUserId) {
            return true;
        }

        $taskDeveloperName = self::normalizePersonName($task->developer_name ?? null);

        if ($taskDeveloperName === '') {
            return false;
        }

        $developer = self::developerUser();
        $candidateNames = [
            $developer->devename ?? null,
            self::userName(),
        ];

        foreach ($candidateNames as $candidateName) {
            if ($taskDeveloperName === self::normalizePersonName($candidateName)) {
                return true;
            }
        }

        return false;
    }

    private static function normalizePersonName($name): string
    {
        return strtolower(trim((string) preg_replace('/\s+/', ' ', (string) $name)));
    }

    public static function deptCode(): ?string
    {
        $deptCode = session('charge')->deptcode ?? null;

        return $deptCode === null ? null : (string) $deptCode;
    }

    public static function departments(): Collection
    {
        return DB::table('audit.mst_dept')
            ->select('deptcode', 'deptesname')
            ->where('statusflag', 'Y')
            ->orderBy('orderid')
            ->orderBy('deptesname')
            ->get();
    }

    public static function sessionDepartment(): ?object
    {
        $deptCode = self::deptCode();

        if (!$deptCode) {
            return null;
        }

        return DB::table('audit.mst_dept')
            ->select('deptcode', 'deptesname')
            ->where('deptcode', $deptCode)
            ->first();
    }

    public static function financialYears(?string $deptCode = null): Collection
    {
        $deptCode = $deptCode ?: self::deptCode();

        $query = DB::table('audit.auditplanmapping as apm')
            ->join('audit.mst_financialyear as fy', 'fy.financialyearcode', '=', 'apm.financialyearcode')
            ->select('fy.financialyearcode', 'fy.financialyear')
            ->where('fy.statusflag', 'Y')
            ->whereIn('apm.statusflag', ['F', 'P', 'Y'])
            ->distinct()
            ->orderByDesc('fy.financialyear');

        if ($deptCode) {
            $query->where('apm.deptcode', $deptCode);
        }

        return $query
            ->get();
    }

    public static function auditQuarters(?string $deptCode = null): Collection
    {
        $deptCode = $deptCode ?: self::deptCode();

        $query = DB::table('audit.auditplanmapping as apm')
            ->join('audit.mst_auditquarter as aq', 'aq.auditquartercode', '=', 'apm.auditquartercode')
            ->select(
                DB::raw('MIN(apm.planmappingid) as planmappingid'),
                'apm.financialyearcode',
                DB::raw("COALESCE(NULLIF(apm.planname, ''), aq.auditquarter, aq.auditquartercode) as planname")
            )
            ->where('aq.statusflag', 'Y')
            ->whereIn('apm.statusflag', ['F', 'P', 'Y'])
            ->groupBy(
                'apm.financialyearcode',
                DB::raw("COALESCE(NULLIF(apm.planname, ''), aq.auditquarter, aq.auditquartercode)")
            )
            ->orderBy('apm.financialyearcode')
            ->orderBy(DB::raw('MIN(apm.planmappingid)'));

        if ($deptCode) {
            $query->where('apm.deptcode', $deptCode);
        }

        return $query
            ->get();
    }

    public static function planFilterOptions(?string $deptCode = null): Collection
    {
        $deptCode = $deptCode === 'ALL' ? null : ($deptCode ?: self::deptCode());

        $query = DB::table('audit.auditplanmapping as apm')
            ->join('audit.mst_financialyear as fy', 'fy.financialyearcode', '=', 'apm.financialyearcode')
            ->join('audit.mst_auditquarter as aq', 'aq.auditquartercode', '=', 'apm.auditquartercode')
            ->select(
                'apm.deptcode',
                'apm.financialyearcode',
                'fy.financialyear',
                DB::raw('MIN(apm.planmappingid) as planmappingid'),
                DB::raw("COALESCE(NULLIF(apm.planname, ''), aq.auditquarter, aq.auditquartercode) as planname")
            )
            ->where('fy.statusflag', 'Y')
            ->where('aq.statusflag', 'Y')
            ->whereIn('apm.statusflag', ['F', 'P', 'Y'])
            ->groupBy(
                'apm.deptcode',
                'apm.financialyearcode',
                'fy.financialyear',
                DB::raw("COALESCE(NULLIF(apm.planname, ''), aq.auditquarter, aq.auditquartercode)")
            )
            ->orderByDesc('fy.financialyear')
            ->orderBy(DB::raw('MIN(apm.planmappingid)'));

        if ($deptCode) {
            $query->where('apm.deptcode', $deptCode);
        }

        return $query->get();
    }

    public static function categoryCreateTableSql(): string
    {
        return "CREATE TABLE IF NOT EXISTS audit.helpdesk_module_categories (
    categoryid BIGSERIAL PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    statusflag CHAR(1) NOT NULL DEFAULT 'Y',
    createdby VARCHAR(50),
    createdon TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT NOW(),
    updatedby VARCHAR(50),
    updatedon TIMESTAMP WITHOUT TIME ZONE
);";
    }

    public static function categoryInsertSql(): string
    {
        return "INSERT INTO audit.helpdesk_module_categories
    (category_name, statusflag, createdby, createdon)
VALUES
    (:category_name, 'Y', :createdby, NOW());";
    }

    public static function createCategoryTable(): void
    {
        DB::statement(self::categoryCreateTableSql());
    }

    public static function categoryTableExists(): bool
    {
        return DB::table('information_schema.tables')
            ->where('table_schema', 'audit')
            ->where('table_name', 'helpdesk_module_categories')
            ->exists();
    }

    public static function categoryRows(): Collection
    {
        if (!self::categoryTableExists()) {
            return collect();
        }

        return DB::table(self::CATEGORY_TABLE)
            ->where('statusflag', 'Y')
            ->orderBy('categoryid')
            ->get();
    }

    public static function categoryOptions(): Collection
    {
        if (!self::categoryTableExists()) {
            return collect(self::defaultCategories());
        }

        $categories = DB::table(self::CATEGORY_TABLE)
            ->where('statusflag', 'Y')
            ->orderBy('categoryid')
            ->pluck('category_name');

        return $categories->isEmpty() ? collect(self::defaultCategories()) : $categories;
    }

    public static function categoryById(int $categoryId): ?object
    {
        if (!self::categoryTableExists()) {
            return null;
        }

        return DB::table(self::CATEGORY_TABLE)
            ->where('categoryid', $categoryId)
            ->where('statusflag', 'Y')
            ->first();
    }

    public static function insertCategory(string $categoryName): void
    {
        self::createCategoryTable();

        DB::table(self::CATEGORY_TABLE)->insert([
            'category_name' => trim($categoryName),
            'statusflag' => 'Y',
            'createdby' => self::userId(),
            'createdon' => now('Asia/Kolkata'),
        ]);
    }

    public static function defaultCategories(): array
    {
        return ['Login', 'Audit Schedule', 'Audit Report', 'APMS', 'Data Correction', 'Support', 'Other'];
    }

    public static function ticketStatusLabels(): array
    {
        return [
            self::TICKET_IN_PROGRESS => 'In Progress',
            self::TICKET_NEED_CLARIFICATION => 'Need Clarification',
            self::TICKET_RESOLVED => 'Resolved',
            self::TICKET_PENDING_STATE => 'Pending State Review',
            self::TICKET_PENDING_STATE_REVIEW => 'Pending State Review',
            self::TICKET_PENDING_STATE_ADMIN_REVIEW => 'Pending State Review',
            self::TICKET_PENDING_NIC => 'In Progress',
            self::TICKET_PENDING_SENIOR => 'In Progress',
            self::TICKET_PENDING_DEVELOPER => 'In Progress',
            self::TICKET_RETURNED_SENIOR => 'Need Clarification',
            self::TICKET_RETURNED_NIC => 'Need Clarification',
            self::TICKET_RETURNED_STATE => 'Need Clarification',
            self::TICKET_RETURNED_USER => 'Need Clarification',
            self::TICKET_CLOSED => 'Closed',
        ];
    }

    public static function normalizedTicketStatus(?string $status): string
    {
        $normalized = strtolower(str_replace(['_', '-'], ' ', trim((string) $status)));

        return preg_replace('/\s+/', ' ', $normalized) ?: '';
    }

    public static function isLegacyStateForwardStatus(?string $status): bool
    {
        $normalized = self::normalizedTicketStatus($status);

        return in_array($normalized, [
            'forward to state',
            'forwarded to state',
            'forward state',
            'pending state',
            'pending state admin',
            'pending state review',
            'pending state admin review',
        ], true)
            || (str_contains($normalized, 'forward') && str_contains($normalized, 'state'))
            || (str_contains($normalized, 'pending') && str_contains($normalized, 'state'));
    }

    public static function ticketStatusLabel(?string $status): string
    {
        $status = trim((string) $status);

        if ($status === '') {
            return '-';
        }

        $labels = self::ticketStatusLabels();

        if (array_key_exists($status, $labels)) {
            return $labels[$status];
        }

        $key = strtolower($status);
        if (array_key_exists($key, $labels)) {
            return $labels[$key];
        }

        $normalized = self::normalizedTicketStatus($status);

        if (self::isLegacyStateForwardStatus($status)) {
            return 'Pending State Review';
        }

        if (str_contains($normalized, 'returned') || str_contains($normalized, 'clarification')) {
            return 'Need Clarification';
        }

        if ($normalized === 'closed') {
            return 'Closed';
        }

        if ($normalized === 'resolved') {
            return 'Resolved';
        }

        if (in_array($normalized, ['in progress', 'inprogress', 'open', 'pending'], true)
            || str_contains($normalized, 'developer')
            || str_contains($normalized, 'nic')
            || str_contains($normalized, 'senior')
        ) {
            return 'In Progress';
        }

        return Str::headline($status);
    }

    public static function ticketStatusFilterLabels(): array
    {
        return [
            self::TICKET_IN_PROGRESS => 'In Progress',
            self::TICKET_NEED_CLARIFICATION => 'Need Clarification',
            self::TICKET_RESOLVED => 'Resolved',
        ];
    }

    public static function ticketStatusFilterKey(?string $status): string
    {
        $normalized = strtolower((string) $status);

        if (str_contains($normalized, 'returned')
            || str_contains($normalized, 'clarification')
            || in_array($normalized, [
                self::TICKET_RETURNED_SENIOR,
                self::TICKET_RETURNED_NIC,
                self::TICKET_RETURNED_STATE,
                self::TICKET_RETURNED_USER,
                self::TICKET_NEED_CLARIFICATION,
            ], true)
        ) {
            return self::TICKET_NEED_CLARIFICATION;
        }

        if (in_array($normalized, [
            self::TICKET_CLOSED,
            self::TICKET_RESOLVED,
            'closed',
            'resolved',
        ], true)) {
            return self::TICKET_RESOLVED;
        }

        return self::TICKET_IN_PROGRESS;
    }

    public static function ticketScopeTypeLabels(): array
    {
        return [
            self::TICKET_SCOPE_SPECIFIED => 'Specified',
            self::TICKET_SCOPE_ALL => 'All',
        ];
    }

    public static function ticketScopeTypeLabel(?string $scopeType): string
    {
        $scopeType = strtolower(trim((string) $scopeType));

        return self::ticketScopeTypeLabels()[$scopeType] ?? '-';
    }

    public static function taskStatusLabels(): array
    {
        return [
	            self::TASK_NOT_STARTED => 'Not Yet Started',
	            self::TASK_IN_PROGRESS => 'In Process',
	            self::TASK_NEED_CLARIFICATION => 'Need Clarification',
	            self::TASK_WRONGLY_ASSIGNED => 'Wrongly Assigned',
	            self::TASK_RESOLVED => 'Resolved',
	            self::TASK_CLOSED => 'Closed',
        ];
    }

	    public static function taskCurrentlyWith(object $task): string
	    {
	        $status = strtolower(trim((string) ($task->task_status_by_tester ?? '')));

        if ($status === self::TASK_CLOSED) {
            return 'Closed';
        }

	        if ($status === self::TASK_RESOLVED) {
	            return !empty($task->verified_by) ? 'NIC Admin' : (($task->senior_name ?? null) ?: 'Senior Developer');
	        }
	
	        if ($status === self::TASK_WRONGLY_ASSIGNED) {
	            return ($task->senior_name ?? null) ?: 'Senior Developer';
	        }

        return ($task->developer_name ?? null) ?: '-';
    }

    public static function ticketDashboardStats(Collection $tickets): array
    {
        return [
            'total' => $tickets->count(),
            'in_progress' => $tickets->filter(fn ($ticket) => self::isDashboardTicketInProgress($ticket))->count(),
            'urgent' => $tickets->filter(fn ($ticket) => self::isDashboardTicketUrgent($ticket))->count(),
            'developer_side' => $tickets->filter(fn ($ticket) => self::isDeveloperSideTicket($ticket))->count(),
            'returned' => $tickets->filter(fn ($ticket) => self::isDashboardTicketReturned($ticket))->count(),
            'resolved_closed' => $tickets->filter(fn ($ticket) => self::isDashboardTicketResolvedClosed($ticket))->count(),
        ];
    }

    public static function isDashboardTicketInProgress(object $ticket): bool
    {
        return !self::isDashboardTicketReturned($ticket)
            && !self::isDashboardTicketResolvedClosed($ticket);
    }

    public static function isDashboardTicketUrgent(object $ticket): bool
    {
        return !self::isDashboardTicketReturned($ticket)
            && !self::isDashboardTicketResolvedClosed($ticket)
            && in_array(strtolower((string) ($ticket->priority ?? '')), ['high', 'critical', 'urgent'], true);
    }

    public static function isDeveloperSideTicket(object $ticket): bool
    {
        if (self::isDashboardTicketResolvedClosed($ticket)) {
            return false;
        }

        $forwardedRole = (string) ($ticket->forwarded_to_role ?? '');
        $normalizedForwardedRole = self::normalizedTicketStatus($forwardedRole);

        return in_array($forwardedRole, [self::ROLE_SENIOR_DEVELOPER, self::ROLE_DEVELOPER, 'developer', 'layer_lead', 'tester'], true)
            || in_array($normalizedForwardedRole, ['senior developer', 'developer', 'layer lead', 'tester'], true);
    }

    public static function isDashboardTicketReturned(object $ticket): bool
    {
        $status = strtolower((string) ($ticket->status ?? ''));

        return str_contains($status, 'returned')
            || str_contains($status, 'clarification')
            || in_array($status, [
                self::TICKET_RETURNED_SENIOR,
                self::TICKET_RETURNED_NIC,
                self::TICKET_RETURNED_STATE,
                self::TICKET_RETURNED_USER,
                self::TICKET_NEED_CLARIFICATION,
            ], true);
    }

    public static function isDashboardTicketResolvedClosed(object $ticket): bool
    {
        return in_array(strtolower((string) ($ticket->status ?? '')), [
            self::TICKET_CLOSED,
            'closed',
            self::TICKET_RESOLVED,
            'rejected',
            'cancelled',
        ], true);
    }

    public static function taskDashboardStats(Collection $tasks): array
    {
        return [
            'total' => $tasks->count(),
            'in_progress' => $tasks->filter(fn ($task) => self::isDashboardTaskInProgress($task))->count(),
            'urgent' => $tasks->filter(fn ($task) => self::isDashboardTaskUrgent($task))->count(),
            'returned' => $tasks->filter(fn ($task) => self::isDashboardTaskReturned($task))->count(),
            'resolved_closed' => $tasks->filter(fn ($task) => self::isDashboardTaskResolvedClosed($task))->count(),
        ];
    }

    public static function isDashboardTaskInProgress(object $task): bool
    {
        return !self::isDashboardTaskReturned($task)
            && !self::isDashboardTaskResolvedClosed($task);
    }

    public static function isDashboardTaskUrgent(object $task): bool
    {
        if (self::isDashboardTaskResolvedClosed($task) || self::isDashboardTaskReturned($task) || empty($task->expected_date_to_complete)) {
            return false;
        }

        try {
            return Carbon::parse($task->expected_date_to_complete, 'Asia/Kolkata')->lt(View::shared('get_nowtime'));
        } catch (\Throwable $exception) {
            return false;
        }
    }

    public static function isDashboardTaskReturned(object $task): bool
    {
        $status = strtolower(trim((string) ($task->task_status_by_tester ?? '')));
        if (str_contains($status, 'returned')) {
            return true;
        }

        $taskId = (int) ($task->id ?? 0);
        if ($taskId <= 0 || !self::ensureTaskHistoryTable()) {
            return false;
        }

        return DB::table(self::TASK_HISTORY_TABLE.' as tmh')
            ->where('tmh.task_id', $taskId)
            ->whereIn('tmh.action_key', ['senior_return', 'reopen'])
            ->whereRaw('tmh.performed_at = (
                SELECT MAX(performed_at)
                FROM audit.task_management_histories latest_task_history
                WHERE latest_task_history.task_id = tmh.task_id
            )')
            ->exists();
    }

    public static function isDashboardTaskResolvedClosed(object $task): bool
    {
        $status = strtolower(trim((string) ($task->task_status_by_tester ?? '')));

        return !empty($task->completed_on)
            || in_array($status, [
                self::TASK_CLOSED,
                'closed',
                'completed',
                'resolved',
            ], true);
    }

    public static function dashboardCurrentWith(object $ticket, ?string $viewerRole = null): string
    {
        $status = (string) ($ticket->status ?? '');

        if (self::normalizedTicketStatus($status) === self::TICKET_CLOSED) {
            if (self::ticketCreatorIsStateAdmin($ticket)) {
                $creatorName = trim((string) ($ticket->user_name ?? ''));

                return $creatorName !== '' ? $creatorName.' (StateAdmin)' : 'State Admin';
            }

            return 'Closed';
        }

        if (self::isLegacyStateForwardStatus($status)) {
            return 'State Admin';
        }

        $forwardedRole = (string) ($ticket->forwarded_to_role ?? '');
        $normalizedForwardedRole = self::normalizedTicketStatus($forwardedRole);
        $nicRoles = ['nicadmin', 'nic_admin', 'nic_admn', self::ROLE_NIC_ADMIN];
        $developerRoles = [self::ROLE_SENIOR_DEVELOPER, self::ROLE_DEVELOPER, 'developer', 'layer_lead', 'tester'];
        $assignedName = trim((string) (($ticket->assigned_to_name ?? '')
            ?: (($ticket->assigned_user_name ?? '')
            ?: (($ticket->forwarded_user_name ?? '')
            ?: (($ticket->latest_assignment_name ?? '')
            ?: (($ticket->latest_assignment_dev_name ?? '')
            ?: ($ticket->latest_dev_comment_user_name ?? '')))))));
        $assignmentRole = self::normalizedTicketStatus((string) ($ticket->latest_assignment_status ?? ''));
        $isSeniorDeveloper = $forwardedRole === self::ROLE_SENIOR_DEVELOPER
            || str_contains($assignmentRole, 'senior');
        $assignedWithRole = function () use ($assignedName, $isSeniorDeveloper) {
            $roleLabel = $isSeniorDeveloper ? 'Senior Developer' : 'Developer';

            return $assignedName !== '' ? $assignedName.' ('.$roleLabel.')' : $roleLabel;
        };
        $assignmentAfterForward = false;

        if ($assignedName !== '' && !empty($ticket->latest_assignment_at) && !empty($ticket->forwarded_at)) {
            try {
                $assignmentAfterForward = Carbon::parse($ticket->latest_assignment_at)->gte(Carbon::parse($ticket->forwarded_at));
            } catch (\Throwable $exception) {
                $assignmentAfterForward = false;
            }
        }

        $isStateRole = self::isLegacyStateForwardStatus($forwardedRole)
            || in_array($normalizedForwardedRole, ['state admin', 'stateadmin', 'superadmin'], true);
        $isNicRole = in_array($forwardedRole, $nicRoles, true);
        $isUserRole = in_array($normalizedForwardedRole, ['user', 'ticket owner', 'owner'], true);

        if ($isUserRole) {
            $userName = trim((string) ($ticket->user_name ?? ''));

            return $userName !== '' ? $userName.' (User)' : 'User';
        }

        if (in_array($viewerRole, [self::ROLE_USER, self::ROLE_STATE_ADMIN], true)
            && (
                in_array($forwardedRole, $developerRoles, true)
                || $assignmentAfterForward
                || ($assignedName !== '' && !$isStateRole && !$isNicRole)
            )
        ) {
            return 'NIC Admin';
        }

        if ($assignmentAfterForward) {
            return $assignedWithRole();
        }

        if ($isStateRole) {
            return 'State Admin';
        }

        if ($isNicRole) {
            return 'NIC Admin';
        }

        if (in_array($forwardedRole, $developerRoles, true) || $assignedName !== '') {
            return $assignedWithRole();
        }

        if (self::ticketStatusFilterKey($status) === self::TICKET_RESOLVED && trim($forwardedRole) === '') {
            return 'Resolved';
        }

        return self::roleLabel($ticket->forwarded_to_role ?? null);
    }

    public static function stateForwardedRoleValues(): array
    {
        return [
            'superadmin',
            'stateadmin',
            'state_admin',
            'FORWARD_TO_STATE',
            'forward_to_state',
            'FORWARDED_TO_STATE',
            'forwarded_to_state',
        ];
    }

    private static function applyNicAdminTicketScope($query): void
    {
        $query->where(function ($nicQuery) {
            $nicQuery->whereIn('t.forwarded_to_role', [
                'nicadmin',
                'nic_admin',
                'nic_admn',
                self::ROLE_NIC_ADMIN,
                self::ROLE_SENIOR_DEVELOPER,
                self::ROLE_DEVELOPER,
                'developer',
                'layer_lead',
                'tester',
            ])
                ->orWhereExists(function ($assignmentQuery) {
                    $assignmentQuery->select(DB::raw(1))
                        ->from('audit.helpdesk_ticket_assignments as hta_scope')
                        ->whereColumn('hta_scope.ticket_id', 't.id');
                })
                ->orWhereExists(function ($commentQuery) {
                    $commentQuery->select(DB::raw(1))
                        ->from('audit.helpdesk_ticket_comments as htc_scope')
                        ->whereColumn('htc_scope.ticket_id', 't.id')
                        ->where(function ($nestedQuery) {
                            $nestedQuery->where('htc_scope.comment', 'like', '%NIC Admin%')
                                ->orWhere('htc_scope.user_role', 'like', '%NIC%');
                        });
                })
                ->orWhereExists(function ($devCommentQuery) {
                    $devCommentQuery->select(DB::raw(1))
                        ->from('audit.helpdesk_ticket_comments_dev as htdc_scope')
                        ->whereColumn('htdc_scope.ticket_id', 't.id');
                });
        });
    }

    public static function dashboardLatestFlow(object $ticket): array
    {
        $events = [];

        if (!empty($ticket->latest_comment) || !empty($ticket->latest_comment_at)) {
            $events[] = [
                'note' => (string) ($ticket->latest_comment ?: 'Workflow updated.'),
                'by' => trim((string) (($ticket->latest_comment_user_name ?? '') ?: ($ticket->latest_comment_user_role ?? ''))),
                'at' => $ticket->latest_comment_at ?? null,
                'source' => 'Workflow',
            ];
        }

        if (!empty($ticket->latest_dev_comment) || !empty($ticket->latest_dev_comment_at)) {
            $events[] = [
                'note' => (string) ($ticket->latest_dev_comment ?: 'Developer note added.'),
                'by' => trim((string) (($ticket->latest_dev_comment_user_name ?? '') ?: ($ticket->latest_dev_comment_user_role ?? ''))),
                'at' => $ticket->latest_dev_comment_at ?? null,
                'source' => 'Developer',
            ];
        }

        if (!empty($ticket->latest_assignment_at) || !empty($ticket->latest_assignment_name)) {
            $assignmentStatus = Str::headline(str_replace('_', ' ', (string) ($ticket->latest_assignment_status ?? 'assigned')));
            $assignmentName = (string) ($ticket->latest_assignment_name ?? '');
            $events[] = [
                'note' => trim(($ticket->latest_assignment_notes ?: $assignmentStatus.($assignmentName ? ' to '.$assignmentName : ''))),
                'by' => (string) ($ticket->latest_assigned_by_name ?? ''),
                'at' => $ticket->latest_assignment_at ?? null,
                'source' => 'Assignment',
            ];
        }

        if (!$events) {
            return [
                'note' => $ticket->forward_notes ?: 'Ticket created.',
                'by' => (string) ($ticket->user_name ?? ''),
                'at' => $ticket->created_at ?? null,
                'source' => 'Ticket',
            ];
        }

        return collect($events)
            ->sortByDesc(function (array $event) {
                if (!$event['at']) {
                    return 0;
                }

                try {
                    return Carbon::parse($event['at'])->timestamp;
                } catch (\Throwable $exception) {
                    return 0;
                }
            })
            ->first();
    }

    public static function createTicket(array $data): object
    {
        $hasTicketScopeTypeColumn = self::ensureTicketScopeTypeColumn();
        $hasImportantFlagColumn = self::ensureImportantFlagColumn();
        $now =View::shared('get_nowtime');
        $deptCode = $data['deptcode'] ?? self::deptCode();
        $departmentName = $deptCode === 'ALL' ? 'All Departments' : self::departmentName($deptCode);
        $deptCodeForSave = $deptCode === 'ALL' ? null : $deptCode;
        $creatorRole = $data['created_by_role'] ?? self::role();
        $forwardToNic = in_array($creatorRole, [self::ROLE_STATE_ADMIN, self::ROLE_NIC_ADMIN], true);
        $initialStatus = self::TICKET_IN_PROGRESS;
        $initialRole = $forwardToNic ? 'nicadmin' : 'stateadmin';
        $initialComment = $forwardToNic
            ? 'Ticket created by '.self::roleLabel($creatorRole).' and auto forwarded to NIC Admin.'
            : 'Ticket created and auto forwarded to State Admin.';

        $insert = [
            'ticket_number' => self::nextTicketNumber($departmentName),
            'cams_userid' => self::userId(),
            'user_name' => self::userName(),
            'user_email' => self::userEmail(),
            'deptcode' => $deptCodeForSave,
            'department_name' => $departmentName,
            'subject' => $data['subject'],
            'description' => $data['description'],
            'category' => $data['category'] ?? 'Support',
            'priority' => $data['priority'],
            'financialyearcode' => $data['financialyearcode'] ?? null,
            'planmappingid' => $data['planmappingid'] ?? null,
            'institution' => $data['institution'] ?? null,
            'status' => $initialStatus,
            'forwarded_to_role' => $initialRole,
            'forwarded_at' => $now,
            'forward_notes' => $initialComment,
            'attachments' => empty($data['attachments']) ? null : json_encode($data['attachments']),
            'request_type' => $data['request_type'] ?? 'support',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if ($hasTicketScopeTypeColumn) {
            $insert['ticket_scope_type'] = $data['ticket_scope_type'] ?? self::TICKET_SCOPE_SPECIFIED;
        }

        if ($hasImportantFlagColumn) {
            $insert['importflag'] = !empty($data['watchlist_notify']) ? 'Y' : null;
        }

        $id = DB::table('audit.helpdesk_tickets')->insertGetId($insert);

        self::addTicketComment($id, $initialComment, false);

        return self::ticket($id);
    }

    public static function forwardTicket(object $ticket, string $action, array $data): object
    {
        $now = now('Asia/Kolkata');
        $isInternalComment = false;
        $hasForwardedToUseridColumn = self::ensureForwardedToUseridColumn();
        $updates = [
            'updated_at' => $now,
        ];
        $workflowUpdates = [
            'forwarded_at' => $now,
            'forward_notes' => $data['remarks'] ?? null,
	        ];
	        $remarks = trim((string) ($data['remarks'] ?? ''));
	        $comment = $remarks !== '' ? $remarks : null;
	        $markImportant = !empty($data['watchlist_notify']);
	        $setForwardedToUserid = static function (?string $userId) use (&$updates, $hasForwardedToUseridColumn): void {
	            if ($hasForwardedToUseridColumn) {
	                $updates['forwarded_to_userid'] = $userId;
	            }
	        };

	        if ($action === 'state_to_nic') {
	            $updates += $workflowUpdates + [
		                'status' => self::TICKET_PENDING_NIC,
		                'forwarded_to_role' => 'nicadmin',
		                'assigned_to_userid' => null,
		                'assigned_to_name' => null,
		            ];
		            $setForwardedToUserid(null);
		            $comment = self::ticketActionComment('Forwarded to NIC Admin.', $remarks);
		        } elseif ($action === 'state_to_user') {
		            $updates += $workflowUpdates + [
			                'status' => self::TICKET_RETURNED_USER,
			                'forwarded_to_role' => $creatorIsStateAdmin ? 'stateadmin' : 'user',
				                'assigned_to_userid' => null,
				                'assigned_to_name' => null,
		            ];
		            $setForwardedToUserid(null);
			            $comment = self::ticketActionComment('Returned to User.', $remarks);
		        } elseif ($action === 'user_to_state') {
		            $updates += $workflowUpdates + [
		                'status' => self::TICKET_IN_PROGRESS,
		                'forwarded_to_role' => 'stateadmin',
		                'assigned_to_userid' => null,
			                'assigned_to_name' => null,
			                'resolved_at' => null,
			            ];
			            $setForwardedToUserid(null);
			            $comment = self::ticketActionComment('Returned to State Admin by User.', $remarks);
			        } elseif ($action === 'nic_to_senior') {
	            $senior = self::developerById($data['senior_userid'] ?? null);
	            if (!$senior) {
	                throw new \InvalidArgumentException('Selected senior developer is invalid.');
	            }
		            $updates += $workflowUpdates + [
			                'status' => self::TICKET_PENDING_SENIOR,
		                'forwarded_to_role' => self::ROLE_SENIOR_DEVELOPER,
		                'assigned_to_userid' => (string) $senior->devuserid,
		                'assigned_to_name' => $senior->devename,
		            ];
		            $setForwardedToUserid((string) $senior->devuserid);
			            self::addTicketAssignment($ticket->id, $senior, 'senior_developer', $data['remarks'] ?? null, 'nic_to_senior', 'Forwarded to Senior Developer');
		            $comment = self::ticketActionComment('Forwarded to Senior Developer '.$senior->devename.'.', $remarks);
		        } elseif ($action === 'nic_to_developer') {
		            $developer = self::developerById($data['developer_userid'] ?? null);
		            if (!$developer) {
		                throw new \InvalidArgumentException('Selected developer is invalid.');
		            }
		            $updates += $workflowUpdates + [
			                'status' => self::TICKET_PENDING_DEVELOPER,
			                'forwarded_to_role' => self::ROLE_DEVELOPER,
			                'assigned_to_userid' => (string) $developer->devuserid,
			                'assigned_to_name' => $developer->devename,
		            ];
		            $setForwardedToUserid((string) $developer->devuserid);
			            self::addTicketAssignment($ticket->id, $developer, 'developer', $data['remarks'] ?? null, 'nic_to_developer', 'Assigned Directly to Developer');
		            $comment = self::ticketActionComment('Assigned directly to Developer '.$developer->devename.'.', $remarks);
		        } elseif ($action === 'senior_to_developer') {
		            $developer = self::developerById($data['developer_userid'] ?? null);
		            if (!$developer) {
		                throw new \InvalidArgumentException('Selected developer is invalid.');
		            }
		            $updates += $workflowUpdates + [
			                'status' => self::TICKET_PENDING_DEVELOPER,
		                'forwarded_to_role' => self::ROLE_DEVELOPER,
		                'assigned_to_userid' => (string) $developer->devuserid,
		                'assigned_to_name' => $developer->devename,
		            ];
		            $setForwardedToUserid((string) $developer->devuserid);
	            self::addTicketAssignment($ticket->id, $developer, 'developer', $data['remarks'] ?? null, 'senior_to_developer', 'Reassigned to Developer');
            $comment = self::ticketActionComment('Assigned to Developer '.$developer->devename.'.', $remarks);
	        } elseif ($action === 'developer_to_senior') {
	            $priorSenior = self::latestTicketSenior($ticket->id);
	            $seniorUserId = $priorSenior ? (string) $priorSenior->developer_userid : null;
	            $seniorName = $priorSenior->developer_name ?? null;

	            if (!$priorSenior) {
	                $autoSenior = self::pickAutoSeniorDeveloper();

	                if ($autoSenior) {
	                    $seniorUserId = (string) $autoSenior->devuserid;
	                    $seniorName = $autoSenior->devename;
		                    self::addTicketAssignment($ticket->id, $autoSenior, 'senior_developer', 'Routed to Senior Developer for review after a direct NIC Admin assignment.', 'developer_to_senior', 'Returned to Senior Developer');
	                }
	            }

	            if ($seniorUserId) {
	                $updates += $workflowUpdates + [
	                    'status' => self::TICKET_RETURNED_SENIOR,
	                    'forwarded_to_role' => self::ROLE_SENIOR_DEVELOPER,
		                    'assigned_to_userid' => $seniorUserId,
		                    'assigned_to_name' => $seniorName,
		                ];
		                $setForwardedToUserid($seniorUserId);
		                $comment = self::ticketActionComment('Returned to Senior Developer'.($seniorName ? ' '.$seniorName : '').'.', $remarks);
		            } else {
	                $updates += $workflowUpdates + [
	                    'status' => self::TICKET_RETURNED_NIC,
	                    'forwarded_to_role' => 'nicadmin',
		                    'assigned_to_userid' => null,
		                    'assigned_to_name' => null,
		                ];
		                $setForwardedToUserid(null);
		                $comment = self::ticketActionComment('Returned to NIC Admin.', $remarks);
		            }
        } elseif ($action === 'senior_to_nic') {
            $updates += $workflowUpdates + [
                'status' => self::TICKET_RETURNED_NIC,
                'forwarded_to_role' => 'nicadmin',
	                'assigned_to_userid' => null,
	                'assigned_to_name' => null,
	            ];
	            $setForwardedToUserid(null);
	            $comment = self::ticketActionComment('Returned to NIC Admin.', $remarks);
	        } elseif ($action === 'nic_to_state') {
	            $stateForwardStatus = self::ticketStatusFilterKey($ticket->status ?? null) === self::TICKET_RESOLVED
	                ? self::TICKET_RESOLVED
	                : self::TICKET_RETURNED_STATE;
	            $updates += $workflowUpdates + [
	                'status' => $stateForwardStatus,
	                'forwarded_to_role' => 'stateadmin',
		                'assigned_to_userid' => null,
		                'assigned_to_name' => null,
		            ];
	            $setForwardedToUserid(null);
	            $comment = self::ticketActionComment('Returned to State Admin.', $remarks);
	        } elseif ($action === 'state_close') {
	            $creatorIsStateAdmin = self::ticketCreatorIsStateAdmin($ticket);
		            $updates += $workflowUpdates + [
			                'status' => $creatorIsStateAdmin ? self::TICKET_CLOSED : self::TICKET_RESOLVED,
			                'forwarded_to_role' => $creatorIsStateAdmin ? 'stateadmin' : 'user',
			                'resolved_at' => $now,
				                'assigned_to_userid' => null,
				                'assigned_to_name' => null,
			            ];
			            $setForwardedToUserid(null);
			            $comment = self::ticketActionComment($creatorIsStateAdmin ? 'Closed ticket.' : 'Resolved and returned to User.', $remarks);
	        } elseif ($action === 'reopen') {
	            $updates += $workflowUpdates + [
		                'status' => self::TICKET_IN_PROGRESS,
		                'forwarded_to_role' => 'stateadmin',
		                'assigned_to_userid' => null,
			                'assigned_to_name' => null,
		                'resolved_at' => null,
		            ];
		            $setForwardedToUserid(null);
		            $comment = 'Ticket reopened and forwarded to State Admin by '.self::userName().'. Remarks: '.trim((string) ($data['remarks'] ?? ''));
	        } elseif ($action === 'developer_status_update') {
		            self::ensureDeveloperStatusColumn();
		            $hasTicketDeveloperMetaColumns = self::ensureTicketDeveloperMetaColumns();
		            $devStatus = (string) ($data['developer_status'] ?? '');
		            $updates['developer_status'] = array_key_exists($devStatus, self::developerStatusLabels()) ? $devStatus : null;
			            if ($hasTicketDeveloperMetaColumns) {
			                if (!empty($data['started_on'])) {
			                    $updates['developer_started_on'] = Carbon::parse($data['started_on'], 'Asia/Kolkata');
			                } elseif (empty($ticket->developer_started_on) && $updates['developer_status'] === self::DEV_STATUS_IN_PROGRESS) {
			                    $updates['developer_started_on'] = $now;
			                }
			            }
			            $comment = self::ticketActionComment('Developer status updated to '.self::developerStatusLabel($devStatus).'.', $remarks);
		            $isInternalComment = true;
	        } elseif ($action === 'status_update') {
	            $status = (string) ($data['ticket_status'] ?? $ticket->status);
	            $updates += [
	                'status' => $status,
	            ];

	            if (self::role() === self::ROLE_SENIOR_DEVELOPER && self::ensureDeveloperStatusColumn()) {
	                $updates['developer_status'] = match (self::ticketStatusFilterKey($status)) {
	                    self::TICKET_RESOLVED => self::DEV_STATUS_COMPLETED,
	                    self::TICKET_NEED_CLARIFICATION => self::DEV_STATUS_NEED_CLARIFICATION,
	                    default => self::DEV_STATUS_IN_PROGRESS,
	                };
	            }
	
	            if (in_array($status, [self::TICKET_CLOSED, self::TICKET_RESOLVED], true)) {
	                $updates['resolved_at'] = $now;
	            }

	            $comment = self::ticketActionComment('Status updated to '.self::ticketStatusLabel($status).'.', $remarks);
	        }

	        if ($markImportant && self::ensureImportantFlagColumn()) {
	            $updates['importflag'] = 'Y';

	            if (self::ensureVerifiedFlagColumn()) {
	                $updates['verifiedflag'] = null;
	            }
	        }

	        DB::table('audit.helpdesk_tickets')->where('id', $ticket->id)->update($updates);
        self::addTicketComment($ticket->id, $comment ?: 'Workflow updated.', $isInternalComment);

        return self::ticket($ticket->id);
    }

    private static function ticketActionComment(string $actionText, ?string $remarks = null): string
    {
        $remarks = trim((string) $remarks);

        return $remarks !== ''
            ? rtrim($actionText).' Remarks: '.$remarks
            : $actionText;
    }

    public static function createTask(array $data): object
    {
        $developer = self::developerById($data['developer_userid'] ?? null);
        if (!$developer) {
            throw new \InvalidArgumentException('Selected developer is invalid.');
        }

        $now = now('Asia/Kolkata');
        $assignedOn = Carbon::parse($data['assigned_on'], 'Asia/Kolkata');
        $expectedOn = Carbon::parse($data['expected_date_to_complete'], 'Asia/Kolkata');

        $insert = [
            'assigned_by_userid' => self::userId(),
            'assigned_by_name' => self::userName(),
            'developer_userid' => (string) $developer->devuserid,
            'developer_name' => $developer->devename,
            'process_assigned' => $data['process_assigned'],
            'task_type' => $data['task_type'],
            'is_testing_task' => false,
            'testing_task_description' => $data['description'] ?? null,
            'assigned_on' => $assignedOn,
            'expected_date_to_complete' => $expectedOn,
            'task_status_by_tester' => self::TASK_NOT_STARTED,
            'remarks_by_project_head' => 'Task created by NIC Admin and assigned to Developer '.$developer->devename.'.',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if (self::taskColumnExists('statusflag')) {
            $insert['statusflag'] = 'Y';
        }

        if (array_key_exists('module_category_id', $data) && self::taskColumnExists('module_category_id')) {
            $insert['module_category_id'] = $data['module_category_id'];
        }

        $id = DB::table(self::TASK_TABLE)->insertGetId($insert);

        self::addTaskHistory($id, 'nic_to_developer', 'NIC Admin', self::TASK_NOT_STARTED, 'Task assigned to Developer '.$developer->devename.'.');

        return self::task($id);
    }

    public static function forwardTask(object $task, string $action, array $data): object
    {
        $now = now('Asia/Kolkata');
        $updates = ['updated_at' => $now];
        $comment = $data['remarks'] ?? null;

	        if ($action === 'developer_resolve') {
	            $taskStatus = (string) ($data['task_status'] ?? self::TASK_RESOLVED);
	            if (!in_array($taskStatus, [self::TASK_IN_PROGRESS, self::TASK_NEED_CLARIFICATION, self::TASK_WRONGLY_ASSIGNED, self::TASK_RESOLVED], true)) {
	                throw new \InvalidArgumentException('Selected task status is invalid.');
	            }
	
	            $developerDefaultComment = match ($taskStatus) {
	                self::TASK_RESOLVED => 'Task forwarded to Senior Developer for review.',
	                self::TASK_NEED_CLARIFICATION => 'Developer marked the task as Need Clarification.',
	                self::TASK_WRONGLY_ASSIGNED => 'Task marked wrongly assigned and forwarded to Senior Developer.',
	                default => 'Developer updated the task status.',
	            };
            $startedOn = $task->started_on ? Carbon::parse($task->started_on, 'Asia/Kolkata') : $now;

            $updates += [
                'task_status_by_tester' => $taskStatus,
                'started_on' => $startedOn,
                'completed_on' => $taskStatus === self::TASK_RESOLVED ? $now : null,
                'remarks_by_developer' => $comment ?: $developerDefaultComment,
            ];
            $comment = $comment ?: $developerDefaultComment;

            if (isset($data['hosted_in'])) {
                self::applyTaskHostedInUpdates($updates, (string) $data['hosted_in']);
            }

            $resolvingDeveloper = self::developerById($task->developer_userid ?? null);
            if ($taskStatus === self::TASK_RESOLVED && $resolvingDeveloper && strtoupper(trim((string) ($resolvingDeveloper->senior_flag ?? ''))) === 'Y') {
                $updates += [
                    'verified_by' => self::userName(),
                    'verified_on' => $now,
                    'remarks_by_verifier' => 'Auto-confirmed — resolved directly by a Senior Developer.',
                ];
            }
	        } elseif ($action === 'senior_confirm') {
	            $updates += [
	                'verified_by' => self::userName(),
	                'verified_on' => $now,
	                'remarks_by_verifier' => $comment ?: 'Senior Developer confirmed resolution.',
	            ];
	            if (isset($data['hosted_in'])) {
	                self::applyTaskHostedInUpdates($updates, (string) $data['hosted_in']);
	            }
	            $comment = $comment ?: 'Senior Developer confirmed resolution.';
        } elseif ($action === 'senior_return') {
            $updates += [
                'task_status_by_tester' => self::TASK_IN_PROGRESS,
                'completed_on' => null,
                'remarks_by_verifier' => $comment ?: 'Senior Developer sent the task back to the Developer.',
            ];
            $comment = $comment ?: 'Senior Developer sent the task back to the Developer.';
	        } elseif ($action === 'senior_reassign') {
	            $developer = self::developerById($data['developer_userid'] ?? null);
	            if (!$developer) {
	                throw new \InvalidArgumentException('Selected developer is invalid.');
	            }

		            $updates += [
		                'task_status_by_tester' => self::TASK_NOT_STARTED,
	                'developer_userid' => (string) $developer->devuserid,
	                'developer_name' => $developer->devename,
                'completed_on' => null,
                'started_on' => null,
	                'remarks_by_verifier' => $comment ?: 'Senior Developer reassigned to Developer '.$developer->devename.'.',
	            ];
	            $comment = 'Reassigned to Developer '.$developer->devename.'.'.(trim((string) $comment) !== '' ? ' Remarks: '.trim((string) $comment) : '');
	        } elseif ($action === 'nic_close') {
	            $updates += [
	                'task_status_by_tester' => self::TASK_CLOSED,
	                'approved_by' => self::userName(),
	                'approved_on' => $now,
	                'remarks_by_project_head' => self::appendLine($task->remarks_by_project_head ?? null, $comment ?: 'Task closed by NIC Admin.'),
	            ];
	            if (isset($data['hosted_in'])) {
	                self::applyTaskHostedInUpdates($updates, (string) $data['hosted_in']);
	            }
	            $comment = $comment ?: 'Task closed by NIC Admin.';
        } elseif ($action === 'reopen') {
            $updates += [
                'task_status_by_tester' => self::TASK_RESOLVED,
                'verified_by' => null,
                'verified_on' => null,
                'approved_by' => null,
                'approved_on' => null,
                'remarks_by_verifier' => $comment ?: 'Task reopened by NIC Admin for Senior Developer review.',
            ];
            $comment = $comment ?: 'Task reopened by NIC Admin for Senior Developer review.';
        }

	        DB::table(self::TASK_TABLE)->where('id', $task->id)->update($updates);
	        self::addTaskHistory($task->id, $action, self::roleLabel(), $updates['task_status_by_tester'] ?? (string) $task->task_status_by_tester, $comment);
	        self::addTaskHostedHistory($task->id, $data['hosted_in'] ?? null, $updates['task_status_by_tester'] ?? (string) $task->task_status_by_tester, $comment);

	        return self::task($task->id);
	    }

    public static function ticketDetailsForCurrentRole(array $filters = [], bool $dashboardMode = false): Collection
    {
        $role = self::role();
        self::ensureForwardedToUseridColumn();
        $financialYears = DB::table('audit.mst_financialyear')
            ->select('financialyearcode', DB::raw('MAX(financialyear) as financialyear'))
            ->groupBy('financialyearcode');
        $planMappings = DB::table('audit.auditplanmapping')
            ->select(
                'planmappingid',
                DB::raw('MAX(planname) as planname'),
                DB::raw('MAX(auditquartercode) as auditquartercode')
            )
            ->groupBy('planmappingid');
        $auditQuarters = DB::table('audit.mst_auditquarter')
            ->select('auditquartercode', DB::raw('MAX(auditquarter) as auditquarter'))
            ->groupBy('auditquartercode');
	        $latestAssignments = DB::table('audit.helpdesk_ticket_assignments as hta')
	            ->select(
	                'hta.ticket_id',
                'hta.developer_userid as latest_assignment_userid',
                'hta.developer_name as latest_assignment_name',
                'hta.assigned_by_userid as latest_assigned_by_userid',
                'hta.assigned_by_name as latest_assigned_by_name',
                'hta.status as latest_assignment_status',
                'hta.notes as latest_assignment_notes',
                'hta.assigned_at as latest_assignment_at'
            )
            ->where(function ($query) {
                $query->whereNull('hta.status')->orWhere('hta.status', '!=', 'watchlist');
            })
            ->whereRaw("hta.id = (
                SELECT MAX(hta_latest.id)
                FROM audit.helpdesk_ticket_assignments as hta_latest
                WHERE hta_latest.ticket_id = hta.ticket_id
                AND (hta_latest.status IS NULL OR hta_latest.status != 'watchlist')
            )");
        $latestComments = DB::table('audit.helpdesk_ticket_comments as htc')
            ->select(
                'htc.ticket_id',
                'htc.user_name as latest_comment_user_name',
                'htc.user_role as latest_comment_user_role',
                'htc.comment as latest_comment',
                'htc.created_at as latest_comment_at'
            )
            ->whereRaw('htc.id = (
                SELECT MAX(htc_latest.id)
                FROM audit.helpdesk_ticket_comments as htc_latest
                WHERE htc_latest.ticket_id = htc.ticket_id
            )');
        $latestDevComments = DB::table('audit.helpdesk_ticket_comments_dev as htdc')
            ->select(
                'htdc.ticket_id',
                'htdc.user_name as latest_dev_comment_user_name',
                'htdc.user_role as latest_dev_comment_user_role',
                'htdc.comment as latest_dev_comment',
                'htdc.created_at as latest_dev_comment_at'
            )
            ->whereRaw('htdc.id = (
                SELECT MAX(htdc_latest.id)
                FROM audit.helpdesk_ticket_comments_dev as htdc_latest
                WHERE htdc_latest.ticket_id = htdc.ticket_id
            )');
        $query = DB::table('audit.helpdesk_tickets as t')
            ->leftJoinSub($financialYears, 'fy', 'fy.financialyearcode', '=', 't.financialyearcode')
            ->leftJoinSub($planMappings, 'apm', 'apm.planmappingid', '=', 't.planmappingid')
            ->leftJoinSub($auditQuarters, 'aq', 'aq.auditquartercode', '=', 'apm.auditquartercode')
            ->leftJoinSub($latestAssignments, 'latest_assignment', 'latest_assignment.ticket_id', '=', 't.id')
            ->leftJoinSub($latestComments, 'latest_comment', 'latest_comment.ticket_id', '=', 't.id')
            ->leftJoinSub($latestDevComments, 'latest_dev_comment', 'latest_dev_comment.ticket_id', '=', 't.id')
            ->leftJoin('audit.dev_userdetails as assigned_dev', function ($join) {
                $join->whereRaw('assigned_dev.devuserid::text = t.assigned_to_userid::text');
            })
            ->leftJoin('audit.dev_userdetails as forwarded_dev', function ($join) {
                $join->whereRaw('forwarded_dev.devuserid::text = t.forwarded_to_userid::text');
            })
            ->leftJoin('audit.dev_userdetails as latest_assignment_dev', function ($join) {
                $join->whereRaw('latest_assignment_dev.devuserid::text = latest_assignment.latest_assignment_userid::text');
            })
            ->select(
                't.*',
                'fy.financialyear',
                'apm.planname',
                'aq.auditquarter',
                'aq.auditquartercode',
                'assigned_dev.devename as assigned_user_name',
                'forwarded_dev.devename as forwarded_user_name',
                'latest_assignment_dev.devename as latest_assignment_dev_name',
                'latest_assignment.latest_assignment_userid',
                'latest_assignment.latest_assignment_name',
                'latest_assignment.latest_assigned_by_userid',
                'latest_assignment.latest_assigned_by_name',
                'latest_assignment.latest_assignment_status',
                'latest_assignment.latest_assignment_notes',
                'latest_assignment.latest_assignment_at',
                'latest_comment.latest_comment_user_name',
                'latest_comment.latest_comment_user_role',
                'latest_comment.latest_comment',
	                'latest_comment.latest_comment_at',
	                'latest_dev_comment.latest_dev_comment_user_name',
	                'latest_dev_comment.latest_dev_comment_user_role',
	                'latest_dev_comment.latest_dev_comment',
	                'latest_dev_comment.latest_dev_comment_at',
	                self::ticketCreatorIsStateAdminSelectSql(),
	                DB::raw("EXISTS (
	                    SELECT 1
	                    FROM audit.helpdesk_ticket_comments as htc_reopen
	                    WHERE htc_reopen.ticket_id = t.id
	                    AND LOWER(COALESCE(htc_reopen.comment, '')) LIKE '%ticket reopened%'
	                ) as is_reopened")
	            )
            ->orderByDesc('t.created_at')
            ->orderByDesc('t.id');

        if ($role === self::ROLE_USER) {
            $query->where('t.cams_userid', self::userId());
        } elseif ($dashboardMode && $role === self::ROLE_STATE_ADMIN) {
            // State admin dashboard is an overall view across the existing ticket flow.
        } elseif ($dashboardMode && $role === self::ROLE_NIC_ADMIN) {
            self::applyNicAdminTicketScope($query);
	        } elseif ($role === self::ROLE_STATE_ADMIN) {
	            $currentUserId = self::userId();
	            $query->where(function ($stateQuery) use ($currentUserId) {
	                $stateQuery->whereIn('t.forwarded_to_role', self::stateForwardedRoleValues())
	                    ->orWhereNull('t.forwarded_to_role')
	                    ->orWhere(function ($historyBuilder) {
	                        $historyBuilder->where('t.forwarded_to_role', 'nicadmin')
	                            ->whereExists(function ($commentQuery) {
	                                $commentQuery->select(DB::raw(1))
	                                    ->from('audit.helpdesk_ticket_comments as htc_scope')
	                                    ->whereColumn('htc_scope.ticket_id', 't.id')
	                                    ->where(function ($historyQuery) {
	                                        $historyQuery->where('htc_scope.comment', 'like', 'Ticket forwarded to NIC Admin.%')
	                                            ->orWhere('htc_scope.comment', 'Ticket created and forwarded NIC Admin.')
	                                            ->orWhere('htc_scope.comment', 'like', '%auto forwarded to State Admin%');
	                                    });
	                            });
	                    })
	                    ->orWhereExists(function ($stateHistoryQuery) {
	                        $stateHistoryQuery->select(DB::raw(1))
	                            ->from('audit.helpdesk_ticket_comments as htc_state_touched')
	                            ->whereColumn('htc_state_touched.ticket_id', 't.id')
	                            ->where('htc_state_touched.user_role', 'like', '%State Admin%');
	                    });

	                if ($currentUserId) {
	                    $stateQuery->orWhere('t.cams_userid', $currentUserId);
	                }
	            });
	        } elseif ($role === self::ROLE_NIC_ADMIN) {
		            self::applyNicAdminTicketScope($query);
        } elseif ($dashboardMode && in_array($role, [self::ROLE_SENIOR_DEVELOPER, self::ROLE_DEVELOPER], true)) {
            $query->where(function ($developerQuery) {
                $developerQuery->whereRaw('t.assigned_to_userid::text = ?', [self::userId()])
                    ->orWhereExists(function ($assignmentQuery) {
                        $assignmentQuery->select(DB::raw(1))
                            ->from('audit.helpdesk_ticket_assignments as hta_scope')
                            ->whereColumn('hta_scope.ticket_id', 't.id')
                            ->whereRaw('hta_scope.developer_userid::text = ?', [self::userId()])
                            ->where(function ($statusQuery) {
                                $statusQuery->whereNull('hta_scope.status')
                                    ->orWhere('hta_scope.status', '!=', 'watchlist');
                            });
                    });
            });
	        } elseif ($role === self::ROLE_SENIOR_DEVELOPER) {
	            // Senior Developers can watch every ticket, including ones NIC Admin
	            // assigned directly to a Developer without routing through a senior.
	        } elseif ($role === self::ROLE_DEVELOPER) {
	            $developerUserId = self::developerUserId();
	            $query->where(function ($developerQuery) use ($developerUserId) {
	                $developerQuery->whereRaw('t.assigned_to_userid::text = ?', [$developerUserId])
	                    ->orWhereRaw('t.forwarded_to_userid::text = ?', [$developerUserId])
	                    ->orWhereExists(function ($assignmentQuery) use ($developerUserId) {
	                        $assignmentQuery->select(DB::raw(1))
	                            ->from('audit.helpdesk_ticket_assignments as hta_role_scope')
	                            ->whereColumn('hta_role_scope.ticket_id', 't.id')
	                            ->whereRaw('hta_role_scope.developer_userid::text = ?', [$developerUserId])
	                            ->where(function ($statusQuery) {
	                                $statusQuery->whereNull('hta_role_scope.status')
	                                    ->orWhere('hta_role_scope.status', '!=', 'watchlist');
	                            });
	                    });
	            });
	        }

	        if (($filters['ticket_scope'] ?? '') === 'on_me') {
		            if ($role === self::ROLE_STATE_ADMIN) {
		                $query->where(function ($scopeQuery) {
		                    $scopeQuery->whereIn('t.forwarded_to_role', self::stateForwardedRoleValues())
		                        ->orWhereNull('t.forwarded_to_role');
			                });
		            } elseif ($role === self::ROLE_NIC_ADMIN) {
		                $query->whereIn('t.forwarded_to_role', ['nicadmin', 'nic_admin', 'nic_admn', self::ROLE_NIC_ADMIN]);
	            } elseif (in_array($role, [self::ROLE_SENIOR_DEVELOPER, self::ROLE_DEVELOPER], true)) {
	                $developerUserId = self::developerUserId();
	                $query->whereRaw('t.assigned_to_userid::text = ?', [$developerUserId]);
	            } else {
	                $query->where('t.forwarded_to_role', 'user')
			                    ->where('t.cams_userid', self::userId());
		            }
		        } elseif ($role === self::ROLE_NIC_ADMIN && ($filters['ticket_scope'] ?? '') === 'developer_side') {
		            $query->where(function ($developerSideQuery) {
		                $developerSideQuery->whereIn('t.forwarded_to_role', [
		                    self::ROLE_SENIOR_DEVELOPER,
		                    self::ROLE_DEVELOPER,
		                    'developer',
		                    'layer_lead',
		                    'tester',
		                ])
		                    ->orWhereIn(DB::raw("LOWER(BTRIM(REPLACE(COALESCE(t.forwarded_to_role::text, ''), '_', ' ')))"), [
		                        'senior developer',
		                        'developer',
		                        'layer lead',
		                        'tester',
		                    ]);
		            })->whereNotIn(DB::raw('LOWER(COALESCE(t.status::text, \'\'))'), [
		                self::TICKET_CLOSED,
		                self::TICKET_RESOLVED,
		                'closed',
		                'resolved',
		            ]);
		        }

	        if ($role === self::ROLE_STATE_ADMIN && ($filters['ticket_scope'] ?? '') === 'returned_from_nic') {
	            $query->where(function ($returnedFromNicQuery) {
	                $returnedFromNicQuery->where('t.status', self::TICKET_RETURNED_STATE)
	                    ->orWhereRaw('LOWER(BTRIM(COALESCE(t.status::text, \'\'))) = ?', [self::TICKET_RETURNED_STATE]);
	            });
	        }

		        if ($role === self::ROLE_STATE_ADMIN && !empty($filters['created_user'])) {
		            $createdUserId = (string) $filters['created_user'];
	            $createdUserName = DB::table('audit.deptuserdetails')
	                ->where('deptuserid', $createdUserId)
	                ->value('username');

	            $query->where(function ($createdQuery) use ($createdUserId, $createdUserName) {
	                $createdQuery->where('t.cams_userid', $createdUserId);

	                if ($createdUserName) {
	                    $createdQuery->orWhereRaw('LOWER(t.user_name) = ?', [strtolower((string) $createdUserName)]);
	                }
	            });
	        }

	        if (!empty($filters['priority'])) {
	            $query->whereRaw('LOWER(t.priority) = ?', [strtolower((string) $filters['priority'])]);
        }

        if (in_array($role, [self::ROLE_NIC_ADMIN, self::ROLE_STATE_ADMIN], true) && ($filters['ticket_scope'] ?? '') === 'important' && self::ensureImportantFlagColumn()) {
            $query->where('t.importflag', 'Y');
        }

	        if (!empty($filters['status'])) {
	            $statusFilter = strtolower(trim((string) $filters['status']));

	            if ($statusFilter === self::TICKET_IN_PROGRESS) {
	                $closedStatuses = [
	                    self::TICKET_CLOSED,
	                    self::TICKET_RESOLVED,
	                    'closed',
	                    'resolved',
	                    'rejected',
	                    'cancelled',
	                ];
	                $returnedStatuses = [
	                    self::TICKET_RETURNED_SENIOR,
	                    self::TICKET_RETURNED_NIC,
	                    self::TICKET_RETURNED_STATE,
	                    self::TICKET_RETURNED_USER,
	                    self::TICKET_NEED_CLARIFICATION,
	                ];

	                $query->where(function ($statusQuery) use ($closedStatuses, $returnedStatuses) {
	                    $statusQuery->whereNull('t.status')
	                        ->orWhere(function ($openQuery) use ($closedStatuses, $returnedStatuses) {
	                            $openQuery->whereRaw('LOWER(t.status::text) NOT IN ('.implode(',', array_fill(0, count($closedStatuses), '?')).')', $closedStatuses)
	                                ->whereRaw('LOWER(t.status::text) NOT IN ('.implode(',', array_fill(0, count($returnedStatuses), '?')).')', $returnedStatuses)
	                                ->whereRaw("LOWER(t.status::text) NOT LIKE '%returned%'")
	                                ->whereRaw("LOWER(t.status::text) NOT LIKE '%clarification%'");
	                        });
	                });
	            } elseif ($statusFilter === self::TICKET_NEED_CLARIFICATION) {
	                $returnedStatuses = [
	                    self::TICKET_RETURNED_SENIOR,
	                    self::TICKET_RETURNED_NIC,
	                    self::TICKET_RETURNED_STATE,
	                    self::TICKET_RETURNED_USER,
	                    self::TICKET_NEED_CLARIFICATION,
	                ];

	                $query->where(function ($statusQuery) use ($returnedStatuses) {
	                    $statusQuery->whereRaw('LOWER(t.status::text) IN ('.implode(',', array_fill(0, count($returnedStatuses), '?')).')', $returnedStatuses)
	                        ->orWhereRaw("LOWER(t.status::text) LIKE '%returned%'")
	                        ->orWhereRaw("LOWER(t.status::text) LIKE '%clarification%'");
	                });
	            } elseif ($statusFilter === self::TICKET_RESOLVED) {
	                $query->whereIn(DB::raw('LOWER(t.status::text)'), [
	                    self::TICKET_CLOSED,
	                    self::TICKET_RESOLVED,
	                    'closed',
	                    'resolved',
	                ]);
	            } else {
	                $query->whereRaw('LOWER(t.status) = ?', [$statusFilter]);
	            }
	        }

        if ($role === self::ROLE_NIC_ADMIN && !empty($filters['developer_userid'])) {
            $developerUserId = (string) $filters['developer_userid'];
            $developerName = DB::table('audit.dev_userdetails')
                ->where('devuserid', $developerUserId)
                ->value('devename');
            $developerName = strtolower(trim((string) $developerName));

            $query->whereIn('t.forwarded_to_role', [
                self::ROLE_SENIOR_DEVELOPER,
                self::ROLE_DEVELOPER,
                'developer',
                'layer_lead',
                'tester',
            ])->where(function ($developerFilterQuery) use ($developerUserId, $developerName) {
	                $developerFilterQuery->whereRaw('t.assigned_to_userid::text = ?', [$developerUserId]);

                if ($developerName !== '') {
                    $developerFilterQuery->orWhereRaw('LOWER(BTRIM(t.assigned_to_name::text)) = ?', [$developerName]);
                }

                $developerFilterQuery->orWhere(function ($fallbackQuery) use ($developerUserId, $developerName) {
                    $fallbackQuery->where(function ($emptyCurrentQuery) {
                        $emptyCurrentQuery->whereNull('t.assigned_to_userid')
                            ->orWhereRaw("BTRIM(COALESCE(t.assigned_to_userid::text, '')) = ''");
                    })->where(function ($emptyNameQuery) {
                        $emptyNameQuery->whereNull('t.assigned_to_name')
                            ->orWhereRaw("BTRIM(COALESCE(t.assigned_to_name::text, '')) = ''");
                    })->where(function ($latestQuery) use ($developerUserId, $developerName) {
                        $latestQuery->whereRaw('latest_assignment.latest_assignment_userid::text = ?', [$developerUserId]);

                        if ($developerName !== '') {
                            $latestQuery->orWhereRaw('LOWER(BTRIM(latest_assignment.latest_assignment_name::text)) = ?', [$developerName]);
                        }
                    });
                });
            });
        }

        if (self::canViewDeveloperStatus($role) && !empty($filters['developer_status']) && self::ensureDeveloperStatusColumn()) {
            $developerStatus = (string) $filters['developer_status'];

            if ($developerStatus === self::DEV_STATUS_IN_PROGRESS) {
                $query->where(function ($statusQuery) use ($developerStatus) {
                    $statusQuery->where('t.developer_status', $developerStatus)
                        ->orWhereNull('t.developer_status')
                        ->orWhereRaw("BTRIM(COALESCE(t.developer_status::text, '')) = ''");
                });
            } else {
                $query->where('t.developer_status', $developerStatus);
            }
        }

        if (!empty($filters['search'])) {
            $search = '%'.strtolower(trim((string) $filters['search'])).'%';
            $query->whereRaw(
                "LOWER(CONCAT_WS(' ', t.ticket_number, t.user_name, t.department_name, t.category, t.priority, t.request_type, t.subject, t.description, t.forwarded_to_role)) LIKE ?",
                [$search]
            );
        }

        return $query->get()->unique('id')->values();
    }

    private static function tasksForCurrentRoleQuery(bool $dashboardMode = false, array $filters = [])
    {
        $role = self::role();
        $developerUserId = self::developerUserId();
        $developer = self::developerUser();
        $developerName = self::normalizePersonName(($developer->devename ?? null) ?: self::userName());
        $hasTaskHistoryTable = self::ensureTaskHistoryTable();
        $query = DB::table(self::TASK_TABLE.' as dt')
            ->orderByRaw('COALESCE(dt.assigned_on, dt.created_at) DESC')
            ->orderByDesc('dt.id');

        if ($dashboardMode && in_array($role, [self::ROLE_NIC_ADMIN, self::ROLE_SENIOR_DEVELOPER], true)) {
            // NIC Admin and Senior Developer get a full watch list of every task.
        } elseif ($role === self::ROLE_NIC_ADMIN) {
            $query->where(function ($nicQuery) {
                $nicQuery->where('task_status_by_tester', self::TASK_CLOSED)
                    ->orWhere(function ($resolvedQuery) {
                        $resolvedQuery->where('task_status_by_tester', self::TASK_RESOLVED)
                            ->whereNotNull('verified_by');
                    });
            });
        } elseif ($dashboardMode && $role === self::ROLE_DEVELOPER) {
            $query->where(function ($taskQuery) use ($developerUserId, $developerName, $hasTaskHistoryTable) {
                $taskQuery->where(function ($developerQuery) use ($developerUserId, $developerName) {
                        self::applyLoggedInDeveloperMatch($developerQuery, $developerUserId, $developerName);
                    })
                    ->orWhere('assigned_by_userid', self::userId());

                if ($hasTaskHistoryTable) {
                    $taskQuery->orWhereExists(function ($historyQuery) use ($developerUserId) {
                        $actorIds = array_values(array_unique(array_filter([
                            (string) $developerUserId,
                            (string) self::userId(),
                        ], fn ($value) => $value !== '')));

                        $historyQuery->select(DB::raw(1))
                            ->from(self::TASK_HISTORY_TABLE.' as tmh_scope')
                            ->whereColumn('tmh_scope.task_id', 'dt.id')
                            ->whereIn('tmh_scope.performed_by_userid', $actorIds);
                    });
                }
            });
        } elseif ($role === self::ROLE_SENIOR_DEVELOPER) {
            $query->whereIn('task_status_by_tester', [self::TASK_RESOLVED, self::TASK_WRONGLY_ASSIGNED])
                ->whereNull('verified_by');
        } elseif ($role === self::ROLE_DEVELOPER) {
            $query->where(function ($statusQuery) {
                $statusQuery->whereNull('task_status_by_tester')
                    ->orWhereRaw("BTRIM(COALESCE(task_status_by_tester::text, '')) = ''")
                    ->orWhereIn('task_status_by_tester', [
                        self::TASK_NOT_STARTED,
                        self::TASK_IN_PROGRESS,
                        self::TASK_NEED_CLARIFICATION,
                    ]);
            })->where(function ($developerQuery) use ($developerUserId, $developerName) {
                self::applyLoggedInDeveloperMatch($developerQuery, $developerUserId, $developerName);
            });
        } else {
            $query->whereRaw('1 = 0');
        }

	        if (!empty($filters['status'])) {
	            $query->where('task_status_by_tester', $filters['status']);
	        }
	
	        if (($filters['task_scope'] ?? '') === 'currently_me') {
	            self::applyTaskCurrentlyMeFilter($query, $role, $developerUserId);
	        }
	
	        if (!empty($filters['task_type'])) {
	            $query->whereRaw('LOWER(task_type) = ?', [strtolower((string) $filters['task_type'])]);
	        }

        if (in_array($role, [self::ROLE_NIC_ADMIN, self::ROLE_SENIOR_DEVELOPER], true) && !empty($filters['developer_userid'])) {
            $query->where('developer_userid', (string) $filters['developer_userid']);
        }

        if (!empty($filters['search'])) {
            $search = '%'.strtolower(trim((string) $filters['search'])).'%';
            $query->whereRaw(
                "LOWER(CONCAT_WS(' ', process_assigned, developer_name, assigned_by_name, testing_task_description)) LIKE ?",
                [$search]
            );
        }

	        return $query;
	    }
	
	    private static function applyTaskCurrentlyMeFilter($query, string $role, ?string $developerUserId): void
	    {
        $developer = self::developerUser();
        $developerName = self::normalizePersonName(($developer->devename ?? null) ?: self::userName());
	
	        if ($role === self::ROLE_DEVELOPER) {
	            $query->where(function ($statusQuery) {
	                $statusQuery->whereNull('task_status_by_tester')
	                    ->orWhereRaw("BTRIM(COALESCE(task_status_by_tester::text, '')) = ''")
	                    ->orWhereNotIn('task_status_by_tester', [
	                        self::TASK_RESOLVED,
	                        self::TASK_WRONGLY_ASSIGNED,
	                        self::TASK_CLOSED,
	                    ]);
	            })
                ->where(function ($developerQuery) use ($developerUserId, $developerName) {
                    self::applyLoggedInDeveloperMatch($developerQuery, $developerUserId, $developerName);
                });
	
	            return;
	        }
	
	        if ($role === self::ROLE_SENIOR_DEVELOPER) {
	            $query->where(function ($seniorQuery) use ($developerUserId, $developerName) {
	                $seniorQuery->where(function ($resolvedQuery) use ($developerUserId, $developerName) {
	                    $resolvedQuery->where('task_status_by_tester', self::TASK_RESOLVED)
	                        ->whereNull('verified_by')
	                        ->where(function ($assignedSeniorQuery) use ($developerUserId, $developerName) {
	                            self::applyLoggedInSeniorMatch($assignedSeniorQuery, $developerUserId, $developerName);
	                        });
	                })->orWhere(function ($wronglyAssignedQuery) use ($developerUserId, $developerName) {
	                    $wronglyAssignedQuery->where('task_status_by_tester', self::TASK_WRONGLY_ASSIGNED)
	                        ->where(function ($assignedSeniorQuery) use ($developerUserId, $developerName) {
	                            self::applyLoggedInSeniorMatch($assignedSeniorQuery, $developerUserId, $developerName);
	                        });
	                });
	            });
	
	            return;
	        }
	
	        if ($role === self::ROLE_NIC_ADMIN) {
	            $query->where('task_status_by_tester', self::TASK_RESOLVED)
	                ->whereNotNull('verified_by');
	
	            return;
	        }
	
	        $query->whereRaw('1 = 0');
	    }
	
    private static function applyLoggedInSeniorMatch($query, ?string $developerUserId, string $developerName): void
    {
        $query->where('senior_userid', (string) $developerUserId);
	
	        if ($developerName !== '') {
	            $query->orWhereRaw('LOWER(BTRIM(COALESCE(senior_name::text, \'\'))) = ?', [$developerName]);
	        }
	
	        $query->orWhere(function ($genericSeniorQuery) {
	            $genericSeniorQuery->where(function ($emptyUserQuery) {
	                $emptyUserQuery->whereNull('senior_userid')
	                    ->orWhereRaw("BTRIM(COALESCE(senior_userid::text, '')) = ''");
	            })->where(function ($emptyNameQuery) {
	                $emptyNameQuery->whereNull('senior_name')
	                    ->orWhereRaw("BTRIM(COALESCE(senior_name::text, '')) = ''");
            });
        });
    }

    private static function applyLoggedInDeveloperMatch($query, ?string $developerUserId, string $developerName): void
    {
        $developerUserId = trim((string) $developerUserId);

        if ($developerUserId !== '') {
            $query->where('developer_userid', $developerUserId);
        } else {
            $query->whereRaw('1 = 0');
        }

        if ($developerName !== '') {
            $query->orWhereRaw('LOWER(BTRIM(COALESCE(developer_name::text, \'\'))) = ?', [$developerName]);
        }
    }

    public static function tasksForCurrentRole(bool $dashboardMode = false, array $filters = [], ?int $limit = 100): Collection
    {
        $query = self::tasksForCurrentRoleQuery($dashboardMode, $filters);

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public static function tasksForCurrentRolePaginated(bool $dashboardMode = false, int $perPage = 15, array $filters = [], ?int $page = null): \Illuminate\Pagination\LengthAwarePaginator
    {
        return self::tasksForCurrentRoleQuery($dashboardMode, $filters)->paginate($perPage, ['*'], 'page', $page)->withQueryString();
    }

    public static function ticket(int $id): object
    {
        self::ensureForwardedToUseridColumn();
        $financialYears = DB::table('audit.mst_financialyear')
            ->select('financialyearcode', DB::raw('MAX(financialyear) as financialyear'))
            ->groupBy('financialyearcode');
        $planMappings = DB::table('audit.auditplanmapping')
            ->select(
                'planmappingid',
                DB::raw('MAX(planname) as planname'),
                DB::raw('MAX(auditquartercode) as auditquartercode')
            )
            ->groupBy('planmappingid');
        $auditQuarters = DB::table('audit.mst_auditquarter')
            ->select('auditquartercode', DB::raw('MAX(auditquarter) as auditquarter'))
            ->groupBy('auditquartercode');
        $latestAssignments = DB::table('audit.helpdesk_ticket_assignments as hta')
            ->select(
                'hta.ticket_id',
                'hta.developer_userid as latest_assignment_userid',
                'hta.developer_name as latest_assignment_name',
                'hta.assigned_by_userid as latest_assigned_by_userid',
                'hta.assigned_by_name as latest_assigned_by_name',
                'hta.status as latest_assignment_status',
                'hta.notes as latest_assignment_notes',
                'hta.assigned_at as latest_assignment_at'
            )
            ->where(function ($query) {
                $query->whereNull('hta.status')->orWhere('hta.status', '!=', 'watchlist');
            })
            ->whereRaw("hta.id = (
                SELECT MAX(hta_latest.id)
                FROM audit.helpdesk_ticket_assignments as hta_latest
	                WHERE hta_latest.ticket_id = hta.ticket_id
	                AND (hta_latest.status IS NULL OR hta_latest.status != 'watchlist')
	            )");
	        $latestComments = DB::table('audit.helpdesk_ticket_comments as htc')
	            ->select(
	                'htc.ticket_id',
	                'htc.user_name as latest_comment_user_name',
	                'htc.user_role as latest_comment_user_role',
	                'htc.comment as latest_comment',
	                'htc.created_at as latest_comment_at'
	            )
	            ->whereRaw('htc.id = (
	                SELECT MAX(htc_latest.id)
	                FROM audit.helpdesk_ticket_comments as htc_latest
	                WHERE htc_latest.ticket_id = htc.ticket_id
	            )');
	        $latestDevComments = DB::table('audit.helpdesk_ticket_comments_dev as htdc')
	            ->select(
                'htdc.ticket_id',
                'htdc.user_name as latest_dev_comment_user_name',
                'htdc.user_role as latest_dev_comment_user_role',
                'htdc.comment as latest_dev_comment',
                'htdc.created_at as latest_dev_comment_at'
            )
            ->whereRaw('htdc.id = (
                SELECT MAX(htdc_latest.id)
                FROM audit.helpdesk_ticket_comments_dev as htdc_latest
                WHERE htdc_latest.ticket_id = htdc.ticket_id
            )');

	        $ticket = DB::table('audit.helpdesk_tickets as t')
	            ->leftJoinSub($financialYears, 'fy', 'fy.financialyearcode', '=', 't.financialyearcode')
	            ->leftJoinSub($planMappings, 'apm', 'apm.planmappingid', '=', 't.planmappingid')
	            ->leftJoinSub($auditQuarters, 'aq', 'aq.auditquartercode', '=', 'apm.auditquartercode')
	            ->leftJoinSub($latestAssignments, 'latest_assignment', 'latest_assignment.ticket_id', '=', 't.id')
	            ->leftJoinSub($latestComments, 'latest_comment', 'latest_comment.ticket_id', '=', 't.id')
	            ->leftJoinSub($latestDevComments, 'latest_dev_comment', 'latest_dev_comment.ticket_id', '=', 't.id')
            ->leftJoin('audit.dev_userdetails as assigned_dev', function ($join) {
                $join->whereRaw('assigned_dev.devuserid::text = t.assigned_to_userid::text');
            })
            ->leftJoin('audit.dev_userdetails as forwarded_dev', function ($join) {
                $join->whereRaw('forwarded_dev.devuserid::text = t.forwarded_to_userid::text');
            })
            ->leftJoin('audit.dev_userdetails as latest_assignment_dev', function ($join) {
                $join->whereRaw('latest_assignment_dev.devuserid::text = latest_assignment.latest_assignment_userid::text');
            })
            ->select(
                't.*',
                'fy.financialyear',
                'apm.planname',
                'aq.auditquarter',
                'aq.auditquartercode',
                'assigned_dev.devename as assigned_user_name',
                'forwarded_dev.devename as forwarded_user_name',
                'latest_assignment_dev.devename as latest_assignment_dev_name',
                'latest_assignment.latest_assignment_userid',
                'latest_assignment.latest_assignment_name',
                'latest_assignment.latest_assigned_by_userid',
	                'latest_assignment.latest_assigned_by_name',
	                'latest_assignment.latest_assignment_status',
	                'latest_assignment.latest_assignment_notes',
		                'latest_assignment.latest_assignment_at',
		                'latest_comment.latest_comment_user_name',
		                'latest_comment.latest_comment_user_role',
		                'latest_comment.latest_comment',
		                'latest_comment.latest_comment_at',
		                'latest_dev_comment.latest_dev_comment_user_name',
	                'latest_dev_comment.latest_dev_comment_user_role',
	                'latest_dev_comment.latest_dev_comment',
	                'latest_dev_comment.latest_dev_comment_at',
	                self::ticketCreatorIsStateAdminSelectSql(),
	                DB::raw("EXISTS (
	                    SELECT 1
	                    FROM audit.helpdesk_ticket_comments as htc_reopen
	                    WHERE htc_reopen.ticket_id = t.id
	                    AND LOWER(COALESCE(htc_reopen.comment, '')) LIKE '%ticket reopened%'
	                ) as is_reopened")
	            )
	            ->where('t.id', $id)
	            ->first();

	        abort_unless($ticket, 404);

	        return $ticket;
	    }

	    public static function task(int $id): object
	    {
	        $task = DB::table(self::TASK_TABLE)->where('id', $id)->first();

	        abort_unless($task, 404);

	        return $task;
	    }

    public static function ticketComments(int $ticketId, ?string $viewerRole = null): Collection
    {
        $comments = DB::table('audit.helpdesk_ticket_comments')
            ->where('ticket_id', $ticketId)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
        $canViewInternalComments = self::canViewInternalTicketComments($viewerRole);
        $displayComments = $canViewInternalComments
            ? $comments
            : $comments
                ->filter(function ($comment) use ($viewerRole) {
                    $isPublic = !((bool) ($comment->is_internal ?? false)) || self::isPublicWorkflowTicketComment($comment);
                    $isSeniorAuthored = self::normalizedTicketStatus((string) ($comment->user_role ?? '')) === 'senior developer';

                    if ($isSeniorAuthored && $isPublic) {
                        return $viewerRole !== self::ROLE_USER;
                    }

                    return !self::isDeveloperTeamTicketComment($comment)
                        && !self::isDeveloperSideStatusTicketComment($comment)
                        && !self::isDeveloperAssignmentTicketComment($comment)
                        && $isPublic;
                })
                ->values();

        $developerCommentsRaw = DB::table('audit.helpdesk_ticket_comments_dev')
            ->where('ticket_id', $ticketId)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $assignments = DB::table('audit.helpdesk_ticket_assignments')
            ->where('ticket_id', $ticketId)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'watchlist');
            })
            ->orderBy('assigned_at')
            ->orderBy('id')
            ->get();

        $assignmentActorIds = $assignments
            ->pluck('assigned_by_userid')
            ->filter(fn ($value) => trim((string) $value) !== '')
            ->map(fn ($value) => (string) $value)
            ->unique()
            ->values();

        $developerRoles = $assignmentActorIds->isEmpty()
            ? collect()
            : DB::table('audit.dev_userdetails')
                ->whereIn('devuserid', $assignmentActorIds->all())
                ->get(['devuserid', 'senior_flag'])
                ->keyBy(fn ($developer) => (string) $developer->devuserid);

        $assignmentEvents = $canViewInternalComments
            ? $assignments
	            ->filter(function ($assignment) use ($displayComments, $developerCommentsRaw, $canViewInternalComments) {
	                if (trim((string) ($assignment->action_key ?? '')) !== '') {
	                    return true;
	                }

	                $developerName = strtolower(trim((string) ($assignment->developer_name ?? '')));
                $assignmentNotes = strtolower(preg_replace('/\s+/', ' ', trim((string) ($assignment->notes ?? ''))) ?: '');
                $status = self::normalizedTicketStatus($assignment->status ?? '');
                $roleNeedle = str_contains($status, 'senior') ? 'senior developer' : 'developer';
                $assignedAt = $assignment->assigned_at ?? $assignment->created_at ?? null;
                $assignedAtTimestamp = null;
                $storedTimelineComments = $displayComments->merge($developerCommentsRaw);

                if ($assignedAt) {
                    try {
                        $assignedAtTimestamp = Carbon::parse($assignedAt)->timestamp;
                    } catch (\Throwable $e) {
                        $assignedAtTimestamp = null;
                    }
                }

                return !$storedTimelineComments->contains(function ($comment) use ($developerName, $assignmentNotes, $roleNeedle, $assignedAtTimestamp) {
                    $text = strtolower(preg_replace('/\s+/', ' ', (string) ($comment->comment ?? '')) ?: '');

                    if (!str_contains($text, 'forward') && !str_contains($text, 'assign')) {
                        return false;
                    }

                    if (!str_contains($text, $roleNeedle) && ($developerName === '' || !str_contains($text, $developerName))) {
                        return false;
                    }

                    if ($assignedAtTimestamp === null || empty($comment->created_at)) {
                        return $assignmentNotes === '' || str_contains($text, $assignmentNotes);
                    }

                    try {
                        $sameTimeWindow = abs(Carbon::parse($comment->created_at)->timestamp - $assignedAtTimestamp) <= 300;

                        return $sameTimeWindow && ($assignmentNotes === '' || str_contains($text, $assignmentNotes));
                    } catch (\Throwable $e) {
                        return $assignmentNotes === '' || str_contains($text, $assignmentNotes);
                    }
                });
            })
            ->map(function ($assignment) use ($ticketId, $developerRoles) {
                $developerName = trim((string) ($assignment->developer_name ?? ''));
                $status = self::normalizedTicketStatus($assignment->status ?? '');
                $role = str_contains($status, 'senior') ? 'Senior Developer' : 'Developer';
                $target = $developerName !== '' ? $developerName : $role;
	                $action = trim((string) ($assignment->action_label ?? ''));
	                if ($action === '') {
	                    $action = str_contains($status, 'senior')
	                        ? 'Forwarded to Senior Developer'
	                        : 'Forwarded to Developer';
	                }
	                $notes = trim((string) (($assignment->action_remarks ?? null) ?: ($assignment->notes ?? '')));
                $actorId = (string) ($assignment->assigned_by_userid ?? '');
                $actorDeveloper = $developerRoles->get($actorId);
                $actorRole = $actorDeveloper
                    ? (strtoupper(trim((string) ($actorDeveloper->senior_flag ?? ''))) === 'Y' ? 'Senior Developer' : 'Developer')
                    : 'NIC Admin';

                return (object) [
                    'id' => 900000000 + (int) $assignment->id,
                    'ticket_id' => $ticketId,
                    'cams_userid' => $assignment->assigned_by_userid,
                    'user_name' => $assignment->assigned_by_name ?: 'NIC Admin',
                    'user_role' => $actorRole,
	                    'comment' => $action.' '.$target.'.'.($notes !== '' ? ' Remarks: '.$notes : ''),
	                    'is_internal' => false,
	                    'created_at' => ($assignment->action_at ?? null) ?: ($assignment->assigned_at ?? $assignment->created_at),
	                    'updated_at' => $assignment->updated_at ?? (($assignment->action_at ?? null) ?: $assignment->assigned_at),
	                ];
	            })
            : collect();

        $developerComments = $canViewInternalComments
            ? $developerCommentsRaw
            ->map(function ($comment) {
                $comment->id = 800000000 + (int) $comment->id;
                $comment->is_internal = false;

                return $comment;
            })
            : collect();

        $timelineComments = $displayComments
            ->merge($assignmentEvents)
            ->merge($developerComments)
            ->sortBy([
                ['created_at', 'asc'],
                ['id', 'asc'],
            ])
            ->values();

        return $timelineComments
            ->reverse()
            ->unique(fn ($comment) => self::ticketTimelineDuplicateKey($comment))
            ->reverse()
            ->values();
    }

    public static function canViewInternalTicketComments(?string $role = null): bool
    {
        return in_array($role ?? self::role(), [
            self::ROLE_NIC_ADMIN,
            self::ROLE_SENIOR_DEVELOPER,
            self::ROLE_DEVELOPER,
        ], true);
    }

    public static function canCreateInternalTicketComments(?string $role = null): bool
    {
        return in_array($role ?? self::role(), [
            self::ROLE_NIC_ADMIN,
            self::ROLE_SENIOR_DEVELOPER,
        ], true);
    }

    private static function isPublicWorkflowTicketComment(object $comment): bool
    {
        $text = trim((string) ($comment->comment ?? ''));

        if ($text === '') {
            return false;
        }

        $normalized = strtolower($text);

        if (preg_match('/^\[(?:forward_to_state|update_[a-z_]+_status|close|pending_[a-z_]+|returned_[a-z_]+|developer_forward_to_lead)\]/i', $text)) {
            return true;
        }

        if (preg_match('/^(?:ticket created|ticket reopened|forwarded to|assigned(?: directly)? to|returned to|closed and returned|resolved and returned|status updated to)\b/i', $text)) {
            return true;
        }

        return str_contains($normalized, '->') && str_contains($normalized, 'assigned to:');
    }

    private static function isDeveloperAssignmentTicketComment(object $comment): bool
    {
        $text = strtolower(preg_replace('/\s+/', ' ', trim((string) ($comment->comment ?? ''))) ?: '');

        if ($text === '') {
            return false;
        }

        return preg_match('/^\[(?:assign_developer|assign_developer_from_nic|developer_forward_to_lead|resolve_layer_to_nic|developer_return)\]/i', (string) ($comment->comment ?? '')) === 1
            || preg_match('/\b(?:forwarded to senior developer|forwarded to developer|assigned directly to developer|assigned to developer)\b/i', (string) ($comment->comment ?? '')) === 1;
    }

    private static function isDeveloperSideStatusTicketComment(object $comment): bool
    {
        $text = (string) ($comment->comment ?? '');

        return preg_match('/^\[(?:update_developer_status|update_senior_status|developer_return|resolve_layer_to_nic)\]/i', $text) === 1
            || preg_match('/\b(?:developer|senior developer)\s+updated\s+the\s+(?:ticket\s+)?status\b/i', $text) === 1;
    }

    private static function isDeveloperTeamTicketComment(object $comment): bool
    {
        $roleText = self::normalizedTicketStatus((string) ($comment->user_role ?? ''));

        return str_contains($roleText, 'developer')
            || str_contains($roleText, 'senior')
            || str_contains($roleText, 'layer lead')
            || str_contains($roleText, 'tech team');
    }

    private static function ticketTimelineDuplicateKey(object $comment): string
    {
        $text = strtolower(preg_replace('/\s+/', ' ', trim((string) ($comment->comment ?? ''))) ?: '');
        $createdAt = '';

        if (!empty($comment->created_at)) {
            try {
                $createdAt = Carbon::parse($comment->created_at)->format('Y-m-d H:i');
            } catch (\Throwable $e) {
                $createdAt = (string) $comment->created_at;
            }
        }

        return $createdAt.'|'.$text;
    }

	    public static function taskHistories(int $taskId): Collection
	    {
	        if (!self::ensureTaskHistoryTable()) {
	            return collect();
	        }

        return DB::table(self::TASK_HISTORY_TABLE)
            ->where('task_id', $taskId)
            ->orderBy('performed_at')
	            ->orderBy('id')
	            ->get();
	    }

	    public static function taskHistoriesWithHostedEvents(object $task, Collection $histories): Collection
	    {
	        $events = [
	            [
	                'action' => 'hosted_in_staging',
	                'stage' => 'Hosted In Staging',
	                'label' => 'Hosted in Staging',
	                'by' => data_get($task, 'hosted_in_staging_by'),
	                'on' => data_get($task, 'hosted_in_staging_on'),
	            ],
	            [
	                'action' => 'hosted_in_production',
	                'stage' => 'Hosted In Production',
	                'label' => 'Hosted in Production',
	                'by' => data_get($task, 'hosted_in_production_by'),
	                'on' => data_get($task, 'hosted_in_production_on'),
	            ],
	        ];

	        foreach ($events as $event) {
	            if (empty($event['by']) && empty($event['on'])) {
	                continue;
	            }

	            if ($histories->contains(fn ($history) => (string) ($history->action_key ?? '') === $event['action'])) {
	                continue;
	            }

	            $matchedHistory = self::matchingHostedHistory($histories, $event['by'], $event['on']);
	            $remarks = trim((string) ($matchedHistory->comment ?? ''));
	            $histories->push((object) [
	                'id' => PHP_INT_MAX,
	                'task_id' => $task->id,
	                'action_key' => $event['action'],
	                'stage' => $event['stage'],
	                'status' => (string) ($task->task_status_by_tester ?? ''),
	                'comment' => $event['label'].($remarks !== '' ? '. Remarks: '.$remarks : '.'),
	                'performed_by_userid' => null,
	                'performed_by_name' => $event['by'] ?: '-',
	                'performed_by_role' => $event['stage'],
	                'performed_at' => $event['on'] ?: ($matchedHistory->performed_at ?? now('Asia/Kolkata')),
	                'created_at' => $event['on'] ?: null,
	                'updated_at' => $event['on'] ?: null,
	            ]);
	        }

	        return $histories
	            ->sortBy(fn ($history) => sprintf('%s|%010d', (string) ($history->performed_at ?? ''), (int) ($history->id ?? 0)))
	            ->values();
	    }

	    private static function matchingHostedHistory(Collection $histories, mixed $hostedBy, mixed $hostedOn): ?object
	    {
	        if (!$hostedOn) {
	            return null;
	        }

	        try {
	            $hostedAt = Carbon::parse($hostedOn, 'Asia/Kolkata')->format('Y-m-d H:i');
	        } catch (\Throwable $exception) {
	            return null;
	        }

	        return $histories
	            ->filter(function ($history) use ($hostedBy, $hostedAt) {
	                if ($hostedBy && strcasecmp((string) ($history->performed_by_name ?? ''), (string) $hostedBy) !== 0) {
	                    return false;
	                }

	                try {
	                    return Carbon::parse($history->performed_at, 'Asia/Kolkata')->format('Y-m-d H:i') === $hostedAt;
	                } catch (\Throwable $exception) {
	                    return false;
	                }
	            })
	            ->last();
	    }

    public static function taskWasHandledBy(int $taskId, ?string $developerUserId): bool
    {
        $actorIds = array_values(array_unique(array_filter([
            (string) $developerUserId,
            (string) self::userId(),
        ], fn ($value) => $value !== '')));

        if (!$actorIds) {
            return false;
        }

        if (!self::ensureTaskHistoryTable()) {
            return false;
        }

        return DB::table(self::TASK_HISTORY_TABLE)
            ->where('task_id', $taskId)
            ->whereIn('performed_by_userid', $actorIds)
            ->exists();
    }

    public static function activeSeniorDevelopers(): Collection
    {
        return DB::table('audit.dev_userdetails')
            ->where('statusflag', 'Y')
            ->where('senior_flag', 'Y')
            ->orderBy('devename')
            ->get(['devuserid', 'devename', 'email']);
    }

    public static function pickAutoSeniorDeveloper(): ?object
    {
        $seniors = self::activeSeniorDevelopers();
        if ($seniors->isEmpty()) {
            return null;
        }

        $openCounts = DB::table('audit.helpdesk_tickets')
            ->select('assigned_to_userid', DB::raw('COUNT(*) as open_count'))
            ->whereIn('status', [self::TICKET_PENDING_SENIOR, self::TICKET_RETURNED_SENIOR])
            ->whereIn('assigned_to_userid', $seniors->pluck('devuserid')->map(fn ($id) => (string) $id))
            ->groupBy('assigned_to_userid')
            ->pluck('open_count', 'assigned_to_userid');

        return $seniors->sortBy(fn ($senior) => (int) ($openCounts[(string) $senior->devuserid] ?? 0))->first();
    }

    public static function autoForwardStaleNicTickets(int $afterMinutes = 2): array
    {
        $cutoff = now('Asia/Kolkata')->subMinutes($afterMinutes);
        $hasAutoForwardedAtColumn = self::ensureAutoForwardedAtColumn();

        $candidates = DB::table('audit.helpdesk_tickets')
            ->whereRaw("LOWER(forwarded_to_role) IN ('nicadmin', 'nic_admin', 'nic_admn')")
            ->whereNotIn('status', [self::TICKET_CLOSED, self::TICKET_RESOLVED])
            ->where('forwarded_at', '<=', $cutoff)
            ->get(['id', 'status']);

        $nicStatuses = [self::TICKET_PENDING_NIC, self::TICKET_RETURNED_NIC];
        $nicAdmin = self::nicAdminDisplayIdentity();

        $forwardedTicketIds = [];

        foreach ($candidates as $staleTicket) {
            $status = (string) ($staleTicket->status ?? '');
            $isNicStage = in_array($status, $nicStatuses, true);

            if (!$isNicStage) {
                continue;
            }

            $senior = self::pickAutoSeniorDeveloper();
            if (!$senior) {
                break;
            }

            $now = now('Asia/Kolkata');
            $comment = self::ticketActionComment('Forwarded to Senior Developer '.$senior->devename.'.', null);

            $updates = [
                'status' => self::TICKET_PENDING_SENIOR,
                'forwarded_to_role' => self::ROLE_SENIOR_DEVELOPER,
                'assigned_to_userid' => (string) $senior->devuserid,
                'assigned_to_name' => $senior->devename,
                'forwarded_at' => $now,
                'forward_notes' => $comment,
                'updated_at' => $now,
            ];

            if ($hasAutoForwardedAtColumn) {
                $updates['auto_forwarded_at'] = $now;
            }

            DB::table('audit.helpdesk_tickets')->where('id', $staleTicket->id)->update($updates);

	            $assignmentInsert = [
	                'ticket_id' => $staleTicket->id,
	                'assigned_by_userid' => $nicAdmin->userid,
	                'assigned_by_name' => $nicAdmin->name,
                'developer_userid' => (string) $senior->devuserid,
                'developer_name' => $senior->devename,
                'notes' => null,
                'status' => 'senior_developer',
	                'assigned_at' => $now,
	                'created_at' => $now,
	                'updated_at' => $now,
	            ];

	            if (self::ensureTicketAssignmentHistoryColumns()) {
	                $assignmentInsert['action_key'] = 'auto_forward_to_senior';
	                $assignmentInsert['action_label'] = 'Auto Forwarded to Senior Developer';
	                $assignmentInsert['action_remarks'] = null;
	                $assignmentInsert['action_at'] = $now;
	            }

	            DB::table('audit.helpdesk_ticket_assignments')->insert($assignmentInsert);

            DB::table('audit.helpdesk_ticket_comments')->insert([
                'ticket_id' => $staleTicket->id,
                'cams_userid' => $nicAdmin->userid,
                'user_name' => $nicAdmin->name,
                'user_role' => 'NIC Admin',
                'comment' => $comment,
                'is_internal' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $forwardedTicketIds[] = (int) $staleTicket->id;
        }

        return $forwardedTicketIds;
    }

    public static function activeDevelopers(): Collection
    {
        return DB::table('audit.dev_userdetails')
            ->where('statusflag', 'Y')
            ->whereRaw("COALESCE(NULLIF(BTRIM(UPPER(senior_flag::text)), ''), 'N') <> 'Y'")
            ->orderBy('devename')
            ->get(['devuserid', 'devename', 'email', 'senior_flag']);
    }

	    public static function activeTaskDevelopers(): Collection
	    {
	        return DB::table('audit.dev_userdetails')
	            ->where('statusflag', 'Y')
	            ->orderBy('devename')
	            ->get(['devuserid', 'devename', 'email', 'senior_flag']);
	    }

    public static function stateAdminChargeUsers(): Collection
    {
        $chargeId = (string) (view()->shared('Stateadminchargeid') ?? '1');

        return DB::table('audit.userchargedetails as uc')
            ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
	            ->select('du.deptuserid', 'du.username')
	            ->where('uc.statusflag', 'Y')
	            ->where('du.statusflag', 'Y')
	            ->where('uc.chargeid', $chargeId)
	            ->orderBy('du.username')
	            ->distinct()
	            ->get();
	    }

    public static function developerById($developerUserId): ?object
    {
        // if (!$developerUserId) {
        //     return null;
        // }

        return DB::table('audit.dev_userdetails')
            ->where('devuserid', $developerUserId)
            ->where('statusflag', 'Y')
            ->first(['devuserid', 'devename', 'email', 'senior_flag']);
    }

    public static function developerByName($developerName, ?bool $seniorOnly = null): ?object
    {
        $developerName = self::normalizePersonName($developerName);

        if ($developerName === '') {
            return null;
        }

        $query = DB::table('audit.dev_userdetails')
            ->where('statusflag', 'Y')
            ->whereRaw("LOWER(BTRIM(COALESCE(devename::text, ''))) = ?", [$developerName]);

        if ($seniorOnly !== null) {
            $query->whereRaw(
                "COALESCE(NULLIF(BTRIM(UPPER(senior_flag::text)), ''), 'N') ".($seniorOnly ? '=' : '<>')." 'Y'"
            );
        }

        return $query
            ->orderBy('devename')
            ->first(['devuserid', 'devename', 'email', 'senior_flag']);
    }

    private static function taskColumnExists(string $column): bool
    {
        return DB::table('information_schema.columns')
            ->where('table_schema', 'audit')
            ->where('table_name', self::TASK_TABLE_NAME)
            ->where('column_name', $column)
            ->exists();
    }

    public static function ensureTaskHistoryTable(): bool
    {
        static $ensured = null;

        if ($ensured !== null) {
            return $ensured;
        }

        try {
            DB::statement(<<<'SQL'
                CREATE TABLE IF NOT EXISTS audit.task_management_histories (
                    id BIGSERIAL PRIMARY KEY,
                    task_id BIGINT NOT NULL,
                    action_key VARCHAR(80) NOT NULL,
                    stage VARCHAR(100),
                    status VARCHAR(100),
                    comment TEXT,
                    performed_by_userid VARCHAR(100),
                    performed_by_name VARCHAR(200),
                    performed_by_role VARCHAR(100),
                    performed_at TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    metadata JSONB,
                    created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP WITHOUT TIME ZONE
                )
            SQL);
            DB::statement('CREATE INDEX IF NOT EXISTS task_management_histories_task_idx ON audit.task_management_histories (task_id, performed_at)');
            $ensured = true;
        } catch (\Throwable $exception) {
            $ensured = DB::table('information_schema.tables')
                ->where('table_schema', 'audit')
                ->where('table_name', 'task_management_histories')
                ->exists();
        }

        return $ensured;
    }

    private static function cachedSchemaEnsure(string $key, callable $callback): bool
    {
        if (array_key_exists($key, self::$schemaEnsureCache)) {
            return self::$schemaEnsureCache[$key];
        }

        return self::$schemaEnsureCache[$key] = (bool) $callback();
    }

    private static function ensureTicketScopeTypeColumn(): bool
    {
        return self::cachedSchemaEnsure(__FUNCTION__, function () {
            try {
                DB::statement("ALTER TABLE audit.helpdesk_tickets ADD COLUMN IF NOT EXISTS ticket_scope_type VARCHAR(20) NOT NULL DEFAULT 'specified'");

                return true;
            } catch (\Throwable $exception) {
                return DB::table('information_schema.columns')
                    ->where('table_schema', 'audit')
                    ->where('table_name', 'helpdesk_tickets')
                    ->where('column_name', 'ticket_scope_type')
                    ->exists();
            }
        });
    }

    private static function ensureImportantFlagColumn(): bool
    {
        return self::cachedSchemaEnsure(__FUNCTION__, function () {
            try {
                DB::statement('ALTER TABLE audit.helpdesk_tickets ADD COLUMN IF NOT EXISTS importflag VARCHAR(1) NULL');

                return true;
            } catch (\Throwable $exception) {
                return DB::table('information_schema.columns')
                    ->where('table_schema', 'audit')
                    ->where('table_name', 'helpdesk_tickets')
                    ->where('column_name', 'importflag')
                    ->exists();
            }
        });
    }

    private static function ensureVerifiedFlagColumn(): bool
    {
        return self::cachedSchemaEnsure(__FUNCTION__, function () {
            try {
                DB::statement('ALTER TABLE audit.helpdesk_tickets ADD COLUMN IF NOT EXISTS verifiedflag VARCHAR(1) NULL');

                return true;
            } catch (\Throwable $exception) {
                return DB::table('information_schema.columns')
                    ->where('table_schema', 'audit')
                    ->where('table_name', 'helpdesk_tickets')
                    ->where('column_name', 'verifiedflag')
                    ->exists();
            }
        });
    }

    private static function ensureVerifiedUseridColumn(): bool
    {
        return self::cachedSchemaEnsure(__FUNCTION__, function () {
            try {
                DB::statement('ALTER TABLE audit.helpdesk_tickets ADD COLUMN IF NOT EXISTS verified_userid VARCHAR(80) NULL');

                return true;
            } catch (\Throwable $exception) {
                return DB::table('information_schema.columns')
                    ->where('table_schema', 'audit')
                    ->where('table_name', 'helpdesk_tickets')
                    ->where('column_name', 'verified_userid')
                    ->exists();
            }
        });
    }

    private static function ensureForwardedToUseridColumn(): bool
    {
        return self::cachedSchemaEnsure(__FUNCTION__, function () {
            try {
                DB::statement('ALTER TABLE audit.helpdesk_tickets ADD COLUMN IF NOT EXISTS forwarded_to_userid VARCHAR(80) NULL');

                return true;
            } catch (\Throwable $exception) {
                return DB::table('information_schema.columns')
                    ->where('table_schema', 'audit')
                    ->where('table_name', 'helpdesk_tickets')
                    ->where('column_name', 'forwarded_to_userid')
                    ->exists();
            }
        });
    }

    private static function ensureAutoForwardedAtColumn(): bool
    {
        return self::cachedSchemaEnsure(__FUNCTION__, function () {
            try {
                DB::statement('ALTER TABLE audit.helpdesk_tickets ADD COLUMN IF NOT EXISTS auto_forwarded_at TIMESTAMP NULL');

                return true;
            } catch (\Throwable $exception) {
                return DB::table('information_schema.columns')
                    ->where('table_schema', 'audit')
                    ->where('table_name', 'helpdesk_tickets')
                    ->where('column_name', 'auto_forwarded_at')
                    ->exists();
            }
        });
    }

    public static function ensureDeveloperStatusColumn(): bool
    {
        return self::cachedSchemaEnsure(__FUNCTION__, function () {
            try {
                DB::statement('ALTER TABLE audit.helpdesk_tickets ADD COLUMN IF NOT EXISTS developer_status VARCHAR(20) NULL');

                return true;
            } catch (\Throwable $exception) {
                return DB::table('information_schema.columns')
                    ->where('table_schema', 'audit')
                    ->where('table_name', 'helpdesk_tickets')
                    ->where('column_name', 'developer_status')
                    ->exists();
            }
        });
    }

    public static function developerStatusLabels(): array
    {
        return [
            self::DEV_STATUS_IN_PROGRESS => 'In Process',
            self::DEV_STATUS_NEED_CLARIFICATION => 'Need Clarification',
            self::DEV_STATUS_COMPLETED => 'Completed',
        ];
    }

    public static function developerStatusLabel(?string $status): string
    {
            return self::developerStatusLabels()[$status ?? self::DEV_STATUS_IN_PROGRESS] ?? '-';
    }

    public static function hostedInLabels(): array
    {
        return [
            'staging' => 'Staging',
            'production' => 'Production',
        ];
    }

    public static function hostedInLabel(?string $hostedIn): string
    {
        return self::hostedInLabels()[$hostedIn ?? ''] ?? '-';
    }

    public static function taskHostedInValue(object $task): string
    {
        if (!empty($task->deployed_in_live_server)) {
            return 'production';
        }

        if (!empty($task->hosted_in_staging)) {
            return 'staging';
        }

        return '';
    }

	    private static function applyTaskHostedInUpdates(array &$updates, string $hostedIn): void
	    {
	        if (!array_key_exists($hostedIn, self::hostedInLabels())) {
	            return;
	        }

	        self::ensureTaskHostedColumns();
	        $now = now('Asia/Kolkata');
	        $userName = self::userName();

	        if (self::taskColumnExists('hosted_in_staging')) {
	            $updates['hosted_in_staging'] = $hostedIn === 'staging';
	        }

	        if (self::taskColumnExists('deployed_in_live_server')) {
	            $updates['deployed_in_live_server'] = $hostedIn === 'production';
	        }

	        if ($hostedIn === 'staging') {
	            if (self::taskColumnExists('hosted_in_staging_on')) {
	                $updates['hosted_in_staging_on'] = $now;
	            }

	            if (self::taskColumnExists('hosted_in_staging_by')) {
	                $updates['hosted_in_staging_by'] = $userName;
	            }
	        }

	        if ($hostedIn === 'production') {
	            if (self::taskColumnExists('hosted_in_production_on')) {
	                $updates['hosted_in_production_on'] = $now;
	            }

	            if (self::taskColumnExists('hosted_in_production_by')) {
	                $updates['hosted_in_production_by'] = $userName;
	            }
	        }
	    }

    private static function ensureTaskHostedColumns(): bool
    {
	        return self::cachedSchemaEnsure(__FUNCTION__, function () {
	            try {
	                DB::statement('ALTER TABLE audit.developer_tasks ADD COLUMN IF NOT EXISTS hosted_in_staging BOOLEAN NOT NULL DEFAULT FALSE');
	                DB::statement('ALTER TABLE audit.developer_tasks ADD COLUMN IF NOT EXISTS deployed_in_live_server BOOLEAN NOT NULL DEFAULT FALSE');
	                DB::statement('ALTER TABLE audit.developer_tasks ADD COLUMN IF NOT EXISTS hosted_in_staging_on TIMESTAMP NULL');
	                DB::statement('ALTER TABLE audit.developer_tasks ADD COLUMN IF NOT EXISTS hosted_in_staging_by VARCHAR(200) NULL');
	                DB::statement('ALTER TABLE audit.developer_tasks ADD COLUMN IF NOT EXISTS hosted_in_production_on TIMESTAMP NULL');
	                DB::statement('ALTER TABLE audit.developer_tasks ADD COLUMN IF NOT EXISTS hosted_in_production_by VARCHAR(200) NULL');

	                return true;
	            } catch (\Throwable $exception) {
	                return self::taskColumnExists('hosted_in_staging')
	                    && self::taskColumnExists('deployed_in_live_server')
	                    && self::taskColumnExists('hosted_in_staging_on')
	                    && self::taskColumnExists('hosted_in_staging_by')
	                    && self::taskColumnExists('hosted_in_production_on')
	                    && self::taskColumnExists('hosted_in_production_by');
	            }
	        });
	    }

			    private static function ensureTicketDeveloperMetaColumns(): bool
			    {
			        return self::cachedSchemaEnsure(__FUNCTION__, function () {
			            try {
			                DB::statement('ALTER TABLE audit.helpdesk_tickets ADD COLUMN IF NOT EXISTS developer_started_on TIMESTAMP NULL');

			                return true;
			            } catch (\Throwable $exception) {
			                $columns = DB::table('information_schema.columns')
			                    ->where('table_schema', 'audit')
			                    ->where('table_name', 'helpdesk_tickets')
			                    ->where('column_name', 'developer_started_on')
			                    ->pluck('column_name')
			                    ->all();

			                return in_array('developer_started_on', $columns, true);
			            }
			        });
			    }

			    private static function ensureTicketAssignmentHistoryColumns(): bool
			    {
			        return self::cachedSchemaEnsure(__FUNCTION__, function () {
			            try {
			                DB::statement('ALTER TABLE audit.helpdesk_ticket_assignments ADD COLUMN IF NOT EXISTS action_key VARCHAR(80) NULL');
			                DB::statement('ALTER TABLE audit.helpdesk_ticket_assignments ADD COLUMN IF NOT EXISTS action_label VARCHAR(200) NULL');
			                DB::statement('ALTER TABLE audit.helpdesk_ticket_assignments ADD COLUMN IF NOT EXISTS action_remarks TEXT NULL');
			                DB::statement('ALTER TABLE audit.helpdesk_ticket_assignments ADD COLUMN IF NOT EXISTS action_at TIMESTAMP NULL');

			                return true;
			            } catch (\Throwable $exception) {
			                $columns = DB::table('information_schema.columns')
			                    ->where('table_schema', 'audit')
			                    ->where('table_name', 'helpdesk_ticket_assignments')
			                    ->whereIn('column_name', ['action_key', 'action_label', 'action_remarks', 'action_at'])
			                    ->pluck('column_name')
			                    ->all();

			                return count(array_intersect(['action_key', 'action_label', 'action_remarks', 'action_at'], $columns)) === 4;
			            }
			        });
			    }

	    public static function canViewDeveloperStatus(?string $role = null): bool
    {
        return in_array($role ?? self::role(), [
            self::ROLE_NIC_ADMIN,
            self::ROLE_SENIOR_DEVELOPER,
            self::ROLE_DEVELOPER,
        ], true);
    }

    private static function unverifiedImportantTicketsQuery()
    {
        $hasVerifiedFlagColumn = self::ensureVerifiedFlagColumn();

        $query = DB::table('audit.helpdesk_tickets')
            ->where('importflag', 'Y')
            ->where('status', '!=', self::TICKET_CLOSED);

        if ($hasVerifiedFlagColumn) {
            $query->whereNull('verifiedflag');
        }

        return $query;
    }

    public static function importantTickets(int $limit = 8): Collection
    {
        if (! self::ensureImportantFlagColumn()) {
            return collect();
        }

        return self::unverifiedImportantTicketsQuery()
            ->select('id', 'ticket_number', 'subject', 'user_name', 'priority', 'status', 'created_at')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public static function importantTicketsCount(): int
    {
        if (! self::ensureImportantFlagColumn()) {
            return 0;
        }

        return (int) self::unverifiedImportantTicketsQuery()->count();
    }

    public static function markImportantTicketsVerified(): int
    {
        if (! self::ensureImportantFlagColumn() || ! self::ensureVerifiedFlagColumn()) {
            return 0;
        }

        $update = ['verifiedflag' => 'Y'];
        if (self::ensureVerifiedUseridColumn()) {
            $update['verified_userid'] = self::userId();
        }

        return self::unverifiedImportantTicketsQuery()->update($update);
    }

    public static function nicAdminDisplayIdentity(): object
    {
        $nicAdmin = DB::table('audit.userchargedetails as uc')
            ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
            ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
            ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
            ->where('uc.statusflag', 'Y')
            ->where('du.statusflag', 'Y')
            ->where('uc.chargeid', '!=', '1')
            ->where(function ($query) {
                $query->where('uc.chargeid', '907')->orWhere('rm.roleactioncode', '01');
            })
            ->orderByRaw("CASE WHEN uc.chargeid = '907' THEN 0 ELSE 1 END")
            ->orderBy('du.deptuserid')
            ->first(['du.deptuserid as userid', 'du.username']);

        return (object) [
            'userid' => $nicAdmin->userid ?? null,
            'name' => $nicAdmin->username ?? 'NIC Admin',
        ];
    }

    public static function emailsForRole(string $role): array
    {
        if ($role === self::ROLE_STATE_ADMIN || $role === 'stateadmin') {
            $stateChargeId = (string) (view()->shared('Stateadminchargeid') ?? '1');

            return DB::table('audit.userchargedetails as uc')
                ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
                ->where('uc.statusflag', 'Y')
                ->where('du.statusflag', 'Y')
                ->where('uc.chargeid', $stateChargeId)
                ->distinct()
                ->pluck('du.email')
                ->all();
        }

	        if ($role === self::ROLE_NIC_ADMIN || $role === 'nicadmin') {
	            $nicChargeId = (string) (view()->shared('NICAdminchargeid') ?? '907');

	            $nicEmail = DB::table('audit.userchargedetails as uc')
	                ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
	                ->where('uc.statusflag', 'Y')
	                ->where('du.statusflag', 'Y')
	                ->where('uc.chargeid', $nicChargeId)
	                ->orderBy('du.deptuserid')
	                ->value('du.email');

	            return [$nicEmail];
	        }

        return [];
    }

    public static function ticketCreatorEmails(object $ticket): array
    {
        $emails = [$ticket->user_email ?? null];
        $creatorUserId = trim((string) ($ticket->cams_userid ?? ''));

        if ($creatorUserId !== '') {
            $emails[] = DB::table('audit.deptuserdetails')
                ->whereRaw('deptuserid::text = ?', [$creatorUserId])
                ->where('statusflag', 'Y')
                ->value('email');
        }

        return self::normalizeEmails($emails);
    }

    public static function ticketCreatorIsStateAdmin(object $ticket): bool
    {
        if (property_exists($ticket, 'creator_is_state_admin') || isset($ticket->creator_is_state_admin)) {
            return self::dbBoolean($ticket->creator_is_state_admin);
        }

        $creatorUserId = trim((string) ($ticket->cams_userid ?? ''));

        if ($creatorUserId === '') {
            return false;
        }

        $stateChargeId = (string) (view()->shared('Stateadminchargeid') ?? '1');

        if (DB::table('audit.userchargedetails')
            ->whereRaw('userid::text = ?', [$creatorUserId])
            ->where('statusflag', 'Y')
            ->whereRaw('chargeid::text = ?', [$stateChargeId])
            ->exists()) {
            return true;
        }

        return DB::table('audit.helpdesk_ticket_comments')
            ->where('ticket_id', (int) ($ticket->id ?? 0))
            ->whereRaw('cams_userid::text = ?', [$creatorUserId])
            ->whereRaw("LOWER(BTRIM(REPLACE(COALESCE(user_role::text, ''), '_', ' '))) IN (?, ?)", ['state admin', 'stateadmin'])
            ->exists();
    }

    private static function ticketCreatorIsStateAdminSelectSql()
    {
        $stateChargeId = str_replace("'", "''", (string) (view()->shared('Stateadminchargeid') ?? '1'));

        return DB::raw("(
            EXISTS (
                SELECT 1
                FROM audit.userchargedetails as uc_creator_state
                WHERE uc_creator_state.userid::text = t.cams_userid::text
                AND uc_creator_state.statusflag = 'Y'
                AND uc_creator_state.chargeid::text = '{$stateChargeId}'
            )
            OR EXISTS (
                SELECT 1
                FROM audit.helpdesk_ticket_comments as htc_creator_state
                WHERE htc_creator_state.ticket_id = t.id
                AND htc_creator_state.cams_userid::text = t.cams_userid::text
                AND LOWER(BTRIM(REPLACE(COALESCE(htc_creator_state.user_role::text, ''), '_', ' '))) IN ('state admin', 'stateadmin')
            )
        ) as creator_is_state_admin");
    }

    private static function dbBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['1', 't', 'true', 'y', 'yes'], true);
    }

    public static function currentStateAdminCanActOnTicket(object $ticket): bool
    {
        if (self::role() !== self::ROLE_STATE_ADMIN) {
            return false;
        }

        if (!self::ticketCreatorIsStateAdmin($ticket)) {
            return true;
        }

        return (string) ($ticket->cams_userid ?? '') === (string) self::userId();
    }

    public static function recipientEmailsForTicket(object $ticket): array
    {
        $forwardedRole = (string) ($ticket->forwarded_to_role ?? '');

        if (self::isLegacyStateForwardStatus($forwardedRole)
            || in_array($forwardedRole, self::stateForwardedRoleValues(), true)
            || in_array(self::normalizedTicketStatus($forwardedRole), ['state admin', 'stateadmin', 'superadmin'], true)
        ) {
            return self::emailsForRole(self::ROLE_STATE_ADMIN);
        }

        return match ($forwardedRole) {
            'stateadmin' => self::emailsForRole(self::ROLE_STATE_ADMIN),
            'nicadmin' => self::emailsForRole(self::ROLE_NIC_ADMIN),
            self::ROLE_SENIOR_DEVELOPER, self::ROLE_DEVELOPER => [optional(self::developerById($ticket->assigned_to_userid ?? null))->email],
            'user' => [$ticket->user_email ?? null],
            default => [],
        };
    }

    public static function recipientEmailsForTask(object $task): array
    {
        $status = (string) $task->task_status_by_tester;

        if ($status === self::TASK_RESOLVED && !empty($task->verified_by)) {
            return self::emailsForRole(self::ROLE_NIC_ADMIN);
        }

        if (in_array($status, [self::TASK_RESOLVED, self::TASK_WRONGLY_ASSIGNED], true)) {
            return self::activeSeniorDevelopers()->pluck('email')->filter()->values()->all();
        }

        return [optional(self::developerById($task->developer_userid ?? null))->email];
    }

    public static function normalizeEmails(array $emails): array
    {
        return collect($emails)
            ->flatten()
            ->filter()
            ->map(fn ($email) => trim((string) $email))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique(fn ($email) => strtolower($email))
            ->values()
            ->all();
    }

    public static function addTicketComment(int $ticketId, string $comment, bool $internal): void
    {
        $now = now('Asia/Kolkata');

        DB::table('audit.helpdesk_ticket_comments')->insert([
            'ticket_id' => $ticketId,
            'cams_userid' => self::userId(),
            'user_name' => self::userName(),
            'user_role' => self::roleLabel(),
            'comment' => $comment,
            'is_internal' => $internal,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private static function addTicketAssignment(int $ticketId, object $developer, string $status, ?string $notes, ?string $actionKey = null, ?string $actionLabel = null): void
    {
        $now = now('Asia/Kolkata');
        $insert = [
            'ticket_id' => $ticketId,
            'assigned_by_userid' => self::userId(),
            'assigned_by_name' => self::userName(),
            'developer_userid' => (string) $developer->devuserid,
            'developer_name' => $developer->devename,
            'notes' => $notes,
            'status' => $status,
            'assigned_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if (self::ensureTicketAssignmentHistoryColumns()) {
            $insert['action_key'] = $actionKey;
            $insert['action_label'] = $actionLabel;
            $insert['action_remarks'] = $notes;
            $insert['action_at'] = $now;
        }

        DB::table('audit.helpdesk_ticket_assignments')->insert($insert);
    }

	    private static function addTaskHistory(int $taskId, string $action, string $stage, string $status, ?string $comment): void
	    {
	        $now = now('Asia/Kolkata');

        if (!self::ensureTaskHistoryTable()) {
            return;
        }

        DB::table(self::TASK_HISTORY_TABLE)->insert([
            'task_id' => $taskId,
            'action_key' => $action,
            'stage' => $stage,
            'status' => $status,
            'comment' => $comment,
            'performed_by_userid' => self::userId(),
            'performed_by_name' => self::userName(),
            'performed_by_role' => self::roleLabel(),
            'performed_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
	        ]);
	    }

	    private static function addTaskHostedHistory(int $taskId, mixed $hostedIn, string $status, ?string $remarks): void
	    {
	        $hostedIn = (string) $hostedIn;
	        if (!array_key_exists($hostedIn, self::hostedInLabels())) {
	            return;
	        }

	        $stage = $hostedIn === 'production' ? 'Hosted In Production' : 'Hosted In Staging';
	        $action = $hostedIn === 'production' ? 'hosted_in_production' : 'hosted_in_staging';
	        $comment = $stage.'.';
	        $remarks = trim((string) $remarks);

	        if ($remarks !== '') {
	            $comment .= ' Remarks: '.$remarks;
	        }

	        self::addTaskHistory($taskId, $action, $stage, $status, $comment);
	    }

	    private static function latestTicketSenior(int $ticketId): ?object
	    {
	        return DB::table('audit.helpdesk_ticket_assignments')
	            ->where('ticket_id', $ticketId)
	            ->where('status', 'senior_developer')
            ->orderByDesc('assigned_at')
            ->orderByDesc('id')
	            ->first();
	    }

	    public static function latestTicketSeniorDeveloper(int $ticketId): ?object
	    {
	        return self::latestTicketSenior($ticketId);
	    }

    public static function ticketWasAssignedTo(int $ticketId, ?string $developerUserId): bool
    {
        if (!$developerUserId) {
            return false;
        }

        return DB::table('audit.helpdesk_ticket_assignments')
            ->where('ticket_id', $ticketId)
            ->whereRaw('developer_userid::text = ?', [(string) $developerUserId])
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'watchlist');
            })
            ->exists();
    }

    private static function departmentName(?string $deptCode): ?string
    {
        if (!$deptCode) {
            return null;
        }

        return DB::table('audit.mst_dept')->where('deptcode', $deptCode)->value('deptesname');
    }

    private static function planMappingId(?string $deptCode, ?string $financialYearCode, ?string $auditQuarterCode): ?int
    {
        if (!$deptCode || !$financialYearCode || !$auditQuarterCode) {
            return null;
        }

        $planMappingId = DB::table('audit.auditplanmapping')
            ->where('deptcode', $deptCode)
            ->where('financialyearcode', $financialYearCode)
            ->where('auditquartercode', $auditQuarterCode)
            ->whereIn('statusflag', ['F', 'P', 'Y'])
            ->value('planmappingid');

        return $planMappingId === null ? null : (int) $planMappingId;
    }

    private static function nextTicketNumber(?string $departmentName): string
    {
        $year = Carbon::now('Asia/Kolkata')->format('Y');
        $departmentLetter = self::departmentLetter($departmentName);
        $prefix = 'TKT'.$year.$departmentLetter.'A';
        $oldPrefix = 'NHD'.$year.$departmentLetter;
        $lastSerial = DB::table('audit.helpdesk_tickets')
            ->where(function ($query) use ($prefix, $oldPrefix) {
                $query->where('ticket_number', 'like', $prefix.'%')
                    ->orWhere('ticket_number', 'like', $oldPrefix.'%');
            })
            ->pluck('ticket_number')
            ->map(fn ($ticketNumber) => (int) substr((string) $ticketNumber, -5))
            ->max();

        $next = $lastSerial ? $lastSerial + 1 : 1;

        return $prefix.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    private static function departmentLetter(?string $departmentName): string
    {
        preg_match('/[A-Z]/', Str::upper((string) $departmentName), $matches);

        return $matches[0] ?? 'X';
    }

    private static function appendLine(?string $existing, string $line): string
    {
        $existing = trim((string) $existing);

        return $existing === '' ? $line : $existing.PHP_EOL.$line;
    }
}
