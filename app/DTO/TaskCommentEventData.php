<?php

declare(strict_types=1);

namespace App\DTO;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class TaskCommentEventData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $task_id,
        public readonly int $project_id,
        public readonly int $user_id,
        public readonly UserData $user,
        public readonly string $body,
        public readonly CarbonImmutable $created_at,
    ) {}
}
