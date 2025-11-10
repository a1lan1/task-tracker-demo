<?php

use App\Enums\TaskStatus;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

it('belongs to a project', function (): void {
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($task->project)->toBeInstanceOf(Project::class)
        ->and($task->project->id)->toBe($project->id);
});

it('belongs to an assigned user', function (): void {
    $user = User::factory()->create();
    $task = Task::factory()->create(['assignee_id' => $user->id]);

    expect($task->assignee)->toBeInstanceOf(User::class)
        ->and($task->assignee->id)->toBe($user->id);
});

it('has many comments', function (): void {
    $task = Task::factory()->create();
    Comment::factory()->count(2)->create(['task_id' => $task->id]);

    expect($task->comments)->toBeInstanceOf(Collection::class)
        ->and($task->comments)->toHaveCount(2)
        ->and($task->comments->first())->toBeInstanceOf(Comment::class);
});

it('can be scoped to completed tasks', function (): void {
    Task::factory()->create(['status' => TaskStatus::Done]);
    Task::factory()->create(['status' => TaskStatus::InProgress]);

    expect(Task::completed()->count())->toBe(1)
        ->and(Task::completed()->first()->status)->toBe(TaskStatus::Done);
});

it('can be scoped to in-progress tasks', function (): void {
    Task::factory()->create(['status' => TaskStatus::Done]);
    Task::factory()->create(['status' => TaskStatus::InProgress]);

    expect(Task::inProgress()->count())->toBe(1)
        ->and(Task::inProgress()->first()->status)->toBe(TaskStatus::InProgress);
});
