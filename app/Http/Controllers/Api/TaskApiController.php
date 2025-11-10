<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Task\UpdateTaskStatus;
use App\Contracts\Services\TaskServiceInterface;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Requests\Task\UpdateTaskStatusRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Throwable;

class TaskApiController extends Controller
{
    public function __construct(private readonly TaskServiceInterface $taskService) {}

    /**
     * @throws AuthorizationException
     */
    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json(
            $this->taskService->getTasksByProject($project->id)
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        return response()->json(
            $this->taskService->getTaskById($task->id)
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $this->authorize('create', Task::class);

        return response()->json(
            $this->taskService->createTask($request->validated()), 201
        );
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function updateStatus(
        UpdateTaskStatusRequest $request,
        Task $task,
        UpdateTaskStatus $updateTaskStatus
    ): JsonResponse {
        $this->authorize('update', $task);

        $updateTaskStatus->handle(
            $task,
            TaskStatus::from($request->validated('status'))
        );

        return response()->json(
            $task->refresh()
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $this->taskService->updateTask($task, $request->validated());

        return response()->json(
            $task->fresh()
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $this->taskService->deleteTask($task);

        return response()->json([], 204);
    }
}
