<?php

namespace App\Http\Controllers;

use App\Http\Requests\HelpdeskV2StoreCommentRequest;
use App\Http\Requests\HelpdeskV2StoreTicketRequest;
use App\Http\Requests\HelpdeskV2TransitionTicketRequest;
use App\Http\Requests\HelpdeskV2UpdateTicketRequest;
use App\Models\CommonModel;
use App\Models\HelpdeskTicketAssignment;
use App\Models\HelpdeskTicketDevComment;
use App\Models\HelpdeskTicket;
use App\Models\HelpdeskV2Comment;
use App\Models\HelpdeskV2Ticket;
use App\Services\HelpdeskV2AttachmentService;
use App\Services\HelpdeskV2DashboardService;
use App\Services\HelpdeskV2NotificationService;
use App\Services\HelpdeskV2Session;
use App\Services\HelpdeskV2WorkflowTransitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class HelpdeskV2TicketController extends Controller
{
    private array $devActorCache = [];

    public function index(Request $request, HelpdeskV2DashboardService $dashboardService)
    {
        $role = $request->string('role', HelpdeskV2Session::role())->toString();
        abort_unless($this->canOpenRole($role), 403);

        $baseQuery = $dashboardService->queryForRole($role);
        $priorityOptions = $this->priorityOptions(clone $baseQuery);
        $showTechnicalStatuses = $this->showsTechnicalStatusFilters($role);
        $statusOptions = $this->statusOptions(clone $baseQuery, $showTechnicalStatuses);
        $query = clone $baseQuery;

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($builder) use ($search) {
                $like = '%'.Str::lower($search).'%';
                $builder->whereRaw('LOWER(ticket_number) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(subject) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(request_type) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(category) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(status) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(user_name) LIKE ?', [$like]);
            });
        }

        if ($request->filled('priority')) {
            $query->whereRaw('LOWER(priority) = ?', [Str::lower((string) $request->input('priority'))]);
        }

        if ($request->filled('status')) {
            $query->whereIn('status', $this->rawStatusesForMainStatus((string) $request->input('status')));
        }

        $leadStage = null;
        $leadStageCounts = [];

        if ($role === HelpdeskV2Session::ROLE_LAYER_LEAD) {
            $requestedStage = $request->string('stage')->toString();
            $leadStage = in_array($requestedStage, ['development', 'testing'], true) ? $requestedStage : null;
            $stageBaseQuery = clone $query;
            $leadStageCounts = [
                'all' => (clone $stageBaseQuery)->count(),
                'development' => (clone $stageBaseQuery)->whereIn('status', $this->leadDevelopmentStatuses())->count(),
                'testing' => (clone $stageBaseQuery)->whereIn('status', $this->leadTestingStatuses())->count(),
            ];

            if ($leadStage) {
                $query->whereIn('status', $leadStage === 'testing'
                    ? $this->leadTestingStatuses()
                    : $this->leadDevelopmentStatuses());
            }
        }

        $tickets = $query
            ->with(['comments', 'devComments', 'assignments'])
            ->latest('updated_at')
            ->latest('id')
            ->get();

        return view('helpdesk-v2.tickets.index', [
            'tickets' => $tickets,
            'role' => $role,
            'statuses' => $statusOptions,
            'priorities' => $priorityOptions,
            'leadStage' => $leadStage,
            'leadStageCounts' => $leadStageCounts,
        ]);
    }

    public function create()
    {
        $planDetails = collect();
        $showDepartmentSelect = $this->canSelectTicketDepartment();
        $deptCode = $showDepartmentSelect
            ? (string) old('deptcode', HelpdeskV2Session::deptCode())
            : HelpdeskV2Session::deptCode();

        if ($deptCode) {
            $planDetails = collect(CommonModel::getplandetailsforreport($deptCode))->values();
        }

        [$financialYears, $auditQuarters] = $this->buildPlanOptions($planDetails);

        return view('helpdesk-v2.tickets.create', [
            'categories' => HelpdeskTicket::CATEGORY_OPTIONS,
            'requestTypes' => HelpdeskTicket::REQUEST_TYPE_OPTIONS,
            'priorities' => [
                'low' => 'Low',
                'medium' => 'Medium',
                'high' => 'High',
                'urgent' => 'Urgent',
            ],
            'financialYears' => $financialYears,
            'auditQuarters' => $auditQuarters,
            'departmentOptions' => $showDepartmentSelect ? $this->departmentOptions() : collect(),
            'showDepartmentSelect' => $showDepartmentSelect,
            'selectedDeptCode' => $deptCode,
            'submissionToken' => (string) Str::uuid(),
        ]);
    }

    public function planDetails(Request $request)
    {
        $deptCode = $this->selectedTicketDeptCode($request->string('deptcode')->toString());

        if (! $deptCode) {
            return response()->json([
                'financialYears' => [],
                'auditQuarters' => [],
            ]);
        }

        [$financialYears, $auditQuarters] = $this->buildPlanOptions(
            collect(CommonModel::getplandetailsforreport($deptCode))->values()
        );

        return response()->json([
            'financialYears' => $financialYears->values(),
            'auditQuarters' => $auditQuarters->values(),
        ]);
    }

    public function store(HelpdeskV2StoreTicketRequest $request, HelpdeskV2AttachmentService $attachmentService, HelpdeskV2NotificationService $notificationService)
    {
        $validated = $request->validated();
        $deptCode = $this->selectedTicketDeptCode($validated['deptcode'] ?? null);
        $department = $deptCode ? $this->departmentByCode($deptCode) : null;

        if (! $deptCode || ! $department) {
            throw ValidationException::withMessages(['deptcode' => 'Select a valid department.']);
        }

        $this->validateTicketPlanSelection($deptCode, (string) $validated['financial_year'], (string) $validated['audit_quarter']);

        $ticket = DB::transaction(function () use ($validated, $request, $attachmentService, $deptCode, $department) {
            $ticket = HelpdeskV2Ticket::create([
                'cams_userid' => HelpdeskV2Session::userId(),
                'user_name' => HelpdeskV2Session::userName(),
                'user_email' => HelpdeskV2Session::email(),
                'deptcode' => $deptCode,
                'department_name' => $department->deptesname ?? null,
                'financialyearcode' => $validated['financial_year'],
                'planmappingid' => $validated['audit_quarter'],
                'institution' => $validated['institution'] ?? null,
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'request_type' => $validated['request_type'],
                'category' => $validated['category'],
                'priority' => $validated['priority'],
                'status' => HelpdeskV2Ticket::STATUS_SUBMITTED,
                'forwarded_to_role' => HelpdeskV2Session::tableRole(HelpdeskV2Session::ROLE_STATE_ADMIN),
                'forwarded_at' => now('Asia/Kolkata'),
                'forward_notes' => null,
                'attachments' => [],
            ]);

            $attachmentService->storeMany($ticket, $request->file('attachments', []));

            HelpdeskV2Comment::create([
                'ticket_id' => $ticket->id,
                'cams_userid' => HelpdeskV2Session::userId(),
                'user_name' => HelpdeskV2Session::userName(),
                'user_role' => HelpdeskV2Session::role(),
                'comment' => '[CREATED] Ticket created and forwarded to State Admin.',
                'is_internal' => false,
                'created_at' => now('Asia/Kolkata'),
                'updated_at' => now('Asia/Kolkata'),
            ]);

            return $ticket->fresh(['comments', 'devComments', 'assignments']);
        });

        $notificationService->ticketCreated($ticket);

        return redirect()
            ->route('helpdesk-v2.tickets.show', $ticket)
            ->with('success', 'Ticket created successfully.');
    }

    public function show(Request $request, HelpdeskV2Ticket $ticket, HelpdeskV2WorkflowTransitionService $workflow)
    {
        $role = $request->string('role', HelpdeskV2Session::role())->toString();
        abort_unless($this->canOpenRole($role), 403);

        $ticket->load(['comments', 'devComments', 'assignments']);
        abort_if($role === HelpdeskV2Session::ROLE_WATCHLIST && $this->ticketAtStateAdminReview($ticket), 403);
        abort_unless($this->canView($ticket), 403);

        return view('helpdesk-v2.tickets.show', [
            'ticket' => $ticket,
            'role' => $role,
            'canEdit' => $this->canEdit($ticket),
            'flowSteps' => $this->flowSteps($ticket, $role),
            'timeline' => $this->timeline($ticket, $role),
            'actions' => $this->actionsForRole($workflow->availableActions($ticket), $role, $ticket),
            'categories' => collect(HelpdeskV2Ticket::CATEGORY_OPTIONS)
                ->prepend((string) $ticket->category)
                ->filter()
                ->unique()
                ->values(),
            'priorities' => HelpdeskV2Ticket::PRIORITIES,
            'layerLeads' => HelpdeskV2Session::activeLayerLeads(),
            'developers' => HelpdeskV2Session::activeDevelopers(),
            'watchlistDevelopers' => $this->watchlistCandidates($ticket),
            'testers' => HelpdeskV2Session::activeTesters(),
            'watchlistLocked' => $this->ticketAtStateAdminReview($ticket),
        ]);
    }

    public function update(HelpdeskV2UpdateTicketRequest $request, HelpdeskV2Ticket $ticket)
    {
        abort_unless($this->canEdit($ticket), 403);

        DB::transaction(function () use ($request, $ticket) {
            $ticket->update([
                'subject' => $request->validated('subject'),
                'description' => $request->validated('description'),
                'request_type' => $request->validated('request_type'),
                'category' => $request->validated('category'),
                'priority' => $request->validated('priority'),
                'forward_notes' => $request->validated('forward_notes'),
                'updated_at' => now('Asia/Kolkata'),
            ]);

            HelpdeskV2Comment::create([
                'ticket_id' => $ticket->id,
                'cams_userid' => HelpdeskV2Session::userId(),
                'user_name' => HelpdeskV2Session::userName(),
                'user_role' => HelpdeskV2Session::role(),
                'comment' => '[UPDATED] Ticket information edited.',
                'is_internal' => true,
                'created_at' => now('Asia/Kolkata'),
                'updated_at' => now('Asia/Kolkata'),
            ]);
        });

        return redirect()
            ->route('helpdesk-v2.tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully.');
    }

    public function transition(HelpdeskV2TransitionTicketRequest $request, HelpdeskV2Ticket $ticket, string $action, HelpdeskV2WorkflowTransitionService $workflow)
    {
        abort_unless($this->canView($ticket), 403);

        $workflow->execute($ticket, $action, $request->validated());

        $role = $request->string('role', HelpdeskV2Session::role())->toString();

        return redirect()
            ->route('helpdesk-v2.tickets.show', ['ticket' => $ticket, 'role' => $role])
            ->with('success', 'Workflow action completed.');
    }

    public function comment(HelpdeskV2StoreCommentRequest $request, HelpdeskV2Ticket $ticket)
    {
        abort_unless($this->canView($ticket), 403);
        abort_if($this->ticketCollaborationLocked($ticket), 403);

        $role = $request->string('role', HelpdeskV2Session::role())->toString();
        abort_unless($this->canOpenRole($role), 403);

        $visibility = $request->validated('visibility');

        if (in_array($role, [HelpdeskV2Session::ROLE_LAYER_LEAD, HelpdeskV2Session::ROLE_DEVELOPER, HelpdeskV2Session::ROLE_TESTER], true)
            || $visibility === 'developer_to_nic') {
            $developer = HelpdeskV2Session::developerUser();

            HelpdeskTicketDevComment::create([
                'ticket_id' => $ticket->id,
                'cams_userid' => HelpdeskV2Session::developerUserId(),
                'user_name' => $developer->devename ?? HelpdeskV2Session::userName(),
                'user_role' => $role,
                'comment' => $request->validated('comment'),
                'created_at' => now('Asia/Kolkata'),
                'updated_at' => now('Asia/Kolkata'),
            ]);
        } else {
            HelpdeskV2Comment::create([
                'ticket_id' => $ticket->id,
                'cams_userid' => HelpdeskV2Session::userId(),
                'user_name' => HelpdeskV2Session::userName(),
                'user_role' => $role,
                'comment' => $request->validated('comment'),
                'is_internal' => $visibility === 'internal',
                'created_at' => now('Asia/Kolkata'),
                'updated_at' => now('Asia/Kolkata'),
            ]);
        }

        return redirect()
            ->route('helpdesk-v2.tickets.show', ['ticket' => $ticket, 'role' => $role])
            ->with('success', 'Comment added.');
    }

    public function forwardDeveloperComment(HelpdeskV2Ticket $ticket, HelpdeskTicketDevComment $devComment)
    {
        abort_unless(HelpdeskV2Session::isNicAdmin() && ! HelpdeskV2Session::isStateAdmin(), 403);
        abort_unless((int) $devComment->ticket_id === (int) $ticket->id, 404);

        $alreadyForwarded = HelpdeskV2Comment::query()
            ->where('ticket_id', $ticket->id)
            ->where('comment', $devComment->comment)
            ->where('is_internal', false)
            ->exists();

        if (! $alreadyForwarded) {
            HelpdeskV2Comment::create([
                'ticket_id' => $ticket->id,
                'cams_userid' => HelpdeskV2Session::userId(),
                'user_name' => HelpdeskV2Session::userName(),
                'user_role' => HelpdeskV2Session::ROLE_NIC_ADMIN,
                'comment' => $devComment->comment,
                'is_internal' => false,
                'created_at' => now('Asia/Kolkata'),
                'updated_at' => now('Asia/Kolkata'),
            ]);
        }

        return redirect()
            ->route('helpdesk-v2.tickets.show', $ticket)
            ->with('success', 'Developer comment forwarded to State Admin.');
    }

    public function watchlist(Request $request, HelpdeskV2Ticket $ticket)
    {
        abort_unless(HelpdeskV2Session::isNicAdmin() && ! HelpdeskV2Session::isStateAdmin(), 403);
        abort_unless($this->canView($ticket), 403);
        abort_if($this->ticketCollaborationLocked($ticket), 403);
        abort_if($this->ticketAtStateAdminReview($ticket), 403);

        $validated = $request->validate([
            'watchlist_userid' => ['required', 'string', 'max:80'],
        ]);

        $developer = HelpdeskV2Session::developerById((string) $validated['watchlist_userid']);

        if (! $developer) {
            return back()->withErrors(['watchlist_userid' => 'Selected watchlist user is invalid.'])->withInput();
        }

        if ((string) $ticket->developer_userid === (string) $developer->devuserid) {
            return back()->withErrors(['watchlist_userid' => 'Assigned developer cannot be added to watchlist.'])->withInput();
        }

        $alreadyWatching = HelpdeskTicketAssignment::query()
            ->where('ticket_id', $ticket->id)
            ->where('developer_userid', (string) $developer->devuserid)
            ->where('status', 'watchlist')
            ->exists();

        if (! $alreadyWatching) {
            $now = now('Asia/Kolkata');

            HelpdeskTicketAssignment::create([
                'ticket_id' => $ticket->id,
                'assigned_by_userid' => HelpdeskV2Session::userId(),
                'assigned_by_name' => HelpdeskV2Session::userName(),
                'developer_userid' => (string) $developer->devuserid,
                'developer_name' => $developer->devename,
                'notes' => 'Watchlist access',
                'status' => 'watchlist',
                'assigned_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            HelpdeskTicketDevComment::create([
                'ticket_id' => $ticket->id,
                'cams_userid' => HelpdeskV2Session::userId(),
                'user_name' => HelpdeskV2Session::userName(),
                'user_role' => HelpdeskV2Session::ROLE_NIC_ADMIN,
                'comment' => 'Watchlist added: '.$developer->devename.'.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return back()->with(
            $alreadyWatching ? 'warning' : 'success',
            $alreadyWatching ? 'Developer is already in the watchlist.' : 'Developer added to watchlist.'
        );
    }

    public function removeWatchlist(Request $request, HelpdeskV2Ticket $ticket)
    {
        abort_unless(HelpdeskV2Session::isNicAdmin() && ! HelpdeskV2Session::isStateAdmin(), 403);
        abort_unless($this->canView($ticket), 403);
        abort_if($this->ticketCollaborationLocked($ticket), 403);
        abort_if($this->ticketAtStateAdminReview($ticket), 403);

        $validated = $request->validate([
            'watchlist_userid' => ['required', 'string', 'max:80'],
        ]);

        $watchlist = HelpdeskTicketAssignment::query()
            ->where('ticket_id', $ticket->id)
            ->where('developer_userid', (string) $validated['watchlist_userid'])
            ->where('status', 'watchlist')
            ->latest('assigned_at')
            ->latest('id')
            ->first();

        if (! $watchlist) {
            return back()->with('warning', 'Watchlist user is already removed.');
        }

        $watchlistName = $watchlist->developer_name ?: 'Watchlist user';
        $watchlist->delete();

        HelpdeskTicketDevComment::create([
            'ticket_id' => $ticket->id,
            'cams_userid' => HelpdeskV2Session::userId(),
            'user_name' => HelpdeskV2Session::userName(),
            'user_role' => HelpdeskV2Session::ROLE_NIC_ADMIN,
            'comment' => 'Watchlist removed: '.$watchlistName.'.',
            'created_at' => now('Asia/Kolkata'),
            'updated_at' => now('Asia/Kolkata'),
        ]);

        return back()->with('success', 'Watchlist user removed.');
    }

    public function downloadAttachment(HelpdeskV2Ticket $ticket, int $index, HelpdeskV2AttachmentService $attachmentService)
    {
        abort_unless($this->canView($ticket), 403);

        return $attachmentService->download($ticket, $index);
    }

    private function canView(HelpdeskV2Ticket $ticket): bool
    {
        $userId = HelpdeskV2Session::userId();
        $developerUserId = HelpdeskV2Session::developerUserId();
        $developerUserIds = collect([$developerUserId, $userId])
            ->filter()
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->all();

        return HelpdeskV2Session::isStateAdmin()
            || HelpdeskV2Session::isNicAdmin()
            || (string) $ticket->created_by_userid === (string) $userId
            || (string) $ticket->layer_lead_userid === (string) $developerUserId
            || (string) $ticket->developer_userid === (string) $developerUserId
            || (string) $ticket->tester_userid === (string) $developerUserId
            || (! $this->ticketAtStateAdminReview($ticket) && $ticket->assignments->contains(function ($assignment) use ($developerUserIds) {
                return $assignment->status === HelpdeskV2Session::ROLE_WATCHLIST
                    && in_array((string) $assignment->developer_userid, $developerUserIds, true);
            }));
    }

    private function canEdit(HelpdeskV2Ticket $ticket): bool
    {
        if (! $this->canView($ticket)) {
            return false;
        }

        if ($this->ticketCollaborationLocked($ticket)) {
            return false;
        }

        return HelpdeskV2Session::isStateAdmin()
            || HelpdeskV2Session::isNicAdmin()
            || (string) $ticket->created_by_userid === (string) HelpdeskV2Session::userId();
    }

    private function ticketCollaborationLocked(HelpdeskV2Ticket $ticket): bool
    {
        return in_array($ticket->status, [
            'resolved',
            HelpdeskV2Ticket::STATUS_CLOSED,
            HelpdeskV2Ticket::STATUS_REJECTED,
            HelpdeskV2Ticket::STATUS_CANCELLED,
        ], true);
    }

    private function ticketAtStateAdminReview(HelpdeskV2Ticket $ticket): bool
    {
        return in_array($ticket->status, [
            HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN_REVIEW,
            'pending_state_admin_review',
        ], true);
    }

    private function canOpenRole(string $role): bool
    {
        return ($role === HelpdeskV2Session::ROLE_USER
                && ! HelpdeskV2Session::isStateAdmin()
                && ! HelpdeskV2Session::isNicAdmin()
                && ! HelpdeskV2Session::isLayerLead()
                && ! HelpdeskV2Session::isDeveloperPerson())
            || ($role === HelpdeskV2Session::ROLE_STATE_ADMIN && HelpdeskV2Session::isStateAdmin())
            || ($role === HelpdeskV2Session::ROLE_NIC_ADMIN
                && HelpdeskV2Session::isNicAdmin()
                && ! HelpdeskV2Session::isStateAdmin())
            || ($role === HelpdeskV2Session::ROLE_LAYER_LEAD && HelpdeskV2Session::isLayerLead())
            || (in_array($role, [
                HelpdeskV2Session::ROLE_DEVELOPER,
                HelpdeskV2Session::ROLE_TESTER,
                HelpdeskV2Session::ROLE_WATCHLIST,
            ], true) && HelpdeskV2Session::isDeveloperPerson());
    }

    private function priorityOptions($query)
    {
        return $query
            ->whereNotNull('priority')
            ->where('priority', '<>', '')
            ->selectRaw('LOWER(priority) as value, MIN(priority) as label')
            ->groupBy(DB::raw('LOWER(priority)'))
            ->orderBy('value')
            ->get()
            ->map(fn ($row) => (object) [
                'value' => $row->value,
                'label' => $row->label,
            ]);
    }

    private function actionsForRole(array $actions, string $role, HelpdeskV2Ticket $ticket): array
    {
        return collect($actions)
            ->filter(function (array $definition) use ($role, $ticket) {
                $roles = $definition['roles'] ?? [];

                return in_array($role, $roles, true)
                    || $this->currentDeveloperOwnsAction($ticket, $roles)
                    || in_array('authorized_reopen', $roles, true);
            })
            ->all();
    }

    private function currentDeveloperOwnsAction(HelpdeskV2Ticket $ticket, array $roles): bool
    {
        if (! in_array(HelpdeskV2Session::ROLE_DEVELOPER, $roles, true)) {
            return false;
        }

        return (string) $ticket->developer_userid === (string) HelpdeskV2Session::developerUserId();
    }

    private function statusOptions($query, bool $includeTechnicalStatuses = false)
    {
        $rawStatuses = $query
            ->whereNotNull('status')
            ->where('status', '<>', '')
            ->pluck('status');

        $mainOptions = $rawStatuses
            ->map(fn ($status) => HelpdeskV2Ticket::mainStatusKeyFor((string) $status))
            ->unique()
            ->reject(fn ($status) => $status === HelpdeskV2Ticket::STATUS_REJECTED)
            ->sort()
            ->values()
            ->map(fn ($status) => (object) [
                'value' => $status,
                'label' => HelpdeskV2Ticket::mainStatusLabelFor($status),
            ]);

        if (! $includeTechnicalStatuses) {
            return $mainOptions;
        }

        $seniorDeveloperOption = collect([
            (object) [
                'value' => 'tech_team_senior_developer',
                'label' => 'Tech Team - Senior Developer',
            ],
            (object) [
                'value' => 'returned_tickets',
                'label' => 'Returned Tickets',
            ],
        ]);

        return $mainOptions
            ->concat($seniorDeveloperOption)
            ->concat($this->technicalStatusOptionsFromRaw($rawStatuses))
            ->unique('value')
            ->values();
    }

    private function technicalStatusOptionsFromRaw($rawStatuses)
    {
        $technicalOptions = $rawStatuses
            ->map(fn ($status) => (string) $status)
            ->unique()
            ->reject(fn ($status) => $status === HelpdeskV2Ticket::STATUS_REJECTED)
            ->filter(fn ($status) => HelpdeskV2Ticket::mainStatusKeyFor($status) !== $status)
            ->sort()
            ->values()
            ->map(fn ($status) => (object) [
                'value' => $status,
                'label' => 'Tech Team - '.HelpdeskV2Ticket::labelFor($status),
            ]);

        return $technicalOptions;
    }

    private function rawStatusesForMainStatus(string $status): array
    {
        $status = Str::lower($status);

        if ($status === 'tech_team_senior_developer') {
            return $this->seniorDeveloperTechStatuses();
        }

        if ($status === 'returned_tickets') {
            return $this->returnedTicketStatuses();
        }

        return collect(array_keys(HelpdeskV2Ticket::STATUS_LABELS))
            ->filter(fn ($rawStatus) => HelpdeskV2Ticket::mainStatusKeyFor($rawStatus) === $status)
            ->push($status)
            ->unique()
            ->values()
            ->all();
    }

    private function seniorDeveloperTechStatuses(): array
    {
        return [
            HelpdeskV2Ticket::STATUS_ASSIGNED_LAYER_LEAD,
            HelpdeskV2Ticket::STATUS_RETURNED_BY_DEVELOPER,
            'returned_by_developer',
            HelpdeskV2Ticket::STATUS_RETURNED_BY_TESTER,
            HelpdeskV2Ticket::STATUS_COMPLETED_LAYER_LEAD,
        ];
    }

    private function returnedTicketStatuses(): array
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

    private function showsTechnicalStatusFilters(string $role): bool
    {
        return in_array($role, [
            HelpdeskV2Session::ROLE_NIC_ADMIN,
            HelpdeskV2Session::ROLE_DEVELOPER,
            HelpdeskV2Session::ROLE_LAYER_LEAD,
            HelpdeskV2Session::ROLE_WATCHLIST,
        ], true);
    }

    private function leadDevelopmentStatuses(): array
    {
        return [
            HelpdeskV2Ticket::STATUS_ASSIGNED_LAYER_LEAD,
            HelpdeskV2Ticket::STATUS_ASSIGNED_DEVELOPER,
            HelpdeskV2Ticket::STATUS_DEVELOPER_IN_PROGRESS,
            'developer_in_progress',
            HelpdeskV2Ticket::STATUS_RETURNED_TO_DEVELOPER,
            'returned_to_developer',
        ];
    }

    private function leadTestingStatuses(): array
    {
        return [
            HelpdeskV2Ticket::STATUS_RETURNED_BY_DEVELOPER,
            'returned_by_developer',
            HelpdeskV2Ticket::STATUS_ASSIGNED_TESTER,
            HelpdeskV2Ticket::STATUS_TESTING_IN_PROGRESS,
            HelpdeskV2Ticket::STATUS_RETURNED_BY_TESTER,
            HelpdeskV2Ticket::STATUS_RETURNED_TO_TESTER,
            HelpdeskV2Ticket::STATUS_COMPLETED_LAYER_LEAD,
        ];
    }

    private function watchlistCandidates(HelpdeskV2Ticket $ticket)
    {
        $blockedUserIds = $ticket->assignments
            ->where('status', 'watchlist')
            ->pluck('developer_userid')
            ->push($ticket->developer_userid)
            ->filter()
            ->map(fn ($userId) => (string) $userId)
            ->all();

        return HelpdeskV2Session::activeLayerLeads()
            ->merge(HelpdeskV2Session::activeDevelopers())
            ->unique(fn ($developer) => (string) $developer->devuserid)
            ->reject(fn ($developer) => in_array((string) $developer->devuserid, $blockedUserIds, true))
            ->sortBy('devename')
            ->values();
    }

    private function buildPlanOptions($planDetails): array
    {
        $financialYears = $planDetails
            ->map(fn ($detail) => [
                'value' => $detail->financialyearcode ?? null,
                'label' => $detail->financialyear ?? null,
            ])
            ->filter(fn ($detail) => ! empty($detail['value']) && ! empty($detail['label']))
            ->unique('value')
            ->values();

        $auditQuarters = $planDetails
            ->map(fn ($detail) => [
                'value' => $detail->planmappingid ?? null,
                'quartercode' => $detail->auditquartercode ?? null,
                'financialyearcode' => $detail->financialyearcode ?? null,
                'label' => $detail->planname ?? $detail->auditquarter ?? $detail->auditquartercode ?? null,
            ])
            ->filter(fn ($detail) => ! empty($detail['value']) && ! empty($detail['label']))
            ->unique('value')
            ->sortBy(fn ($detail) => (int) $detail['value'])
            ->values();

        return [$financialYears, $auditQuarters];
    }

    private function canSelectTicketDepartment(): bool
    {
        return HelpdeskV2Session::isNicAdmin() || HelpdeskV2Session::isStateAdmin();
    }

    private function selectedTicketDeptCode(?string $requestedDeptCode = null): ?string
    {
        $requestedDeptCode = trim((string) $requestedDeptCode);

        if ($this->canSelectTicketDepartment()) {
            return $requestedDeptCode !== '' ? $requestedDeptCode : null;
        }

        return HelpdeskV2Session::deptCode();
    }

    private function departmentOptions()
    {
        return DB::table('audit.mst_dept')
            ->select('deptcode', 'deptesname')
            ->whereNotNull('deptcode')
            ->whereNotNull('deptesname')
            ->orderBy('deptesname')
            ->get();
    }

    private function departmentByCode(string $deptCode): ?object
    {
        return DB::table('audit.mst_dept')
            ->where('deptcode', $deptCode)
            ->first(['deptcode', 'deptesname']);
    }

    private function validateTicketPlanSelection(string $deptCode, string $financialYearCode, string $planMappingId): void
    {
        $hasPlan = collect(CommonModel::getplandetailsforreport($deptCode))
            ->contains(function ($detail) use ($financialYearCode, $planMappingId) {
                return (string) ($detail->financialyearcode ?? '') === $financialYearCode
                    && (string) ($detail->planmappingid ?? '') === $planMappingId;
            });

        if (! $hasPlan) {
            throw ValidationException::withMessages([
                'audit_quarter' => 'Select a valid financial year and audit quarter for the selected department.',
            ]);
        }
    }

    private function flowSteps(HelpdeskV2Ticket $ticket, string $role): array
    {
        $steps = $this->travelFlowSteps($ticket, $role);
        $activeKey = $this->activeTravelFlowKey($ticket, $role);

        $keys = array_keys($steps);
        $activeIndex = array_search($activeKey, $keys, true);

        if ($activeIndex === false) {
            $activeKey = $this->visibleTravelFlowKey($activeKey, $steps);
            $activeIndex = array_search($activeKey, $keys, true);
        }

        $activeIndex = $activeIndex === false ? 0 : $activeIndex;

        return collect($steps)
            ->map(function ($step, $key) use ($keys, $activeIndex) {
                $index = array_search($key, $keys, true);
                $step['state'] = $index < $activeIndex ? 'done' : ($index === $activeIndex ? 'active' : 'todo');

                return (object) $step;
            })
            ->values()
            ->all();
    }

    private function visibleTravelFlowKey(string $activeKey, array $steps): string
    {
        $fallbacks = [
            'layer_lead_development' => 'nic_admin_start',
            'developer' => 'nic_admin_start',
            'layer_lead_forward_to_nic' => 'nic_admin_start',
            'nic_admin_return' => 'nic_admin_start',
        ];

        $fallbackKey = $fallbacks[$activeKey] ?? $activeKey;

        return array_key_exists($fallbackKey, $steps) ? $fallbackKey : $activeKey;
    }

    private function travelFlowSteps(HelpdeskV2Ticket $ticket, string $role): array
    {
        $showTechnicalTravel = $this->showsTechnicalTravel($role);
        $hasLayerLeadFlow = $ticket->hasLayerLeadFlow();
        $hasDeveloperAssigned = $this->hasDeveloperAssignment($ticket);
        $stateStart = $this->actorStepSummary($ticket, ['forward_to_nic'], HelpdeskV2Session::ROLE_STATE_ADMIN, 'State Admin review');
        $nicStart = $this->actorStepSummary($ticket, ['assign_developer_from_nic', 'assign_layer_lead', 'update_nic_status'], HelpdeskV2Session::ROLE_NIC_ADMIN, 'NIC Admin review');

        $steps = [
            'user_created' => [
                'label' => $this->actorDisplayLabel($ticket->created_by_name, HelpdeskV2Session::ROLE_USER, $ticket->created_by_userid),
                'caption' => 'User',
                'date' => $this->displayDateTime($ticket->created_at),
            ],
            'state_admin_start' => [
                'label' => $stateStart['label'],
                'caption' => $stateStart['caption'],
                'date' => $stateStart['date'],
            ],
            'nic_admin_start' => [
                'label' => $nicStart['label'],
                'caption' => $nicStart['caption'],
                'date' => $nicStart['date'],
            ],
        ];

        if ($showTechnicalTravel) {
            $developerWork = $this->developerWorkSummary($ticket);

            if ($hasLayerLeadFlow) {
                $steps['layer_lead_development'] = [
                    'label' => $this->actorDisplayLabel($ticket->layer_lead_name, HelpdeskV2Session::ROLE_LAYER_LEAD),
                    'caption' => 'Senior Developer review',
                    'date' => $this->assignmentDate($ticket, ['layer_lead', 'additional_layer']),
                ];
            }

            if ($hasDeveloperAssigned) {
                $steps['developer'] = [
                    'label' => $this->actorDisplayLabel($ticket->developer_name, HelpdeskV2Session::ROLE_DEVELOPER),
                    'caption' => $developerWork['caption'],
                    'date' => $developerWork['date'],
                ];
            }

            $leadForward = $this->workflowCommentForAction($ticket, ['resolve_layer_to_nic', 'forward_completed_to_nic']);

            if ($leadForward) {
                $steps['layer_lead_forward_to_nic'] = [
                    'label' => $this->actorDisplayLabel($leadForward->user_name ?: $ticket->layer_lead_name, $leadForward->user_role ?: HelpdeskV2Session::ROLE_LAYER_LEAD, $leadForward->cams_userid),
                    'caption' => 'Forwarded to NIC Admin',
                    'date' => $this->displayDateTime($leadForward->created_at),
                ];
            }
        }

        $nicReturn = $this->forwardedBySummary($ticket, ['lead_developer_forward_to_nic', 'resolve_layer_to_nic', 'forward_completed_to_nic', 'developer_forward_to_nic'], HelpdeskV2Session::ROLE_NIC_ADMIN, 'Resolution review', false);
        $stateReturn = $this->forwardedBySummary($ticket, ['forward_to_state'], HelpdeskV2Session::ROLE_STATE_ADMIN, 'Final review', false);

        if ($showTechnicalTravel && $this->hasNicResolutionReview($ticket)) {
            $steps['nic_admin_return'] = [
                'label' => $nicReturn['label'],
                'caption' => $nicReturn['caption'],
                'date' => $nicReturn['date'],
            ];
        }
        $steps['state_admin_return'] = [
            'label' => $stateReturn['label'],
            'caption' => $stateReturn['caption'],
            'date' => $stateReturn['date'],
        ];
        $steps['user_final'] = [
            'label' => $this->actorDisplayLabel($ticket->created_by_name, HelpdeskV2Session::ROLE_USER, $ticket->created_by_userid),
            'caption' => $ticket->isFinalStatus() ? HelpdeskV2Ticket::labelFor($ticket->status) : 'Closure',
            'date' => $this->displayDateTime($ticket->closed_at),
        ];

        return $steps;
    }

    private function developerWorkSummary(HelpdeskV2Ticket $ticket): array
    {
        $assignedDate = $this->assignmentDate($ticket, ['assigned', 'developer', 'reassigned', 'returned']);

        return [
            'caption' => $ticket->developer_name ? 'Developer assigned' : 'Development',
            'date' => $assignedDate,
        ];
    }

    private function forwardedBySummary(HelpdeskV2Ticket $ticket, array $actions, string $fallbackRole, string $defaultCaption, bool $useActorLabel = true): array
    {
        $forwarded = $this->workflowCommentForAction($ticket, $actions);

        if ($forwarded) {
            return [
                'label' => $useActorLabel
                    ? $this->actorDisplayLabel($forwarded->user_name, $forwarded->user_role ?: $fallbackRole, $forwarded->cams_userid)
                    : $this->actorDisplayLabel(null, $fallbackRole),
                'caption' => $defaultCaption,
                'date' => $this->displayDateTime($forwarded->created_at),
            ];
        }

        return [
            'label' => $this->actorDisplayLabel(null, $fallbackRole),
            'caption' => $defaultCaption,
            'date' => null,
        ];
    }

    private function hasNicResolutionReview(HelpdeskV2Ticket $ticket): bool
    {
        $status = $this->normalizedTicketStatus($ticket);

        return $ticket->hasLayerLeadFlow()
            || $this->hasDeveloperAssignment($ticket)
            || $this->workflowCommentForAction($ticket, ['lead_developer_forward_to_nic', 'resolve_layer_to_nic', 'forward_completed_to_nic', 'developer_forward_to_nic']) !== null
            || in_array($status, [HelpdeskV2Ticket::STATUS_PENDING_NIC_ADMIN_REVIEW, 'pending_nic_admin_review'], true);
    }

    private function actorStepSummary(HelpdeskV2Ticket $ticket, array $actions, string $fallbackRole, string $caption): array
    {
        $event = $this->workflowCommentForAction($ticket, $actions);

        return [
            'label' => $this->actorDisplayLabel($event?->user_name, $event?->user_role ?: $fallbackRole, $event?->cams_userid),
            'caption' => $caption,
            'date' => $this->displayDateTime($event?->created_at),
        ];
    }

    private function hasDeveloperAssignment(HelpdeskV2Ticket $ticket): bool
    {
        if ($ticket->developer_userid) {
            return true;
        }

        return $ticket->assignments
            ->whereIn('status', ['assigned', 'developer', 'reassigned', 'returned'])
            ->isNotEmpty();
    }

    private function workflowCommentForAction(HelpdeskV2Ticket $ticket, array $actions): ?object
    {
        $actionTags = collect($actions)
            ->map(fn ($action) => '['.Str::upper($action).']')
            ->all();

        return $ticket->devComments
            ->concat($ticket->comments)
            ->sortByDesc('created_at')
            ->first(function ($comment) use ($actionTags) {
                $text = Str::upper((string) $comment->comment);

                return collect($actionTags)->contains(fn ($tag) => Str::startsWith($text, $tag));
            });
    }

    private function showsTechnicalTravel(string $role): bool
    {
        return in_array($role, [
            HelpdeskV2Session::ROLE_NIC_ADMIN,
            HelpdeskV2Session::ROLE_LAYER_LEAD,
            HelpdeskV2Session::ROLE_DEVELOPER,
            HelpdeskV2Session::ROLE_WATCHLIST,
        ], true);
    }

    private function activeTravelFlowKey(HelpdeskV2Ticket $ticket, string $role): string
    {
        $status = $this->normalizedTicketStatus($ticket);
        $pendingRole = $this->normalizedPendingRole($ticket);

        if ($ticket->isFinalStatus()) {
            return 'user_final';
        }

        if (in_array($status, [HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN_REVIEW, 'pending_state_admin_review'], true)) {
            return 'state_admin_return';
        }

        $hasTechnicalWork = $ticket->hasLayerLeadFlow()
            || (bool) $ticket->developer_userid
            || (bool) $ticket->tester_userid;

        if (in_array($status, [HelpdeskV2Ticket::STATUS_PENDING_NIC_ADMIN_REVIEW, 'pending_nic_admin_review'], true)
            || $status === HelpdeskV2Ticket::STATUS_RETURNED_TO_NIC_ADMIN
            || ($pendingRole === HelpdeskV2Session::ROLE_NIC_ADMIN && $hasTechnicalWork)) {
            return $this->hasNicResolutionReview($ticket) ? 'nic_admin_return' : 'nic_admin_start';
        }

        if (! $this->showsTechnicalTravel($role)) {
            if ($pendingRole === HelpdeskV2Session::ROLE_NIC_ADMIN
                || $status === HelpdeskV2Ticket::STATUS_FORWARDED_NIC_ADMIN) {
                return 'nic_admin_start';
            }

            if (in_array($pendingRole, [HelpdeskV2Session::ROLE_STATE_ADMIN, 'superadmin'], true)
                || in_array($status, [HelpdeskV2Ticket::STATUS_SUBMITTED, HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN], true)) {
                return 'state_admin_start';
            }

            return 'nic_admin_start';
        }

        if ($pendingRole === HelpdeskV2Session::ROLE_NIC_ADMIN
            || $status === HelpdeskV2Ticket::STATUS_FORWARDED_NIC_ADMIN) {
            return $hasTechnicalWork ? 'nic_admin_return' : 'nic_admin_start';
        }

        if (in_array($pendingRole, [HelpdeskV2Session::ROLE_STATE_ADMIN, 'superadmin'], true)
            || in_array($status, [HelpdeskV2Ticket::STATUS_SUBMITTED, HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN], true)) {
            return 'state_admin_start';
        }

        if (in_array($ticket->status, [
            HelpdeskV2Ticket::STATUS_RETURNED_BY_DEVELOPER,
            'returned_by_developer',
        ], true) || ($pendingRole === HelpdeskV2Session::ROLE_LAYER_LEAD && $ticket->developer_userid)) {
            return $ticket->hasLayerLeadFlow() ? 'layer_lead_development' : 'nic_admin_start';
        }

        if (in_array($status, [
            HelpdeskV2Ticket::STATUS_RETURNED_TO_DEVELOPER,
            'returned_to_developer',
        ], true)) {
            return 'developer';
        }

        if (in_array($status, [
            HelpdeskV2Ticket::STATUS_ASSIGNED_DEVELOPER,
            HelpdeskV2Ticket::STATUS_DEVELOPER_IN_PROGRESS,
            'developer_in_progress',
        ], true) || $pendingRole === HelpdeskV2Session::ROLE_DEVELOPER) {
            return 'developer';
        }

        if (in_array($status, [HelpdeskV2Ticket::STATUS_ASSIGNED_LAYER_LEAD], true)
            || $pendingRole === HelpdeskV2Session::ROLE_LAYER_LEAD) {
            return 'layer_lead_development';
        }

        if (in_array($status, [
            HelpdeskV2Ticket::STATUS_ASSIGNED_TESTER,
            HelpdeskV2Ticket::STATUS_TESTING_IN_PROGRESS,
            HelpdeskV2Ticket::STATUS_RETURNED_BY_TESTER,
            HelpdeskV2Ticket::STATUS_RETURNED_TO_TESTER,
            HelpdeskV2Ticket::STATUS_COMPLETED_LAYER_LEAD,
        ], true) || $pendingRole === HelpdeskV2Session::ROLE_DEVELOPER) {
            return 'developer';
        }

        if ($pendingRole === HelpdeskV2Session::ROLE_NIC_ADMIN
            || $status === HelpdeskV2Ticket::STATUS_FORWARDED_NIC_ADMIN) {
            return 'nic_admin_start';
        }

        if (in_array($pendingRole, [HelpdeskV2Session::ROLE_STATE_ADMIN, 'superadmin'], true)
            || in_array($status, [HelpdeskV2Ticket::STATUS_SUBMITTED, HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN], true)) {
            return 'state_admin_start';
        }

        return 'user_created';
    }

    private function normalizedTicketStatus(HelpdeskV2Ticket $ticket): string
    {
        $status = Str::of((string) $ticket->status)
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

            if ($status === $labelKey) {
                return (string) $key;
            }
        }

        return match ($status) {
            'forwarded_to_nic_admin' => HelpdeskV2Ticket::STATUS_FORWARDED_NIC_ADMIN,
            'pending_nic_admin_review' => HelpdeskV2Ticket::STATUS_PENDING_NIC_ADMIN_REVIEW,
            'pending_state_admin_review' => HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN_REVIEW,
            'returned_to_nic_admin' => HelpdeskV2Ticket::STATUS_RETURNED_TO_NIC_ADMIN,
            'returned_by_developer' => HelpdeskV2Ticket::STATUS_RETURNED_BY_DEVELOPER,
            'returned_to_developer' => HelpdeskV2Ticket::STATUS_RETURNED_TO_DEVELOPER,
            'developer_in_progress' => HelpdeskV2Ticket::STATUS_DEVELOPER_IN_PROGRESS,
            default => $status,
        };
    }

    private function normalizedPendingRole(HelpdeskV2Ticket $ticket): ?string
    {
        $role = Str::of((string) $ticket->pending_role)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();

        return match ($role) {
            'nicadmin', 'nic_admin', 'nic_admn', 'nic' => HelpdeskV2Session::ROLE_NIC_ADMIN,
            'stateadmin', 'state_admin', 'state', 'superadmin' => HelpdeskV2Session::ROLE_STATE_ADMIN,
            'layerlead', 'layer_lead', 'lead' => HelpdeskV2Session::ROLE_LAYER_LEAD,
            'developer', 'dev' => HelpdeskV2Session::ROLE_DEVELOPER,
            'tester' => HelpdeskV2Session::ROLE_TESTER,
            default => $role ?: null,
        };
    }

    private function activeFlowKey(HelpdeskV2Ticket $ticket): string
    {
        if (in_array($ticket->status, [HelpdeskV2Ticket::STATUS_CLOSED, HelpdeskV2Ticket::STATUS_REJECTED, HelpdeskV2Ticket::STATUS_CANCELLED], true)
            || ($ticket->status === 'resolved' && ! $ticket->pending_role)) {
            return 'closed';
        }

        if (in_array($ticket->status, [
            HelpdeskV2Ticket::STATUS_ASSIGNED_LAYER_LEAD,
            HelpdeskV2Ticket::STATUS_RETURNED_BY_DEVELOPER,
            'returned_by_developer',
            HelpdeskV2Ticket::STATUS_RETURNED_BY_TESTER,
            HelpdeskV2Ticket::STATUS_COMPLETED_LAYER_LEAD,
        ], true) || $ticket->pending_role === HelpdeskV2Session::ROLE_LAYER_LEAD) {
            return 'layer_lead';
        }

        if (in_array($ticket->status, [HelpdeskV2Ticket::STATUS_ASSIGNED_TESTER, HelpdeskV2Ticket::STATUS_TESTING_IN_PROGRESS, HelpdeskV2Ticket::STATUS_RETURNED_TO_TESTER], true)
            || $ticket->pending_role === HelpdeskV2Session::ROLE_TESTER) {
            return 'tester';
        }

        if (in_array($ticket->status, ['in_progress', HelpdeskV2Ticket::STATUS_ASSIGNED_DEVELOPER, HelpdeskV2Ticket::STATUS_DEVELOPER_IN_PROGRESS, HelpdeskV2Ticket::STATUS_RETURNED_TO_DEVELOPER, 'returned_to_developer'], true)
            || $ticket->pending_role === HelpdeskV2Session::ROLE_DEVELOPER) {
            return 'developer';
        }

        if (in_array($ticket->pending_role, ['nicadmin', 'nic_admin'], true)
            || $ticket->status === HelpdeskV2Ticket::STATUS_FORWARDED_NIC_ADMIN
            || $ticket->status === HelpdeskV2Ticket::STATUS_PENDING_NIC_ADMIN_REVIEW) {
            return 'nic_admin';
        }

        if (in_array($ticket->pending_role, ['stateadmin', 'state_admin', 'superadmin'], true)
            || in_array($ticket->status, [HelpdeskV2Ticket::STATUS_SUBMITTED, HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN, HelpdeskV2Ticket::STATUS_PENDING_STATE_ADMIN_REVIEW], true)) {
            return 'state_admin';
        }

        return 'created';
    }

    private function assignmentDate(HelpdeskV2Ticket $ticket, array $statuses): ?string
    {
        $assignment = $ticket->assignments->first(fn ($row) => in_array($row->status, $statuses, true));

        return $this->displayDateTime($assignment?->assigned_at);
    }

    private function displayDateTime(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return HelpdeskV2Ticket::displayDateTime($value);
    }

    private function actorDisplayLabel(?string $name, ?string $role, ?string $userId = null): string
    {
        $normalizedRole = $this->normalizedActorRole($role);
        $developer = $this->shouldResolveDeveloperActor($normalizedRole)
            ? $this->developerActor($userId, $name)
            : null;

        if ($developer) {
            $roleLabel = ($developer->senior_flag ?? null) === 'Y' ? 'Senior Developer' : 'Developer';
            $actorName = trim((string) ($developer->devename ?? '')) ?: (trim((string) $name) ?: $roleLabel);

            return $actorName.' ('.$roleLabel.')';
        }

        $roleLabel = $this->actorRoleLabel($role);
        $actorName = trim((string) $name) ?: $roleLabel;

        return $actorName.' ('.$roleLabel.')';
    }

    private function actorRoleLabel(?string $role): string
    {
        $normalized = $this->normalizedActorRole($role);

        return match ($normalized) {
            'stateadmin', 'state_admin', 'state', 'superadmin' => 'State Admin',
            'nicadmin', 'nic_admin', 'nic_admn', 'nic' => 'NIC Admin',
            'layerlead', 'layer_lead', 'lead', 'additional_layer' => 'Senior Developer',
            'developer', 'dev' => 'Developer',
            'tester' => 'Tester',
            'watchlist' => 'Watchlist',
            default => 'User',
        };
    }

    private function normalizedActorRole(?string $role): string
    {
        return Str::of((string) $role)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();
    }

    private function shouldResolveDeveloperActor(string $normalizedRole): bool
    {
        return in_array($normalizedRole, ['', 'user', 'developer', 'dev', 'layerlead', 'layer_lead', 'lead', 'additional_layer'], true);
    }

    private function developerActor(?string $userId, ?string $name): ?object
    {
        $userId = trim((string) $userId);
        $name = trim((string) $name);

        if ($userId === '' && $name === '') {
            return null;
        }

        $cacheKey = strtolower($userId).'|'.strtolower($name);

        if (array_key_exists($cacheKey, $this->devActorCache)) {
            return $this->devActorCache[$cacheKey];
        }

        $developer = DB::table('audit.dev_userdetails')
            ->where('statusflag', 'Y')
            ->when($userId !== '', fn ($query) => $query->where('devuserid', $userId))
            ->when($userId === '' && $name !== '', fn ($query) => $query->whereRaw('LOWER(devename) = ?', [strtolower($name)]))
            ->first(['devuserid', 'devename', 'senior_flag']);

        return $this->devActorCache[$cacheKey] = $developer;
    }

    private function timeline(HelpdeskV2Ticket $ticket, string $role)
    {
        $devSignatures = $ticket->devComments
            ->map(fn ($comment) => $this->commentSignature($comment->cams_userid, $comment->comment))
            ->all();
        $devCommentTexts = $ticket->devComments
            ->map(fn ($comment) => $this->commentTextSignature($comment->comment))
            ->all();

        $mainComments = $ticket->comments
            ->filter(fn ($comment) => $this->canSeeMainComment($comment, $role))
            ->reject(fn ($comment) => $role === HelpdeskV2Session::ROLE_NIC_ADMIN
                && in_array($this->commentSignature($comment->cams_userid, $comment->comment), $devSignatures, true))
            ->reject(fn ($comment) => in_array($role, [
                    HelpdeskV2Session::ROLE_LAYER_LEAD,
                    HelpdeskV2Session::ROLE_DEVELOPER,
                    HelpdeskV2Session::ROLE_TESTER,
                    HelpdeskV2Session::ROLE_WATCHLIST,
                ], true)
                && ! $comment->is_internal
                && in_array($this->commentTextSignature($comment->comment), $devCommentTexts, true))
	            ->map(fn ($comment) => (object) [
	                'source' => 'main',
	                'source_id' => $comment->id,
	                'user_role' => $comment->user_role,
	                'user_name' => $comment->user_name,
		                'actor_label' => $this->actorDisplayLabel($comment->user_name, $comment->user_role, $comment->cams_userid),
	                'visibility' => $comment->visibility,
	                'created_at' => $comment->created_at,
	                'comment' => $this->timelineCommentText($comment->comment, $comment->user_name),
                'comment_html' => $this->timelineCommentHtml($this->timelineCommentText($comment->comment, $comment->user_name)),
                'kind' => $this->timelineCommentKind($comment->comment),
                'can_forward' => false,
                'forwarded' => false,
            ]);

        $forwardedSignatures = $ticket->comments
            ->map(fn ($comment) => $this->commentSignature($comment->cams_userid, $comment->comment))
            ->merge($ticket->comments->map(fn ($comment) => $this->commentTextSignature($comment->comment)))
            ->all();

        $devComments = $ticket->devComments
            ->filter(fn ($comment) => $this->canSeeDeveloperComment($comment, $role))
            ->map(function ($comment) use ($role, $forwardedSignatures) {
                $forwarded = in_array($this->commentSignature($comment->cams_userid, $comment->comment), $forwardedSignatures, true)
                    || in_array($this->commentTextSignature($comment->comment), $forwardedSignatures, true);

                return (object) [
                    'source' => 'dev',
	                    'source_id' => $comment->id,
	                    'user_role' => $comment->user_role,
	                    'user_name' => $comment->user_name,
		                    'actor_label' => $this->actorDisplayLabel($comment->user_name, $comment->user_role, $comment->cams_userid),
	                    'visibility' => 'To NIC Admin',
	                    'created_at' => $comment->created_at,
	                    'comment' => $this->timelineCommentText($comment->comment, $comment->user_name),
                    'comment_html' => $this->timelineCommentHtml($this->timelineCommentText($comment->comment, $comment->user_name)),
                    'kind' => $this->timelineCommentKind($comment->comment),
                    'can_forward' => $role === HelpdeskV2Session::ROLE_NIC_ADMIN && ! $forwarded,
                    'forwarded' => $forwarded,
                ];
            });

        return $mainComments
            ->concat($devComments)
            ->sortBy('created_at')
            ->values();
    }

    private function timelineCommentText(?string $comment, ?string $userName): string
    {
        $text = trim((string) $comment);
        $name = trim((string) $userName) ?: 'Developer';

        if (! preg_match('/^\[([A-Z_]+)\]/', $text, $matches)) {
            return $text;
        }

        $message = match (Str::lower($matches[1])) {
            'created' => 'Ticket created and forwarded to State Admin.',
            'reopen' => $name.' reopened the ticket and forwarded it to State Admin.',
            'forward_to_nic' => $name.' forwarded the ticket to NIC Admin.',
            'assign_developer_from_nic' => $this->assignedTimelineText($text, $name, 'assigned the ticket to'),
            'assign_layer_lead' => $this->assignedTimelineText($text, $name, 'assigned the ticket to Senior Developer'),
            'assign_developer' => $this->assignedTimelineText($text, $name, 'assigned the ticket to Developer'),
            'start_development' => $name.' started development.',
            'return_to_developer' => $this->assignedTimelineText($text, $name, 'returned the ticket to Developer'),
            'lead_developer_forward_to_nic' => $name.' completed the work and forwarded the ticket to NIC Admin.',
            'developer_forward_to_lead' => $name.' completed development and forwarded the ticket to Senior Developer for testing.',
            'developer_forward_to_nic' => $name.' completed development and forwarded the ticket to NIC Admin.',
            'developer_return' => $name.' completed development and returned the ticket to Senior Developer.',
	            'assign_tester' => $this->assignedTimelineText($text, $name, 'assigned the ticket to Tester'),
	            'start_testing' => $name.' started testing.',
	            'return_to_tester' => $this->assignedTimelineText($text, $name, 'returned the ticket to Tester'),
	            'tester_return' => $name.' returned the test result to Senior Developer.',
	            'resolve_layer_to_nic' => $name.' forwarded the ticket to NIC Admin.',
	            'forward_completed_to_nic' => $name.' forwarded the ticket to NIC Admin.',
	            'forward_to_state' => $name.' forwarded the ticket to State Admin.',
	            'return_to_nic_admin' => $name.' returned the ticket to NIC Admin.',
	            'update_nic_status' => $this->statusUpdateTimelineText($text),
	            'close' => $name.' updated final status to '.$this->workflowTargetStatusLabel($text).'.',
	            default => $text,
	        };

        return $this->appendWorkflowRemarks($message, $text);
    }

    private function appendWorkflowRemarks(string $message, string $rawText): string
    {
        if (preg_match('/Remarks?:\s*(.+)$/is', $rawText, $matches)) {
            $remarks = trim($matches[1]);

            if ($remarks !== '' && ! Str::contains(Str::lower($message), 'remarks:')) {
                return rtrim($message).' Remarks: '.$remarks;
            }
        }

	        return $message;
	    }

    private function statusUpdateTimelineText(string $text): string
    {
        $statusText = 'Status updated.';

        if (preg_match('/\]\s*(.+?)\.\s*Assigned to:/is', $text, $matches)
            || preg_match('/\]\s*(.+?)\.\s*Remarks?:/is', $text, $matches)
            || preg_match('/\]\s*(.+?)\./is', $text, $matches)) {
            $statusText = trim($matches[1]).'.';
        }

        return $this->appendWorkflowRemarks($statusText, $text);
    }

	    private function timelineCommentHtml(string $comment): string
	    {
	        if (preg_match('/^([^\.]*\s->\s[^\.]*\.)(\s*)(Remarks?:\s*)(.+)$/is', $comment, $matches)) {
	            return '<strong>'.e($matches[1]).'</strong>'.e($matches[2]).'<strong>'.e($matches[3].$matches[4]).'</strong>';
	        }

	        if (preg_match('/^(.*?)(Remarks?:\s*)(.+)$/is', $comment, $matches)) {
	            return e($matches[1]).'<strong>'.e($matches[2].$matches[3]).'</strong>';
	        }

        return e($comment);
    }

    private function workflowTargetStatusLabel(string $text): string
    {
        if (preg_match('/->\s*([^\.]+)\./', $text, $matches)) {
            $status = trim($matches[1]);

            return $status !== '' ? $status : 'Closed';
        }

        return 'Closed';
    }

    private function assignedTimelineText(string $text, string $actorName, string $actionText): string
    {
        if (preg_match('/Assigned to:\s*([^\.]+)\./i', $text, $matches)) {
            $target = trim($matches[1]);

            if ($target !== '' && $target !== '-') {
                return $actorName.' '.$actionText.': '.$target.'.';
            }
        }

        return $actorName.' '.$actionText.'.';
    }

    private function timelineCommentKind(?string $comment): string
    {
        $text = Str::upper(trim((string) $comment));

        return match (true) {
            Str::startsWith($text, '[CREATED]') => 'created',
            Str::startsWith($text, '[REOPEN]') => 'reopened',
            ! Str::startsWith($text, '[') => 'comment',
            default => 'normal',
        };
    }

    private function canSeeMainComment(HelpdeskV2Comment $comment, string $role): bool
    {
        if (Str::startsWith(Str::upper(trim((string) $comment->comment)), '[REOPEN]')) {
            return true;
        }

        if (! $comment->is_internal) {
            return true;
        }

        if ($this->isPublicWorkflowComment($comment->comment)) {
            return true;
        }

        if (in_array($role, [HelpdeskV2Session::ROLE_USER, HelpdeskV2Session::ROLE_STATE_ADMIN], true)
            && $this->isTechnicalMovementComment($comment->comment)) {
            return false;
        }

        return in_array($role, [
            HelpdeskV2Session::ROLE_NIC_ADMIN,
            HelpdeskV2Session::ROLE_LAYER_LEAD,
            HelpdeskV2Session::ROLE_DEVELOPER,
            HelpdeskV2Session::ROLE_TESTER,
            HelpdeskV2Session::ROLE_WATCHLIST,
        ], true);
    }

    private function isPublicWorkflowComment(?string $comment): bool
    {
        $text = Str::upper(trim((string) $comment));

        return collect([
            '[CREATED]',
            '[REOPEN]',
            '[FORWARD_TO_NIC]',
            '[RESOLVE_LAYER_TO_NIC]',
            '[FORWARD_COMPLETED_TO_NIC]',
            '[FORWARD_TO_STATE]',
            '[CLOSE]',
        ])->contains(fn ($prefix) => Str::startsWith($text, $prefix));
    }

    private function isTechnicalMovementComment(?string $comment): bool
    {
        $text = Str::upper(trim((string) $comment));

        return collect([
            '[ASSIGN_DEVELOPER_FROM_NIC]',
            '[ASSIGN_LAYER_LEAD]',
            '[ASSIGN_DEVELOPER]',
            '[START_DEVELOPMENT]',
            '[RETURN_TO_DEVELOPER]',
            '[ASSIGN_TESTER]',
            '[START_TESTING]',
            '[RETURN_TO_TESTER]',
            '[COMPLETE_LAYER]',
            '[RESOLVE_LAYER_TO_NIC]',
            '[FORWARD_COMPLETED_TO_NIC]',
            '[LEAD_DEVELOPER_FORWARD_TO_NIC]',
            '[DEVELOPER_FORWARD_TO_LEAD]',
            '[DEVELOPER_FORWARD_TO_NIC]',
            '[DEVELOPER_RETURN]',
            '[TESTER_RETURN]',
            '[UPDATE_NIC_STATUS]',
        ])->contains(fn ($prefix) => Str::startsWith($text, $prefix));
    }

    private function canSeeDeveloperComment(HelpdeskTicketDevComment $comment, string $role): bool
    {
        if ($role === HelpdeskV2Session::ROLE_NIC_ADMIN) {
            return true;
        }

        return in_array($role, [
            HelpdeskV2Session::ROLE_LAYER_LEAD,
            HelpdeskV2Session::ROLE_DEVELOPER,
            HelpdeskV2Session::ROLE_TESTER,
            HelpdeskV2Session::ROLE_WATCHLIST,
        ], true);
    }

    private function commentSignature(?string $userId, ?string $comment): string
    {
        return ((string) $userId).'|'.md5((string) $comment);
    }

    private function commentTextSignature(?string $comment): string
    {
        return md5((string) $comment);
    }
}
