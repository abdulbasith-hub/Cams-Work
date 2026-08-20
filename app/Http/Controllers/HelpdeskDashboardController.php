<?php

namespace App\Http\Controllers;

use App\Models\HelpdeskTicket;
use App\Services\DeveloperTaskDashboardDataService;
use App\Support\HelpdeskSession;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HelpdeskDashboardController extends Controller
{
    public function __construct(private DeveloperTaskDashboardDataService $taskDashboardDataService)
    {
    }

    public function index(): View
    {
        abort_unless(HelpdeskSession::canAccess(), 403);


        $roleactioncode = HelpdeskSession::roleActionCode();
        $isChargeOneDashboard = HelpdeskSession::isSuperAdmin() || HelpdeskSession::isNicAdmin();
        $isChargeFiveDashboard = $roleactioncode === '21';
        $baseQuery = $this->scopedTickets();

        $stats = [
            'total_tickets' => (clone $baseQuery)->count(),
            'open_tickets' => (clone $baseQuery)->where('status', 'open')->count(),
            'in_progress_tickets' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'resolved_tickets' => (clone $baseQuery)->where('status', 'resolved')->count(),
            'closed_tickets' => (clone $baseQuery)->where('status', 'closed')->count(),
        ];

        if (HelpdeskSession::isDeveloper()) {
            $stats['forwarded_to_tech'] = (clone $baseQuery)->where('assigned_to_userid', HelpdeskSession::userId())->count();
        }

        if (HelpdeskSession::isSuperAdmin()) {
            $stats['forwarded_to_admin'] = HelpdeskTicket::whereIn('forwarded_to_role', ['superadmin', 'stateadmin'])->count();
        }

        if (HelpdeskSession::isNicAdmin()) {
            $stats['forwarded_to_admin'] = HelpdeskTicket::where('forwarded_to_role', 'nicadmin')->count();
        }

        if ($isChargeOneDashboard) {
            $stats['total_departments'] = (clone $baseQuery)
                ->whereNotNull('deptcode')
                ->distinct('deptcode')
                ->count('deptcode');

            $stats['total_users'] = (clone $baseQuery)
                ->whereNotNull('cams_userid')
                ->distinct('cams_userid')
                ->count('cams_userid');
        }

        $recentTickets = (clone $baseQuery)
            ->orderByRaw('DATE(created_at) DESC')
            ->orderByRaw("CASE WHEN priority = 'urgent' THEN 1 WHEN priority = 'high' THEN 2 WHEN priority = 'medium' THEN 3 WHEN priority = 'low' THEN 4 ELSE 5 END")
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $ticketsByPriority = (clone $baseQuery)
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->orderBy('priority')
            ->get();

        $ticketsByDepartment = collect();

        if (HelpdeskSession::isSuperAdmin() || HelpdeskSession::isNicAdmin()) {
            $ticketsByDepartment = (clone $baseQuery)
                ->selectRaw("COALESCE(NULLIF(TRIM(department_name), ''), 'N/A') as name, count(*) as tickets_count")
                ->groupBy('name')
                ->orderByDesc('tickets_count')
                ->get();
        }

        $viewData = [
            'stats' => $stats,
            'recentTickets' => $recentTickets,
            'ticketsByPriority' => $ticketsByPriority,
            'ticketsByDepartment' => $ticketsByDepartment,
            'ticketDashboardCards' => $this->ticketDashboardCards($stats, $isChargeOneDashboard),
            'ticketDashboardDetails' => $this->ticketDashboardDetails($baseQuery, $ticketsByDepartment, $isChargeOneDashboard),
            'helpdeskRole' => HelpdeskSession::role(),
            'helpdeskRoleLabel' => HelpdeskSession::roleLabel(),
            'canViewTaskDashboard' => $this->taskDashboardDataService->canView(),
        ];

        if ($viewData['canViewTaskDashboard']) {
            $viewData['dashboardData'] = $this->taskDashboardDataService->taskDashboardData();
            $viewData['testingTaskDashboardData'] = $this->taskDashboardDataService->testingTaskDashboardData();
        }

        if (HelpdeskSession::isSuperAdmin() || HelpdeskSession::isNicAdmin()) {
            return view('tickets.superadmin', $viewData);
        }

        if ($isChargeFiveDashboard) {
            return view('tickets.user', $viewData);
        }

        return view('tickets.dashboard', $viewData);
    }

    private function ticketDashboardCards(array $stats, bool $includeDepartments): array
    {
        $cards = [
            [
                'key' => 'total_tickets',
                'label' => 'Total Tickets',
                'count' => $stats['total_tickets'] ?? 0,
                'accent' => 'assigned',
            ],
            [
                'key' => 'pending_tickets',
                'label' => 'Pending Tickets',
                'count' => $stats['open_tickets'] ?? 0,
                'accent' => 'pending',
            ],
            [
                'key' => 'in_progress_tickets',
                'label' => 'In Progress Tickets',
                'count' => $stats['in_progress_tickets'] ?? 0,
                'accent' => 'in-progress',
            ],
            [
                'key' => 'resolved_tickets',
                'label' => 'Resolved Tickets',
                'count' => $stats['resolved_tickets'] ?? 0,
                'accent' => 'completed',
            ],
            [
                'key' => 'closed_tickets',
                'label' => 'Closed Tickets',
                'count' => $stats['closed_tickets'] ?? 0,
                'accent' => 'closed',
            ],
        ];

        if ($includeDepartments) {
            $cards[] = [
                'key' => 'departments',
                'label' => 'Departments',
                'count' => $stats['total_departments'] ?? 0,
                'accent' => 'before-due',
            ];
        }

        return $cards;
    }

	    private function ticketDashboardDetails($baseQuery, Collection $ticketsByDepartment, bool $includeDepartments): array
	    {
	        $recentTicketQuery = fn (Builder $query) => $query
	            ->orderByDesc('created_at')
	            ->get()
	            ->map(fn (HelpdeskTicket $ticket) => $this->ticketDetailRow($ticket))
	            ->values()
            ->all();

        $details = [
            'total_tickets' => [
                'label' => 'Total Tickets',
                'type' => 'tickets',
                'rows' => $recentTicketQuery(clone $baseQuery),
            ],
            'pending_tickets' => [
                'label' => 'Pending Tickets',
                'type' => 'tickets',
                'rows' => $recentTicketQuery((clone $baseQuery)->where('status', 'open')),
            ],
            'in_progress_tickets' => [
                'label' => 'In Progress Tickets',
                'type' => 'tickets',
                'rows' => $recentTicketQuery((clone $baseQuery)->where('status', 'in_progress')),
            ],
            'resolved_tickets' => [
                'label' => 'Resolved Tickets',
                'type' => 'tickets',
                'rows' => $recentTicketQuery((clone $baseQuery)->where('status', 'resolved')),
            ],
            'closed_tickets' => [
                'label' => 'Closed Tickets',
                'type' => 'tickets',
                'rows' => $recentTicketQuery((clone $baseQuery)->where('status', 'closed')),
            ],
        ];

        if ($includeDepartments) {
            $details['departments'] = [
                'label' => 'Departments',
                'type' => 'departments',
                'rows' => $ticketsByDepartment
                    ->map(function ($department) use ($baseQuery, $recentTicketQuery) {
                        return [
                            'department' => $department->name,
                            'tickets_count' => $department->tickets_count,
                            'tickets' => $recentTicketQuery(
                                (clone $baseQuery)->whereRaw(
                                    "COALESCE(NULLIF(TRIM(department_name), ''), 'N/A') = ?",
                                    [$department->name]
                                )
                            ),
                        ];
                    })
                    ->values()
                    ->all(),
            ];
        }

        return $details;
    }

    private function ticketDetailRow(HelpdeskTicket $ticket): array
    {
        return [
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'user_name' => HelpdeskSession::normalizeUserName($ticket->user_name),
            'department_name' => $ticket->department_name ?: 'N/A',
            'priority' => ucfirst((string) $ticket->priority),
            'status' => ucfirst(str_replace('_', ' ', (string) $ticket->status)),
            'created_at' => optional($ticket->created_at)->format('D, d/m/Y h:i A') ?: '-',
            'show_url' => route('helpdesk.tickets.show', $ticket),
        ];
    }

    private function scopedTickets()
    {
        $query = HelpdeskTicket::query();

        if (HelpdeskSession::isSuperAdmin() || HelpdeskSession::isNicAdmin()) {
            return $query;
        }

        if (HelpdeskSession::isDeveloper()) {
            $currentUserId = HelpdeskSession::userId();
            $isAdditionalLayerDeveloper = $this->isAdditionalAssignmentLayerUser($currentUserId);

            return $query->where(function ($builder) use ($currentUserId, $isAdditionalLayerDeveloper) {
                $builder->where('assigned_to_userid', HelpdeskSession::userId())
                    ->orWhereExists(function ($assignmentQuery) {
                        $assignmentQuery->select(DB::raw(1))
	                            ->from('audit.helpdesk_ticket_assignments as hta')
	                            ->whereColumn('hta.ticket_id', 'audit.helpdesk_tickets.id')
	                            ->where('hta.developer_userid', HelpdeskSession::userId())
	                            ->where('hta.status', '!=', 'watchlist')
	                            ->whereNotExists(function ($latestAssignmentQuery) {
	                                $latestAssignmentQuery->select(DB::raw(1))
	                                    ->from('audit.helpdesk_ticket_assignments as hta_latest')
	                                    ->whereColumn('hta_latest.ticket_id', 'hta.ticket_id')
	                                    ->where('hta_latest.status', '!=', 'watchlist')
	                                    ->whereColumn('hta_latest.id', '>', 'hta.id');
	                            });
                    });

                if ($isAdditionalLayerDeveloper) {
                    $builder->orWhereExists(function ($assignmentQuery) use ($currentUserId) {
                        $assignmentQuery->select(DB::raw(1))
	                            ->from('audit.helpdesk_ticket_assignments as hta')
	                            ->whereColumn('hta.ticket_id', 'audit.helpdesk_tickets.id')
	                            ->where('hta.assigned_by_userid', (string) $currentUserId)
	                            ->where('hta.status', '!=', 'watchlist')
	                            ->whereNotExists(function ($latestAssignmentQuery) {
	                                $latestAssignmentQuery->select(DB::raw(1))
	                                    ->from('audit.helpdesk_ticket_assignments as hta_latest')
	                                    ->whereColumn('hta_latest.ticket_id', 'hta.ticket_id')
	                                    ->where('hta_latest.status', '!=', 'watchlist')
	                                    ->whereColumn('hta_latest.id', '>', 'hta.id');
	                            });
                    });
                }
            });
        }

        if (HelpdeskSession::isDepartmentAdmin()) {
            return $query->where('deptcode', HelpdeskSession::deptCode());
        }

        return $query->where('cams_userid', HelpdeskSession::userId());
    }

    private function isAdditionalAssignmentLayerUser($developerUserId = null): bool
    {
        $developerUserId = $developerUserId ?: HelpdeskSession::userId();

        if (! $developerUserId) {
            return false;
        }

        return DB::table('audit.dev_userdetails')
            ->where('devuserid', (string) $developerUserId)
            ->where('statusflag', 'Y')
            ->where('senior_flag', 'Y')
            ->exists();
    }
}
