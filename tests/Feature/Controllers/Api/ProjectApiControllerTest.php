<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('returns a list of projects for the authenticated user', function (): void {
    $user = User::factory()->create();
    $myProject = Project::factory()->for($user, 'owner')->create();
    $anotherUserProject = Project::factory()->create(); // Project owned by another user

    $response = actingAs($user)->getJson(route('api.projects.index'));

    $response
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.id', $myProject->id)
        ->assertJsonMissing(['id' => $anotherUserProject->id]);
});

it('returns unauthorized for guests trying to list projects', function (): void {
    $response = getJson(route('api.projects.index'));
    $response->assertUnauthorized();
});

it('allows an authenticated user to create a project', function (): void {
    $user = User::factory()->create();
    $projectData = [
        'name' => 'My New Project',
        'description' => 'This is a description for my new project.',
    ];

    $response = actingAs($user)->postJson(route('api.projects.store'), $projectData);

    $response
        ->assertCreated()
        ->assertJsonFragment(['name' => 'My New Project']);

    $this->assertDatabaseHas('projects', [
        'name' => 'My New Project',
        'owner_id' => $user->id,
    ]);
});

it('returns validation errors when creating a project with invalid data', function (): void {
    $user = User::factory()->create();
    $invalidProjectData = [
        'name' => '',
        'description' => Str::random(2000),
    ];

    $response = actingAs($user)->postJson(route('api.projects.store'), $invalidProjectData);

    $response
        ->assertJsonValidationErrors(['name', 'description'])
        ->assertStatus(422);
});

it('returns unauthorized for guests trying to create a project', function (): void {
    $response = postJson(route('api.projects.store'), ['name' => 'Guest Project']);
    $response->assertUnauthorized();
});

it('allows the project owner to view their project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->for($user, 'owner')->create();

    $response = actingAs($user)->getJson(route('api.projects.show', $project));

    $response
        ->assertOk()
        ->assertJsonPath('id', $project->id);
});

it('prevents a non-member from viewing a project', function (): void {
    $owner = User::factory()->create();
    $nonMember = User::factory()->create();
    $project = Project::factory()->for($owner, 'owner')->create();

    $response = actingAs($nonMember)->getJson(route('api.projects.show', $project));

    $response->assertForbidden();
});

it('returns unauthorized for guests trying to view a project', function (): void {
    $project = Project::factory()->create();

    $response = getJson(route('api.projects.show', $project));

    $response->assertUnauthorized();
});

it('allows the project owner to update their project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id, 'name' => 'Old Name']);
    $updateData = ['name' => 'New Project Name'];

    $response = actingAs($user)->putJson(route('api.projects.update', $project), $updateData);

    $response
        ->assertOk()
        ->assertJsonPath('name', 'New Project Name');

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'New Project Name',
    ]);
});

it('allows a project member to update the project', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $owner->id]);
    $project->members()->attach($member);
    $updateData = ['name' => 'Updated by member'];

    $response = actingAs($member)->putJson(route('api.projects.update', $project), $updateData);

    $response
        ->assertOk()
        ->assertJsonPath('name', 'Updated by member');
});

it('prevents a non-member from updating a project', function (): void {
    $owner = User::factory()->create();
    $nonMember = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $owner->id]);

    $response = actingAs($nonMember)->putJson(route('api.projects.update', $project), ['name' => 'Should Fail']);

    $response->assertForbidden();
});

it('returns validation errors when updating a project with invalid data', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    $response = actingAs($user)->putJson(route('api.projects.update', $project), ['name' => '']);

    $response->assertJsonValidationErrors(['name'])->assertStatus(422);
});

it('returns unauthorized for guests trying to update a project', function (): void {
    $project = Project::factory()->create();
    $response = putJson(route('api.projects.update', $project), ['name' => 'Guest Update']);
    $response->assertUnauthorized();
});

it('allows the project owner to delete their project', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    $response = actingAs($user)->deleteJson(route('api.projects.destroy', $project));

    $response->assertNoContent();
    $this->assertModelMissing($project);
});

it('prevents a project member from deleting the project', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $owner->id]);
    $project->members()->attach($member);

    $response = actingAs($member)->deleteJson(route('api.projects.destroy', $project));

    $response->assertForbidden();
});

it('prevents a non-member from deleting a project', function (): void {
    $owner = User::factory()->create();
    $nonMember = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $owner->id]);

    $response = actingAs($nonMember)->deleteJson(route('api.projects.destroy', $project));

    $response->assertForbidden();
});

it('returns unauthorized for guests trying to delete a project', function (): void {
    $project = Project::factory()->create();

    $response = deleteJson(route('api.projects.destroy', $project));

    $response->assertUnauthorized();
});
