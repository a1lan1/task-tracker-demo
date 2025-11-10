<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

it('allows a user to create a project', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    visit(route('dashboard'))
        ->click('Add Project')
        ->assertSee('Create Project')
        ->type('name', 'My First Project')
        ->type('description', 'This is my first project.')
        ->press('Create')
        ->assertSee('Project created successfully!')
        ->assertSee('My First Project');

    $this->assertDatabaseHas('projects', [
        'name' => 'My First Project',
        'owner_id' => $user->id,
    ]);
});

it('shows validation error when project name is missing', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    visit(route('dashboard'))
        ->press('Add Project')
        ->assertSee('Create Project')
        ->type('name', '')
        ->type('description', 'This is my first project.')
        ->press('Create')
        ->assertSee('Project name is required');
});

it('redirects guest to login when accessing dashboard', function (): void {
    visit(route('dashboard'))
        ->assertSee('Log in to your account');
});
