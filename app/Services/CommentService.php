<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\CommentServiceInterface;
use App\Models\Comment;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentService implements CommentServiceInterface
{
    const int PAGINATE = 20;

    public function getTaskComments(int $taskId): LengthAwarePaginator
    {
        return Comment::query()
            ->where('task_id', $taskId)
            ->with('user:id,name', 'user.media')
            ->latest('id')
            ->paginate(self::PAGINATE)
            ->through(fn (Comment $comment) => $comment->append('avatar'));
    }
}
