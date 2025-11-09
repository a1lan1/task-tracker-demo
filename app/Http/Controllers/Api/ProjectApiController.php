<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ProjectServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectApiController extends Controller
{
    public function __construct(private readonly ProjectServiceInterface $projectService) {}

    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Project::class);

        return response()->json(
            $this->projectService->getUserProjects($request->user())
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json(
            $this->projectService->getProjectById($project->id)
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $this->authorize('create', Project::class);

        return response()->json(
            $this->projectService->createProject($request->validated()), 201
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        return response()->json(
            $this->projectService->updateProject($project, $request->validated())
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $this->projectService->deleteProject($project);

        return response()->json([], 204);
    }

    /**
     * @throws AuthorizationException
     */
    public function addMember(Project $project, User $user): JsonResponse
    {
        $this->authorize('update', $project);

        $this->projectService->addMember($project, $user);

        return response()->json([], 204);
    }

    /**
     * @throws AuthorizationException
     */
    public function removeMember(Project $project, User $user): JsonResponse
    {
        $this->authorize('update', $project);

        $this->projectService->removeMember($project, $user);

        return response()->json([], 204);
    }
}
