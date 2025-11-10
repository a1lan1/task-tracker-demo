<?php

use App\Models\Project;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

it('allows a user to create a task within a project', function (): void {
    $owner = User::factory()->create();
    $project = Project::factory()->for($owner, 'owner')->create();
    $taskTitle = 'My First Browser Task';

    actingAs($owner);

    visit(route('projects.showProject', $project->id))
        ->assertSee($project->name)
        ->press('Add Task')
        ->assertSee('Create Task')
        ->type('title', $taskTitle)
        ->press('Create')
        ->assertSee('Task created successfully!')
        ->assertSee($taskTitle);

    assertDatabaseHas('tasks', [
        'project_id' => $project->id,
        'title' => $taskTitle,
    ]);
});
