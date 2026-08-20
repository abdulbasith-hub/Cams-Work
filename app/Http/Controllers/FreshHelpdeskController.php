<?php

namespace App\Http\Controllers;

use App\Models\FreshHelpdesk;
use App\Models\SmsmailModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FreshHelpdeskController extends Controller
{
    private const MAIL_NOTIFICATIONS_ENABLED = true;
    private const DASHBOARD_STATE_SESSION_KEY = 'fresh_helpdesk_dashboard_state';
    private const DASHBOARD_STATE_KEYS = [
        'pane',
        'ticket_card',
        'task_card',
        'dev_ticket_card',
        'dev_task_card',
        'developer_id',
        'developer_userid',
        'ticket_scope',
        'search',
        'priority',
        'status',
        'clear_filters',
        'ticket_page',
        'task_page',
        'developer_ticket_page',
        'developer_task_page',
    ];
    private const TICKET_DETAIL_FILTER_SESSION_KEY = 'fresh_helpdesk_ticket_detail_filters';
    private const TICKET_DETAIL_PAGE_SESSION_KEY = 'fresh_helpdesk_ticket_detail_page';
    private const TICKET_DETAIL_FILTER_KEYS = [
        'search',
        'priority',
        'status',
        'ticket_scope',
        'created_user',
        'developer_userid',
        'developer_status',
    ];
    private const TASK_DETAIL_FILTER_SESSION_KEY = 'fresh_helpdesk_task_detail_filters';
    private const TASK_DETAIL_PAGE_SESSION_KEY = 'fresh_helpdesk_task_detail_page';
	    private const TASK_DETAIL_FILTER_KEYS = [
	        'search',
	        'status',
	        'task_scope',
	        'task_type',
	        'developer_userid',
	    ];

    public function index(): RedirectResponse
    {
        return redirect()->route('fresh-helpdesk.dashboard');
    }

    public function dashboard(Request $request): View|RedirectResponse|StreamedResponse
    {
        abort_unless(FreshHelpdesk::canAccess(), 403);

        if ($this->dashboardHasStateQuery($request)) {
            session()->put(self::DASHBOARD_STATE_SESSION_KEY, $this->dashboardStateFromInput($request->query->all()));

            return redirect()->route('fresh-helpdesk.dashboard');
        }

        $role = FreshHelpdesk::role();
        $dashboardState = session(self::DASHBOARD_STATE_SESSION_KEY, []);
        $activeTicketCard = $this->dashboardCard($dashboardState['ticket_card'] ?? null, 'in_progress');
        $activeTaskCard = $this->dashboardCard($dashboardState['task_card'] ?? null, 'total');
        $activeDeveloperTicketCard = $this->dashboardCard($dashboardState['dev_ticket_card'] ?? null, 'total');
        $activeDeveloperTaskCard = $this->dashboardCard($dashboardState['dev_task_card'] ?? null, 'total');
        $dashboardFilters = $this->dashboardFilters($dashboardState);
        $unfilteredTicketOptions = FreshHelpdesk::ticketDetailsForCurrentRole([], true);
        $dashboardPriorityOptions = $unfilteredTicketOptions
            ->pluck('priority')
            ->filter()
            ->unique(fn ($priority) => strtolower((string) $priority))
            ->values();
        $ticketOptions = collect($dashboardFilters)->filter(fn ($value) => $value !== null && $value !== '')->isEmpty()
            ? $unfilteredTicketOptions
            : FreshHelpdesk::ticketDetailsForCurrentRole($dashboardFilters, true);
        $canViewTaskDashboard = $this->canAccessTaskManagement();
        $isDownloadRequest = $request->filled('download');
        $taskOptions = $canViewTaskDashboard ? FreshHelpdesk::tasksForCurrentRole(true, [], $isDownloadRequest ? null : 100) : collect();
        $canViewDeveloperSummary = $this->canViewDeveloperDashboardSummary();
        $developerTicketOptions = $canViewDeveloperSummary ? $unfilteredTicketOptions : collect();
        $developerOptions = $canViewDeveloperSummary ? FreshHelpdesk::activeTaskDevelopers() : collect();
        $selectedDashboardDeveloperId = $this->selectedDashboardDeveloperId($dashboardState, $developerOptions);
        $selectedDashboardDeveloper = $selectedDashboardDeveloperId
            ? $developerOptions->first(fn ($developer) => (string) $developer->devuserid === (string) $selectedDashboardDeveloperId)
            : null;
        $developerTicketRows = $canViewDeveloperSummary
            ? $this->developerDashboardTickets($developerTicketOptions, $selectedDashboardDeveloperId)
            : collect();
        $developerTaskRows = $canViewDeveloperSummary
            ? $this->developerDashboardTasks($taskOptions, $selectedDashboardDeveloperId)
            : collect();
        $tickets = $this->paginateDashboardCollection(
            $this->filterDashboardTickets($ticketOptions, $activeTicketCard),
            $request,
            'ticket_page',
            (int) ($dashboardState['ticket_page'] ?? 1)
        );
        $tasks = $canViewTaskDashboard
            ? $this->paginateDashboardCollection($this->filterDashboardTasks($taskOptions, $activeTaskCard), $request, 'task_page', (int) ($dashboardState['task_page'] ?? 1))
            : collect();
        $isNicAdmin = $role === FreshHelpdesk::ROLE_NIC_ADMIN;
        $importantTickets = $isNicAdmin ? FreshHelpdesk::importantTickets() : collect();
        $importantTicketsCount = $isNicAdmin ? FreshHelpdesk::importantTicketsCount() : 0;

        if ($isDownloadRequest) {
            return $this->downloadDashboardRows(
                (string) $request->query('download'),
                $ticketOptions,
                $taskOptions,
                $developerTicketRows,
                $developerTaskRows,
                $activeTicketCard,
                $activeTaskCard,
                $activeDeveloperTicketCard,
                $activeDeveloperTaskCard,
                $canViewTaskDashboard,
                $canViewDeveloperSummary,
                $role
            );
        }

        return view('fresh-helpdesk.dashboard', [
            'role' => $role,
            'roleLabel' => FreshHelpdesk::roleLabel(),
            'tickets' => $tickets,
            'tasks' => $tasks,
            'ticketDashboardStats' => FreshHelpdesk::ticketDashboardStats($ticketOptions),
            'taskDashboardStats' => FreshHelpdesk::taskDashboardStats($taskOptions),
            'ticketStatusLabels' => FreshHelpdesk::ticketStatusLabels(),
            'taskStatusLabels' => FreshHelpdesk::taskStatusLabels(),
            'dashboardStatusOptions' => FreshHelpdesk::ticketStatusFilterLabels(),
            'dashboardFilters' => $dashboardFilters,
            'priorityOptions' => $dashboardPriorityOptions,
            'canViewTaskDashboard' => $canViewTaskDashboard,
            'canViewDeveloperSummary' => $canViewDeveloperSummary,
            'developerOptions' => $developerOptions,
            'selectedDashboardDeveloperId' => $selectedDashboardDeveloperId,
            'selectedDashboardDeveloper' => $selectedDashboardDeveloper,
            'developerTicketDashboardStats' => $canViewDeveloperSummary
                ? FreshHelpdesk::ticketDashboardStats($developerTicketRows)
                : $this->emptyDashboardStats(),
            'developerTaskDashboardStats' => $canViewDeveloperSummary
                ? FreshHelpdesk::taskDashboardStats($developerTaskRows)
                : $this->emptyDashboardStats(),
            'developerTickets' => $canViewDeveloperSummary
                ? $this->paginateDashboardCollection($this->filterDashboardTickets($developerTicketRows, $activeDeveloperTicketCard), $request, 'developer_ticket_page', (int) ($dashboardState['developer_ticket_page'] ?? 1))
                : collect(),
            'developerTasks' => $canViewDeveloperSummary
                ? $this->paginateDashboardCollection($this->filterDashboardTasks($developerTaskRows, $activeDeveloperTaskCard), $request, 'developer_task_page', (int) ($dashboardState['developer_task_page'] ?? 1))
                : collect(),
            'activePane' => ($dashboardState['pane'] ?? '') === 'tasks' && $canViewTaskDashboard ? 'tasks' : 'tickets',
            'activeTicketCard' => $activeTicketCard,
            'activeTaskCard' => $activeTaskCard,
            'activeDeveloperTicketCard' => $activeDeveloperTicketCard,
            'activeDeveloperTaskCard' => $activeDeveloperTaskCard,
            'importantTickets' => $importantTickets,
            'importantTicketsCount' => $importantTicketsCount,
        ]);
    }

    public function dashboardState(Request $request): RedirectResponse
    {
        abort_unless(FreshHelpdesk::canAccess(), 403);

        session()->put(self::DASHBOARD_STATE_SESSION_KEY, $this->dashboardStateFromInput($request->only(self::DASHBOARD_STATE_KEYS)));

        return redirect()->route('fresh-helpdesk.dashboard');
    }

    public function dashboardDeveloperSummary(Request $request): JsonResponse
    {
        abort_unless(FreshHelpdesk::canAccess() && $this->canViewDeveloperDashboardSummary(), 403);

        $developerOptions = FreshHelpdesk::activeTaskDevelopers();
        if (FreshHelpdesk::role() === FreshHelpdesk::ROLE_NIC_ADMIN) {
            $developerId = trim((string) $request->query('developer_id'));
            if ($developerId === '' || !$developerOptions->contains(fn ($developer) => (string) $developer->devuserid === $developerId)) {
                $developerId = (string) optional($developerOptions->first())->devuserid;
            }
        } else {
            $developerId = (string) FreshHelpdesk::developerUserId();
        }

        $dashboardState = session(self::DASHBOARD_STATE_SESSION_KEY, []);
        $dashboardState = is_array($dashboardState) ? $dashboardState : [];
        $dashboardState['developer_id'] = $developerId;

        $pane = $request->query('pane') === 'tasks' ? 'tasks' : 'tickets';
        $activeDeveloperTicketCard = $this->dashboardCard($request->query('dev_ticket_card', $dashboardState['dev_ticket_card'] ?? 'total'), 'total');
        $activeDeveloperTaskCard = $this->dashboardCard($request->query('dev_task_card', $dashboardState['dev_task_card'] ?? 'total'), 'total');
        $dashboardState[$pane === 'tasks' ? 'dev_task_card' : 'dev_ticket_card'] = $pane === 'tasks'
            ? $activeDeveloperTaskCard
            : $activeDeveloperTicketCard;
        session()->put(self::DASHBOARD_STATE_SESSION_KEY, $dashboardState);

        $developer = $developerOptions->first(fn ($row) => (string) $row->devuserid === $developerId);
        if ($pane === 'tasks') {
            $taskOptions = FreshHelpdesk::tasksForCurrentRole(true);
            $developerRows = $this->developerDashboardTasks($taskOptions, $developerId);
            $filteredRows = $this->filterDashboardTasks($developerRows, $activeDeveloperTaskCard);
            $paginator = $this->paginateDashboardCollection($filteredRows, $request, 'developer_task_page');

            return response()->json([
                'pane' => 'tasks',
                'developer_id' => $developerId,
                'developer_name' => $developer->devename ?? 'Developer',
                'active_card' => $activeDeveloperTaskCard,
                'stats' => FreshHelpdesk::taskDashboardStats($developerRows),
                'title' => $this->dashboardCardLabel($activeDeveloperTaskCard, true).' Task Details',
                'rows_html' => $this->developerTaskRowsHtml($paginator->getCollection()),
                'pagination_html' => $this->dashboardPaginationHtml($paginator),
            ]);
        }

        $ticketOptions = FreshHelpdesk::ticketDetailsForCurrentRole([], true);
        $developerRows = $this->developerDashboardTickets($ticketOptions, $developerId);
        $filteredRows = $this->filterDashboardTickets($developerRows, $activeDeveloperTicketCard);
        $paginator = $this->paginateDashboardCollection($filteredRows, $request, 'developer_ticket_page');

        return response()->json([
            'pane' => 'tickets',
            'developer_id' => $developerId,
            'developer_name' => $developer->devename ?? 'Developer',
            'active_card' => $activeDeveloperTicketCard,
            'stats' => FreshHelpdesk::ticketDashboardStats($developerRows),
            'title' => $this->dashboardCardLabel($activeDeveloperTicketCard).' Ticket Details',
            'rows_html' => $this->developerTicketRowsHtml($paginator->getCollection()),
            'pagination_html' => $this->dashboardPaginationHtml($paginator),
        ]);
    }

    public function dashboardPaneSummary(Request $request): JsonResponse
    {
        abort_unless(FreshHelpdesk::canAccess(), 403);

        $pane = $request->input('pane') === 'tasks' ? 'tasks' : 'tickets';

        if ($pane === 'tasks') {
            abort_unless($this->canAccessTaskManagement(), 403);
        }

        $dashboardState = $this->dashboardStateFromInput($request->only(self::DASHBOARD_STATE_KEYS));
        session()->put(self::DASHBOARD_STATE_SESSION_KEY, $dashboardState);

        if ($pane === 'tasks') {
            $activeTaskCard = $this->dashboardCard($dashboardState['task_card'] ?? null, 'total');
            $taskOptions = FreshHelpdesk::tasksForCurrentRole(true);
            $filteredRows = $this->filterDashboardTasks($taskOptions, $activeTaskCard);
            $paginator = $this->paginateDashboardCollection($filteredRows, $request, 'task_page', (int) ($dashboardState['task_page'] ?? 1));

            return response()->json([
                'pane' => 'tasks',
                'active_card' => $activeTaskCard,
                'stats' => FreshHelpdesk::taskDashboardStats($taskOptions),
                'result_count' => $filteredRows->count(),
                'rows_html' => $this->taskDashboardRowsHtml($paginator->getCollection()),
                'pagination_html' => $this->dashboardPaginationHtml($paginator),
            ]);
        }

        $dashboardFilters = $this->dashboardFilters($dashboardState);
        $activeTicketCard = $this->dashboardCard($dashboardState['ticket_card'] ?? null, 'in_progress');
        $ticketOptions = FreshHelpdesk::ticketDetailsForCurrentRole($dashboardFilters, true);
        $filteredRows = $this->filterDashboardTickets($ticketOptions, $activeTicketCard);
        $paginator = $this->paginateDashboardCollection($filteredRows, $request, 'ticket_page', (int) ($dashboardState['ticket_page'] ?? 1));

        return response()->json([
            'pane' => 'tickets',
            'active_card' => $activeTicketCard,
            'stats' => FreshHelpdesk::ticketDashboardStats($ticketOptions),
            'result_count' => $filteredRows->count(),
            'rows_html' => $this->ticketDashboardRowsHtml($paginator->getCollection(), FreshHelpdesk::role()),
            'pagination_html' => $this->dashboardPaginationHtml($paginator),
        ]);
    }

    public function dashboardVerifyImportantTickets(): JsonResponse
    {
        abort_unless(FreshHelpdesk::canAccess() && FreshHelpdesk::role() === FreshHelpdesk::ROLE_NIC_ADMIN, 403);

        FreshHelpdesk::markImportantTicketsVerified();

        return response()->json(['success' => true]);
    }

    public function autoForwardStaleNicTickets(Request $request): JsonResponse
    {
        abort_unless(FreshHelpdesk::canAccess(), 403);

	        $forwardedIds = FreshHelpdesk::autoForwardStaleNicTickets();

	        foreach ($forwardedIds as $forwardedId) {
	            $ticket = FreshHelpdesk::ticket((int) $forwardedId);
	            $this->sendTicketNotificationAfterResponse(
	                $ticket,
	                'Helpdesk Ticket Auto Forwarded',
	                'Ticket Auto Forwarded',
	                'The ticket was auto forwarded to Senior Developer.',
	                $this->ticketBaseCcEmails($ticket),
	                ['Forward Mode' => 'Auto'],
	                'auto_forward_to_senior'
	            );
	        }

	        $watchedTicketId = $request->filled('ticket')
            ? FreshHelpdesk::ticketIdFromUrlToken((string) $request->input('ticket'))
            : null;

        return response()->json([
            'success' => true,
            'forwarded' => count($forwardedIds),
            'current_ticket_forwarded' => $watchedTicketId !== null && in_array($watchedTicketId, $forwardedIds, true),
        ]);
    }

    public function tasks(): RedirectResponse
    {
        return redirect()->route('fresh-helpdesk.task-details');
    }

    public function assignTask(): View
    {
        abort_unless($this->canAssignTask(), 403);

        return view('fresh-helpdesk.assign-task', [
            'role' => FreshHelpdesk::role(),
            'roleLabel' => FreshHelpdesk::roleLabel(),
            'developers' => FreshHelpdesk::activeTaskDevelopers(),
            'moduleCategories' => FreshHelpdesk::categoryRows(),
        ]);
    }

    public function taskDetails(Request $request): View|RedirectResponse|StreamedResponse
    {
        abort_unless($this->canAccessTaskManagement(), 403);

        $role = FreshHelpdesk::role();
        $taskStatusLabels = FreshHelpdesk::taskStatusLabels();
		        $developerFilterOptions = in_array($role, [FreshHelpdesk::ROLE_NIC_ADMIN, FreshHelpdesk::ROLE_SENIOR_DEVELOPER], true)
		            ? FreshHelpdesk::activeTaskDevelopers()
		            : collect();

	        if ($request->query->has('clear_filters')) {
	            session()->forget([self::TASK_DETAIL_FILTER_SESSION_KEY, self::TASK_DETAIL_PAGE_SESSION_KEY]);

	            $routeParams = [];
	            if ($request->filled('task')) {
	                $routeParams['task'] = (string) $request->query('task');
	            }

	            return redirect()->route('fresh-helpdesk.task-details', $routeParams);
	        }

	        $hasFilterQuery = collect(self::TASK_DETAIL_FILTER_KEYS)->contains(fn ($key) => $request->query->has($key));
        $filters = $this->taskDetailFiltersFromInput(
            $hasFilterQuery ? $request->query->all() : [],
            $role,
            $taskStatusLabels,
            $developerFilterOptions
        );
        $page = max(1, (int) $request->query('page', 1));

        if ($request->query('download') === 'tasks') {
            if (!$hasFilterQuery) {
                $filters = $this->taskDetailFiltersFromInput(
                    session(self::TASK_DETAIL_FILTER_SESSION_KEY, []),
                    $role,
                    $taskStatusLabels,
                    $developerFilterOptions
                );
            }

            return $this->downloadTaskDetailRows(
                FreshHelpdesk::tasksForCurrentRole(true, $filters, null),
                'helpdesk-task-details'
            );
        }

        $selectedTask = null;
        $taskHistories = collect();
        if ($request->filled('task')) {
            $taskId = FreshHelpdesk::taskIdFromUrlToken((string) $request->query('task'));
            abort_if($taskId === null, 404);

            $selectedTask = FreshHelpdesk::task($taskId);
            abort_unless($this->canViewTask($selectedTask), 403);
	            $taskHistories = FreshHelpdesk::taskHistoriesWithHostedEvents(
	                $selectedTask,
	                FreshHelpdesk::taskHistories($selectedTask->id)
	            );
        }

        return view('fresh-helpdesk.task-details', [
            'role' => $role,
            'roleLabel' => FreshHelpdesk::roleLabel(),
            'tasks' => FreshHelpdesk::tasksForCurrentRolePaginated(true, 10, $filters, $page),
            'selectedTask' => $selectedTask,
            'taskHistories' => $taskHistories,
            'developers' => FreshHelpdesk::activeDevelopers(),
            'developerFilterOptions' => $developerFilterOptions,
            'taskStatusLabels' => $taskStatusLabels,
            'filters' => $filters,
        ]);
    }

    public function taskDetailsFilter(Request $request): RedirectResponse|JsonResponse
    {
        abort_unless($this->canAccessTaskManagement(), 403);

        $role = FreshHelpdesk::role();
        $taskStatusLabels = FreshHelpdesk::taskStatusLabels();
	        $developerFilterOptions = in_array($role, [FreshHelpdesk::ROLE_NIC_ADMIN, FreshHelpdesk::ROLE_SENIOR_DEVELOPER], true)
	            ? FreshHelpdesk::activeTaskDevelopers()
	            : collect();

        $filters = $this->taskDetailFiltersFromInput($request->only(self::TASK_DETAIL_FILTER_KEYS), $role, $taskStatusLabels, $developerFilterOptions);

        session()->put(self::TASK_DETAIL_FILTER_SESSION_KEY, $filters);
        session()->put(self::TASK_DETAIL_PAGE_SESSION_KEY, 1);

        if ($request->ajax()) {
            $tasks = FreshHelpdesk::tasksForCurrentRolePaginated(true, 10, $filters, 1)
                ->withPath(route('fresh-helpdesk.task-details'))
                ->appends($this->filledFilterParams($filters));

            return response()->json([
                'success' => true,
                'count' => $tasks->total(),
                'rows_html' => $this->taskDetailRowsHtml($tasks),
                'pagination_html' => $this->taskDetailPaginationHtml($tasks),
                'empty' => $tasks->total() === 0,
                'download_url' => route('fresh-helpdesk.task-details', ['download' => 'tasks'] + $this->filledFilterParams($filters)),
            ]);
        }

        return redirect()->route('fresh-helpdesk.task-details', $this->filledFilterParams($filters));
    }

    public function create(): View
    {
        abort_unless($this->canCreateTicket(), 403);

        $department = FreshHelpdesk::sessionDepartment();
        $isAdminCreator = in_array(FreshHelpdesk::role(), [
            FreshHelpdesk::ROLE_STATE_ADMIN,
            FreshHelpdesk::ROLE_NIC_ADMIN,
        ], true);
        $isStateAdminCreator = FreshHelpdesk::role() === FreshHelpdesk::ROLE_STATE_ADMIN;

        return view('fresh-helpdesk.create', [
            'department' => $department,
            'departments' => FreshHelpdesk::departments(),
            'isAdminCreator' => $isAdminCreator,
            'isStateAdminCreator' => $isStateAdminCreator,
            'financialYears' => FreshHelpdesk::financialYears($department->deptcode ?? null),
            'auditQuarters' => FreshHelpdesk::auditQuarters($department->deptcode ?? null),
            'planOptions' => FreshHelpdesk::planFilterOptions($isAdminCreator ? 'ALL' : ($department->deptcode ?? null)),
            'priorities' => ['Low', 'Medium', 'High', 'Critical'],
            'types' => [
                'support' => 'Support',
                'new_feature' => 'New Feature',
                'bug' => 'Bug / Issue',
                'data_correction' => 'Data Correction',
            ],
            'ticketScopeTypes' => FreshHelpdesk::ticketScopeTypeLabels(),
            'categories' => FreshHelpdesk::categoryOptions(),
        ]);
    }

    public function categoryMaster(): View
    {
        abort_unless($this->canManageCategoryMaster(), 403);

        return view('fresh-helpdesk.category-master', [
            'categories' => FreshHelpdesk::categoryRows(),
            'tableExists' => FreshHelpdesk::categoryTableExists(),
            'createTableSql' => FreshHelpdesk::categoryCreateTableSql(),
            'insertSql' => FreshHelpdesk::categoryInsertSql(),
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        abort_unless($this->canManageCategoryMaster(), 403);

        $validated = $request->validate([
            'category_name' => 'required|string|max:100',
        ]);

        try {
            FreshHelpdesk::insertCategory($validated['category_name']);
        } catch (\Throwable $exception) {
            return back()
                ->withErrors(['category_name' => 'Category already exists or could not be saved.'])
                ->withInput();
        }

        return redirect()
            ->route('fresh-helpdesk.category-master')
            ->with('success', 'Helpdesk category inserted successfully.');
    }

    public function ticketDetails(Request $request): View|RedirectResponse|StreamedResponse
    {
        abort_unless(FreshHelpdesk::canAccess(), 403);
        $role = FreshHelpdesk::role();
        $statusOptions = FreshHelpdesk::ticketStatusFilterLabels();
        $developerFilterOptions = $role === FreshHelpdesk::ROLE_NIC_ADMIN
            ? FreshHelpdesk::activeTaskDevelopers()
            : collect();

	        if ($request->query->has('clear_filters')) {
	            session()->forget(self::TICKET_DETAIL_FILTER_SESSION_KEY);
	            session()->forget(self::TICKET_DETAIL_PAGE_SESSION_KEY);

	            $routeParams = [];
	            if ($request->filled('ticket')) {
	                $routeParams['ticket'] = (string) $request->query('ticket');
	            }
	            if ($request->filled('task')) {
	                $routeParams['task'] = (string) $request->query('task');
	            }

	            return redirect()->route('fresh-helpdesk.ticket-details', $routeParams);
	        }

        $hasFilterQuery = collect(self::TICKET_DETAIL_FILTER_KEYS)->contains(fn ($key) => $request->query->has($key));
        $filters = $this->ticketDetailFiltersFromInput(
            $hasFilterQuery ? $request->query->all() : [],
            $role,
            $statusOptions,
            $developerFilterOptions
        );
        $ticketDetailPage = max(1, (int) $request->query('ticket_detail_page', 1));

        if ($request->query('download') === 'tickets') {
            if (!$hasFilterQuery) {
                $filters = $this->ticketDetailFiltersFromInput(
                    session(self::TICKET_DETAIL_FILTER_SESSION_KEY, []),
                    $role,
                    $statusOptions,
                    $developerFilterOptions
                );
            }

            return $this->downloadTicketDetailRows(
                FreshHelpdesk::ticketDetailsForCurrentRole($filters),
                'helpdesk-ticket-details',
                $role
            );
        }

        $selectedTicket = null;
        $ticketComments = collect();
        if ($request->filled('ticket')) {
            $ticketId = FreshHelpdesk::ticketIdFromUrlToken((string) $request->query('ticket'));
            abort_if($ticketId === null, 404);

            $selectedTicket = FreshHelpdesk::ticket($ticketId);
            abort_unless($this->canViewTicket($selectedTicket), 403);
            $ticketComments = FreshHelpdesk::ticketComments($selectedTicket->id, $role);
        }

        $selectedTask = null;
        $taskHistories = collect();
        if ($request->filled('task')) {
            $taskId = FreshHelpdesk::taskIdFromUrlToken((string) $request->query('task'));
            abort_if($taskId === null, 404);

            $selectedTask = FreshHelpdesk::task($taskId);
            abort_unless($this->canViewTask($selectedTask), 403);
		            $taskHistories = FreshHelpdesk::taskHistoriesWithHostedEvents(
		                $selectedTask,
	                FreshHelpdesk::taskHistories($selectedTask->id)
	            );
        }

        $tickets = $this->paginateDashboardCollection(
            FreshHelpdesk::ticketDetailsForCurrentRole($filters),
            $request,
            'ticket_detail_page',
            $ticketDetailPage
        );
        $ticketOptions = FreshHelpdesk::ticketDetailsForCurrentRole();
        $stateCreatorOptions = $role === FreshHelpdesk::ROLE_STATE_ADMIN
            ? FreshHelpdesk::stateAdminChargeUsers()
            : collect();

        return view('fresh-helpdesk.ticket-details', [
            'role' => $role,
            'roleLabel' => FreshHelpdesk::roleLabel(),
            'tickets' => $tickets,
            'tasks' => FreshHelpdesk::tasksForCurrentRole(),
            'selectedTicket' => $selectedTicket,
            'ticketComments' => $ticketComments,
            'selectedTask' => $selectedTask,
            'taskHistories' => $taskHistories,
            'seniorDevelopers' => FreshHelpdesk::activeSeniorDevelopers(),
            'developers' => FreshHelpdesk::activeDevelopers(),
            'ticketStatusLabels' => FreshHelpdesk::ticketStatusLabels(),
            'taskStatusLabels' => FreshHelpdesk::taskStatusLabels(),
            'priorities' => $ticketOptions
                ->pluck('priority')
                ->filter()
                ->map(fn ($priority) => ucfirst(strtolower(trim((string) $priority))))
                ->unique(fn ($priority) => strtolower($priority))
                ->sort()
                ->values(),
            'filters' => $filters,
            'statusOptions' => $statusOptions,
            'categories' => FreshHelpdesk::categoryOptions(),
            'stateCreatorOptions' => $stateCreatorOptions,
            'developerFilterOptions' => $developerFilterOptions,
        ]);
    }

    public function ticketDetailsFilter(Request $request): RedirectResponse|JsonResponse
    {
        abort_unless(FreshHelpdesk::canAccess(), 403);

        $role = FreshHelpdesk::role();
        $statusOptions = FreshHelpdesk::ticketStatusFilterLabels();
        $developerFilterOptions = $role === FreshHelpdesk::ROLE_NIC_ADMIN
            ? FreshHelpdesk::activeTaskDevelopers()
            : collect();
        $filters = $this->ticketDetailFiltersFromInput($request->only(self::TICKET_DETAIL_FILTER_KEYS), $role, $statusOptions, $developerFilterOptions);

        session()->put(self::TICKET_DETAIL_FILTER_SESSION_KEY, $filters);
        session()->put(self::TICKET_DETAIL_PAGE_SESSION_KEY, 1);

        if ($request->ajax()) {
            $tickets = $this->paginateDashboardCollection(
                FreshHelpdesk::ticketDetailsForCurrentRole($filters),
                $request,
                'ticket_detail_page',
                1
            )->withPath(route('fresh-helpdesk.ticket-details'))
                ->appends($this->filledFilterParams($filters));

            return response()->json([
                'success' => true,
                'count' => $tickets->total(),
                'rows_html' => $this->ticketDetailRowsHtml($tickets, $role),
                'pagination_html' => $this->dashboardPaginationHtml($tickets),
                'download_url' => route('fresh-helpdesk.ticket-details', ['download' => 'tickets'] + $this->filledFilterParams($filters)),
            ]);
        }

        return redirect()->route('fresh-helpdesk.ticket-details', $this->filledFilterParams($filters));
    }

    public function storeTicket(Request $request): RedirectResponse
    {
        abort_unless($this->canCreateTicket(), 403);

        $validated = $request->validate([
            'deptcode' => 'nullable|string',
            'financialyearcode' => 'required|string',
            'planmappingid' => 'required|integer',
            'request_type' => 'required|string|max:50',
            'ticket_scope_type' => 'required|in:specified,all',
            'subject' => 'required|string|max:200',
            'description' => 'required|string|max:750',
            'category' => 'required|string|max:100',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'institution' => 'nullable|string|max:255',
            'watchlist_notify' => 'nullable|boolean',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:500',
        ]);

        $validated['deptcode'] = $this->deptCodeForTicket($validated['deptcode'] ?? null);
        $this->validatePlanMapping($validated['deptcode'], $validated['financialyearcode'], (int) $validated['planmappingid']);
        $validated['attachments'] = $this->storeTicketAttachments($request);
        $validated['created_by_role'] = FreshHelpdesk::role();
        $watchlistCc = FreshHelpdesk::role() === FreshHelpdesk::ROLE_STATE_ADMIN
            ? []
            : ($request->boolean('watchlist_notify')
            ? $this->internalCcEmails()
            : []);

	        try {
	            $ticket = DB::transaction(function () use ($validated, $watchlistCc) {
		                $ticket = FreshHelpdesk::createTicket($validated);
		                $forwardedRole = FreshHelpdesk::roleLabel($ticket->forwarded_to_role);
		                $message = 'A new ticket has been auto forwarded to '.$forwardedRole.'.';
		                $details = ['Forwarded To' => $forwardedRole];
		                if ($watchlistCc) {
		                    $details['Watchlist CC'] = 'Enabled';
		                }

		                $this->notifyTicket($ticket, 'New Helpdesk Ticket', 'Ticket Created', $message, $watchlistCc, $details, 'create', false, true);

	                return $ticket;
	            });
	        } catch (\Throwable $exception) {
	            Log::warning('Fresh helpdesk ticket creation rolled back because mail notification failed.', [
	                'error' => $exception->getMessage(),
	            ]);

	            return back()
	                ->withErrors(['mail' => 'Ticket not created because mail notification failed. '.$exception->getMessage()])
	                ->withInput();
	        }

	        $forwardedRole = FreshHelpdesk::roleLabel($ticket->forwarded_to_role);

	        return redirect()
		            ->route('fresh-helpdesk.ticket-details', ['ticket' => FreshHelpdesk::ticketUrlToken($ticket->id)])
		            ->with('success', 'Ticket '.$ticket->ticket_number.' created successfully and forwarded to '.$forwardedRole.'.');
    }

    public function ticketAction(Request $request, int $ticket, string $action): RedirectResponse
    {
        abort_unless(FreshHelpdesk::canAccess(), 403);

        $ticketRow = FreshHelpdesk::ticket($ticket);
        abort_unless($this->canRunTicketAction($ticketRow, $action), 403);

        $validated = $this->validateTicketAction($request, $action, $ticketRow);

        if ($action === 'comment') {
            $isInternalComment = FreshHelpdesk::canCreateInternalTicketComments()
                && $this->ticketHasDevelopmentTeamCommentTarget($ticketRow)
                && ($validated['visibility'] ?? 'public') === 'internal';

            DB::transaction(fn () => FreshHelpdesk::addTicketComment(
                $ticketRow->id,
                $validated['remarks'],
                $isInternalComment
            ));
            $ticketRow = FreshHelpdesk::ticket($ticketRow->id);
            $this->notifyTicket(
                $ticketRow,
                'Helpdesk Ticket Comment',
                'Ticket Comment Added',
                'A ticket comment has been added.',
                $this->ticketBaseCcEmails($ticketRow, $validated),
                $this->ticketActionMailDetails('comment', $validated),
                'comment',
                $isInternalComment
            );
        } else {
            $ticketRow = DB::transaction(fn () => FreshHelpdesk::forwardTicket($ticketRow, $action, $validated));
            if (!in_array($action, ['status_update', 'developer_status_update'], true)) {
                $this->notifyTicket(
                    $ticketRow,
                    'Helpdesk Ticket Updated',
                    'Ticket Workflow Updated',
                    'A ticket action has been completed.',
                    $this->ticketBaseCcEmails($ticketRow, $validated),
                    $this->ticketActionMailDetails($action, $validated),
                    $action
                );
            }
        }

        return redirect()
            ->route('fresh-helpdesk.ticket-details', ['ticket' => FreshHelpdesk::ticketUrlToken($ticketRow->id)])
            ->with('success', $action === 'comment' ? 'Comment added successfully.' : 'Ticket action completed.');
    }

    public function storeTask(Request $request): RedirectResponse
    {
        abort_unless(FreshHelpdesk::canAccess() && FreshHelpdesk::role() === FreshHelpdesk::ROLE_NIC_ADMIN, 403);

        $validated = $request->validate([
            'developer_userid' => 'required|string',
            'task_type' => 'required|in:new,existing',
            'new_task_description' => 'required|string|max:2000',
            'module_category_id' => 'nullable|required_if:task_type,existing|integer',
            'assigned_on' => 'required|date',
            'expected_date_to_complete' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $assignedOn = $request->input('assigned_on');
                    if ($assignedOn && strtotime((string) $value) < strtotime((string) $assignedOn) + 7200) {
                        $fail('Expected On must be at least 2 hours after Assigned On.');
                    }
                },
            ],
        ]);

        $developer = FreshHelpdesk::developerById($validated['developer_userid']);
        if (!$developer) {
            return back()->withErrors(['developer_userid' => 'Selected developer is invalid.'])->withInput();
        }

        if (($validated['task_type'] ?? null) === 'existing') {
            $category = FreshHelpdesk::categoryById((int) $validated['module_category_id']);
            if (!$category) {
                return back()->withErrors(['module_category_id' => 'Selected module is invalid.'])->withInput();
            }
            $validated['process_assigned'] = $category->category_name;
            $validated['description'] = $validated['new_task_description'];
        } else {
            $validated['process_assigned'] = $validated['new_task_description'];
            $validated['description'] = $validated['new_task_description'];
            $validated['module_category_id'] = null;
        }

        $task = DB::transaction(fn () => FreshHelpdesk::createTask($validated));
        $this->notifyTask(
            $task,
            'New Helpdesk Task',
            'Task Created',
            'A new task has been assigned to Developer '.$developer->devename.'.',
            'nic_to_developer',
            $this->taskActionMailDetails('nic_to_developer', ['remarks' => $validated['description'] ?? $validated['process_assigned'] ?? null])
        );

        return redirect()
            ->route('fresh-helpdesk.task-details', ['task' => FreshHelpdesk::taskUrlToken($task->id)])
            ->with('success', 'Task created and forwarded to Developer.');
    }

    public function taskAction(Request $request, int $task, string $action): RedirectResponse
    {
        abort_unless(FreshHelpdesk::canAccess(), 403);

        $taskRow = FreshHelpdesk::task($task);
        abort_unless($this->canRunTaskAction($taskRow, $action), 403);

        $validated = $this->validateTaskAction($request, $action);

        $taskRow = DB::transaction(fn () => FreshHelpdesk::forwardTask($taskRow, $action, $validated));
        $this->notifyTask(
            $taskRow,
            'Helpdesk Task Updated',
            'Task Workflow Updated',
            'A task action has been completed.',
            $action,
            $this->taskActionMailDetails($action, $validated)
        );

        return redirect()
            ->route('fresh-helpdesk.task-details', ['task' => FreshHelpdesk::taskUrlToken($taskRow->id)])
            ->with('success', $action === 'comment' ? 'Comment added successfully.' : 'Task action completed.');
    }

    private function validateTicketAction(Request $request, string $action, object $ticket): array
    {
        $rules = [
            'remarks' => 'required|string|max:2000',
            'watchlist_notify' => 'nullable|boolean',
        ];

        if ($action === 'comment') {
            $rules['visibility'] = 'nullable|in:public,internal';
        }

        if ($action === 'status_update') {
            $rules['ticket_status'] = 'required|in:'
                .implode(',', array_keys(FreshHelpdesk::ticketStatusFilterLabels()));
        }

	        if ($action === 'developer_status_update') {
	            $rules['developer_status'] = 'required|in:'
	                .implode(',', array_keys(FreshHelpdesk::developerStatusLabels()));
	            $rules['started_on'] = 'nullable|date';
	        }

        if ($action === 'nic_to_senior') {
            $rules['senior_userid'] = 'required|string';
        }

        if (in_array($action, ['nic_to_developer', 'senior_to_developer'], true)) {
            $rules['developer_userid'] = 'required|string';
        }

	        $validated = $request->validate($rules);

	        if ($action === 'developer_to_senior' && (string) ($ticket->developer_status ?? '') !== FreshHelpdesk::DEV_STATUS_COMPLETED) {
	            throw ValidationException::withMessages([
	                'developer_status' => 'Please update developer status to Completed before forwarding to Senior Developer.',
	            ]);
	        }

        if ($validated['senior_userid'] ?? null) {
            $senior = FreshHelpdesk::developerById($validated['senior_userid']);
            if (!$senior || ($senior->senior_flag ?? null) !== 'Y') {
                throw ValidationException::withMessages(['senior_userid' => 'Selected senior developer is invalid.']);
            }
        }

        if ($validated['developer_userid'] ?? null) {
            $developer = FreshHelpdesk::developerById($validated['developer_userid']);
            if (!$developer || ($developer->senior_flag ?? null) === 'Y') {
                throw ValidationException::withMessages(['developer_userid' => 'Selected developer is invalid.']);
            }
        }

	        if (($validated['ticket_status'] ?? null) && !in_array($validated['ticket_status'], $this->allowedTicketStatusUpdates($ticket), true)) {
	            throw ValidationException::withMessages(['ticket_status' => 'Selected status is not allowed for your role.']);
	        }

		        if ($action === 'developer_status_update') {
		            if (!empty($ticket->developer_started_on)) {
		                unset($validated['started_on']);
		            } else {
		                $validated['started_on'] = ViewFacade::shared('get_nowtime')->format('Y-m-d H:i:s');
		            }
		        }

		        return $validated;
		    }

		    private function allowedTicketStatusUpdates(?object $ticket = null): array
    {
        if ($ticket && $this->isCreatorTicketWithUser($ticket)) {
            return [FreshHelpdesk::TICKET_RESOLVED];
        }

        return array_keys(FreshHelpdesk::ticketStatusFilterLabels());
    }

    private function validateTaskAction(Request $request, string $action): array
    {
        $remarksRequired = !in_array($action, ['senior_confirm', 'nic_close'], true);

        $rules = [
            'remarks' => ($remarksRequired ? 'required' : 'nullable').'|string|max:2000',
        ];

        if ($action === 'senior_reassign') {
            $rules['developer_userid'] = 'required|string';
        }

			        if ($action === 'developer_resolve') {
				            $rules['task_status'] = 'required|in:'.implode(',', [
				                FreshHelpdesk::TASK_IN_PROGRESS,
				                FreshHelpdesk::TASK_NEED_CLARIFICATION,
				                FreshHelpdesk::TASK_WRONGLY_ASSIGNED,
				                FreshHelpdesk::TASK_RESOLVED,
				            ]);
			        }

		        if (
		            in_array($action, ['senior_confirm', 'nic_close'], true)
		            || ($action === 'developer_resolve' && FreshHelpdesk::role() === FreshHelpdesk::ROLE_SENIOR_DEVELOPER)
		        ) {
		            $rules['hosted_in'] = 'nullable|in:staging,production';
		        }

        $validated = $request->validate($rules);

	        if ($validated['developer_userid'] ?? null) {
	            $developer = FreshHelpdesk::developerById($validated['developer_userid']);
	            if (!$developer) {
	                throw ValidationException::withMessages(['developer_userid' => 'Selected developer is invalid.']);
	            }
	        }

		        if ($action === 'developer_resolve') {
		            $forwardableStatuses = [FreshHelpdesk::TASK_RESOLVED, FreshHelpdesk::TASK_WRONGLY_ASSIGNED];
		            if (($request->has('forward_to_senior') || ($validated['forward_to_senior'] ?? null)) && !in_array($validated['task_status'] ?? null, $forwardableStatuses, true)) {
			                throw ValidationException::withMessages([
				                    'task_status' => 'Please select Resolved or Wrongly Assigned before forwarding to Senior Developer. Need Clarification cannot be forwarded.',
				                ]);
			            }
		            unset($validated['started_on']);
		            if (FreshHelpdesk::role() === FreshHelpdesk::ROLE_DEVELOPER) {
		                unset($validated['hosted_in']);
		            }
		        }

		        return $validated;
		    }

    private function validatePlanMapping(?string $deptCode, string $financialYearCode, int $planMappingId): void
    {
        $query = DB::table('audit.auditplanmapping')
            ->where('financialyearcode', $financialYearCode)
            ->where('planmappingid', $planMappingId)
            ->whereIn('statusflag', ['F', 'P', 'Y']);

        if ($deptCode && $deptCode !== 'ALL') {
            $query->where('deptcode', $deptCode);
        }

        $valid = $query->exists();

        if (!$valid) {
            throw ValidationException::withMessages([
                'planmappingid' => 'Selected audit quarter is invalid for this department and financial year.',
            ]);
        }
    }

    private function canRunTicketAction(object $ticket, string $action): bool
    {
        $role = FreshHelpdesk::role();

        return match ($action) {
            'comment' => $this->canViewTicket($ticket),
	            'status_update' => $this->canViewTicket($ticket)
	                && !$this->canReopenTicket($ticket)
	                && (
	                    $this->canActOnCurrentTicketStage($ticket)
	                    || $this->canSeniorUpdateAssignedDeveloperTicket($ticket)
	                    || $this->canNicAdminUpdateTicketStatus($ticket)
	                    || $this->isCreatorTicketWithUser($ticket)
	                ),
            'user_to_state' => $this->canUserReturnToState($ticket),
            'reopen' => $this->canReopenTicket($ticket),
            'state_to_nic' => $role === FreshHelpdesk::ROLE_STATE_ADMIN
                && $this->canStateAdminActOnCurrentTicket($ticket),
            'state_to_user' => $role === FreshHelpdesk::ROLE_STATE_ADMIN
                && $this->canStateAdminActOnCurrentTicket($ticket),
            'nic_to_senior' => $role === FreshHelpdesk::ROLE_NIC_ADMIN
                && $this->isNicStageTicket($ticket),
            'nic_to_developer' => $role === FreshHelpdesk::ROLE_NIC_ADMIN
                && $this->isNicStageTicket($ticket),
            'senior_to_developer' => $role === FreshHelpdesk::ROLE_SENIOR_DEVELOPER
                && (string) $ticket->forwarded_to_role === FreshHelpdesk::ROLE_SENIOR_DEVELOPER
                && (string) $ticket->assigned_to_userid === FreshHelpdesk::userId()
                && in_array((string) $ticket->status, [FreshHelpdesk::TICKET_PENDING_SENIOR, FreshHelpdesk::TICKET_RETURNED_SENIOR, FreshHelpdesk::TICKET_NEED_CLARIFICATION], true),
	            'developer_to_senior' => $role === FreshHelpdesk::ROLE_DEVELOPER
	                && (string) $ticket->forwarded_to_role === FreshHelpdesk::ROLE_DEVELOPER
	                && (string) $ticket->assigned_to_userid === FreshHelpdesk::userId()
	                && (string) $ticket->status !== FreshHelpdesk::TICKET_CLOSED,
            'senior_to_nic' => $role === FreshHelpdesk::ROLE_SENIOR_DEVELOPER
                && (string) $ticket->forwarded_to_role === FreshHelpdesk::ROLE_SENIOR_DEVELOPER
                && (string) $ticket->assigned_to_userid === FreshHelpdesk::userId()
                && in_array((string) $ticket->status, [FreshHelpdesk::TICKET_PENDING_SENIOR, FreshHelpdesk::TICKET_RETURNED_SENIOR, FreshHelpdesk::TICKET_NEED_CLARIFICATION], true),
            'developer_status_update' => $role === FreshHelpdesk::ROLE_DEVELOPER
                && (string) $ticket->forwarded_to_role === FreshHelpdesk::ROLE_DEVELOPER
                && (string) $ticket->assigned_to_userid === FreshHelpdesk::userId(),
            'nic_to_state' => $role === FreshHelpdesk::ROLE_NIC_ADMIN
                && $this->isNicStageTicket($ticket),
            'state_close' => $role === FreshHelpdesk::ROLE_STATE_ADMIN
		                && $this->canStateAdminActOnCurrentTicket($ticket)
		                && (string) $ticket->forwarded_to_role === 'stateadmin'
		                && in_array((string) $ticket->status, [FreshHelpdesk::TICKET_RETURNED_STATE, FreshHelpdesk::TICKET_RESOLVED], true),
	            default => false,
	        };
	    }

	    private function canNicAdminUpdateTicketStatus(object $ticket): bool
	    {
	        return FreshHelpdesk::role() === FreshHelpdesk::ROLE_NIC_ADMIN
	            && $this->canViewTicket($ticket)
	            && $this->isNicStageTicket($ticket);
	    }

    private function canReopenTicket(object $ticket): bool
    {
        return $this->isCurrentTicketCreator($ticket)
            && FreshHelpdesk::ticketStatusFilterKey($ticket->status ?? null) === FreshHelpdesk::TICKET_RESOLVED;
    }

    private function canUserReturnToState(object $ticket): bool
    {
        return $this->isTicketCreator($ticket)
            && FreshHelpdesk::normalizedTicketStatus($ticket->forwarded_to_role ?? '') === 'user'
            && (string) ($ticket->status ?? '') === FreshHelpdesk::TICKET_RETURNED_USER;
    }

    private function isUserResolvedTicket(object $ticket): bool
    {
        return $this->isTicketCreator($ticket)
            && FreshHelpdesk::ticketStatusFilterKey($ticket->status ?? null) === FreshHelpdesk::TICKET_RESOLVED;
    }

    private function isCreatorTicketWithUser(object $ticket): bool
    {
        return $this->isTicketCreator($ticket)
            && (
                FreshHelpdesk::normalizedTicketStatus($ticket->forwarded_to_role ?? '') === 'user'
                || FreshHelpdesk::ticketStatusFilterKey($ticket->status ?? null) === FreshHelpdesk::TICKET_RESOLVED
            );
    }

    private function isTicketCreator(object $ticket): bool
    {
        return FreshHelpdesk::role() === FreshHelpdesk::ROLE_USER
            && (string) ($ticket->cams_userid ?? '') === (string) FreshHelpdesk::userId();
    }

    private function isCurrentTicketCreator(object $ticket): bool
    {
        return (string) ($ticket->cams_userid ?? '') === (string) FreshHelpdesk::userId();
    }

    private function canActOnCurrentTicketStage(object $ticket): bool
    {
        $role = FreshHelpdesk::role();

        return match ($role) {
            FreshHelpdesk::ROLE_STATE_ADMIN => $this->canStateAdminActOnCurrentTicket($ticket),
            FreshHelpdesk::ROLE_NIC_ADMIN => $this->isNicStageTicket($ticket),
            FreshHelpdesk::ROLE_SENIOR_DEVELOPER => (string) $ticket->forwarded_to_role === FreshHelpdesk::ROLE_SENIOR_DEVELOPER
                && (string) $ticket->assigned_to_userid === FreshHelpdesk::userId()
                && in_array((string) $ticket->status, [FreshHelpdesk::TICKET_PENDING_SENIOR, FreshHelpdesk::TICKET_RETURNED_SENIOR, FreshHelpdesk::TICKET_NEED_CLARIFICATION], true),
            // Developer no longer changes the main ticket status here; they use the
            // separate, NIC-Admin-only developer_status field instead.
            default => false,
        };
    }

    private function canStateAdminActOnCurrentTicket(object $ticket): bool
    {
        return $this->isStateStageTicket($ticket)
            && FreshHelpdesk::currentStateAdminCanActOnTicket($ticket);
    }

    private function canSeniorUpdateAssignedDeveloperTicket(object $ticket): bool
    {
        return FreshHelpdesk::role() === FreshHelpdesk::ROLE_SENIOR_DEVELOPER
            && (string) ($ticket->forwarded_to_role ?? '') === FreshHelpdesk::ROLE_DEVELOPER
            && in_array((string) ($ticket->status ?? ''), [FreshHelpdesk::TICKET_PENDING_DEVELOPER, FreshHelpdesk::TICKET_NEED_CLARIFICATION], true)
            && (string) ($ticket->latest_assigned_by_userid ?? '') === (string) FreshHelpdesk::userId();
    }

    private function isStateStageTicket(object $ticket): bool
    {
        $status = (string) ($ticket->status ?? '');
        $forwardedRole = (string) ($ticket->forwarded_to_role ?? '');
        $normalizedForwardedRole = FreshHelpdesk::normalizedTicketStatus($forwardedRole);

        return (in_array($forwardedRole, ['superadmin', 'stateadmin'], true)
                || in_array($normalizedForwardedRole, ['superadmin', 'stateadmin', 'state admin'], true)
                || FreshHelpdesk::isLegacyStateForwardStatus($status)
                || FreshHelpdesk::isLegacyStateForwardStatus($forwardedRole))
                && $this->isCurrentOrLegacyStageStatus($ticket, [
                    FreshHelpdesk::TICKET_PENDING_STATE,
	                    FreshHelpdesk::TICKET_PENDING_STATE_REVIEW,
	                    FreshHelpdesk::TICKET_PENDING_STATE_ADMIN_REVIEW,
	                    FreshHelpdesk::TICKET_RETURNED_STATE,
	                    FreshHelpdesk::TICKET_RESOLVED,
	                ]);
    }

	    private function isNicStageTicket(object $ticket): bool
	    {
	        if (!in_array((string) ($ticket->forwarded_to_role ?? ''), ['nicadmin', 'nic_admin', 'nic_admn', FreshHelpdesk::ROLE_NIC_ADMIN], true)) {
	            return false;
	        }

	        return !in_array((string) ($ticket->status ?? ''), [FreshHelpdesk::TICKET_CLOSED], true);
	    }

	    private function isCurrentOrLegacyStageStatus(object $ticket, array $currentStatuses): bool
	    {
	        $status = (string) ($ticket->status ?? '');
	        if (in_array($status, $currentStatuses, true)) {
	            return true;
        }

        $normalized = $this->normalizeTicketStatus($status);

        return in_array($normalized, ['in progress', 'inprogress', 'open', 'pending', 'need clarification', 'needclarification'], true)
            || in_array($status, [FreshHelpdesk::TICKET_NEED_CLARIFICATION], true)
            || FreshHelpdesk::isLegacyStateForwardStatus($status)
            || FreshHelpdesk::isLegacyStateForwardStatus((string) ($ticket->forwarded_to_role ?? ''));
    }

	    private function normalizeTicketStatus(string $status): string
	    {
	        return FreshHelpdesk::normalizedTicketStatus($status);
	    }

	    private function ticketHasDevelopmentTeamCommentTarget(object $ticket): bool
	    {
	        $assignmentStatus = FreshHelpdesk::normalizedTicketStatus((string) ($ticket->latest_assignment_status ?? ''));
	        $latestAssignmentHasAssignee = trim((string) ($ticket->latest_assignment_userid ?? '')) !== ''
	            || trim((string) ($ticket->latest_assignment_name ?? '')) !== ''
	            || trim((string) ($ticket->latest_assignment_dev_name ?? '')) !== '';

	        if ($latestAssignmentHasAssignee && (str_contains($assignmentStatus, 'developer') || str_contains($assignmentStatus, 'senior') || str_contains($assignmentStatus, 'lead'))) {
	            return true;
	        }

	        $assignedRole = FreshHelpdesk::normalizedTicketStatus((string) ($ticket->forwarded_to_role ?? ''));

	        return trim((string) ($ticket->assigned_to_userid ?? '')) !== ''
	            && in_array($assignedRole, ['developer', 'senior developer', 'senior_developer'], true);
	    }

	    private function ticketActionMailDetails(string $action, array $data): array
	    {
	        $remarks = trim((string) ($data['remarks'] ?? ''));
	        $details = [
	            'Command' => $this->ticketActionMailLabel($action, $data),
	            'Remarks' => $remarks !== '' ? $remarks : '-',
	        ];

	        if (!empty($data['watchlist_notify'])) {
	            $details['Priority Alert'] = 'Important - Notify NIC Admin';
	        }

	        return $details;
	    }

		    private function ticketActionMailLabel(string $action, array $data): string
		    {
		        return match ($action) {
	            'comment' => 'Add Comment',
	            'status_update' => 'Update Status'.(!empty($data['ticket_status']) ? ' to '.FreshHelpdesk::ticketStatusLabel((string) $data['ticket_status']) : ''),
	            'developer_status_update' => 'Update Developer Status'.(!empty($data['developer_status']) ? ' to '.FreshHelpdesk::developerStatusLabel((string) $data['developer_status']) : ''),
	            'user_to_state' => 'Return to State Admin',
	            'reopen' => 'Reopen and Forward to State Admin',
	            'state_to_nic' => 'Forward to NIC Admin',
	            'state_to_user' => 'Return to User',
	            'state_close' => 'Close Ticket',
	            'nic_to_state' => 'Return to State Admin',
	            'nic_to_senior' => 'Forward to Senior Developer',
	            'nic_to_developer' => 'Assign Directly to Developer',
	            'senior_to_developer' => 'Assign Developer',
	            'developer_to_senior' => 'Return to Senior Developer',
	            'senior_to_nic' => 'Return to NIC Admin',
		            default => \Illuminate\Support\Str::headline(str_replace('_', ' ', $action)),
		        };
		    }

    private function taskActionMailDetails(string $action, array $data): array
    {
        $remarks = trim((string) ($data['remarks'] ?? ''));

        return [
            'Remarks' => $remarks !== '' ? $remarks : '-',
        ];
    }

		    private function canRunTaskAction(object $task, string $action): bool
    {
        $role = FreshHelpdesk::role();
        $developerUserId = FreshHelpdesk::developerUserId();

        $status = (string) $task->task_status_by_tester;

        return match ($action) {
            'comment' => $this->canViewTask($task),
            'developer_resolve' => in_array($role, [FreshHelpdesk::ROLE_SENIOR_DEVELOPER, FreshHelpdesk::ROLE_DEVELOPER], true)
                && FreshHelpdesk::taskAssignedToCurrentDeveloper($task, $developerUserId)
                && $status !== FreshHelpdesk::TASK_CLOSED
                && empty($task->verified_by)
                && empty($task->approved_by),
	            'senior_confirm' => $role === FreshHelpdesk::ROLE_SENIOR_DEVELOPER
	                && $status === FreshHelpdesk::TASK_RESOLVED
	                && empty($task->verified_by),
	            'senior_return', 'senior_reassign' => $role === FreshHelpdesk::ROLE_SENIOR_DEVELOPER
	                && in_array($status, [FreshHelpdesk::TASK_RESOLVED, FreshHelpdesk::TASK_WRONGLY_ASSIGNED], true)
	                && empty($task->verified_by),
            'nic_close' => $role === FreshHelpdesk::ROLE_NIC_ADMIN
                && $status === FreshHelpdesk::TASK_RESOLVED
                && !empty($task->verified_by),
            'reopen' => $role === FreshHelpdesk::ROLE_NIC_ADMIN
                && $status === FreshHelpdesk::TASK_CLOSED,
            default => false,
        };
    }

    private function canViewTicket(object $ticket): bool
    {
        if ((string) $ticket->cams_userid === FreshHelpdesk::userId()) {
            return true;
        }

        $developerUserId = FreshHelpdesk::developerUserId();

        return match (FreshHelpdesk::role()) {
            FreshHelpdesk::ROLE_STATE_ADMIN,
            FreshHelpdesk::ROLE_NIC_ADMIN,
            FreshHelpdesk::ROLE_SENIOR_DEVELOPER => true,
            FreshHelpdesk::ROLE_DEVELOPER => (string) $ticket->assigned_to_userid === (string) $developerUserId
                || (string) ($ticket->forwarded_to_userid ?? '') === (string) $developerUserId
                || FreshHelpdesk::ticketWasAssignedTo((int) $ticket->id, $developerUserId),
            default => false,
        };
    }

    private function canViewTask(object $task): bool
    {
        $role = FreshHelpdesk::role();
        $developerUserId = FreshHelpdesk::developerUserId();

        return match ($role) {
            FreshHelpdesk::ROLE_NIC_ADMIN,
            FreshHelpdesk::ROLE_SENIOR_DEVELOPER => true,
            FreshHelpdesk::ROLE_DEVELOPER => FreshHelpdesk::taskAssignedToCurrentDeveloper($task, $developerUserId)
                || FreshHelpdesk::taskWasHandledBy($task->id, $developerUserId),
            default => false,
        };
    }

    private function notifyTicket(object $ticket, string $subject, string $headline, string $message, array $ccEmails = [], array $extraDetails = [], string $action = 'workflow', bool $isInternalComment = false, bool $failOnMailError = false): void
    {
        $ticketNumber = $ticket->ticket_number ?? 'Ticket #'.$ticket->id;
        $mailSubject = $ticketNumber.' - '.($ticket->subject ?? $subject);
        $channels = $this->ticketNotificationChannels($ticket, $action, $ccEmails, $isInternalComment);
        $details = [
            'Ticket No' => $ticketNumber,
            'Subject' => $ticket->subject ?? '-',
            'Status' => FreshHelpdesk::ticketStatusLabel($ticket->status ?? null),
            'Currently With' => FreshHelpdesk::dashboardCurrentWith($ticket, FreshHelpdesk::role()),
            'Action By' => FreshHelpdesk::userName(),
        ];

	        $this->sendCommonMail($channels['to'], $mailSubject, $headline, $details + $extraDetails, $channels['cc'], $failOnMailError);
    }

    private function sendTicketNotificationAfterResponse(object $ticket, string $subject, string $headline, string $message, array $ccEmails = [], array $extraDetails = [], string $action = 'workflow', bool $isInternalComment = false): void
    {
        app()->terminating(function () use ($ticket, $subject, $headline, $message, $ccEmails, $extraDetails, $action, $isInternalComment) {
            try {
                $this->notifyTicket($ticket, $subject, $headline, $message, $ccEmails, $extraDetails, $action, $isInternalComment);
            } catch (\Throwable $exception) {
                Log::warning('Fresh helpdesk ticket notification failed.', [
                    'ticket_id' => $ticket->id ?? null,
                    'error' => $exception->getMessage(),
                ]);
            }
        });
    }

    private function ticketNotificationChannels(object $ticket, string $action, array $baseCcEmails = [], bool $isInternalComment = false): array
    {
        $nicEmails = FreshHelpdesk::emailsForRole(FreshHelpdesk::ROLE_NIC_ADMIN);
        $stateEmails = FreshHelpdesk::emailsForRole(FreshHelpdesk::ROLE_STATE_ADMIN);
        $seniorEmails = FreshHelpdesk::activeSeniorDevelopers()->pluck('email')->all();
        $assignedDeveloperEmails = $this->ticketAssignedDeveloperEmails($ticket);
        $latestSeniorEmails = $this->ticketLatestSeniorEmails($ticket);
        $stateOwnerEmails = $this->ticketStateOwnerEmails($ticket, $stateEmails);
        $userEmails = [$ticket->user_email ?? null];

        $toEmails = FreshHelpdesk::recipientEmailsForTicket($ticket);
        $ccEmails = $baseCcEmails;

	        switch ($action) {
	            case 'create':
	                $forwardedRole = (string) ($ticket->forwarded_to_role ?? '');
	                if (FreshHelpdesk::isLegacyStateForwardStatus($forwardedRole)
	                    || in_array($forwardedRole, FreshHelpdesk::stateForwardedRoleValues(), true)
	                    || in_array(FreshHelpdesk::normalizedTicketStatus($forwardedRole), ['state admin', 'stateadmin', 'superadmin'], true)
	                ) {
	                    $toEmails = $stateEmails;
	                } elseif (in_array($forwardedRole, ['nicadmin', FreshHelpdesk::ROLE_NIC_ADMIN, 'nic_admin', 'nic_admn'], true)) {
	                    $toEmails = $nicEmails;
	                }
	                break;

		            case 'user_to_state':
		            case 'reopen':
		            case 'nic_to_state':
	                $toEmails = $stateOwnerEmails;
	                break;

	            case 'state_to_nic':
	            case 'senior_to_nic':
		                $toEmails = $nicEmails;
		                $ccEmails = [];
		                break;

            case 'state_to_user':
            case 'state_close':
                $toEmails = $userEmails;
                break;

            case 'nic_to_senior':
            case 'auto_forward_to_senior':
                $toEmails = $assignedDeveloperEmails ?: $latestSeniorEmails;
                $ccEmails = [];
                break;

            case 'nic_to_developer':
                $toEmails = $assignedDeveloperEmails;
                $ccEmails = $seniorEmails;
                break;

            case 'senior_to_developer':
                $toEmails = $assignedDeveloperEmails;
                $ccEmails = $this->mergeEmails($ccEmails, $nicEmails);
                break;

            case 'developer_to_senior':
                $toEmails = $latestSeniorEmails;
                $ccEmails = $nicEmails;
                break;

            case 'developer_status_update':
                $toEmails = $latestSeniorEmails ?: $nicEmails;
                $ccEmails = $this->mergeEmails($ccEmails, $nicEmails);
                break;

            case 'comment':
                [$toEmails, $ccEmails] = $this->ticketCommentNotificationChannels($ticket, $toEmails, $ccEmails, $nicEmails, $latestSeniorEmails, $assignedDeveloperEmails, $isInternalComment);
                break;
        }

        return [
            'to' => FreshHelpdesk::normalizeEmails($toEmails),
            'cc' => FreshHelpdesk::normalizeEmails($ccEmails),
        ];
    }

    private function ticketCommentNotificationChannels(object $ticket, array $defaultToEmails, array $baseCcEmails, array $nicEmails, array $latestSeniorEmails, array $assignedDeveloperEmails, bool $isInternalComment): array
    {
        $role = FreshHelpdesk::role();

        if ($role === FreshHelpdesk::ROLE_DEVELOPER) {
            return [
                $latestSeniorEmails ?: $nicEmails,
                $this->mergeEmails($baseCcEmails, $nicEmails),
            ];
        }

	        if ($role === FreshHelpdesk::ROLE_SENIOR_DEVELOPER) {
	            if ($isInternalComment) {
	                return [
	                    $assignedDeveloperEmails ?: $nicEmails,
	                    $nicEmails,
	                ];
	            }

	            return [
	                $this->ticketLatestStateAdminEmails($ticket),
	                $nicEmails,
	            ];
	        }

	        if ($role === FreshHelpdesk::ROLE_STATE_ADMIN) {
	            return [
	                [$ticket->user_email ?? null],
	                [],
	            ];
	        }

	        if (!$isInternalComment && $role === FreshHelpdesk::ROLE_NIC_ADMIN) {
	            return [
	                $this->ticketStateOwnerEmails($ticket, $this->ticketLatestStateAdminEmails($ticket)),
	                [],
	            ];
	        }

	        if ($isInternalComment && $role === FreshHelpdesk::ROLE_NIC_ADMIN) {
	            return [
	                $assignedDeveloperEmails ?: ($latestSeniorEmails ?: $nicEmails),
	                $this->mergeEmails($baseCcEmails, $nicEmails),
	            ];
        }

        return [$defaultToEmails, $baseCcEmails];
    }

    private function ticketBaseCcEmails(object $ticket, array $data = []): array
    {
        $isImportant = !empty($data['watchlist_notify'])
            || strtoupper((string) ($ticket->importflag ?? '')) === 'Y';

        return $isImportant ? $this->internalCcEmails() : [];
    }

	    private function ticketAssignedDeveloperEmails(object $ticket): array
    {
        $developer = FreshHelpdesk::developerById($ticket->assigned_to_userid ?? null);

	        return [$developer->email ?? null];
	    }

    private function ticketStateOwnerEmails(object $ticket, array $fallbackEmails = []): array
    {
        if (FreshHelpdesk::ticketCreatorIsStateAdmin($ticket)) {
            $creatorEmails = FreshHelpdesk::ticketCreatorEmails($ticket);

            if (!empty($creatorEmails)) {
                return $creatorEmails;
            }
        }

        return $fallbackEmails;
    }

	    private function ticketLatestSeniorEmails(object $ticket): array
	    {
	        $senior = FreshHelpdesk::latestTicketSeniorDeveloper((int) $ticket->id);
	        $developer = FreshHelpdesk::developerById($senior->developer_userid ?? null);

	        return [$developer->email ?? null];
	    }

	    private function ticketLatestStateAdminEmails(object $ticket): array
	    {
	        $email = DB::table('audit.helpdesk_ticket_comments as htc')
	            ->join('audit.deptuserdetails as du', function ($join) {
	                $join->whereRaw('du.deptuserid::text = htc.cams_userid::text');
	            })
	            ->where('htc.ticket_id', (int) $ticket->id)
	            ->where('du.statusflag', 'Y')
	            ->whereRaw("LOWER(BTRIM(COALESCE(htc.user_role::text, ''))) IN (?, ?)", ['state admin', 'stateadmin'])
	            ->orderByDesc('htc.id')
	            ->value('du.email');

	        return [$email];
	    }

	    private function mergeEmails(array ...$emailGroups): array
    {
        return FreshHelpdesk::normalizeEmails($emailGroups);
    }

    private function notifyTask(object $task, string $subject, string $headline, string $message, string $action = 'workflow', array $extraDetails = []): void
    {
        $taskStatusValue = (string) ($task->task_status_by_tester ?? '');
        $details = $this->taskMailPrimaryDetails($task) + [
            'Status' => (FreshHelpdesk::taskStatusLabels()[$taskStatusValue] ?? $taskStatusValue) ?: '-',
            'Currently With' => $task->developer_name ?? $task->senior_name ?? 'NIC Admin',
            'Action By' => FreshHelpdesk::userName(),
        ];

        $channels = $this->taskNotificationChannels($task, $action);

        $this->sendCommonMail($channels['to'], $subject, $headline, array_merge($details, $extraDetails), $channels['cc']);
    }

    private function taskMailPrimaryDetails(object $task): array
    {
        $moduleName = trim((string) ($task->process_assigned ?? ''));

        if ((string) ($task->task_type ?? '') === 'existing' && $moduleName !== '') {
            return ['Module' => $moduleName];
        }

        return ['Type' => $this->taskTypeLabel($task->task_type ?? null)];
    }

    private function taskNotificationChannels(object $task, string $action): array
    {
        $role = FreshHelpdesk::role();
        $developerEmails = $this->taskAssignedDeveloperEmails($task);
        $seniorEmails = $this->taskSeniorDeveloperEmails($task);
        $nicEmails = FreshHelpdesk::emailsForRole(FreshHelpdesk::ROLE_NIC_ADMIN);
        $toEmails = FreshHelpdesk::recipientEmailsForTask($task);
        $ccEmails = [];

        if ($action === 'nic_to_developer') {
            $toEmails = $developerEmails;
            $ccEmails = $seniorEmails;
        } elseif ($role === FreshHelpdesk::ROLE_DEVELOPER && in_array($action, ['comment', 'developer_resolve'], true)) {
            $toEmails = $seniorEmails;
            $ccEmails = $nicEmails;
        } elseif ($role === FreshHelpdesk::ROLE_SENIOR_DEVELOPER && in_array($action, ['comment', 'senior_return', 'senior_reassign'], true)) {
            $toEmails = $developerEmails;
            $ccEmails = $nicEmails;
        }

        return [
            'to' => FreshHelpdesk::normalizeEmails($toEmails),
            'cc' => FreshHelpdesk::normalizeEmails($ccEmails),
        ];
    }

    private function taskAssignedDeveloperEmails(object $task): array
    {
        $developer = FreshHelpdesk::developerById($task->developer_userid ?? null);

        if (empty($developer?->email)) {
            $developer = FreshHelpdesk::developerByName($task->developer_name ?? null);
        }

        return [$developer->email ?? null];
    }

    private function taskSeniorDeveloperEmails(object $task): array
    {
        if (!empty($task->senior_userid)) {
            $senior = FreshHelpdesk::developerById($task->senior_userid);

            if (!empty($senior?->email)) {
                return [$senior->email];
            }
        }

        if (!empty($task->senior_name)) {
            $senior = FreshHelpdesk::developerByName($task->senior_name, true);

            if (!empty($senior?->email)) {
                return [$senior->email];
            }
        }

        return FreshHelpdesk::activeSeniorDevelopers()->pluck('email')->all();
    }

    private function sendCommonMail(array $emails, string $subject, string $headline, array $details, array $ccEmails = [], bool $failOnMailError = false): void
    {
        if (!self::MAIL_NOTIFICATIONS_ENABLED) {
            if ($failOnMailError) {
                throw new \RuntimeException('Mail notifications are disabled.');
            }

            return;
        }

	        $mail = new SmsmailModel();
	        $toEmails = FreshHelpdesk::normalizeEmails($emails);
	        $ccEmails = FreshHelpdesk::normalizeEmails($ccEmails);

	        if (empty($toEmails)) {
	            Log::warning('Fresh helpdesk mail notification skipped because no recipient email was found.', [
	                'subject' => $subject,
	                'headline' => $headline,
	                'raw_emails' => $emails,
	                'cc_emails' => $ccEmails,
	            ]);

	            if ($failOnMailError) {
	                throw new \RuntimeException('No recipient email found.');
	            }

	            return;
	        }

	        Log::info('Fresh helpdesk mail notification recipients.', [
	            'to' => $toEmails,
	            'cc' => $ccEmails,
	            'subject' => $subject,
	        ]);

	        foreach ($toEmails as $email) {
	            $recipientCcEmails = array_values(array_filter($ccEmails, fn ($ccEmail) => strtolower($ccEmail) !== strtolower($email)));
	            try {
	                $result = $mail->sendHelpdeskEmailNotification($email, $subject, $headline, $details, $recipientCcEmails);
	                if ($result === 'Message has been sent') {
	                    Log::info('Fresh helpdesk mail notification sent.', [
	                        'email' => $email,
	                        'cc' => $recipientCcEmails,
	                        'subject' => $subject,
	                    ]);
		                } elseif (is_string($result)) {
		                    Log::warning('Fresh helpdesk mail notification skipped.', [
		                        'email' => $email,
		                        'result' => $result,
		                    ]);
		                    if ($failOnMailError) {
		                        throw new \RuntimeException($result);
		                    }
		                }
		            } catch (\Throwable $exception) {
		                Log::warning('Fresh helpdesk mail notification failed.', [
		                    'email' => $email,
		                    'error' => $exception->getMessage(),
		                ]);
		                if ($failOnMailError) {
		                    throw $exception;
		                }
		            }
	        }
	    }

    private function internalCcEmails(): array
    {
        return FreshHelpdesk::normalizeEmails((array) view()->shared('internal_cc_emails', []));
    }

    private function canManageCategoryMaster(): bool
    {
        return FreshHelpdesk::canAccess()
            && in_array(FreshHelpdesk::role(), [
                FreshHelpdesk::ROLE_STATE_ADMIN,
                FreshHelpdesk::ROLE_NIC_ADMIN,
            ], true);
    }

    private function canCreateTicket(): bool
    {
        return FreshHelpdesk::canAccess()
            && in_array(FreshHelpdesk::role(), [
                FreshHelpdesk::ROLE_USER,
                FreshHelpdesk::ROLE_STATE_ADMIN,
                FreshHelpdesk::ROLE_NIC_ADMIN,
            ], true);
    }

    private function canAssignTask(): bool
    {
        return FreshHelpdesk::canAccess()
            && FreshHelpdesk::role() === FreshHelpdesk::ROLE_NIC_ADMIN;
    }

    private function canAccessTaskManagement(): bool
    {
        return FreshHelpdesk::canAccess()
            && in_array(FreshHelpdesk::role(), [
                FreshHelpdesk::ROLE_NIC_ADMIN,
                FreshHelpdesk::ROLE_SENIOR_DEVELOPER,
                FreshHelpdesk::ROLE_DEVELOPER,
            ], true);
    }

    private function dashboardCard(mixed $card, string $default): string
    {
        $card = (string) $card;

        return in_array($card, ['total', 'in_progress', 'urgent', 'developer_side', 'returned', 'resolved_closed'], true)
            ? $card
            : $default;
    }

    private function dashboardHasStateQuery(Request $request): bool
    {
        foreach (self::DASHBOARD_STATE_KEYS as $key) {
            if ($request->query->has($key)) {
                return true;
            }
        }

        return false;
    }

    private function dashboardStateFromInput(array $input): array
    {
        $current = session(self::DASHBOARD_STATE_SESSION_KEY, []);
        $state = is_array($current) ? $current : [];

        if (array_key_exists('pane', $input)) {
            $state['pane'] = ($input['pane'] ?? '') === 'tasks' ? 'tasks' : 'tickets';
        }

        foreach ([
            'ticket_card' => 'in_progress',
            'task_card' => 'total',
            'dev_ticket_card' => 'total',
            'dev_task_card' => 'total',
        ] as $key => $default) {
            if (array_key_exists($key, $input)) {
                $state[$key] = $this->dashboardCard($input[$key] ?? null, $default);
            }
        }

        if (array_key_exists('developer_id', $input)) {
            $state['developer_id'] = trim((string) ($input['developer_id'] ?? ''));
        }

        foreach (['ticket_page', 'task_page', 'developer_ticket_page', 'developer_task_page'] as $key) {
            if (array_key_exists($key, $input)) {
                $state[$key] = max(1, (int) ($input[$key] ?? 1));
            }
        }

        if (array_key_exists('clear_filters', $input)) {
            unset($state['search'], $state['priority'], $state['status'], $state['developer_userid'], $state['ticket_scope']);
        } else {
	        foreach (['search', 'priority', 'status', 'ticket_scope'] as $key) {
	                if (array_key_exists($key, $input)) {
	                    $state[$key] = trim((string) ($input[$key] ?? ''));
	                }
	            }
	        }

        return $state;
    }

    private function dashboardFilters(array $dashboardState): array
    {
        $statusOptions = FreshHelpdesk::ticketStatusFilterLabels();
        $status = trim((string) ($dashboardState['status'] ?? ''));
        $ticketScopeRaw = trim((string) ($dashboardState['ticket_scope'] ?? ''));
        $role = FreshHelpdesk::role();
		        $allowedTicketScopes = ['on_me'];
		        if ($role === FreshHelpdesk::ROLE_STATE_ADMIN) {
		            $allowedTicketScopes[] = 'returned_from_nic';
		        }
		        if ($role === FreshHelpdesk::ROLE_NIC_ADMIN) {
		            $allowedTicketScopes[] = 'developer_side';
		        }
		        if (in_array($role, [FreshHelpdesk::ROLE_NIC_ADMIN, FreshHelpdesk::ROLE_STATE_ADMIN], true)) {
		            $allowedTicketScopes[] = 'important';
		        }

        return [
            'search' => trim((string) ($dashboardState['search'] ?? '')),
            'priority' => trim((string) ($dashboardState['priority'] ?? '')),
            'status' => $status !== '' && array_key_exists($status, $statusOptions) ? $status : '',
	            'developer_userid' => '',
            'ticket_scope' => in_array($ticketScopeRaw, $allowedTicketScopes, true) ? $ticketScopeRaw : '',
        ];
    }

    private function filterDashboardTickets(Collection $tickets, string $card): Collection
    {
        return $tickets->filter(fn ($ticket) => match ($card) {
            'urgent' => FreshHelpdesk::isDashboardTicketUrgent($ticket),
            'developer_side' => FreshHelpdesk::isDeveloperSideTicket($ticket),
            'returned' => FreshHelpdesk::isDashboardTicketReturned($ticket),
            'resolved_closed' => FreshHelpdesk::isDashboardTicketResolvedClosed($ticket),
            'in_progress' => FreshHelpdesk::isDashboardTicketInProgress($ticket),
            default => true,
        })->values();
    }

    private function filterDashboardTasks(Collection $tasks, string $card): Collection
    {
        return $tasks->filter(fn ($task) => match ($card) {
            'urgent' => FreshHelpdesk::isDashboardTaskUrgent($task),
            'returned' => FreshHelpdesk::isDashboardTaskReturned($task),
            'resolved_closed' => FreshHelpdesk::isDashboardTaskResolvedClosed($task),
            'in_progress' => FreshHelpdesk::isDashboardTaskInProgress($task),
            default => true,
        })->values();
    }

    private function filledFilterParams(array $filters): array
    {
        return array_filter($filters, fn ($value) => trim((string) $value) !== '');
    }

    private function canViewDeveloperDashboardSummary(): bool
    {
        return false;
    }

    private function selectedDashboardDeveloperId(array $dashboardState, Collection $developers): ?string
    {
        if (FreshHelpdesk::role() !== FreshHelpdesk::ROLE_NIC_ADMIN) {
            return FreshHelpdesk::developerUserId();
        }

        $requestedDeveloperId = (string) ($dashboardState['developer_id'] ?? '');
        if ($requestedDeveloperId !== '' && $developers->contains(fn ($developer) => (string) $developer->devuserid === $requestedDeveloperId)) {
            return $requestedDeveloperId;
        }

        return optional($developers->first())->devuserid;
    }

    private function developerDashboardTickets(Collection $tickets, ?string $developerId): Collection
    {
        if (!$developerId) {
            return collect();
        }

        $ticketIds = $tickets->pluck('id')->filter()->values();
        $assignedTicketIds = $ticketIds->isEmpty()
            ? collect()
            : DB::table('audit.helpdesk_ticket_assignments')
                ->whereIn('ticket_id', $ticketIds)
                ->where('developer_userid', (string) $developerId)
                ->pluck('ticket_id')
                ->map(fn ($ticketId) => (string) $ticketId);

        return $tickets->filter(fn ($ticket) => (string) ($ticket->assigned_to_userid ?? '') === (string) $developerId
            || (string) ($ticket->latest_assignment_userid ?? '') === (string) $developerId
            || $assignedTicketIds->contains((string) ($ticket->id ?? '')))->values();
    }

    private function developerDashboardTasks(Collection $tasks, ?string $developerId): Collection
    {
        if (!$developerId) {
            return collect();
        }

        $taskIds = $tasks->pluck('id')->filter()->values();
        $handledTaskIds = $taskIds->isEmpty()
            || !FreshHelpdesk::ensureTaskHistoryTable()
            ? collect()
            : DB::table(FreshHelpdesk::TASK_HISTORY_TABLE)
                ->whereIn('task_id', $taskIds)
                ->where('performed_by_userid', (string) $developerId)
                ->pluck('task_id')
                ->map(fn ($taskId) => (string) $taskId);

        return $tasks->filter(fn ($task) => (string) ($task->developer_userid ?? '') === (string) $developerId
            || (string) ($task->senior_userid ?? '') === (string) $developerId
            || $handledTaskIds->contains((string) ($task->id ?? '')))->values();
    }

    private function emptyDashboardStats(): array
    {
        return [
            'total' => 0,
            'in_progress' => 0,
            'urgent' => 0,
            'developer_side' => 0,
            'returned' => 0,
            'resolved_closed' => 0,
        ];
    }

    private function dashboardCardLabel(string $card, bool $task = false): string
    {
        $labels = [
            'total' => $task ? 'Total Tasks' : 'Total Tickets',
            'in_progress' => 'In Progress',
            'urgent' => 'Urgent',
            'developer_side' => 'Developer Side',
            'returned' => $task ? 'Returned' : 'Need Clarification',
            'resolved_closed' => 'Resolved / Closed',
        ];

        return $labels[$card] ?? $labels['total'];
    }

    private function developerTicketRowsHtml(Collection $tickets): string
    {
        return $tickets->map(function ($ticket) {
	            $ticketNo = e($ticket->ticket_number ?: '#'.$ticket->id);
	            $ticketUrl = route('fresh-helpdesk.ticket-details', ['ticket' => FreshHelpdesk::ticketUrlToken($ticket->id)]);
		            $reopenedBadge = !empty($ticket->is_reopened) ? '<span class="fh-reopened-badge">Reopened</span>' : '';
		            $importantBadge = strtoupper((string) ($ticket->importflag ?? '')) === 'Y'
		                ? '<span class="fh-important-badge" title="Important ticket"><i class="ti ti-bell-ringing"></i></span>'
		                : '';
	            $subject = e($ticket->subject ?: '-');
            $userName = e($ticket->user_name ?: '-');
            $module = e($ticket->category ?: ($ticket->request_type ? \Illuminate\Support\Str::headline((string) $ticket->request_type) : '-'));
            $priority = e($ticket->priority ?: '-');
            $priorityClass = e(\Illuminate\Support\Str::slug((string) ($ticket->priority ?: 'medium')));
	            $status = e(FreshHelpdesk::ticketStatusLabel($ticket->status ?? null));
	            $createdAt = e($this->dashboardDate($ticket->created_at));
	            $createdOrder = e($this->dateOrderValue($ticket->created_at));
	            $updatedAt = e($this->dashboardDate($ticket->updated_at));

		            return '<tr>'
		                .'<td><a class="fh-ticket-no" href="'.$ticketUrl.'"><span class="fh-ticket-number-line">'.$ticketNo.$importantBadge.'</span>'.$reopenedBadge.'</a></td>'
                .'<td class="text-wrap"><strong>'.$subject.'</strong><span class="fh-muted">'.$userName.'</span></td>'
                .'<td class="text-wrap">'.$module.'</td>'
                .'<td><span class="fh-badge fh-priority-'.$priorityClass.'">'.$priority.'</span></td>'
                .'<td><span class="fh-badge fh-status">'.$status.'</span></td>'
	                .'<td class="text-wrap" data-order="'.$createdOrder.'">'.$createdAt.'</td>'
                .'<td class="text-wrap">'.$updatedAt.'</td>'
                .'</tr>';
        })->implode('');
    }

    private function ticketDashboardRowsHtml(Collection $tickets, string $role): string
    {
        return $tickets->map(function ($ticket) use ($role) {
            $ticketNo = e($ticket->ticket_number ?: '#'.$ticket->id);
            $ticketUrl = route('fresh-helpdesk.ticket-details', ['ticket' => FreshHelpdesk::ticketUrlToken($ticket->id)]);
	            $reopenedBadge = !empty($ticket->is_reopened) ? '<span class="fh-reopened-badge">Reopened</span>' : '';
	            $importantBadge = strtoupper((string) ($ticket->importflag ?? '')) === 'Y'
	                ? '<span class="fh-important-badge" title="Important ticket"><i class="ti ti-bell-ringing"></i></span>'
	                : '';
            $subject = e($ticket->subject ?: '-');
            $userName = e($ticket->user_name ?: '-');
            $module = e($ticket->category ?: ($ticket->request_type ? \Illuminate\Support\Str::headline((string) $ticket->request_type) : '-'));
            $priority = e($ticket->priority ?: '-');
            $priorityClass = e(\Illuminate\Support\Str::slug((string) ($ticket->priority ?: 'medium')));
            $status = e(FreshHelpdesk::ticketStatusLabel($ticket->status ?? null));
            $currentWith = e(FreshHelpdesk::dashboardCurrentWith($ticket, $role));
            $assignmentSubtitle = '';
            if ($role === FreshHelpdesk::ROLE_NIC_ADMIN && !empty($ticket->latest_assignment_status) && empty($ticket->is_reopened)) {
                $assignmentSubtitle = '<span class="fh-muted">'.e(\Illuminate\Support\Str::headline((string) $ticket->latest_assignment_status)).'</span>';
            }
	            $ageing = e($this->dashboardAgeing($ticket->created_at));
	            $createdAt = e($this->dashboardDate($ticket->created_at));
	            $createdOrder = e($this->dateOrderValue($ticket->created_at));
	            $updatedAt = e($this->dashboardDate($ticket->updated_at));

	            return '<tr>'
	                .'<td><a class="fh-ticket-no" href="'.$ticketUrl.'"><span class="fh-ticket-number-line">'.$ticketNo.$importantBadge.'</span>'.$reopenedBadge.'</a></td>'
                .'<td class="text-wrap"><strong>'.$subject.'</strong><span class="fh-muted">'.$userName.'</span></td>'
                .'<td class="text-wrap">'.$module.'</td>'
                .'<td><span class="fh-badge fh-priority-'.$priorityClass.'">'.$priority.'</span></td>'
                .'<td><span class="fh-badge fh-status">'.$status.'</span></td>'
                .'<td class="text-wrap">'.$currentWith.$assignmentSubtitle.'</td>'
                .'<td class="text-wrap">'.$ageing.'</td>'
	                .'<td class="text-wrap" data-order="'.$createdOrder.'">'.$createdAt.'</td>'
                .'<td class="text-wrap">'.$updatedAt.'</td>'
                .'</tr>';
        })->implode('');
    }

    private function taskDashboardRowsHtml(Collection $tasks): string
    {
        $statusLabels = FreshHelpdesk::taskStatusLabels();

        return $tasks->map(function ($task) use ($statusLabels) {
            $taskName = e($task->process_assigned ?: '#'.$task->id);
            $taskUrl = route('fresh-helpdesk.task-details', ['task' => FreshHelpdesk::taskUrlToken($task->id)]);
            $assignedBy = e($task->assigned_by_name ?: '-');
            $currentlyWith = e(FreshHelpdesk::taskCurrentlyWith($task));
            $type = e(ucfirst((string) $task->task_type));
	            $status = e($statusLabels[$task->task_status_by_tester] ?? \Illuminate\Support\Str::headline((string) $task->task_status_by_tester));
	            $assignedOn = e($this->dashboardDate($task->assigned_on));
	            $assignedOrder = e($this->dateOrderValue($task->assigned_on ?? $task->created_at ?? null));
	            $expectedOn = e($this->dashboardDate($task->expected_date_to_complete));
            $completedOn = e($this->dashboardDate($task->completed_on));
            $updatedAt = e($this->dashboardDate($task->updated_at));

            return '<tr>'
                .'<td class="text-wrap"><strong><a href="'.$taskUrl.'" class="fh-task-link">'.$taskName.'</a></strong><span class="fh-muted">Assigned by '.$assignedBy.'</span></td>'
                .'<td class="text-wrap">'.$currentlyWith.'</td>'
                .'<td class="text-wrap">'.$type.'</td>'
                .'<td class="text-wrap"><span class="fh-badge fh-status">'.$status.'</span></td>'
	                .'<td class="text-wrap" data-order="'.$assignedOrder.'">'.$assignedOn.'</td>'
                .'<td class="text-wrap">'.$expectedOn.'</td>'
                .'<td class="text-wrap">'.$completedOn.'</td>'
                .'<td class="text-wrap">'.$updatedAt.'</td>'
                .'</tr>';
        })->implode('');
    }

    private function ticketDetailFiltersFromInput(array $input, string $role, array $statusOptions, Collection $developerFilterOptions): array
    {
        $filters = [
            'search' => trim((string) ($input['search'] ?? '')),
            'priority' => trim((string) ($input['priority'] ?? '')),
            'status' => trim((string) ($input['status'] ?? '')),
            'ticket_scope' => trim((string) ($input['ticket_scope'] ?? '')),
            'created_user' => trim((string) ($input['created_user'] ?? '')),
            'developer_userid' => trim((string) ($input['developer_userid'] ?? '')),
            'developer_status' => trim((string) ($input['developer_status'] ?? '')),
        ];

	        $allowedTicketScopes = ['', 'on_me'];
		        if ($role === FreshHelpdesk::ROLE_STATE_ADMIN) {
		            $allowedTicketScopes[] = 'returned_from_nic';
		        }
		        if ($role === FreshHelpdesk::ROLE_NIC_ADMIN) {
		            $allowedTicketScopes[] = 'developer_side';
		        }
		        if (in_array($role, [FreshHelpdesk::ROLE_NIC_ADMIN, FreshHelpdesk::ROLE_STATE_ADMIN], true)) {
		            $allowedTicketScopes[] = 'important';
		        }
        if (!in_array($filters['ticket_scope'], $allowedTicketScopes, true)) {
            $filters['ticket_scope'] = '';
        }

        if ($role !== FreshHelpdesk::ROLE_STATE_ADMIN) {
            $filters['created_user'] = '';
        }

	        if (!in_array($role, [FreshHelpdesk::ROLE_NIC_ADMIN, FreshHelpdesk::ROLE_SENIOR_DEVELOPER], true)
	            || ($filters['developer_userid'] !== ''
	                && !$developerFilterOptions->contains(fn ($developer) => (string) $developer->devuserid === $filters['developer_userid']))
	        ) {
            $filters['developer_userid'] = '';
        }

        if ($filters['status'] !== '' && !array_key_exists($filters['status'], $statusOptions)) {
            $filters['status'] = '';
        }

        if (!FreshHelpdesk::canViewDeveloperStatus($role)
            || !array_key_exists($filters['developer_status'], FreshHelpdesk::developerStatusLabels())
        ) {
            $filters['developer_status'] = '';
        }

        return $filters;
    }

    private function taskDetailFiltersFromInput(array $input, string $role, array $statusOptions, Collection $developerFilterOptions): array
    {
	        $filters = [
	            'search' => trim((string) ($input['search'] ?? '')),
	            'status' => trim((string) ($input['status'] ?? '')),
	            'task_scope' => trim((string) ($input['task_scope'] ?? '')),
	            'task_type' => trim((string) ($input['task_type'] ?? '')),
	            'developer_userid' => trim((string) ($input['developer_userid'] ?? '')),
	        ];

	        if (!in_array($filters['task_scope'], ['', 'currently_me'], true)) {
	            $filters['task_scope'] = '';
	        }

	        if (!in_array($filters['task_type'], ['', 'new', 'existing'], true)) {
	            $filters['task_type'] = '';
	        }

	        if (!in_array($role, [FreshHelpdesk::ROLE_NIC_ADMIN, FreshHelpdesk::ROLE_SENIOR_DEVELOPER], true)
	            || ($filters['developer_userid'] !== ''
	                && !$developerFilterOptions->contains(fn ($developer) => (string) $developer->devuserid === $filters['developer_userid']))
	        ) {
            $filters['developer_userid'] = '';
        }

        if ($filters['status'] !== '' && !array_key_exists($filters['status'], $statusOptions)) {
            $filters['status'] = '';
        }

        return $filters;
    }

    private function developerTaskRowsHtml(Collection $tasks): string
    {
        $statusLabels = FreshHelpdesk::taskStatusLabels();

        return $tasks->map(function ($task) use ($statusLabels) {
            $taskName = e($task->process_assigned ?: '#'.$task->id);
            $taskUrl = route('fresh-helpdesk.task-details', ['task' => FreshHelpdesk::taskUrlToken($task->id)]);
            $assignedBy = e($task->assigned_by_name ?: '-');
            $type = e(ucfirst((string) $task->task_type));
	            $status = e($statusLabels[$task->task_status_by_tester] ?? \Illuminate\Support\Str::headline((string) $task->task_status_by_tester));
	            $assignedOn = e($this->dashboardDate($task->assigned_on));
	            $assignedOrder = e($this->dateOrderValue($task->assigned_on ?? $task->created_at ?? null));
	            $expectedOn = e($this->dashboardDate($task->expected_date_to_complete));
            $completedOn = e($this->dashboardDate($task->completed_on));
            $updatedAt = e($this->dashboardDate($task->updated_at));

            return '<tr>'
                .'<td class="text-wrap"><strong><a href="'.$taskUrl.'" class="fh-task-link">'.$taskName.'</a></strong><span class="fh-muted">Assigned by '.$assignedBy.'</span></td>'
                .'<td class="text-wrap">'.$type.'</td>'
                .'<td class="text-wrap"><span class="fh-badge fh-status">'.$status.'</span></td>'
	                .'<td class="text-wrap" data-order="'.$assignedOrder.'">'.$assignedOn.'</td>'
                .'<td class="text-wrap">'.$expectedOn.'</td>'
                .'<td class="text-wrap">'.$completedOn.'</td>'
                .'<td class="text-wrap">'.$updatedAt.'</td>'
                .'</tr>';
        })->implode('');
    }

    private function ticketDetailRowsHtml(LengthAwarePaginator $tickets, string $role): string
    {
        $canViewDeveloperStatus = FreshHelpdesk::canViewDeveloperStatus($role);

        return collect($tickets->items())->map(function ($ticket, int $index) use ($tickets, $role, $canViewDeveloperStatus) {
            $ticketNo = e($ticket->ticket_number ?: '#'.$ticket->id);
            $ticketUrl = e(route('fresh-helpdesk.ticket-details', ['ticket' => FreshHelpdesk::ticketUrlToken($ticket->id)]));
            $importantBadge = strtoupper((string) ($ticket->importflag ?? '')) === 'Y'
                ? '<span class="fh-important-badge" title="Important ticket"><i class="ti ti-bell-ringing"></i></span>'
                : '';
            $reopenedBadge = !empty($ticket->is_reopened)
                ? '<span class="fh-reopened-badge">Reopened</span>'
                : '';
            $priorityClass = 'fh-priority-'.e(\Illuminate\Support\Str::slug(trim((string) ($ticket->priority ?: 'medium'))));
            $statusClass = 'fh-status-'.e(\Illuminate\Support\Str::slug(FreshHelpdesk::ticketStatusFilterKey($ticket->status ?? null) ?: FreshHelpdesk::normalizedTicketStatus($ticket->status ?? null) ?: 'empty'));
            $department = e($ticket->department_name ?: '-');
            $category = e($ticket->category ?: '-');
            $devStatusHtml = '';

            if ($canViewDeveloperStatus) {
                $devStatusValue = (string) ($ticket->developer_status ?? '');
                $devStatusClass = 'fh-dev-status-'.e(\Illuminate\Support\Str::slug($devStatusValue !== '' ? $devStatusValue : 'empty'));
                $devStatusHtml = '<td class="text-wrap fh-col-dev-status"><span class="fh-dev-status-badge '.$devStatusClass.'">'
                    .e(FreshHelpdesk::developerStatusLabel($ticket->developer_status ?? null))
                    .'</span></td>';
            }

            return '<tr>'
                .'<td class="text-center">'.($tickets->firstItem() + $index).'</td>'
                .'<td><div class="fh-ticket-created"><div class="fh-ticket-no"><span class="fh-ticket-number-line">'.$ticketNo.$importantBadge.'</span>'.$reopenedBadge.'</div><div class="fh-cell-muted">'.e($ticket->user_name ?: '-').'</div></div></td>'
                .'<td><div class="fh-grouped-meta"><div class="fh-meta-line"><span class="fh-meta-value">'.$department.'</span> - <span class="fh-meta-value">'.$category.'</span></div></div></td>'
                .'<td><span class="fh-priority-badge '.$priorityClass.'">'.e($ticket->priority ?: '-').'</span></td>'
                .'<td>'.e(\Illuminate\Support\Str::headline((string) $ticket->request_type)).'</td>'
                .'<td class="text-wrap"><div class="fh-subject">'.e($ticket->subject ?: '-').'</div></td>'
                .'<td><span class="fh-status-badge '.$statusClass.'">'.e(FreshHelpdesk::ticketStatusLabel($ticket->status ?? null)).'</span></td>'
                .'<td class="text-wrap">'.e(FreshHelpdesk::dashboardCurrentWith($ticket, $role)).'</td>'
                .'<td class="text-wrap" data-order="'.e($this->dateOrderValue($ticket->created_at)).'"><span class="fh-cell-muted">'.e($this->dashboardDate($ticket->created_at)).'</span></td>'
                .$devStatusHtml
                .'<td class="text-wrap fh-col-updated"><span class="fh-cell-muted">'.e($this->dashboardDate($ticket->updated_at)).'</span></td>'
                .'<td class="text-wrap fh-col-action"><a href="'.$ticketUrl.'" class="btn btn-sm btn-outline-primary fh-action-btn"><i class="ti ti-eye me-1"></i> View</a></td>'
                .'</tr>';
        })->implode('');
    }

    private function taskDetailRowsHtml(LengthAwarePaginator $tasks): string
    {
        $statusLabels = FreshHelpdesk::taskStatusLabels();

        return collect($tasks->items())->map(function ($task, int $index) use ($tasks, $statusLabels) {
            $taskUrl = e(route('fresh-helpdesk.task-details', ['task' => FreshHelpdesk::taskUrlToken($task->id)]));
            $statusValue = (string) ($task->task_status_by_tester ?? '');
            $statusClass = 'fht-status-'.e(\Illuminate\Support\Str::slug(trim($statusValue !== '' ? $statusValue : 'empty')));
            $statusLabel = $statusLabels[$statusValue] ?? \Illuminate\Support\Str::headline($statusValue);

            return '<tr>'
                .'<td>'.($tasks->firstItem() + $index).'</td>'
                .'<td><strong class="fht-task-no">'.e($task->process_assigned ?: '#'.$task->id).'</strong><span class="fht-muted">Assigned by '.e($task->assigned_by_name ?: '-').'</span></td>'
                .'<td>'.e(ucfirst((string) $task->task_type)).'</td>'
                .'<td><span class="fht-badge '.$statusClass.'">'.e($statusLabel).'</span></td>'
                .'<td>'.e(FreshHelpdesk::taskCurrentlyWith($task)).'</td>'
                .'<td data-order="'.e($this->dateOrderValue($task->assigned_on ?: $task->created_at)).'">'.e($this->dashboardDate($task->assigned_on)).'</td>'
                .'<td>'.e($this->dashboardDate($task->expected_date_to_complete)).'</td>'
                .'<td>'.e($this->dashboardDate($task->updated_at)).'</td>'
                .'<td><a href="'.$taskUrl.'" class="btn btn-sm btn-outline-primary"><i class="ti ti-eye me-1"></i> View</a></td>'
                .'</tr>';
        })->implode('');
    }

    private function taskDetailPaginationHtml(LengthAwarePaginator $paginator): string
    {
        if (!$paginator->hasPages()) {
            return '';
        }

        $html = '<div class="fht-dt-footer">';
        $html .= '<span class="text-muted">Showing '.$paginator->firstItem().' to '.$paginator->lastItem().' of '.$paginator->total().' entries</span>';
        $html .= '<div class="fht-pagination">';
        $html .= '<a class="fht-page-link '.($paginator->onFirstPage() ? 'is-disabled' : '').'" href="'.e($paginator->previousPageUrl() ?: '#').'">Previous</a>';

        foreach ($this->windowedPageNumbers($paginator) as $page) {
            if ($page === '...') {
                $html .= '<span class="fht-page-ellipsis">&hellip;</span>';
            } elseif ($page === $paginator->currentPage()) {
                $html .= '<span class="fht-page-current">'.$page.'</span>';
            } else {
                $html .= '<a class="fht-page-link" href="'.e($paginator->url($page)).'">'.$page.'</a>';
            }
        }

        $html .= '<a class="fht-page-link '.($paginator->hasMorePages() ? '' : 'is-disabled').'" href="'.e($paginator->nextPageUrl() ?: '#').'">Next</a>';
        $html .= '</div></div>';

        return $html;
    }

    private function dashboardPaginationHtml(LengthAwarePaginator $paginator): string
    {
        if (!$paginator->hasPages()) {
            return '';
        }

        $html = '<div class="fh-laravel-pagination">';
        $html .= '<a class="fh-page-link '.($paginator->onFirstPage() ? 'is-disabled' : '').'" href="'.e($paginator->previousPageUrl() ?: '#').'">Previous</a>';

        foreach ($this->windowedPageNumbers($paginator) as $page) {
            if ($page === '...') {
                $html .= '<span class="fh-page-ellipsis">&hellip;</span>';
            } elseif ($page === $paginator->currentPage()) {
                $html .= '<span class="fh-page-current">'.$page.'</span>';
            } else {
                $html .= '<a class="fh-page-link" href="'.e($paginator->url($page)).'">'.$page.'</a>';
            }
        }

        $html .= '<a class="fh-page-link '.($paginator->hasMorePages() ? '' : 'is-disabled').'" href="'.e($paginator->nextPageUrl() ?: '#').'">Next</a>';
        $html .= '</div>';

        return $html;
    }

    private function windowedPageNumbers(LengthAwarePaginator $paginator, int $edge = 3, int $window = 1): array
    {
        $lastPage = $paginator->lastPage();
        $current = $paginator->currentPage();
        $items = [];
        $lastShown = 0;

        for ($page = 1; $page <= $lastPage; $page++) {
            if ($page <= $edge || $page > $lastPage - $edge || abs($page - $current) <= $window) {
                if ($lastShown && $page - $lastShown > 1) {
                    $items[] = '...';
                }
                $items[] = $page;
                $lastShown = $page;
            }
        }

        return $items;
    }

	    private function dashboardDate($value): string
	    {
	        if (!$value) {
	            return '-';
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d/m/Y h:i A');
        } catch (\Throwable $exception) {
            return (string) $value;
	        }
	    }

	    private function dateOrderValue($value): int
	    {
	        if (!$value) {
	            return 0;
	        }

	        try {
	            return \Carbon\Carbon::parse($value, 'Asia/Kolkata')->timestamp;
	        } catch (\Throwable $exception) {
	            return 0;
	        }
	    }

	    private function dashboardAgeing($value): string
	    {
	        if (!$value) {
	            return '-';
	        }

		        try {
			            return \Carbon\Carbon::parse($value, 'Asia/Kolkata')
			                ->diffForHumans(ViewFacade::shared('get_nowtime'), \Carbon\CarbonInterface::DIFF_ABSOLUTE, false, 2).' ago';
	        } catch (\Throwable $exception) {
	            return '-';
	        }
	    }

    private function paginateDashboardCollection(Collection $rows, Request $request, string $pageName, ?int $page = null): LengthAwarePaginator
    {
        $perPage = 10;
        $page = max(1, $page ?: LengthAwarePaginator::resolveCurrentPage($pageName));
        $pageRows = $rows->forPage($page, $perPage)->values();

        return (new LengthAwarePaginator(
            $pageRows,
            $rows->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => $pageName,
            ]
        ))->withQueryString();
    }

    private function downloadDashboardRows(
        string $downloadKey,
        Collection $ticketOptions,
        Collection $taskOptions,
        Collection $developerTicketRows,
        Collection $developerTaskRows,
        string $activeTicketCard,
        string $activeTaskCard,
        string $activeDeveloperTicketCard,
        string $activeDeveloperTaskCard,
        bool $canViewTaskDashboard,
        bool $canViewDeveloperSummary,
        string $role
    ): StreamedResponse {
        return match ($downloadKey) {
            'tickets' => $this->downloadDashboardTicketRows(
                $this->filterDashboardTickets($ticketOptions, $activeTicketCard),
                'fresh-helpdesk-dashboard-tickets',
                $role
            ),
            'tasks' => $canViewTaskDashboard
                ? $this->downloadDashboardTaskRows($this->filterDashboardTasks($taskOptions, $activeTaskCard), 'fresh-helpdesk-dashboard-tasks')
                : abort(403),
            'developer_tickets' => $canViewDeveloperSummary
                ? $this->downloadDashboardTicketRows(
                    $this->filterDashboardTickets($developerTicketRows, $activeDeveloperTicketCard),
                    'fresh-helpdesk-developer-tickets',
                    $role
                )
                : abort(403),
            'developer_tasks' => $canViewDeveloperSummary
                ? $this->downloadDashboardTaskRows(
                    $this->filterDashboardTasks($developerTaskRows, $activeDeveloperTaskCard),
                    'fresh-helpdesk-developer-tasks'
                )
                : abort(403),
            default => abort(404),
        };
    }

    private function downloadDashboardTicketRows(Collection $tickets, string $basename, string $role): StreamedResponse
    {
        return $this->streamCsvDownload($basename, [
            'Ticket Number',
            'Subject',
            'Created By',
            'Module',
            'Priority',
            'Current Status',
            'Currently With',
            'Ageing Days',
            'Created On',
            'Last Updated On',
        ], $tickets, function ($ticket) use ($role) {
            return [
                $ticket->ticket_number ?: '#'.$ticket->id,
                $ticket->subject,
                $ticket->user_name,
                $this->ticketModule($ticket),
                $ticket->priority,
                FreshHelpdesk::ticketStatusLabel($ticket->status),
                FreshHelpdesk::dashboardCurrentWith($ticket, $role),
                $this->dashboardAgeing($ticket->created_at),
                $this->exportDate($ticket->created_at),
                $this->exportDate($ticket->updated_at),
            ];
        });
    }

    private function downloadDashboardTaskRows(Collection $tasks, string $basename): StreamedResponse
    {
        return $this->streamCsvDownload($basename, [
            'Task',
            'Developer',
            'Task Type',
            'Status',
            'Assigned On',
            'Expected On',
            'Completed On',
            'Last Updated On',
        ], $tasks, function ($task) {
            return [
                $task->process_assigned ?: '#'.$task->id,
                FreshHelpdesk::taskCurrentlyWith($task),
                $this->taskTypeLabel($task->task_type ?? null),
                $this->taskStatusLabel($task->task_status_by_tester ?? null),
                $this->exportDate($task->assigned_on ?? null),
                $this->exportDate($task->expected_date_to_complete ?? null),
                $this->exportDate($task->completed_on ?? null),
                $this->exportDate($task->updated_at ?? null),
            ];
        });
    }

    private function downloadTicketDetailRows(Collection $tickets, string $basename, string $role): StreamedResponse
    {
        $headers = [
            'S.No',
            'Ticket Number',
            'Created By',
            'Department',
            'Category',
            'Priority',
            'Type',
            'Subject',
            'Status',
            'Currently With',
            'Created On',
        ];

        if (FreshHelpdesk::canViewDeveloperStatus($role)) {
            $headers[] = 'Dev Status';
        }

        $headers[] = 'Updated On';

        return $this->streamCsvDownload($basename, $headers, $tickets, function ($ticket, int $index) use ($role) {
            $row = [
                $index + 1,
                $ticket->ticket_number ?: '#'.$ticket->id,
                $ticket->user_name,
                $ticket->department_name,
                $ticket->category,
                $ticket->priority,
                $this->ticketTypeLabel($ticket->request_type ?? null),
                $ticket->subject,
                FreshHelpdesk::ticketStatusLabel($ticket->status),
                FreshHelpdesk::dashboardCurrentWith($ticket, $role),
                $this->exportDate($ticket->created_at),
            ];

            if (FreshHelpdesk::canViewDeveloperStatus($role)) {
                $row[] = FreshHelpdesk::developerStatusLabel($ticket->developer_status ?? null);
            }

            $row[] = $this->exportDate($ticket->updated_at);

            return $row;
        });
    }

    private function downloadTaskDetailRows(Collection $tasks, string $basename): StreamedResponse
    {
        return $this->streamCsvDownload($basename, [
            'S.No',
            'Task',
            'Assigned By',
            'Type',
            'Status',
            'Currently With',
            'Assigned On',
            'Expected On',
            'Updated On',
        ], $tasks, function ($task, int $index) {
            return [
                $index + 1,
                $task->process_assigned ?: '#'.$task->id,
                $task->assigned_by_name,
                $this->taskTypeLabel($task->task_type ?? null),
                $this->taskStatusLabel($task->task_status_by_tester ?? null),
                FreshHelpdesk::taskCurrentlyWith($task),
                $this->exportDate($task->assigned_on ?? null),
                $this->exportDate($task->expected_date_to_complete ?? null),
                $this->exportDate($task->updated_at ?? null),
            ];
        });
    }

    private function streamCsvDownload(string $basename, array $headers, Collection $rows, callable $mapRow): StreamedResponse
    {
        $filename = $basename.'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($headers, $rows, $mapRow) {
            $output = fopen('php://output', 'w');
            fwrite($output, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($output, $headers);

            foreach ($rows->values() as $index => $row) {
                fputcsv($output, array_map(fn ($value) => $this->exportValue($value), $mapRow($row, $index)));
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function exportValue($value): string
    {
        $value = trim((string) ($value ?? ''));

        return $value !== '' ? $value : '-';
    }

    private function exportDate($value): string
    {
        return $this->dashboardDate($value);
    }

    private function ticketModule(object $ticket): string
    {
        $module = trim((string) ($ticket->category ?? ''));

        if ($module !== '') {
            return $module;
        }

        return $this->ticketTypeLabel($ticket->request_type ?? null);
    }

    private function ticketTypeLabel(?string $type): string
    {
        $type = trim((string) $type);

        return $type !== '' ? \Illuminate\Support\Str::headline($type) : '-';
    }

    private function taskTypeLabel(?string $type): string
    {
        $type = trim((string) $type);

        return $type !== '' ? ucfirst($type) : '-';
    }

    private function taskStatusLabel(?string $status): string
    {
        $labels = FreshHelpdesk::taskStatusLabels();
        $status = trim((string) $status);

        return $labels[$status] ?? ($status !== '' ? \Illuminate\Support\Str::headline($status) : '-');
    }

    private function deptCodeForTicket(?string $submittedDeptCode): string
    {
        if (in_array(FreshHelpdesk::role(), [
            FreshHelpdesk::ROLE_STATE_ADMIN,
            FreshHelpdesk::ROLE_NIC_ADMIN,
        ], true)) {
            return $submittedDeptCode ?: 'ALL';
        }

        return FreshHelpdesk::deptCode() ?: '';
    }

    private function storeTicketAttachments(Request $request): array
    {
        if (!$request->hasFile('attachments')) {
            return [];
        }

        $stored = [];
        foreach ($request->file('attachments') as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $path = $file->store('helpdesk', 'public');
            $stored[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime' => $file->getClientMimeType(),
            ];
        }

        return $stored;
    }
}
