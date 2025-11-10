<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Policies\TaskPolicy;

beforeEach(function (): void {
    $this->policy = new TaskPolicy;
});

it('allows a user to view any task', function (): void {
    $user = User::factory()->create();

    expect($this->policy->viewAny($user))->toBeTrue();
});

it('allows a user to view a task if they can view the project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->members()->attach($user);
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($this->policy->view($user, $task))->toBeTrue();
});

it('does not allow a user to view a task if they cannot view the project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($this->policy->view($user, $task))->toBeFalse();
});

it('allows a user to create a task', function (): void {
    $user = User::factory()->create();

    expect($this->policy->create($user))->toBeTrue();
});

it('allows a user to update a task if they can update the project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->members()->attach($user);
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($this->policy->update($user, $task))->toBeTrue();
});

it('does not allow a user to update a task if they cannot update the project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($this->policy->update($user, $task))->toBeFalse();
});

it('allows a user to delete a task if they can delete the project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($this->policy->delete($user, $task))->toBeTrue();
});

it('does not allow a user to delete a task if they cannot delete the project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($this->policy->delete($user, $task))->toBeFalse();
});

it('allows a user to comment on a task if they can update the project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->members()->attach($user);
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($this->policy->comment($user, $task))->toBeTrue();
});

it('does not allow a user to comment on a task if they cannot update the project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($this->policy->comment($user, $task))->toBeFalse();
});
