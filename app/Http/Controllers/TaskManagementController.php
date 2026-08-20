<?php

namespace App\Http\Controllers;

use App\Models\TaskManagementTask;
use App\Services\HelpdeskMailNotificationService;
use App\Services\HelpdeskV2Session;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TaskManagementController extends Controller
{
    public function __construct(private HelpdeskMailNotificationService $notificationService)
    {
    }

    public function dashboard(Request $request): RedirectResponse
    {
        abort_unless($this->canAccess(), 403);
        abort_unless(HelpdeskV2Session::isNicAdmin(), 403);

        return redirect()->route('helpdesk-v2.dashboard', [
            'role' => HelpdeskV2Session::role(),
            'pane' => 'tasks',
        ]);
    }

    public function details(Request $request): View
    {
        abort_unless($this->canAccess(), 403);

        $filters = [
            'developer_userid' => trim((string) $request->input('developer_userid', '')),
            'status' => trim((string) $request->input('status', '')),
            'task_type' => trim((string) $request->input('task_type', '')),
            'assigned_from' => trim((string) $request->input('assigned_from', '')),
            'assigned_to' => trim((string) $request->input('assigned_to', '')),
        ];

        $query = $this->taskDetailsQuery();

        if ($filters['developer_userid'] !== '' && $this->canFilterTaskDeveloper()) {
            $query->where('developer_userid', $filters['developer_userid']);
        }

        if (in_array($filters['task_type'], ['new', 'existing'], true)) {
            $query->where('task_type', $filters['task_type']);
        }

        if ($filters['assigned_from'] !== '') {
            $query->whereDate('assigned_on', '>=', $filters['assigned_from']);
        }

        if ($filters['assigned_to'] !== '') {
            $query->whereDate('assigned_on', '<=', $filters['assigned_to']);
        }

        $filteredTasks = $query
            ->orderByDesc('assigned_on')
            ->orderByDesc('id')
            ->get();

        $stats = $this->stats($filteredTasks);
        $statusOptions = $this->taskStatusOptions();
        $tasks = array_key_exists($filters['status'], $statusOptions)
            ? $filteredTasks->filter(fn (TaskManagementTask $task) => $task->statusKey() === $filters['status'])->values()
            : $filteredTasks;

        return view('helpdesk-v2.taskdetails', [
            'tasks' => $tasks,
            'stats' => $stats,
            'filters' => $filters,
            'developers' => $this->activeDevelopers(),
            'canFilterDeveloper' => $this->canFilterTaskDeveloper(),
            'canCreate' => $this->canCreate(),
            'role' => HelpdeskV2Session::role(),
            'roleLabel' => HelpdeskV2Session::roleLabel(),
            'statusOptions' => $statusOptions,
            'taskTypeOptions' => [
                'new' => 'New',
                'existing' => 'Existing',
            ],
        ]);
    }

    public function create(): View
    {
        abort_unless($this->canCreate(), 403);

        return view('task-management.create', [
            'developers' => $this->activeDevelopers(),
            'moduleOptions' => $this->moduleOptions(),
            'minimumTaskDateTime' => now('Asia/Kolkata')->format('Y-m-d\TH:i'),
        ]);
    }

    public function show(TaskManagementTask $task): View
    {
        abort_unless($this->canAccess(), 403);
        abort_unless($this->canViewTask($task), 403);

        return view('task-management.show', [
            'task' => $task,
            'histories' => $task->histories()
                ->orderBy('performed_at')
                ->orderBy('id')
                ->get(),
            'canComplete' => !$task->completed_on && HelpdeskV2Session::isDeveloperPerson() && $this->canUpdateTask($task),
            'canEditSchedule' => $this->canEditSchedule($task),
            'canVerifyTesting' => $this->canVerifyTesting($task),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->canCreate(), 403);

        $validated = $request->validate([
            'developer_userid' => 'required|string',
            'task_type' => 'required|in:new,existing',
            'existing_module' => 'nullable|required_if:task_type,existing|string|max:1000',
            'new_module' => 'nullable|required_if:task_type,new|string|max:1000',
            'description' => 'nullable|string|max:2000',
            'assigned_on' => 'required|date',
            'expected_date_to_complete' => 'nullable|date|after_or_equal:assigned_on',
        ]);

        $developer = $this->findDeveloper($validated['developer_userid']);

        if (!$developer) {
            return back()
                ->withErrors(['developer_userid' => 'Selected developer is invalid.'])
                ->withInput();
        }

        $taskText = $validated['task_type'] === 'new'
            ? $validated['new_module']
            : $validated['existing_module'];

        $assignedOn = $this->parseKolkataDateTime($validated['assigned_on']);
        $expectedOn = $this->parseKolkataDateTime($validated['expected_date_to_complete'] ?? null);
        $now = now('Asia/Kolkata');

        $task = TaskManagementTask::create([
            'assigned_by_userid' => HelpdeskV2Session::userId(),
            'assigned_by_name' => HelpdeskV2Session::userName(),
            'developer_userid' => (string) $developer->devuserid,
            'developer_name' => $developer->devename,
            'process_assigned' => $taskText,
            'task_type' => $validated['task_type'],
            'is_testing_task' => false,
            'testing_task_description' => $validated['description'] ?? null,
            'assigned_on' => $assignedOn,
            'expected_date_to_complete' => $expectedOn,
            'remarks_by_project_head' => $this->assignmentTimelineText($developer->devename, $assignedOn, $expectedOn),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->recordTaskHistory(
            $task,
            'assigned',
            'Assignment',
            'Completed',
            $task->remarks_by_project_head,
            $assignedOn ?: $now,
            [],
            HelpdeskV2Session::roleLabel(HelpdeskV2Session::ROLE_NIC_ADMIN)
        );

        $this->notificationService->developerTaskAssigned($task, $developer->email ?? null, $this->taskAssignmentCcEmails());

        return redirect()
            ->route('helpdesk-v2.dashboard', [
                'role' => HelpdeskV2Session::role(),
                'pane' => 'tasks',
            ])
            ->with('success', 'Task assigned successfully.');
    }

    public function complete(Request $request, TaskManagementTask $task): RedirectResponse
    {
        abort_unless($this->canAccess(), 403);
        abort_unless(HelpdeskV2Session::isDeveloperPerson() && $this->canUpdateTask($task), 403);

        $validated = $request->validate([
            'developer_status' => 'required|in:in_progress,completed',
            'remarks_by_developer' => 'required|string|max:2000',
        ]);

        $now = now('Asia/Kolkata');
        $updates = [
            'started_on' => $task->started_on ?: $now,
            'remarks_by_developer' => $validated['remarks_by_developer'],
            'updated_at' => $now,
        ];

        if ($validated['developer_status'] === 'completed') {
            $updates['completed_on'] = $now;
        }

        $task->update($updates);
        $task->refresh();

	        $this->recordTaskHistory(
	            $task,
	            'developer_status',
	            'Developer Work',
	            $validated['developer_status'] === 'completed' ? 'Completed' : 'In Progress',
	            $validated['remarks_by_developer'],
	            $now,
	            ['visible_to' => $this->historyAudienceForCurrentRole()],
	            HelpdeskV2Session::roleLabel(HelpdeskV2Session::ROLE_DEVELOPER)
	        );

        return redirect()
            ->route('task-management.show', $task)
            ->with('success', 'Developer status updated.');
    }

    public function updateSchedule(Request $request, TaskManagementTask $task): RedirectResponse
    {
        abort_unless($this->canCreate(), 403);
        abort_unless($this->canViewTask($task), 403);

        $validated = $request->validate([
            'assigned_on' => 'required|date',
            'expected_date_to_complete' => 'nullable|date|after_or_equal:assigned_on',
            'comment' => 'nullable|string|max:2000',
        ]);

        $assignedOn = $this->parseKolkataDateTime($validated['assigned_on']);
        $expectedOn = $this->parseKolkataDateTime($validated['expected_date_to_complete'] ?? null);
        $scheduleText = 'Schedule updated by '.HelpdeskV2Session::userName().' on '.now('Asia/Kolkata')->format('d/m/Y h:i A')
            .'. Assigned on '.$this->displayDateTime($assignedOn).'. Expected on '.$this->displayDateTime($expectedOn).'.';

        if (!empty($validated['comment'])) {
            $scheduleText .= ' Comment: '.trim($validated['comment']);
        }

        $task->update([
            'assigned_on' => $assignedOn,
            'expected_date_to_complete' => $expectedOn,
            'remarks_by_project_head' => $this->appendTimelineText($task->remarks_by_project_head, $scheduleText),
            'updated_at' => now('Asia/Kolkata'),
        ]);
        $task->refresh();

        $this->recordTaskHistory(
            $task,
            'schedule_update',
            'Schedule',
            'Updated',
            $scheduleText,
            now('Asia/Kolkata'),
	            [
	                'assigned_on' => $assignedOn?->format('Y-m-d H:i:s'),
	                'expected_date_to_complete' => $expectedOn?->format('Y-m-d H:i:s'),
	                'visible_to' => $this->historyAudienceForCurrentRole(),
	            ],
	            HelpdeskV2Session::roleLabel()
	        );

        return redirect()
            ->route('task-management.show', $task)
            ->with('success', 'Task schedule updated.');
    }

    public function addDescription(Request $request, TaskManagementTask $task): RedirectResponse
    {
        abort_unless($this->canAccess(), 403);
        abort_unless($this->canViewTask($task), 403);

        $validated = $request->validate([
            'description_note' => 'required|string|max:2000',
        ]);

        $line = 'Description added by '.HelpdeskV2Session::userName().' ('.HelpdeskV2Session::roleLabel().') on '
            .now('Asia/Kolkata')->format('d/m/Y h:i A').': '.trim($validated['description_note']);

        $task->update([
            'remarks_by_project_head' => $this->appendTimelineText($task->remarks_by_project_head, $line),
            'updated_at' => now('Asia/Kolkata'),
        ]);
        $task->refresh();

        $this->recordTaskHistory(
            $task,
            'description',
            'Description',
	            'Added',
	            $validated['description_note'],
	            now('Asia/Kolkata'),
	            ['visible_to' => $this->historyAudienceForCurrentRole()],
	            HelpdeskV2Session::roleLabel()
	        );

        return redirect()
            ->route('task-management.show', $task)
            ->with('success', 'Description added.');
    }

    public function verifyTesting(Request $request, TaskManagementTask $task): RedirectResponse
    {
        abort_unless($this->canAccess(), 403);
        abort_unless($this->canVerifyTesting($task), 403);

        $validated = $request->validate([
            'testing_description' => 'required|string|max:2000',
        ]);

        $now = now('Asia/Kolkata');
        $line = 'Testing verified by '.HelpdeskV2Session::userName().' (Senior Developer) on '
            .$now->format('d/m/Y h:i A').': '.trim($validated['testing_description']);

        $task->update([
            'task_status_by_tester' => 'Verified by Senior Developer',
            'remarks_by_verifier' => $validated['testing_description'],
            'verified_by' => HelpdeskV2Session::userName(),
            'verified_on' => $now,
            'remarks_by_project_head' => $this->appendTimelineText($task->remarks_by_project_head, $line),
            'updated_at' => $now,
        ]);
        $task->refresh();

        $this->recordTaskHistory(
            $task,
            'testing_verified',
            'Senior Testing',
	            'Sent to NIC Admin',
	            $validated['testing_description'],
	            $now,
	            ['visible_to' => $this->historyAudienceForCurrentRole()],
	            HelpdeskV2Session::roleLabel(HelpdeskV2Session::ROLE_LAYER_LEAD)
	        );

        $this->notificationService->taskTestingSentToNic($task);

        return redirect()
            ->route('task-management.show', $task)
            ->with('success', 'Testing sent to NIC Admin.');
    }

    private function visibleTasksQuery(string $activeTaskTab = 'all')
    {
        $query = TaskManagementTask::query()
            ->where('is_testing_task', false);

        if ($this->canManageAllTasks()) {
            return $query;
        }

        if (HelpdeskV2Session::isLayerLead()) {
            $developerIds = $this->currentDeveloperIds();

            return match ($activeTaskTab) {
                'developer' => $query->whereIn('assigned_by_userid', $developerIds),
                'testing' => $query
                    ->whereNotNull('completed_on')
                    ->whereNull('verified_on'),
                default => $query
                    ->whereNotIn('assigned_by_userid', $developerIds)
                    ->whereNull('completed_on'),
            };
        }

        if (!$this->canCreate()) {
            $query->whereIn('developer_userid', $this->currentDeveloperIds());
        }

        return $query;
    }

    private function taskDetailsQuery()
    {
        $query = TaskManagementTask::query()
            ->where('is_testing_task', false);

        if ($this->canManageAllTasks() || HelpdeskV2Session::isLayerLead()) {
            return $query;
        }

        return $query->whereIn('developer_userid', $this->currentDeveloperIds());
    }

    private function taskStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'overdue' => 'Overdue',
            'completed' => 'Testing Stage',
        ];
    }

    private function canFilterTaskDeveloper(): bool
    {
        return $this->canManageAllTasks() || HelpdeskV2Session::isLayerLead();
    }

    private function stats(Collection $tasks): array
    {
        return [
            'total' => $tasks->count(),
            'pending' => $tasks->filter(fn (TaskManagementTask $task) => $task->statusKey() === 'pending')->count(),
            'in_progress' => $tasks->filter(fn (TaskManagementTask $task) => $task->statusKey() === 'in_progress')->count(),
            'overdue' => $tasks->filter(fn (TaskManagementTask $task) => $task->statusKey() === 'overdue')->count(),
            'completed' => $tasks->filter(fn (TaskManagementTask $task) => $task->statusKey() === 'completed')->count(),
        ];
    }

    private function activeDevelopers(): Collection
    {
        return HelpdeskV2Session::activeDevelopers();
    }

    private function moduleOptions(): Collection
    {
        return DB::table('audit.mst_menu')
            ->where(function ($query) {
                $query->whereNotNull('menuename')
                    ->orWhereNotNull('menuurl');
            })
            ->selectRaw("COALESCE(NULLIF(TRIM(menuename), ''), NULLIF(TRIM(menuurl), '')) as module_name")
            ->orderBy('module_name')
            ->pluck('module_name')
            ->filter()
            ->unique()
            ->values();
    }

    private function findDeveloper(string $developerUserId): ?object
    {
        return DB::table('audit.dev_userdetails')
            ->where('devuserid', trim($developerUserId))
            ->where('statusflag', 'Y')
            ->first(['devuserid', 'devename', 'email']);
    }

    private function parseKolkataDateTime(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        return Carbon::parse($value, 'Asia/Kolkata');
    }

    private function assignmentTimelineText(string $developerName, ?Carbon $assignedOn, ?Carbon $expectedOn): string
    {
        return 'Assigned to '.$developerName.' by '.HelpdeskV2Session::userName().' on '.$this->displayDateTime($assignedOn)
            .'. Expected on '.$this->displayDateTime($expectedOn).'.';
    }

    private function appendTimelineText(?string $existing, string $line): string
    {
        $existing = trim((string) $existing);

        return $existing === '' ? $line : $existing.PHP_EOL.$line;
    }

    private function displayDateTime(?Carbon $value): string
    {
        return $value ? $value->format('d/m/Y h:i A') : '-';
    }

    private function historyAudienceForCurrentRole(): array
    {
        if (HelpdeskV2Session::isNicAdmin()) {
            return ['Senior Developer', 'Developer'];
        }

        if (HelpdeskV2Session::isLayerLead()) {
            return ['NIC Admin', 'Senior Developer', 'Developer'];
        }

        return ['NIC Admin', 'Senior Developer', 'Developer'];
    }

    private function recordTaskHistory(
        TaskManagementTask $task,
        string $actionKey,
        string $stage,
        string $status,
        ?string $comment,
        ?Carbon $performedAt = null,
        array $metadata = [],
        ?string $performedByRole = null
    ): void {
        $task->histories()->create([
            'action_key' => $actionKey,
            'stage' => $stage,
            'status' => $status,
            'comment' => $comment,
            'performed_by_userid' => HelpdeskV2Session::userId(),
            'performed_by_name' => HelpdeskV2Session::userName(),
            'performed_by_role' => $performedByRole ?: HelpdeskV2Session::roleLabel(),
            'performed_at' => $performedAt ?: now('Asia/Kolkata'),
            'metadata' => empty($metadata) ? null : $metadata,
        ]);
    }

    private function canAccess(): bool
    {
        return HelpdeskV2Session::canAccess()
            && (
                $this->canCreate()
                || HelpdeskV2Session::isDeveloperPerson()
            );
    }

    private function canCreate(): bool
    {
        return HelpdeskV2Session::isNicAdmin() || HelpdeskV2Session::isLayerLead();
    }

    private function canManageAllTasks(): bool
    {
        return HelpdeskV2Session::isNicAdmin();
    }

    private function canEditSchedule(TaskManagementTask $task): bool
    {
        return $this->canManageAllTasks()
            || (
                HelpdeskV2Session::isLayerLead()
                && in_array((string) $task->assigned_by_userid, $this->currentDeveloperIds(), true)
            );
    }

    private function canVerifyTesting(TaskManagementTask $task): bool
    {
        return HelpdeskV2Session::isLayerLead()
            && $this->canViewTask($task)
            && $task->completed_on
            && !$task->verified_on;
    }

    private function canUpdateTask(TaskManagementTask $task): bool
    {
        if ($this->canManageAllTasks()) {
            return true;
        }

        $developerIds = collect([
            HelpdeskV2Session::developerUserId(),
            HelpdeskV2Session::userId(),
        ])
            ->filter()
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->values()
            ->all();

        return in_array((string) $task->developer_userid, $developerIds, true);
    }

    private function canViewTask(TaskManagementTask $task): bool
    {
        if ($this->canManageAllTasks() || $this->canUpdateTask($task)) {
            return true;
        }

        if (HelpdeskV2Session::isLayerLead()) {
            return true;
        }

        return false;
    }

    private function currentDeveloperIds(): array
    {
        return collect([
            HelpdeskV2Session::developerUserId(),
            HelpdeskV2Session::userId(),
        ])
            ->filter()
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function activeTaskTab(Request $request): string
    {
        if (!HelpdeskV2Session::isLayerLead() || HelpdeskV2Session::isNicAdmin()) {
            return 'all';
        }

        $tab = (string) $request->input('tab', 'watchlist');

        return in_array($tab, ['watchlist', 'developer', 'testing'], true) ? $tab : 'watchlist';
    }

    private function taskTabs(): array
    {
        if (!HelpdeskV2Session::isLayerLead() || HelpdeskV2Session::isNicAdmin()) {
            return [];
        }

        return [
            'watchlist' => 'Watchlist',
            'developer' => 'Developer',
            'testing' => 'Testing',
        ];
    }

    private function taskAssignmentCcEmails(): array
    {
        if (HelpdeskV2Session::isNicAdmin()) {
            return HelpdeskV2Session::activeLayerLeads()->pluck('email')->all();
        }

        if (HelpdeskV2Session::isLayerLead()) {
            return $this->nicAdminEmails();
        }

        return [];
    }

    private function nicAdminEmails(): array
    {
        return DB::table('audit.userchargedetails as uc')
            ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
            ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
            ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
            ->where('uc.statusflag', 'Y')
            ->where('du.statusflag', 'Y')
            ->where(function ($query) {
                $query->where('uc.chargeid', '907')
                    ->orWhere('rm.roleactioncode', '01');
            })
            ->pluck('du.email')
            ->all();
    }
}
