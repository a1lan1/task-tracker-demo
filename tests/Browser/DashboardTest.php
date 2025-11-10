<?php

use App\Models\Project;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('allows a user to see their projects on the dashboard', function (): void {
    $owner = User::factory()->create();
    $project = Project::factory()->for($owner, 'owner')->create();

    actingAs($owner);

    visit(route('dashboard'))
        ->assertSee($project->name)
        ->assertDontSee('Logged in');
});
