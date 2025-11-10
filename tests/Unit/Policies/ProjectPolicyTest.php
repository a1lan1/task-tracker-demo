<?php

use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;

beforeEach(function (): void {
    $this->policy = new ProjectPolicy;
});

it('allows a user to view any project', function (): void {
    $user = User::factory()->create();

    expect($this->policy->viewAny($user))->toBeTrue();
});

it('allows a user to view a project if they are the owner', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    expect($this->policy->view($user, $project))->toBeTrue();
});

it('allows a user to view a project if they are a member', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->members()->attach($user);

    expect($this->policy->view($user, $project))->toBeTrue();
});

it('does not allow a user to view a project if they are not the owner or a member', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    expect($this->policy->view($user, $project))->toBeFalse();
});

it('allows a user to create a project', function (): void {
    $user = User::factory()->create();

    expect($this->policy->create($user))->toBeTrue();
});

it('allows a user to update a project if they are the owner', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    expect($this->policy->update($user, $project))->toBeTrue();
});

it('allows a user to update a project if they are a member', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->members()->attach($user);

    expect($this->policy->update($user, $project))->toBeTrue();
});

it('does not allow a user to update a project if they are not the owner or a member', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    expect($this->policy->update($user, $project))->toBeFalse();
});

it('allows a user to delete a project if they are the owner', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    expect($this->policy->delete($user, $project))->toBeTrue();
});

it('does not allow a user to delete a project if they are not the owner', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    expect($this->policy->delete($user, $project))->toBeFalse();
});

it('allows a user to restore a project if they are the owner', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    expect($this->policy->restore($user, $project))->toBeTrue();
});

it('does not allow a user to restore a project if they are not the owner', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    expect($this->policy->restore($user, $project))->toBeFalse();
});

it('allows a user to force delete a project if they are the owner', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    expect($this->policy->forceDelete($user, $project))->toBeTrue();
});

it('does not allow a user to force delete a project if they are not the owner', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    expect($this->policy->forceDelete($user, $project))->toBeFalse();
});

it('correctly determines if a user is the owner of a project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    expect($this->policy->isOwner($user, $project))->toBeTrue();
});

it('correctly determines if a user is a member of a project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->members()->attach($user);

    expect($this->policy->isMember($user, $project))->toBeTrue();
});
