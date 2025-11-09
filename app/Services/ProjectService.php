<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\ProjectServiceInterface;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProjectService implements ProjectServiceInterface
{
    public function getAllProjects(): Collection
    {
        return Project::with('owner', 'members')->get();
    }

    public function getUserProjects(User $user): Collection
    {
        return Project::query()
            ->with('owner', 'members')
            ->where(fn (Builder $query) => $query
                ->where('owner_id', $user->id)
                ->orWhereHas('members', fn (Builder $q) => $q->where('users.id', $user->id)))
            ->get();
    }

    public function createProject(array $data): Project
    {
        return Project::create($data);
    }

    public function getProjectById(int $id): Project
    {
        return Project::with('owner', 'members', 'tasks.assignee')->findOrFail($id);
    }

    public function updateProject(Project $project, array $data): bool
    {
        return $project->update($data);
    }

    public function deleteProject(Project $project): bool
    {
        return $project->delete();
    }

    public function addMember(Project $project, User $user): void
    {
        $project->members()->attach($user->id);
    }

    public function removeMember(Project $project, User $user): void
    {
        $project->members()->detach($user->id);
    }
}
