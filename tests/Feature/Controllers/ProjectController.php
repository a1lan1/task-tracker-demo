<?php

use App\Models\Project;
use App\Models\User;

test('authenticated users can view projects', function (): void {
    $owner = User::factory()->create();
    Project::factory(3)->for($owner, 'owner')->create();

    $this->actingAs($owner)
        ->get(route('dashboard'))
        ->assertOk();
});

test('authenticated users can view a single Project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->get(route('projects.showProject', $project->id))
        ->assertOk();
});

test('it returns a 404 if project is not found', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('projects.showProject', 999))
        ->assertNotFound();
});
