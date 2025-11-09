<?php

declare(strict_types=1);

namespace App\DTO;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class CommentData extends Data
{
    public function __construct(
        #[Required, Exists('tasks', 'id')]
        public readonly int $task_id,

        #[Required, Exists('users', 'id')]
        public readonly int $user_id,

        #[Required, StringType, Max(2000)]
        public readonly string $body,
    ) {}
}
