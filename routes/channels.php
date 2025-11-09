<?php

use App\Models\Project;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', fn ($user, $id): bool => (int) $user->id === (int) $id);
Broadcast::channel('project.{project}', fn ($user, Project $project): bool => $user->can('view', $project));
