<?php

declare(strict_types=1);

use App\Contracts\Services\TaskServiceInterface;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

beforeEach(function (): void {
    $this->service = app(TaskServiceInterface::class);
});

it('gets tasks by project with assignee eager loaded', function (): void {
    $project = Project::factory()->create();
    $assignee = User::factory()->create();
    $taskWithAssignee = Task::factory()->create([
        'project_id' => $project->id,
        'assignee_id' => $assignee->id,
    ]);
    $otherProjectTask = Task::factory()->create();

    $tasks = $this->service->getTasksByProject($project->id);

    expect($tasks->pluck('id')->all())
        ->toContain($taskWithAssignee->id)
        ->not()->toContain($otherProjectTask->id);

    $tasks->each(function (Task $task): void {
        expect($task->relationLoaded('assignee'))->toBeTrue();
    });
});

it('creates a task', function (): void {
    $project = Project::factory()->create();
    $data = [
        'project_id' => $project->id,
        'title' => 'Implement feature',
        'description' => 'Do it',
        'status' => TaskStatus::InProgress,
    ];

    $task = $this->service->createTask($data);

    expect($task->exists)->toBeTrue();
    expect($task->project_id)->toBe($project->id);
    expect($task->status)->toBe(TaskStatus::InProgress);
});

it('gets task by id with assignee and media eager loaded', function (): void {
    $assignee = User::factory()->create();
    $task = Task::factory()->create([
        'assignee_id' => $assignee->id,
    ]);

    $found = $this->service->getTaskById($task->id);

    expect($found)->not()->toBeNull();
    expect($found?->relationLoaded('assignee'))->toBeTrue();
    // Nested eager load: media on assignee
    expect($found?->assignee?->relationLoaded('media'))->toBeTrue();
});

it('updates a task successfully', function (): void {
    $task = Task::factory()->create([
        'title' => 'Old',
        'status' => TaskStatus::InProgress,
    ]);

    $ok = $this->service->updateTask($task, [
        'title' => 'New',
        'status' => TaskStatus::Done,
    ]);

    expect($ok)->toBeTrue();
    $task->refresh();
    expect($task->title)->toBe('New');
    expect($task->status)->toBe(TaskStatus::Done);
});

it('deletes a task and returns true', function (): void {
    $task = Task::factory()->create();

    $deleted = $this->service->deleteTask($task);

    expect($deleted)->toBeTrue();
    expect(Task::query()->whereKey($task->id)->exists())->toBeFalse();
});
