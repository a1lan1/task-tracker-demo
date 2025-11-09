<?php

declare(strict_types=1);

namespace App\Actions\Comment;

use App\DTO\CommentData;
use App\DTO\TaskCommentEventData;
use App\DTO\UserData;
use App\Events\TaskCommentCreated;
use App\Models\Comment;
use App\Models\Task;

readonly class CreateTaskComment
{
    public function handle(Task $task, CommentData $data): Comment
    {
        $comment = Comment::create($data->toArray());

        $comment->load('user:id,name');

        event(new TaskCommentCreated(
            new TaskCommentEventData(
                id: $comment->id,
                task_id: $task->id,
                project_id: $task->project_id,
                user_id: $comment->user_id,
                user: UserData::from($comment->user),
                body: $comment->body,
                created_at: $comment->created_at,
            )
        ));

        return $comment;
    }
}
