<?php

namespace App\Services;

use App\Models\HelpdeskV2Ticket;
use Illuminate\Support\Facades\DB;

class HelpdeskV2DashboardService
{
    public function queryForRole(string $role)
    {
        $query = HelpdeskV2Ticket::query();
        $userId = HelpdeskV2Session::userId();
        $developerUserId = HelpdeskV2Session::developerUserId();
        $developerUserIds = collect([$developerUserId, $userId])
            ->filter()
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->values()
            ->all();

        return match ($role) {
            HelpdeskV2Session::ROLE_STATE_ADMIN => $query,
            HelpdeskV2Session::ROLE_NIC_ADMIN => $query->where(function ($builder) {
                $builder->whereIn(DB::raw("LOWER(REPLACE(COALESCE(forwarded_to_role, ''), ' ', '_'))"), HelpdeskV2Session::tableRoleAliases(HelpdeskV2Session::ROLE_NIC_ADMIN))
                    ->orWhereIn('status', [
                        HelpdeskV2Ticket::STATUS_FORWARDED_NIC_ADMIN,
                        HelpdeskV2Ticket::STATUS_ASSIGNED_LAYER_LEAD,
                        HelpdeskV2Ticket::STATUS_ASSIGNED_DEVELOPER,
                        HelpdeskV2Ticket::STATUS_DEVELOPER_IN_PROGRESS,
                        HelpdeskV2Ticket::STATUS_RETURNED_BY_DEVELOPER,
                        HelpdeskV2Ticket::STATUS_ASSIGNED_TESTER,
                        HelpdeskV2Ticket::STATUS_TESTING_IN_PROGRESS,
                        HelpdeskV2Ticket::STATUS_RETURNED_BY_TESTER,
                        HelpdeskV2Ticket::STATUS_RETURNED_TO_DEVELOPER,
                        HelpdeskV2Ticket::STATUS_RETURNED_TO_TESTER,
                        HelpdeskV2Ticket::STATUS_COMPLETED_LAYER_LEAD,
                        HelpdeskV2Ticket::STATUS_PENDING_NIC_ADMIN_REVIEW,
                        HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN_REVIEW,
                        HelpdeskV2Ticket::STATUS_RETURNED_TO_NIC_ADMIN,
                        'pending_state_admin_review',
                    ])
                    ->orWhere(function ($nicWorkingQueue) {
                        $nicWorkingQueue->whereIn('status', ['in_progress', 'resolved', 'need_clarification']);
                    })
                    ->orWhere(function ($developerQueue) {
                        $developerQueue->whereIn(DB::raw("LOWER(REPLACE(COALESCE(forwarded_to_role, ''), ' ', '_'))"), [
                            'developer',
                            'layer_lead',
                            'additional_layer',
                            'tester',
                        ]);
                    });
            }),
            HelpdeskV2Session::ROLE_LAYER_LEAD => $query->where(function ($layerLeadQuery) use ($developerUserIds) {
                $layerLeadQuery->whereIn('assigned_to_userid', $developerUserIds)
                    ->orWhereExists(function ($assignmentQuery) use ($developerUserIds) {
                        $assignmentQuery->select(DB::raw(1))
                            ->from('audit.helpdesk_ticket_assignments as hta')
                            ->whereColumn('hta.ticket_id', 'audit.helpdesk_tickets.id')
                            ->whereIn('hta.status', HelpdeskV2Session::tableRoleAliases(HelpdeskV2Session::ROLE_LAYER_LEAD))
                            ->whereIn('hta.developer_userid', $developerUserIds);
                    });
            }),
            HelpdeskV2Session::ROLE_DEVELOPER => $query
                ->whereNotIn('status', [
                    HelpdeskV2Ticket::STATUS_CLOSED,
                    HelpdeskV2Ticket::STATUS_REJECTED,
                    HelpdeskV2Ticket::STATUS_CANCELLED,
                ])
                ->where(function ($developerQuery) use ($developerUserIds) {
                    $developerQuery->whereIn('assigned_to_userid', $developerUserIds)
                        ->orWhereExists(function ($assignmentQuery) use ($developerUserIds) {
                            $assignmentQuery->select(DB::raw(1))
                                ->from('audit.helpdesk_ticket_assignments as hta')
                                ->whereColumn('hta.ticket_id', 'audit.helpdesk_tickets.id')
                                ->where('hta.status', 'assigned')
                                ->whereIn('hta.developer_userid', $developerUserIds);
                        });
                }),
            HelpdeskV2Session::ROLE_TESTER => $query->whereExists(function ($assignmentQuery) use ($developerUserIds) {
                $assignmentQuery->select(DB::raw(1))
                    ->from('audit.helpdesk_ticket_assignments as hta')
                    ->whereColumn('hta.ticket_id', 'audit.helpdesk_tickets.id')
                    ->where('hta.status', HelpdeskV2Session::ROLE_TESTER)
                    ->whereIn('hta.developer_userid', $developerUserIds);
            }),
            HelpdeskV2Session::ROLE_WATCHLIST => $query
                ->whereNotIn('status', [
                    HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN_REVIEW,
                    'pending_state_admin_review',
                ])
                ->whereExists(function ($assignmentQuery) use ($developerUserIds) {
                    $assignmentQuery->select(DB::raw(1))
                        ->from('audit.helpdesk_ticket_assignments as hta')
                        ->whereColumn('hta.ticket_id', 'audit.helpdesk_tickets.id')
                        ->where('hta.status', HelpdeskV2Session::ROLE_WATCHLIST)
                        ->whereIn('hta.developer_userid', $developerUserIds);
                }),
            default => $query->where('cams_userid', $userId),
        };
    }

    public function stats($query): array
    {
        return [
            'total' => (clone $query)->count(),
            'in_progress' => $this->applyCardFilter(clone $query, 'in_progress')->count(),
            'urgent' => $this->applyCardFilter(clone $query, 'urgent')->count(),
            'returned' => $this->applyCardFilter(clone $query, 'returned')->count(),
            'resolved' => (clone $query)->where('status', 'resolved')->count(),
            'closed' => $this->applyCardFilter(clone $query, 'closed')->count(),
            'resolved_closed' => $this->applyCardFilter(clone $query, 'resolved_closed')->count(),
        ];
    }

    public function applyCardFilter($query, ?string $filter)
    {
        $filter = (string) ($filter ?: 'in_progress');
        $closedStatuses = $this->closedStatuses();
        $returnedStatuses = $this->returnedStatuses();
        $finishedStatuses = array_merge($closedStatuses, ['resolved']);
        $activeReturnedStatuses = array_merge($finishedStatuses, $returnedStatuses);

        if ($filter === 'total') {
            return $query;
        }

        if ($filter === 'in_progress') {
            return $query
                ->whereNotIn('status', $activeReturnedStatuses)
                ->whereNotIn(DB::raw('LOWER(priority)'), ['critical', 'urgent']);
        }

        if ($filter === 'urgent' || $filter === 'critical') {
            return $query
                ->whereNotIn('status', $activeReturnedStatuses)
                ->whereIn(DB::raw('LOWER(priority)'), ['critical', 'urgent']);
        }

        if ($filter === 'returned') {
            return $query
                ->whereNotIn('status', $finishedStatuses)
                ->whereIn('status', $returnedStatuses);
        }

        if ($filter === 'resolved') {
            return $query->where('status', 'resolved');
        }

        if ($filter === 'closed') {
            return $query->whereIn('status', $closedStatuses);
        }

        if ($filter === 'resolved_closed') {
            return $query->where(function ($builder) use ($closedStatuses) {
                $builder->where('status', 'resolved')
                    ->orWhereIn('status', $closedStatuses);
            });
        }

        if (str_starts_with($filter, 'priority:')) {
            return $query->where(DB::raw('LOWER(priority)'), substr($filter, 9));
        }

        if (str_starts_with($filter, 'status:')) {
            return $query->where('status', substr($filter, 7));
        }

        if (str_starts_with($filter, 'developer:')) {
            $parts = explode(':', $filter, 3);
            $developerUserId = $parts[1] ?? '';
            $stage = $parts[2] ?? 'total';
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

            $this->whereLatestDeveloperAssignment($query, $developerUserId);

            if ($stage === 'pending') {
                return $query->whereIn('status', [
                    HelpdeskV2Ticket::STATUS_ASSIGNED_DEVELOPER,
                    HelpdeskV2Ticket::STATUS_RETURNED_TO_DEVELOPER,
                    'returned_to_developer',
                ]);
            }

            if ($stage === 'resolved') {
                return $query->whereIn('status', $resolvedStatuses);
            }

            if ($stage === 'returned') {
                return $query->whereIn('status', $returnedStatuses);
            }

            if ($stage === 'in_progress') {
                return $query
                    ->whereNotIn('status', $resolvedStatuses)
                    ->whereNotIn('status', $returnedStatuses);
            }

            return $query;
        }

        return $query;
    }

    private function closedStatuses(): array
    {
        return [
            HelpdeskV2Ticket::STATUS_CLOSED,
            HelpdeskV2Ticket::STATUS_REJECTED,
            HelpdeskV2Ticket::STATUS_CANCELLED,
        ];
    }

    private function returnedStatuses(): array
    {
        return [
            HelpdeskV2Ticket::STATUS_RETURNED_BY_DEVELOPER,
            'returned_by_developer',
            HelpdeskV2Ticket::STATUS_RETURNED_BY_TESTER,
            HelpdeskV2Ticket::STATUS_RETURNED_TO_DEVELOPER,
            'returned_to_developer',
            HelpdeskV2Ticket::STATUS_RETURNED_TO_TESTER,
            'returned_to_tester',
            HelpdeskV2Ticket::STATUS_RETURNED_TO_NIC_ADMIN,
        ];
    }

    private function sqlStringList(array $values): string
    {
        return collect($values)
            ->map(fn ($value) => "'".str_replace("'", "''", (string) $value)."'")
            ->implode(',');
    }

    private function whereLatestDeveloperAssignment($query, string $developerUserId): void
    {
        $assignmentStatuses = ['developer', 'assigned', 'returned', 'reassigned'];
        $assignmentStatusSql = $this->sqlStringList($assignmentStatuses);

        $query->whereExists(function ($assignmentQuery) use ($developerUserId, $assignmentStatuses, $assignmentStatusSql) {
            $assignmentQuery->select(DB::raw(1))
                ->from('audit.helpdesk_ticket_assignments as hta')
                ->whereColumn('hta.ticket_id', 'audit.helpdesk_tickets.id')
                ->where('hta.developer_userid', $developerUserId)
                ->whereIn('hta.status', $assignmentStatuses)
                ->whereRaw("hta.id = (
                    SELECT MAX(hta_latest.id)
                    FROM audit.helpdesk_ticket_assignments as hta_latest
                    WHERE hta_latest.ticket_id = audit.helpdesk_tickets.id
                    AND hta_latest.status IN ($assignmentStatusSql)
                )");
            });
    }
}
