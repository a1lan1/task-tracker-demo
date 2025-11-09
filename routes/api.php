<?php

use App\Http\Controllers\Api\ProjectApiController;
use App\Http\Controllers\Api\TaskApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['auth', 'verified'])->group(function (): void {
    // Projects
    Route::apiResource('projects', ProjectApiController::class);
    Route::post('projects/{project}/members/{user}', [ProjectApiController::class, 'addMember'])->name('api.projects.members.add');
    Route::delete('projects/{project}/members/{user}', [ProjectApiController::class, 'removeMember'])->name('api.projects.members.remove');

    // Tasks
    Route::apiResource('tasks', TaskApiController::class)->except(['index']);
    Route::get('projects/{project}/tasks', [TaskApiController::class, 'index'])->name('api.projects.tasks.index');
    Route::patch('tasks/status/{task}', [TaskApiController::class, 'updateStatus'])->name('api.tasks.updateStatus');
});
