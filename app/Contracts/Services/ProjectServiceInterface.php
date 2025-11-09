<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface ProjectServiceInterface
{
    public function getAllProjects(): Collection;

    public function getUserProjects(User $user): Collection;

    public function createProject(array $data): Project;

    public function getProjectById(int $id): Project;

    public function updateProject(Project $project, array $data): bool;

    public function deleteProject(Project $project): bool;

    public function addMember(Project $project, User $user): void;

    public function removeMember(Project $project, User $user): void;
}
