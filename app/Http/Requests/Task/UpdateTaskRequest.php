<?php

declare(strict_types=1);

namespace App\Http\Requests\Task;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['sometimes', 'exists:projects,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', new Enum(TaskStatus::class)],
            'assignee_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
        ];
    }
}
