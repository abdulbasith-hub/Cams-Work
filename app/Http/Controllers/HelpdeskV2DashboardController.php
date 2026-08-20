<?php

namespace App\Http\Controllers;

use App\Models\HelpdeskV2Ticket;
use App\Models\TaskManagementTask;
use App\Services\HelpdeskV2DashboardService;
use App\Services\HelpdeskV2Session;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class HelpdeskV2DashboardController extends Controller
{
    public function index(Request $request, HelpdeskV2DashboardService $dashboardService, ?string $role = null)
    {
        $role = $role ?: HelpdeskV2Session::role();
        abort_unless($this->canOpenRoleDashboard($role), 403);

        $baseQuery = $dashboardService->queryForRole($role);
        $stats = $dashboardService->stats(clone $baseQuery);
        $dashboardTickets = collect();
        $canViewTaskDashboard = $this->canViewTaskDashboard();
        $canViewDeveloperFilter = $this->canViewDeveloperFilter();
        $dashboardTasks = $this->dashboardTasks();

        return view('helpdesk-v2.dashboard', [
            'role' => $role,
            'roleLabel' => HelpdeskV2Session::roleLabel($role),
            'roles' => $this->visibleRoles(),
            'dashboardPane' => $this->dashboardPane($request),
            'stats' => $stats,
            'dashboardTickets' => $dashboardTickets,
            'canViewTaskDashboard' => $canViewTaskDashboard,
            'canViewDeveloperFilter' => $canViewDeveloperFilter,
            'dashboardTasks' => $dashboardTasks,
            'taskStats' => $this->taskStats($dashboardTasks),
        ]);
    }

    public function data(Request $request, HelpdeskV2DashboardService $dashboardService, ?string $role = null): JsonResponse
    {
        $role = $role ?: HelpdeskV2Session::role();
        abort_unless($this->canOpenRoleDashboard($role), 403);

        $type = $this->dashboardRequestValue($request, 't', 'type', 'tickets');
        $filter = $this->dashboardRequestValue($request, 'f', 'filter', $type === 'tasks' ? 'total' : 'in_progress');

        if ($type === 'tasks') {
            abort_unless($this->canViewTaskDashboard(), 403);

            $tasks = $this->filterTasks($this->dashboardTasks(), $filter)->values();

            return response()->json([
                'count' => $tasks->count(),
                'rows' => $tasks->map(fn (TaskManagementTask $task) => $this->taskRow($task))->values(),
            ]);
        }

        if ($type === 'developers') {
            abort_unless($this->canViewDeveloperFilter(), 403);

            return response()->json([
                'developers' => $this->developerTechCounts($dashboardService->queryForRole($role)),
            ]);
        }

        $tickets = $dashboardService->applyCardFilter($dashboardService->queryForRole($role), $filter)
            ->with(['comments', 'assignments'])
            ->latest('updated_at')
            ->latest('id')
            ->get();

        return response()->json([
            'count' => $tickets->count(),
            'rows' => $tickets->map(fn (HelpdeskV2Ticket $ticket) => $this->ticketRow($ticket, $role))->values(),
        ]);
    }

    private function dashboardRequestValue(Request $request, string $encryptedKey, string $plainKey, string $default): string
    {
        if ($request->filled($encryptedKey)) {
            try {
                return (string) Crypt::decryptString((string) $request->query($encryptedKey));
            } catch (DecryptException) {
                abort(400, 'Invalid dashboard request.');
            }
        }

        return (string) $request->query($plainKey, $default);
    }

    private function visibleRoles(): array
    {
        if (HelpdeskV2Session::isStateAdmin()) {
            return [HelpdeskV2Session::ROLE_STATE_ADMIN => 'State Admin'];
        }

        if (HelpdeskV2Session::isNicAdmin()) {
            return [HelpdeskV2Session::ROLE_NIC_ADMIN => 'NIC Admin'];
        }

        if (HelpdeskV2Session::isLayerLead()) {
            return [
                HelpdeskV2Session::ROLE_LAYER_LEAD => 'Layer Lead',
                HelpdeskV2Session::ROLE_DEVELOPER => 'Developer',
                HelpdeskV2Session::ROLE_TESTER => 'Tester',
                HelpdeskV2Session::ROLE_WATCHLIST => 'Watchlist',
            ];
        }

        if (HelpdeskV2Session::isDeveloperPerson()) {
            return [
                HelpdeskV2Session::ROLE_DEVELOPER => 'Developer',
                HelpdeskV2Session::ROLE_TESTER => 'Tester',
                HelpdeskV2Session::ROLE_WATCHLIST => 'Watchlist',
            ];
        }

        return [HelpdeskV2Session::ROLE_USER => 'User'];
    }

    private function developerTechCounts($query): Collection
    {
        $resolvedStatuses = [
            'resolved',
            HelpdeskV2Ticket::STATUS_CLOSED,
            HelpdeskV2Ticket::STATUS_REJECTED,
            HelpdeskV2Ticket::STATUS_CANCELLED,
            HelpdeskV2Ticket::STATUS_COMPLETED_LAYER_LEAD,
            HelpdeskV2Ticket::STATUS_PENDING_NIC_ADMIN_REVIEW,
            HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN_REVIEW,
            'pending_nic_admin_review',
            'pending_state_admin_review',
        ];
        $returnedStatuses = [
            HelpdeskV2Ticket::STATUS_RETURNED_TO_DEVELOPER,
            'returned_to_developer',
        ];
        $assignmentStatuses = ['developer', 'assigned', 'returned', 'reassigned'];
        $ticketQuery = (clone $query)->select([
            'audit.helpdesk_tickets.id',
            'audit.helpdesk_tickets.status',
            'audit.helpdesk_tickets.forwarded_to_role',
        ]);
        $resolvedSql = $this->sqlStringList($resolvedStatuses);
        $returnedSql = $this->sqlStringList($returnedStatuses);
        $assignmentStatusSql = $this->sqlStringList($assignmentStatuses);

        return DB::query()
            ->fromSub($ticketQuery->toBase(), 'tickets')
            ->join('audit.helpdesk_ticket_assignments as hta', function ($join) use ($assignmentStatuses, $assignmentStatusSql) {
                $join->on('hta.ticket_id', '=', 'tickets.id')
                    ->whereIn('hta.status', $assignmentStatuses)
                    ->whereRaw("hta.id = (
                        SELECT MAX(hta_latest.id)
                        FROM audit.helpdesk_ticket_assignments as hta_latest
                        WHERE hta_latest.ticket_id = tickets.id
                        AND hta_latest.status IN ($assignmentStatusSql)
                    )");
            })
            ->whereNotNull('hta.developer_userid')
            ->whereNotNull('hta.developer_name')
            ->selectRaw('hta.developer_userid, hta.developer_name')
            ->selectRaw('COUNT(DISTINCT tickets.id) as total')
            ->selectRaw('0 as pending')
            ->selectRaw("COUNT(DISTINCT CASE WHEN tickets.status NOT IN ($resolvedSql) AND tickets.status NOT IN ($returnedSql) THEN tickets.id END) as in_progress")
            ->selectRaw("COUNT(DISTINCT CASE WHEN tickets.status IN ($resolvedSql) THEN tickets.id END) as resolved")
            ->selectRaw("COUNT(DISTINCT CASE WHEN tickets.status IN ($returnedSql) THEN tickets.id END) as returned")
            ->groupBy('hta.developer_userid', 'hta.developer_name')
            ->orderBy('hta.developer_name')
            ->get()
            ->map(function ($row) {
                $developerUserId = (string) $row->developer_userid;

                return [
                    'developer_userid' => $developerUserId,
                    'developer_name' => (string) $row->developer_name,
                    'total' => (int) $row->total,
                    'pending' => (int) $row->pending,
                    'in_progress' => (int) $row->in_progress,
                    'resolved' => (int) $row->resolved,
                    'returned' => (int) $row->returned,
                    'filters' => [
                        'total' => Crypt::encryptString('developer:'.$developerUserId.':total'),
                        'pending' => Crypt::encryptString('developer:'.$developerUserId.':pending'),
                        'in_progress' => Crypt::encryptString('developer:'.$developerUserId.':in_progress'),
                        'resolved' => Crypt::encryptString('developer:'.$developerUserId.':resolved'),
                        'returned' => Crypt::encryptString('developer:'.$developerUserId.':returned'),
                    ],
                ];
            })
            ->values();
    }

    private function sqlStringList(array $values): string
    {
        return collect($values)
            ->map(fn ($value) => "'".str_replace("'", "''", (string) $value)."'")
            ->implode(',');
    }

    private function canOpenRoleDashboard(string $role): bool
    {
        return array_key_exists($role, $this->visibleRoles());
    }

    private function canViewTaskDashboard(): bool
    {
        return HelpdeskV2Session::isNicAdmin();
    }

    private function canViewDeveloperFilter(): bool
    {
        return HelpdeskV2Session::isNicAdmin();
    }

    private function dashboardPane(Request $request): string
    {
        if ($request->query('pane') === 'tasks' && $this->canViewTaskDashboard()) {
            return 'tasks';
        }

        return 'tickets';
    }

    private function dashboardTasks(): Collection
    {
        if (!$this->canViewTaskDashboard()) {
            return collect();
        }

        $query = TaskManagementTask::query()
            ->where('is_testing_task', false);

        return $this->orderedTaskQuery($query)->get();
    }

    private function ticketRow(HelpdeskV2Ticket $ticket, string $role): array
    {
        $currentOnMeta = $ticket->currentOnMetaForRole($role);
        $ticketUrl = route('helpdesk-v2.tickets.show', ['ticket' => $ticket, 'role' => $role]);
        $priorityKey = strtolower((string) $ticket->priority);
        $statusKey = $ticket->mainStatusKey();

        return [
            '<a href="'.$ticketUrl.'">'.e($ticket->ticket_number).'</a>',
            '<strong>'.e($ticket->subject).'</strong><small>'.e($ticket->created_by_name).'</small>',
            e($ticket->request_type_label),
            '<span class="hdv2-badge hdv2-priority-'.$priorityKey.'">'.e($ticket->priority).'</span>',
            '<span class="hdv2-badge hdv2-status-'.$statusKey.'">'.e($ticket->mainStatusLabel()).'</span>',
            e($ticket->currentOnLabelForRole($role)).($currentOnMeta ? '<small>'.e($currentOnMeta).'</small>' : ''),
            e($ticket->updated_at ? $ticket->updated_at->diffForHumans() : '-'),
            e($ticket->created_at ? HelpdeskV2Ticket::displayDateTime($ticket->created_at) : '-'),
            e($ticket->updated_at ? HelpdeskV2Ticket::displayDateTime($ticket->updated_at) : '-'),
        ];
    }

    private function taskRow(TaskManagementTask $task): array
    {
        $taskStatusKey = $task->statusKey();

        return [
            '<strong>'.e($task->process_assigned).'</strong><small>Assigned by '.e($task->assigned_by_name ?: '-').'</small>',
            e($task->developer_name ?: '-'),
            e(ucfirst((string) $task->task_type)),
            '<span class="hdv2-badge hdv2-status-'.$taskStatusKey.'">'.e($task->statusLabel()).'</span>',
            e($task->assigned_on ? $task->assigned_on->format('d/m/Y h:i A') : '-'),
            e($task->expected_date_to_complete ? $task->expected_date_to_complete->format('d/m/Y h:i A') : '-'),
            e($task->updated_at ? $task->updated_at->format('d/m/Y h:i A') : '-'),
            '<a href="'.route('task-management.show', $task).'" class="btn btn-primary btn-sm hdv2-view-sheet-btn">View Sheet</a>',
        ];
    }

    private function filterTasks(Collection $tasks, string $filter): Collection
    {
        if ($filter === 'total') {
            return $tasks;
        }

        return $tasks->filter(fn (TaskManagementTask $task) => $task->statusKey() === $filter);
    }

    private function orderedTaskQuery($query)
    {
        return $query
            ->orderByDesc('updated_at')
            ->orderByDesc('assigned_on')
            ->orderByDesc('id');
    }

    private function taskStats(Collection $tasks): array
    {
        return [
            'total' => $tasks->count(),
            'in_progress' => $tasks->filter(fn (TaskManagementTask $task) => $task->statusKey() === 'in_progress')->count(),
            'pending' => $tasks->filter(fn (TaskManagementTask $task) => $task->statusKey() === 'pending')->count(),
            'overdue' => $tasks->filter(fn (TaskManagementTask $task) => $task->statusKey() === 'overdue')->count(),
            'completed' => $tasks->filter(fn (TaskManagementTask $task) => $task->statusKey() === 'completed')->count(),
        ];
    }

}
