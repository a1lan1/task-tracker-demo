<?php

declare(strict_types=1);

use App\Contracts\Services\CommentServiceInterface;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

beforeEach(function (): void {
    $this->service = app(CommentServiceInterface::class);
});

it('returns paginated task comments with user and avatar eager loaded and appended', function (): void {
    $task = Task::factory()->create();
    $users = User::factory()->count(3)->create();
    // create more than pagination size to ensure pagination works
    Comment::factory()->count(25)->sequence(
        ['user_id' => $users[0]->id, 'task_id' => $task->id],
        ['user_id' => $users[1]->id, 'task_id' => $task->id],
        ['user_id' => $users[2]->id, 'task_id' => $task->id],
    )->create();

    $paginator = $this->service->getTaskComments($task->id);

    expect($paginator)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($paginator->perPage())->toBe(20);
    expect($paginator->total())->toBe(25);
    expect($paginator->count())->toBe(20); // first page size

    $paginator->getCollection()->each(function (Comment $comment): void {
        expect($comment->relationLoaded('user'))->toBeTrue();
        // user has media relation available; service eager loads 'user.media'
        expect($comment->user->relationLoaded('media'))->toBeTrue();
        // avatar is appended accessor on User model
        expect(isset($comment->user->avatar))->toBeTrue();
    });
});
