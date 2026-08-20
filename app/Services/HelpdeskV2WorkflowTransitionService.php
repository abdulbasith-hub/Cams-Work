<?php

namespace App\Services;

use App\Models\HelpdeskTicketAssignment;
use App\Models\HelpdeskV2Comment;
use App\Models\HelpdeskV2Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class HelpdeskV2WorkflowTransitionService
{
    public function definitions(): array
    {
        return [
            'forward_to_nic' => [
                'label' => 'Forward to NIC Admin',
                'roles' => [HelpdeskV2Session::ROLE_STATE_ADMIN],
                'from' => [
                    'open',
                    HelpdeskV2Ticket::STATUS_SUBMITTED,
                    HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN,
                    HelpdeskV2Ticket::STATUS_REOPENED,
                ],
                'to' => HelpdeskV2Ticket::STATUS_FORWARDED_NIC_ADMIN,
                'pending_role' => HelpdeskV2Session::ROLE_NIC_ADMIN,
                'remarks' => true,
                'status_change' => true,
                'allowed_statuses' => ['in_progress', 'resolved', 'need_clarification'],
            ],
            'assign_layer_lead' => [
                'label' => 'Assign Senior Developer',
                'roles' => [HelpdeskV2Session::ROLE_NIC_ADMIN],
                'from' => [
                    'open',
                    'in_progress',
                    'resolved',
                    'need_clarification',
                    HelpdeskV2Ticket::STATUS_FORWARDED_NIC_ADMIN,
                    HelpdeskV2Ticket::STATUS_PENDING_NIC_ADMIN_REVIEW,
                    'pending_nic_admin_review',
                    HelpdeskV2Ticket::STATUS_COMPLETED_LAYER_LEAD,
                ],
                'to' => HelpdeskV2Ticket::STATUS_ASSIGNED_LAYER_LEAD,
                'pending_role' => HelpdeskV2Session::ROLE_LAYER_LEAD,
                'assign' => 'layer_lead',
                'remarks' => true,
            ],
            'assign_developer_from_nic' => [
                'label' => 'Assign to Developer',
                'roles' => [HelpdeskV2Session::ROLE_NIC_ADMIN],
                'from' => [
                    'open',
                    'in_progress',
                    HelpdeskV2Ticket::STATUS_FORWARDED_NIC_ADMIN,
                    HelpdeskV2Ticket::STATUS_PENDING_NIC_ADMIN_REVIEW,
                    'pending_nic_admin_review',
                    HelpdeskV2Ticket::STATUS_REOPENED,
                    HelpdeskV2Ticket::STATUS_RETURNED_BY_DEVELOPER,
                    'returned_by_developer',
                    HelpdeskV2Ticket::STATUS_RETURNED_BY_TESTER,
                    HelpdeskV2Ticket::STATUS_COMPLETED_LAYER_LEAD,
                ],
                'to' => HelpdeskV2Ticket::STATUS_ASSIGNED_DEVELOPER,
                'pending_role' => HelpdeskV2Session::ROLE_DEVELOPER,
                'assign' => 'developer',
                'remarks' => false,
            ],
            'update_nic_status' => [
                'label' => 'Update Status',
                'roles' => [HelpdeskV2Session::ROLE_NIC_ADMIN],
                'from' => [
                    'open',
                    'in_progress',
                    'resolved',
                    'need_clarification',
                    HelpdeskV2Ticket::STATUS_FORWARDED_NIC_ADMIN,
                    HelpdeskV2Ticket::STATUS_PENDING_NIC_ADMIN_REVIEW,
                    'pending_nic_admin_review',
                    HelpdeskV2Ticket::STATUS_REOPENED,
                    HelpdeskV2Ticket::STATUS_RETURNED_BY_DEVELOPER,
                    'returned_by_developer',
                    HelpdeskV2Ticket::STATUS_RETURNED_BY_TESTER,
                ],
                'to' => 'in_progress',
                'pending_role' => HelpdeskV2Session::ROLE_NIC_ADMIN,
                'remarks' => false,
                'status_change' => true,
                'allowed_statuses' => ['in_progress', 'resolved', 'need_clarification'],
            ],
            'assign_developer' => [
                'label' => 'Assign Developer',
                'roles' => [HelpdeskV2Session::ROLE_LAYER_LEAD],
                'from' => [
                    HelpdeskV2Ticket::STATUS_ASSIGNED_LAYER_LEAD,
                ],
                'to' => HelpdeskV2Ticket::STATUS_ASSIGNED_DEVELOPER,
                'pending_role' => HelpdeskV2Session::ROLE_DEVELOPER,
                'assign' => 'developer',
                'remarks' => true,
                'lead_owner_only' => true,
            ],
            'start_development' => [
                'label' => 'Start Development',
                'roles' => [HelpdeskV2Session::ROLE_DEVELOPER],
                'from' => [
                    HelpdeskV2Ticket::STATUS_ASSIGNED_DEVELOPER,
                ],
                'to' => HelpdeskV2Ticket::STATUS_DEVELOPER_IN_PROGRESS,
                'pending_role' => HelpdeskV2Session::ROLE_DEVELOPER,
            ],
            'developer_return' => [
                'label' => 'Return to Senior Developer',
                'roles' => [HelpdeskV2Session::ROLE_DEVELOPER],
                'from' => [
                    HelpdeskV2Ticket::STATUS_DEVELOPER_IN_PROGRESS,
                    'developer_in_progress',
                    HelpdeskV2Ticket::STATUS_RETURNED_TO_DEVELOPER,
                    'returned_to_developer',
                ],
                'to' => HelpdeskV2Ticket::STATUS_RETURNED_BY_DEVELOPER,
                'pending_role' => HelpdeskV2Session::ROLE_LAYER_LEAD,
                'remarks' => true,
                'layer_developer_only' => true,
            ],
            'developer_forward_to_lead' => [
                'label' => 'Forward to Senior Developer for Testing',
                'roles' => [HelpdeskV2Session::ROLE_DEVELOPER],
                'from' => [
                    'in_progress',
                    HelpdeskV2Ticket::STATUS_ASSIGNED_DEVELOPER,
                    HelpdeskV2Ticket::STATUS_DEVELOPER_IN_PROGRESS,
                    'developer_in_progress',
                    HelpdeskV2Ticket::STATUS_RETURNED_TO_DEVELOPER,
                    'returned_to_developer',
                ],
                'to' => HelpdeskV2Ticket::STATUS_RETURNED_BY_DEVELOPER,
                'pending_role' => HelpdeskV2Session::ROLE_LAYER_LEAD,
                'assign' => 'layer_lead',
                'remarks' => true,
                'direct_developer_only' => true,
                'non_lead_developer_only' => true,
            ],
            'lead_developer_forward_to_nic' => [
                'label' => 'Forward to NIC Admin',
                'roles' => [HelpdeskV2Session::ROLE_DEVELOPER],
                'from' => [
                    'in_progress',
                    HelpdeskV2Ticket::STATUS_ASSIGNED_DEVELOPER,
                    HelpdeskV2Ticket::STATUS_DEVELOPER_IN_PROGRESS,
                    'developer_in_progress',
                    HelpdeskV2Ticket::STATUS_RETURNED_TO_DEVELOPER,
                    'returned_to_developer',
                ],
                'to' => HelpdeskV2Ticket::STATUS_PENDING_NIC_ADMIN_REVIEW,
                'pending_role' => HelpdeskV2Session::ROLE_NIC_ADMIN,
                'remarks' => true,
                'direct_developer_only' => true,
                'lead_developer_only' => true,
            ],
            'return_to_developer' => [
                'label' => 'Return to Developer',
                'roles' => [HelpdeskV2Session::ROLE_LAYER_LEAD],
                'from' => [
                    HelpdeskV2Ticket::STATUS_RETURNED_BY_DEVELOPER,
                    'returned_by_developer',
                    HelpdeskV2Ticket::STATUS_RETURNED_BY_TESTER,
                ],
                'to' => HelpdeskV2Ticket::STATUS_RETURNED_TO_DEVELOPER,
                'pending_role' => HelpdeskV2Session::ROLE_DEVELOPER,
                'remarks' => true,
            ],
            'assign_tester' => [
                'label' => 'Assign Tester',
                'roles' => [HelpdeskV2Session::ROLE_LAYER_LEAD],
                'from' => [],
                'to' => HelpdeskV2Ticket::STATUS_ASSIGNED_TESTER,
                'pending_role' => HelpdeskV2Session::ROLE_TESTER,
                'assign' => 'tester',
                'remarks' => true,
            ],
            'start_testing' => [
                'label' => 'Start Testing',
                'roles' => [HelpdeskV2Session::ROLE_TESTER],
                'from' => [
                    HelpdeskV2Ticket::STATUS_ASSIGNED_TESTER,
                    HelpdeskV2Ticket::STATUS_RETURNED_TO_TESTER,
                ],
                'to' => HelpdeskV2Ticket::STATUS_TESTING_IN_PROGRESS,
                'pending_role' => HelpdeskV2Session::ROLE_TESTER,
            ],
            'tester_return' => [
                'label' => 'Return Test Result',
                'roles' => [HelpdeskV2Session::ROLE_TESTER],
                'from' => [HelpdeskV2Ticket::STATUS_TESTING_IN_PROGRESS],
                'to' => HelpdeskV2Ticket::STATUS_RETURNED_BY_TESTER,
                'pending_role' => HelpdeskV2Session::ROLE_LAYER_LEAD,
                'remarks' => true,
            ],
            'return_to_tester' => [
                'label' => 'Return to Tester',
                'roles' => [HelpdeskV2Session::ROLE_LAYER_LEAD],
                'from' => [HelpdeskV2Ticket::STATUS_RETURNED_BY_TESTER],
                'to' => HelpdeskV2Ticket::STATUS_RETURNED_TO_TESTER,
                'pending_role' => HelpdeskV2Session::ROLE_TESTER,
                'remarks' => true,
            ],
            'complete_layer' => [
                'label' => 'Mark Senior Developer Completed',
                'roles' => [HelpdeskV2Session::ROLE_LAYER_LEAD],
                'from' => [
                    HelpdeskV2Ticket::STATUS_RETURNED_BY_TESTER,
                ],
                'to' => HelpdeskV2Ticket::STATUS_COMPLETED_LAYER_LEAD,
                'pending_role' => HelpdeskV2Session::ROLE_LAYER_LEAD,
                'remarks' => true,
            ],
            'resolve_layer_to_nic' => [
                'label' => 'Forward to NIC Admin',
                'roles' => [HelpdeskV2Session::ROLE_LAYER_LEAD],
                'from' => [
                    HelpdeskV2Ticket::STATUS_ASSIGNED_LAYER_LEAD,
                    HelpdeskV2Ticket::STATUS_RETURNED_BY_DEVELOPER,
                    'returned_by_developer',
                    HelpdeskV2Ticket::STATUS_RETURNED_BY_TESTER,
                    HelpdeskV2Ticket::STATUS_COMPLETED_LAYER_LEAD,
                ],
                'to' => HelpdeskV2Ticket::STATUS_PENDING_NIC_ADMIN_REVIEW,
                'pending_role' => HelpdeskV2Session::ROLE_NIC_ADMIN,
                'remarks' => true,
            ],
            'forward_completed_to_nic' => [
                'label' => 'Forward Completed Ticket to NIC',
                'roles' => [HelpdeskV2Session::ROLE_LAYER_LEAD],
                'from' => [HelpdeskV2Ticket::STATUS_COMPLETED_LAYER_LEAD],
                'to' => HelpdeskV2Ticket::STATUS_PENDING_NIC_ADMIN_REVIEW,
                'pending_role' => HelpdeskV2Session::ROLE_NIC_ADMIN,
                'remarks' => true,
            ],
            'forward_to_state' => [
                'label' => 'Forward to State Admin',
                'roles' => [HelpdeskV2Session::ROLE_NIC_ADMIN],
                'from' => [
                    'in_progress',
                    'resolved',
                    'need_clarification',
                    HelpdeskV2Ticket::STATUS_FORWARDED_NIC_ADMIN,
                    HelpdeskV2Ticket::STATUS_PENDING_NIC_ADMIN_REVIEW,
                ],
                'to' => HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN_REVIEW,
                'pending_role' => HelpdeskV2Session::ROLE_STATE_ADMIN,
                'remarks' => true,
                'requires_nic_status_update' => true,
            ],
            'close' => [
                'label' => 'Update Final Status',
                'roles' => [HelpdeskV2Session::ROLE_STATE_ADMIN],
                'from' => [HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN_REVIEW],
                'to' => HelpdeskV2Ticket::STATUS_CLOSED,
                'pending_role' => null,
                'remarks' => true,
                'status_change' => true,
                'allowed_statuses' => [
                    HelpdeskV2Ticket::STATUS_CLOSED,
                ],
            ],
            'return_to_nic_admin' => [
                'label' => 'Return Ticket',
                'roles' => [HelpdeskV2Session::ROLE_STATE_ADMIN],
                'from' => [HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN_REVIEW],
                'to' => HelpdeskV2Ticket::STATUS_RETURNED_TO_NIC_ADMIN,
                'pending_role' => HelpdeskV2Session::ROLE_NIC_ADMIN,
                'remarks' => true,
                'status_change' => true,
                'allowed_statuses' => [
                    HelpdeskV2Ticket::STATUS_RETURNED_TO_NIC_ADMIN,
                ],
            ],
            'reopen' => [
                'label' => 'Reopen Ticket',
                'roles' => ['authorized_reopen'],
                'from' => [HelpdeskV2Ticket::STATUS_CLOSED],
                'to' => HelpdeskV2Ticket::STATUS_REOPENED,
                'pending_role' => HelpdeskV2Session::ROLE_STATE_ADMIN,
                'remarks' => true,
            ],
        ];
    }

    public function availableActions(HelpdeskV2Ticket $ticket): array
    {
        return collect($this->definitions())
            ->filter(fn (array $definition, string $action) => $this->canExecute($ticket, $action))
            ->all();
    }

    public function canExecute(HelpdeskV2Ticket $ticket, string $action): bool
    {
        $definition = $this->definitions()[$action] ?? null;

        if (!$definition || ! $this->statusMatches($ticket->status, $definition['from'])) {
            return false;
        }

        if (($definition['lead_owner_only'] ?? false) && ! $this->currentLeadOwnsTicket($ticket)) {
            return false;
        }

        if (($definition['layer_developer_only'] ?? false) && ! $this->shouldReturnDeveloperToLayer($ticket)) {
            return false;
        }

        if (($definition['direct_developer_only'] ?? false) && $this->shouldReturnDeveloperToLayer($ticket)) {
            return false;
        }

        if (($definition['non_lead_developer_only'] ?? false) && HelpdeskV2Session::isLayerLead()) {
            return false;
        }

        if (($definition['lead_developer_only'] ?? false) && ! HelpdeskV2Session::isLayerLead()) {
            return false;
        }

        if (($definition['requires_nic_status_update'] ?? false) && ! $this->hasNicStatusUpdateAfterLatestReceipt($ticket)) {
            return false;
        }

        foreach ($definition['roles'] as $role) {
            if ($role === 'authorized_reopen' && $this->canReopen($ticket)) {
                return true;
            }

            if ($this->currentActorMatchesRole($ticket, $role)) {
                return true;
            }
        }

        return false;
    }

    public function execute(HelpdeskV2Ticket $ticket, string $action, array $payload = []): HelpdeskV2Ticket
    {
        $definition = $this->definitions()[$action] ?? null;

        if (!$definition) {
            throw ValidationException::withMessages(['action' => 'Invalid workflow action.']);
        }

        $remarks = trim((string) ($payload['remarks'] ?? ''));
        if (($definition['remarks'] ?? false) && $remarks === '') {
            throw ValidationException::withMessages(['remarks' => 'Remarks are required for this action.']);
        }

        $updatedTicket = DB::transaction(function () use ($ticket, $action, $definition, $payload, $remarks) {
            $locked = HelpdeskV2Ticket::query()->lockForUpdate()->findOrFail($ticket->id);

            if (!$this->canExecute($locked, $action)) {
                if (($definition['requires_nic_status_update'] ?? false) && ! $this->hasNicStatusUpdateAfterLatestReceipt($locked)) {
                    throw ValidationException::withMessages(['ticket_status' => 'Please update the ticket status before forwarding.']);
                }

                throw ValidationException::withMessages(['action' => 'This action is not allowed for the current ticket status and role.']);
            }

            $previous = [
                'status' => $locked->status,
                'pending_role' => $locked->forwarded_to_role,
                'pending_userid' => $locked->assigned_to_userid,
                'pending_name' => $locked->assigned_to_name,
            ];

            $target = $this->resolveTarget($locked, $definition, $payload);
            $newStatus = $this->resolveNewStatus($definition, $payload);
            $pendingRole = $definition['pending_role'] ?? null;
            $assignmentRole = $definition['assign'] ?? null;

            if ($action === 'assign_developer_from_nic' && $this->hasAdditionalLayerSelected($payload)) {
                $newStatus = HelpdeskV2Ticket::STATUS_ASSIGNED_LAYER_LEAD;
                $pendingRole = HelpdeskV2Session::ROLE_LAYER_LEAD;
                $assignmentRole = 'layer_lead';
            }

            if ($action === 'developer_return' && ! $this->shouldReturnDeveloperToLayer($locked)) {
                $newStatus = HelpdeskV2Ticket::STATUS_PENDING_NIC_ADMIN_REVIEW;
                $pendingRole = HelpdeskV2Session::ROLE_NIC_ADMIN;
                $target = ['pending_userid' => null, 'pending_name' => null];
            }

            $locked->fill([
                'status' => $newStatus,
                'forwarded_to_role' => $pendingRole ? HelpdeskV2Session::tableRole($pendingRole) : null,
                'assigned_to_userid' => $target['pending_userid'],
                'assigned_to_name' => $target['pending_name'],
                'forwarded_at' => now('Asia/Kolkata'),
                'forward_notes' => $remarks ?: $locked->forward_notes,
            ]);

            if ($assignmentRole === 'layer_lead') {
                $this->recordAssignment($locked, HelpdeskV2Session::ROLE_LAYER_LEAD, $target, $remarks);
            }

            if ($assignmentRole === 'developer') {
                $assignmentNotes = $remarks;

                if (! empty($payload['expected_completion_date'])) {
                    $assignmentNotes = trim(($assignmentNotes ? $assignmentNotes.' ' : '').'Expected date: '.$payload['expected_completion_date']);
                }

                $this->recordAssignment($locked, 'assigned', $target, $assignmentNotes);
            }

            if ($assignmentRole === 'tester') {
                $this->recordAssignment($locked, HelpdeskV2Session::ROLE_TESTER, $target, $remarks);
            }

            if ($action === 'close') {
                $locked->forward_notes = trim((string) ($payload['resolution'] ?? $remarks));
                $locked->resolved_at = now('Asia/Kolkata');
            }

            if ($action === 'reopen') {
                $locked->resolved_at = null;
            }

            if (! in_array($newStatus, ['resolved', HelpdeskV2Ticket::STATUS_CLOSED, HelpdeskV2Ticket::STATUS_REJECTED, HelpdeskV2Ticket::STATUS_CANCELLED], true)) {
                $locked->resolved_at = null;
            }

            if ($newStatus === 'resolved') {
                $locked->resolved_at = now('Asia/Kolkata');
            }

            $locked->save();

            $actorRole = $this->actorRoleForAction($locked, $action);
            $isTechnicalActor = in_array($actorRole, [
                HelpdeskV2Session::ROLE_LAYER_LEAD,
                HelpdeskV2Session::ROLE_DEVELOPER,
                HelpdeskV2Session::ROLE_TESTER,
            ], true);

            $this->recordTimeline([
                'ticket_id' => $locked->id,
                'action' => $action,
                'actor_userid' => $isTechnicalActor ? HelpdeskV2Session::developerUserId() : HelpdeskV2Session::userId(),
                'actor_name' => $isTechnicalActor
                    ? (HelpdeskV2Session::developerUser()->devename ?? HelpdeskV2Session::userName())
                    : HelpdeskV2Session::userName(),
                'actor_role' => $actorRole,
                'previous_status' => $previous['status'],
                'new_status' => $newStatus,
                'remarks' => $remarks,
                'assigned_from_role' => $previous['pending_role'],
                'assigned_from_userid' => $previous['pending_userid'],
                'assigned_from_name' => $previous['pending_name'],
                'assigned_to_role' => $locked->pending_role,
                'assigned_to_userid' => $locked->pending_userid,
                'assigned_to_name' => $locked->pending_name,
            ]);

            return $locked->fresh(['comments', 'devComments', 'assignments']);
        });

        app(HelpdeskV2NotificationService::class)->ticketTransitioned($updatedTicket, $action, $remarks);

        return $updatedTicket;
    }

    private function resolveTarget(HelpdeskV2Ticket $ticket, array $definition, array $payload): array
    {
        $pendingRole = $definition['pending_role'] ?? null;

        if (!$pendingRole) {
            return ['pending_userid' => null, 'pending_name' => null];
        }

        if (($definition['assign'] ?? null) === 'layer_lead') {
            $person = HelpdeskV2Session::developerById((string) ($payload['layer_lead_userid'] ?? ''));

            if (!$person || ($person->senior_flag ?? null) !== 'Y') {
                throw ValidationException::withMessages(['layer_lead_userid' => 'Select a valid Senior Developer.']);
            }

            return ['pending_userid' => (string) $person->devuserid, 'pending_name' => $person->devename];
        }

        if (($definition['assign'] ?? null) === 'developer') {
            $additionalLayerUserId = trim((string) ($payload['additional_layer_userid'] ?? ''));
            $selectedDeveloperUserId = trim((string) ($payload['developer_userid'] ?? ''));

            if ($additionalLayerUserId !== '' && $selectedDeveloperUserId !== '') {
                throw ValidationException::withMessages(['developer_userid' => 'Select either Senior Developer or Developer, not both.']);
            }

            $developerUserId = $additionalLayerUserId !== ''
                ? $additionalLayerUserId
                : $selectedDeveloperUserId;
            $person = HelpdeskV2Session::developerById($developerUserId);

            if (!$person) {
                throw ValidationException::withMessages(['developer_userid' => 'Select a valid Developer.']);
            }

            if ($additionalLayerUserId !== '' && ($person->senior_flag ?? null) !== 'Y') {
                throw ValidationException::withMessages(['additional_layer_userid' => 'Select a valid Senior Developer.']);
            }

            return ['pending_userid' => (string) $person->devuserid, 'pending_name' => $person->devename];
        }

        if (($definition['assign'] ?? null) === 'tester') {
            $person = HelpdeskV2Session::developerById((string) ($payload['tester_userid'] ?? ''));

            if (!$person) {
                throw ValidationException::withMessages(['tester_userid' => 'Select a valid Tester.']);
            }

            return ['pending_userid' => (string) $person->devuserid, 'pending_name' => $person->devename];
        }

        return match ($pendingRole) {
            HelpdeskV2Session::ROLE_LAYER_LEAD => ['pending_userid' => $ticket->layer_lead_userid, 'pending_name' => $ticket->layer_lead_name],
            HelpdeskV2Session::ROLE_DEVELOPER => ['pending_userid' => $ticket->developer_userid, 'pending_name' => $ticket->developer_name],
            HelpdeskV2Session::ROLE_TESTER => ['pending_userid' => $ticket->tester_userid, 'pending_name' => $ticket->tester_name],
            default => ['pending_userid' => null, 'pending_name' => null],
        };
    }

    private function hasAdditionalLayerSelected(array $payload): bool
    {
        return trim((string) ($payload['additional_layer_userid'] ?? '')) !== '';
    }

    private function shouldReturnDeveloperToLayer(HelpdeskV2Ticket $ticket): bool
    {
        if (! $ticket->layer_lead_userid) {
            return false;
        }

        if ($ticket->developer_userid
            && (string) $ticket->developer_userid === (string) $ticket->layer_lead_userid) {
            return false;
        }

        $developerAssignment = DB::table('audit.helpdesk_ticket_assignments')
            ->where('ticket_id', $ticket->id)
            ->where('status', 'assigned')
            ->latest('assigned_at')
            ->latest('id')
            ->first(['assigned_by_userid']);

        if (! $developerAssignment) {
            return true;
        }

        return (string) $developerAssignment->assigned_by_userid === (string) $ticket->layer_lead_userid;
    }

    private function resolveNewStatus(array $definition, array $payload): string
    {
        if (! ($definition['status_change'] ?? false)) {
            return $definition['to'];
        }

        $status = $this->normalizeStatus(trim((string) ($payload['ticket_status'] ?? '')));
        $allowedStatuses = $definition['allowed_statuses'] ?? [];

        if ($status === '' || ! $this->statusMatches($status, $allowedStatuses)) {
            throw ValidationException::withMessages(['ticket_status' => 'Select a valid ticket status.']);
        }

        return $status;
    }

    private function statusMatches(?string $status, array $allowedStatuses): bool
    {
        if (in_array($status, $allowedStatuses, true)) {
            return true;
        }

        $normalizedStatus = $this->normalizeStatus($status);

        return collect($allowedStatuses)
            ->contains(fn ($allowedStatus) => $this->normalizeStatus($allowedStatus) === $normalizedStatus);
    }

    private function normalizeStatus(?string $status): string
    {
        $normalized = Str::of((string) $status)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();

        foreach (HelpdeskV2Ticket::STATUS_LABELS as $key => $label) {
            $labelKey = Str::of((string) $label)
                ->lower()
                ->replaceMatches('/[^a-z0-9]+/', '_')
                ->trim('_')
                ->toString();

            if ($normalized === $labelKey) {
                return (string) $key;
            }
        }

        return match ($normalized) {
            'developer_in_progress' => HelpdeskV2Ticket::STATUS_DEVELOPER_IN_PROGRESS,
            'returned_by_developer' => HelpdeskV2Ticket::STATUS_RETURNED_BY_DEVELOPER,
            'returned_to_developer' => HelpdeskV2Ticket::STATUS_RETURNED_TO_DEVELOPER,
            'pending_nic_admin_review' => HelpdeskV2Ticket::STATUS_PENDING_NIC_ADMIN_REVIEW,
            'pending_state_admin_review' => HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN_REVIEW,
            default => $normalized,
        };
    }

    private function currentActorMatchesRole(HelpdeskV2Ticket $ticket, string $role): bool
    {
        $userId = HelpdeskV2Session::userId();
        $developerUserId = HelpdeskV2Session::developerUserId();

        return match ($role) {
            HelpdeskV2Session::ROLE_STATE_ADMIN => HelpdeskV2Session::isStateAdmin(),
            HelpdeskV2Session::ROLE_NIC_ADMIN => HelpdeskV2Session::isNicAdmin() && ! HelpdeskV2Session::isStateAdmin(),
            HelpdeskV2Session::ROLE_LAYER_LEAD => HelpdeskV2Session::isLayerLead() && (string) $ticket->layer_lead_userid === (string) $developerUserId,
            HelpdeskV2Session::ROLE_DEVELOPER => (string) $ticket->developer_userid === (string) $developerUserId,
            HelpdeskV2Session::ROLE_TESTER => (string) $ticket->tester_userid === (string) $developerUserId,
            HelpdeskV2Session::ROLE_WATCHLIST => false,
            HelpdeskV2Session::ROLE_USER => (string) $ticket->created_by_userid === (string) $userId,
            default => false,
        };
    }

    private function currentLeadOwnsTicket(HelpdeskV2Ticket $ticket): bool
    {
        if (! HelpdeskV2Session::isLayerLead()) {
            return false;
        }

        return $ticket->pending_role === HelpdeskV2Session::ROLE_LAYER_LEAD
            && (string) $ticket->layer_lead_userid === (string) HelpdeskV2Session::developerUserId();
    }

    private function canReopen(HelpdeskV2Ticket $ticket): bool
    {
        return HelpdeskV2Session::isStateAdmin()
            || HelpdeskV2Session::isNicAdmin()
            || (string) $ticket->created_by_userid === (string) HelpdeskV2Session::userId();
    }

    private function hasNicStatusUpdateAfterLatestReceipt(HelpdeskV2Ticket $ticket): bool
    {
        $receiptActions = [
            'forward_to_nic',
            'lead_developer_forward_to_nic',
            'resolve_layer_to_nic',
            'forward_completed_to_nic',
            'developer_return',
            'return_to_nic_admin',
        ];
        $receiptAt = $this->latestTimelineActionAt($ticket, $receiptActions) ?: $ticket->created_at;

        return DB::table('audit.helpdesk_ticket_comments')
            ->where('ticket_id', $ticket->id)
            ->where('comment', 'like', '[UPDATE_NIC_STATUS]%')
            ->when($receiptAt, function ($query) use ($receiptAt) {
                $query->where('created_at', '>=', $receiptAt);
            })
            ->exists();
    }

    private function latestTimelineActionAt(HelpdeskV2Ticket $ticket, array $actions): ?string
    {
        $applyActionFilter = function ($query) use ($actions) {
            $query->where(function ($actionQuery) use ($actions) {
                foreach ($actions as $action) {
                    $actionQuery->orWhere('comment', 'like', '['.Str::upper($action).']%');
                }
            });
        };

        $mainActionAt = DB::table('audit.helpdesk_ticket_comments')
            ->where('ticket_id', $ticket->id)
            ->where($applyActionFilter)
            ->max('created_at');
        $devActionAt = DB::table('audit.helpdesk_ticket_comments_dev')
            ->where('ticket_id', $ticket->id)
            ->where($applyActionFilter)
            ->max('created_at');

        return collect([$mainActionAt, $devActionAt])
            ->filter()
            ->max();
    }

    private function actorRoleForAction(HelpdeskV2Ticket $ticket, string $action): string
    {
        foreach (($this->definitions()[$action]['roles'] ?? []) as $role) {
            if ($role !== 'authorized_reopen' && $this->currentActorMatchesRole($ticket, $role)) {
                return $role;
            }
        }

        return HelpdeskV2Session::role();
    }

    private function recordAssignment(HelpdeskV2Ticket $ticket, string $role, array $target, string $remarks): void
    {
        HelpdeskTicketAssignment::create([
            'ticket_id' => $ticket->id,
            'assigned_by_userid' => $this->assignmentActorUserId(),
            'assigned_by_name' => HelpdeskV2Session::userName(),
            'developer_userid' => $target['pending_userid'],
            'developer_name' => $target['pending_name'],
            'notes' => $remarks,
            'status' => $role,
            'assigned_at' => now('Asia/Kolkata'),
            'created_at' => now('Asia/Kolkata'),
            'updated_at' => now('Asia/Kolkata'),
        ]);
    }

    private function assignmentActorUserId(): ?string
    {
        if (! HelpdeskV2Session::isStateAdmin() && ! HelpdeskV2Session::isNicAdmin() && HelpdeskV2Session::isDeveloperPerson()) {
            return HelpdeskV2Session::developerUserId();
        }

        return HelpdeskV2Session::userId();
    }

    private function recordTimeline(array $data): void
    {
        $label = HelpdeskV2Ticket::labelFor($data['new_status'] ?? null);
        $previous = $data['previous_status'] ? HelpdeskV2Ticket::labelFor($data['previous_status']) : 'None';
        $assignedTo = trim((string) ($data['assigned_to_name'] ?: $data['assigned_to_role'] ?: '-'));
        $remarks = trim((string) ($data['remarks'] ?? ''));
        $comment = '['.strtoupper((string) $data['action']).'] '.$previous.' -> '.$label.'. Assigned to: '.$assignedTo.'.';

        if ($remarks !== '') {
            $comment .= ' Remarks: '.$remarks;
        }

        $table = in_array($data['actor_role'], [HelpdeskV2Session::ROLE_DEVELOPER, HelpdeskV2Session::ROLE_TESTER], true)
            ? 'audit.helpdesk_ticket_comments_dev'
            : 'audit.helpdesk_ticket_comments';

        $payload = [
            'ticket_id' => $data['ticket_id'],
            'cams_userid' => $data['actor_userid'],
            'user_name' => $data['actor_name'],
            'user_role' => $data['actor_role'],
            'comment' => $comment,
            'created_at' => now('Asia/Kolkata'),
            'updated_at' => now('Asia/Kolkata'),
        ];

        if ($table === 'audit.helpdesk_ticket_comments') {
            $payload['is_internal'] = true;
        }

        DB::table($table)->insert($payload);
    }
}
