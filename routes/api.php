<?php

use App\Http\Controllers\Api\CommentApiController;
use App\Http\Controllers\Api\ProjectApiController;
use App\Http\Controllers\Api\TaskApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['auth', 'verified'])->name('api.')->group(function (): void {
    // Projects
    Route::apiResource('projects', ProjectApiController::class);
    Route::post('projects/{project}/members/{user}', [ProjectApiController::class, 'addMember'])->name('projects.members.add');
    Route::delete('projects/{project}/members/{user}', [ProjectApiController::class, 'removeMember'])->name('projects.members.remove');

    // Tasks
    Route::apiResource('tasks', TaskApiController::class)->except(['index']);
    Route::get('projects/{project}/tasks', [TaskApiController::class, 'index'])->name('projects.tasks.index');
    Route::patch('tasks/status/{task}', [TaskApiController::class, 'updateStatus'])->name('tasks.updateStatus');

    // Comments
    Route::get('tasks/{task}/comments', [CommentApiController::class, 'index'])->name('tasks.comments.index');
    Route::post('tasks/{task}/comments', [CommentApiController::class, 'store'])->name('tasks.comments.store');
});
