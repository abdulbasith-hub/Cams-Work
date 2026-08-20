<?php

namespace App\Services;

use App\Models\DeveloperTask;
use App\Support\HelpdeskSession;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DeveloperTaskDashboardDataService
{
    public function canView(): bool
    {
        return HelpdeskSession::isNicAdmin() || HelpdeskSession::isDeveloper();
    }

    public function taskDashboardData(): array
    {
        return $this->buildDashboardPayload($this->dashboardTasksForType('task') ?? collect());
    }

    public function testingTaskDashboardData(): array
    {
        return $this->buildDashboardPayload($this->dashboardTasksForType('testing') ?? collect());
    }

    public function dashboardTasksForType(string $dashboardType): ?Collection
    {
        if ($dashboardType === 'testing') {
            return $this->visibleTestingTasksQuery()
                ->orderBy('developer_name')
                ->orderByDesc('assigned_on')
                ->orderByDesc('id')
                ->get();
        }

        if ($dashboardType !== 'task') {
            return null;
        }

        return $this->visibleTasksQuery(false)
            ->orderBy('developer_name')
            ->orderByDesc('assigned_on')
            ->orderByDesc('id')
            ->get();
    }

    public function buildDashboardPayload(Collection $tasks): array
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

    private function visibleTasksQuery(bool $isTestingTask)
    {
        $query = DeveloperTask::query()->where('is_testing_task', $isTestingTask);

        if (HelpdeskSession::isDeveloper()) {
            $query->where('developer_userid', HelpdeskSession::userId());
        }

        return $query;
    }

    private function visibleTestingTasksQuery()
    {
        return $this->visibleTasksQuery(true);
    }

    private function currentTimestamp(): Carbon
    {
        return Carbon::now('Asia/Kolkata');
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
