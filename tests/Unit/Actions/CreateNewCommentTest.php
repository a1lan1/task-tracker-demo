<?php

use App\Actions\Comment\CreateTaskComment;
use App\DTO\CommentData;
use App\Events\TaskCommentCreated;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Event;

it('creates a new comment for a task and dispatches event', function (): void {
    Event::fake();
    $action = new CreateTaskComment;
    $user = User::factory()->create();
    $task = Task::factory()->create();
    $commentData = new CommentData(
        task_id: $task->id,
        user_id: $user->id,
        body: 'This is a brand new comment.',
    );

    $comment = $action->handle($task, $commentData);

    expect($comment)->toBeInstanceOf(Comment::class)
        ->and($comment->body)->toBe('This is a brand new comment.')
        ->and($comment->task_id)->toBe($task->id)
        ->and($comment->user_id)->toBe($user->id);

    $this->assertDatabaseHas('comments', ['id' => $comment->id]);

    Event::assertDispatched(TaskCommentCreated::class);
});
