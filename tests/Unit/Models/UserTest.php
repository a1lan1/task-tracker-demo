<?php

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

it('has many projects', function (): void {
    $user = User::factory()
        ->has(Project::factory()->count(2), 'projects')
        ->create();

    expect($user->projects)->toBeInstanceOf(Collection::class)
        ->and($user->projects)->toHaveCount(2)
        ->and($user->projects->first())->toBeInstanceOf(Project::class);
});

it('has many assigned tasks', function (): void {
    $user = User::factory()
        ->has(Task::factory()->count(3), 'tasks')
        ->create();

    expect($user->tasks)->toBeInstanceOf(Collection::class)
        ->and($user->tasks)->toHaveCount(3)
        ->and($user->tasks->first())->toBeInstanceOf(Task::class);
});

it('has many comments', function (): void {
    $user = User::factory()
        ->has(Comment::factory()->count(4))
        ->create();

    expect($user->comments)->toBeInstanceOf(Collection::class)
        ->and($user->comments)->toHaveCount(4)
        ->and($user->comments->first())->toBeInstanceOf(Comment::class);
});
