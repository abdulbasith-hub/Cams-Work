<?php

namespace App\Http\Controllers;

use App\Models\DeveloperTask;
use App\Services\DeveloperTaskDashboardDataService;
use App\Services\HelpdeskMailNotificationService;
use App\Support\HelpdeskSession;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DeveloperTaskController extends Controller
{
    public function __construct(
        private HelpdeskMailNotificationService $notificationService,
        private DeveloperTaskDashboardDataService $dashboardDataService
    )
    {
    }

    public function index(Request $request): View
    {
        abort_unless(HelpdeskSession::canAccess(), 403);
        abort_unless(HelpdeskSession::isNicAdmin() || HelpdeskSession::isDeveloper(), 403);

        $taskPerPage = max(1, min((int) $request->integer('task_per_page', 10), 100));
        $testingTaskPerPage = max(1, min((int) $request->integer('testing_task_per_page', 10), 100));

        $visibleTasksQuery = DeveloperTask::query()->where('is_testing_task', false);

        if (HelpdeskSession::isDeveloper()) {
            $visibleTasksQuery->where('developer_userid', HelpdeskSession::userId());
        }

        $query = (clone $visibleTasksQuery)
            ->orderByDesc('assigned_on')
            ->orderByDesc('id');

        $tasks = $query->paginate($taskPerPage, ['*'], 'task_page')->withQueryString();
        $testingTasks = $this->fetchVisibleTestingTasks($testingTaskPerPage, 'testing_task_page');
        $testingTasksCount = $this->buildPendingTestingTasksQuery()->count();

        return view('developer-tasks.index', [
            'tasks' => $tasks,
            'testingTasks' => $testingTasks,
            'testingTasksCount' => $testingTasksCount,
            'developers' => HelpdeskSession::isNicAdmin() ? $this->fetchActiveDevelopers() : collect(),
            'pendingTestingTasks' => HelpdeskSession::isNicAdmin() ? $this->fetchPendingTestingTasks() : collect(),
            'isNicAdmin' => HelpdeskSession::isNicAdmin(),
            'isDeveloper' => HelpdeskSession::isDeveloper(),
            'taskPerPage' => $taskPerPage,
            'testingTaskPerPage' => $testingTaskPerPage,
        ]);
    }

    public function redirectToDashboard(): RedirectResponse
    {
        abort_unless(HelpdeskSession::canAccess(), 403);
        abort_unless(HelpdeskSession::isNicAdmin() || HelpdeskSession::isDeveloper(), 403);

        return redirect()->route('helpdesk.tasks.dashboard');
    }

    public function dashboard(): View
    {
        abort_unless(HelpdeskSession::canAccess(), 403);
        abort_unless(HelpdeskSession::isNicAdmin() || HelpdeskSession::isDeveloper(), 403);

        return view('developer-tasks.dashboard', [
            'dashboardData' => $this->dashboardDataService->taskDashboardData(),
            'testingTaskDashboardData' => $this->dashboardDataService->testingTaskDashboardData(),
            'isNicAdmin' => HelpdeskSession::isNicAdmin(),
            'isDeveloper' => HelpdeskSession::isDeveloper(),
        ]);
    }

    public function dashboardData(Request $request): JsonResponse
    {
        abort_unless(HelpdeskSession::canAccess(), 403);
        abort_unless(HelpdeskSession::isNicAdmin() || HelpdeskSession::isDeveloper(), 403);

        $dashboardType = $request->string('dashboard_type', 'task')->toString();
        $categoryKey = $request->string('category_key', 'assigned')->toString();
        $developerUserId = $request->string('developer_userid')->toString();

        $tasks = $this->dashboardDataService->dashboardTasksForType($dashboardType);
        if ($tasks === null) {
            return response()->json([
                'success' => false,
                'message' => 'Dashboard type is not available.',
            ], 404);
        }

        $payload = $this->dashboardDataService->buildDashboardPayload($tasks);
        $category = $payload['categories'][$categoryKey] ?? null;

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Dashboard category is not available.',
            ], 404);
        }

        if ($developerUserId !== '') {
            $developer = collect($category['developers'] ?? [])
                ->first(fn ($developer) => (string) ($developer['developer_userid'] ?? '') === (string) $developerUserId);

            if (!$developer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Developer details are not available for this category.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'category' => $category,
                'developer' => [
                    'developer_userid' => $developer['developer_userid'] ?? '',
                    'developer_name' => $developer['developer_name'] ?? '',
                    'count' => $developer['count'] ?? 0,
                ],
                'tasks' => $developer['tasks'] ?? [],
            ]);
        }

        return response()->json([
            'success' => true,
            'category' => $category,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(HelpdeskSession::canAccess(), 403);
        abort_unless(HelpdeskSession::isNicAdmin(), 403);

        $validated = $request->validate([
            'developer_userid' => 'required|string',
            'process_assigned' => 'required|string|max:1000',
            'task_type' => 'required|in:new,existing',
            'expected_date_to_complete' => 'nullable|string',
            'is_testing_task' => 'nullable|boolean',
            'testing_task_description' => 'nullable|string|max:2000',
        ]);

        $developer = $this->findDeveloper($validated['developer_userid']);
        if (!$developer) {
            return back()->withErrors(['developer_userid' => 'Selected developer is invalid.'])->withInput();
        }

        $currentTimestamp = $this->currentTimestamp();

        $task = DeveloperTask::create([
            'assigned_by_userid' => HelpdeskSession::userId(),
            'assigned_by_name' => HelpdeskSession::userName(),
            'developer_userid' => (string) $developer->devuserid,
            'developer_name' => $developer->devename,
            'process_assigned' => $validated['process_assigned'],
            'task_type' => $validated['task_type'],
            'is_testing_task' => $request->boolean('is_testing_task'),
            'testing_task_description' => $validated['testing_task_description'] ?? null,
            'assigned_on' => $currentTimestamp,
            'expected_date_to_complete' => $this->parseKolkataDateTime($validated['expected_date_to_complete'] ?? null),
            'created_at' => $currentTimestamp,
            'updated_at' => $currentTimestamp,
        ]);

        $this->notificationService->developerTaskAssigned($task, $developer->email ?? null);

        return redirect()->route('helpdesk.tasks.list')->with('success', 'Task assigned successfully.');
    }

    public function show(DeveloperTask $task): View
    {
        abort_unless(HelpdeskSession::canAccess(), 403);
        $this->authorizeTaskAccess($task);

        return view('developer-tasks.show', [
            'task' => $task,
            'isNicAdmin' => HelpdeskSession::isNicAdmin(),
            'isDeveloper' => HelpdeskSession::isDeveloper(),
        ]);
    }

    public function testingTasks(Request $request): JsonResponse
    {
        abort_unless(HelpdeskSession::canAccess(), 403);
        abort_unless(HelpdeskSession::isNicAdmin() || HelpdeskSession::isDeveloper(), 403);

        $perPage = max(1, min((int) $request->integer('testing_task_per_page', 10), 100));
        $tasks = $this->fetchVisibleTestingTasks($perPage, 'testing_task_page');

        return response()->json([
            'tasks' => $tasks->items(),
            'count' => $tasks->total(),
            'pagination' => $tasks->appends([
                'testing_task_per_page' => $perPage,
            ])->links('pagination::bootstrap-4')->render(),
            'current_page' => $tasks->currentPage(),
        ]);
    }

    public function update(Request $request, DeveloperTask $task): RedirectResponse
    {
        abort_unless(HelpdeskSession::canAccess(), 403);
        $this->authorizeTaskAccess($task);

        $rules = [
            'process_assigned' => 'required|string|max:1000',
            'task_type' => 'required|in:new,existing',
            'expected_date_to_complete' => 'nullable|string',
            'is_testing_task' => 'nullable|boolean',
            'testing_task_description' => 'nullable|string|max:2000',
            'started_on' => 'nullable|string',
            'completed_on' => 'nullable|string',
            'remarks_by_developer' => 'nullable|string|max:1000',
            'task_status_by_tester' => 'nullable|string|max:100',
            'remarks_by_project_head' => 'nullable|string|max:1000',
            'verifier_feedback' => 'nullable|string|max:1000',
            'verified_by' => 'nullable|string|max:150',
            'verified_on' => 'nullable|string',
            'remarks_by_verifier' => 'nullable|string|max:1000',
            'approved_by' => 'nullable|string|max:150',
            'approved_on' => 'nullable|string',
            'hosted_in_staging' => 'nullable|boolean',
            'deployed_in_live_server' => 'nullable|boolean',
        ];

        $validated = $request->validate($rules);
        $currentTimestamp = $this->currentTimestamp();

        if (HelpdeskSession::isDeveloper()) {
            $task->update([
                'started_on' => array_key_exists('started_on', $validated)
                    ? $this->parseKolkataDateTime($validated['started_on'])
                    : $task->started_on,
                'completed_on' => array_key_exists('completed_on', $validated)
                    ? $this->parseKolkataDateTime($validated['completed_on'])
                    : $task->completed_on,
                'remarks_by_developer' => $validated['remarks_by_developer'] ?? null,
                'task_status_by_tester' => $validated['task_status_by_tester'] ?? null,
                'hosted_in_staging' => $request->boolean('hosted_in_staging'),
                'deployed_in_live_server' => $request->boolean('deployed_in_live_server'),
                'updated_at' => $currentTimestamp,
            ]);
        } else {
            $task->update([
                'process_assigned' => $validated['process_assigned'],
                'task_type' => $validated['task_type'],
                'is_testing_task' => $request->boolean('is_testing_task'),
                'testing_task_description' => array_key_exists('testing_task_description', $validated)
                    ? $validated['testing_task_description']
                    : $task->testing_task_description,
                'expected_date_to_complete' => $this->parseKolkataDateTime($validated['expected_date_to_complete'] ?? null),
                'started_on' => $this->parseKolkataDateTime($validated['started_on'] ?? null),
                'completed_on' => $this->parseKolkataDateTime($validated['completed_on'] ?? null),
                'remarks_by_developer' => $validated['remarks_by_developer'] ?? null,
                'task_status_by_tester' => $validated['task_status_by_tester'] ?? null,
                'remarks_by_project_head' => $validated['remarks_by_project_head'] ?? null,
                'verifier_feedback' => $validated['verifier_feedback'] ?? null,
                'verified_by' => $validated['verified_by'] ?? null,
                'verified_on' => $this->parseKolkataDateTime($validated['verified_on'] ?? null),
                'remarks_by_verifier' => $validated['remarks_by_verifier'] ?? null,
                'approved_by' => $validated['approved_by'] ?? null,
                'approved_on' => $this->parseKolkataDateTime($validated['approved_on'] ?? null),
                'hosted_in_staging' => $request->boolean('hosted_in_staging'),
                'deployed_in_live_server' => $request->boolean('deployed_in_live_server'),
                'updated_at' => $currentTimestamp,
            ]);
        }

        return redirect()->route('helpdesk.tasks.show', $task)->with('success', 'Task updated successfully.');
    }

    private function authorizeTaskAccess(DeveloperTask $task): void
    {
        if (HelpdeskSession::isNicAdmin()) {
            return;
        }

        if (HelpdeskSession::isDeveloper()) {
            abort_unless((string) $task->developer_userid === (string) HelpdeskSession::userId(), 403);
            return;
        }

        abort(403);
    }

    private function fetchActiveDevelopers()
    {
        $ticketCountsSubquery = DB::table('audit.helpdesk_ticket_assignments')
            ->select(
                DB::raw('developer_userid::text as developer_userid'),
                DB::raw('COUNT(DISTINCT ticket_id) as assigned_tickets_count'),
                DB::raw("COUNT(DISTINCT CASE WHEN status = 'assigned' THEN ticket_id END) as pending_tickets_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN status = 'returned' THEN ticket_id END) as completed_tickets_count")
	            )
	            ->whereNotNull('developer_userid')
	            ->where('status', '!=', 'watchlist')
	            ->groupBy(DB::raw('developer_userid::text'));

        return DB::table('audit.dev_userdetails as dev')
            ->leftJoin('audit.developer_tasks as tasks', function ($join) {
                $join->on(DB::raw('tasks.developer_userid::text'), '=', DB::raw('dev.devuserid::text'));
            })
            ->leftJoinSub($ticketCountsSubquery, 'ticket_counts', function ($join) {
                $join->on(DB::raw('ticket_counts.developer_userid'), '=', DB::raw('dev.devuserid::text'));
            })
            ->where('statusflag', 'Y')
            ->groupBy(
                'dev.devuserid',
                'dev.devename',
                'dev.email',
                'ticket_counts.assigned_tickets_count',
                'ticket_counts.pending_tickets_count',
                'ticket_counts.completed_tickets_count'
            )
            ->orderBy('dev.devename')
            ->select(
                'dev.devuserid',
                'dev.devename',
                'dev.email',
                DB::raw('COUNT(tasks.id) as assigned_tasks_count'),
                DB::raw('COUNT(CASE WHEN tasks.id IS NOT NULL AND tasks.completed_on IS NULL THEN 1 END) as pending_tasks_count'),
                DB::raw('COUNT(CASE WHEN tasks.id IS NOT NULL AND tasks.completed_on IS NOT NULL THEN 1 END) as completed_tasks_count'),
                DB::raw('COALESCE(ticket_counts.assigned_tickets_count, 0) as assigned_tickets_count'),
                DB::raw('COALESCE(ticket_counts.pending_tickets_count, 0) as pending_tickets_count'),
                DB::raw('COALESCE(ticket_counts.completed_tickets_count, 0) as completed_tickets_count')
            )
            ->get();
    }

    private function dashboardTasksForType(string $dashboardType): ?Collection
    {
        if ($dashboardType === 'testing') {
            return $this->buildVisibleTestingTasksQuery()
                ->orderBy('developer_name')
                ->orderByDesc('assigned_on')
                ->orderByDesc('id')
                ->get();
        }

        if ($dashboardType !== 'task') {
            return null;
        }

        $query = DeveloperTask::query()
            ->where('is_testing_task', false);

        if (HelpdeskSession::isDeveloper()) {
            $query->where('developer_userid', HelpdeskSession::userId());
        }

        return $query
            ->orderBy('developer_name')
            ->orderByDesc('assigned_on')
            ->orderByDesc('id')
            ->get();
    }

    private function findDeveloper(string $developerUserId): ?object
    {
        return DB::table('audit.dev_userdetails')
            ->where('devuserid', trim($developerUserId))
            ->where('statusflag', 'Y')
            ->select('devuserid', 'devename', 'email')
            ->first();
    }

    private function fetchPendingTestingTasks()
    {
        return DeveloperTask::query()
            ->where('is_testing_task', false)
            ->whereNull('completed_on')
            ->where(function ($query) {
                $query->whereNull('task_status_by_tester')
                    ->orWhereRaw('LOWER(task_status_by_tester) NOT IN (?, ?)', ['closed', 'completed']);
            })
            ->orderByDesc('assigned_on')
            ->orderByDesc('id')
            ->get(['id', 'process_assigned', 'developer_name', 'task_status_by_tester']);
    }

    private function fetchVisibleTestingTasks(int $perPage = 10, string $pageName = 'page'): LengthAwarePaginator
    {
        $query = $this->buildVisibleTestingTasksQuery()
            ->orderByDesc('assigned_on')
            ->orderByDesc('id');

        $paginator = $query->paginate($perPage, ['*'], $pageName)->withQueryString();

        return $paginator->through(function (DeveloperTask $task) {
            return [
                'id' => $task->id,
                'process_assigned' => $task->process_assigned,
                'developer_name' => $task->developer_name,
                'task_type' => ucfirst($task->task_type),
                'task_type_value' => $task->task_type,
                'testing_task_description' => $task->testing_task_description ?: '-',
                'testing_task_description_value' => $task->testing_task_description ?? '',
                'assigned_on' => optional($task->assigned_on)->format('d/m/Y h:i A') ?: '-',
                'expected_date_to_complete' => optional($task->expected_date_to_complete)->format('d/m/Y h:i A') ?: '-',
                'expected_date_to_complete_value' => optional($task->expected_date_to_complete)->format('Y-m-d\TH:i') ?: '',
                'started_on' => optional($task->started_on)->format('d/m/Y h:i A') ?: '-',
                'started_on_value' => optional($task->started_on)->format('Y-m-d\TH:i') ?: '',
                'completed_on' => optional($task->completed_on)->format('d/m/Y h:i A') ?: '-',
                'completed_on_value' => optional($task->completed_on)->format('Y-m-d\TH:i') ?: '',
                'task_status_by_tester' => $task->task_status_by_tester ?: '-',
                'task_status_by_tester_value' => $task->task_status_by_tester ?? '',
                'remarks_by_developer' => $task->remarks_by_developer ?: '-',
                'remarks_by_developer_value' => $task->remarks_by_developer ?? '',
                'remarks_by_project_head_value' => $task->remarks_by_project_head ?? '',
                'verifier_feedback_value' => $task->verifier_feedback ?? '',
                'verified_by_value' => $task->verified_by ?? '',
                'verified_on_value' => optional($task->verified_on)->format('Y-m-d\TH:i') ?: '',
                'remarks_by_verifier_value' => $task->remarks_by_verifier ?? '',
                'approved_by_value' => $task->approved_by ?? '',
                'approved_on_value' => optional($task->approved_on)->format('Y-m-d\TH:i') ?: '',
                'hosted_in_staging_value' => (bool) $task->hosted_in_staging,
                'deployed_in_live_server_value' => (bool) $task->deployed_in_live_server,
                'update_url' => route('helpdesk.tasks.update', $task),
            ];
        });
    }

    private function buildVisibleTestingTasksQuery()
    {
        $query = DeveloperTask::query()
            ->where('is_testing_task', true);

        if (HelpdeskSession::isDeveloper()) {
            $query->where('developer_userid', HelpdeskSession::userId());
        }

        return $query;
    }

    private function buildPendingTestingTasksQuery()
    {
        return $this->buildVisibleTestingTasksQuery()
            ->whereNull('completed_on')
            ->where(function ($builder) {
                $builder->whereNull('task_status_by_tester')
                    ->orWhereRaw('LOWER(TRIM(task_status_by_tester)) NOT IN (?, ?)', ['closed', 'completed']);
            });
    }

    private function currentTimestamp(): Carbon
    {
        return Carbon::now('Asia/Kolkata');
    }

    private function parseKolkataDateTime(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        $value = trim($value);

        foreach (['d/m/Y H:i', 'd/m/Y H:i:A', 'd/m/Y H:i A', 'Y-m-d H:i', 'Y-m-d\TH:i'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value, 'Asia/Kolkata');
            } catch (\Throwable $exception) {
            }
        }

        return Carbon::parse($value, 'Asia/Kolkata');
    }

    private function buildDashboardPayload(Collection $tasks): array
    {
        $now = $this->currentTimestamp();

        $categories = [
            'assigned' => [
                'label' => 'Assigned Tasks',
                'accent' => 'assigned',
                'matches' => fn (DeveloperTask $task): bool => true,
            ],
            'completed' => [
                'label' => 'Completed Tasks',
                'accent' => 'completed',
                'matches' => fn (DeveloperTask $task): bool => !is_null($task->completed_on),
            ],
            'in_progress' => [
                'label' => 'In Progress',
                'accent' => 'in-progress',
                'matches' => fn (DeveloperTask $task): bool => !is_null($task->started_on) && is_null($task->completed_on),
            ],
            'pending' => [
                'label' => 'Pending Tasks',
                'accent' => 'pending',
                'matches' => fn (DeveloperTask $task): bool => is_null($task->started_on) && is_null($task->completed_on),
            ],
            'overdue' => [
                'label' => 'Overdue on Expected Date',
                'accent' => 'overdue',
                'matches' => fn (DeveloperTask $task): bool => is_null($task->completed_on)
                    && !is_null($task->expected_date_to_complete)
                    && $task->expected_date_to_complete->lt($now),
            ],
            'completed_before_due' => [
                'label' => 'Completed Before Due Date',
                'accent' => 'before-due',
                'matches' => fn (DeveloperTask $task): bool => !is_null($task->completed_on)
                    && !is_null($task->expected_date_to_complete)
                    && $task->completed_on->lte($task->expected_date_to_complete),
            ],
        ];

        $cards = [];
        $categoryPayload = [];

        foreach ($categories as $key => $category) {
            $matchingTasks = $tasks
                ->filter($category['matches'])
                ->values();

            $developers = $matchingTasks
                ->groupBy(fn (DeveloperTask $task) => (string) $task->developer_userid)
                ->map(function (Collection $developerTasks) use ($now) {
                    $firstTask = $developerTasks->first();

                    return [
                        'developer_userid' => (string) $firstTask->developer_userid,
                        'developer_name' => $firstTask->developer_name,
                        'count' => $developerTasks->count(),
                        'tasks' => $developerTasks
                            ->map(fn (DeveloperTask $task) => $this->transformDashboardTask($task, $now))
                            ->values()
                            ->all(),
                    ];
                })
                ->sortByDesc('count')
                ->values()
                ->all();

            $count = $matchingTasks->count();

            $cards[] = [
                'key' => $key,
                'label' => $category['label'],
                'count' => $count,
                'accent' => $category['accent'],
            ];

            $categoryPayload[$key] = [
                'key' => $key,
                'label' => $category['label'],
                'count' => $count,
                'developers' => $developers,
            ];
        }

        return [
            'cards' => $cards,
            'categories' => $categoryPayload,
        ];
    }

    private function transformDashboardTask(DeveloperTask $task, Carbon $now): array
    {
        return [
            'id' => $task->id,
            'process_assigned' => $task->process_assigned,
            'task_type' => ucfirst($task->task_type),
            'developer_name' => $task->developer_name,
            'created_on' => optional($task->created_at)->format('d/m/Y h:i A') ?: '-',
            'updated_on' => optional($task->updated_at)->format('d/m/Y h:i A') ?: '-',
            'assigned_on' => optional($task->assigned_on)->format('d/m/Y h:i A') ?: '-',
            'expected_date_to_complete' => optional($task->expected_date_to_complete)->format('d/m/Y h:i A') ?: '-',
            'started_on' => optional($task->started_on)->format('d/m/Y h:i A') ?: '-',
            'completed_on' => optional($task->completed_on)->format('d/m/Y h:i A') ?: '-',
            'remarks_by_developer' => $task->remarks_by_developer ?: '-',
            'task_status_by_tester' => $task->task_status_by_tester ?: '-',
            'progress_status' => $this->resolveProgressStatus($task, $now),
            'schedule_status' => $this->resolveScheduleStatus($task, $now),
            'show_url' => route('helpdesk.tasks.show', $task),
        ];
    }

    private function resolveProgressStatus(DeveloperTask $task, Carbon $now): string
    {
        if (!is_null($task->completed_on)) {
            return 'Completed';
        }

        if (!is_null($task->expected_date_to_complete) && $task->expected_date_to_complete->lt($now)) {
            return 'Overdue';
        }

        if (!is_null($task->started_on)) {
            return 'In Progress';
        }

        return 'Pending';
    }

    private function resolveScheduleStatus(DeveloperTask $task, Carbon $now): string
    {
        if (is_null($task->expected_date_to_complete)) {
            return 'No due date';
        }

        if (!is_null($task->completed_on)) {
            return $task->completed_on->lte($task->expected_date_to_complete)
                ? 'Completed before due date'
                : 'Completed after due date';
        }

        return $task->expected_date_to_complete->lt($now)
            ? 'Overdue'
            : 'Within due date';
    }
}
