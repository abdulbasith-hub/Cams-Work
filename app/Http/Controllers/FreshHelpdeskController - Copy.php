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
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

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
        'task_type',
        'developer_userid',
    ];

    public function index(): RedirectResponse
    {
        return redirect()->route('fresh-helpdesk.dashboard');
    }

    public function dashboard(Request $request): View|RedirectResponse
    {
        abort_unless(FreshHelpdesk::canAccess(), 403);

        if ($this->dashboardHasStateQuery($request)) {
            session()->put(self::DASHBOARD_STATE_SESSION_KEY, $this->dashboardStateFromInput($request->query->all()));

            return redirect()->route('fresh-helpdesk.dashboard');
        }

        $dashboardState = session(self::DASHBOARD_STATE_SESSION_KEY, []);
        $activeTicketCard = $this->dashboardCard($dashboardState['ticket_card'] ?? null, 'in_progress');
        $activeTaskCard = $this->dashboardCard($dashboardState['task_card'] ?? null, 'total');
        $activeDeveloperTicketCard = $this->dashboardCard($dashboardState['dev_ticket_card'] ?? null, 'total');
        $activeDeveloperTaskCard = $this->dashboardCard($dashboardState['dev_task_card'] ?? null, 'total');
        $dashboardFilters = $this->dashboardFilters($dashboardState);
        $dashboardPriorityOptions = FreshHelpdesk::ticketDetailsForCurrentRole([], true)
            ->pluck('priority')
            ->filter()
            ->unique(fn ($priority) => strtolower((string) $priority))
            ->values();
        $ticketOptions = FreshHelpdesk::ticketDetailsForCurrentRole($dashboardFilters, true);
        $developerTicketOptions = FreshHelpdesk::ticketDetailsForCurrentRole([], true);
        $canViewTaskDashboard = $this->canAccessTaskManagement();
        $taskOptions = $canViewTaskDashboard ? FreshHelpdesk::tasksForCurrentRole(true) : collect();
        $canViewDeveloperSummary = $this->canViewDeveloperDashboardSummary();
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
        $isNicAdmin = FreshHelpdesk::role() === FreshHelpdesk::ROLE_NIC_ADMIN;
        $importantTickets = $isNicAdmin ? FreshHelpdesk::importantTickets() : collect();
        $importantTicketsCount = $isNicAdmin ? FreshHelpdesk::importantTicketsCount() : 0;

        return view('fresh-helpdesk.dashboard', [
            'role' => FreshHelpdesk::role(),
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

    public function taskDetails(Request $request): View|RedirectResponse
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
        $hasPageQuery = $request->query->has('page');

        if ($hasFilterQuery || $hasPageQuery) {
            if ($hasFilterQuery) {
                $filters = $this->taskDetailFiltersFromInput($request->query->all(), $role, $taskStatusLabels, $developerFilterOptions);
                session()->put(self::TASK_DETAIL_FILTER_SESSION_KEY, $filters);
                session()->put(self::TASK_DETAIL_PAGE_SESSION_KEY, 1);
            }

            if ($hasPageQuery) {
                session()->put(self::TASK_DETAIL_PAGE_SESSION_KEY, max(1, (int) $request->query('page')));
            }

            $routeParams = [];
            if ($request->filled('task')) {
                $routeParams['task'] = (string) $request->query('task');
            }

            return redirect()->route('fresh-helpdesk.task-details', $routeParams);
        }

        $filters = $this->taskDetailFiltersFromInput(
            session(self::TASK_DETAIL_FILTER_SESSION_KEY, []),
            $role,
            $taskStatusLabels,
            $developerFilterOptions
        );
        $page = max(1, (int) session(self::TASK_DETAIL_PAGE_SESSION_KEY, 1));

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

    public function taskDetailsFilter(Request $request): RedirectResponse
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

        return redirect()->route('fresh-helpdesk.task-details');
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

    public function ticketDetails(Request $request): View|RedirectResponse
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
        $hasPageQuery = $request->query->has('ticket_detail_page');

        if ($hasFilterQuery || $hasPageQuery) {
            if ($hasFilterQuery) {
                $filters = $this->ticketDetailFiltersFromInput($request->query->all(), $role, $statusOptions, $developerFilterOptions);
                session()->put(self::TICKET_DETAIL_FILTER_SESSION_KEY, $filters);
                session()->put(self::TICKET_DETAIL_PAGE_SESSION_KEY, 1);
            }

            if ($hasPageQuery) {
                session()->put(self::TICKET_DETAIL_PAGE_SESSION_KEY, max(1, (int) $request->query('ticket_detail_page')));
            }

            $routeParams = [];
            if ($request->filled('ticket')) {
                $routeParams['ticket'] = (string) $request->query('ticket');
            }
            if ($request->filled('task')) {
                $routeParams['task'] = (string) $request->query('task');
            }

            return redirect()->route('fresh-helpdesk.ticket-details', $routeParams);
        }

        $filters = $this->ticketDetailFiltersFromInput(
            session(self::TICKET_DETAIL_FILTER_SESSION_KEY, []),
            $role,
            $statusOptions,
            $developerFilterOptions
        );
        $ticketDetailPage = max(1, (int) session(self::TICKET_DETAIL_PAGE_SESSION_KEY, 1));

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
            $selectedTask = FreshHelpdesk::task((int) $request->query('task'));
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

    public function ticketDetailsFilter(Request $request): RedirectResponse
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

        return redirect()->route('fresh-helpdesk.ticket-details');
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
        $watchlistCc = $request->boolean('watchlist_notify')
            ? $this->internalCcEmails()
            : [];

	        try {
	            $ticket = DB::transaction(function () use ($validated, $watchlistCc) {
	                $ticket = FreshHelpdesk::createTicket($validated);
	                $forwardedRole = FreshHelpdesk::roleLabel($ticket->forwarded_to_role);
	                $message = 'A new ticket has been auto forwarded to '.$forwardedRole.'.';
	                $details = $watchlistCc ? ['Watchlist CC' => 'Enabled'] : [];
	
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
                ['Comment Visibility' => $isInternalComment ? 'Internal' : 'Public'],
                'comment',
                $isInternalComment
            );
        } else {
            $ticketRow = DB::transaction(fn () => FreshHelpdesk::forwardTicket($ticketRow, $action, $validated));
            $this->notifyTicket(
                $ticketRow,
                'Helpdesk Ticket Updated',
                'Ticket Workflow Updated',
                'A ticket action has been completed.',
                $this->ticketBaseCcEmails($ticketRow, $validated),
                [],
                $action
            );
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
        $this->notifyTask($task, 'New Helpdesk Task', 'Task Created', 'A new task has been assigned to Developer '.$developer->devename.'.');

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
        $this->notifyTask($taskRow, 'Helpdesk Task Updated', 'Task Workflow Updated', 'A task action has been completed.');

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
		                $validated['started_on'] = now('Asia/Kolkata')->format('Y-m-d H:i:s');
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
		            $rules['task_status'] = 'required|in:'.FreshHelpdesk::TASK_IN_PROGRESS.','.FreshHelpdesk::TASK_RESOLVED;
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
		            if (($request->has('forward_to_senior') || ($validated['forward_to_senior'] ?? null)) && ($validated['task_status'] ?? null) !== FreshHelpdesk::TASK_RESOLVED) {
		                throw ValidationException::withMessages([
		                    'task_status' => 'Please select Resolved before forwarding to Senior Developer.',
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
                && $this->isStateStageTicket($ticket),
            'state_to_user' => $role === FreshHelpdesk::ROLE_STATE_ADMIN
                && $this->isStateStageTicket($ticket),
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
        return $this->isTicketCreator($ticket)
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

    private function canActOnCurrentTicketStage(object $ticket): bool
    {
        $role = FreshHelpdesk::role();

        return match ($role) {
            FreshHelpdesk::ROLE_STATE_ADMIN => $this->isStateStageTicket($ticket),
            FreshHelpdesk::ROLE_NIC_ADMIN => $this->isNicStageTicket($ticket),
            FreshHelpdesk::ROLE_SENIOR_DEVELOPER => (string) $ticket->forwarded_to_role === FreshHelpdesk::ROLE_SENIOR_DEVELOPER
                && (string) $ticket->assigned_to_userid === FreshHelpdesk::userId()
                && in_array((string) $ticket->status, [FreshHelpdesk::TICKET_PENDING_SENIOR, FreshHelpdesk::TICKET_RETURNED_SENIOR, FreshHelpdesk::TICKET_NEED_CLARIFICATION], true),
            // Developer no longer changes the main ticket status here; they use the
            // separate, NIC-Admin-only developer_status field instead.
            default => false,
        };
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

    private function canRunTaskAction(object $task, string $action): bool
    {
        $role = FreshHelpdesk::role();
        $developerUserId = FreshHelpdesk::developerUserId();

        $status = (string) $task->task_status_by_tester;

        return match ($action) {
            'comment' => $this->canViewTask($task),
            'developer_resolve' => in_array($role, [FreshHelpdesk::ROLE_SENIOR_DEVELOPER, FreshHelpdesk::ROLE_DEVELOPER], true)
                && (string) ($task->developer_userid ?? '') === (string) $developerUserId
                && $status !== FreshHelpdesk::TASK_CLOSED
                && empty($task->verified_by)
                && empty($task->approved_by),
            'senior_confirm', 'senior_return', 'senior_reassign' => $role === FreshHelpdesk::ROLE_SENIOR_DEVELOPER
                && $status === FreshHelpdesk::TASK_RESOLVED
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
            FreshHelpdesk::ROLE_DEVELOPER => (string) $task->developer_userid === (string) $developerUserId
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

        $this->sendCommonMail($channels['to'], $mailSubject, $headline, $details + $extraDetails + ['Message' => $message], $channels['cc'], $failOnMailError);
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
        $userEmails = [$ticket->user_email ?? null];

        $toEmails = FreshHelpdesk::recipientEmailsForTicket($ticket);
        $ccEmails = $baseCcEmails;

        switch ($action) {
            case 'user_to_state':
            case 'reopen':
            case 'nic_to_state':
                $toEmails = $stateEmails;
                break;

            case 'state_to_nic':
            case 'senior_to_nic':
                $toEmails = $nicEmails;
                break;

            case 'state_to_user':
            case 'state_close':
                $toEmails = $userEmails;
                break;

            case 'nic_to_senior':
            case 'auto_forward_to_senior':
                $toEmails = $assignedDeveloperEmails ?: $latestSeniorEmails;
                break;

            case 'nic_to_developer':
                $toEmails = $assignedDeveloperEmails;
                $ccEmails = $this->mergeEmails($ccEmails, $seniorEmails);
                break;

            case 'senior_to_developer':
                $toEmails = $assignedDeveloperEmails;
                $ccEmails = $this->mergeEmails($ccEmails, $nicEmails);
                break;

            case 'developer_to_senior':
                $toEmails = $assignedDeveloperEmails ?: $latestSeniorEmails;
                $ccEmails = $this->mergeEmails($ccEmails, $nicEmails);
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
            return [
                $assignedDeveloperEmails ?: $nicEmails,
                $this->mergeEmails($baseCcEmails, $nicEmails),
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
        return [FreshHelpdesk::developerById($ticket->assigned_to_userid ?? null)->email ?? null];
    }

    private function ticketLatestSeniorEmails(object $ticket): array
    {
        $senior = FreshHelpdesk::latestTicketSeniorDeveloper((int) $ticket->id);

        return [FreshHelpdesk::developerById($senior->developer_userid ?? null)->email ?? null];
    }

    private function mergeEmails(array ...$emailGroups): array
    {
        return FreshHelpdesk::normalizeEmails($emailGroups);
    }

    private function notifyTask(object $task, string $subject, string $headline, string $message): void
    {
        $details = [
            'Task' => $task->process_assigned ?? '-',
            'Status' => FreshHelpdesk::taskStatusLabels()[$task->task_status_by_tester] ?? $task->task_status_by_tester,
            'Currently With' => $task->developer_name ?? $task->senior_name ?? 'NIC Admin',
            'Action By' => FreshHelpdesk::userName(),
        ];

        $this->sendCommonMail(FreshHelpdesk::recipientEmailsForTask($task), $subject, $headline, $details + ['Message' => $message]);
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

        return in_array($card, ['total', 'in_progress', 'urgent', 'returned', 'resolved_closed'], true)
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
	        $allowedTicketScopes = in_array($role, [FreshHelpdesk::ROLE_NIC_ADMIN, FreshHelpdesk::ROLE_STATE_ADMIN], true)
	            ? ['on_me', 'important']
	            : ['on_me'];

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

        $allowedTicketScopes = in_array($role, [FreshHelpdesk::ROLE_NIC_ADMIN, FreshHelpdesk::ROLE_STATE_ADMIN], true)
            ? ['', 'on_me', 'important']
            : ['', 'on_me'];
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
            'task_type' => trim((string) ($input['task_type'] ?? '')),
            'developer_userid' => trim((string) ($input['developer_userid'] ?? '')),
        ];

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
		                ->diffForHumans(now('Asia/Kolkata'), \Carbon\CarbonInterface::DIFF_ABSOLUTE, false, 2).' ago';
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

            $path = $file->store('fresh-helpdesk', 'public');
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
