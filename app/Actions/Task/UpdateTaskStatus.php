<?php

declare(strict_types=1);

namespace App\Actions\Task;

use App\DTO\TaskStatusUpdatedData;
use App\Enums\TaskStatus;
use App\Events\TaskStatusUpdated;
use App\Models\Task;
use Exception;
use Throwable;

readonly class UpdateTaskStatus
{
    /**
     * @throws Throwable
     */
    public function handle(Task $task, TaskStatus $newStatus): Task
    {
        $oldStatus = $task->status;

        if ($oldStatus === $newStatus) {
            throw new Exception('Task status is already '.$newStatus->value);
        }

        $task->updateOrFail(['status' => $newStatus]);

        event(new TaskStatusUpdated(
            new TaskStatusUpdatedData(
                task_id: $task->id,
                project_id: $task->project_id,
                old_status: $oldStatus,
                new_status: $newStatus,
            )
        ));

        return $task;
    }
}
