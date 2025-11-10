<?php

declare(strict_types=1);

use App\Contracts\Services\ProjectServiceInterface;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

beforeEach(function (): void {
    $this->service = app(ProjectServiceInterface::class);
});

it('returns all projects with owner and members eager loaded', function (): void {
    $users = User::factory()->count(3)->create();
    $projects = Project::factory()->count(2)->create();
    $projects[0]->members()->attach([$users[0]->id, $users[1]->id]);
    $projects[1]->members()->attach([$users[2]->id]);

    $result = $this->service->getAllProjects();

    expect($result)->toHaveCount(2);
    $result->each(function (Project $project): void {
        expect($project->relationLoaded('owner'))->toBeTrue();
        expect($project->relationLoaded('members'))->toBeTrue();
    });
});

it('returns projects for a given user (owner or member)', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $owned = Project::factory()->create(['owner_id' => $user->id]);
    $memberProject = Project::factory()->create(['owner_id' => $other->id]);
    $memberProject->members()->attach($user->id);
    $unrelated = Project::factory()->create(['owner_id' => $other->id]);

    $projects = $this->service->getUserProjects($user);

    expect($projects->pluck('id')->all())
        ->toContain($owned->id)
        ->toContain($memberProject->id)
        ->not()->toContain($unrelated->id);

    $projects->each(function (Project $project): void {
        expect($project->relationLoaded('owner'))->toBeTrue();
        expect($project->relationLoaded('members'))->toBeTrue();
    });
});

it('creates a project and sets the authenticated user as owner when missing', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $project = $this->service->createProject([
        'name' => 'Demo',
        'description' => 'Desc',
        // no owner_id provided
    ]);

    expect($project->exists)->toBeTrue()
        ->and($project->owner_id)->toBe($user->id);
});

it('gets a project by id with tasks and assignees', function (): void {
    $assignee = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'assignee_id' => $assignee->id,
    ]);

    $found = $this->service->getProjectById($project->id);

    expect($found->is($project))->toBeTrue();
    expect($found->relationLoaded('owner'))->toBeTrue();
    expect($found->relationLoaded('members'))->toBeTrue();
    expect($found->relationLoaded('tasks'))->toBeTrue();
    expect($found->tasks->first()->relationLoaded('assignee'))->toBeTrue();
    expect($found->tasks->pluck('id'))->toContain($task->id);
});

it('updates a project and returns true', function (): void {
    $project = Project::factory()->create(['name' => 'Old']);

    $updated = $this->service->updateProject($project, ['name' => 'New']);

    expect($updated)->toBeTrue();
    expect($project->refresh()->name)->toBe('New');
});

it('deletes a project and returns true', function (): void {
    $project = Project::factory()->create();

    $deleted = $this->service->deleteProject($project);

    expect($deleted)->toBeTrue();
    expect(Project::query()->whereKey($project->id)->exists())->toBeFalse();
});

it('adds and removes a member to a project', function (): void {
    $project = Project::factory()->create();
    $user = User::factory()->create();

    $this->service->addMember($project, $user);
    expect($project->members()->whereKey($user->id)->exists())->toBeTrue();

    $this->service->removeMember($project, $user);
    expect($project->members()->whereKey($user->id)->exists())->toBeFalse();
});
