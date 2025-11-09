<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskServiceInterface
{
    public function getTasksByProject(int $projectId): Collection;

    public function createTask(array $data): Task;

    public function getTaskById(int $id): ?Task;

    public function updateTask(Task $task, array $data): bool;

    public function deleteTask(Task $task): bool;
}
