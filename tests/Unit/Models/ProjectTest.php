<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

it('belongs to an owner', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    expect($project->owner)->toBeInstanceOf(User::class)
        ->and($project->owner->id)->toBe($user->id);
});

it('has many tasks', function (): void {
    $project = Project::factory()->create();
    Task::factory()->count(3)->create(['project_id' => $project->id]);

    expect($project->tasks)->toBeInstanceOf(Collection::class)
        ->and($project->tasks)->toHaveCount(3)
        ->and($project->tasks->first())->toBeInstanceOf(Task::class);
});
