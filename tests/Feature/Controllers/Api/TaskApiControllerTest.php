<?php

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('allows a project member to create a task in the project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->members()->attach($user);

    $taskData = [
        'project_id' => $project->id,
        'title' => 'A new task for the project',
        'description' => 'Task description.',
        'status' => TaskStatus::Todo->value,
    ];

    $response = actingAs($user)->postJson(route('api.tasks.store'), $taskData);

    $response
        ->assertCreated()
        ->assertJsonPath('title', 'A new task for the project');

    $this->assertDatabaseHas('tasks', [
        'project_id' => $project->id,
        'title' => 'A new task for the project',
    ]);
});

it('returns validation errors when creating a task with invalid data', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    $invalidTaskData = [
        'project_id' => $project->id,
        'title' => '',
    ];

    $response = actingAs($user)->postJson(route('api.tasks.store'), $invalidTaskData);

    $response
        ->assertJsonValidationErrors(['title'])
        ->assertStatus(422);
});

it('returns unauthorized for guests trying to create a task', function (): void {
    $project = Project::factory()->create();

    $response = postJson(route('api.tasks.store'), ['project_id' => $project->id, 'title' => 'Guest Task']);

    $response->assertUnauthorized();
});

it('allows a project member to update a task in the project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->members()->attach($user);
    $task = Task::factory()->create(['project_id' => $project->id]);

    $taskData = [
        'title' => 'Updated task title',
        'description' => 'Updated task description.',
        'status' => TaskStatus::InProgress->value,
    ];

    $response = actingAs($user)->putJson(route('api.tasks.update', $task), $taskData);

    $response
        ->assertOk()
        ->assertJsonPath('title', 'Updated task title');

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Updated task title',
    ]);
});

it('prevents a user from updating a task in a project they are not a member of', function (): void {
    $user = User::factory()->create();
    $anotherProject = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $anotherProject->id]);

    $taskData = [
        'title' => 'Updated task title',
    ];

    $response = actingAs($user)->putJson(route('api.tasks.update', $task), $taskData);

    $response->assertForbidden();
});

it('returns unauthorized for guests trying to update a task', function (): void {
    $task = Task::factory()->create();

    $response = putJson(route('api.tasks.update', $task), [
        'title' => 'Updated task title',
    ]);

    $response->assertUnauthorized();
});

it('returns validation errors when updating a task with invalid data', function (): void {
    $user = User::factory()->create();
    $task = Task::factory()->create();

    $response = actingAs($user)->putJson(route('api.tasks.update', $task), ['title' => null]);

    $response->assertStatus(422)->assertJsonValidationErrors(['title']);
});

it('allows the project owner to delete a task', function (): void {
    $owner = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $owner->id]);
    $task = Task::factory()->create(['project_id' => $project->id]);

    $response = actingAs($owner)->deleteJson(route('api.tasks.destroy', $task));

    $response->assertNoContent();
    $this->assertModelMissing($task);
});

it('prevents a project member from deleting a task', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $owner->id]);
    $project->members()->attach($member);
    $task = Task::factory()->create(['project_id' => $project->id]);

    $response = actingAs($member)->deleteJson(route('api.tasks.destroy', $task));

    $response->assertForbidden();
    $this->assertModelExists($task);
});

it('returns unauthorized for guests trying to delete a task', function (): void {
    $task = Task::factory()->create();

    $response = deleteJson(route('api.tasks.destroy', $task));

    $response->assertUnauthorized();
});
