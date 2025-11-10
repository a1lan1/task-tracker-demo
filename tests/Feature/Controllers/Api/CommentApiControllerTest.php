<?php

use App\Events\TaskCommentCreated;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\actingAs;

it('returns task comments', function (): void {
    $user = User::factory()->create();
    $project = Project::factory()->for($user, 'owner')->create();
    $task = Task::factory()->for($project)->create();
    $comment = Comment::factory()->for($task)->for($user, 'user')->create();

    $response = actingAs($user)->getJson(route('api.tasks.comments.index', $task->id));

    $response
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $comment->id);
});

it('allows an authenticated user to create a comment for a task and dispatches event', function (): void {
    Event::fake();
    $user = User::factory()->create();
    $project = Project::factory()->for($user, 'owner')->create();
    $task = Task::factory()->for($project)->create();
    $commentData = [
        'body' => 'This is a new comment.',
    ];

    $response = actingAs($user)->postJson(route('api.tasks.comments.store', $task->id), $commentData);

    $response
        ->assertCreated()
        ->assertJsonFragment(['body' => $commentData['body']]);

    $this->assertDatabaseHas('comments', [
        'body' => $commentData['body'],
        'task_id' => $task->id,
        'user_id' => $user->id,
    ]);

    Event::assertDispatched(TaskCommentCreated::class);
});
