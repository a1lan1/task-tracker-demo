<?php

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;

it('belongs to a task', function (): void {
    $task = Task::factory()->create();
    $comment = Comment::factory()->create(['task_id' => $task->id]);

    expect($comment->task)->toBeInstanceOf(Task::class)
        ->and($comment->task->id)->toBe($task->id);
});

it('belongs to a user', function (): void {
    $user = User::factory()->create();
    $comment = Comment::factory()->create(['user_id' => $user->id]);

    expect($comment->user)->toBeInstanceOf(User::class)
        ->and($comment->user->id)->toBe($user->id);
});
