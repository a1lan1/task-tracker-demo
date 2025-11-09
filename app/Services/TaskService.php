<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\TaskServiceInterface;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class TaskService implements TaskServiceInterface
{
    public function getTasksByProject(int $projectId): Collection
    {
        return Task::where('project_id', $projectId)->with('assignee')->get();
    }

    public function createTask(array $data): Task
    {
        return Task::create($data);
    }

    public function getTaskById(int $id): ?Task
    {
        return Task::with('assignee.media')->findOrFail($id);
    }

    /**
     * @throws Throwable
     */
    public function updateTask(Task $task, array $data): bool
    {
        return $task->updateOrFail($data);
    }

    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }
}
