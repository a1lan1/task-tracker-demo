<?php

use App\Actions\Task\UpdateTaskStatus;
use App\Enums\TaskStatus;
use App\Events\TaskStatusUpdated;
use App\Models\Task;
use Illuminate\Support\Facades\Event;

it('updates task status and dispatches an event', function (): void {
    Event::fake();
    $action = new UpdateTaskStatus;
    $task = Task::factory()->create(['status' => TaskStatus::Todo]);
    $newStatus = TaskStatus::InProgress;

    $updatedTask = $action->handle($task, $newStatus);

    expect($updatedTask)->toBeInstanceOf(Task::class)
        ->and($updatedTask->status)->toBe($newStatus);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => $newStatus->value,
    ]);

    Event::assertDispatched(TaskStatusUpdated::class, function ($event) use ($task, $newStatus): bool {
        return $event->data->task_id === $task->id &&
               $event->data->new_status === $newStatus &&
               $event->data->old_status === TaskStatus::Todo;
    });
});

it('throws an exception if the new status is the same as the old status', function (): void {
    $action = new UpdateTaskStatus;
    $task = Task::factory()->create(['status' => TaskStatus::Todo]);

    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Task status is already todo');

    $action->handle($task, TaskStatus::Todo);
});
