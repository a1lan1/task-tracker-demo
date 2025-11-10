<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Comment\CreateTaskComment;
use App\Contracts\Services\CommentServiceInterface;
use App\DTO\CommentData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentApiController extends Controller
{
    public function __construct(private readonly CommentServiceInterface $commentService) {}

    /**
     * @throws AuthorizationException
     */
    public function index(Task $task): AnonymousResourceCollection
    {
        $this->authorize('view', $task);

        return CommentResource::collection(
            $this->commentService->getTaskComments($task->id)
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function store(StoreCommentRequest $request, Task $task, CreateTaskComment $createTaskComment): CommentResource
    {
        $this->authorize('comment', $task);

        $comment = $createTaskComment->handle(
            $task,
            new CommentData(
                task_id: $task->id,
                user_id: $request->user()->id,
                body: $request->validated('body'),
            )
        );

        return new CommentResource($comment);
    }
}
