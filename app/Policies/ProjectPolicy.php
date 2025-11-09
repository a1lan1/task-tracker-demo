<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        return $this->isOwnerOrMember($user, $project);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Project $project): bool
    {
        return $this->isOwnerOrMember($user, $project);
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->isOwner($user, $project);
    }

    public function restore(User $user, Project $project): bool
    {
        return $this->isOwner($user, $project);
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $this->isOwner($user, $project);
    }

    public function isOwner(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id;
    }

    public function isMember(User $user, Project $project): bool
    {
        return $project->members()->where('users.id', $user->id)->exists();
    }

    public function isOwnerOrMember(User $user, Project $project): bool
    {
        if ($this->isOwner($user, $project)) {
            return true;
        }

        return $this->isMember($user, $project);
    }
}
