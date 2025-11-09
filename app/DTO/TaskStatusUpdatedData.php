<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\TaskStatus;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class TaskStatusUpdatedData extends Data
{
    public function __construct(
        public readonly int $task_id,
        public readonly int $project_id,
        #[WithCast(EnumCast::class)]
        public readonly TaskStatus $old_status,
        #[WithCast(EnumCast::class)]
        public readonly TaskStatus $new_status,
    ) {}
}
