<?php

declare(strict_types=1);

namespace App\DTO;

use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $avatar,
    ) {}
}
